<?php

function local_aistrix_before_http_headers() {
    global $PAGE;

    if ($PAGE->pagelayout !== 'embedded') {
        $PAGE->requires->js(new moodle_url('/local/aistrix/amd/build/main.js'), true);
    }
}
