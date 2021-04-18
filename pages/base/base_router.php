<?php

$request_0 = request::get(0);
switch ($request_0):
    case null:
        grand::include_check_grand('base.info');
        break;
    case 'icons':
        grand::include_check_grand('base.icons');
        break;
    case 'export':
        grand::include_check_grand('base.export');
        break;
    case 'support':
        $request_1 = request::get(1);
        switch ($request_1):
            case null:
                grand::include_check_grand('base.support');
                break;
            case 'login_share':
                grand::include_check_grand('base.support_login_share');
                break;
        endswitch;
endswitch;

