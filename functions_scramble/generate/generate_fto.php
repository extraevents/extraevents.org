<?php

function generate_fto() {

    $scramble = scramble_competititon_from_database('fto');

    return $scramble;
}
