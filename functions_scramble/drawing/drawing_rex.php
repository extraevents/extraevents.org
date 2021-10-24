<?php

function drawing_rex($scramble, $training = false) {

    $Ceil = 100;
    $Border = 20;
    $D = 10;

    $Centers = [
        'F' => ['x' => 1, 'y' => 1, 'Color' => 'Green'],
        'D' => ['x' => 1, 'y' => 2, 'Color' => 'Yellow'],
        'L' => ['x' => 0, 'y' => 1, 'Color' => 'Orange'],
        'U' => ['x' => 1, 'y' => 0, 'Color' => 'White'],
        'R' => ['x' => 2, 'y' => 1, 'Color' => 'Red'],
        'B' => ['x' => 3, 'y' => 1, 'Color' => 'Blue'],
    ];

    $K1 = 0.5;
    $K2 = 0.15;
    $Coors = [
        'U' => [['x' => 0, 'y' => 0], ['x' => 1, 'y' => 0], ['x' => $K1, 'y' => $K2]],
        'R' => [['x' => 1, 'y' => 0], ['x' => 1, 'y' => 1], ['x' => 1 - $K2, 'y' => 1 - $K1]],
        'D' => [['x' => 1, 'y' => 1], ['x' => 0, 'y' => 1], ['x' => 1 - $K1, 'y' => 1 - $K2]],
        'L' => [['x' => 0, 'y' => 1], ['x' => 0, 'y' => 0], ['x' => $K2, 'y' => $K1]],
        'C' => [
            ['x' => 1 - $K1, 'y' => 1 - $K2],
            ['x' => 1 - $K2, 'y' => 1 - $K1],
            ['x' => $K1, 'y' => $K2],
            ['x' => $K2, 'y' => $K1]
        ],
    ];

    $Coors['u'] = [$Coors['U'][1], $Coors['U'][2], $Coors['R'][2]];
    $Coors['r'] = [$Coors['R'][1], $Coors['R'][2], $Coors['D'][2]];
    $Coors['d'] = [$Coors['D'][1], $Coors['D'][2], $Coors['L'][2]];
    $Coors['l'] = [$Coors['L'][1], $Coors['L'][2], $Coors['U'][2]];

    $CoorColor = array();
    foreach ($Centers as $n => $center) {
        foreach ($Coors as $c => $coor) {
            $CoorColor[$n][$c] = $center['Color'];
        }
    }

    $circles = [
        'R' => [
            ['UC', 'RC', 'FC'],
            ['UR', 'RL', 'FU'],
            ['UD', 'RU', 'FR'],
            ['Uu', 'Rd', 'Fl'],
            ['Ur', 'Rl', 'Fu'],
            ['Ud', 'Ru', 'Fr'],
        ],
        'L' => [
            ['UC', 'FC', 'LC'],
            ['UD', 'FL', 'LU'],
            ['UL', 'FU', 'LR'],
            ['Ul', 'Fu', 'Lr'],
            ['Ud', 'Fl', 'Lu'],
            ['Ur', 'Fd', 'Ll'],
        ],
        'x' => [
            ['UC', 'BC', 'DC', 'FC'],
            ['UU', 'BD', 'DU', 'FU'],
            ['UR', 'BL', 'DR', 'FR'],
            ['UD', 'BU', 'DD', 'FD'],
            ['UL', 'BR', 'DL', 'FL'],
            ['Uu', 'Bd', 'Du', 'Fu'],
            ['Ur', 'Bl', 'Dr', 'Fr'],
            ['Ud', 'Bu', 'Dd', 'Fd'],
            ['Ul', 'Br', 'Dl', 'Fl'],
            ['RU', 'RR', 'RD', 'RL'],
            ['Ru', 'Rr', 'Rd', 'Rl'],
            ['LU', 'LL', 'LD', 'LR'],
            ['Lu', 'Ll', 'Ld', 'Lr'],
        ]
    ];

    foreach (explode(" ", $scramble) as $move) {
        $move = trim($move);
        if ($move <> "" and in_array($move[0], ['R', 'L', 'x'])) {
            $direct = true;
            if (isset($move[1])) {
                $direct = false;
            }
            $CoorColor = Rotate($CoorColor, $circles, $move[0], $direct);
        }
    }

    $im = imagecreate($Border * 2 + $Ceil * 4 + $D * 3, $Border * 2 + $Ceil * 3 + $D * 2);
    $white = imagecolorallocate($im, 255, 255, 255);
    $black = imagecolorallocate($im, 0, 0, 0);

    $Colors = array(
        'Red' => imagecolorallocate($im, 255, 0, 0),
        'Green' => imagecolorallocate($im, 49, 127, 67),
        'White' => imagecolorallocate($im, 255, 255, 255),
        'Blue' => imagecolorallocate($im, 0, 0, 255),
        'Yellow' => imagecolorallocate($im, 255, 255, 0),
        'Orange' => imagecolorallocate($im, 255, 165, 0),
        'Black' => imagecolorallocate($im, 0, 0, 0),
    );


    $Polygons = array();
    foreach ($Centers as $n => $center) {
        foreach ($Coors as $c => $coor) {
            $pairs = array();
            foreach ($coor as $xy) {
                $pairs[] = array($center['x'] + $xy['x'], $center['y'] + $xy['y']);
            }
            $Polygons[] = array($pairs, $Colors[$CoorColor[$n][$c]]);
        }
    }

    foreach ($Polygons as $Polygon) {
        imagesetthickness($im, 2);

        $minX = 10000;
        $minY = 10000;
        foreach ($Polygon[0] as $point) {
            if ($minX > $point[0])
                $minX = $point[0];
            if ($minY > $point[1])
                $minY = $point[1];
        }

        $dx = floor($minX) * $D;
        $dy = floor($minY) * $D;
        $Points = array();
        foreach ($Polygon[0] as $point) {
            $point[0] = $dx + $Border + $Ceil * $point[0];
            $point[1] = $dy + $Border + $Ceil * $point[1];
            $Points[] = $point[0];
            $Points[] = $point[1];
        }

        imagefilledpolygon($im, $Points, sizeof($Points) / 2, $Polygon[1]);
        imagepolygon($im, $Points, sizeof($Points) / 2, $black);
    }

    if ($training) {

        $moveNames = [
            'R' => [2, 1, 1.5, 0.5],
            'L' => [1, 1, 0.5, 0.5],
        ];

        imagesetthickness($im, 2);
        foreach ($moveNames as $name => $coor) {
            $X = $D * $coor[2] + $Border + $coor[0] * $Ceil;
            $Y = $D * $coor[3] + $Border + $coor[1] * $Ceil;
            imagefilledellipse($im, $X, $Y, 30, 30, $Colors['White']);
            imageellipse($im, $X, $Y, 30, 30, $Colors['Black']);
            $param = image_size(18, 'fonts/arial_bold.ttf', $name);
            imagefttext($im, 18, 0, $X - $param['weith'] / 2 - $param['dx'], $Y + $param['height'] / 2 - $param['dy'], $Colors['Black'], 'fonts/arial_bold.ttf', $name);
        }

        $sideNames = [
            'U' => [1.5, 0.5, 1, 0, 'Black'],
            'F' => [1.5, 1.5, 1, 1, 'White'],
            'R' => [2.5, 1.5, 2, 1, 'Black'],
            'L' => [0.5, 1.5, 0, 1, 'Black'],
        ];

        imagesetthickness($im, 1);
        foreach ($sideNames as $name => $coor) {
            $X = $D * $coor[2] + $Border + $coor[0] * $Ceil;
            $Y = $D * $coor[3] + $Border + $coor[1] * $Ceil;
            imagefilledrectangle($im, $X - 15, $Y - 15, $X + 15, $Y + 15, $Colors[$Centers[$name]['Color']]);
            imagerectangle($im, $X - 15, $Y - 15, $X + 15, $Y + 15, $Colors['Black']);
            $param = image_size(12, 'fonts/arial_bold.ttf', $name);
            imagefttext($im, 12, 0, $X - $param['weith'] / 2 - $param['dx'], $Y + $param['height'] / 2 - $param['dy'], $Colors[$coor[4]], 'fonts/arial_bold.ttf', $name);
        }
    }

    return $im;
}
