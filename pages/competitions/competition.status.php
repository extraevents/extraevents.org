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
        $button = "<form data-confirm>"
                . "<input hidden name='status' value='$action'>"
                . "<button>"
                . t($key)
                . "</button>"
                . "</form>";
        $row->add_value('action', $button);
        $key_details = $key . '__details';
        $details = t($key_details);
        if ($details != "{%$key_details}") {
            $row->add_value('details', '<i class="color_red fas fa-exclamation-triangle"></i> ' . $details);
        }
    }
    $table->add_tr($row);
}


$data = (object) [
            'status_block_actions' => $table->out()
];

