<?php

$config = config :: get();
$mails = [
    'leaders' => [
        'mail' => $config->email->leaders,
        'subject' => $data->title
    ],
    'support' => [
        'mail' => $config->email->support,
        'subject' => "Support: {$data->title}"
    ],
];
$data->contacts = arrayToObject($mails);

$data->owner = (object) [
            'url' => $config->owner->url,
            'name' => $config->owner->name
];
