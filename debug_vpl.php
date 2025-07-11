<?php
// Script de debug temporal para revisar VPLs del estudiante
// Ejecutar desde: /local/aistrix/debug_vpl.php

require_once('../../config.php');
require_login();

global $DB, $USER;

echo "<h2>Debug VPL - Usuario: {$USER->firstname} {$USER->lastname} (ID: {$USER->id})</h2>";

// 1. Verificar si existen registros en vpl_submissions
echo "<h3>1. Registros en vpl_submissions para el usuario:</h3>";
$submissions = $DB->get_records('vpl_submissions', ['userid' => $USER->id]);
echo "<p>Encontrados: " . count($submissions) . " registros</p>";
if ($submissions) {
    echo "<ul>";
    foreach ($submissions as $sub) {
        echo "<li>Submission ID: {$sub->id}, VPL ID: {$sub->vpl}, Date: " . date('Y-m-d H:i:s', $sub->datesubmitted) . "</li>";
    }
    echo "</ul>";
}

// 2. Verificar si existen VPLs activos
echo "<h3>2. VPLs en el sistema:</h3>";
$vpls = $DB->get_records('vpl');
echo "<p>Total VPLs: " . count($vpls) . "</p>";
if ($vpls) {
    echo "<ul>";
    foreach ($vpls as $vpl) {
        echo "<li>VPL ID: {$vpl->id}, Name: {$vpl->name}, Course: {$vpl->course}</li>";
    }
    echo "</ul>";
}

// 3. Probar la consulta SQL directamente
echo "<h3>3. Consulta SQL directa:</h3>";
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
    WHERE s.userid = ?
    GROUP BY v.id, v.name, v.course, c.fullname 
    ORDER BY last_submission DESC
";

$params = [$USER->id];
echo "<p><strong>SQL:</strong> " . str_replace('{', $CFG->prefix, $sql) . "</p>";
echo "<p><strong>Parámetros:</strong> [" . implode(', ', $params) . "]</p>";

$results = $DB->get_records_sql($sql, $params);
echo "<p><strong>Resultados:</strong> " . count($results) . " VPLs encontrados</p>";

if ($results) {
    echo "<ul>";
    foreach ($results as $result) {
        echo "<li>VPL: {$result->name} (ID: {$result->id}) - Curso: {$result->coursename} - Entregas: {$result->submission_count}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>⚠️ No se encontraron resultados. Posibles causas:</p>";
    echo "<ul>";
    echo "<li>El usuario no tiene entregas en VPL</li>";
    echo "<li>Los cursos no existen o están ocultos</li>";
    echo "<li>Las tablas vpl o course tienen problemas</li>";
    echo "</ul>";
}

// 4. Verificar cursos disponibles
echo "<h3>4. Cursos en el sistema:</h3>";
$courses = $DB->get_records('course', null, 'id ASC', 'id, fullname, visible', 0, 10);
echo "<ul>";
foreach ($courses as $course) {
    echo "<li>Course ID: {$course->id}, Name: {$course->fullname}, Visible: {$course->visible}</li>";
}
echo "</ul>";
