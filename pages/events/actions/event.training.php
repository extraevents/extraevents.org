<?php

$set_count = form::value('set_count');
$solve_count = 5;
$extra = 2;
$event = page::get_object('event');
$event_id = $event->id;
$scrambles = [];
$date = form::value('date');

foreach (range(1, $solve_count + $extra) as $attempt) {
    foreach (range(0, $set_count - 1) as $group) {
        $scrambles[$group][$attempt] = $event->generate_scramble(true);
    }
}
$scramble_pdf = new scramble_pdf(
        $event
        , 'Training Extra Event'
        , $solve_count
        , false
        , $date
        ,true);
$scramble_pdf->set_scrambles($scrambles);
$pdf = $scramble_pdf->build();
db::close();
ob_end_clean();
$pdf->Output("$event_id.pdf");
$pdf->Close();
exit();
