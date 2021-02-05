<?php
$rows = db::rows(
                "select 
                RC.Competitor,
                C.wcaid, C.name,
                RC.datetime, RC.status,
                RCF.Field, RCF.Value
                from `RequestCandidate` RC 
                join `RequestCandidateField` RCF on RCF.RequestCandidate=RC.ID
                join Competitor C on C.ID=RC.Competitor",
        'initial_data');

$rows2 = db::rows(
                "select 
        RC.Competitor, RCV.Status, RCV.Reason,D.WCA_ID
        from `RequestCandidate` RC 
        join `RequestCandidateVote` RCV on RCV.Competitor=RC.Competitor
        join Delegate D on D.ID=RCV.Delegate",
        'initial_data');
$statuses = [-1 => 'declined', 0 => 'unknown', 1 => 'accepted'];
$candidates = [];
$votes = [];
foreach ($rows2 as $row) {
    $votes[$row->Competitor] ??= [];
    $votes[$row->Competitor][] = $row;
}

foreach ($rows as $row) {
    $candidate = "---\n";
    $candidate .= "$row->Field:\n$row->Value\n";
    $code = "{$row->status}_$row->wcaid";
    if (!isset($candidates[$code])) {
        $candidate_header = "$row->name ($row->wcaid)\n";
        $candidate_header .= "Datetime: $row->datetime\n";
        $status = $statuses [$row->status];
        $candidate_header .= "Status: $status\n";
        foreach ($votes[$row->Competitor] ?? [] as $vote) {
            $status = $statuses [$vote->Status];
            $candidate_header .= "$vote->WCA_ID: $status - $vote->Reason\n";
        }

        $candidates[$code] = $candidate_header;
    }
    $candidates[$code] .= $candidate;
}

$count_candidates = 0;
$dir = file::build_path([$base_dir, 'candidates']);
foreach ($candidates as $code => $candidate) {
    $filename = "$dir/$code.txt";
    file_put_contents($filename, $candidate);
    $count_candidates++;
}
?>
<p><?= $dir ?>/ - <?= $count_candidates ?></p>