<?php
include 'events_map.php';
$rows_rounds = db::rows("
            SELECT 
                CONCAT(C.WCA,'_',D.Code,'_',E.Round) id,
                C.WCA competition_id,
                D.Code event_id,
                E.Round round_number,
                F.FormatID format,
                (E.CutoffSecond+E.CutoffMinute*60)*100 cutoff,
                (E.LimitSecond+E.LimitMinute*60)*100 time_limit,
                E.Cumulative time_limit_cumulative,
                E.Competitors competitor_limit
                FROM Event E 
                JOIN `DisciplineFormat` DF on DF.`ID` = E.`DisciplineFormat`
                JOIN Format F on F.ID = DF.Format
                JOIN Discipline D on D.ID = DF.Discipline
                JOIN Competition C on C.ID = E.Competition
                ORDER BY  C.WCA, D.Code, E.Round",
                'initial_data');
$round_count = 0;
$rounds_skip = [];
db::exec("DELETE FROM rounds");

foreach ($rows_rounds as $round) {
    $event_id = $events_map[$round->event_id] ?? false;
    $competition = db::row("SELECT id FROM competitions WHERE id='$round->competition_id' ");

    if ($event_id and $competition) {
        db::exec(" INSERT INTO `rounds` "
                . " (`competition_id`,`event_id`,`round_number`,`round_format`,`cutoff`,`time_limit`,`time_limit_cumulative`,`competitor_limit`) "
                . " VALUES ('$round->competition_id','$event_id','$round->round_number','$round->format','$round->cutoff','$round->time_limit',$round->time_limit_cumulative,'$round->competitor_limit')");
        $round_count++;
    } else {
        $rounds_skip[] = $round->id;
    }
}
competition::set_final();
round::set_rounds_type();
?>
<p>migration.rounds / - <?= $round_count ?></p>
<p>skip:<br><?= implode('<br> - ', $rounds_skip) ?></p>