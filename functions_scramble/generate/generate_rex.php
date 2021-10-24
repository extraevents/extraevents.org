<?php

function generate_rex() {

    $scramble = '';
    $flip = 'x';
    $move = ['R', 'L'];
    $ext = [" ", "'"];
    $prev_move = '';
    for ($i = 1; $i <= 8; $i++) {
        $m = rand(2, 5);
        for ($j = 1; $j <= $m; $j++) {
            $rand_move = $move[array_rand($move)];
            $rand_ext = $ext[array_rand($ext)];
            if ($prev_move == $rand_move) {
                $j--;
            } else {
                $scramble .= "$rand_move$rand_ext ";
                $prev_move = $rand_move;
            }
        }
        if ($i < 8) {
            $scramble .= "$flip  ";
        }
    }
    return $scramble;
}
