<?php

$competition = competition::get();
$ulr = $competition->url();
$navigation[] = [
    "icon" => '<i class="fas fa-info-circle"></i> ',
    "url" => $ulr,
    "title" => t('competition.navigation.info')
];

if ($competition->show_register() and grand::resolve_access('competition.register')) {
    $navigation[] = [
        "icon" => '<i class="fas fa-sign-in-alt"></i>',
        "url" => "$ulr/register",
        "title" => t('competition.navigation.register')
    ];
}
if ($competition->show_registrations()) {
    $navigation[] = [
        "icon" => '<i class="fas fa-users"></i> ',
        "url" => "$ulr/registrations",
        "title" => t('competition.navigation.registrations')
    ];
}
if ($competition->show_scrambles() and grand::resolve_access('competition.scrambles')) {
    $navigation[] = [
        "icon" => '<i class="fas fa-random"></i> ',
        "url" => "$ulr/scrambles",
        "title" => t('competition.navigation.scrambles')
    ];
}
if ($competition->show_scorecards() and grand::resolve_access('competition.scorecards')) {
    $navigation[] = [
        "icon" => '<i class="far fa-file-alt"></i> ',
        "url" => "$ulr/scorecards",
        "title" => t('competition.navigation.scorecards')
    ];
}
if ($competition->show_results()) {
    $navigation[] = [
        "icon" => '<i class="fas fa-edit"></i> ',
        "url" => "$ulr/results",
        "title" => t('competition.navigation.results')
    ];
}

if (grand::resolve_access('competition.status')) {
    $navigation[] = [
        "icon" => '<i class="fas fa-shoe-prints"></i> ',
        "url" => "$ulr/status",
        "title" => t('competition.navigation.status')
    ];
}

if ((($competition->show_settings()) and grand::resolve_access('competition.settings'))
        or access::is_leader()) {
    $navigation[] = [
        "icon" => '<i class="fas fa-cog"></i> ',
        "url" => "$ulr/settings",
        "title" => t('competition.navigation.settings')
    ];
}

$data = arrayToObject([
    'competition_line' => $competition->line(),
    'navigation' => $navigation,
    'status' => $competition->status
        ]);

