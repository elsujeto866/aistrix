<?php
// Script temporal para asignar permisos al usuario Admin
require_once('../../config.php');
require_login();

// Solo ejecutar si es el administrador principal
if (!is_siteadmin()) {
    die('Solo el administrador del sitio puede ejecutar este script');
}

echo "<h2>Asignando permisos para local_aistrix</h2>";

// Obtener el contexto del sistema
$systemcontext = context_system::instance();

// Buscar el rol de manager o administrador
$managerrole = $DB->get_record('role', ['shortname' => 'manager']);
if (!$managerrole) {
    $managerrole = $DB->get_record('role', ['shortname' => 'coursecreator']);
}

if ($managerrole) {
    echo "<p>Encontrado rol: {$managerrole->shortname} (ID: {$managerrole->id})</p>";
    
    // Asignar la capacidad al rol
    $capability = 'local/aistrix:processvpl';
    
    // Verificar si ya existe
    $existing = $DB->get_record('role_capabilities', [
        'roleid' => $managerrole->id,
        'capability' => $capability,
        'contextid' => $systemcontext->id
    ]);
    
    if ($existing) {
        echo "<p>✅ La capacidad {$capability} ya está asignada</p>";
    } else {
        // Asignar la capacidad
        $record = new stdClass();
        $record->contextid = $systemcontext->id;
        $record->roleid = $managerrole->id;
        $record->capability = $capability;
        $record->permission = CAP_ALLOW;
        $record->timemodified = time();
        $record->modifierid = $USER->id;
        
        $DB->insert_record('role_capabilities', $record);
        echo "<p>✅ Capacidad {$capability} asignada al rol {$managerrole->shortname}</p>";
    }
    
    // Ahora asignar el rol al usuario actual si no lo tiene
    $userrole = $DB->get_record('role_assignments', [
        'userid' => $USER->id,
        'roleid' => $managerrole->id,
        'contextid' => $systemcontext->id
    ]);
    
    if ($userrole) {
        echo "<p>✅ El usuario ya tiene el rol {$managerrole->shortname}</p>";
    } else {
        role_assign($managerrole->id, $USER->id, $systemcontext->id);
        echo "<p>✅ Rol {$managerrole->shortname} asignado al usuario {$USER->username}</p>";
    }
    
} else {
    echo "<p>❌ No se encontró el rol manager o coursecreator</p>";
}

echo "<p><strong>Recarga la página del plugin para probar los nuevos permisos.</strong></p>";
?>
