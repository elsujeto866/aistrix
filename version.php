<?php
/**
 * Archivo de definición de versión del plugin Aistrix
 * 
 * Este archivo define la información básica del plugin que Moodle necesita
 * para instalación, upgrades y compatibilidad.
 * 
 * @package    local_aistrix
 * @copyright  2025 Tu Nombre
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Nombre del componente del plugin (tipo_nombre)
$plugin->component = 'local_aistrix';

// Versión del plugin (formato: YYYYMMDDXX)
// Incrementar este número fuerza upgrade en Moodle
$plugin->version = 2025072801;

// Versión mínima de Moodle requerida (Moodle 4.0+)
$plugin->requires = 2022041900;

// Nivel de madurez del plugin
// MATURITY_ALPHA = En desarrollo, puede tener bugs
// MATURITY_BETA = Más estable, aún en pruebas  
// MATURITY_RC = Release Candidate
// MATURITY_STABLE = Listo para producción
$plugin->maturity = MATURITY_ALPHA;

// Versión legible para humanos
$plugin->release = '1.1';
