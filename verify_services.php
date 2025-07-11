<?php
// Script para verificar servicios registrados
require_once('../../config.php');
require_login();

if (!is_siteadmin()) {
    die('Solo administradores');
}

global $DB;

echo "<h2>Verificación de servicios web registrados</h2>";

// Buscar nuestros servicios en external_functions
$functions = $DB->get_records_sql("
    SELECT * FROM {external_functions} 
    WHERE name LIKE 'local_aistrix_%'
");

echo "<h3>Servicios encontrados en external_functions:</h3>";
if ($functions) {
    foreach ($functions as $func) {
        echo "<p>✅ {$func->name} - {$func->classname}::{$func->methodname}</p>";
    }
} else {
    echo "<p>❌ No se encontraron servicios de local_aistrix</p>";
}

// Verificar si los archivos de clase existen
echo "<h3>Verificación de archivos de clase:</h3>";
$classes = [
    'local_aistrix\external\get_student_vpls' => '/home/elsujeto/moodles/stable_main/moodle/local/aistrix/classes/external/get_student_vpls.php',
    'local_aistrix\external\process_student_vpl' => '/home/elsujeto/moodles/stable_main/moodle/local/aistrix/classes/external/process_student_vpl.php'
];

foreach ($classes as $class => $file) {
    if (file_exists($file)) {
        echo "<p>✅ {$class} - Archivo existe</p>";
        
        // Verificar si la clase se puede cargar
        try {
            if (class_exists($class)) {
                echo "<p>✅ {$class} - Clase se puede cargar</p>";
            } else {
                echo "<p>❌ {$class} - Clase NO se puede cargar</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ {$class} - Error: {$e->getMessage()}</p>";
        }
    } else {
        echo "<p>❌ {$class} - Archivo NO existe</p>";
    }
}

// Mostrar contenido de services.php
echo "<h3>Contenido de db/services.php:</h3>";
$servicesFile = '/home/elsujeto/moodles/stable_main/moodle/local/aistrix/db/services.php';
if (file_exists($servicesFile)) {
    echo "<pre>";
    echo htmlspecialchars(file_get_contents($servicesFile));
    echo "</pre>";
} else {
    echo "<p>❌ Archivo services.php no existe</p>";
}
?>
