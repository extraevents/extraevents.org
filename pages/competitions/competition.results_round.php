<?php

$round = page::get_object('round');
$results = results::get($round,
                [
                    'team_complete',
                    'remove' => false
                ]
);
$event = new event($round->event_id);
$solve_count = $round->format_solve_count;
$next_round_exists = $round->competitor_limit->next ?? false;

$table = new build_table();
$table->add_head('position', t('results.position'));
$table->add_head('competitor', t('results.competitor'));
$table->add_head($round->format_sort_by, t('results.sort_by.' . $round->format_sort_by));
$table->add_head($round->format_sort_by_second, t('results.sort_by.' . $round->format_sort_by_second));
$table->add_head('country', t('results.country'));
foreach (range(1, $round->format_solve_count) as $attempt) {
    $table->add_head("attempt_$attempt", $attempt);
}
$table->add_head('mark', false);

function registration_sort_name($a, $b) {
    if (!$b->name)
        return false;
    return strcasecmp($a->name, $b->name);
}

foreach ($results as $r) {
    $persons = [];
    foreach (range(1, 4) as $i) {
        $persons[] = (object)
                [
                    'id' => $r->{"person{$i}_id"},
                    'name' => $r->{"person{$i}_name"},
                    'country_name' => $r->{"person{$i}_country_name"},
                    'country_iso2' => $r->{"person{$i}_country_iso2"}
        ];
        usort($persons, 'registration_sort_name');
    }
    $persons_out = [];
    $countries_out = [];
    $competitor_sort = "";
    foreach ($persons as $person) {
        if ($person->id) {
            $persons_out[] = person::get_line($person->id, $person->name);
            $countries_out[] = region::flag($person->country_name, $person->country_iso2) . ' ' . $person->country_name;
            $competitor_sort .= $person->name;
        }
    }

    $next_mark = false;
    if (!$round->final and $r->next_round) {
        $next_mark = 'next';
    }
    if ($round->final and $r->best > 0 and $r->pos <= 3) {
        $next_mark = 'top';
    }
    $row = new build_row([
        'card_id' => $r->card_id,
        'next_mark' => $next_mark,
        'except' => json_encode(results::get_except(
                        $r->attempt1,
                        $r->attempt2,
                        $r->attempt3,
                        $r->attempt4,
                        $r->attempt5))
    ]);
    $not_publish = null;
    if ($r->reason_not_publish) {
        $title = t('results.not_publish', ['reason' => $r->reason_not_publish]);
        $not_publish = " <i title='$title' class='color_red far fa-times-circle'></i>";
    }
    $row->add_value('position', $r->pos . $not_publish);
    $row->add_value('position_sort', $r->pos ?? PHP_INT_MAX);
    $row->add_value('competitor', implode('<br>', $persons_out));
    $row->add_value('country', implode('<br>', $countries_out));
    $row->add_value('competitor_sort', $competitor_sort);
    foreach (range(1, $round->format_solve_count) as $attempt) {
        $row->add_value("attempt_$attempt",
                centisecond::out($r->{"attempt$attempt"} ?? false));
    }
    $row->add_value("single", centisecond::out($r->best ?? false));
    $row->add_value("average", centisecond::out($r->average ?? false));
    $table->add_tr($row);
}
$table->sort([
    'position_sort' => 'asc',
    'competitor_sort' => 'asc'
]);

$data = arrayToObject(
        [
            'title' => $event->line() . ', ' . $round->round_format,
            'table' => $table->out()
        ]
);

if (grand::resolve_access_global('competitions', 'competition.results_round_print')) {
    $data->results_print_link = "competitions/$round->competition_id/results/$round->event_id/{$round->round_number}/print";
}
