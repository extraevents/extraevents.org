<?php

function random_string($length) {
    $key = '';
    $keys = [2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'W', 'X', 'Y', 'Z'];


    for ($i = 0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }

    return $key;
}

function transliterate($textcyr) {
    $cyr = array(
        'ж', 'ч', 'щ', 'ш', 'ю',
        'а', 'б', 'в', 'г', 'д',
        'е', 'ё', 'з', 'и', 'й',
        'к', 'л', 'м', 'н', 'о',
        'п', 'р', 'с', 'т', 'у',
        'ф', 'х', 'ц', 'ъ', 'ь',
        'э', 'я', 'ы',
        'Ж', 'Ч', 'Щ', 'Ш', 'Ю',
        'А', 'Б', 'В', 'Г', 'Д',
        'Е', 'Ё', 'З', 'И', 'Й',
        'К', 'Л', 'М', 'Н', 'О',
        'П', 'Р', 'С', 'Т', 'У',
        'Ф', 'Х', 'Ц', 'Ъ', 'Ь',
        'Э', 'Я', 'Ы');
    $lat = array(
        'zh', 'ch', 'shch', 'sh', 'iu',
        'a', 'b', 'v', 'g', 'd',
        'e', 'e', 'z', 'i', 'i',
        'k', 'l', 'm', 'n', 'o',
        'p', 'r', 's', 't', 'u',
        'f', 'kh', 'ts', 'ie', '',
        'e', 'ia', 'y',
        'Zh', 'Ch', 'Shch', 'Sh', 'Iu',
        'A', 'B', 'V', 'G', 'D',
        'E', 'E', 'Z', 'I', 'I',
        'K', 'L', 'M', 'N', 'O',
        'P', 'R', 'S', 'T', 'U',
        'F', 'Kh', 'Ts', 'Ie', '',
        'E', 'Ia', 'Y');
    return str_replace($cyr, $lat, $textcyr);
}

function PageLocal() {
    return str_replace("index.php", "", $_SERVER['PHP_SELF']);
}
