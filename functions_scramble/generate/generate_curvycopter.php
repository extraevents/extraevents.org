<?php

function generate_curvycopter() {

    $scramble = scramble_competititon_from_database('curvycopter');
    return
            generate_curvycopter_helper($scramble);
}
