<?php
namespace local_aistrix\services;

defined('MOODLE_INTERNAL') || die();

class vpl_service {

    /**
     * Devuelve un array de objetos stdClass con:
     *   - datos de la actividad VPL
     *   - datos de la entrega
     *   - resultado de la evaluación
     *   - contenido del/los archivo(s) fuente en texto plano
     */
    public static function get_vpl_data(int $courseid = null): array {
        global $DB, $CFG;

        // 1.- Consulta SQL (tablas vpl, submissions, user, course)
        // NOTA: No incluimos vpl_evaluations porque no existe en VPL
        $sql = "
            SELECT
                v.id              AS vplid,
                v.name            AS vplname,
                v.course          AS courseid,
                c.fullname        AS coursename,

                s.id              AS submissionid,
                s.userid,
                u.firstname,
                u.lastname,
                s.datesubmitted,
                s.grade           AS grade,
                s.dategraded      AS dategraded,
                s.grader          AS grader
            FROM {vpl} v
            JOIN {course}          c ON c.id = v.course
            LEFT JOIN {vpl_submissions} s ON s.vpl = v.id
            LEFT JOIN {user}       u ON u.id = s.userid
            WHERE (:cid IS NULL OR v.course = :cid)
            ORDER BY v.id, s.datesubmitted DESC
        ";
        
        try {
            $records = $DB->get_records_sql($sql, ['cid' => $courseid]);
        } catch (Exception $e) {
            error_log("AISTRIX ERROR in get_vpl_data: " . $e->getMessage());
            return [];
        }

        // 2.- Enriquecer cada registro con el código fuente de la entrega
        $fs = get_file_storage();
        foreach ($records as $rec) {
            if (!$rec->submissionid) {
                $rec->filesource = null;
                continue;
            }
            
            // Intentar primero desde file storage
            $files = $fs->get_area_files(
                \context_system::instance()->id,
                'mod_vpl',
                'submission_files',
                $rec->submissionid,
                'itemid, filepath, filename',
                false
            );

            $sources = [];
            foreach ($files as $f) {
                $sources[] = [
                    'filename' => $f->get_filename(),
                    'content'  => $f->get_content()
                ];
            }
            
            // Si no hay archivos en file storage, leer desde moodledata
            if (empty($sources)) {
                $sources = self::get_submitted_files_from_moodledata($rec->vplid, $rec->userid, $rec->submissionid);
            }
            
            $rec->filesource = $sources;
        }
        return $records;
    }

    /** Convierte los registros en JSON jerárquico apto para la IA */
    public static function generate_json(array $records): string {
        $out = [];
        foreach ($records as $r) {
            if (!isset($out[$r->vplid])) {
                $out[$r->vplid] = [
                    'vplid'   => $r->vplid,
                    'name'    => $r->vplname,
                    'course'  => ['id' => $r->courseid, 'name' => $r->coursename],
                    'submissions' => []
                ];
            }
            if ($r->submissionid) {
                $out[$r->vplid]['submissions'][] = [
                    'submissionid' => $r->submissionid,
                    'student'      => [
                        'id'        => $r->userid,
                        'firstname' => $r->firstname,
                        'lastname'  => $r->lastname
                    ],
                    'datesubmitted' => $r->datesubmitted,
                    'grade'        => isset($r->grade) ? (float)$r->grade : null,
                    'dategraded'   => $r->dategraded ?? null,
                    'grader'       => $r->grader ?? null,
                    'files'        => $r->filesource          // << código fuente
                ];
            }
        }
        return json_encode(array_values($out), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Obtiene la última entrega VPL del estudiante actual en un VPL específico
     * @param int $vplid ID del VPL
     * @param int $userid ID del usuario (opcional, usa el actual si no se especifica)
     * @return array|null Datos de la entrega o null si no existe
     */
    public static function get_student_vpl_data(int $vplid, int $userid = null): ?array {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        // Consulta para obtener la última entrega del estudiante en el VPL específico
        // NOTA: No incluimos vpl_evaluations porque no existe - los resultados están en archivos
        $sql = "
            SELECT
                v.id              AS vplid,
                v.name            AS vplname,
                v.course          AS courseid,
                c.fullname        AS coursename,
                v.grade           AS maxgrade,

                s.id              AS submissionid,
                s.userid,
                u.firstname,
                u.lastname,
                s.datesubmitted,
                s.grade           AS grade,
                s.dategraded      AS dategraded,
                s.grader          AS grader
            FROM {vpl} v
            JOIN {course}          c ON c.id = v.course
            JOIN {vpl_submissions} s ON s.vpl = v.id
            JOIN {user}            u ON u.id = s.userid
            WHERE v.id = :vplid AND s.userid = :userid
            ORDER BY s.datesubmitted DESC
            LIMIT 1
        ";
        
        // DEBUG: Log de la consulta y parámetros
        error_log("AISTRIX DEBUG get_student_vpl_data SQL: " . $sql);
        error_log("AISTRIX DEBUG get_student_vpl_data PARAMS: " . json_encode(['vplid' => $vplid, 'userid' => $userid]));
        
        try {
            $record = $DB->get_record_sql($sql, ['vplid' => $vplid, 'userid' => $userid]);
            
            // DEBUG: Log del resultado
            error_log("AISTRIX DEBUG get_student_vpl_data RECORD: " . ($record ? 'Found' : 'Not found'));
            
        } catch (Exception $e) {
            error_log("AISTRIX ERROR in get_student_vpl_data: " . $e->getMessage());
            error_log("AISTRIX ERROR SQL: " . $sql);
            error_log("AISTRIX ERROR PARAMS: " . json_encode(['vplid' => $vplid, 'userid' => $userid]));
            return null;
        }
        
        if (!$record) {
            return null;
        }

        // Obtener archivos de código fuente - primero intentar desde file storage, luego desde moodledata
        $sources = [];
        
        // Método 1: Intentar desde file storage de Moodle
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            \context_system::instance()->id,
            'mod_vpl',
            'submission_files',
            $record->submissionid,
            'itemid, filepath, filename',
            false
        );

        foreach ($files as $f) {
            $sources[] = [
                'filename' => $f->get_filename(),
                'content'  => $f->get_content()
            ];
        }
        
        // Método 2: Si no hay archivos en file storage, leer desde moodledata
        if (empty($sources)) {
            $sources = self::get_submitted_files_from_moodledata($record->vplid, $record->userid, $record->submissionid);
        }
        
        $record->filesource = $sources;

        // Obtener resultados de ejecución desde moodledata
        $record->execution_results = self::get_execution_results($record->vplid, $record->userid, $record->submissionid);

        return (array) $record;
    }

    /**
     * Verifica si el estudiante actual tiene entregas en un VPL específico
     * @param int $vplid ID del VPL
     * @param int $userid ID del usuario (opcional)
     * @return bool True si tiene entregas
     */
    public static function student_has_submissions(int $vplid, int $userid = null): bool {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $count = $DB->count_records('vpl_submissions', [
            'vpl' => $vplid,
            'userid' => $userid
        ]);

        return $count > 0;
    }

    /**
     * Obtiene todos los VPLs donde el estudiante actual tiene entregas
     * @param int $courseid Curso específico (opcional)
     * @param int $userid ID del usuario (opcional)
     * @return array Lista de VPLs con entregas del estudiante
     */
    public static function get_student_available_vpls(int $courseid = null, int $userid = null): array {
        global $DB, $USER;

        if (!$userid) {
            $userid = $USER->id;
        }

        $sql = "
            SELECT DISTINCT
                v.id,
                v.name,
                v.course,
                c.fullname as coursename,
                COUNT(s.id) as submission_count,
                MAX(s.datesubmitted) as last_submission
            FROM {vpl} v
            JOIN {course} c ON c.id = v.course
            JOIN {vpl_submissions} s ON s.vpl = v.id
            WHERE s.userid = :userid
        ";
        
        $params = ['userid' => $userid];
        
        if ($courseid) {
            $sql .= " AND v.course = :courseid";
            $params['courseid'] = $courseid;
        }
        
        $sql .= " GROUP BY v.id, v.name, v.course, c.fullname ORDER BY last_submission DESC";

        // DEBUG: Log de la consulta y parámetros
        error_log("AISTRIX DEBUG SQL: " . $sql);
        error_log("AISTRIX DEBUG PARAMS: " . json_encode($params));
        
        try {
            $results = $DB->get_records_sql($sql, $params);
            
            // DEBUG: Log de resultados
            error_log("AISTRIX DEBUG RESULTS: " . count($results) . " records found");
            
            return $results;
        } catch (Exception $e) {
            error_log("AISTRIX ERROR in get_student_available_vpls: " . $e->getMessage());
            error_log("AISTRIX ERROR SQL: " . $sql);
            error_log("AISTRIX ERROR PARAMS: " . json_encode($params));
            return [];
        }
    }

    /** Convierte los datos del estudiante en JSON para la IA */
    public static function generate_student_json(array $record): string {
        $out = [
            'vpl' => [
                'id' => $record['vplid'],
                'name' => $record['vplname'],
                'course' => [
                    'id' => $record['courseid'],
                    'name' => $record['coursename']
                ]
            ],
            'submission' => [
                'id' => $record['submissionid'],
                'student' => [
                    'id' => $record['userid'],
                    'firstname' => $record['firstname'],
                    'lastname' => $record['lastname']
                ],
                'datesubmitted' => $record['datesubmitted'],
                'grade' => isset($record['grade']) ? (float)$record['grade'] : null,
                'dategraded' => $record['dategraded'] ?? null,
                'grader' => $record['grader'] ?? null,
                'files' => $record['filesource'],
                'execution_results' => $record['execution_results'] ?? null
            ]
        ];

        return json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Obtiene los resultados de ejecución desde moodledata
     * @param int $vplid ID del VPL
     * @param int $userid ID del usuario
     * @param int $submissionid ID de la entrega
     * @return array Resultados de ejecución
     */
    private static function get_execution_results(int $vplid, int $userid, int $submissionid): array {
        global $CFG;
        
        $results = [
            'execution_output' => null,
            'compilation_output' => null,
            'grade_comments' => null,
            'stdout' => null,
            'stderr' => null
        ];
        
        // Ruta base en moodledata: vpl_data/{vplid}/usersdata/{userid}/{submissionid}/
        $basePath = $CFG->dataroot . '/vpl_data/' . $vplid . '/usersdata/' . $userid . '/' . $submissionid . '/';
        
        // Leer execution.txt
        $executionFile = $basePath . 'execution.txt';
        if (file_exists($executionFile)) {
            $results['execution_output'] = file_get_contents($executionFile);
            
            // Parsear execution.txt para extraer stdout y stderr si están separados
            $content = $results['execution_output'];
            if (strpos($content, '--- Program output ---') !== false) {
                $parts = explode('--- Program output ---', $content);
                if (count($parts) > 1) {
                    $outputPart = $parts[1];
                    if (strpos($outputPart, '--- Expected output') !== false) {
                        $outputParts = explode('--- Expected output', $outputPart);
                        $results['stdout'] = trim($outputParts[0]);
                    } else {
                        $results['stdout'] = trim($outputPart);
                    }
                }
            }
        }
        
        // Leer compilation.txt
        $compilationFile = $basePath . 'compilation.txt';
        if (file_exists($compilationFile)) {
            $results['compilation_output'] = file_get_contents($compilationFile);
        }
        
        // Leer grade_comments.txt
        $gradeFile = $basePath . 'grade_comments.txt';
        if (file_exists($gradeFile)) {
            $results['grade_comments'] = file_get_contents($gradeFile);
        }
        
        // DEBUG: Log de archivos encontrados
        error_log("AISTRIX DEBUG execution files for VPL {$vplid}, user {$userid}, submission {$submissionid}:");
        error_log("AISTRIX DEBUG - execution.txt: " . (file_exists($executionFile) ? 'found' : 'not found'));
        error_log("AISTRIX DEBUG - compilation.txt: " . (file_exists($compilationFile) ? 'found' : 'not found'));
        error_log("AISTRIX DEBUG - grade_comments.txt: " . (file_exists($gradeFile) ? 'found' : 'not found'));
        
        return $results;
    }

    /**
     * Obtiene los archivos de código fuente desde moodledata
     * @param int $vplid ID del VPL
     * @param int $userid ID del usuario
     * @param int $submissionid ID de la entrega
     * @return array Array de archivos con filename y content
     */
    private static function get_submitted_files_from_moodledata(int $vplid, int $userid, int $submissionid): array {
        global $CFG;
        
        $files = [];
        
        // Ruta de submittedfiles: vpl_data/{vplid}/usersdata/{userid}/{submissionid}/submittedfiles/
        $submittedPath = $CFG->dataroot . '/vpl_data/' . $vplid . '/usersdata/' . $userid . '/' . $submissionid . '/submittedfiles/';
        
        // DEBUG: Log de la ruta
        error_log("AISTRIX DEBUG looking for submitted files in: " . $submittedPath);
        
        if (!is_dir($submittedPath)) {
            error_log("AISTRIX DEBUG submitted files directory not found: " . $submittedPath);
            return $files;
        }
        
        // Leer todos los archivos en la carpeta submittedfiles
        $dirHandle = opendir($submittedPath);
        if ($dirHandle) {
            while (($filename = readdir($dirHandle)) !== false) {
                if ($filename != '.' && $filename != '..' && is_file($submittedPath . $filename)) {
                    $filepath = $submittedPath . $filename;
                    $content = file_get_contents($filepath);
                    
                    if ($content !== false) {
                        $files[] = [
                            'filename' => $filename,
                            'content' => $content
                        ];
                        error_log("AISTRIX DEBUG found file: " . $filename . " (" . strlen($content) . " bytes)");
                    }
                }
            }
            closedir($dirHandle);
        }
        
        return $files;
    }
}
