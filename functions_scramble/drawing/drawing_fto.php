<?php

function drawing_fto($scramble) {

    $Cell_Size = 50;
    $Margin = 0.1;
    $Padding = 0.1;

    $Centers = array(
        'U' => array('Flip' => 0, 'Padding' => [1, 0], 'x' => 0, 'y' => 0, 'Color' => 'White'),
        'R' => array('Flip' => 0, 'Padding' => [2, 1], 'x' => 3, 'y' => 3, 'Color' => 'Grey'),
        'F' => array('Flip' => 1, 'Padding' => [1, 1], 'x' => 0, 'y' => 3, 'Color' => 'Green'),
        'L' => array('Flip' => 0, 'Padding' => [0, 1], 'x' => 0, 'y' => 3, 'Color' => 'Red'),
        'BR' => array('Hidden' => true, 'Color' => 'Blue'),
        'BL' => array('Hidden' => true, 'Color' => 'Violet'),
        'D' => array('Hidden' => true, 'Color' => 'Yellow'),
        'B' => array('Hidden' => true, 'Color' => 'Orange'),
    );


    foreach ($Centers as $center_name => $center) {
        if (isset($center['Hidden'])) {
            foreach (['1a', '2a', '3a', '1c', '2c', '3c', '1e', '2e', '3e'] as $coor_name) {
                $Cells[$center_name][$coor_name] = FALSE;
            }
        }
    }


    foreach (['2a' => [0, 0], '1e' => [1, 0], '3a' => [2, 0], '3e' => [1, 1], '2e' => [2, 1], '1a' => [2, 2]] as $coor_name => $coor_tmp) {
        list($x, $y) = $coor_tmp;
        $Cells['F'][$coor_name] = [['x' => 0 + $x, 'y' => 0 + $y], ['x' => 1 + $x, 'y' => 0 + $y], ['x' => 1 + $x, 'y' => 1 + $y]];
    }

    foreach (['2c' => [1, 0], '3c' => [2, 0], '1c' => [2, 1]] as $coor_name => $coor_tmp) {
        list($x, $y) = $coor_tmp;
        $Cells['F'][$coor_name] = [['x' => 0 + $x, 'y' => 0 + $y], ['x' => 1 + $x, 'y' => 1 + $y], ['x' => 0 + $x, 'y' => 1 + $y]];
    }

    foreach (['1a' => [0, 0], '2e' => [0, 1], '3e' => [1, 1], '3a' => [0, 2], '1e' => [1, 2], '2a' => [2, 2]] as $coor_name => $coor_tmp) {
        list($x, $y) = $coor_tmp;
        $Cells['U'][$coor_name] = [['x' => 0 + $x, 'y' => 0 + $y], ['x' => 0 + $x, 'y' => 1 + $y], ['x' => 1 + $x, 'y' => 1 + $y]];
    }

    foreach (['1c' => [0, 1], '3c' => [0, 2], '2c' => [1, 2]] as $coor_name => $coor_tmp) {
        list($x, $y) = $coor_tmp;
        $Cells['U'][$coor_name] = [['x' => 0 + $x, 'y' => 0 + $y], ['x' => 1 + $x, 'y' => 0 + $y], ['x' => 1 + $x, 'y' => 1 + $y]];
    }

    foreach (['3a' => [0, 0], '1e' => [0, 1], '2e' => [1, 1], '2a' => [0, 2], '3e' => [1, 2], '1a' => [2, 2]] as $coor_name => $coor_tmp) {
        list($x, $y) = $coor_tmp;
        $Cells['L'][$coor_name] = [['x' => 0 + $x, 'y' => 0 + $y], ['x' => 0 + $x, 'y' => 1 + $y], ['x' => 1 + $x, 'y' => 1 + $y]];
    }

    foreach (['3c' => [0, 1], '2c' => [0, 2], '1c' => [1, 2]] as $coor_name => $coor_tmp) {
        list($x, $y) = $coor_tmp;
        $Cells['L'][$coor_name] = [['x' => 0 + $x, 'y' => 0 + $y], ['x' => 1 + $x, 'y' => 0 + $y], ['x' => 1 + $x, 'y' => 1 + $y]];
    }

    foreach (['2a' => [0, 0], '3e' => [0, 1], '1e' => [1, 1], '1a' => [0, 2], '2e' => [1, 2], '3a' => [2, 2]] as $coor_name => $coor_tmp) {
        list($x, $y) = $coor_tmp;
        $Cells['R'][$coor_name] = [['x' => 0 + $x, 'y' => 0 + $y], ['x' => 0 + $x, 'y' => 1 + $y], ['x' => 1 + $x, 'y' => 1 + $y]];
    }

    foreach (['2c' => [0, 1], '1c' => [0, 2], '3c' => [1, 2]] as $coor_name => $coor_tmp) {
        list($x, $y) = $coor_tmp;
        $Cells['R'][$coor_name] = [['x' => 0 + $x, 'y' => 0 + $y], ['x' => 1 + $x, 'y' => 0 + $y], ['x' => 1 + $x, 'y' => 1 + $y]];
    }


    foreach (array_keys($Centers) as $center_name) {
        foreach (array_keys($Cells[$center_name]) as $coor_name) {
            $CellsColor[$center_name][$coor_name] = $Centers[$center_name]['Color'];
        }
    }


    $Cycles = [];

    $CenterCyrcles = [
        ['1a', '2a', '3a'],
        ['1c', '2c', '3c'],
        ['1e', '2e', '3e']];


    $EdgeType = [
        'Count' => 5,
        '1_side' => ['1a', '1c', '3e', '2c', '2a'],
        '2_side' => ['3a', '3c', '2e', '1c', '1a'],
        '3_side' => ['2a', '2c', '1e', '3c', '3a']
    ];

    $EdgeTypeW = [
        'Count' => 3,
        '1_side' => ['2e', '3c', '1e'],
        '2_side' => ['1e', '2c', '3e'],
        '3_side' => ['3e', '1c', '2e']
    ];

    $EdgeMoveType = [
        'F' => ['R' => '1_side', 'L' => '2_side', 'U' => '3_side'],
        'R' => ['D' => '1_side', 'F' => '2_side', 'BR' => '3_side'],
        'L' => ['F' => '1_side', 'D' => '2_side', 'BL' => '3_side'],
        'U' => ['BL' => '1_side', 'BR' => '2_side', 'F' => '3_side'],
    ];

    $CornerType = [
        'Count' => 1,
        '1_side' => ['1a'],
        '2_side' => ['2a'],
        '3_side' => ['3a']
    ];

    $CornerTypeW = [
        'Count' => 3,
        '1_side' => ['3e', '1c', '2e'],
        '2_side' => ['1e', '2c', '3e'],
        '3_side' => ['2e', '3c', '1e']
    ];

    $CornerMoveType = [
        'F' => ['D' => '1_side', 'BL' => '2_side', 'BR' => '3_side'],
        'R' => ['L' => '1_side', 'U' => '2_side', 'B' => '3_side'],
        'L' => ['R' => '1_side', 'B' => '2_side', 'U' => '3_side'],
        'U' => ['B' => '1_side', 'R' => '2_side', 'L' => '3_side']
    ];


    foreach (array_keys($CornerMoveType) as $centerName) {
        foreach ($CenterCyrcles as $centerCyrcle) {
            $cycle = [];
            foreach ($centerCyrcle as $centerCyrcleCell) {
                $cycle[] = [$centerName, $centerCyrcleCell];
            }
            $Cycles[$centerName][] = $cycle;
        }


        $MetaMoves = [
            ['type' => $EdgeType, 'movetype' => $EdgeMoveType, 'cyclenameadd' => ''],
            ['type' => $EdgeTypeW, 'movetype' => $EdgeMoveType, 'cyclenameadd' => 'w'],
            ['type' => $CornerType, 'movetype' => $CornerMoveType, 'cyclenameadd' => ''],
            ['type' => $CornerTypeW, 'movetype' => $CornerMoveType, 'cyclenameadd' => 'w'],
        ];

        foreach ($MetaMoves as $metaMove) {
            for ($et = 0; $et < $metaMove['type']['Count']; $et++) {
                $cycle = [];
                foreach ($metaMove['movetype'][$centerName] as $centerEdgeName => $edgeType) {
                    $cycle[] = [$centerEdgeName, $metaMove['type'][$edgeType][$et]];
                }
                $Cycles[$centerName . $metaMove['cyclenameadd']][] = $cycle;
            }
        }
    }

    foreach ($Cycles as $centerName => $cycles) {
        if (substr($centerName, -1, 1) != 'w') {
            foreach ($cycles as $cycle) {
                $Cycles[$centerName . "w"][] = $cycle;
            }
        }
    }

    $scramble = str_replace("\\r", "", $scramble);
    $scramble = str_replace('\\', "", $scramble);

    foreach (explode(" ", $scramble) as $move) {
        $move_clear = str_replace(["\'", "'"], "", $move);
        if ($move_clear <> "" and isset($Cycles[$move_clear])) {
            $Paddingirect = !(strpos($move, "'") !== false);
            $CellsColor = Rotate($CellsColor, $Cycles, $move_clear, $Paddingirect);
        }
    }


    $im = imagecreate($Cell_Size * ($Margin * 2 + 6 + 2 * $Padding), $Cell_Size * ($Margin * 2 + (6 + $Padding) * (sqrt(3) / 2)));
    $white = imagecolorallocate($im, 250, 255, 255);
    $black = imagecolorallocate($im, 0, 0, 0);

    $Colors = array(
        'Grey' => imagecolorallocate($im, 153, 153, 153),
        'Red' => imagecolorallocate($im, 200, 0, 0),
        'Green' => imagecolorallocate($im, 0, 255, 0),
        'White' => imagecolorallocate($im, 254, 254, 254),
        'Blue' => imagecolorallocate($im, 35, 0, 186),
        'Yellow' => imagecolorallocate($im, 255, 225, 34),
        'Violet' => imagecolorallocate($im, 157, 0, 255),
        'Orange' => imagecolorallocate($im, 255, 146, 20),
    );


    $Polygons = array();
    foreach ($Centers as $center_name => $center) {
        if (!isset($center['Hidden'])) {
            foreach ($Cells[$center_name] as $cell_name => $coor) {
                $pairs = array();
                foreach ($coor as $xy) {
                    $X = $center['x'] + $xy['x'];
                    $Y = $center['y'] + $xy['y'];
                    $pairs[] = array(3 + $X - $Y / 2 + $center['Padding'][0] * $Padding, 0 + $Y * (sqrt(3) / 2) + $center['Padding'][1] * $Padding);
                }
                $Polygons[] = array($pairs, $Colors[$CellsColor[$center_name][$cell_name]]);
            }
        }
    }
    foreach ($Polygons as $Polygon) {
        imagesetthickness($im, 1);

        $Points = array();

        foreach ($Polygon[0] as $point) {
            $point[0] = $Cell_Size * ($point[0] + $Margin);
            $point[1] = $Cell_Size * ($point[1] + $Margin);
            $Points[] = $point[0];
            $Points[] = $point[1];
        }

        imagefilledpolygon($im, $Points, sizeof($Points) / 2, $Polygon[1]);
        imagepolygon($im, $Points, sizeof($Points) / 2, $black);
    }


    foreach ($Centers as $name => $center) {
        if (!isset($center['Hidden'])) {

            $dx = 1;
            $dy = 2;
            if ($center['Flip']) {
                $dx = 2;
                $dy = 1;
            }
            $X = $Margin * $Cell_Size + (3 + ($center['x'] + $dx) - ($center['y'] + $dy) / 2 + $center['Padding'][0] * $Padding) * $Cell_Size;
            $Y = $Margin * $Cell_Size + (0 + ($center['y'] + $dy) * (sqrt(3) / 2) + $center['Padding'][1] * $Padding) * $Cell_Size;
            imagefilledellipse($im, $X, $Y, 30, 30, $Colors[$Centers[$name]['Color']]);
            imageellipse($im, $X, $Y, 30, 30, $black);
            $param = image_size(20, 'fonts/arial_bold.ttf', $name);
            imagefttext($im, 20, 0, $X - $param['weith'] / 2 - $param['dx'], $Y + $param['height'] / 2 - $param['dy'], $black, 'fonts/arial_bold.ttf', $name);
        }
    }

    return $im;
}
