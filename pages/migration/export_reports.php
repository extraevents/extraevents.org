<?php
$rows = db::rows(
                "select competitor.wcaid, competitor.name competitor_name,
                c.wca,c.name competition_name, c.startdate,
                cr.report
                from `CompetitionReport` CR
                JOIN Competition C on C.ID=CR.Competition
                left outer join delegate D on D.ID=CR.Delegate
                left outer join competitor on competitor.wid=D.wid or competitor.wid=cr.delegateWca
                order by c.startdate",
        'initial_data');
$count_reports = 0;
$dir = file::build_path([$base_dir, 'reports']);
foreach ($rows as $row) {
    $report = '';
    $report .= $row->competition_name . "\n";
    $report .= $row->startdate . "\n";
    $report .= $row->competitor_name . ' (' . $row->wcaid . ")\n";
    $report .= "\n---\n";
    $report .= $row->report;
    $filename = "$dir/{$row->wca}_$row->wcaid.txt";
    file_put_contents($filename, $report);
    $count_reports++;
}
?>

<p><?= $dir ?>/ - <?= $count_reports ?></p>