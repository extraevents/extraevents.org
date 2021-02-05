<?php

$region = query::value('region');
$result_type = db::escape(request::get(3));
$event_id = db::escape(request::get(1));
$event = new event($event_id);
$list = db::rows(str_replace(
                        [
                            '@region',
                            '@event_id',
                            '@result_type'
                        ],
                        [
                            $region ? $region : 'world',
                            $event_id,
                            $result_type
                        ]
                        , $sql));

$table = new build_table();
$table->add_head('rank', t('rankings.rank'));
$table->add_head('competitor', t('results.competitor'));
$table->add_head('result', t('results.types.' . $result_type));
$table->add_head('country', t('results.country'));
$table->add_head('competition', t('results.competition'));
if ($result_type == 'average') {
    $table->add_head("attempt_1", 1);
    $table->add_head("attempt_2", 2);
    $table->add_head("attempt_3", 3);
    $table->add_head("attempt_4", 4);
    $table->add_head("attempt_5", 5);
}
foreach ($list as $r) {
    if ($result_type == 'average') {
        $row = new build_row([
            'except' => json_encode(results::get_except(
                            $r->attempt1,
                            $r->attempt2,
                            $r->attempt3,
                            $r->attempt4,
                            $r->attempt5))
        ]);
    } else {
        $row = new build_row();
    }
    $row->add_value('rank', $r->rank);
    $row->add_value('competitor', person::get_line($r->person_id, $r->person_name));
    $row->add_value('country',region::flag($r->person_country_name, $r->person_country_iso2) . ' ' . $r->person_country_name);
    $row->add_value('result', centisecond::out($r->result));
    $row->add_value('competition', competition::get_line($r->competition_id, $r->competition_name, $r->competition_country_name, $r->competition_country_iso2, "/results/$event_id/$r->round_number"));
    if ($result_type == 'average') {
        $row->add_value("attempt_1", centisecond::out($r->attempt1));
        $row->add_value("attempt_2", centisecond::out($r->attempt2));
        $row->add_value("attempt_3", centisecond::out($r->attempt3));
        $row->add_value("attempt_4", centisecond::out($r->attempt4));
        $row->add_value("attempt_5", centisecond::out($r->attempt5));
    }

    $table->add_tr($row);
}

$navigation = [];

$navigation[] = [
    "icon" => false,
    "url" => "%i/events/$event_id/rankings/single",
    "title" => t('results.types.single')
];

$navigation[] = [
    "icon" => false,
    "url" => "%i/events/$event_id/rankings/average",
    "title" => t('results.types.average')
];

$data = arrayToObject([
    'title' => $event->line(),
    'table' => $table->out(),
    'navigation' => $navigation
        ]);

