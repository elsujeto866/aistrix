<?php
// Script para limpiar cachés agresivamente
require_once('../../config.php');
require_login();

if (!is_siteadmin()) {
    die('Solo administradores');
}

echo "<h2>Limpiando cachés agresivamente</h2>";

// 1. Purgar todas las cachés
echo "<p>1. Purgando todas las cachés...</p>";
purge_all_caches();

// 2. Limpiar caché de sesiones
echo "<p>2. Limpiando caché de sesiones...</p>";
\core\session\manager::gc();

// 3. Limpiar caché de tema
echo "<p>3. Limpiando caché de tema...</p>";
theme_reset_all_caches();

// 4. Limpiar caché de JavaScript y CSS
echo "<p>4. Limpiando caché de recursos estáticos...</p>";
js_reset_all_caches();

// 5. Información de debug
echo "<h3>Información de debug:</h3>";
echo "<p>Modo dev: " . (getenv('VITE_DEV') === '1' ? 'SÍ' : 'NO') . "</p>";
echo "<p>Cachés habilitadas: " . ($CFG->debugdeveloper ? 'NO (modo desarrollador)' : 'SÍ') . "</p>";

// 6. Verificar archivo build
$buildFile = __DIR__ . '/amd/build/main.js';
if (file_exists($buildFile)) {
    $modTime = filemtime($buildFile);
    echo "<p>Último build: " . date('Y-m-d H:i:s', $modTime) . "</p>";
    echo "<p>Tamaño archivo: " . filesize($buildFile) . " bytes</p>";
} else {
    echo "<p>❌ Archivo build no encontrado</p>";
}

echo "<p>✅ Limpieza completa realizada</p>";
echo "<p><strong>Ahora recarga la página con Ctrl+F5 (forzar recarga)</strong></p>";
?>
