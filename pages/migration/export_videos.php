<?php
$rows = db::rows("
select 
C.Name Competition,D.Name Event,E.Round,Com.Video, group_concat(CONCAT(Cr.Name,' (',Cr.WCAID,')') SEPARATOR ';') Competitors
from Command Com
join Event E on E.ID=Com.Event
join Competition C on C.ID=E.Competition
join `CommandCompetitor` CC on CC.Command=Com.ID
join `Competitor` Cr on Cr.ID=CC.Competitor
join DisciplineFormat DF on DF.ID=E.DisciplineFormat
join Discipline D on D.ID=DF.Discipline
where coalesce(video,'')<>''
group by C.Name,D.Name,E.Round,Com.Video 
order by 1,2,3",
                'initial_data');

$contents = [];
foreach ($rows as $row) {
    $contents[] = "$row->Competition / $row->Event / $row->Round\n$row->Competitors\n$row->Video\n";
}
$dir = file::build_path([$base_dir, 'videos']);
$filename = "$dir/videos.txt";
file_put_contents($filename, implode("\n", $contents));
?>
<p><?= $filename ?> - <?= count($contents) ?></p>