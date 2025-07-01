<?php
require('../../config.php');
require_login();

$PAGE->set_url(new moodle_url('/local/aistrix/view.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Aistrix React');
$PAGE->set_heading('Panel con React + Vite');

echo $OUTPUT->header();
echo $OUTPUT->render(new \local_aistrix\output\panel());
echo $OUTPUT->footer();