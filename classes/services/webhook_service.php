<?php
/**
 * Servicio para manejo de webhooks externos
 * 
 * Esta clase proporciona funcionalidades para enviar datos JSON a webhooks
 * externos mediante peticiones HTTP POST. Se utiliza para enviar datos de
 * entregas VPL a servicios de IA externos para su análisis.
 * 
 * Características:
 * - Envío de peticiones POST con contenido JSON
 * - Manejo de timeouts y errores HTTP
 * - Logging de respuestas para debugging
 * - Headers apropiados para APIs REST
 * 
 * @package    local_aistrix
 * @copyright  2025 EPN  
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_aistrix\services;  
  
defined('MOODLE_INTERNAL') || die();  

/**
 * Clase de servicio para comunicación con webhooks externos
 * 
 * Encapsula la lógica de envío de datos a servicios externos
 * mediante peticiones HTTP con cURL.
 */
class webhook_service {  
      
    /**
     * Envía datos JSON a un webhook externo mediante HTTP POST
     * 
     * @param string $json_data Datos JSON a enviar al webhook
     * @param string $webhook_url URL del webhook de destino
     * @return array Array con success (bool), http_code (int) y response (string)
     */
    public static function send_to_webhook($json_data, $webhook_url) {  
        $curl = curl_init();  
          
        curl_setopt_array($curl, [  
            CURLOPT_URL => $webhook_url,  
            CURLOPT_RETURNTRANSFER => true,  
            CURLOPT_POST => true,  
            CURLOPT_POSTFIELDS => $json_data,  
            CURLOPT_HTTPHEADER => [  
                'Content-Type: application/json',  
                'Content-Length: ' . strlen($json_data)  
            ],  
            CURLOPT_TIMEOUT => 30  
        ]);  
          
        $response = curl_exec($curl);  
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);  
        curl_close($curl);  
          
        return [  
            'success' => $http_code >= 200 && $http_code < 300,  
            'http_code' => $http_code,  
            'response' => $response  
        ];  
    }  
}
