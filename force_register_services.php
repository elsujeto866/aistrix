<?php
// Script para forzar registro de servicios faltantes
require_once('../../config.php');
require_login();

if (!is_siteadmin()) {
    die('Solo administradores');
}

global $DB, $CFG;

echo "<h2>Forzando registro de servicios faltantes</h2>";

// Cargar la configuración de servicios
$servicesFile = __DIR__ . '/db/services.php';
if (!file_exists($servicesFile)) {
    die("Archivo services.php no encontrado");
}

// Incluir el archivo
$functions = [];
include($servicesFile);

echo "<h3>Servicios definidos en db/services.php:</h3>";
foreach ($functions as $name => $config) {
    echo "<p>📝 {$name}</p>";
}

echo "<h3>Registrando servicios faltantes:</h3>";

// Verificar y registrar cada servicio
foreach ($functions as $name => $config) {
    // Verificar si ya existe
    $existing = $DB->get_record('external_functions', ['name' => $name]);
    
    if ($existing) {
        echo "<p>✅ {$name} - Ya existe (ID: {$existing->id})</p>";
    } else {
        echo "<p>➕ Registrando {$name}...</p>";
        
        // Crear el registro
        $record = new stdClass();
        $record->name = $name;
        $record->classname = $config['classname'];
        $record->methodname = $config['methodname'];
        $record->classpath = $config['classpath'];
        $record->component = 'local_aistrix';
        $record->capabilities = $config['capabilities'] ?? '';
        $record->services = '';
        
        try {
            $id = $DB->insert_record('external_functions', $record);
            echo "<p>✅ {$name} - Registrado correctamente (ID: {$id})</p>";
        } catch (Exception $e) {
            echo "<p>❌ Error registrando {$name}: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<h3>Purgando cachés...</h3>";
purge_all_caches();
echo "<p>✅ Cachés purgadas</p>";

echo "<h3>Verificación final:</h3>";
$registeredFunctions = $DB->get_records_sql("
    SELECT * FROM {external_functions} 
    WHERE name LIKE 'local_aistrix_%'
");

foreach ($registeredFunctions as $func) {
    echo "<p>✅ {$func->name} - Registrado</p>";
}

echo "<p><strong>Total servicios registrados: " . count($registeredFunctions) . "</strong></p>";
?>
