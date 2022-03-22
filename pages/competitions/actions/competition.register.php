<?php

$args = form::required('event');
$event_id = $args->event;
$competition = competition::get();

$details = [
    'competition' => $competition->id,
    'event' => $event_id
];

if (!$competition->show_register()) {
    form::process(false, $details, 'round.registration_disabled!');
    form::return();
}

$action = form::action();
$team_key = form::value('team-key');
$user_id = wcaoauth::user_id();
$wca_id = wcaoauth::wca_id();
$round = $competition->get_round($event_id, 1);

if ($action == 'register') {

    $registrations = wcaapi::get("competitions/$competition->id/registrations") ?? [];
    $wca_registred = false;
    $users = [];
    foreach ($registrations as $registration) {
        if ($registration->user_id == $user_id) {
            $wca_registred = true;
        }
    }

    $register_round = sql_query::row('register_round',
                    [
                        'competition' => $competition->id,
                        'event' => $event_id
    ]);

    if ($register_round) {
        if ($register_round->register_count >= $register_round->competitor_limit) {
            form::process(false, $details, 'round.register_full!');
            form::return();
        }
    }

    if (!$wca_registred and!in_array($wca_id, $competition->organizers)) {
        form::process(false, $details, 'round.not_wca_registration!');
        form::return();
    }

    if (round::register($wca_id, $round, $team_key)) {
        form::process(true, $details, 'round.register');
    } else {
        form::process(false, $details, 'round.register!');
    }
}

if ($action == 'unregister') {
    if (round::unregister($wca_id, $round)) {
        form::process(true, $details, 'round.unregister');
    } else {
        form::process(false, $details, 'round.unregister!');
    }
}

form::return();
