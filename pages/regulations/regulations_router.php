<?php

$request_1 = request::get(1);
switch ($request_1):
    case null:
        grand::include_check_grand('regulations');
        break;
    default:
        page_404();
endswitch;
