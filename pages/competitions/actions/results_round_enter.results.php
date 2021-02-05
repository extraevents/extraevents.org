<?php

$card_id = $args->card_id;

$args->attempts = [
    ($args->attempt1 ??= 0) + 0,
    ($args->attempt2 ??= 0) + 0,
    ($args->attempt3 ??= 0) + 0,
    ($args->attempt4 ??= 0) + 0,
    ($args->attempt5 ??= 0) + 0
];

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