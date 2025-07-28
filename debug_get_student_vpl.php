<?php
// Script específico para debuggear get_student_vpl_data
require_once('../../config.php');
require_login();

global $DB, $USER;

echo "<h2>Debug específico de get_student_vpl_data</h2>";
echo "<p>Usuario actual: {$USER->firstname} {$USER->lastname} (ID: {$USER->id})</p>";

// Parámetros de prueba
$vplid = 1; // VPL que sabemos que existe
$userid = $USER->id;

echo "<h3>1. Verificar que las tablas existen</h3>";

// Verificar tablas principales
$tables = ['vpl', 'vpl_submissions', 'course', 'user', 'vpl_evaluations'];
foreach ($tables as $table) {
    try {
        $count = $DB->count_records($table);
        echo "<p>✅ Tabla {$table}: {$count} registros</p>";
    } catch (Exception $e) {
        echo "<p>❌ Error en tabla {$table}: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>2. Verificar datos específicos del VPL</h3>";

// Verificar VPL
try {
    $vpl = $DB->get_record('vpl', ['id' => $vplid]);
    if ($vpl) {
        echo "<p>✅ VPL encontrado: {$vpl->name} (Curso: {$vpl->course})</p>";
    } else {
        echo "<p>❌ VPL con ID {$vplid} no encontrado</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p>❌ Error verificando VPL: " . $e->getMessage() . "</p>";
    exit;
}

// Verificar entregas del usuario
try {
    $submissions = $DB->get_records('vpl_submissions', ['vpl' => $vplid, 'userid' => $userid]);
    echo "<p>✅ Entregas del usuario: " . count($submissions) . "</p>";
    if (empty($submissions)) {
        echo "<p>❌ No hay entregas para este usuario en este VPL</p>";
        exit;
    }
    foreach ($submissions as $sub) {
        echo "<p>- Entrega ID: {$sub->id}, Fecha: " . date('Y-m-d H:i:s', $sub->datesubmitted) . "</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error verificando entregas: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h3>3. Probar la consulta SQL paso a paso</h3>";

// Consulta simple sin JOIN
echo "<h4>3.1. Consulta básica VPL</h4>";
try {
    $sql = "SELECT id, name, course FROM {vpl} WHERE id = ?";
    $result = $DB->get_record_sql($sql, [$vplid]);
    if ($result) {
        echo "<p>✅ VPL básico: " . json_encode($result) . "</p>";
    } else {
        echo "<p>❌ No se encontró VPL</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error en consulta básica VPL: " . $e->getMessage() . "</p>";
}

// Consulta con JOIN course
echo "<h4>3.2. Consulta VPL + Course</h4>";
try {
    $sql = "SELECT v.id, v.name, v.course, c.fullname 
            FROM {vpl} v 
            JOIN {course} c ON c.id = v.course 
            WHERE v.id = ?";
    $result = $DB->get_record_sql($sql, [$vplid]);
    if ($result) {
        echo "<p>✅ VPL + Course: " . json_encode($result) . "</p>";
    } else {
        echo "<p>❌ No se encontró VPL + Course</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error en consulta VPL + Course: " . $e->getMessage() . "</p>";
}

// Consulta con JOIN submissions
echo "<h4>3.3. Consulta VPL + Course + Submissions</h4>";
try {
    $sql = "SELECT v.id as vplid, v.name as vplname, v.course, c.fullname as coursename,
                   s.id as submissionid, s.userid, s.datesubmitted
            FROM {vpl} v 
            JOIN {course} c ON c.id = v.course 
            JOIN {vpl_submissions} s ON s.vpl = v.id
            WHERE v.id = ? AND s.userid = ?
            ORDER BY s.datesubmitted DESC
            LIMIT 1";
    $result = $DB->get_record_sql($sql, [$vplid, $userid]);
    if ($result) {
        echo "<p>✅ VPL + Course + Submissions: " . json_encode($result) . "</p>";
    } else {
        echo "<p>❌ No se encontró VPL + Course + Submissions</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error en consulta VPL + Course + Submissions: " . $e->getMessage() . "</p>";
}

// Consulta con JOIN user
echo "<h4>3.4. Consulta VPL + Course + Submissions + User</h4>";
try {
    $sql = "SELECT v.id as vplid, v.name as vplname, v.course, c.fullname as coursename,
                   s.id as submissionid, s.userid, s.datesubmitted,
                   u.firstname, u.lastname
            FROM {vpl} v 
            JOIN {course} c ON c.id = v.course 
            JOIN {vpl_submissions} s ON s.vpl = v.id
            JOIN {user} u ON u.id = s.userid
            WHERE v.id = ? AND s.userid = ?
            ORDER BY s.datesubmitted DESC
            LIMIT 1";
    $result = $DB->get_record_sql($sql, [$vplid, $userid]);
    if ($result) {
        echo "<p>✅ VPL + Course + Submissions + User: " . json_encode($result) . "</p>";
    } else {
        echo "<p>❌ No se encontró VPL + Course + Submissions + User</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error en consulta VPL + Course + Submissions + User: " . $e->getMessage() . "</p>";
}

// Consulta completa con LEFT JOIN evaluations
echo "<h4>3.5. Consulta completa (como en el método original)</h4>";
try {
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
    
    echo "<p><strong>SQL:</strong></p>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    echo "<p><strong>Parámetros:</strong> vplid={$vplid}, userid={$userid}</p>";
    
    $result = $DB->get_record_sql($sql, ['vplid' => $vplid, 'userid' => $userid]);
    if ($result) {
        echo "<p>✅ Consulta completa exitosa</p>";
        echo "<div style='background: #e8f5e8; padding: 10px; margin: 10px 0;'>";
        echo "<strong>Resultado:</strong><br>";
        echo "<pre>" . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";
        echo "</div>";
    } else {
        echo "<p>❌ Consulta completa no devolvió resultados</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error en consulta completa: " . $e->getMessage() . "</p>";
    echo "<p><strong>Detalles del error:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<h3>4. Verificar archivos de la entrega</h3>";
if (isset($result) && $result && $result->submissionid) {
    echo "<p>Buscando archivos para submission ID: {$result->submissionid}</p>";
    
    try {
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            \context_system::instance()->id,
            'mod_vpl',
            'submission_files',
            $result->submissionid,
            'itemid, filepath, filename',
            false
        );
        
        echo "<p>✅ Archivos encontrados: " . count($files) . "</p>";
        foreach ($files as $file) {
            echo "<p>- " . $file->get_filename() . " (" . strlen($file->get_content()) . " bytes)</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Error obteniendo archivos: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>5. Probar el método completo</h3>";
try {
    $data = \local_aistrix\services\vpl_service::get_student_vpl_data($vplid, $userid);
    if ($data) {
        echo "<p>✅ Método get_student_vpl_data ejecutado exitosamente</p>";
        echo "<div style='background: #e8f4fd; padding: 10px; margin: 10px 0;'>";
        echo "<strong>Datos devueltos:</strong><br>";
        echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>";
        echo "</div>";
    } else {
        echo "<p>❌ Método get_student_vpl_data devolvió null</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error ejecutando get_student_vpl_data: " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

?>
