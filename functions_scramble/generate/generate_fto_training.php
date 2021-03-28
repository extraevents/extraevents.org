<?php

function generate_fto_training() {

    $scramble = scramble_training_from_database('fto');

    return $scramble;
}
