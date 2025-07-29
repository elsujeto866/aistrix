<?php
/**
 * Definición de capacidades (permisos) para el plugin local_aistrix
 *
 * Este archivo define las capacidades (capabilities) que controlan quién puede
 * acceder a las diferentes funcionalidades del plugin aistrix.
 *
 * Las capacidades son el sistema de permisos de Moodle que determina qué usuarios
 * pueden realizar qué acciones específicas.
 *
 * @package    local_aistrix
 * @copyright  2024 EPN
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
$capabilities = [
    // Capacidad para procesar VPLs con IA
    'local/aistrix:processvpl' => [
        'riskbitmask' => RISK_DATALOSS,  // Riesgo de pérdida de datos (puede modificar datos)
        'captype' => 'write',            // Tipo: escritura (modifica datos)
        'contextlevel' => CONTEXT_SYSTEM, // Nivel de contexto: sistema completo
        'archetypes' => [
            'editingteacher' => CAP_ALLOW,  // Profesores con permisos de edición: SÍ
            'manager' => CAP_ALLOW          // Administradores: SÍ
        ]
    ],
    
    // Capacidad para ver datos de estudiantes en el plugin
    'local/aistrix:viewstudentdata' => [
        'riskbitmask' => RISK_PERSONAL,   // Riesgo personal (accede a datos personales)
        'captype' => 'read',              // Tipo: lectura (solo consulta datos)
        'contextlevel' => CONTEXT_SYSTEM, // Nivel de contexto: sistema completo
        'archetypes' => [
            'student' => CAP_ALLOW,         // Estudiantes: SÍ (pueden ver sus propios datos)
            'editingteacher' => CAP_ALLOW,  // Profesores con permisos de edición: SÍ
            'manager' => CAP_ALLOW          // Administradores: SÍ
        ]
    ]
];

/**
 * EXPLICACIÓN DE LOS CAMPOS:
 * 
 * - riskbitmask: Define el nivel de riesgo de la capacidad
 *   * RISK_DATALOSS: Puede causar pérdida de datos
 *   * RISK_PERSONAL: Accede a información personal
 *   * RISK_SPAM: Puede enviar spam
 *   * RISK_XSS: Riesgo de ataques XSS
 * 
 * - captype: Tipo de operación
 *   * 'read': Solo lectura
 *   * 'write': Escritura/modificación
 * 
 * - contextlevel: Nivel donde se aplica la capacidad
 *   * CONTEXT_SYSTEM: A nivel de todo el sistema
 *   * CONTEXT_COURSE: A nivel de curso
 *   * CONTEXT_MODULE: A nivel de módulo/actividad
 * 
 * - archetypes: Roles predeterminados que tienen esta capacidad
 *   * CAP_ALLOW: Permite la acción
 *   * CAP_PREVENT: Previene la acción
 *   * CAP_PROHIBIT: Prohíbe la acción (no se puede sobrescribir)
 */


