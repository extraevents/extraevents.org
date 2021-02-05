<?php
$event_map = [
    'fifteen' => 'fifteen',
    'ivy' => 'ivy',
    'mirror' => 'mirror',
    'redi' => 'redi',
    'kilo' => 'kilo',
    '2mguild' => '2mguild',
    'tbld' => 'teambld',
    'RainbowBall' => false,
    'Assembly' => false,
    'SportStacks' => false,
    '222oh' => '222oh',
    '10cubes' => '333ten',
    '333rescr' => '333rescr',
    'Prepared' => false,
    'cube2cm' => false,
    '3mguild' => '3mguild',
    '1mguild' => '1mguild',
    '888' => '888',
    '223' => '223',
    'dino' => 'dino',
    '333_scr' => '333scr',
    'pyra222' => '222pyra',
    '333bets' => '333bets',
    '999' => '999',
    '333uw' => false,
    '333bfoh' => '333bfoh',
    '333omt' => '333omt',
    'mirrorBLD' => 'mirrorbld',
    'pyra444' => '444pyra',
    '234relay' => '2to4relay',
    'clock_scr' => 'clockscr',
    '332' => '332',
    '333ft' => '333ft',
    '444ft' => '444ft',
    'fto' => 'fto',
    'curvyCopter' => 'curvyCopter',
    'sia113' => '113sia',
    '234567relay' => '2to7relay',
];

$scrambling_explode = [
    '222scr',
    '333scr',
    '444scr',
    '555scr',
    '666scr',
    '777scr',
    'skewbscr',
    'pyramscr',
    'sq1scr',
    'clockscr',
    'minxscr'
];

foreach ($scrambling_explode as $event_id) {
    $event_map[$event_id] = $event_id;
}

$rows_competitions = db::rows("SELECT * FROM Competition",
                'initial_data');

db::exec("DELETE FROM organizers");
db::exec("DELETE FROM competitions");

$competitions_skip = [];
$competition_count = 0;
foreach ($rows_competitions as $competition) {
    $wca_competition = db::row("SELECT * FROM Competitions WHERE id='$competition->WCA'",
                    wca::db());
    if (!$wca_competition) {
        $competitions_skip[] = $competition->WCA;
        continue;
    }

    $rows_organizer = db::rows(" 
        SELECT
            C.WCA,D.WCA_ID
        FROM `CompetitionDelegate` CD
        JOIN Competition C on C.ID=CD.Competition
        JOIN Delegate D on D.ID=CD.Delegate
        WHERE D.WCA_ID<>2 AND C.WCA='{$competition->WCA}'
        ORDER BY D.ID",
                    'initial_data');
    $organizers = [];
    foreach ($rows_organizer as $organizer) {
        $organizers[] = strtoupper($organizer->WCA_ID);
    }

    if (!sizeof($organizers)) {
        $rows = db::rows(" 
            SELECT Competitor.WCAID wca_id
            FROM `CompetitionReport` CR
            JOIN Competition C on C.ID = CR.Competition
            JOIN Competitor ON Competitor.WID=CR.DelegateWCA  
            WHERE CR.DelegateWCA is not null
            AND C.WCA='{$competition->WCA}'",
                        'initial_data');
        foreach ($rows as $row) {
            $organizers[] = $row->wca_id;
        }
    }

    $start_date = date('Y-m-d', mktime(0, 0, 0, $wca_competition->month, $wca_competition->day, $wca_competition->year));
    $end_date = date('Y-m-d', mktime(0, 0, 0, $wca_competition->endMonth, $wca_competition->endDay, $wca_competition->year));
    $registration_close = mktime(0, 0, 0, $wca_competition->month, $wca_competition->day - 1, $wca_competition->year);
    $name = db::escape($wca_competition->cellName);
    db::exec("INSERT INTO competitions 
                (id, country_id, name, city, start_date, end_date,  registration_close, status) 
                VALUES 
                ('$wca_competition->id','$wca_competition->countryId','$name','$wca_competition->cityName','$start_date','$end_date',FROM_UNIXTIME($registration_close), '" . competition::COMPLETED . "') ");
    $competition_count++;
    foreach ($organizers as $organizer) {
        db::exec("INSERT INTO organizers 
                (competition_id, person) 
                VALUES 
                ('$wca_competition->id','$organizer') ");
    }
}
?>
<p>migration.competitions / - <?= $competition_count ?></p>
<p>skip:<br><?= implode('<br> - ', $competitions_skip) ?></p>