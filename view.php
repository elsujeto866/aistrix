<?php
/**
 * Página principal del plugin local_aistrix
 * 
 * Esta es la página de entrada principal del plugin Aistrix. Se encarga de:
 * - Configurar el contexto y la página de Moodle
 * - Cargar los recursos CSS y JavaScript necesarios
 * - Inicializar la aplicación React mediante AMD
 * - Renderizar el panel principal usando el sistema de templates
 * 
 * La página carga la aplicación React que proporciona la interfaz de usuario
 * para que los estudiantes puedan enviar sus entregas VPL para análisis de IA.
 * 
 * URL: /local/aistrix/view.php
 * 
 * @package    local_aistrix
 * @copyright  2025 EPN
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Configuración básica de Moodle
require('../../config.php');
require_login();

// Configuración de la página de Moodle
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/aistrix/view.php'));
$PAGE->set_title(get_string('pluginname', 'local_aistrix'));
$PAGE->set_heading(get_string('pluginname', 'local_aistrix'));
$PAGE->set_pagelayout('standard');

// Obtener información del usuario actual
global $USER;
$dev = getenv('VITE_DEV') === '1';

// CSS compilado desde SCSS
$PAGE->requires->css('/local/aistrix/amd/build/local_aistrix.css');
// Módulo AMD que inicializa la aplicación React
$PAGE->requires->js_call_amd('local_aistrix/main', 'init');

// Crear el objeto renderable para el panel principal
$panel = new \local_aistrix\output\panel();

// Renderizar la página usando el sistema de output de Moodle
echo $OUTPUT->header();  
echo $OUTPUT->render($panel);  
echo $OUTPUT->footer();