<?php

function generate_fto() {

    $Sides = ['U', 'R', 'L', 'F', 'Uw', 'Rw', 'Lw'];
    $Moves = ["", "'"];
    $tmp = '';
    $layers_lock = [];
    for ($i = 0; $i < 25; $i++) {
        $Side = $Sides[array_rand($Sides)];

        if (!isset($layers_lock[$Side])) {
            $Move = $Moves[array_rand($Moves)];
            $tmp .= "$Side$Move ";
            $layers_lock[$Side] = true;
            foreach ($layers_lock as $ll => $tl) {
                if (substr($ll, 0, 1) != substr($Side, 0, 1)) {
                    unset($layers_lock[$ll]);
                }
            }
        } else {
            $i--;
        }
    }
    return trim($tmp);
}
