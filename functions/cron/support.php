<?php

function support_ping() {
    $text = "Ping";
    return
            support_notification($text, $text);
}

function support_error() {
    $process = 'support_error';
    $errors = errors::get();
    $count = sizeof($errors);
    if (!$count) {
        return
                false;
    }
    $cash_new = md5(serialize($errors));
    $cash_old = cash::get($process);
    if ($cash_new == $cash_old) {
        return
                false;
    }
    cash::set($process, $cash_new);
    $text = "support_error $count";
    return
            support_notification($text, $text);
}

function support_checker() {
    $site = config::get()->site;
    $get = @stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
    $read = @stream_socket_client("ssl://$site:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
    $cert = @stream_context_get_params($read);
    $openssl_x509_parse = @openssl_x509_parse($cert["options"]["ssl"]["peer_certificate"] ?? FALSE);
    $validTo_time_t = $openssl_x509_parse['validTo_time_t'] ?? FALSE;

    $ssl_expired = $validTo_time_t ? date('d.m.y', $validTo_time_t) : false;


    $file = curl_init("https://www.nic.ru/whois/?searchWord=$site");
    curl_setopt($file, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($file, CURLOPT_HEADER, false);
    curl_setopt($file, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($file, CURLOPT_MAXREDIRS, 5);
    curl_setopt($file, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($file);
    $code = curl_getinfo($file, CURLINFO_HTTP_CODE);
    curl_close($file);
/*
    if ($code != 200) {
        $site_expired = "whois.ru: $code";
    } else {
        preg_match("/Registrar Registration Expiration Date: (.*Z)/", $data, $matches);
        var_dump($matches);
        exit();
        $expiry = $matches[1];
        $exptime = strtotime($expiry);
        $expdays = round(($exptime - time()) / 84600);
        $site_expired = date('d.m.y', $exptime);
    }*/
    $site_expired= '12.01.2023';
    $text = "ssl_expired = $ssl_expired, site_expired = $site_expired";
    $subject = "support_checker";
    return
            support_notification($subject, $text);
}

function support_backup() {
    $subject = "support_backup";
    $text = "You need to create a backup!";

    return
            support_notification($subject, $text);
}

function support_scramble($attributes) {
    $datediff = $attributes->datediff;
    $competitions = db::rows("
           SELECT 
            id
           FROM competitions c
           WHERE TIMESTAMPDIFF(DAY,NOW(),start_date) between 0 and $datediff
        ");

    $scrambles_absent = [];
    foreach ($competitions as $competition_row) {
        $competition = new competition($competition_row->id);
        if ($competition->enable_regenerate_scrambles()) {
            foreach ($competition->rounds as $round) {
                $file = round::file_scramble($round);
                if (!file_exists($file)) {
                    $scrambles_absent[] = $round;
                }
            }
        }
    }

    if (sizeof($scrambles_absent)) {
        $text = '';
        foreach ($scrambles_absent as $round) {
            $text .= "$round->competition_name $round->event_name $round->round_format;";
        }
        support_notification('Need to generate scrambles', $text);
    }
    return sizeof($scrambles_absent);
}

function support_notification($subject, $text) {
    $short = config::get()->short;
    $email = config::get()->email->support;
    return
            [
                'smtp' => smtp::put($email, $subject, $text),
                'telegram' => telegram::send('support', $subject, $text)
    ];
}
