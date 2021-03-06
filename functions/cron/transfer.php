<?php

function transfer() {
    db::add_connection('wca');

    transfer_dictionaries();
    transfer_person();
    transfer_events();
    return
            true;
}

function transfer_dictionaries() {

    $rows = db::rows("SELECT * FROM Countries", wca::db());
    db::exec("DELETE FROM countries ");
    foreach ($rows as $row) {
        db::exec("INSERT INTO countries (id,name,continent_id,iso2)
            VALUES ('$row->id','" . db::escape($row->name) . "','$row->continentId','$row->iso2') ");
    }

    $rows = db::rows("SELECT * FROM Continents", wca::db());
    db::exec("DELETE FROM continents ");
    foreach ($rows as $row) {
        db::exec("INSERT INTO continents (id,name,record_name)
            VALUES ('$row->id','" . db::escape($row->name) . "','$row->recordName') ");
    }

    $rows = db::rows("SELECT * FROM Formats", wca::db());
    db::exec("DELETE FROM formats ");
    foreach ($rows as $row) {
        $cutoff_count = $row->expected_solve_count == 5 ? 2 : 1;
        $extra_count = $row->expected_solve_count == 5 ? 2 : 1;
        db::exec("INSERT INTO formats (id,name,sort_by,sort_by_second,solve_count,cutoff_count,extra_count)
            VALUES ('$row->id','$row->name','$row->sort_by','$row->sort_by_second','$row->expected_solve_count',$cutoff_count,$extra_count) ");
    }

    $rows = db::rows("SELECT * FROM RoundTypes", wca::db());
    db::exec("DELETE FROM round_types ");
    foreach ($rows as $row) {
        $cutoff = 0;
        $number = 0;
        switch ($row->id) {
            case 'd':$cutoff = 1;
            case '1':$number = 1;
                break;
            case 'e':$cutoff = 1;
            case '2':$number = 2;
                break;
            case 'g':$cutoff = 1;
            case '3':$number = 3;
                break;
            case 'c':$cutoff = 1;
            case 'f':$number = 4;
                break;
        }
        if ($number) {
            db::exec("INSERT INTO round_types (id,name,final,number,cutoff)
            VALUES ('$row->id','$row->name','$row->final','$number',$cutoff) ");
        }
    }
}

function transfer_person() {
    $persons = [];
    foreach (db::rows(" select distinct person from (select person1 person from `results`
                        union
                        select person2 person from `results`
                        union
                        select person3 person from `results`
                        union
                        select person4 person from `results`
                        union
                        select person person from `organizers`
                        union
                        select id person from `team`) p
                        ") as $row) {
        $persons[$row->person] = true;
    }

    $rows = db::rows("SELECT id, name, countryId FROM Persons WHERE subid = 1", wca::db());
    foreach ($rows as $row) {
        if ($persons[$row->id] ?? false) {
            person::update($row->id, $row->name, $row->countryId);
        }
    }
}

function transfer_events() {
    $events_yml = event::get_yml();
    db::exec("DELETE FROM events");
    foreach ($events_yml as $event_id => $event_yml) {
        $event = new stdClass();
        $event->id = $event_id;
        $event->name = $event_yml->name;
        $event->person_count = $event_yml->person_count ?? 1;
        $event->scramble_tnoodle_events = json_encode($event_yml->scramble_tnoodle->events ?? []);
        $event->scramble_tnoodle_hook = $event_yml->scramble_tnoodle->hook ?? false;
        $event->scramble_tnoodle_format = $event_yml->scramble_tnoodle->format ?? false;
        $event->long_inspection = ($event_yml->long_inspection ?? false) + 0;
        $event->scramble = $event_yml->scramble ?? false;
        $event->scramble_training = $event_yml->scramble_training ?? $event_yml->scramble ?? false;
        $event->drawing = $event_yml->drawing ?? false;
        $event->comments = json_encode($event_yml->comments ?? []);
        $event->custom_wrap = ($event_yml->custom_wrap ?? false) + 0;
        $event->scrambling = ($event_yml->scrambling ?? false) + 0;
        $event->icon_wca_revert = $event->scrambling ? ($event_yml->scramble_tnoodle->events[0] ?? false) : false;

        if (($event->scramble or $event->scramble_training) and!$event->drawing) {
            trigger_error(__FUNCTION__ . ": $event_id drawing");
        }

        foreach (['scramble', 'scramble_training', 'drawing', 'scramble_tnoodle_hook'] as $func) {
            if ($event->$func and!function_exists($event->$func)) {
                trigger_error(__FUNCTION__ . ": $event_id $func/{$event->$func}");
            }
        }


        db::exec("INSERT INTO events 
                    (id,name,person_count,
                    scramble_tnoodle_events,scramble_tnoodle_hook, scramble_tnoodle_format,
                    long_inspection,scramble,scramble_training,drawing,
                    comments,custom_wrap,scrambling,icon_wca_revert)
                VALUES ('$event->id','$event->name','$event->person_count',
                    '$event->scramble_tnoodle_events','$event->scramble_tnoodle_hook','$event->scramble_tnoodle_format',
                    $event->long_inspection,'$event->scramble','$event->scramble_training','$event->drawing',
                    '$event->comments',$event->custom_wrap,$event->scrambling,'$event->icon_wca_revert') ");
    }
}
