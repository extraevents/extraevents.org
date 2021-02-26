<?php

$content = file_get_contents(__DIR__.'/team.md');

$data = (object) [
            'message' => message::get_custom('team.import'),
            'markdown_team' => markdown::convertToHtml($content)
];
