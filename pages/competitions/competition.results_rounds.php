<?php

$competition = competition::get();

$table = new build_table(t('competition.results'));
$table->add_head('image', false);
$table->add_head('name', t('round.event'));
$table->add_head('round', t('round.round'));
$table->add_head('results_enter', false);

foreach ($competition->rounds as $round) {
    $row = new build_row();
    $row->add_value('image', event::get_image($round->event_id, $round->event_name, $round->icon_wca_revert));
    $results_view_link = "competitions/$competition->id/results/{$round->event_id}/{$round->round_number}";
    $row->add_value('name', "<a href='%i/$results_view_link'>{$round->event_name}</a>");
    $row->add_value('round', $round->round_format);
    if (grand::resolve_access('competition.results_round_enter') and ($competition->enable_enter_results())) {
        $results_enter_link = "competitions/$competition->id/results/{$round->event_id}/{$round->round_number}/enter";
        $row->add_value('results_enter',
                "<a href='%i/$results_enter_link'>"
                . "<i class='fas fa-edit'></i> Enter"
                . "</a>");
    }
    $table->add_tr($row);
}

$data = (object) [
            'out_results' => $table->out()
];

