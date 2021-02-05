<?php

$content = file_get_contents(
        "readme.md");

$data = arrayToObject([
    'regulations' => markdown::convertToHtml($content)
        ]);


