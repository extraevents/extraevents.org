<?php

if (!config::isTest()) {
    page_404();
}

$base_dir = '.initial_export';

db::add_connection('wca');
db::add_connection('initial_data');

foreach (scandir(__DIR__) as $file) {
    if (strpos($file, 'export_') === 0) {
        include $file;
    }
}

transfer();

include 'migration.team.php';
include 'migration.competitions.php';
include 'migration.rounds.php';
include 'migration.competitions_wo_rounds.php';
include 'migration.results.php';

transfer_person();
