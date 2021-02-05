<?php

$id = request::get(1);
switch ($id):
    case null:
        grand::include_check_grand('persons.list');
        break;
    case 'login':
        if (!filter_input(INPUT_SERVER, 'HTTP_REFERER')) {
            #page_404();
        }
        grand::include_check_grand('person.login');
        break;
    case 'logout':
        page::post_required();
        grand::action_check_grand('person.logout');
        break;
    case 'logout_all':
        page::post_required();
        grand::action_check_grand('person.logout_all');
        break;
    default:
        $person = new person($id);
        if (!$person->id) {
            page_404();
        }
        page::set_title($person->name);
        page::add_object('person', $person);
        grand::include_check_grand('person.info');
endswitch;
