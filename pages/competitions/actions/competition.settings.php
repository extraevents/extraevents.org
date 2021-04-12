<?php

$competition = competition::get();
$wca_id = wcaoauth::wca_id();
if ($competition->id) {
    if (!$competition->show_settings() and!access::is_leader()) {
        message::set_custom('settings_error', t('settings.not_allowed'));
        form::return();
    }
} else {
    if (!access::is_member()) {
        message::set_custom('settings_error', t('settings.not_allowed'));
        form::return();
    }
}

$args = form::required('settings');
$json = json_decode($args->settings);
if (!$json) {
    message::set_custom('settings_error', 'JSON parser error.');
    form::return();
}

$validator = new json_schema();
$schema_json = file_get_contents(__DIR__ . '/competition.settings.json_schema.json');
$validator->validate($json, json_decode($schema_json));
$errors = [];
if (!$validator->isValid()) {
    foreach ($validator->getErrors() as $error) {
        $errors[] = sprintf("[%s] - %s<br>", $error['property'], $error['message']);
    }

    message::set_custom('settings_error', implode('<br>', $errors));
    form::return();
}

if ($competition->id) {
    if ($competition->id != $json->id) {
        message::set_custom('settings_error', "Сompetition id does not change ");
        form::return();
    }
} else {

    if ((new competition($json->id))->id) {
        message::set_custom('settings_error', "<a href='%i/competitions/$json->id'>Сompetition $json->id already exists</a>");
        form::return();
    }

    if (!access::is_leader()) {
        $json->organizers[] = $wca_id;
    }
}


$api_competition = wcaapi::get("competitions/$json->id");
if (!$api_competition) {
    message::set_custom('settings_error', "Сompetition $json->id not found on the WCA");
    form::return();
} else {
    $json->id = $api_competition->id;
    $json->api_competition = $api_competition;
}

foreach ($json->organizers as $o => $organizer) {
    $user = wcaapi::get("users/$organizer")->user ?? false;
    if (!$user) {
        $errors[] = "User $organizer not found in the WCA";
    } else {
        person::update($user->wca_id, $user->name, false, $user->country_iso2);
        $json->organizers[$o] = $user->wca_id;
    }
}

$json->organizers = array_unique($json->organizers);

$events = [];
foreach ($json->events as &$round) {
    $round->cutoff ??= 0;
    $round->time_limit ??= 0;
    $round->cutoff *= 100;
    $round->time_limit *= 100;
    $event = new event($round->id);
    if (!$event->id) {
        $errors[] = "Extra event $round->id not found.";
    } else {
        $events[$round->id][] = $round->round;
    }
}
unset($round);

if (sizeof($errors)) {
    message::set_custom('settings_error', implode('<br>', $errors));
    form::return();
}

foreach ($events as $event_id => $rounds) {
    if (sizeof($rounds) != sizeof(array_unique($rounds))
            or(max($rounds) != sizeof($rounds))
    ) {
        $errors[] = "Round numbering error for event $event_id " . json_encode($rounds) . ".";
    }
}

if (sizeof($errors)) {
    message::set_custom('settings_error', implode('<br>', $errors));
    form::return();
}

$import_result = $competition->import($json);

if ($import_result) {
    form::process(true, ['settings' => $json], 'settings.save');
    form::get('save', 'settings');
    form::return("competitions/$json->id/settings");
} else {
    form::process(false, ['settings' => $json], 'settings.save!');
    form::return('');
}
