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
        $key = "competition.actions." . $status . '__' . $action;
        $key_button = $key . '.button';
        $key_details = $key . '.details';
        $button = "<form data-available_action data-confirm>"
                . "<input hidden name='status' value='$action'>"
                . "<input hidden name='description'>"
                . "<button>"
                . t($key_button)
                . "</button>"
                . "</form>";
        $row->add_value('action', $button);
        $details = t($key_details);
        if ($details != "{%$key_details}") {
            $row->add_value('details', '<i class="color_red fas fa-exclamation-triangle"></i> ' . $details);
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

