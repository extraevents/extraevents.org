<?php

$round = page::get_object('round');
$scorecards_pdf = new scorecards_pdf($round);

ob_end_clean();
$scorecards_pdf->pdf()->Output($scorecards_pdf->get_filename());
exit();
