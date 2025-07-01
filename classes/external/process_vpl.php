<?php  
namespace local_aistrix\external;  
  
defined('MOODLE_INTERNAL') || die();  
  
require_once($CFG->libdir . '/externallib.php');  
  
class process_vpl extends \external_api {  
      
    public static function execute_parameters() {  
        return new \external_function_parameters([  
            'courseid' => new \external_value(PARAM_INT, 'Course ID', VALUE_OPTIONAL)  
        ]);  
    }  
      
    public static function execute($courseid = null) {
        global $CFG;
        // 1) Validar parámetros
        $params = self::validate_parameters(self::execute_parameters(), ['courseid' => $courseid]);
    
        // 2) Seguridad: usuario debe tener la capability (o usa require_login() si solo admins)
        require_capability('local/aistrix:processvpl', \context_system::instance());
    
        // 3) Obtener los datos VPL + código
        $data  = \local_aistrix\services\vpl_service::get_vpl_data($params['courseid']);
        if (!$data) {
            return ['success' => false, 'message' => 'No VPL data found'];
        }
    
        // 4) Generar JSON estructurado
        $json  = \local_aistrix\services\vpl_service::generate_json($data);
    
        // 5) Enviar al webhook
        $url   = get_config('local_aistrix', 'webhook_url');
        if (!$url) {
            return ['success' => false, 'message' => 'Webhook URL not configured'];
        }
    
        $resp = \local_aistrix\services\webhook_service::send_to_webhook($json, $url);
    
        // 6) Respuesta al caller (React)
        return [
            'success' => $resp['success'],
            'message' => $resp['success']
                ? "Sent OK (HTTP {$resp['http_code']})"
                : "Failed (HTTP {$resp['http_code']}) — {$resp['response']}"
        ];
    }
    
      
    public static function execute_returns() {  
        return new \external_single_structure([  
            'success' => new \external_value(PARAM_BOOL, 'Success status'),  
            'message' => new \external_value(PARAM_TEXT, 'Response message')  
        ]);  
    }  
}