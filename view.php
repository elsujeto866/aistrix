<?php
require('../../config.php');
require_login();

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/aistrix/view.php'));
$PAGE->set_title(get_string('pluginname', 'local_aistrix'));
$PAGE->set_heading(get_string('pluginname', 'local_aistrix'));
$PAGE->set_pagelayout('standard');


global $USER;
$dev = getenv('VITE_DEV') === '1';

/*$templatecontext = [
    'username' => $USER->firstname ?? 'Usuario',
    'fullname' => fullname($USER),
    'dev' => $dev
];*/


// Cargar CSS y módulo AMD
$PAGE->requires->css('/local/aistrix/amd/build/local_aistrix.css');
$PAGE->requires->js_call_amd('local_aistrix/main', 'init');

//Crear el objeto renderable
$panel = new \local_aistrix\output\panel();

// Renderizar contenido  
echo $OUTPUT->header();  
echo $OUTPUT->render($panel);  
echo $OUTPUT->footer();