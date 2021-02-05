<?php

$competition = competition::get();
$settings = json::out($competition->settings());
$settings_form = form::get('save', 'settings');
if ($settings_form) {
    $settings = $settings_form;
}
$content = file_get_contents(__DIR__ . '/competition.settings.md');
$settings_error = message::get_custom('settings_error');
$data = (object) [
            'markdown_competition' => markdown::convertToHtml($content),
            'settings' => $settings,
            'settings_error' => $settings_error
];

