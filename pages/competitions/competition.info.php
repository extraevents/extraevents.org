<?php

$competition = competition::get();

$id = $competition->id;
$block = new build_block(false);


$block->add_element(t('competition.date'),
        $competition->get_date_range());

$block->add_element(t('competition.country'),
        $competition->get_country_line());

$block->add_element(false,
        "<a data-external-link='" . config::get()->wca_site . "/competitions/$id'>" .
        t('competition.more_info') .
        "</a>");

$block->add_element(t('competition.status'),
        competition::status_line($competition->status));

$organizers = [];
foreach ($competition->organizers as $o => $organizer) {
    $person = new person($organizer);
    $organizers[] = $person::get_line($person->id, $person->name);
}

$block->add_element(t('competition.organizers'),
        implode(', ', $organizers));

if ($competition->contact) {
    $block->add_element(t('competition.contact'),
            "<a href=mailto:$competition->contact>" .
            t('competition.organizers') .
            "</a>");
}


$block->add_element(t('competition.registration'),
        t('competition.registration_date_close') .
        " <span data-utc-time='$competition->registration_close'>$competition->registration_close</span>");

$table = new build_table(false);
$table->add_head('image', false);
$table->add_head('name', t('round.event'));
$table->add_head('round', t('round.round'));
$table->add_head('format', t('round.format'));
$table->add_head('time_limit', t('round.time_limit'));
$table->add_head('cutoff', t('round.cutoff'));
$table->add_head('competitor_limit', t('round.competitor_limit'));
foreach ($competition->rounds as $round) {
    $row = new build_row();
    $row->add_value('image', event::get_image($round->event_id, $round->event_name, $round->icon_wca_revert));
    if ($competition->show_results()) {
        $results_view_link = "competitions/$competition->id/results/{$round->event_id}/{$round->round_number}";
        $row->add_value('name', "<a href= '%i/$results_view_link' >$round->event_name</>");
    } else {
        $row->add_value('name', $round->event_name);
    }
    $row->add_value('round', $round->round_format);
    $row->add_value('format', $round->format_name);
    $row->add_value('cutoff', centisecond::out($round->cutoff, true));
    $row->add_value('time_limit', centisecond::out($round->time_limit, true) .
            ($round->time_limit_cumulative ? (' ' . t('round.cumulative')) : ''));
    $row->add_value('competitor_limit', $round->competitor_limit);
    $table->add_tr($row);
}

$rounds_block = $table->out();

$data = (object) [
            'competition_line' => $competition->line(),
            'info' => $block->out(),
            'rounds' => $rounds_block
];

