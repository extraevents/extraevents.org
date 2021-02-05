<?php

$request_1 = request::get(1);
switch ($request_1):
    case null:
        grand::include_check_grand('events.list');
        break;
    default:
        $event = new event($request_1);
        if (!$event->id) {
            page_404();
        }
        page::add_object('event', $event);
        $request_2 = request::get(2);
        switch ($request_2):
            case 'training':
                if (!$event->scramble_training) {
                    page_404();
                }
                if (page::is_post()) {
                    grand::action_check_grand('event.training');
                } else {
                    grand::include_check_grand('event.training');
                }

                break;
            case 'rankings':
                page::set_title($event->name);
                grand::include_check_grand('event.rankings');
                break;
            default:
                page_404();
        endswitch;
endswitch;
