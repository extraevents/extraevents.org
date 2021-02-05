<?php

$content = null;
$main = request::get(1);
switch ($main) {
    case null:
        grand::include_check_grand('api.description');
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
                        page_404();
                    default:
                        $content = person::api($id);
                }
                break;
            case 'competitions':
                switch ($id) {
                    case null:
                        page_404();
                    default:
                        $content = competition::api($id, $type);
                }
                break;
            case 'records':
                $content = results::api_records();
                break;
            default:
                page_404_api();
        }
        break;
    default:
        page_404_api();
}
if ($content !== null) {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    exit(json::out($content));
}
