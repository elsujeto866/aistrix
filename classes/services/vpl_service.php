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
}
