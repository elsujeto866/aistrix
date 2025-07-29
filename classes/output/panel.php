<?php
/**
 * Clase renderable para el panel principal de Aistrix
 * 
 * Esta clase implementa las interfaces renderable y templatable de Moodle
 * para generar el contenido del panel principal que se muestra en view.php.
 * Proporciona los datos necesarios para el template Mustache y la inicialización
 * de la aplicación React.
 * 
 * Datos exportados:
 * - URL base del plugin
 * - Información del usuario (nombre, nombre completo)
 * - Flag de desarrollo para cargar assets en modo dev
 * 
 * @package    local_aistrix
 * @copyright  2025 EPN
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_aistrix\output;  
  
use renderable;  
use templatable;  
use renderer_base;  

/**
 * Clase para renderizar el panel principal de Aistrix
 * 
 * Implementa las interfaces necesarias para integrarse con el sistema
 * de templates de Moodle y proporcionar datos al frontend.
 */
class panel implements renderable, templatable {  
      
    /**
     * Exporta datos para el template Mustache
     * 
     * Esta función se llama automáticamente por el sistema de renderizado
     * de Moodle para obtener los datos que se pasarán al template.
     * 
     * @param renderer_base $output El renderer de Moodle
     * @return array Array de datos para el template
     */
    public function export_for_template(renderer_base $output) {
        global $USER;
        
        $dev = getenv('VITE_DEV') === '1';
        
        return [
            'pluginurl' => new \moodle_url('/local/aistrix/'),
            'username' => $USER->firstname ?? 'Usuario',
            'fullname' => fullname($USER),
            'dev' => $dev
        ];
    }  
}