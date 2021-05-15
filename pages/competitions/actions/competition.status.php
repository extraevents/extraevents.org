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
    switch ($competition->set_status($new_status)) {
        case 'OK':
            form::process(true, ['status' => $new_status], 'competition.status_updated');
            break;
        case 'ERROR':
            form::process(false, ['status' => $new_status], 'competition.status_updated!');
            break;
        case 'NOT_FOUND':
            form::process(false, ['status' => $new_status], 'competition.not_found!');
            break;
    }
}
form::return();
