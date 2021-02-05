<?php

$ccc = new import('competitions',
        'competitions.import',
        'import/competitions_schema.json');
$is_leader = access::is_leader();
$competitions = $ccc->get_data()->competitions ?? [];
foreach ($competitions as $competition) {
    $organizers = new stdClass();
    $key = $competition->id;
### logical checks
#id
    $wca_competition = wca_competition::get($competition->id);
    $wca_id = wcaoauth::get_user()->wca_id ?? false;
    if (!$wca_competition) {
        $ccc->add_message(false, $key, "Competition not found on the WCA.");
        goto next_competition;
    }

#organizers.main
    if (!wca_user::get($competition->organizers->main)) {
        $ccc->add_message(false, $key, "User with wca_id [{$competition->organizers->main}] not found on the WCA site.");
        goto next_competition;
    } else {
        $organizers->main = $main_organizer_id;
    }

#check_access
    if ($wca_id !== $competition->organizers->main
            and!$is_leader) {
        $ccc->add_message(false, $key, "You can only manage your own competitions.");
        goto next_competition;
    }

#organizers.additional
    foreach ($competition->organizers->additional ?? [] as $organizer) {
        if (!wca_user::get($organizer)) {
            $ccc->add_message(false, $key, "User with wca_id [{$organizer}] not found on the WCA site.");
            goto next_competition;
        }
    }

#events[]id
    foreach ($competition->events as $event) {
        $event = new event($event->id);
        if (!$event->exists()) {
            $ccc->add_message(false, $key, "Extra event with id [{$event->id}] not found .");
            goto next_competition;
        }
    }


#events[]rounds.number
    foreach ($competition->events as $event) {
        $n = 1;
        foreach ($event->rounds as $round) {
            if ($n != $round->number) {
                $ccc->add_message(false, $key,
                        "Incorrect round numbers " .
                        json_encode($round->number) .
                        " for the event [{$event->id}].");
                goto next_competition;
            }
            $n++;
        }
    }

#events[]rounds.competitorLimit.percent
    foreach ($competition->events as $event) {
        foreach ($event->rounds as $round) {
            if ($round->competitorLimit->type == 'percent' and $round->number == 1) {
                $ccc->add_message(false, $key,
                        " Event [{$event->id}], round [{$round->number}]."
                        . " The first round can not have a type 'percent' competitors limit.");
                goto next_competition;
            }
            if ($round->competitorLimit->type == 'percent' and $round->competitorLimit->level > 75) {
                $ccc->add_message(false, $key,
                        " Event [{$event->id}], round [{$round->number}]."
                        . " The competitors limit cannot be more than 75 percent.");
                goto next_competition;
            }
        }
    }

#events    
    $rounds = [];
    foreach ($competition->events as $event) {
        $final_round = sizeof($event->rounds);
        foreach ($event->rounds as $round) {
            $round_new = new stdClass();
            $round_new->key = (object) [
                        'competition_id' => $competition->id,
                        'event_id' => $event->id,
                        'round_number' => $round->number,
                        'url' => "competitions/$competition->id/%s/$event->id/$round->number",
                        'id' => $event->id . '_' . $round->number
            ];
            $round_new->cutoff = (object) [
                        'str' => (new centisecond($round->cutoff))->out(),
                        'centisecond' => $round->cutoff,
                        'is_set' => $round->cutoff > 0
            ];
            $round_new->time_limit = (object) [
                        'str' => (new centisecond($round->timeLimit))->out() .
                        ($round->isCumulative ? ' ' . t('round.cumulative') : ''),
                        'centisecond' => $round->timeLimit,
                        'is_cumulative' => $round->isCumulative,
                        'is_set' => $round->timeLimit > 0
            ];

            $round_new->round_type = wca_round_type::get(
                            $round->number,
                            $round->cutoff > 0,
                            $round->number == $final_round);
            $round_new->format = wca_format::get(
                            $round->format);
            $round_new->competitor_limit = (object) [
                        'type' => $round->competitorLimit->type,
                        'level' => $round->competitorLimit->level
            ];
            $rounds[$event->id][$round->number] = $round_new;
            if ($round->number - 1) {
                $rounds[$event->id][$round->number - 1]->competitor_limit->next = $round_new->competitor_limit;
            }
        }
    }

#competition
    $competition = new competition($competition->id);
    if (!$competition->exists()) {
        $competition->create($competition, $rounds, $organizers);
        $ccc->add_message(true, $key, 'created', $competition);
        goto next_competition;
    }

#update competition
    if (!$competition->is_status_draft() and!$is_leader
    ) {
        $ccc->add_message(false, $key, "You can only update competitions in the draft.");
        goto next_competition;
    }
    $user_id = wcaoauth::get_user_id() ?? false;
    if ($user_id !== $competition->get_organizer()->id and!$is_leader
    ) {
        $ccc->add_message(false, $key, "You can only manage your own competitions.");
        goto next_competition;
    }
    $affected = $competition->update($competition, $rounds, $organizers);
    if ($affected) {
        $ccc->add_message(true, $key, 'updated', $competition);
    } else {
        $ccc->add_message(null, $key, 'identical');
    }

    next_competition:;
}
$ccc->out();

