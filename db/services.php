<?php
/**  Define functions consumibles como Web Service. */
$functions = [
    'local_aistrix_process_vpl' => [
        'classname'   => 'local_aistrix\external\process_vpl',
        'methodname'  => 'execute',
        'classpath'   => 'local/aistrix/classes/external/process_vpl.php',
        'description' => 'Process VPL data and send it to a webhook',
        'type'        => 'write',   // modifica datos (aunque sea externo)
        'ajax'        => true       // ==> invocable vía /lib/ajax/service.php sin token
    ],
    'local_aistrix_process_student_vpl' => [
        'classname'   => 'local_aistrix\external\process_student_vpl',
        'methodname'  => 'execute',
        'classpath'   => 'local/aistrix/classes/external/process_student_vpl.php',
        'description' => 'Process current student VPL submission and send to webhook',
        'type'        => 'write',
        'ajax'        => true
    ],
    'local_aistrix_get_student_vpls' => [
        'classname'   => 'local_aistrix\external\get_student_vpls',
        'methodname'  => 'execute',
        'classpath'   => 'local/aistrix/classes/external/get_student_vpls.php',
        'description' => 'Get VPLs where current student has submissions',
        'type'        => 'read',
        'ajax'        => true
    ],
];
