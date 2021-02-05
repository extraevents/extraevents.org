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
    
endswitch;

