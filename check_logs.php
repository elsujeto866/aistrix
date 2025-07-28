<?php
// Script para verificar configuración de logs de Moodle
require_once('../../config.php');
require_login();

if (!is_siteadmin()) {
    die('Solo administradores');
}

echo "<h2>Configuración de Logs de Moodle</h2>";

// Mostrar configuración de debug
echo "<h3>Configuración de Debug:</h3>";
echo "<p><strong>debugdeveloper:</strong> " . (isset($CFG->debugdeveloper) && $CFG->debugdeveloper ? 'SÍ' : 'NO') . "</p>";
echo "<p><strong>debug:</strong> " . (isset($CFG->debug) ? $CFG->debug : 'No configurado') . "</p>";
echo "<p><strong>debugdisplay:</strong> " . (isset($CFG->debugdisplay) && $CFG->debugdisplay ? 'SÍ' : 'NO') . "</p>";

// Mostrar configuración de logs
echo "<h3>Configuración de Logging:</h3>";
echo "<p><strong>log_manager:</strong> " . (isset($CFG->log_manager) ? $CFG->log_manager : 'No configurado') . "</p>";

// Directorio de datos de Moodle
echo "<h3>Directorios relevantes:</h3>";
echo "<p><strong>dataroot:</strong> " . $CFG->dataroot . "</p>";
echo "<p><strong>dirroot:</strong> " . $CFG->dirroot . "</p>";

// Verificar si existe el archivo de logs de PHP
$php_error_log = ini_get('error_log');
echo "<p><strong>PHP error_log:</strong> " . ($php_error_log ? $php_error_log : 'No configurado') . "</p>";

// Mostrar errores recientes de PHP si están disponibles
if ($php_error_log && file_exists($php_error_log)) {
    echo "<h3>Últimas líneas del log de PHP:</h3>";
    $lines = array_slice(file($php_error_log), -20);
    echo "<pre style='background: #f0f0f0; padding: 10px; max-height: 300px; overflow-y: scroll;'>";
    foreach ($lines as $line) {
        if (strpos($line, 'AISTRIX') !== false) {
            echo "<span style='background: yellow;'>" . htmlspecialchars($line) . "</span>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
}

// Información sobre configuración de error_log
echo "<h3>Configuración de error_log de PHP:</h3>";
echo "<p><strong>log_errors:</strong> " . (ini_get('log_errors') ? 'SÍ' : 'NO') . "</p>";
echo "<p><strong>display_errors:</strong> " . (ini_get('display_errors') ? 'SÍ' : 'NO') . "</p>";

// Comandos útiles para ver logs
echo "<h3>Comandos útiles para ver logs:</h3>";
echo "<div style='background: #000; color: #0f0; padding: 10px; font-family: monospace;'>";
echo "# Ver logs de Apache en tiempo real:<br>";
echo "sudo tail -f /var/log/apache2/error.log<br><br>";

echo "# Buscar mensajes de AISTRIX:<br>";
echo "sudo grep 'AISTRIX' /var/log/apache2/error.log<br><br>";

echo "# Ver logs de PHP:<br>";
echo "sudo tail -f " . ($php_error_log ?: '/var/log/php/error.log') . "<br><br>";

echo "# Ver logs del sistema:<br>";
echo "sudo journalctl -f | grep php<br>";
echo "</div>";

?>
