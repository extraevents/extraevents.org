<?php

function drawing_curvycopter($scramble, $training = false) {
    $Ceil = 50;
    $Border = 10;
    $D = 20;

    $edges = ['UL', 'UB', 'UR', 'UF', 'LF', 'LB', 'RB', 'RF', 'DL', 'DB', 'DR', 'DF'];
    $edgeblock = [[3, 5, 1, 4], [0, 6, 2, 5], [1, 7, 3, 6], [2, 4, 0, 7], [0, 11, 3, 8], [1, 8, 0, 9],
        [2, 9, 1, 10], [3, 10, 2, 11], [4, 9, 5, 11], [5, 10, 6, 8], [6, 11, 7, 9], [7, 8, 4, 10]];
    $jumblings = [];

    $scramble = str_replace(["(", ")"], "", $scramble);

    foreach ($edgeblock as $n => $e) {
        $jumblings["J" . $edges[$n] . "+"] = $edges[$e[0]] . "+ " . $edges[$e[1]] . "+ " . $edges[$n] . " " . $edges[$e[0]] . "- " . $edges[$e[1]] . "-";
        $jumblings["J" . $edges[$n] . "-"] = $edges[$e[2]] . "- " . $edges[$e[3]] . "- " . $edges[$n] . " " . $edges[$e[2]] . "+ " . $edges[$e[3]] . "+";
    }

    foreach ($jumblings as $jumblingname => $jumbling) {
        $scramble = str_replace($jumbling, $jumblingname, $scramble);
    }

    $Center = array(
        'F' => array('x' => 3, 'y' => 3, 'Color' => 'Green'),
        'D' => array('x' => 3, 'y' => 6, 'Color' => 'Yellow'),
        'L' => array('x' => 0, 'y' => 3, 'Color' => 'Orange'),
        'U' => array('x' => 3, 'y' => 0, 'Color' => 'White'),
        'R' => array('x' => 6, 'y' => 3, 'Color' => 'Red'),
        'B' => array('x' => 9, 'y' => 3, 'Color' => 'Blue'),
    );


    $Coors = array(
        'ul' => array(array('x' => 0, 'y' => 0), array('x' => 1, 'y' => 0), array('x' => 0, 'y' => 1)),
        'ur' => array(array('x' => 2, 'y' => 0), array('x' => 3, 'y' => 0), array('x' => 3, 'y' => 1)),
        'dr' => array(array('x' => 2, 'y' => 3), array('x' => 3, 'y' => 3), array('x' => 3, 'y' => 2)),
        'dl' => array(array('x' => 0, 'y' => 2), array('x' => 0, 'y' => 3), array('x' => 1, 'y' => 3)),
        'u' => array(array('x' => 1, 'y' => 0), array('x' => 1.5, 'y' => 1.5), array('x' => 2, 'y' => 0)),
        'r' => array(array('x' => 3, 'y' => 1), array('x' => 1.5, 'y' => 1.5), array('x' => 3, 'y' => 2)),
        'd' => array(array('x' => 1, 'y' => 3), array('x' => 1.5, 'y' => 1.5), array('x' => 2, 'y' => 3)),
        'l' => array(array('x' => 0, 'y' => 1), array('x' => 1.5, 'y' => 1.5), array('x' => 0, 'y' => 2)),
        'UL' => [['x' => 0, 'y' => 1], ['x' => 1, 'y' => 0], ['x' => 1.5, 'y' => 1.5]],
        'UR' => [['x' => 3, 'y' => 1], ['x' => 2, 'y' => 0], ['x' => 1.5, 'y' => 1.5]],
        'DL' => [['x' => 0, 'y' => 2], ['x' => 1, 'y' => 3], ['x' => 1.5, 'y' => 1.5]],
        'DR' => [['x' => 3, 'y' => 2], ['x' => 2, 'y' => 3], ['x' => 1.5, 'y' => 1.5]],
    );

    $CoorColor = array();
    foreach ($Center as $n => $center) {
        foreach ($Coors as $c => $coor) {
            $CoorColor[$n][$c] = $center['Color'];
        }
    }

    $circles = array(
        'UB' => [[['B', 'u'], ['U', 'u']],
            [['B', 'UL'], ['U', 'UL']], [['B', 'UR'], ['U', 'UR']],
            [['B', 'ul'], ['U', 'ul']], [['B', 'ur'], ['U', 'ur']],
            [['R', 'ur'], ['L', 'ul']]],
        'UF' => [[['U', 'd'], ['F', 'u']],
            [['U', 'DR'], ['F', 'UL']], [['U', 'DL'], ['F', 'UR']],
            [['U', 'dr'], ['F', 'ul']], [['U', 'dl'], ['F', 'ur']],
            [['R', 'ul'], ['L', 'ur']]],
        'DF' => [[['F', 'd'], ['D', 'u']],
            [['F', 'DR'], ['D', 'UL']], [['F', 'DL'], ['D', 'UR']],
            [['F', 'dr'], ['D', 'ul']], [['F', 'dl'], ['D', 'ur']],
            [['R', 'dl'], ['L', 'dr']]],
        'DB' => [[['D', 'd'], ['B', 'd']],
            [['D', 'DL'], ['B', 'DL']], [['D', 'DR'], ['B', 'DR']],
            [['D', 'dl'], ['B', 'dl']], [['D', 'dr'], ['B', 'dr']],
            [['R', 'dr'], ['L', 'dl']]],
        'LB' => [[['B', 'r'], ['L', 'l']],
            [['B', 'UR'], ['L', 'DL']], [['B', 'DR'], ['L', 'UL']],
            [['B', 'ur'], ['L', 'dl']], [['B', 'dr'], ['L', 'ul']],
            [['U', 'ul'], ['D', 'dl']]],
        'LF' => [[['L', 'r'], ['F', 'l']],
            [['L', 'UR'], ['F', 'DL']], [['L', 'DR'], ['F', 'UL']],
            [['L', 'ur'], ['F', 'dl']], [['L', 'dr'], ['F', 'ul']],
            [['U', 'dl'], ['D', 'ul']]],
        'RF' => [[['F', 'r'], ['R', 'l']],
            [['F', 'UR'], ['R', 'DL']], [['F', 'DR'], ['R', 'UL']],
            [['F', 'ur'], ['R', 'dl']], [['F', 'dr'], ['R', 'ul']],
            [['U', 'dr'], ['D', 'ur']]],
        'RB' => [[['R', 'r'], ['B', 'l']],
            [['R', 'UR'], ['B', 'DL']], [['R', 'DR'], ['B', 'UL']],
            [['R', 'ur'], ['B', 'dl']], [['R', 'dr'], ['B', 'ul']],
            [['U', 'ur'], ['D', 'dr']]],
        'UR' => [[['U', 'r'], ['R', 'u']],
            [['U', 'UR'], ['R', 'UL']], [['U', 'DR'], ['R', 'UR']],
            [['U', 'ur'], ['R', 'ul']], [['U', 'dr'], ['R', 'ur']],
            [['F', 'ur'], ['B', 'ul']]],
        'UL' => [[['U', 'l'], ['L', 'u']],
            [['U', 'UL'], ['L', 'UR']], [['U', 'DL'], ['L', 'UL']],
            [['U', 'ul'], ['L', 'ur']], [['U', 'dl'], ['L', 'ul']],
            [['F', 'ul'], ['B', 'ur']]],
        'DR' => [[['D', 'r'], ['R', 'd']],
            [['D', 'UR'], ['R', 'DR']], [['D', 'DR'], ['R', 'DL']],
            [['D', 'ur'], ['R', 'dr']], [['D', 'dr'], ['R', 'dl']],
            [['F', 'dr'], ['B', 'dl']]],
        'DL' => [[['D', 'l'], ['L', 'd']],
            [['D', 'UL'], ['L', 'DL']], [['D', 'DL'], ['L', 'DR']],
            [['D', 'ul'], ['L', 'dl']], [['D', 'dl'], ['L', 'dr']],
            [['F', 'dl'], ['B', 'dr']]],
        'JUF+' => [[['U', 'd'], ['F', 'u']],
            [['U', 'DL'], ['F', 'UR']], [['U', 'dl'], ['F', 'ur']],
            [['L', 'UR'], ['R', 'UL']], [['L', 'ur'], ['R', 'ul']],
            [['U', 'dr'], ['F', 'ul']]],
        'JUF-' => [[['U', 'd'], ['F', 'u']],
            [['U', 'DR'], ['F', 'UL']], [['U', 'dr'], ['F', 'ul']],
            [['R', 'UL'], ['L', 'UR']], [['R', 'ul'], ['L', 'ur']],
            [['U', 'dl'], ['F', 'ur']]],
        'JDF+' => [[['F', 'd'], ['D', 'u']],
            [['F', 'DL'], ['D', 'UR']], [['F', 'dl'], ['D', 'ur']],
            [['L', 'DR'], ['R', 'DL']], [['L', 'dr'], ['R', 'dl']],
            [['F', 'dr'], ['D', 'ul']]],
        'JDF-' => [[['F', 'd'], ['D', 'u']],
            [['F', 'DR'], ['D', 'UL']], [['F', 'dr'], ['D', 'ul']],
            [['R', 'DL'], ['L', 'DR']], [['R', 'dl'], ['L', 'dr']],
            [['F', 'dl'], ['D', 'ur']]],
        'JUB+' => [[['B', 'u'], ['U', 'u']],
            [['B', 'UR'], ['U', 'UR']], [['B', 'ur'], ['U', 'ur']],
            [['L', 'UL'], ['R', 'UR']], [['L', 'ul'], ['R', 'ur']],
            [['B', 'ul'], ['U', 'ul']]],
        'JUB-' => [[['B', 'u'], ['U', 'u']],
            [['B', 'UL'], ['U', 'UL']], [['B', 'ul'], ['U', 'ul']],
            [['R', 'UR'], ['L', 'UL']], [['R', 'ur'], ['L', 'ul']],
            [['B', 'ur'], ['U', 'ur']]],
        'JDB+' => [[['D', 'd'], ['B', 'd']],
            [['B', 'DL'], ['D', 'DL']], [['B', 'dl'], ['D', 'dl']],
            [['L', 'DL'], ['R', 'DR']], [['L', 'dl'], ['R', 'dr']],
            [['B', 'dr'], ['D', 'dr']]],
        'JDB-' => [[['D', 'd'], ['B', 'd']],
            [['B', 'DR'], ['D', 'DR']], [['B', 'dr'], ['D', 'dr']],
            [['R', 'DR'], ['L', 'DL']], [['R', 'dr'], ['L', 'dl']],
            [['B', 'dl'], ['D', 'dl']]],
        'JLB+' => [[['B', 'r'], ['L', 'l']],
            [['L', 'UL'], ['B', 'DR']], [['L', 'ul'], ['B', 'dr']],
            [['U', 'UL'], ['D', 'DL']], [['U', 'ul'], ['D', 'dl']],
            [['L', 'dl'], ['B', 'ur']]],
        'JLB-' => [[['B', 'r'], ['L', 'l']],
            [['L', 'DL'], ['B', 'UR']], [['L', 'dl'], ['B', 'ur']],
            [['U', 'UL'], ['D', 'DL']], [['U', 'ul'], ['D', 'dl']],
            [['L', 'ul'], ['B', 'dr']]],
        'JLF+' => [[['L', 'r'], ['F', 'l']],
            [['F', 'UL'], ['L', 'DR']], [['F', 'ul'], ['L', 'dr']],
            [['U', 'DL'], ['D', 'UL']], [['U', 'dl'], ['D', 'ul']],
            [['F', 'dl'], ['L', 'ur']]],
        'JLF-' => [[['L', 'r'], ['F', 'l']],
            [['F', 'DL'], ['L', 'UR']], [['F', 'dl'], ['L', 'ur']],
            [['U', 'DL'], ['D', 'UL']], [['U', 'dl'], ['D', 'ul']],
            [['F', 'ul'], ['L', 'dr']]],
        'JRF+' => [[['F', 'r'], ['R', 'l']],
            [['R', 'UL'], ['F', 'DR']], [['R', 'ul'], ['F', 'dr']],
            [['U', 'DR'], ['D', 'UR']], [['U', 'dr'], ['D', 'ur']],
            [['R', 'dl'], ['F', 'ur']]],
        'JRF-' => [[['F', 'r'], ['R', 'l']],
            [['R', 'DL'], ['F', 'UR']], [['R', 'dl'], ['F', 'ur']],
            [['U', 'DR'], ['D', 'UR']], [['U', 'dr'], ['D', 'ur']],
            [['R', 'ul'], ['F', 'dr']]],
        'JRB+' => [[['R', 'r'], ['B', 'l']],
            [['B', 'UL'], ['R', 'DR']], [['B', 'ul'], ['R', 'dr']],
            [['U', 'UR'], ['D', 'DR']], [['U', 'ur'], ['D', 'dr']],
            [['B', 'dl'], ['R', 'ur']]],
        'JRB-' => [[['R', 'r'], ['B', 'l']],
            [['B', 'DL'], ['R', 'UR']], [['B', 'dl'], ['R', 'ur']],
            [['U', 'UR'], ['D', 'DR']], [['U', 'ur'], ['D', 'dr']],
            [['B', 'ul'], ['R', 'dr']]],
        'JUR+' => [[['U', 'r'], ['R', 'u']],
            [['U', 'DR'], ['R', 'UR']], [['U', 'dr'], ['R', 'ur']],
            [['F', 'UR'], ['B', 'UL']], [['F', 'ur'], ['B', 'ul']],
            [['U', 'ur'], ['R', 'ul']]],
        'JUR-' => [[['U', 'r'], ['R', 'u']],
            [['U', 'UR'], ['R', 'UL']], [['U', 'ur'], ['R', 'ul']],
            [['F', 'UR'], ['B', 'UL']], [['F', 'ur'], ['B', 'ul']],
            [['U', 'dr'], ['R', 'ur']]],
        'JUL+' => [[['U', 'l'], ['L', 'u']],
            [['U', 'UL'], ['L', 'UR']], [['U', 'ul'], ['L', 'ur']],
            [['F', 'UL'], ['B', 'UR']], [['F', 'ul'], ['B', 'ur']],
            [['U', 'dl'], ['L', 'ul']]],
        'JUL-' => [[['U', 'l'], ['L', 'u']],
            [['U', 'DL'], ['L', 'UL']], [['U', 'dl'], ['L', 'ul']],
            [['F', 'UL'], ['B', 'UR']], [['F', 'ul'], ['B', 'ur']],
            [['U', 'ul'], ['L', 'ur']]],
        'JDR+' => [[['D', 'r'], ['R', 'd']],
            [['D', 'DR'], ['R', 'DL']], [['D', 'dr'], ['R', 'dl']],
            [['F', 'DR'], ['B', 'DL']], [['F', 'dr'], ['B', 'dl']],
            [['D', 'ur'], ['R', 'dr']]],
        'JDR-' => [[['D', 'r'], ['R', 'd']],
            [['D', 'UR'], ['R', 'DR']], [['D', 'ur'], ['R', 'dr']],
            [['F', 'DR'], ['B', 'DL']], [['F', 'dr'], ['B', 'dl']],
            [['D', 'dr'], ['R', 'dl']]],
        'JDL+' => [[['D', 'l'], ['L', 'd']],
            [['D', 'UL'], ['L', 'DL']], [['D', 'ul'], ['L', 'dl']],
            [['F', 'DL'], ['B', 'DR']], [['F', 'dl'], ['B', 'dr']],
            [['D', 'dl'], ['L', 'dr']]],
        'JDL-' => [[['D', 'l'], ['L', 'd']],
            [['D', 'DL'], ['L', 'DR']], [['D', 'dl'], ['L', 'dr']],
            [['F', 'DL'], ['B', 'DR']], [['F', 'dl'], ['B', 'dr']],
            [['D', 'ul'], ['L', 'dl']]],
    );


    $break = false;
    foreach (explode(" ", $scramble) as $m => $move) {
        $move = trim($move);
        #echo "$move ";
        if ($move) {
            if (isset($circles[$move])) {
                $CoorColor = Rotate($CoorColor, $circles, $move, true);
            } else {
                if ($move == '[verify]') {
                    break;
                } else {
                    echo 'ERROR ' . $move;
                    exit();
                }
            }
        }
    }
#exit();

    $im = imagecreate($Border * 2 + $Ceil * 3 * 4 + $D * 3, $Border * 2 + $Ceil * 3 * 3 + $D * 2);
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
    foreach ($Center as $n => $center) {
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

        $dx = floor($minX / 3) * $D;
        $dy = floor($minY / 3) * $D;
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
            'UF' => [4.5, 3, 1, .5],
            'UR' => [6, 1.5, 1, 0],
            'UL' => [3, 1.5, 1, 0],
            'UB' => [4.5, 0, 1, 0],
            'LB' => [0, 4.5, 0, 1],
            'LF' => [3, 4.5, 0.5, 1],
            'RB' => [9, 4.5, 2.5, 1],
            'RF' => [6, 4.5, 1.5, 1],
            'DB' => [4.5, 9, 1, 2],
            'DR' => [6, 7.5, 1, 2],
            'DL' => [3, 7.5, 1, 2],
            'DF' => [4.5, 6, 1, 1.5],
        ];

        imagesetthickness($im, 2);
        foreach ($moveNames as $name => $coor) {
            $X = $D * $coor[2] + $Border + $coor[0] * $Ceil;
            $Y = $D * $coor[3] + $Border + $coor[1] * $Ceil;
            imagefilledellipse($im, $X, $Y, 45, 45, $Colors['White']);
            imageellipse($im, $X, $Y, 45, 45, $Colors['Black']);
            $param = image_size(16, 'fonts/arial.ttf', $name);
            imagefttext($im, 16, 0, $X - $param['weith'] / 2 - $param['dx'], $Y + $param['height'] / 2 - $param['dy'], $Colors['Black'], 'fonts/arial.ttf', $name);
        }

        $sideNames = [
            'U' => [4.5, 1.5, 1, 0, 'Black'],
            'F' => [4.5, 4.5, 1, 1, 'White'],
            'R' => [7.5, 4.5, 2, 1, 'Black'],
            'L' => [1.5, 4.5, 0, 1, 'Black'],
            'D' => [4.5, 7.5, 1, 2, 'Black'],
            'B' => [10.5, 4.5, 3, 1, 'Black'],
        ];

        imagesetthickness($im, 1);
        foreach ($sideNames as $name => $coor) {
            $X = $D * $coor[2] + $Border + $coor[0] * $Ceil;
            $Y = $D * $coor[3] + $Border + $coor[1] * $Ceil;
            imagefilledrectangle($im, $X - 15, $Y - 15, $X + 15, $Y + 15, $Colors[$Center[$name]['Color']]);
            imagerectangle($im, $X - 15, $Y - 15, $X + 15, $Y + 15, $Colors['Black']);
            $param = image_size(12, 'fonts/arial_bold.ttf', $name);
            imagefttext($im, 12, 0, $X - $param['weith'] / 2 - $param['dx'], $Y + $param['height'] / 2 - $param['dy'], $Colors[$coor[4]], 'fonts/arial_bold.ttf', $name);
        }
    }

    return $im;
}
