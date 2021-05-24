<?php

$table = new build_table();
$table->add_filter(['event']);
$table->add_head('event', t('results.event'));
$table->add_head('type', t('results.type'));
$table->add_head('result', t('results.result'));
$region = query::value('region');
$list = sql_query::rows('records_by_region', [
            'region' => $region ? $region : 'world'
        ]);
$table->add_head('competitor', t('results.competitor'));
$table->add_head('country', t('results.country'));
$table->add_head('competition', t('results.competition'));

foreach ($list as $r) {
    $row = new build_row();
    $row->add_value('competitor', person::get_line($r->person_id, $r->person_name));
    $row->add_value('event', event::get_image($r->event_id, $r->event_name, $r->icon_wca_revert) . ' ' . $r->event_name);
    $row->add_value('event_sort', $r->event_name);
    $row->add_value('result', centisecond::out($r->result));
    $row->add_value('country', region::flag($r->person_country_name, $r->person_country_iso2) . ' ' . $r->person_country_name . ', ' . $r->person_continent_name);
    $row->add_value('country_sort', $r->person_country_name);
    $row->add_value('continent_sort', $r->person_continent_name);
    $row->add_value('type', t('results.types.' . $r->result_type));
    $row->add_value('competition', competition::get_line($r->competition_id, $r->competition_name, $r->competition_country_name, $r->competition_country_iso2, "/results/$r->event_id/$r->round_number"));
    $table->add_tr($row);
}

$table->sort([
    'event_sort' => 'asc',
    'continent_sort' => 'asc',
    'country_sort' => 'asc',
    'type' => 'desc'
]);

$data = arrayToObject([
    'table' => $table->out()
        ]);




