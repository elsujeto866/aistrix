<?php  
namespace local_aistrix\external;  
  
defined('MOODLE_INTERNAL') || die();  
  
require_once($CFG->libdir . '/externallib.php');  
  
class process_student_vpl extends \external_api {  
      
    public static function execute_parameters() {  
        return new \external_function_parameters([  
            'vplid' => new \external_value(PARAM_INT, 'VPL ID')  
        ]);  
    }  
      
    public static function execute($vplid) {
        global $CFG, $USER;
        
        // 1) Validar parámetros
        $params = self::validate_parameters(self::execute_parameters(), ['vplid' => $vplid]);
    
        // 2) Seguridad: usuario debe estar logueado
        require_login();
        
        // 3) Verificar límite de uso antes de procesar (TODO: implementar usage_service)
        /*
        $usage_service = new \local_aistrix\services\usage_service();
        if (!$usage_service->can_user_execute()) {
            return [
                'success' => false, 
                'message' => 'You have reached the maximum number of AI feedback requests for this period. Please try again later.'
            ];
        }
        */
        
        // 4) Verificar si el estudiante tiene entregas en este VPL
        if (!\local_aistrix\services\vpl_service::student_has_submissions($params['vplid'])) {
            return ['success' => false, 'message' => 'No submissions found for this VPL'];
        }

        // 5) Obtener los datos de la última entrega del estudiante
        $data = \local_aistrix\services\vpl_service::get_student_vpl_data($params['vplid']);
        if (!$data) {
            return ['success' => false, 'message' => 'No VPL data found'];
        }

        // 6) Verificar si el estudiante ya alcanzó la nota máxima
        if (isset($data['grade']) && isset($data['maxgrade']) && 
            $data['grade'] >= $data['maxgrade'] && $data['maxgrade'] > 0) {
            
            // El estudiante ya tiene la nota máxima, no enviar al webhook
            error_log("AISTRIX INFO: Student already has max grade ({$data['grade']}/{$data['maxgrade']}) for VPL {$params['vplid']}");
            
            return [
                'success' => true,
                'message' => 'Excellent work! You have already achieved the maximum grade for this assignment.',
                'vplname' => $data['vplname'],
                'studentname' => $data['firstname'] . ' ' . $data['lastname'],
                'feedback' => '¡Felicidades! Has completado esta tarea obteniendo la calificación máxima. Tu esfuerzo está dando frutos. Sigue adelante, ¡vas por buen camino!'
            ];
        }

        // 7) Registrar uso antes de enviar al webhook (TODO: implementar usage_service)
        // $usage_service->record_execution();

        // 8) Generar JSON estructurado para el estudiante
        $json = \local_aistrix\services\vpl_service::generate_student_json($data);

        // 9) Enviar al webhook
        $url = get_config('local_aistrix', 'webhook_url');
        if (!$url) {
            return ['success' => false, 'message' => 'Webhook URL not configured'];
        }

        $resp = \local_aistrix\services\webhook_service::send_to_webhook($json, $url);

        // 10) Respuesta al caller (React)
        $response = [
            'success' => $resp['success'],
            'message' => $resp['success']
                ? "Sent student VPL data OK (HTTP {$resp['http_code']})"
                : "Failed to send student VPL data (HTTP {$resp['http_code']}) — {$resp['response']}",
            'vplname' => $data['vplname'],
            'studentname' => $data['firstname'] . ' ' . $data['lastname']
        ];
        
        // 11) Si el webhook fue exitoso, extraer el feedback del JSON de respuesta
        if ($resp['success'] && !empty($resp['response'])) {
            $webhookResponse = json_decode($resp['response'], true);
            if ($webhookResponse && isset($webhookResponse['feedback'])) {
                $response['feedback'] = $webhookResponse['feedback'];
            }
        }
        
        return $response;
    }
    
      
    public static function execute_returns() {  
        return new \external_single_structure([  
            'success' => new \external_value(PARAM_BOOL, 'Success status'),  
            'message' => new \external_value(PARAM_TEXT, 'Response message'),
            'vplname' => new \external_value(PARAM_TEXT, 'VPL name', VALUE_OPTIONAL),
            'studentname' => new \external_value(PARAM_TEXT, 'Student name', VALUE_OPTIONAL),
            'feedback' => new \external_value(PARAM_RAW, 'AI feedback from webhook', VALUE_OPTIONAL)
        ]);  
    }  
}
