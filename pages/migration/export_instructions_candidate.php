<?php
$rows = db::rows("select Name from `RequestCandidateTemplate` order by ID",
        'initial_data');
$dir = file::build_path([$base_dir, 'instructions']);
$filename = "$dir/candidate.txt";
$values = [];
foreach ($rows as $n => $row) {
    $values[] = ($n + 1) . ". $row->Name";
}
$value = str_replace("<br>", "\n", implode("\n", $values));
file_put_contents($filename, $value);
?>
<p><?= $filename ?> - <?= sizeof($values) ?></p>