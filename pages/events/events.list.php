<?php

$list = db::rows($sql);


$table = new build_table(false);
$table->add_head('image', false);
$table->add_head('name', t('event.name'));
$table->add_head('long_inspection', false);
$table->add_head('person_count', false);
$table->add_head('id', t('event.id'));
$table->add_head('wca_events', t('event.wca_events'));
$table->add_head('training', false);
$table->add_filter(['name', 'id']);

foreach ($list as $e) {
    $row = new build_row();
    $row->add_value('image', event::get_image($e->id, $e->name, $e->icon_wca_revert));
    $row->add_value('name',
            "<a href='%i/events/$e->id/rankings/single'>$e->name</a>");
    $row->add_value('id', $e->id);

    if ($e->long_inspection) {
        $row->add_value('long_inspection', '<i title="Inspection 20 seconds" class="fas fa-stopwatch-20"></i>');
    }

    switch ($e->person_count) {
        case 1:
            $person_count = false;
            break;
        case 2:
            $person_count = '<i title="2 members in a team" class="fas fa-user-friends"></i>';
            break;
        case 3:

            $person_count = '<i title="3 members in a team" class="fas fa-users"></i>';
            break;
        default:
            $person_count = $event->person_count;
    };
    $row->add_value('person_count', $person_count);

    $wca_events_images = [];
    foreach (json_decode($e->scramble_tnoodle_events) as $wca_event) {
        $wca_events_images[] = event::get_image_wca($wca_event);
    }
    $row->add_value('wca_events',
            implode(' ', $wca_events_images));

    if ($e->scramble_training) {
        $row->add_value('training',
                "<a href='%i/events/$e->id/training'>" .
                t('event.training') .
                '</a>');
    }
    $table->add_tr($row);
}
$data = (object) [
            'info_events' => $table->out()
];
