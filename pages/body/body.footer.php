<?php

$config = config :: get();
$mails = [
    'leaders' => [
        'mail' => urlencode($config->email->leaders),
        'subject' => $data->title
    ],
    'support' => [
        'mail' => urlencode($config->email->support),
        'subject' => "Support: {$data->title}"
    ],
];
$data->contacts = arrayToObject($mails);

$data->owner = (object) [
            'url' => $config->owner->url,
            'name' => $config->owner->name
];
