<?php
/**
 * Configuración del plugin Aistrix
 * 
 * Define la página de configuración que aparece en:
 * Administración del sitio → Plugins → Plugins locales → Aistrix
 * 
 * @package    local_aistrix
 * @copyright  2025 Tu Nombre
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Solo crear configuración si el usuario tiene permisos de administrador del sitio
if ($hassiteconfig) {
    
    // Crear página de configuración del plugin
    $settings = new admin_settingpage('local_aistrix', 'Aistrix');
    
    // Campo para configurar URL del webhook
    $settings->add(new admin_setting_configtext(
        'local_aistrix/webhook_url',           // Nombre interno de la configuración
        'Webhook URL',                         // Etiqueta mostrada al admin
        'URL a la que se enviarán los datos VPL para procesamiento con IA', // Descripción
        '',                                    // Valor por defecto (vacío)
        PARAM_URL                             // Tipo de validación (debe ser URL válida)
    ));
    
    // Agregar la página de configuración al menú de plugins locales
    $ADMIN->add('localplugins', $settings);
} 