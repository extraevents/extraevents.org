<?php

$competition = competition::get();

$table = new build_table(t('competition.registrations'));
$table->add_head('competitor', t('results.competitor'));
$table->add_head('country', t('results.country'));
$table->add_filter(['competitor', 'country']);
$rows = db::rows(str_replace('@competition_id', $competition->id, $sql));
$events = [];
$competitors = [];
$registrations = [];
$registration_event = [];
foreach ($competition->rounds as $round) {
    $events[$round->event_id] = (object) [
                'id' => $round->event_id,
                'name' => $round->event_name,
                'icon' => event::get_image($round->event_id, $round->event_name, $round->icon_wca_revert)
    ];
    $registration_event[$round->event_id] = 0;
}

function registration_sort_name($a, $b) {
    if (!$b->name)
        return false;
    return strcasecmp($a->name, $b->name);
}

foreach ($rows as $row) {
    $keys = [];
    foreach (range(1, 4) as $i) {
        $keys[] = $row->{"person{$i}_id"};
        $competitors[] = $row->{"person{$i}_id"};
    }
    sort($keys);
    $persons_key = implode(' ', $keys);
    if (!isset($registrations[$persons_key])) {
        $persons = [];
        foreach (range(1, 4) as $i) {
            $persons[] = (object)
                    [
                        'id' => $row->{"person{$i}_id"},
                        'name' => $row->{"person{$i}_name"},
                        'country_name' => $row->{"person{$i}_country_name"},
                        'country_iso2' => $row->{"person{$i}_country_iso2"}
            ];
            usort($persons, 'registration_sort_name');
        }

        $registrations[$persons_key] = (object) [
                    'name' => $persons[0]->name . $persons[1]->name . $persons[2]->name . $persons[3]->name,
                    'persons' => $persons,
                    'events' => []
        ];
    }
    $registrations[$persons_key]->events[] = $row->event_id;
    $registration_event[$row->event_id]++;
}
uasort($registrations, 'registration_sort_name');
foreach ($events as $event) {
    $table->add_head($event->id, $event->icon);
}
foreach ($registrations as $persons_key => $registration) {
    $row = new build_row();
    $persons_out = [];
    $countries_out = [];
    foreach ($registration->persons as $person) {
        if ($person->id) {
            $persons_out[] = person::get_line($person->id, $person->name);
            $countries_out[] = region::flag($person->country_name, $person->country_iso2) . ' ' . $person->country_name;
        }
    }
    $row->add_value('competitor', implode('<br>', $persons_out));
    $row->add_value('country', implode('<br>', $countries_out));
    foreach ($registration->events as $event_id) {
        $event = $events[$event_id];
        $row->add_value($event->id, $event->icon);
    }
    $table->add_tr($row);
}
$row = new build_row();

$row->add_value('competitor', t('table.total') . ' ' . sizeof(array_unique($competitors)));
foreach ($events as $event) {
    $row->add_value($event->id, $registration_event[$event->id]);
}
$table->add_foot($row);

$data = (object) [
            'out_registrations' => $table->out()
];

