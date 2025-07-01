<?php  
namespace local_aistrix\services;  
  
defined('MOODLE_INTERNAL') || die();  
  
class webhook_service {  
      
    /**  
     * Envía datos JSON a webhook externo  
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
