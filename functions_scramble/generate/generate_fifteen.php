<?php

function generate_fifteen() {

    $scramble = scramble_competititon_from_database('fifteen');

    return $scramble;
}
