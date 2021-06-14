<?php

function support_error() {
    $email = config::get()->email->support;
    $short = config::get()->short;

    $count = sizeof(errors::get());
    if (!$count) {
        return
                false;
    }
    return
            smtp::put($email, "$short: support_error $count", "$short: support_error $count");
}

function support_checker() {
    $email = config::get()->email->support;
    $short = config::get()->short;

    $site = config::get()->site;
    $get = @stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
    $read = @stream_socket_client("ssl://$site:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
    $cert = @stream_context_get_params($read);
    $openssl_x509_parse = @openssl_x509_parse($cert["options"]["ssl"]["peer_certificate"] ?? FALSE);
    $validTo_time_t = $openssl_x509_parse['validTo_time_t'] ?? FALSE;

    $ssl_expired = $validTo_time_t ? date('d.m.y', $validTo_time_t) : false;


    $file = curl_init("http://whois.ru/$site");
    curl_setopt($file, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($file, CURLOPT_HEADER, false);
    curl_setopt($file, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($file, CURLOPT_MAXREDIRS, 5);
    $data = curl_exec($file);
    $code = curl_getinfo($file, CURLINFO_HTTP_CODE);
    curl_close($file);

    if ($code != 200) {
        trigger_error($code, E_USER_ERROR);
    }

    preg_match("/Registry Expiry Date: (.*Z)/", $data, $matches);
    $expiry = $matches[1];
    $exptime = strtotime($expiry);
    $expdays = round(($exptime - time()) / 84600);
    $site_expired = date("d.m.y", $exptime);

    return
            smtp::put($email, "$short: support_checker",
                    "$short: support_checker. ssl_expired = $ssl_expired, site_expired=$site_expired");
}

function support_backup() {
    $email = config::get()->email->support;
    $short = config::get()->short;

    return
            smtp::put($email, "$short: support_backup",
                    "You need to create a backup!");
}
