<?php

function generate_113sia() {

    $str = "";
    $moves = array("u", "U", "r", "R");
    $ext = array(" ", "'", "2");
    $blocks = [];
    for ($k = 1; $k <= 2; $k++) {
        for ($j = 1; $j <= 25; $j++) {
            $move = $moves[array_rand($moves)];
            if (!isset($blocks[$move])) {
                if ($move == 'u' or $move == 'U') {
                    unset($blocks['r']);
                    unset($blocks['R']);
                }
                if ($move == 'r' or $move == 'R') {
                    unset($blocks['u']);
                    unset($blocks['U']);
                }
                $str .= $move . $ext[array_rand($ext)] . " ";
                $blocks[$move] = true;
            } else {
                $j--;
            }
        }
        if ($k == 1) {
            $str .= 'z2 ';
        }
    }

    $str = str_replace('u', "Uw", $str);
    $str = str_replace('r', "Rw", $str);
    return $str;
}
