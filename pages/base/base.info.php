<?php

$statistics = [];
$statistics_out = [];
$rows = sql_query::rows('info');
foreach ($rows as $row) {
    $statistics[$row->id][] = $row;
}


foreach (['persons_event', 'competitions_event', 'countries_competition_event', 'countries_person_event']
as $id) {
    $table = new build_table(t("statistics.$id"));
    $table->add_head('count', false);
    $table->add_head('icon', false);
    $table->add_head('event', false);
    foreach ($statistics[$id] as $r) {
        $row = new build_row();
        $row->add_value('count', $r->count);
        $row->add_value('icon', event::get_image($r->event_id, $r->event_name, $r->icon_wca_revert));
        $row->add_value('event', $r->event_name);
        $table->add_tr($row);
    }
    $statistics_out[] = $table->out();
}

foreach (['persons_country', 'competitions_country', 'world_records_country', 'events_competition_country'] as $id) {
    $table = new build_table(t("statistics.$id"));
    $table->add_head('count', false);
    $table->add_head('flag', false);
    $table->add_head('country', false);
    foreach ($statistics[$id] as $r) {
        $row = new build_row();
        $row->add_value('count', $r->count);
        $row->add_value('flag', region::flag($r->country_name, $r->country_iso2));
        $row->add_value('country', $r->country_name);
        $table->add_tr($row);
    }
    $statistics_out[] = $table->out();
}


$data = arrayToObject(
        [
            'content' => markdown::convertToHtml(file_get_contents(__DIR__ . "/base.info.md")),
            'statistics' => $statistics_out
        ]);
