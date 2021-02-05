<?php

$request_1 = request::get(1);
switch ($request_1):
    case null:
        grand::include_check_grand('team.list');
        break;
    case 'import':
        if (page::is_post()) {
            grand::action_check_grand('team.import');
        } else {
            grand::include_check_grand('team.import');
        }
        break;
    default:
        page_404();
endswitch;
