<?php

function generate_kilo_training() {

    $scramble = scramble_training_from_database('kilo');

    return $scramble;
}
