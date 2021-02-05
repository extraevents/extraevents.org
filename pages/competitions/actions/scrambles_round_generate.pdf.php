<?php

if ($_FILES['file']['error'] != 0 or
        $_FILES['file']['type'] != 'application/pdf' or
        $_FILES['file']['name'] != $data_tnoodle->wcif->shortName . " - All Scrambles.pdf") {
    form::process(false, false, 'scrambles_generate.wrong_file!');
    form::return();
}
$pdf_file = $_FILES['file']['tmp_name'];
$dir = scramble_pdf::dir();
$file_prefix = $dir . '/' . session_id() . '_' . random_string(6);

$im = new imagick();
$im->readimage($pdf_file);
$Pages = $im->getnumberimages();

$lines = [];

for ($page = 0; $page < $Pages; $page++) {
    $im = new imagick();
    $im->setResolution(300, 300);
    $im->readimage($pdf_file . "[$page]");
    $im->setImageFormat('jpeg');
    $jpg_file = $file_prefix . "_$page.jpg";
    $im->writeImage($jpg_file);
    $im->clear();
    $im->destroy();

    $img_lines = imagecreatefromjpeg($jpg_file);
    $page_lines = [];
    $attempt = 0;
    $start = 0;
    $end = 0;
    for ($y = 260; $y < 3050; $y++) {
        if (in_array(imagecolorat($img_lines, 250, $y), [0, 65793])
                and in_array(imagecolorat($img_lines, 250, $y + 1), [0, 65793])
                and in_array(imagecolorat($img_lines, 310, $y), [0, 65793])
                and in_array(imagecolorat($img_lines, 310, $y + 1), [0, 65793])
        ) {
            if (!$start) {
                $start = $y;
            } else {
                if ($y - $start > 119) {
                    $page_lines[$attempt] = (object) ['start' => $start, 'end' => $y];
                    $start = $y;
                    $attempt++;
                } else {
                    $start = $y;
                }
            }
        }
    }
    $lines[$page] = $page_lines;
}

$page_events = [];
$page = 0;
foreach ($wca_options as $event) {
    $event_attemps = $event->solve_count + $event->extra_count;
    $event_attempt = 0;
    for ($set_number = 0; $set_number < $event->set_count; $set_number++) {
        for ($page_attempt = 0; $page_attempt < $event_attemps; $page_attempt++) {
            $page_events[$event->wca_event][$event_attempt] = (object) [
                        'page' => $page,
                        'attempt' => $page_attempt,
                        'line' => $lines[$page][$page_attempt]
            ];
            $event_attempt++;
        }
        $page++;
    }
}

$X0 = 225;
$X1 = 2413;
$X = $X1 - $X0;
$scrambles = [];
$event_attemptions = [];
for ($group = 0; $group < $ee_option->set_count; $group++) {
    for ($attempt = 1; $attempt <= $ee_option->solve_count + $ee_option->extra_count; $attempt++) {
        foreach ($ee_option->wca_events as $id) {
            $event_attemptions[$id] ??= 0;
            $page_event = $page_events[$id][$event_attemptions[$id]] ?? null;
            $event_attemptions[$id]++;
            if ($page_event == null) {
                form::process(false, false, 'scrambles_generate.wrong_count!');
                form::return();
            }
            $file_page = $file_prefix . "_{$page_event->page}.jpg";
            $line_end = $page_event->line->end;
            $line_start = $page_event->line->start;
            $image_cut = imagecreatetruecolor($X, $line_end - $line_start + 1);
            imagecolorallocate($image_cut, 0, 0, 0);

            imagecopy($image_cut, imagecreatefromjpeg($file_page), 0, 0, $X0,
                    $line_start, $X, $line_end - $line_start + 1);

            $file_scramble = $file_prefix . "_{$id}_{$event_attemptions[$id]}.jpg";
            imagejpeg($image_cut, $file_scramble);

            $scrambles[$group][$attempt][] = $file_scramble;
        }
    }
}

$scramble_pdf = new scramble_pdf(
        new event($ee_option->event_id)
        , $ee_option->competition_name
        , $ee_option->solve_count
        , $ee_option->round_number
        , $date
        , false);
$scramble_pdf->set_image_scrambles($scrambles);
$pdf = $scramble_pdf->build();

$pdf->Output($filename, 'F');
form::process(true, [
    'ee_option' => $ee_option,
    'wca_options' => $wca_options,
    'data_tnoodle' => $data_tnoodle
        ], 'scrambles_generate.done');
