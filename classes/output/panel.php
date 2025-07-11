<?php  
namespace local_aistrix\output;  
  
use renderable;  
use templatable;  
use renderer_base;  
  
class panel implements renderable, templatable {  
      
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