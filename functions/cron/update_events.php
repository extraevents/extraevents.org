<?php

function update_events() {
    $process_name = 'update_events';
    $events_yml = event::get_yml();
    $new_cash = md5(serialize($events_yml));
    $old_cash = cash::get($process_name);
    if ($new_cash == $old_cash) {
        return false;
    }
    cash::set($process_name, $new_cash);
    db::exec("DELETE FROM events");

    foreach ($events_yml as $event_id => $event_yml) {
        $event = new stdClass();
        $event->id = $event_id;
        $event->name = $event_yml->name;
        $event->person_count = $event_yml->person_count ?? 1;
        $event->scramble_tnoodle_events = json_encode($event_yml->scramble_tnoodle->events ?? []);
        $event->scramble_tnoodle_hook = $event_yml->scramble_tnoodle->hook ?? false;
        $event->scramble_tnoodle_format = $event_yml->scramble_tnoodle->format ?? false;
        $event->long_inspection = ($event_yml->long_inspection ?? false) + 0;
        $event->scramble = $event_yml->scramble ?? false;
        $event->scramble_training = $event_yml->scramble_training ?? $event_yml->scramble ?? false;
        $event->drawing = $event_yml->drawing ?? false;
        $event->comments = json_encode($event_yml->comments ?? []);
        $event->custom_wrap = ($event_yml->custom_wrap ?? false) + 0;
        $event->scrambling = ($event_yml->scrambling ?? false) + 0;
        $event->icon_wca_revert = $event->scrambling ? ($event_yml->scramble_tnoodle->events[0] ?? false) : false;

        if (($event->scramble or $event->scramble_training) and!$event->drawing) {
            trigger_error(__FUNCTION__ . ": $event_id drawing");
        }

        foreach (['scramble', 'scramble_training', 'drawing', 'scramble_tnoodle_hook'] as $func) {
            if ($event->$func and!function_exists($event->$func)) {
                trigger_error(__FUNCTION__ . ": $event_id $func/{$event->$func}");
            }
        }


        db::exec("INSERT INTO events 
                    (id,name,person_count,
                    scramble_tnoodle_events,scramble_tnoodle_hook, scramble_tnoodle_format,
                    long_inspection,scramble,scramble_training,drawing,
                    comments,custom_wrap,scrambling,icon_wca_revert)
                VALUES ('$event->id','$event->name','$event->person_count',
                    '$event->scramble_tnoodle_events','$event->scramble_tnoodle_hook','$event->scramble_tnoodle_format',
                    $event->long_inspection,'$event->scramble','$event->scramble_training','$event->drawing',
                    '$event->comments',$event->custom_wrap,$event->scrambling,'$event->icon_wca_revert') ");
    }
    return true;
}
