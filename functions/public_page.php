<?php

function page_404() {
    ob_end_clean();
    header('HTTP/1.0 404 not found');
    exit(
            file_get_contents('public/404.html')
    );
}

function page_401() {
    if (ob_get_contents())
        ob_end_clean();
    header('HTTP/1.0 401 Unauthorized');
    $link = wcaoauth::url();
    exit(str_replace(
                    ['<!--%link-->', '%hidden'],
                    [$link, wcaoauth::get_user_id() ? 'hidden' : ''],
                    file_get_contents(__DIR__ . '/../public/401.html')
    ));
}

function page_500($title = false, $details = false) {
    if (ob_get_contents())
        ob_end_clean();
    header('HTTP/1.0 500 Internal Server Error');
    exit(str_replace(
                    ['<!--%title-->', '<!--%details-->'],
                    [$title, $details],
                    file_get_contents(__DIR__ . '/../public/500.html')
    ));
}
