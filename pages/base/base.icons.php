<?php

page::set_title(t('navigation.icons'));
$filenames = [];
foreach (scandir('svgs') as $filename) {
    if (strpos($filename, ".svg")) {
        $filenames[] = $filename;
    }
}
$data = arrayToObject([
    'filenames' => $filenames
        ]);

