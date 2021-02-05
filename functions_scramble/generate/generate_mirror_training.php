<?php

function generate_mirror_training() {

    $Sides = array('R', 'L', 'U', 'D', 'B', 'F');
    $Moves = array("", "'", "2");

    $tmp = '';
    $side_lock = [];
    for ($i = 0; $i < 20; $i++) {
        $Side = $Sides[array_rand($Sides)];
        if (!in_array($Side, $side_lock)) {
            $Move = $Moves[array_rand($Moves)];
            $tmp .= $Side . $Move . ' ';

            if ($Side == 'R') {
                if (in_array('L', $side_lock)) {
                    $side_lock = ['R', 'L'];
                } else {
                    $side_lock = ['R'];
                }
            }

            if ($Side == 'L') {
                if (in_array('R', $side_lock)) {
                    $side_lock = ['L', 'R'];
                } else {
                    $side_lock = ['L'];
                }
            }

            if ($Side == 'U') {
                if (in_array('D', $side_lock)) {
                    $side_lock = ['U', 'D'];
                } else {
                    $side_lock = ['U'];
                }
            }

            if ($Side == 'D') {
                if (in_array('U', $side_lock)) {
                    $side_lock = ['D', 'U'];
                } else {
                    $side_lock = ['D'];
                }
            }

            if ($Side == 'F') {
                if (in_array('B', $side_lock)) {
                    $side_lock = ['F', 'B'];
                } else {
                    $side_lock = ['F'];
                }
            }

            if ($Side == 'B') {
                if (in_array('F', $side_lock)) {
                    $side_lock = ['B', 'F'];
                } else {
                    $side_lock = ['B'];
                }
            }
        } else {
            $i--;
        }
    }

    return trim($tmp);
}
