<?php

$competition = competition::get();

$status = $competition->status;
$actions = $competition->actions();

$table = new build_table();
$table->add_head('current_status', false);
$table->add_head('status', t('competition.status'));
$table->add_head('action', t('competition.action'));
$table->add_head('details', false);

foreach (competition::get_statuses() as $action) {
    $row = new build_row();
    $row->add_value('status', $competition->status_line($action));
    if ($status == $action) {
        $row->add_value('current_status',
                '<i class="far fa-hand-point-right"></i>');
    }

    if (in_array($action, $actions)) {
        $status_action = $status . '__' . $action;
        $key = "competition.actions." . $status_action;
        $key_button = $key . '.button';
        $key_details = $key . '.details';
        $button = "<form data-available_action data-confirm>"
                . "<input hidden name='status' value='$action'>"
                . "<input hidden name='description'>"
                . "<button>"
                . t($key_button)
                . "</button>"
                . "</form>";
        $deadline_config = (config::get('regulation')->deadline->$status_action ?? false);
        $deadline_details = null;
        $is_deadline_expired = false;
        $is_deadline_required = config::get('regulation')->deadline->required ?? false;
        $deadline_date = false;
        if ($deadline_config) {
            if ($deadline_config->start_date ?? false) {
                $deadline_date = strtotime($competition->start_date . " $deadline_config->start_date day");
            }
            if ($deadline_config->end_date ?? false) {
                $deadline_date = strtotime($competition->end_date . " $deadline_config->end_date day");
            }
            $now_00 = strtotime(gmdate("Y-m-d\T00:00:00\Z"));
            $deadline_details = date('Y-m-d', $deadline_date) . ' 00:00 UTC';
            if ($deadline_date <= $now_00) {
                $is_deadline_expired = true;
                $deadline_details = "<span class='color_red'>$deadline_details</span>";
            }
        }
        if ($is_deadline_expired and $is_deadline_required) {
            $row->add_value('action', '<i class="color_red fas fa-times-circle"></i> ' . t('competition.actions.expired'));
        } else {
            $row->add_value('action', $button);
        }
        $details = t($key_details,
                ['deadline' => $deadline_details]
        );
        if ($deadline_date and $details != "{%$key_details}") {
            $row->add_value('details', '<i class="color_orange fas fa-exclamation-triangle"></i>' . ' ' . $details);
        }
    }
    $table->add_tr($row);
}
$description = new build_block(false);
$description->add_element(t('competition.actions.description'),
        '<textarea data-description class="large"></textarea>');

$competitions_status = sql_query::rows('competition_change_status',
                ['competition' => $competition->id],
                helper::db());

$statuses = new build_table();
$statuses->add_head('timestamp', t('competition.actions.log.timestamp'));
$statuses->add_head('person', t('competition.actions.log.person'));
$statuses->add_head('status_old', t('competition.actions.log.status_old'));
$statuses->add_head('status_new', t('competition.actions.log.status_new'));
$statuses->add_head('description', t('competition.actions.log.description'));
foreach ($competitions_status as $competition_status) {
    $row = new build_row();
    $row->add_value('person', (New person($competition_status->person))->line());
    if ($competition_status->status_old) {
        $row->add_value('status_old', t('competition.statuses.' . $competition_status->status_old));
    }
    if ($competition_status->status_new) {
        $row->add_value('status_new', t('competition.statuses.' . $competition_status->status_new));
    }
    $row->add_value('timestamp', $competition_status->timestamp);
    $row->add_value('description', $competition_status->description);
    $statuses->add_tr($row);
}

$data = (object) [
            'status_description' => $description->out(),
            'status_block_actions' => $table->out(),
            'statuses' => sizeof($competitions_status) > 0 ? $statuses->out() : ''
];

