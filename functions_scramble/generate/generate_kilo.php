<?php

function generate_kilo() {

    $scramble = scramble_competititon_from_database('kilo');

    return $scramble;
}
