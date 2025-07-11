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

        // 1.- Consulta SQL (tablas vpl, submissions, evaluations, user, course)
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

                e.id              AS evaluationid,
                e.grade,
                e.dategraded,
                e.grader,
                e.stdout,
                e.stderr
            FROM {vpl} v
            JOIN {course}          c ON c.id = v.course
            LEFT JOIN {vpl_submissions} s ON s.vpl = v.id
            LEFT JOIN {user}       u ON u.id = s.userid
            LEFT JOIN {vpl_evaluations} e ON e.submission = s.id
            WHERE (:cid IS NULL OR v.course = :cid)
            ORDER BY v.id, s.datesubmitted DESC
        ";
        $records = $DB->get_records_sql($sql, ['cid' => $courseid]);

        // 2.- Enriquecer cada registro con el código fuente de la entrega
        $fs = get_file_storage();
        foreach ($records as $rec) {
            if (!$rec->submissionid) {
                $rec->filesource = null;
                continue;
            }
            // El area de archivos de un VPL se llama 'submission_files'
            $files = $fs->get_area_files(
                \context_system::instance()->id, // el contexto System funciona, pero si conoces el cmid usa context_module
                'mod_vpl',
                'submission_files',
                $rec->submissionid,
                'itemid, filepath, filename',
                false /* solo archivos, no directorios */
            );

            // Si tu actividad permite varios archivos, concaténalos.
            $sources = [];
            foreach ($files as $f) {
                // Solo texto: si los estudiantes suben binarios, filtra por mimetype.
                $sources[] = [
                    'filename' => $f->get_filename(),
                    'content'  => $f->get_content()
                ];
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
                    'evaluation'    => $r->evaluationid ? [
                        'id'         => $r->evaluationid,
                        'grade'      => (float)$r->grade,
                        'dategraded' => $r->dategraded,
                        'grader'     => $r->grader,
                        'stdout'     => $r->stdout,
                        'stderr'     => $r->stderr
                    ] : null,
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

                e.id              AS evaluationid,
                e.grade,
                e.dategraded,
                e.grader,
                e.stdout,
                e.stderr
            FROM {vpl} v
            JOIN {course}          c ON c.id = v.course
            JOIN {vpl_submissions} s ON s.vpl = v.id
            JOIN {user}            u ON u.id = s.userid
            LEFT JOIN {vpl_evaluations} e ON e.submission = s.id
            WHERE v.id = :vplid AND s.userid = :userid
            ORDER BY s.datesubmitted DESC
            LIMIT 1
        ";
        
        $record = $DB->get_record_sql($sql, ['vplid' => $vplid, 'userid' => $userid]);
        
        if (!$record) {
            return null;
        }

        // Obtener archivos de código fuente
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            \context_system::instance()->id,
            'mod_vpl',
            'submission_files',
            $record->submissionid,
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
        $record->filesource = $sources;

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
        
        $results = $DB->get_records_sql($sql, $params);
        
        // DEBUG: Log de resultados
        error_log("AISTRIX DEBUG RESULTS: " . count($results) . " records found");
        
        return $results;
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
                'evaluation' => $record['evaluationid'] ? [
                    'id' => $record['evaluationid'],
                    'grade' => (float)$record['grade'],
                    'dategraded' => $record['dategraded'],
                    'grader' => $record['grader'],
                    'stdout' => $record['stdout'],
                    'stderr' => $record['stderr']
                ] : null,
                'files' => $record['filesource']
            ]
        ];

        return json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
