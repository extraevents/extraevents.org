<?php

$competition = competition::get();
$args = form::required('event', 'round', 'date', 'set_count');
$round = $competition->get_round($args->event, $args->round);

if (!$round) {
    page_404();
}

$solve_count = $round->format_solve_count;
$extra_count = $round->format_extra_count;
$date = $args->date;
$set_count = $args->set_count;
$event = new event($round->event_id);
foreach (range(1, $solve_count + $extra_count) as $attempt) {
    foreach (range(0, $set_count - 1) as $set_number) {
        $scrambles[$set_number][$attempt] = $event->generate_scramble(false);
    }
}

$scramble_pdf = new scramble_pdf(
        $event
        , $competition->name
        , $solve_count
        , $round->round_number
        , filter_input(INPUT_POST, 'date')
        , false);
$scramble_pdf->set_scrambles($scrambles);
$pdf = $scramble_pdf->build();
$file=round::file_scramble($round);
$pdf->Output($file, 'F');
form::process(true, $args, 'scrambles_generate.done');

form::return();
