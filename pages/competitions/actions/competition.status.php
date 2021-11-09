<?php

$competition = competition::get();
$new_status = form::value('status');
$description = form::value('description');
$details = [
    'status' => $new_status,
    'description' => $description
];


if ($new_status == competition::DELETE) {
    if ($competition->delete($description)) {
        form::process(true, $details, 'competition.deleted');
        form::return('');
    } else {
        form::process(false, $details, 'competition.status_updated!');
    }
} else {
    switch ($competition->set_status($new_status, $description)) {
        case 'OK':
            form::process(true, $details, 'competition.status_updated');
            break;
        case 'ERROR':
            form::process(false, $details, 'competition.status_updated!');
            break;
        case 'NOT_FOUND':
            form::process(false, $details, 'competition.not_found!');
            break;
    }
}
form::return();
