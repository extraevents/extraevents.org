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
foreach ($competition->rounds as $r) {
    $row = new build_row();
    $row->add_value('image', event::get_image($r->event_id, $r->event_name, $r->icon_wca_revert));
    $row->add_value('name', $r->event_name);
    $row->add_value('round', $r->round_format);
    $row->add_value('format', $r->format_name);
    $row->add_value('cutoff', centisecond::out($r->cutoff, true));
    $row->add_value('time_limit', centisecond::out($r->time_limit, true) .
            ($r->time_limit_cumulative ? (' ' . t('round.cumulative')) : ''));
    $row->add_value('competitor_limit', $r->competitor_limit);
    $table->add_tr($row);
}

$rounds_block = $table->out();

$data = (object) [
            'competition_line' => $competition->line(),
            'info' => $block->out(),
            'rounds' => $rounds_block
];

