<?php

function drawing_113sia($scramble) {

    $Ceil = 50;
    $Border = 5;
    $D = 20;

    $Center = array(
        'UU' => array('x' => 6, 'y' => 1, 'Color' => 'Yellow', 'd' => ['x' => 1, 'y' => 0]),
        'UF' => array('x' => 6, 'y' => 4, 'Color' => 'Green', 'd' => ['x' => 1, 'y' => 1]),
        'UD' => array('x' => 6, 'y' => 9, 'Color' => 'White', 'd' => ['x' => 1, 'y' => 2]),
        'UL' => array('x' => 1, 'y' => 4, 'Color' => 'Red', 'd' => ['x' => 0, 'y' => 1]),
        'UR' => array('x' => 9, 'y' => 4, 'Color' => 'Orange', 'd' => ['x' => 2, 'y' => 1]),
        'UB' => array('x' => 12, 'y' => 4, 'Color' => 'Blue', 'd' => ['x' => 3, 'y' => 1]),
        'DU' => array('x' => 4, 'y' => 1, 'Color' => 'Yellow', 'd' => ['x' => 1, 'y' => 0]),
        'DF' => array('x' => 4, 'y' => 6, 'Color' => 'Green', 'd' => ['x' => 1, 'y' => 1]),
        'DD' => array('x' => 4, 'y' => 9, 'Color' => 'White', 'd' => ['x' => 1, 'y' => 2]),
        'DL' => array('x' => 1, 'y' => 6, 'Color' => 'Red', 'd' => ['x' => 0, 'y' => 1]),
        'DR' => array('x' => 9, 'y' => 6, 'Color' => 'Orange', 'd' => ['x' => 2, 'y' => 1]),
        'DB' => array('x' => 14, 'y' => 6, 'Color' => 'Blue', 'd' => ['x' => 3, 'y' => 1]),
    );


    $CenterCoor = [['x' => 0, 'y' => 0], ['x' => 1, 'y' => 0], ['x' => 1, 'y' => 1], ['x' => 0, 'y' => 1]];
    $Coor = [
        'C' => ['dx' => 0, 'dy' => 0],
        'U' => ['dx' => 0, 'dy' => -1],
        'R' => ['dx' => 1, 'dy' => 0],
        'L' => ['dx' => -1, 'dy' => 0],
        'D' => ['dx' => 0, 'dy' => 1],
        'ur' => ['dx' => 1, 'dy' => -1],
        'ul' => ['dx' => -1, 'dy' => -1],
        'dr' => ['dx' => 1, 'dy' => 1],
        'dl' => ['dx' => -1, 'dy' => 1],
    ];


    $CoorColor = array();
    foreach ($Center as $n => $center) {
        foreach ($Coor as $c => $coor) {
            $CoorColor[$n][$c] = $center['Color'];
        }
    }



    $Cycles_Cube['U'] = array(
        'R' => [
            [['UF', 'R'], ['UU', 'R'], ['UB', 'L'], ['UD', 'R']],
            [['UF', 'ur'], ['UU', 'ur'], ['UB', 'dl'], ['UD', 'ur']],
            [['UF', 'dr'], ['UU', 'dr'], ['UB', 'ul'], ['UD', 'dr']],
            [['UR', 'R'], ['UR', 'D'], ['UR', 'L'], ['UR', 'U']],
            [['UR', 'ur'], ['UR', 'dr'], ['UR', 'dl'], ['UR', 'ul']],
        ],
        'r' => [
            [['UF', 'C'], ['UU', 'C'], ['UB', 'C'], ['UD', 'C']],
            [['UF', 'U'], ['UU', 'U'], ['UB', 'D'], ['UD', 'U']],
            [['UF', 'D'], ['UU', 'D'], ['UB', 'U'], ['UD', 'D']],
        ],
        'U' => [
            [['UF', 'U'], ['UL', 'U'], ['UB', 'U'], ['UR', 'U']],
            [['UF', 'ur'], ['UL', 'ur'], ['UB', 'ur'], ['UR', 'ur']],
            [['UF', 'ul'], ['UL', 'ul'], ['UB', 'ul'], ['UR', 'ul']],
            [['UU', 'U'], ['UU', 'R'], ['UU', 'D'], ['UU', 'L']],
            [['UU', 'ur'], ['UU', 'dr'], ['UU', 'dl'], ['UU', 'ul']],
        ],
        'u' => [
            [['UF', 'C'], ['UL', 'C'], ['UB', 'C'], ['UR', 'C']],
            [['UF', 'R'], ['UL', 'R'], ['UB', 'R'], ['UR', 'R']],
            [['UF', 'L'], ['UL', 'L'], ['UB', 'L'], ['UR', 'L']],
        ]
    );

    $Cycles_Cube['D'] = array(
        'R' => [
            [['DF', 'L'], ['DD', 'L'], ['DB', 'R'], ['DU', 'L']],
            [['DF', 'dl'], ['DD', 'dl'], ['DB', 'ur'], ['DU', 'dl']],
            [['DF', 'ul'], ['DD', 'ul'], ['DB', 'dr'], ['DU', 'ul']],
            [['DL', 'R'], ['DL', 'D'], ['DL', 'L'], ['DL', 'U']],
            [['DL', 'ur'], ['DL', 'dr'], ['DL', 'dl'], ['DL', 'ul']],
        ],
        'r' => [
            [['DF', 'C'], ['DD', 'C'], ['DB', 'C'], ['DU', 'C']],
            [['DF', 'U'], ['DD', 'U'], ['DB', 'D'], ['DU', 'U']],
            [['DF', 'D'], ['DD', 'D'], ['DB', 'U'], ['DU', 'D']],
        ],
        'U' => [
            [['DF', 'D'], ['DR', 'D'], ['DB', 'D'], ['DL', 'D']],
            [['DF', 'dr'], ['DR', 'dr'], ['DB', 'dr'], ['DL', 'dr']],
            [['DF', 'dl'], ['DR', 'dl'], ['DB', 'dl'], ['DL', 'dl']],
            [['DD', 'U'], ['DD', 'R'], ['DD', 'D'], ['DD', 'L']],
            [['DD', 'ur'], ['DD', 'dr'], ['DD', 'dl'], ['DD', 'ul']],
        ],
        'u' => [
            [['DF', 'C'], ['DR', 'C'], ['DB', 'C'], ['DL', 'C']],
            [['DF', 'R'], ['DR', 'R'], ['DB', 'R'], ['DL', 'R']],
            [['DF', 'L'], ['DR', 'L'], ['DB', 'L'], ['DL', 'L']],
        ]
    );

    foreach ($Cycles_Cube['U']['R'] as $cycle) {
        $Cycles_Cube['U']['r'][] = $cycle;
    }
    foreach ($Cycles_Cube['U']['U'] as $cycle) {
        $Cycles_Cube['U']['u'][] = $cycle;
    }

    foreach ($Cycles_Cube['D']['R'] as $cycle) {
        $Cycles_Cube['D']['r'][] = $cycle;
    }
    foreach ($Cycles_Cube['D']['U'] as $cycle) {
        $Cycles_Cube['D']['u'][] = $cycle;
    }


    $scramble = str_replace("\\r", "", $scramble);
    $scramble = str_replace('\\', "", $scramble);
    $scramble = str_replace('Uw', "u", $scramble);
    $scramble = str_replace('Rw', "r", $scramble);

    $Cube = 'D';

    foreach (explode(" ", $scramble) as $move) {
        if ($move == 'z2') {
            $Cube = 'U';
            continue;
        }

        $Cycles = $Cycles_Cube[$Cube];
        $move = trim($move);
        if (strlen($move) > 0 and isset($Cycles[$move[0]])) {
            if (isset($move[1])) {
                if ($move[1] == " ") {
                    $CoorColor = Rotate($CoorColor, $Cycles, $move[0], true);
                } elseif ($move[1] == '2') {
                    $CoorColor = Rotate($CoorColor, $Cycles, $move[0], true);
                    $CoorColor = Rotate($CoorColor, $Cycles, $move[0], true);
                } elseif ($move[1] == '\'') {
                    $CoorColor = Rotate($CoorColor, $Cycles, $move[0], false);
                }
            } else {
                $CoorColor = Rotate($CoorColor, $Cycles, $move[0], true);
            }
        }
    }

    $im = imagecreate($Border * 2 + $Ceil * (3 + 5 + 3 + 5) + $D * 3, $Border * 2 + $Ceil * (3 + 5 + 3) + $D * 2);
    $white = imagecolorallocate($im, 255, 255, 255);
    $black = imagecolorallocate($im, 0, 0, 0);

    $Colors = array(
        'Red' => imagecolorallocate($im, 255, 0, 0),
        'Green' => imagecolorallocate($im, 49, 127, 67),
        'White' => imagecolorallocate($im, 255, 255, 255),
        'Blue' => imagecolorallocate($im, 0, 0, 255),
        'Yellow' => imagecolorallocate($im, 255, 255, 0),
        'Orange' => imagecolorallocate($im, 255, 165, 0)
    );


    $Polygons = array();
    foreach ($Center as $n => $center) {
        foreach ($Coor as $c => $coor) {
            if ($n == 'DU' and in_array($c, ['R', 'ur', 'dr'])) {
                continue;
            }

            if ($n == 'UD' and in_array($c, ['L', 'ul', 'dl'])) {
                continue;
            }

            if ($n == 'UL' and in_array($c, ['D', 'dr', 'dl'])) {
                continue;
            }

            if ($n == 'DR' and in_array($c, ['U', 'ur', 'ul'])) {
                continue;
            }


            $pairs = array();
            foreach ($CenterCoor as $xy) {
                $pairs[] = array(
                    $coor['dx'] + $center['x'] + $xy['x'] + $center['d']['x'] * $D / $Ceil,
                    $coor['dy'] + $center['y'] + $xy['y'] + $center['d']['y'] * $D / $Ceil,
                );
            }
            $Polygons[] = array($pairs, $Colors[$CoorColor[$n][$c]]);
        }
    }

#echo '<pre>';
#print_r($Polygons);
#echo '</pre>';
    imagesetthickness($im, 2);
    foreach ($Polygons as $Polygon) {
        $Points = array();

        foreach ($Polygon[0] as $point) {
            $point[0] = $Border + $Ceil * $point[0];
            $point[1] = $Border + $Ceil * $point[1];
            $Points[] = $point[0];
            $Points[] = $point[1];
        }
        imagefilledpolygon($im, $Points, sizeof($Points) / 2, $Polygon[1]);
        imagepolygon($im, $Points, sizeof($Points) / 2, $black);
    }


    imagesetthickness($im, 6);
    $Lines = [];
    $Lines[] = [['x' => 5 + 1 * $D / $Ceil, 'y' => 0], ['x' => 5 + 1 * $D / $Ceil, 'y' => 3]];
    $Lines[] = [['x' => 6 + 1 * $D / $Ceil, 'y' => 8 + 2 * $D / $Ceil], ['x' => 6 + 1 * $D / $Ceil, 'y' => 11 + 2 * $D / $Ceil]];
    $Lines[] = [['x' => 0, 'y' => 5 + 1 * $D / $Ceil], ['x' => 3, 'y' => 5 + 1 * $D / $Ceil]];
    $Lines[] = [['x' => 8 + 2 * $D / $Ceil, 'y' => 6 + 1 * $D / $Ceil], ['x' => 11 + 2 * $D / $Ceil, 'y' => 6 + 1 * $D / $Ceil]];

    foreach ($Lines as $Line) {
        $p_x0 = $Border + $Ceil * $Line[0]['x'];
        $p_x1 = $Border + $Ceil * $Line[1]['x'];
        $p_y0 = $Border + $Ceil * $Line[0]['y'];
        $p_y1 = $Border + $Ceil * $Line[1]['y'];
        imageline($im, $p_x0, $p_y0, $p_x1, $p_y1, $white);
    }

    return $im;
}
