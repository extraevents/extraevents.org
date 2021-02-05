<?php

$round = page::get_object('round');

$file_scramble = round::file_scramble($round);
ob_end_clean();
$mode = request::get(5);

$filename = "scrambles_{$round->competition_id}_{$round->event_id}_{$round->round_number}";
if ($mode == 'download') {
    header("Content-disposition: attachment; filename=\"$filename\"");
    header("Content-type: application/force-download");
} else {
    header("Content-disposition: inline; filename=\"$filename\"");
    header('Content-Type: application/pdf; charset=utf-8');
}
exit(file_get_contents($file_scramble));
