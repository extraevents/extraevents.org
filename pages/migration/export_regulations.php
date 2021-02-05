<?php
$rows = db::rows(
                "SELECT Language, Name, Text, D.Code,
                 D.Competitors, D.Simple, D.Inspection, D.Comment, D.ScrambleComment
                FROM `Regulation` R
                JOIN `Discipline` D on R.Event=D.ID
                WHERE text<>''
                ORDER BY 2,1",
        'initial_data');
$regulations = [];
foreach ($rows as $row) {
    $regulation = "\n---\n";
    $regulation .= "$row->Language\n";
    $regulation .= "$row->Text\n";
    if (!isset($regulations[$row->Code])) {
        $regulation_header = "$row->Name\n";
        $regulation_header .= "Competitors: $row->Competitors\n";
        if ($row->Simple) {
            $regulation_header .= "Simple\n";
        }
        $regulation_header .= "Inspection: $row->Inspection\n";
        if ($row->Comment and $row->Comment != '[]') {
            $regulation_header .= "Comment:\n";
            foreach (json_decode($row->Comment) as $key => $value) {
                $regulation_header .= "    $key - $value\n";
            }
        }
        if ($row->ScrambleComment and $row->ScrambleComment != '[]') {
            $regulation_header .= "ScrambleComment: \n$row->ScrambleComment\n";
        }
        $regulations[$row->Code] = $regulation_header;
    }
    $regulations[$row->Code] .= $regulation;
}

$count_regulations = 0;
$dir = file::build_path([$base_dir, 'regulations']);
foreach ($regulations as $code => $regulation) {
    $filename = "$dir/$code.txt";
    file_put_contents($filename, $regulation);
    $count_regulations++;
}
?>

<p><?= $dir ?>/ - <?= $count_regulations ?></p>