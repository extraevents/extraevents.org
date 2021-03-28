<?php

function drawing_fto($scramble) {

    $Cell_Size = 100;
    $Margin = 0.2;
    $Padding = 0.1;

    $sides_config = [
        'U' => ['color' => 'White', 'part_point' => [0, -2], 'part' => 'front', 'rotate' => 'up',],
        'R' => ['color' => 'Green', 'part_point' => [2, 0], 'part' => 'front', 'rotate' => 'right',],
        'F' => ['color' => 'Red', 'part_point' => [0, 2], 'part' => 'front', 'rotate' => 'down',],
        'L' => ['color' => 'Violet', 'part_point' => [-2, 0], 'part' => 'front', 'rotate' => 'left',],
        'B' => ['color' => 'Blue', 'part_point' => [0, -2], 'part' => 'back', 'rotate' => 'up'],
        'BL' => ['color' => 'Orange', 'part_point' => [2, 0], 'part' => 'back', 'rotate' => 'right',],
        'D' => ['color' => 'Yellow', 'part_point' => [0, 2], 'part' => 'back', 'rotate' => 'down',],
        'BR' => ['color' => 'Grey', 'part_point' => [-2, 0], 'part' => 'back', 'rotate' => 'left',]
    ];

    $part = [
        'front' => [3, 3],
        'back' => [9, 3]
    ];

    $rotate = [
        'up' => [[1, 0], [0, 1]],
        'right' => [[0, -1], [1, 0]],
        'down' => [[-1, 0], [0, -1]],
        'left' => [[0, 1], [-1, 0]],
    ];

    $sides = [];

    foreach ($sides_config as $side_name => $side_config) {
        foreach ([0, 1] as $d) {
            $side['base_point'][$d] = $side_config['part_point'][$d] +
                    $part[$side_config['part']][$d];
        }
        $side['color'] = $side_config['color'];
        $side['part'] = $side_config['part'];
        $side['rotate'] = $rotate[$side_config['rotate']];
        $sides[$side_name] = $side;
    }

    $cells = [];

    $cells_config = [
        'corner' => ['side_point' => [-3, -1], 'type' => 'v'],
        'corner+' => ['side_point' => [1, -1], 'type' => 'v'],
        'corner-' => ['side_point' => [-1, 1], 'type' => 'v'],
        'edge' => ['side_point' => [-2, 0], 'type' => 'v'],
        'edge+' => ['side_point' => [-1, -1], 'type' => 'v'],
        'edge-' => ['side_point' => [0, 0], 'type' => 'v'],
        'center' => ['side_point' => [-2, 0], 'type' => '^'],
        'center+' => ['side_point' => [0, 0], 'type' => '^'],
        'center-' => ['side_point' => [-1, 1], 'type' => '^'],
    ];

    $cells_type = [
        'v' => [[0, 0], [2, 0], [1, 1]],
        '^' => [[0, 0], [1, -1], [2, 0]],
    ];

    $cells_base_point = [];

    foreach ($sides as $side_name => $side) {
        foreach ($cells_config as $cell_name => $cell_config) {
            $points = [];
            foreach ($cells_type[$cell_config['type']] as $point_config) {
                $point[0] = $side['base_point'][0] +
                        ($cell_config['side_point'][0] + $point_config[0]) * $side['rotate'][0][0] +
                        ($cell_config['side_point'][1] + $point_config[1]) * $side['rotate'][0][1];
                $point[1] = $side['base_point'][1] +
                        ($cell_config['side_point'][0] + $point_config[0]) * $side['rotate'][1][0] +
                        ($cell_config['side_point'][1] + $point_config[1]) * $side['rotate'][1][1];
                $points[] = $point;
            }
            $cells[$side_name][$cell_name] = [
                'color' => $side['color'],
                'points' => $points,
                'part' => $side['part']
            ];
        }
    }


    $cells_color = [];
    foreach ($cells as $side_name => $side) {
        foreach ($side as $cell_name => $cell) {
            $cells_color[$side_name][$cell_name] = $cell['color'];
        }
    }

    $cycles_side = [
        ['corner', 'corner+', 'corner-'],
        ['edge', 'edge+', 'edge-'],
        ['center', 'center+', 'center-']
    ];

    $cycles_move = [];

    $cycles_move['U'] = [
        [['L', 'corner+'], ['B', 'corner'], ['R', 'corner-']],
        [['L', 'corner-'], ['B', 'corner+'], ['R', 'corner']],
        [['L', 'center+'], ['B', 'center'], ['R', 'center-']],
        [['L', 'center-'], ['B', 'center+'], ['R', 'center']],
        [['L', 'edge-'], ['B', 'edge+'], ['R', 'edge']],
        [['F', 'corner-'], ['BL', 'corner'], ['BR', 'corner+']]
    ];

    $cycles_move['D'] = [
        [['BR', 'corner-'], ['BL', 'corner+'], ['F', 'corner']],
        [['BR', 'corner'], ['BL', 'corner-'], ['F', 'corner+']],
        [['BR', 'center-'], ['BL', 'center+'], ['F', 'center']],
        [['BR', 'center'], ['BL', 'center-'], ['F', 'center+']],
        [['BR', 'edge'], ['BL', 'edge-'], ['F', 'edge+']],
        [['B', 'corner-'], ['L', 'corner'], ['R', 'corner+']]
    ];

    $cycles_move['R'] = [
        [['U', 'corner+'], ['BR', 'corner'], ['F', 'corner-']],
        [['U', 'corner-'], ['BR', 'corner+'], ['F', 'corner']],
        [['U', 'center+'], ['BR', 'center'], ['F', 'center-']],
        [['U', 'center-'], ['BR', 'center+'], ['F', 'center']],
        [['U', 'edge-'], ['BR', 'edge+'], ['F', 'edge']],
        [['L', 'corner-'], ['B', 'corner'], ['D', 'corner+']]
    ];

    $cycles_move['BL'] = [
        [['D', 'corner-'], ['B', 'corner+'], ['L', 'corner']],
        [['D', 'corner'], ['B', 'corner-'], ['L', 'corner+']],
        [['D', 'center-'], ['B', 'center+'], ['L', 'center']],
        [['D', 'center'], ['B', 'center-'], ['L', 'center+']],
        [['D', 'edge'], ['B', 'edge-'], ['L', 'edge+']],
        [['BR', 'corner-'], ['U', 'corner'], ['F', 'corner+']]
    ];


    $cycles_move['L'] = [
        [['U', 'corner'], ['F', 'corner-'], ['BL', 'corner+']],
        [['U', 'corner-'], ['F', 'corner+'], ['BL', 'corner']],
        [['U', 'center'], ['F', 'center-'], ['BL', 'center+']],
        [['U', 'center-'], ['F', 'center+'], ['BL', 'center']],
        [['U', 'edge'], ['F', 'edge-'], ['BL', 'edge+']],
        [['R', 'corner-'], ['D', 'corner'], ['B', 'corner+']]
    ];

    $cycles_move['BR'] = [
        [['D', 'corner-'], ['R', 'corner+'], ['B', 'corner']],
        [['D', 'corner+'], ['R', 'corner'], ['B', 'corner-']],
        [['D', 'center-'], ['R', 'center+'], ['B', 'center']],
        [['D', 'center+'], ['R', 'center'], ['B', 'center-']],
        [['D', 'edge-'], ['R', 'edge+'], ['B', 'edge']],
        [['BL', 'corner-'], ['F', 'corner'], ['U', 'corner+']]
    ];

    $cycles_move['F'] = [
        [['L', 'corner-'], ['R', 'corner+'], ['D', 'corner']],
        [['L', 'corner'], ['R', 'corner-'], ['D', 'corner+']],
        [['L', 'center-'], ['R', 'center+'], ['D', 'center']],
        [['L', 'center'], ['R', 'center-'], ['D', 'center+']],
        [['L', 'edge'], ['R', 'edge-'], ['D', 'edge+']],
        [['U', 'corner-'], ['BR', 'corner'], ['BL', 'corner+']]
    ];

    $cycles_move['B'] = [
        [['BR', 'corner-'], ['U', 'corner+'], ['BL', 'corner']],
        [['BR', 'corner+'], ['U', 'corner'], ['BL', 'corner-']],
        [['BR', 'center-'], ['U', 'center+'], ['BL', 'center']],
        [['BR', 'center+'], ['U', 'center'], ['BL', 'center-']],
        [['BR', 'edge-'], ['U', 'edge+'], ['BL', 'edge']],
        [['D', 'corner-'], ['R', 'corner'], ['L', 'corner+']]
    ];

    foreach ($sides as $side_name => $side) {
        foreach ($cycles_side as $cycle_side) {
            $cycle_elements = [];
            foreach ($cycle_side as $cycle_element) {
                $cycle_elements[] = [$side_name, $cycle_element];
            }
            $cycles_move[$side_name][] = $cycle_elements;
        }
    }

    $scramble = str_replace("\\r", "", $scramble);
    $scramble = str_replace('\\', "", $scramble);

    foreach (explode(" ", $scramble) as $move) {
        $move_clear = str_replace(["\'", "'"], "", $move);
        if ($move_clear <> "" and isset($cycles_move[$move_clear])) {
            $direct = !(strpos($move, "'") !== false);
            $cells_color = Rotate($cells_color, $cycles_move, $move_clear, $direct);
        }
    }


    $im = imagecreate($Cell_Size * ($Margin + 12 + 2 * $Padding), $Cell_Size * (6 + 2 * $Padding));
    $white = imagecolorallocate($im, 250, 255, 255);
    $black = imagecolorallocate($im, 0, 0, 0);

    $Colors = array(
        'Grey' => imagecolorallocate($im, 188, 188, 188),
        'Red' => imagecolorallocate($im, 255, 0, 0),
        'Green' => imagecolorallocate($im, 0, 233, 0),
        'White' => imagecolorallocate($im, 254, 254, 254),
        'Blue' => imagecolorallocate($im, 55, 0, 255),
        'Yellow' => imagecolorallocate($im, 251, 255, 0),
        'Violet' => imagecolorallocate($im, 140, 0, 130),
        'Orange' => imagecolorallocate($im, 255, 170, 0),
    );


    $Polygons = array();

    foreach ($cells as $side_name => $side_cells) {
        foreach ($side_cells as $cell_name => $cell) {
            $Polygons[] = [$cell['points'], $Colors[$cells_color[$side_name][$cell_name]], $cell['part']];
        }
    }
    foreach ($Polygons as $Polygon) {
        imagesetthickness($im, 3);
        $Points = array();
        foreach ($Polygon[0] as $point) {
            $point[0] = $Cell_Size * ($point[0] + $Padding + $Margin * ($Polygon[2] == 'back' ? 1 : 0));
            $point[1] = $Cell_Size * ($point[1] + $Padding);
            $Points[] = $point[0];
            $Points[] = $point[1];
        }
        imagefilledpolygon($im, $Points, sizeof($Points) / 2, $Polygon[1]);
        imagepolygon($im, $Points, sizeof($Points) / 2, $black);
    }
    imagesetthickness($im, 8);
    imageline($im, $Cell_Size * $Padding, $Cell_Size * $Padding, $Cell_Size * (6 + $Padding), $Cell_Size * (6 + $Padding), $black);
    imageline($im, $Cell_Size * $Padding, $Cell_Size * (6 + $Padding), $Cell_Size * (6 + $Padding), $Cell_Size * $Padding, $black);
    imageline($im, $Cell_Size * (6 + $Padding + $Margin), $Cell_Size * $Padding, $Cell_Size * (12 + $Padding + $Margin), $Cell_Size * (6 + $Padding), $black);
    imageline($im, $Cell_Size * (6 + $Padding + $Margin), $Cell_Size * (6 + $Padding), $Cell_Size * (12 + $Padding + $Margin), $Cell_Size * $Padding, $black);

    foreach ($sides as $side_name => $side) {
        $X = $Cell_Size * ($Padding + $side['base_point'][0] + $Margin * ($side['part'] == 'back' ? 1 : 0));
        $Y = $Cell_Size * ($Padding + $side['base_point'][1]);
        imagefilledellipse($im, $X, $Y, $Cell_Size, $Cell_Size, $white);
        imageellipse($im, $X, $Y, $Cell_Size, $Cell_Size, $black);
        $param = image_size($Cell_Size * 0.4, 'fonts/arial_bold.ttf', $side_name);
        imagefttext($im, $Cell_Size * 0.4, 0, $X - $param['weith'] / 2 - $param['dx'], $Y + $param['height'] / 2 - $param['dy'], $black, 'fonts/arial_bold.ttf', $side_name);
    }

    return $im;
}
