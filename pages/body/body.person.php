<?php

$links = [];
if (grand::resolve_access_global('competitions', 'competition.create')) {
    $links[] = (object) [
                'link' => '%i/competitions/create',
                'value' => '<i class="fas fa-plus-square"></i> ' . t('navigation.competition_create')
    ];
}

if (grand::resolve_access_global('team', 'team.import')) {
    $links[] = (object) [
                'link' => '%i/team/import',
                'value' => '<i class="fas fa-users-cog"></i> ' . t('navigation.team_import')
    ];
}
$data->links = $links;
$user = wcaoauth::get_user();
$data->user = $user;

if ($user->wca_id ?? false) {
    $data->person_link = '%i/persons/' . $user->wca_id;
} else {
    $data->person_link = false;
}
