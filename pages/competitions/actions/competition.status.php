<?php

$competition = competition::get();
$new_status = form::value('status');

if ($new_status == competition::DELETE) {
    if ($competition->delete()) {
        form::process(true, ['status' => $new_status], 'competition.deleted');
        form::return('');
    } else {
        form::process(false, ['status' => $new_status], 'competition.status_updated!');
    }
} else {

    if ($competition->set_status($new_status)) {
        form::process(true, ['status' => $new_status], 'competition.status_updated');
    } else {
        form::process(false, ['status' => $new_status], 'competition.status_updated!');
    }
}
form::return();
