<?php

$wca_id = $args->wca_id;

$person = wcaapi::get("persons/$wca_id")->person ?? false;

$details = [
    'competition' => $competition->id,
    'round' => $round
];

if (!$person) {
    form::process(false, $details, 'round.not_publish_register_wcaid!');
    form::return();
}
$wca_id = $person->wca_id;
person::update($person->wca_id, $person->name, false, $person->country_iso2);

if (round::register($wca_id, $round, false, results::OVERDUE)) {
    form::process(true, $details, 'round.not_publish_register');
} else {
    form::process(false, $details, 'round.not_publish_register!');
}
form::return();
