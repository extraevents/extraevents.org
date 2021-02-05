<?php
$rows = db::rows("select Name, Value, Country from BlockText where Name like 'Regulation%' ORDER by Name,Country",
        'initial_data');
$blocks = [];
foreach ($rows as $row) {
    $block = "---\n";
    $block .= "$row->Country\n";
    $block .= "$row->Value\n";
    $blocks[$row->Name] ??= '';
    $blocks[$row->Name] .= $block;
}

$count_blocks = 0;
$dir = file::build_path([$base_dir, 'regulations_ext']);
foreach ($blocks as $code => $block) {
    $filename = "$dir/$code.txt";
    file_put_contents($filename, $block);
    $count_blocks++;
}
?>

<p><?= $dir ?>/ - <?= $count_blocks ?></p>