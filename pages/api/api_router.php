<?php

$main = request::get(1);
$page = false;
switch ($main) {
    case null:
        grand::include_check_grand('api.description');
        $page = true;
        break;
    case 'v0':
        $section = request::get(2);
        $id = request::get(3);
        $type = request::get(4);
        switch ($section) {
            case 'team':
                $content = member::api_list();
                break;
            case 'persons':
                switch ($id) {
                    case null:
                        break;
                    default:
                        $content = person::api($id);
                }
                break;
            case 'competitions':
                switch ($id) {
                    case null:
                        break;
                    default:
                        $content = competition::api($id, $type);
                }
                break;
            case 'records':
                $content = results::api_records();
                break;
        }
        break;
    default:
}

if (!$page) {
    $content ??= page_404_api();
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    exit(json::out($content));
}

