<?php

function generate_223($training = false) {
    do {
        $solve = generate_223_attempt($training);
    } while (!$solve);
    return $solve;
}

function generate_223_attempt($training = false) {
    $move = array("D ", "D'", "D2", "U ", "U'", "U2", "F2", "R2", "L2");
    $str = "";
    $prev = "";
    $Us = 0;
    $Fs = 0;
    $Ds = 0;
    $UDs = 0;
    $LRs = 0;
    $Lenght = 12;
    for ($i = 1; $i <= $Lenght; $i++) {
        $rand = $move[array_rand($move)];
        if ($prev != $rand[0]) {
            $pLRs = $LRs;
            $pUDs = $UDs;
            $pUs = $Us;
            $pDs = $Ds;
            $pFs = $Fs;

            switch ($rand[0]) {
                case "U": $UDs++;
                    $Fs = 0;
                    $Us++;
                    $Ds = 0;
                    $LRs = 0;
                    break;
                case "D": $UDs++;
                    $Fs = 0;
                    $Us = 0;
                    $Ds++;
                    $LRs = 0;
                    break;
                case "F": $UDs = 0;
                    $Fs++;
                    $Us = 0;
                    $Ds = 0;
                    $LRs = 0;
                    break;
                case "R": $UDs = 0;
                    $Fs = 0;
                    $Us = 0;
                    $Ds = 0;
                    $LRs++;
                    break;
                case "L": $UDs = 0;
                    $Fs = 0;
                    $Us = 0;
                    $Ds = 0;
                    $LRs++;
                    break;
            }
            if ($UDs == 3 or $Fs == 2 or $Us == 2 or $Ds == 2 or $LRs == 2) {
                $i--;

                $UDs = $pUDs;
                $Fs = $pFs;
                $Us = $pUs;
                $Ds = $pDs;
                $LRs = $pLRs;
            } else {
                $str .= $rand . " ";
                $prev = $rand[0];
            }
        } else {
            $i--;
        }
    }

    $str = trim($str);

    if (generate_check_solve_223($str, "")) {
        return $str;
    }

    if (!$training) {
        $min_solve = 4;
        for ($d = 1; $d <= $min_solve; $d++) {
            $HelperAlgs = file(__DIR__ . "/../helper/223/algs$d.txt", true);

            foreach ($HelperAlgs as $alg) {
                if (generate_check_solve_223($str, $alg)) {
                    return false;
                }
            }
        }
    }

    return $str;
}

function generate_check_solve_223($scramble, $solve) {
    foreach (['F', 'L', 'R', 'B'] as $side) {
        foreach ([1, 2, 3] as $row) {
            foreach ([1, 2] as $column) {
                $cube[$side][$row][$column] = $side;
            }
        }
    }


    $moves = [];
    $scramble = str_replace("&", "", $scramble);

    foreach (explode(' ', $scramble) as $c) {
        if (trim($c) !== '') {
            $moves[] = trim($c);
        }
    }
    foreach (explode(' ', $solve) as $c) {
        if (trim($c) !== '') {
            $moves[] = trim($c);
        }
    }


    $Center23 = array(
        'F' => array('Color' => 2),
        'L' => array('Color' => 3),
        'R' => array('Color' => 4),
        'B' => array('Color' => 5),
    );

    $Coor23 = array(
        'u' => array('x' => 0, 'y' => -1.5),
        'r' => array('x' => 0, 'y' => .5),
        'd' => array('x' => -1, 'y' => .5),
        'l' => array('x' => -1, 'y' => -1.5),
        'R' => array('x' => 0, 'y' => -.5),
        'L' => array('x' => -1, 'y' => -.5),
    );


    $CoorColor = array();

    foreach ($Center23 as $n => $center) {
        foreach ($Coor23 as $c => $coor) {
            $CoorColor[$n][$c] = $center['Color'];
        }
    }

    $circles = array(
        'R2' => array(
            array('RR', 'RL'),
            array('FR', 'BL'),
            array('Fu', 'Bd'),
            array('Fr', 'Bl'),
            array('Ru', 'Rd'),
            array('Rr', 'Rl'),
        ),
        'L2' => array(
            array('LR', 'LL'),
            array('FL', 'BR'),
            array('Fl', 'Br'),
            array('Fd', 'Bu'),
            array('Lu', 'Ld'),
            array('Lr', 'Ll'),
        ),
        'F2' => array(
            array('FR', 'FL'),
            array('RL', 'LR'),
            array('Lu', 'Rd'),
            array('Lr', 'Rl'),
            array('Fu', 'Fd'),
            array('Fr', 'Fl'),
        ),
        'U' => array(
            array('Fu', 'Lu', 'Bu', 'Ru'),
            array('Fl', 'Ll', 'Bl', 'Rl'),
        ),
        'D' => array(
            array('Fd', 'Rd', 'Bd', 'Ld'),
            array('Fr', 'Rr', 'Br', 'Lr'),
        )
    );

    foreach ($moves as $move) {
        $move = trim($move);
        if ($move <> "") {
            if (in_array($move, array("R2", "F2", "L2"))) {
                $CoorColor = Rotate($CoorColor, $circles, $move, true);
            } elseif (in_array($move[0], array("U", "D"))) {
                if (isset($move[1])) {
                    if ($move[1] == " ") {
                        $CoorColor = Rotate($CoorColor, $circles, $move[0], true);
                    } elseif ($move[1] == '2') {
                        $CoorColor = Rotate($CoorColor, $circles, $move[0], true);
                        $CoorColor = Rotate($CoorColor, $circles, $move[0], true);
                    } elseif ($move[1] == '\'') {
                        $CoorColor = Rotate($CoorColor, $circles, $move[0], false);
                    }
                } else {
                    $CoorColor = Rotate($CoorColor, $circles, $move[0], true);
                }
            }
        }
    }
    foreach ($CoorColor as $side => $cells) {
        $cells = array_unique($cells);
        if (sizeof($cells) > 1)
            return false;
    }

    return true;
}
