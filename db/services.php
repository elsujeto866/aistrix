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
];
