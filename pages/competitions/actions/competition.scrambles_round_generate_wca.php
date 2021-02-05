<?php

$action = form::value('action');
$competition_id = competition::get()->id;
$date = form::value('date');
$args = form::required('ee_option', 'wca_options', 'data_tnoodle');
$round = page::get_object('round');

$ee_option = json_decode($args->ee_option, false);
$wca_options = json_decode($args->wca_options, false);
$data_tnoodle = json_decode($args->data_tnoodle, false);
$filename = round::file_scramble($round);
switch ($action) {
    case 'json':
        include 'scrambles_round_generate.json.php';
        break;
    case 'pdf':
        include 'scrambles_round_generate.pdf.php';
        break;
}

form::return("competitions/$competition_id/scrambles");
