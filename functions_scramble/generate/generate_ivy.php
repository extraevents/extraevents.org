<?php

function generate_ivy() {

    do {
        $solve = generate_ivy_attempt();
    } while (!$solve);
    return $solve;
}

function generate_ivy_attempt() {

    $Corners = array(
        "U" => rand(-1, 1),
        "F" => rand(-1, 1),
        "R" => rand(-1, 1),
        "L" => rand(-1, 1)
    );

    $positionCenters = array("U", "R", "D", "L", " ", " ");
    $Centers = array("U" => " ", "R" => " ", "r" => " ", "L" => " ", "l" => " ", "D" => " ");


    foreach ($Centers as $c => $tmp) {
        $r = array_rand($positionCenters);
        $Centers[$c] = $positionCenters[$r];
        unset($positionCenters[$r]);
    }

    if (generate_ivy_check_solver($Corners, $Centers)) {
        return false;
    }
    $max_depth = 7;
    $Cut = rand(6, 7);
    $min_solve = 3;
    for ($d = 1; $d <= $min_solve; $d++) {
        $IvyHelperAlgs = file(__DIR__."/../helper/ivy/algs$d.txt", true);
        $IvyHelperPrimes = file(__DIR__."/../helper/ivy/primes$d.txt", true);

        foreach ($IvyHelperAlgs as $alg) {
            foreach ($IvyHelperPrimes as $prime) {
                $solver = array();
                for ($i = 0; $i < $d; $i++) {
                    $solver[] = substr($alg, $i, 1) . substr($prime, $i, 1);
                }

                if (generate_ivy_solver($Corners, $Centers, $solver)) {
                    return false;
                }
            }
        }
    }

    for ($d = $Cut; $d <= $max_depth; $d++) {
        $IvyHelperAlgs = file(__DIR__."/../helper/ivy/algs$d.txt", true);
        $IvyHelperPrimes = file(__DIR__."/../helper/ivy/primes$d.txt", true);

        foreach ($IvyHelperAlgs as $alg) {
            foreach ($IvyHelperPrimes as $prime) {
                $solver = array();
                for ($i = 0; $i < $d; $i++) {
                    $solver[] = substr($alg, $i, 1) . substr($prime, $i, 1);
                }

                if (generate_ivy_solver($Corners, $Centers, $solver)) {
                    return(implode(" ", $solver));
                }
            }
        }
    }
}

function generate_ivy_solver($Corners, $Centers, $Solve) {
    $tmpCorners = $Corners;
    $tmpCenters = $Centers;


    foreach ($Solve as $move) {
        if ($move[0] == 'R') {
            if ($move[1] == ' ') {
                $tmpCenters = array(
                    'r' => $tmpCenters['R'],
                    'D' => $tmpCenters['r'],
                    'R' => $tmpCenters['D'],
                    'l' => $tmpCenters['l'],
                    'L' => $tmpCenters['L'],
                    'U' => $tmpCenters['U']);
                $tmpCorners['R']++;
            } else {
                $tmpCenters = array(
                    'r' => $tmpCenters['D'],
                    'D' => $tmpCenters['R'],
                    'R' => $tmpCenters['r'],
                    'l' => $tmpCenters['l'],
                    'L' => $tmpCenters['L'],
                    'U' => $tmpCenters['U']);
                $tmpCorners['R']--;
            }
        }
        if ($move[0] == 'L') {
            if ($move[1] == ' ') {
                $tmpCenters = array(
                    'L' => $tmpCenters['l'],
                    'l' => $tmpCenters['D'],
                    'D' => $tmpCenters['L'],
                    'r' => $tmpCenters['r'],
                    'R' => $tmpCenters['R'],
                    'U' => $tmpCenters['U']);
                $tmpCorners['L']++;
            } else {
                $tmpCenters = array(
                    'L' => $tmpCenters['D'],
                    'l' => $tmpCenters['L'],
                    'D' => $tmpCenters['l'],
                    'r' => $tmpCenters['r'],
                    'R' => $tmpCenters['R'],
                    'U' => $tmpCenters['U']);
                $tmpCorners['L']--;
            }
        }
        if ($move[0] == 'U') {
            if ($move[1] == ' ') {
                $tmpCenters = array(
                    'r' => $tmpCenters['l'],
                    'l' => $tmpCenters['U'],
                    'U' => $tmpCenters['r'],
                    'R' => $tmpCenters['R'],
                    'L' => $tmpCenters['L'],
                    'D' => $tmpCenters['D']);
                $tmpCorners['U']++;
            } else {
                $tmpCenters = array(
                    'r' => $tmpCenters['U'],
                    'l' => $tmpCenters['r'],
                    'U' => $tmpCenters['l'],
                    'R' => $tmpCenters['R'],
                    'L' => $tmpCenters['L'],
                    'D' => $tmpCenters['D']);
                $tmpCorners['U']--;
            }
        }
        if ($move[0] == 'F') {
            if ($move[1] == ' ') {
                $tmpCenters = array(
                    'R' => $tmpCenters['U'],
                    'L' => $tmpCenters['R'],
                    'U' => $tmpCenters['L'],
                    'r' => $tmpCenters['r'],
                    'l' => $tmpCenters['l'],
                    'D' => $tmpCenters['D']);
                $tmpCorners['F']++;
            } else {
                $tmpCenters = array(
                    'R' => $tmpCenters['L'],
                    'L' => $tmpCenters['U'],
                    'U' => $tmpCenters['R'],
                    'r' => $tmpCenters['r'],
                    'l' => $tmpCenters['l'],
                    'D' => $tmpCenters['D']);
                $tmpCorners['F']--;
            }
        }
    }


    return generate_ivy_check_solver($tmpCorners, $tmpCenters);
}

function generate_ivy_check_solver($Corners, $Centers) {

    foreach ($Corners as $n => $t) {
        if ($t % 3 != 0)
            return false;
    }

    foreach ($Centers as $n => $t) {
        if ($t != " " and $n != $t)
            return false;
    }
    return true;
}
