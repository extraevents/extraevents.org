<?php
include 'events_map.php';
$rows_results = db::rows(
                <<<out
select 	
 C.id,
 Cn.WCA competition_id, D.Code `event_id`, E.Round round_number,
 Competitor_count.competitors,
 Cr1.WCAID person1,
 Cr2.WCAID person2,
 Cr3.WCAID person3,
 Cr4.WCAID person4,
case  WHEN best.ID is null THEN 0 WHEN best.IsDNF THEN -1 WHEN best.IsDNS THEN -1 ELSE best.Minute*60*100+best.Second*100+best.Milisecond END best,
case  WHEN average.ID is null or A3.ID is null THEN 0 WHEN average.IsDNF THEN -1 WHEN average.IsDNS THEN -1 ELSE average.Minute*60*100+average.Second*100+average.Milisecond END average,
case  WHEN A1.ID is null THEN 0 WHEN A1.IsDNF THEN -1 WHEN A1.IsDNS THEN -1 ELSE A1.Minute*60*100+A1.Second*100+A1.Milisecond END attempt1,
case  WHEN A2.ID is null THEN 0 WHEN A2.IsDNF THEN -1 WHEN A2.IsDNS THEN -1 ELSE A2.Minute*60*100+A2.Second*100+A2.Milisecond END attempt2,
case  WHEN A3.ID is null THEN 0 WHEN A3.IsDNF THEN -1 WHEN A3.IsDNS THEN -1 ELSE A3.Minute*60*100+A3.Second*100+A3.Milisecond END attempt3,
case  WHEN A4.ID is null THEN 0 WHEN A4.IsDNF THEN -1 WHEN A4.IsDNS THEN -1 ELSE A4.Minute*60*100+A4.Second*100+A4.Milisecond END attempt4,
case  WHEN A5.ID is null THEN 0 WHEN A5.IsDNF THEN -1 WHEN A5.IsDNS THEN -1 ELSE A5.Minute*60*100+A5.Second*100+A5.Milisecond END attempt5
 from Command C 
 left outer join `Attempt` A1 on C.ID=A1.Command and A1.Attempt=1
 left outer join `Attempt` A2 on C.ID=A2.Command and A2.Attempt=2
 left outer join `Attempt` A3 on C.ID=A3.Command and A3.Attempt=3
 left outer join `Attempt` A4 on C.ID=A4.Command and A4.Attempt=4
 left outer join `Attempt` A5 on C.ID=A5.Command and A5.Attempt=5
 left outer join `Attempt` best on C.ID=best.Command and best.Special='Best'
 left outer join `Attempt` average on C.ID=average.Command and average.Special in('Average','Mean')
 join (SELECT count(*) competitors, Command FROM CommandCompetitor GROUP by Command) Competitor_count on Competitor_count.Command=C.ID
 join CommandCompetitor CC1 on CC1.Command=C.ID
 join Competitor Cr1 on CC1.Competitor = Cr1.ID
 left outer join CommandCompetitor CC2 on CC2.Command=C.ID AND CC2.ID>CC1.ID
 left outer join Competitor Cr2 on CC2.Competitor = Cr2.ID
 left outer join CommandCompetitor CC3 on CC3.Command=C.ID AND CC3.ID>CC2.ID
 left outer join Competitor Cr3 on CC3.Competitor = Cr3.ID
 left outer join CommandCompetitor CC4 on CC4.Command=C.ID AND CC4.ID>CC3.ID
 left outer join Competitor Cr4 on CC4.Competitor = Cr4.ID
 join Event E on C.Event=E.ID
 join Competition Cn on Cn.ID=E.Competition
 join DisciplineFormat DF on DF.ID=E.DisciplineFormat
 join Format F on DF.Format=F.ID
 join Discipline D on DF.Discipline=D.ID

 where
 (
 (Competitor_count.competitors=1 AND Cr1.ID is not null) or
 (Competitor_count.competitors=2 AND Cr2.ID is not null) or
 (Competitor_count.competitors=3 AND Cr3.ID is not null) or
 (Competitor_count.competitors=4 AND Cr4.ID is not null)
 )
 AND
 coalesce(Cr1.WCAID,'-')!='' AND
 coalesce(Cr2.WCAID,'-')!='' AND
 coalesce(Cr3.WCAID,'-')!='' AND
 coalesce(Cr4.WCAID,'-')!='' AND
 best.ID is not null
out,
                'initial_data');

db::exec("DELETE FROM results");
$results_count = 0;
$results_skip = 0;
foreach ($rows_results as $row) {
    $event_id = $events_map[$row->event_id] ?? false;
    $persons = [];
    foreach (range(1, 4) as $p) {
        if ($row->{"person$p"}) {
            $persons[] = $row->{"person$p"};
        }
    }
    sort($persons);
    $persons[] = "";
    $persons[] = "";
    $persons[] = "";
    $persons[] = "";

    if ($event_id) {
        db::exec(" INSERT INTO `results` 
                (`competition_id`,`event_id`,`round_number`,
                `person1`,`person2`,`person3`,`person4`,
                `best`,`average`,`attempt1`,`attempt2`,`attempt3`,`attempt4`,`attempt5`,
                `team_complete`,`is_publish`)
             VALUES ('$row->competition_id','$event_id','$row->round_number',
                    '$persons[0]','$persons[1]','$persons[2]','$persons[3]',
                    '$row->best','$row->average','$row->attempt1','$row->attempt2','$row->attempt3','$row->attempt4','$row->attempt5',
                    1,1)");
        $results_count++;
    } else {
        $results_skip++;
    }
}

db::exec("DELETE FROM results 
WHERE not exists (select * from rounds r2 
WHERE results.competition_id=r2.competition_id
AND results.event_id=r2.event_id
AND results.round_number=r2.round_number)");
$affected = db::affected();
$results_skip += $affected;
$results_count -= $affected;


competition::generate_card_number();
foreach (competition::get_list() as $competition) {
    foreach ($competition->rounds as $round) {
        round::update_pos($round);

        if (!$round->final) {
            $this_where = "WHERE competition_id = '$round->competition_id' 
                        AND event_id = '$round->event_id'
                        AND round_number = '$round->round_number'";
            db::exec(" UPDATE `results` SET next_round = false $this_where");

            $next_round_number = $round->round_number + 1;
            $next_where = "WHERE competition_id = '$round->competition_id' 
                        AND event_id = '$round->event_id'
                        AND round_number = '$next_round_number'";

            $rows = db::rows("SELECT person1, person2, person3, person4 FROM `results` $next_where");

            foreach ($rows as $row) {
                db::exec("UPDATE `results` set next_round = true $this_where 
                    AND person1 = '{$row->person1}' 
                    AND person2 = '{$row->person2}' 
                    AND person3 = '{$row->person3}' 
                    AND person4 = '{$row->person4}' ");
            }
        }
    }
}

results::update_rank();
results::update_records();
?>
<p>migration.results / - <?= $results_count ?>, skip <?= $results_skip ?></p>
