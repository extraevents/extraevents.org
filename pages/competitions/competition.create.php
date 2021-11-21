<?php

$competition = competition::get();
$wca_id = wcaoauth::wca_id();
$settings = json::out(
                [
                    'id' => "ENTER_HERE_ID_COMPETITION",
                    'organizers' => [$wca_id],
                    'contact' => "ENTER_HERE_EMAIL",
                    'registration_close' => date('Y-m-d', strtotime('+4 week')) . 'T00:00:00Z',
                    'events' => [
                        [
                            'id' => 'ENTER_HERE_ID_EVENT',
                            'round' => 1,
                            'format' => 'a',
                            'time_limit' => 600,
                            'competitor_limit' => 12
                        ]
                    ]
                ]
);
$settings_form = form::get('create', 'settings');
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

