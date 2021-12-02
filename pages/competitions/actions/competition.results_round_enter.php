<?php

$action = form::value('action');
$round = page::get_object('round');
$competition = competition::get();

$details = [
    'competition' => $competition->id,
    'round' => $round
];

if (!$competition->show_results() or!$competition->enable_enter_results()) {
    form::process(false, $details, 'round.results_disabled!');
    form::return();
}

switch ($action) {
    case 'results':
        $args = form::required('card_id', 'attempt1', 'attempt2', 'attempt3', 'attempt4', 'attempt5');
        include 'results_round_enter.results.php';
        break;

    case 'remove':
        $args = form::required('card_id');
        $remove = 1;
        include 'results_round_enter.remove.php';
        break;

    case 'recover':
        $args = form::required('card_id');
        $remove = 0;
        include 'results_round_enter.remove.php';
        break;

    case 'finish':
        include 'results_round_enter.finish.php';
        break;
}

round::update_pos($round);
form::return();
