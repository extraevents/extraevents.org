<?php

function scramble_competititon_from_database($event) {
    $table = "scramble_{$event}_competition";
    $row = db::row("
                SELECT id, scramble
                FROM $table
                WHERE timestamp_use is null
                ORDER BY rand()
                LIMIT 1");

    db::exec("
                UPDATE $table
                SET timestamp_use = CURRENT_TIMESTAMP
                WHERE id = {$row->id}");

    return trim($row->scramble);
}

function scramble_training_from_database($event) {
    $table = "scramble_{$event}_training";
    $row = db::row("
                SELECT scramble
                FROM $table
                ORDER BY rand()
                LIMIT 1");

    return trim($row->scramble);
}
