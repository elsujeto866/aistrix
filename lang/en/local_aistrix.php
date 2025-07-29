<?php
/**
 * Archivo de cadenas de idioma en inglés para el plugin local_aistrix
 * 
 * Este archivo define todas las cadenas de texto utilizadas en el plugin
 * para el idioma inglés. Moodle utiliza estas cadenas para:
 * - Mostrar textos en la interfaz de usuario
 * - Mensajes de configuración y administración
 * - Textos de ayuda y descripciones
 * - Mensajes de error y confirmación
 * 
 * Convenciones de nomenclatura:
 * - 'pluginname': Nombre del plugin (obligatorio)
 * - 'config_*': Cadenas de configuración administrativa
 * - 'error_*': Mensajes de error
 * - 'help_*': Textos de ayuda
 * - 'privacy_*': Información de privacidad
 * 
 * Para añadir soporte multiidioma, crear carpetas adicionales:
 * - lang/es/local_aistrix.php (español)
 * - lang/fr/local_aistrix.php (francés)
 * - etc.
 * 
 * @package    local_aistrix
 * @copyright  2025 EPN
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// ============================================================================
// CADENAS BÁSICAS DEL PLUGIN
// ============================================================================

/** Nombre del plugin mostrado en la administración y menús */
$string['pluginname'] = 'Aistrix';

/** Descripción corta del plugin */
$string['plugindescription'] = 'AI assistant for VPL programming activities';

/** Nombre completo del plugin */
$string['aistrix'] = 'Aistrix - AI Programming Assistant';

// ============================================================================
// CONFIGURACIÓN ADMINISTRATIVA
// ============================================================================

/** Título de la sección de configuración */
$string['settings_header'] = 'Aistrix Configuration';

/** Configuración del webhook de IA */
$string['config_webhook_url'] = 'AI Webhook URL';
$string['config_webhook_url_help'] = 'URL of the external AI service to send VPL data for analysis. Leave empty to disable AI integration.';

/** Configuración de límites de uso */
$string['config_max_executions'] = 'Maximum executions per user';
$string['config_max_executions_help'] = 'Maximum number of AI requests a user can make within the reset period.';

$string['config_reset_period'] = 'Usage reset period (hours)';
$string['config_reset_period_help'] = 'Hours after which user usage counters are reset. Set to 24 for daily limits.';

/** Configuración de debugging */
$string['config_debug_mode'] = 'Debug mode';
$string['config_debug_mode_help'] = 'Enable detailed logging for troubleshooting. Only use in development environments.';

// ============================================================================
// INTERFAZ DE USUARIO
// ============================================================================

/** Textos del panel principal */
$string['welcome_title'] = 'Welcome to Aistrix';
$string['welcome_message'] = 'Your AI programming assistant';
$string['panel_greeting'] = 'Hello, {$a}! 👋';
$string['panel_description'] = 'I am Aistrix, your programming assistant.';

/** Botones y acciones */
$string['analyze_code'] = 'Analyze Code';
$string['analyze_description'] = 'I analyze your code to detect syntax errors, logic issues, and failed tests. Get clear explanations and suggestions to move forward.';
$string['send_vpl_admin'] = 'Send VPL (Admin)';
$string['select_vpl'] = 'Select VPL Activity';

/** Estados y mensajes */
$string['loading'] = 'Processing...';
$string['processing'] = 'Analyzing your code...';
$string['success'] = 'Analysis completed successfully';
$string['no_vpls_available'] = 'No VPL activities with submissions found.';
$string['no_submissions'] = 'You don\'t have submissions in VPL activities yet.';
$string['when_submit'] = 'When you make a submission, you can send your code for analysis.';

/** Información de VPL */
$string['vpl_activities'] = 'VPL Activities';
$string['total_submissions'] = 'Total submissions: {$a}';
$string['course'] = 'Course: {$a}';
$string['last_submission'] = 'Last submission';

// ============================================================================
// MENSAJES DE ERROR
// ============================================================================

/** Errores generales */
$string['error_general'] = 'An error occurred while processing your request.';
$string['error_no_vpl_selected'] = 'Please select a VPL activity first.';
$string['error_no_submissions'] = 'No submissions found for this VPL activity.';
$string['error_webhook_failed'] = 'Failed to connect to AI service. Please try again later.';
$string['error_invalid_response'] = 'Invalid response from AI service.';
$string['error_permission_denied'] = 'You don\'t have permission to perform this action.';

/** Errores de configuración */
$string['error_no_webhook_configured'] = 'AI webhook URL is not configured. Contact your administrator.';
$string['error_invalid_webhook_url'] = 'Invalid webhook URL format.';

/** Errores de límites de uso */
$string['error_usage_limit_reached'] = 'You have reached the maximum number of AI requests ({$a}) for this period.';
$string['error_usage_limit_info'] = 'Usage limits reset every {$a} hours.';

// ============================================================================
// MENSAJES DE FEEDBACK Y EXPLICACIONES
// ============================================================================

/** Títulos de feedback */
$string['ai_feedback_title'] = 'AI Analysis';
$string['explanation_title'] = 'Code Explanation';
$string['suggestions_title'] = 'Suggestions for Improvement';

/** Mensajes de footer */
$string['ai_footer'] = 'Aistrix accompanies you in your learning.';
$string['ai_footer_extended'] = 'Aistrix accompanies you in your learning.<br />Let\'s go step by step!';

/** Estados especiales */
$string['max_grade_achieved'] = 'Congratulations! You have already achieved the maximum grade for this VPL.';
$string['no_analysis_needed'] = 'No further analysis needed.';

// ============================================================================
// PRIVACIDAD Y CUMPLIMIENTO
// ============================================================================

/** Información de privacidad (GDPR) */
$string['privacy:metadata'] = 'The Aistrix plugin stores usage data to control access limits.';
$string['privacy:metadata:usage'] = 'User usage tracking';
$string['privacy:metadata:usage:userid'] = 'User ID who made the request';
$string['privacy:metadata:usage:execution_count'] = 'Number of AI requests made';
$string['privacy:metadata:usage:last_execution'] = 'Timestamp of last AI request';
$string['privacy:metadata:external:ai_service'] = 'VPL submission data is sent to external AI service for analysis';

// ============================================================================
// AYUDA Y DOCUMENTACIÓN
// ============================================================================

/** Textos de ayuda para administradores */
$string['help_webhook_setup'] = 'To set up the AI integration, configure a webhook URL that accepts POST requests with JSON data containing VPL submission information.';
$string['help_usage_limits'] = 'Usage limits help prevent abuse of the AI service. Users will be blocked when they reach the limit until the reset period expires.';
$string['help_debugging'] = 'Debug mode logs detailed information about AI requests and responses. Only enable in development environments.';

/** Información de capacidades */
$string['capability_use'] = 'Use Aistrix AI assistant';
$string['capability_manage'] = 'Manage Aistrix configuration';

// ============================================================================
// TAREAS Y MANTENIMIENTO
// ============================================================================

/** Tareas programadas */
$string['task_cleanup_usage'] = 'Clean up old Aistrix usage records';
$string['task_reset_limits'] = 'Reset Aistrix usage limits';

/** Mensajes de instalación/actualización */
$string['install_success'] = 'Aistrix plugin installed successfully';
$string['upgrade_success'] = 'Aistrix plugin upgraded successfully';
$string['table_created'] = 'Aistrix usage table created successfully';
