<?php

$data->message = message::get();
$data->message_error = substr($data->message, -1) == '!';
