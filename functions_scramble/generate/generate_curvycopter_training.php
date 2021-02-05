<?php

function generate_curvycopter_training() {

    $scramble = scramble_training_from_database('curvycopter');
    return
            generate_curvycopter_helper($scramble);
}
