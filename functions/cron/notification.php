<?php

function notifications_competition_change_status() {
    $email_leaders = config::get()->email->leaders;
    $title = config::get()->title;

    $competitions_status = sql_query::rows('notifications_competition_change_status', [], helper::db());
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
            $competition_line = "<a href='http:$page_index/competitions/$competition->id'>$competition->name</a>";
        } else {
            $competition_line = "[DEL] $competition_status->competition";
        }
        if ($person->id) {
            $person_line = $person->name;
        } else {
            $person_line = "[DEL] $competition_status->person";
        }
        $timestamp = $competition_status->timestamp;
        $body = "<p>$competition_line. <b>$person_line</b>: $notification_competition_status ($timestamp)</p>";
        if ($competition_status->description) {
            $body .= '<p>' . $competition_status->description . '</p>';
        }

        $emails = [$email_leaders];
        if ($competition->contact) {
            $emails[] = $competition->contact;
        }
        $subject = "$competition->name: $title (" . $competition_status_values['status_new'] . ")";
        foreach ($emails as $email) {
            smtp::put($email,
                    $subject,
                    $body);
        }

        $body_discort = "**$competition->name**: " . $competition_status_values['status_new'] . " / $person->name $timestamp";

        if ($competition_status->description) {
            $body_discort .= "\n" . $competition_status->description;
        }

        discort::send('competition', $body_discort);
    }

    return sizeof($competitions_status);
}
