<?php

if ($_FILES['file']['error'] != 0 or
        $_FILES['file']['type'] != 'application/json' or
        $_FILES['file']['name'] != $data_tnoodle->wcif->shortName . ".json") {
    form::process(false, false, 'scrambles_generate.wrong_file!');
    form::return();
}

$scrs = json_decode(file_get_contents($_FILES['file']['tmp_name']), true);

$scrambles_src = array();
foreach ($scrs['wcif']['events'] as $event) {
    foreach ($event['rounds'][0]['scrambleSets'] as $sets) {
        foreach ($sets['scrambles'] as $scr) {
            $scrambles_src[] = $scr;
        }
        foreach ($sets['extraScrambles'] as $scr) {
            $scrambles_src[] = $scr;
        }
    }
}

$event = new event($ee_option->event_id);
$event->thoodle_hook_function($scrambles_src);
$scrambles = [];
$r = 0;
for ($g = 0; $g < $ee_option->set_count; $g++) {
    for ($a = 1; $a <= $ee_option->solve_count + $ee_option->extra_count; $a++) {
        if (isset($scrambles_src[$r])) {
            $scrambles[$g][$a] = str_replace("\n", "", $scrambles_src[$r]);
        } else {
            form::process(false, false, 'scrambles_generate.wrong_count!');
            form::return();
        }
        $r++;
    }
}

$scramble_pdf = new scramble_pdf(
        $event
        , $ee_option->competition_name
        , $ee_option->solve_count
        , $ee_option->round_number
        , $date
        , false);
$scramble_pdf->set_scrambles($scrambles);
$pdf = $scramble_pdf->build();

$pdf->Output($filename, 'F');
form::process(true, [
    'ee_option' => $ee_option,
    'wca_options' => $wca_options,
    'data_tnoodle' => $data_tnoodle
        ], 'scrambles_generate.done');
