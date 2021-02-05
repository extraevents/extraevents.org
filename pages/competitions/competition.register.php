<?php

$competition = competition::get();
$wca_id = wcaoauth::wca_id();
$rows = db::rows(str_replace(['@person_id', '@competition_id'], [$wca_id ? $wca_id : '-', $competition->id], $sql));

$enable_registration = $competition->show_register();

$table = new build_table(t('round.register.title'));
$table->add_head('event', t('round.event'));
$table->add_head('registation_status', false);
$table->add_head('register', false);
$table->add_head('competitor_registered', t('round.register.registrations_count'));
foreach ($rows as $r) {
    $team_person_registred = ($r->person1_id != '') + ($r->person2_id != '') + ($r->person3_id != '') + 1;
    $row = new build_row();
    $row->add_value('event', event::get_image($r->event_id, $r->event_name, $r->icon_wca_revert) . ' ' . $r->event_name);
    $row->add_value('competitor_registered',
            "$r->registred_count / $r->competitor_limit");
    if ($competition->show_register()) {
        if (!$r->registred) {
            $row->add_value('register',
                    "<form data-event='$r->event_id' data-action='register'>"
                    . '<button> '
                    . '<i class="fas fa-user-plus"></i> '
                    . t('round.register.buttons.register')
                    . '</button></form>');
        } else {
            $row->add_value('register',
                    "<form data-event='$r->event_id' data-action='unregister' data-confirm>"
                    . '<button> '
                    . '<i class="fas fa-user-minus"></i> '
                    . t('round.register.buttons.unregister')
                    . '</button>'
                    . '</form>');
        }
    }
    $registation_status = false;
    if ($r->team_complete) {
        $registation_status = '<i class="fas fa-check"></i> ' .
                t('round.register.statuses.complete');
    } elseif ($r->registred) {
        $registation_status = '<i class="fas fa-hourglass-half"></i> ' .
                t('round.register.statuses.part') .
                ' (' . $team_person_registred . '/' . $r->person_count . ')' .
                '<p><i class="fas fa-key"></i> ' .
                t('round.register.team_key')
                . ': '
                . $r->key
                . '</p>';
    } elseif ($r->registred_count >= $r->competitor_limit) {
        $registation_status = '<i class="fas fa-battery-full"></i> ' .
                t('round.register.statuses.full');
    } elseif ($r->person_count > 1) {
        $registation_status = t('round.register.team_key_join') .
                '<p><i class="fas fa-key"></i>'
                . ' <input maxlength="6" size="10" data-team-key/>'
                . '</p>';
    }

    $persons = [];
    foreach (range(1, 4) as $i) {
        if ($r->{"person{$i}_id"}) {
            $persons[] = '<p>' .
                    '<i class="fas fa-user-plus"></i>' .
                    person::get_line($r->{"person{$i}_id"}, $r->{"person{$i}_name"}) .
                    '</p>';
        }
    }

    $row->add_value('registation_status',
            $registation_status .
            implode('', $persons));
    $table->add_tr($row);
}
$registation_description = $competition->show_register() ?
        t('competition.register_descriptions.open') :
        t('competition.register_descriptions.close');

$data = arrayToObject(
        [
            'description' => $registation_description,
            'events' => $table->out()
        ]
);
