<?php
// Permite configurar el URL del webhook desde la administración de Moodle


if ($hassiteconfig) {
    $settings = new admin_settingpage('local_aistrix', 'Aistrix');
    $settings->add(new admin_setting_configtext(
        'local_aistrix/webhook_url',
        'Webhook URL',
        'URL a la que se enviarán los datos VPL',
        '',
        PARAM_URL
    ));
    $ADMIN->add('localplugins', $settings);
} 