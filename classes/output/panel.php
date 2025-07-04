<?php
namespace local_aistrix\output;

use renderable;
use templatable;
use renderer_base;

class panel implements renderable, templatable {

    public function export_for_template(renderer_base $output): array {
        return [
            'pluginurl' => (new \moodle_url('/local/aistrix'))->out(false)
        ];
    }
}