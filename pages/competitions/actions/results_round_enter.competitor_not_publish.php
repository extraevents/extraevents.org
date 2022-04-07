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
exit();
if (min($args->attempts) == 0 and max($args->attempts) == 0) {
    $result = null;
    goto set_result;
}

$attempts = $args->attempts;

$best = -1;
foreach ($attempts as $attempt) {
    if ($attempt > 0) {
        if ($best == -1) {
            $best = $attempt;
        } elseif ($attempt < $best) {
            $best = $attempt;
        }
    }
}

foreach ($attempts as $a => $attempt) {
    if ($attempt == 0) {
        unset($attempts[$a]);
    }
}

$average = 0;
if ($round->format_id == 'm') {
    if (sizeof($attempts) == $round->format_solve_count) {
        if (min($attempts) < 0) {
            $average = -1;
        } else {
            $average = round(array_sum($attempts) / sizeof($attempts));
        }
    }
}

if ($round->format_id == 'a') {
    if (sizeof($attempts) == $round->format_solve_count) {
        foreach ($attempts as $a => $attempt) {
            if ($attempt < 0) {
                $attempts[$a] = PHP_INT_MAX;
            }
        }
        sort($attempts);
        array_shift($attempts);
        array_pop($attempts);
        if (max($attempts) == PHP_INT_MAX) {
            $average = -1;
        } else {
            $average = round(array_sum($attempts) / sizeof($attempts));
        }
    }
}
set_result:;
$result = (object) [
            'attempt1' => $args->attempt1,
            'attempt2' => $args->attempt2,
            'attempt3' => $args->attempt3,
            'attempt4' => $args->attempt4,
            'attempt5' => $args->attempt5,
            'best' => $best ?? 0,
            'average' => $average ?? 0
];
$affected = round::set_result($round, $card_id, $result);
if ($affected) {
    form::process(true, $result, 'results.results');
}