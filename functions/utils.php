<?php

function random_string($length) {
    $key = '';
    $keys = [2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z'];


    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}

function PageIndex() {
    $index = "//" . $_SERVER['HTTP_HOST'] . str_replace("index.php", "", $_SERVER['PHP_SELF']);
    if (substr("$index", -1) == '/') {
        $index = substr_replace($index, '', -1);
    }
    if (substr("$index", -1) == '/') {
        $index = substr_replace($index, '', -1);
    }
    return $index;
}

function PageLocal() {
    return str_replace("index.php", "", $_SERVER['PHP_SELF']);
}
