<?php

function generate_curvycopter_helper($scramble_in) {

    $scramble = trim(str_replace("*", "[verify]", $scramble_in));

    $edges = ['UL', 'UB', 'UR', 'UF', 'LF', 'LB', 'RB', 'RF', 'DL', 'DB', 'DR', 'DF'];
    $edgeblock = [[3, 5, 1, 4], [0, 6, 2, 5], [1, 7, 3, 6], [2, 4, 0, 7], [0, 11, 3, 8], [1, 8, 0, 9],
        [2, 9, 1, 10], [3, 10, 2, 11], [4, 9, 5, 11], [5, 10, 6, 8], [6, 11, 7, 9], [7, 8, 4, 10]];
    $jumblings = [];

    foreach ($edgeblock as $n => $e) {
        $jumblings["J" . $edges[$n] . "+"] = $edges[$e[0]] . "+ " . $edges[$e[1]] . "+ " . $edges[$n] . " " . $edges[$e[0]] . "- " . $edges[$e[1]] . "-";
        $jumblings["J" . $edges[$n] . "-"] = $edges[$e[2]] . "- " . $edges[$e[3]] . "- " . $edges[$n] . " " . $edges[$e[2]] . "+ " . $edges[$e[3]] . "+";
    }

    foreach ($jumblings as $jumblingname => $jumbling) {
        $scramble = str_replace($jumbling, $jumblingname, $scramble);
    }

    foreach ($jumblings as $jumblingname => $jumbling) {
        $scramble = str_replace("($jumblingname) ($jumblingname)", "", $scramble);
        $scramble = str_replace("  ", " ", $scramble);
    }

    foreach ($jumblings as $jumblingname => $jumbling) {
        $scramble = str_replace($jumblingname, $jumbling, $scramble);
    }

    return $scramble;
}
