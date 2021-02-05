<?php

$competition = competition::get();

$table = new build_table(t('competition.scorecards'));
$table->add_head('image', false);
$table->add_head('name', t('round.event'));
$table->add_head('round', t('round.round'));
$table->add_head('print', false);

foreach ($competition->rounds as $round) {
    $row = new build_row();
    $row->add_value('image', event::get_image($round->event_id, $round->event_name, $round->icon_wca_revert));
    $row->add_value('round', $round->round_format);
    $row->add_value('name', $round->event_name);
    $scorecards_print_link = "%i/competitions/{$round->competition_id}/scorecards/{$round->event_id}/{$round->round_number}";
    $row->add_value('print',
            "<a target='_blank' href='$scorecards_print_link'>"
            . "<i class='fas fa-print'></i> Print"
            . "</a>");
    $table->add_tr($row);
}

$out_scorecards = $table->out();

$data = (object) [
            'out_scorecards' => $out_scorecards
];

