<?php

$person = wcaoauth::get_user();
if ($person) {
    $person->name = trim(preg_replace('/\(.*?\)/', '', $person->name ?? null));
    $person->auth_count = wcaoauth::get_count_session();
}
$data = arrayToObject([
    'title' => 'Speedcubing Extra Events',
    'person' => $person
        ]);
