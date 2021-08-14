<?php

function transfer() {
    db::add_connection('wca');

    transfer_dictionaries();
    transfer_person();
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
