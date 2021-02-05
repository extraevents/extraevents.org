<?php

$event = page::get_object('event');
$scramble = $event->generate_scramble(true);
$filename = training::filename($event->id);
$event->drawing_scramble($scramble, $filename,true);

$comments = implode("<br>", $event->comments);

$data = (object) [
            'event' => $event,
            'comments' => $comments,
            'filename' => $filename,
            'scramble' => str_replace(["& "], ["<br>"], $scramble ?? false),
];
