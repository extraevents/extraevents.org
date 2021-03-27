<?php

function generate_fifteen_training() {

    $scramble = scramble_training_from_database('fifteen');
    $scramble = str_replace('XD3 R U2 R D2 R U3 L3 ', '', 'X' . $scramble);
    return $scramble;
}
