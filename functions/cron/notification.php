<?php

function notifications_competition_change_status() {
    $email = config::get()->email->leaders;
    $short = config::get()->short;

    $competitions_status = sql_query::rows('notifications_competition_change_status', [], helper::db());
    $notification_body = [];
    foreach ($competitions_status as $competition_status) {
        $competition = new Competition($competition_status->competition);
        $person = new person($competition_status->person);
        sql_query::exec('update_competition_status_send_notification'
                , ['id' => $competition_status->id]
                , helper::db());
        $competition_status_values = [];
        $competition_status_values['status_old'] = $competition_status->status_old ?
                t("competition.statuses.$competition_status->status_old") :
                t("competition.statuses.none");
        $competition_status_values['status_new'] = t("competition.statuses.$competition_status->status_new");

        $notification_competition_status = t('notification.competition_status', $competition_status_values);
        $page_index = page::get_index();
        if ($competition->id) {
            $competition_line = "<a href='http://$page_index/competitions/$competition->id'>$competition->name</a>";
        } else {
            $competition_line = "[DEL] $competition_status->competition";
        }
        if ($person->id) {
            $person_line = $person->name;
        } else {
            $person_line = "[DEL] $competition_status->person";
        }
        $timestamp = $competition_status->timestamp;
        $notification_body [] = "<p>$competition_line. <b>$person_line</b>: $notification_competition_status ($timestamp)</p>";
    }
    $count = sizeof($notification_body);
    if (!$count) {
        return false;
    } else {
        smtp::put($email,
                "$short: Сhange of competition statuses",
                implode("", $notification_body));
        return $count;
    }
}
