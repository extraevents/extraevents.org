<?php

function generate_222pyra_training() {

    $Sides = array('R', 'U', 'B');
    $Moves = array("", "'", "2");

    $tmp = '';
    $side_lock = [];
    for ($i = 0; $i < 11; $i++) {
        $Side = $Sides[array_rand($Sides)];
        if (!in_array($Side, $side_lock)) {
            $Move = $Moves[array_rand($Moves)];
            $tmp .= $Side . $Move . ' ';
            $side_lock = [$Side];
        } else {
            $i--;
        }
    }

    return trim($tmp);
}
