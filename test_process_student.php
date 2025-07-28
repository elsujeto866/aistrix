<?php
// Script para probar el servicio process_student_vpl directamente
require_once('../../config.php');
require_login();

global $DB, $USER;

echo "<h2>Test del servicio process_student_vpl</h2>";
echo "<p>Usuario actual: {$USER->firstname} {$USER->lastname} (ID: {$USER->id})</p>";

// 1. Obtener VPLs disponibles del estudiante
echo "<h3>1. VPLs disponibles del estudiante:</h3>";
try {
    $vpls = \local_aistrix\services\vpl_service::get_student_available_vpls();
    if (empty($vpls)) {
        echo "<p>❌ No se encontraron VPLs para este estudiante</p>";
        exit;
    }
    
    echo "<p>✅ VPLs encontrados: " . count($vpls) . "</p>";
    foreach ($vpls as $vpl) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 5px;'>";
        echo "<p><strong>VPL ID:</strong> {$vpl->id}</p>";
        echo "<p><strong>Nombre:</strong> {$vpl->name}</p>";
        echo "<p><strong>Curso:</strong> {$vpl->coursename}</p>";
        echo "<p><strong>Entregas:</strong> {$vpl->submission_count}</p>";
        echo "<p><strong>Última entrega:</strong> " . date('Y-m-d H:i:s', $vpl->last_submission) . "</p>";
        echo "</div>";
    }
    
    // Tomar el primer VPL para la prueba
    $testVpl = reset($vpls);
    $vplid = $testVpl->id;
    
} catch (Exception $e) {
    echo "<p>❌ Error obteniendo VPLs: " . $e->getMessage() . "</p>";
    exit;
}

echo "<hr>";
echo "<h3>2. Probando process_student_vpl con VPL ID: {$vplid}</h3>";

// 2. Probar cada paso del servicio process_student_vpl individualmente
echo "<h4>Paso 1: Verificar si tiene entregas</h4>";
try {
    $hasSubmissions = \local_aistrix\services\vpl_service::student_has_submissions($vplid);
    echo "<p>✅ Tiene entregas: " . ($hasSubmissions ? 'SÍ' : 'NO') . "</p>";
    
    if (!$hasSubmissions) {
        echo "<p>❌ El estudiante no tiene entregas en este VPL</p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p>❌ Error verificando entregas: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h4>Paso 2: Obtener datos del VPL del estudiante</h4>";
try {
    $studentData = \local_aistrix\services\vpl_service::get_student_vpl_data($vplid);
    
    if (!$studentData) {
        echo "<p>❌ No se pudieron obtener datos del VPL</p>";
        exit;
    }
    
    echo "<p>✅ Datos obtenidos correctamente</p>";
    echo "<div style='background: #f5f5f5; padding: 10px; margin: 10px 0;'>";
    echo "<strong>Datos del VPL:</strong><br>";
    echo "ID: " . $studentData['vplid'] . "<br>";
    echo "Nombre: " . $studentData['vplname'] . "<br>";
    echo "Curso: " . $studentData['coursename'] . "<br>";
    echo "Estudiante: " . $studentData['firstname'] . " " . $studentData['lastname'] . "<br>";
    echo "Fecha entrega: " . date('Y-m-d H:i:s', $studentData['datesubmitted']) . "<br>";
    echo "Archivos: " . count($studentData['filesource']) . " archivo(s)<br>";
    
    // Mostrar archivos de código
    if (!empty($studentData['filesource'])) {
        echo "<strong>Archivos de código:</strong><br>";
        foreach ($studentData['filesource'] as $file) {
            echo "- " . $file['filename'] . " (" . strlen($file['content']) . " caracteres)<br>";
        }
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p>❌ Error obteniendo datos del estudiante: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h4>Paso 3: Generar JSON para IA</h4>";
try {
    $json = \local_aistrix\services\vpl_service::generate_student_json($studentData);
    echo "<p>✅ JSON generado correctamente</p>";
    echo "<div style='background: #e8f5e8; padding: 10px; margin: 10px 0; max-height: 300px; overflow-y: auto;'>";
    echo "<strong>JSON generado:</strong><br>";
    echo "<pre>" . htmlspecialchars(json_encode(json_decode($json), JSON_PRETTY_PRINT)) . "</pre>";
    echo "</div>";
} catch (Exception $e) {
    echo "<p>❌ Error generando JSON: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h4>Paso 4: Verificar configuración de webhook</h4>";
$webhookUrl = get_config('local_aistrix', 'webhook_url');
if (!$webhookUrl) {
    echo "<p>⚠️ Webhook URL no configurada</p>";
    echo "<p><em>Para configurar: Administración del sitio > Plugins > Local plugins > Aistrix</em></p>";
} else {
    echo "<p>✅ Webhook URL configurada: " . htmlspecialchars($webhookUrl) . "</p>";
}

echo "<hr>";
echo "<h3>3. Ejecutar el servicio completo (simulado)</h3>";

// 3. Simular la llamada completa al servicio
try {
    // Simular los parámetros que llegarían del frontend
    $params = ['vplid' => $vplid];
    
    echo "<p><strong>Parámetros:</strong> " . json_encode($params) . "</p>";
    
    // Ejecutar el servicio usando la clase externa directamente
    $result = \local_aistrix\external\process_student_vpl::execute($vplid);
    
    echo "<p>✅ Servicio ejecutado correctamente</p>";
    echo "<div style='background: #e8f4fd; padding: 10px; margin: 10px 0;'>";
    echo "<strong>Resultado del servicio:</strong><br>";
    echo "<pre>" . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . "</pre>";
    echo "</div>";
    
    // Analizar el resultado
    if ($result['success']) {
        echo "<p style='color: green;'>🎉 <strong>¡Éxito!</strong> El servicio funcionó correctamente</p>";
    } else {
        echo "<p style='color: red;'>❌ <strong>Error:</strong> " . $result['message'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error ejecutando el servicio: " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<hr>";
echo "<h3>4. Información adicional para debugging</h3>";
echo "<p><strong>Usuario actual:</strong> {$USER->username} (ID: {$USER->id})</p>";
echo "<p><strong>Contexto:</strong> " . get_class(\context_system::instance()) . "</p>";
echo "<p><strong>Configuración:</strong></p>";
echo "<ul>";
echo "<li>Debug level: " . (isset($CFG->debug) ? $CFG->debug : 'No definido') . "</li>";
echo "<li>Developer mode: " . (isset($CFG->debugdeveloper) && $CFG->debugdeveloper ? 'SÍ' : 'NO') . "</li>";
echo "</ul>";

?>
