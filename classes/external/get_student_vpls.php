<?php
/**
 * Servicio web para obtener VPLs donde el estudiante actual tiene entregas
 * 
 * Este archivo define un servicio web que permite al frontend obtener una lista
 * de actividades VPL (Virtual Programming Lab) donde el estudiante actual ha
 * realizado al menos una entrega. Se utiliza para poblar el selector de VPLs
 * en la interfaz de usuario.
 * 
 * Flujo de funcionamiento:
 * 1. Valida parámetros de entrada (courseid opcional)
 * 2. Verifica que el usuario esté logueado
 * 3. Consulta VPLs con entregas del estudiante actual
 * 4. Retorna lista formateada para el frontend
 * 
 * @package    local_aistrix
 * @copyright  2025 EPN
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_aistrix\external;  
  
defined('MOODLE_INTERNAL') || die();  
  
require_once($CFG->libdir . '/externallib.php');  

/**
 * Clase de servicio web para obtener VPLs del estudiante
 * 
 * Implementa la API externa de Moodle para exponer el servicio
 * local_aistrix_get_student_vpls como función AJAX.
 */
class get_student_vpls extends \external_api {  
      
    /**
     * Define los parámetros de entrada del servicio web
     * 
     * @return external_function_parameters Estructura de parámetros esperados
     */
    public static function execute_parameters() {  
        return new \external_function_parameters([  
            'courseid' => new \external_value(PARAM_INT, 'Course ID', VALUE_OPTIONAL)  
        ]);  
    }  
      
    /**
     * Ejecuta el servicio para obtener VPLs donde el estudiante tiene entregas
     * 
     * @param int|null $courseid ID del curso (opcional, null = todos los cursos)
     * @return array Estructura con success, vpls array y count
     */
    public static function execute($courseid = null) {
        global $CFG, $USER;
        
        // 1) Validar parámetros
        $params = self::validate_parameters(self::execute_parameters(), ['courseid' => $courseid]);
    
        // 2) Seguridad: usuario debe estar logueado
        require_login();
        
        // DEBUG: Log del usuario actual
        error_log("AISTRIX DEBUG - User ID: " . $USER->id . ", Username: " . $USER->username);
    
        // 3) Obtener VPLs donde el estudiante tiene entregas
        $vpls = \local_aistrix\services\vpl_service::get_student_available_vpls($params['courseid']);
        
        // DEBUG: Log de resultados
        error_log("AISTRIX DEBUG - VPLs encontrados: " . count($vpls));
        if (!empty($vpls)) {
            foreach ($vpls as $vpl) {
                error_log("AISTRIX DEBUG - VPL: " . $vpl->id . " - " . $vpl->name . " - Entregas: " . $vpl->submission_count);
            }
        }
        
        // 4) Formatear la respuesta
        $formatted_vpls = [];
        foreach ($vpls as $vpl) {
            $formatted_vpls[] = [
                'id' => $vpl->id,
                'name' => $vpl->name,
                'courseid' => $vpl->course,
                'coursename' => $vpl->coursename,
                'submission_count' => $vpl->submission_count,
                'last_submission' => $vpl->last_submission
            ];
        }
    
        $result = [
            'success' => true,
            'vpls' => $formatted_vpls,
            'count' => count($formatted_vpls)
        ];
        
        // DEBUG: Log del resultado final que se enviará al frontend
        error_log("AISTRIX DEBUG - Resultado final: " . json_encode($result));
        
        return $result;
    }
    
      
    /**
     * Define la estructura de datos de retorno del servicio web
     * 
     * @return external_single_structure Estructura de datos de respuesta
     */
    public static function execute_returns() {  
        return new \external_single_structure([  
            'success' => new \external_value(PARAM_BOOL, 'Success status'),  
            'vpls' => new \external_multiple_structure(
                new \external_single_structure([
                    'id' => new \external_value(PARAM_INT, 'VPL ID'),
                    'name' => new \external_value(PARAM_TEXT, 'VPL name'),
                    'courseid' => new \external_value(PARAM_INT, 'Course ID'),
                    'coursename' => new \external_value(PARAM_TEXT, 'Course name'),
                    'submission_count' => new \external_value(PARAM_INT, 'Number of submissions'),
                    'last_submission' => new \external_value(PARAM_INT, 'Last submission timestamp')
                ])
            ),
            'count' => new \external_value(PARAM_INT, 'Total VPLs count')
        ]);  
    }  
}
