<?php
/**
 * Definición de servicios web (External API) del plugin local_aistrix
 * 
 * Este archivo define todos los servicios web que el plugin expone para ser
 * consumidos vía AJAX desde el frontend. Cada servicio se registra automáticamente
 * en la tabla {external_functions} de Moodle durante la instalación/upgrade.
 * @package    local_aistrix
 * @copyright  2025 EPN
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Array de definición de servicios web del plugin
 * 
 * Cada clave del array será el nombre del servicio web registrado en Moodle.
 * Los nombres siguen la convención: {component}_{function_name}
 */
$functions = [
    // Servicio para procesar datos VPL completos (modo administrador)
    'local_aistrix_process_vpl' => [
        'classname'   => 'local_aistrix\external\process_vpl',
        'methodname'  => 'execute',
        'classpath'   => 'local/aistrix/classes/external/process_vpl.php',
        'description' => 'Process VPL data and send it to a webhook',
        'type'        => 'write',   // Marcado como 'write' porque envía datos externos
        'ajax'        => true       // Invocable vía /lib/ajax/service.php sin token
    ],   
    // Servicio principal para estudiantes: procesar entrega específica
    'local_aistrix_process_student_vpl' => [
        'classname'   => 'local_aistrix\external\process_student_vpl',
        'methodname'  => 'execute',
        'classpath'   => 'local/aistrix/classes/external/process_student_vpl.php',
        'description' => 'Process current student VPL submission and send to webhook',
        'type'        => 'write',   // Envía datos al webhook y potencialmente registra uso
        'ajax'        => true       // Permite llamadas AJAX desde React
    ],  
    // Servicio para obtener lista de VPLs disponibles para el estudiante
    'local_aistrix_get_student_vpls' => [
        'classname'   => 'local_aistrix\external\get_student_vpls',
        'methodname'  => 'execute',
        'classpath'   => 'local/aistrix/classes/external/get_student_vpls.php',
        'description' => 'Get VPLs where current student has submissions',
        'type'        => 'read',    // Solo lee datos, no modifica nada
        'ajax'        => true       // Usado por el frontend para poblar el selector
    ],
];
