<?php
// Script para testear específicamente el error "Error reading from database"
require_once('../../config.php');
require_login();

global $DB, $USER;

echo "<h2>Test específico: Error reading from database</h2>";
echo "<p>Usuario actual: {$USER->firstname} {$USER->lastname} (ID: {$USER->id})</p>";

// Test 1: Verificar si las tablas existen
echo "<h3>1. Verificación de tablas VPL:</h3>";
$tables = ['vpl', 'vpl_submissions', 'course'];
foreach ($tables as $table) {
    try {
        $count = $DB->count_records($table);
        echo "<p>✅ Tabla {$table}: {$count} registros</p>";
    } catch (Exception $e) {
        echo "<p>❌ Error en tabla {$table}: " . $e->getMessage() . "</p>";
    }
}

// Test 2: Probar la consulta problemática paso a paso
echo "<h3>2. Test de consulta get_student_available_vpls:</h3>";

// Versión original (que puede fallar)
echo "<h4>Consulta original:</h4>";
$sql_original = "
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
    GROUP BY v.id, v.name, v.course, c.fullname 
    ORDER BY last_submission DESC
";

try {
    $results = $DB->get_records_sql($sql_original, ['userid' => $USER->id]);
    echo "<p>✅ Consulta original exitosa: " . count($results) . " resultados</p>";
    foreach ($results as $result) {
        echo "<p>- VPL: {$result->name} (Entregas: {$result->submission_count})</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error en consulta original: " . $e->getMessage() . "</p>";
    
    // Test de consulta simplificada
    echo "<h4>Intentando consulta simplificada:</h4>";
    $sql_simple = "
        SELECT v.id, v.name, COUNT(s.id) as submission_count
        FROM {vpl} v
        JOIN {vpl_submissions} s ON s.vpl = v.id
        WHERE s.userid = :userid
        GROUP BY v.id, v.name
    ";
    
    try {
        $simple_results = $DB->get_records_sql($sql_simple, ['userid' => $USER->id]);
        echo "<p>✅ Consulta simplificada exitosa: " . count($simple_results) . " resultados</p>";
    } catch (Exception $e2) {
        echo "<p>❌ Error en consulta simplificada: " . $e2->getMessage() . "</p>";
    }
}

// Test 3: Probar el servicio completo
echo "<h3>3. Test del servicio get_student_vpls:</h3>";
try {
    $vpls = \local_aistrix\services\vpl_service::get_student_available_vpls();
    echo "<p>✅ Servicio exitoso: " . count($vpls) . " VPLs encontrados</p>";
} catch (Exception $e) {
    echo "<p>❌ Error en servicio: " . $e->getMessage() . "</p>";
}

// Test 4: Verificar logs recientes
echo "<h3>4. Información de debug:</h3>";
echo "<p>Revisa los logs de error de PHP para ver mensajes de AISTRIX DEBUG</p>";
echo "<p>Ubicación típica: /var/log/apache2/error.log o /var/log/nginx/error.log</p>";

?>
