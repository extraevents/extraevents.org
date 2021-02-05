<?php

$round = page::get_object('round');
$results_pdf = new results_pdf($round);

ob_end_clean();
$results_pdf->pdf()->Output($results_pdf->get_filename());
exit();
