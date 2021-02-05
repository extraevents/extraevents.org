<?php

function generate_fifteen_training() {

    $scramble = scramble_training_from_database('fifteen');

    return $scramble;
}
