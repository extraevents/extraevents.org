<?php

function drawing_ivy($scramble, $training = false) {
    $Ceil = 100;
    $Border = 20;
    $D = 10;

    $CentersR = array(
        'D' => array('x' => 1, 'y' => 2, 'Color' => 'Yellow'),
        'L' => array('x' => 0, 'y' => 1, 'Color' => 'Blue'),
        'U' => array('x' => 1, 'y' => 0, 'Color' => 'White'),
        'R' => array('x' => 2, 'y' => 1, 'Color' => 'Green'));

    $CentersL = array(
        'F' => array('x' => 1, 'y' => 1, 'Color' => 'Orange'),
        'B' => array('x' => 3, 'y' => 1, 'Color' => 'Red'));

    $K1 = 0.5;
    $K2 = 0.15;
    $CoorsR = array(
        'U' => array(array('x' => 0, 'y' => 0), array('x' => 0, 'y' => 1), array('x' => 1, 'y' => 0)),
        'D' => array(array('x' => 1, 'y' => 1), array('x' => 0, 'y' => 1), array('x' => 1, 'y' => 0)),
        'C' => array(array('x' => 0, 'y' => 1), array('x' => 1 - $K1, 'y' => 1 - $K2), array('x' => 1 - $K2, 'y' => 1 - $K1),
            array('x' => 1, 'y' => 0), array('x' => $K1, 'y' => $K2), array('x' => $K2, 'y' => $K1)),
    );

    $CoorsL = array(
        'U' => array(array('x' => 0, 'y' => 0), array('x' => 1, 'y' => 0), array('x' => 1, 'y' => 1)),
        'D' => array(array('x' => 0, 'y' => 0), array('x' => 0, 'y' => 1), array('x' => 1, 'y' => 1)),
        'C' => array(array('x' => 0, 'y' => 0), array('x' => 1 - $K1, 'y' => $K2), array('x' => 1 - $K2, 'y' => $K1),
            array('x' => 1, 'y' => 1), array('x' => $K1, 'y' => 1 - $K2), array('x' => $K2, 'y' => 1 - $K1)),
    );

    /*
      $Coor=array(
      'R'=>array(
      'U'=>array('x'=>50,'y'=>-50),
      'D'=>array('x'=>-50,'y'=>50),
      'C'=>array('x'=>0,'y'=>0),
      ),
      'L'=>array(
      'U'=>array('x'=>-50,'y'=>-50),
      'D'=>array('x'=>50,'y'=>50),
      'C'=>array('x'=>0,'y'=>0),
      )
      );
     */

    $CoorColor = array();
    foreach ($CentersR as $n => $center) {
        foreach ($CoorsR as $c => $coor) {
            $CoorColor[$n][$c] = $center['Color'];
        }
    }

    foreach ($CentersL as $n => $center) {
        foreach ($CoorsL as $c => $coor) {
            $CoorColor[$n][$c] = $center['Color'];
        }
    }

    $circles = array(
        'R' => array(
            array('DC', 'RC', 'BC'),
            array('DD', 'RD', 'BD'),
        ),
        'L' => array(
            array('DC', 'LC', 'FC'),
            array('DU', 'LD', 'FD'),
        ),
        'F' => array(
            array('UC', 'RC', 'FC'),
            array('UD', 'RU', 'FU'),
        ),
        'U' => array(
            array('UC', 'LC', 'BC'),
            array('UU', 'LU', 'BU')
        )
    );


    foreach (explode(" ", $scramble) as $move) {
        $move = trim($move);
        if ($move <> "" and in_array($move[0], array("U", "R", "F", "L"))) {
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
    foreach ($CentersR as $n => $center) {
        foreach ($CoorsR as $c => $coor) {
            $pairs = array();
            foreach ($coor as $xy) {
                $pairs[] = array($center['x'] + $xy['x'], $center['y'] + $xy['y']);
            }
            $Polygons[] = array($pairs, $Colors[$CoorColor[$n][$c]]);
        }
    }

    foreach ($CentersL as $n => $center) {
        foreach ($CoorsL as $c => $coor) {
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
            'F' => [2, 1, 1.5, 0.5],
            'R' => [3, 2, 2.5, 1.5],
            'L' => [1, 2, 0.5, 1.5],
            'U' => [1, 0, 0.5, -0.5],
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
            'U' => [1.5, 0.5, 1, 0, 'Black', 'White'],
            'R' => [2.5, 1.5, 2, 1, 'White', 'Green'],
            'L' => [1.5, 1.5, 1, 1, 'Black', 'Orange'],
        ];

        imagesetthickness($im, 1);
        foreach ($sideNames as $name => $coor) {
            $X = $D * $coor[2] + $Border + $coor[0] * $Ceil;
            $Y = $D * $coor[3] + $Border + $coor[1] * $Ceil;
            imagefilledrectangle($im, $X - 10, $Y - 10, $X + 10, $Y + 10, $Colors[$coor[5]]);
            imagerectangle($im, $X - 10, $Y - 10, $X + 10, $Y + 10, $Colors['Black']);
            $param = image_size(12, 'fonts/arial_bold.ttf', $name);
            imagefttext($im, 12, 0, $X - $param['weith'] / 2 - $param['dx'], $Y + $param['height'] / 2 - $param['dy'], $Colors[$coor[4]], 'fonts/arial_bold.ttf', $name);
        }
    }

    return $im;
}
