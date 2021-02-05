<?php

$isTest = config :: isTest();
$isProd = $_SERVER['HTTP_USER_AGENT'] == 'Wget/1.17.1 (linux-gnu)';
$is_leader = access::is_leader();

if ($isTest or $isProd or $is_leader) {
    cron::run();
    log_clear::run();
    file_clear::run();
    db::close();
    ob_clean();
    echo 'OK';
    exit();
} else {
    header('HTTP/1.1 401 Unauthorized');
    db::close();
    die("You don't have permission to do this");
}