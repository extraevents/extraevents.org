<?php

$list = sql_query::rows('competitions');
$table = new build_table();
$table->add_head('status', false);
$table->add_head('date', t('competition.date'));
$table->add_head('name', t('competition.name'));
$table->add_head('country', t('competition.country'));
$table->add_head('events', t('competition.events'));

$table->add_filter(['name', 'country']);

foreach ($list as $el) {
    $row = new build_row();
    $row->add_value('date', competition::date_range($el->start_date, $el->end_date));
    $row->add_value('status', competition::get_status_icon($el->status));
    $row->add_value('name', competition::get_line($el->id, $el->name, $el->country_name, $el->country_iso2));
    $row->add_value('country', competition::country_line($el->country_name, $el->city));
    $events = [];
    foreach (explode(';', $el->events) as $event) {
        if ($event) {
            $events[] = event::get_image(...explode(',', $event));
        }
    }
    $row->add_value('events', implode(' ', $events));
    $row->add_value('start_date', $el->start_date);
    $row->add_value('end_date', $el->end_date);
    $table->add_tr($row);
}
$table->sort([
    'start_date' => 'desc',
    'end_date' => 'desc',
    'name' => 'asc']);

$data = (object) [
            'competitions' => $table->out(),
            'event_filter' => event::filter(false, t('competition.event_filter.all_events'))
];
