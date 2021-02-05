<?php
$rows = db::rows("SELECT c.id FROM competitions c
    left outer join rounds r on c.id=r.competition_id
    WHERE r.competition_id is null");
$competitions_delete = [];
foreach ($rows as $row) {
    db::exec("DELETE FROM competitions WHERE id ='$row->id'");
    $competitions_delete[] = $row->id;
}
?>
<p>migration.competitions_wo_rounds / - <?= sizeof($rows) ?>
<p>delete:<br><?= implode('<br> - ', $competitions_delete) ?></p>

