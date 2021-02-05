<?php
$row = db::row("select Value from BlockText where Name='Report'",
        'initial_data');
$dir = file::build_path([$base_dir, 'instructions']);
$filename = "$dir/report.txt";
file_put_contents($filename, $row->Value);
?>
<p><?= $filename ?> - true</p>