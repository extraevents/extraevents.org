<?php

$competition = competition::get();

$status = $competition->status;
$actions = $competition->actions();

$table = new build_table();
$table->add_head('current_status', false);
$table->add_head('status', t('competition.status'));
$table->add_head('action', t('competition.action'));
foreach (competition::get_statuses() as $action) {
    $row = new build_row();
    $row->add_value('status', $competition->status_line($action));
    if ($status == $action) {
        $row->add_value('current_status',
                '<i class="far fa-hand-point-right"></i>');
    }

    if (in_array($action, $actions)) {
        $button = "<form data-confirm>"
                . "<input hidden name='status' value='$action'>"
                . "<button>"
                . t("competition.actions." . $status . '__' . $action)
                . "</button>"
                . "</form>";
        $row->add_value('action', $button);
    }
    $table->add_tr($row);
}


$data = (object) [
            'status_block_actions' => $table->out()
];

