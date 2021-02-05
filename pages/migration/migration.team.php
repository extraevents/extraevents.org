<?php
$rows = db::rows("SELECT * FROM Delegate WHERE status !='Archive'",
                'initial_data');
$team = [];
foreach ($rows as $row) {
    $contacts = explode("\r\n", $row->Contact);
    $contacts_json = [];
    foreach ($contacts as $contact) {
        $contact = str_replace(['<', '>'], '', $contact);
        $type = '?';
        if (strpos($contact, '@') !== false) {
            $type = 'email';
        }
        if (strpos($contact, 'http') !== false) {
            $type = 'url';
        }
        if (strpos($contact, '+') === 0) {
            $type = 'phone';
        }
        if ($contact) {
            $contacts_json[] = ['type' => $type, 'value' => $contact];
        }
    }

    $team[] = (object) [
                'id' => strtoupper($row->WCA_ID),
                'is_leader' => ($row->Status == 'Senior') + 0,
                'contacts' => json_encode($contacts_json),
                'description' => $row->Status != 'Senior' ? $row->Status : '',
    ];
}

$member_count = 0;
foreach ($team as $member) {
    $row = db::row("SELECT id FROM team WHERE UPPER(id) = '$member->id'");
    if (!$row) {
        db::exec("INSERT INTO team
                    (id, is_leader, contacts, description)
                    VALUES ('$member->id', $member->is_leader, '$member->contacts', '$member->description')");
        $member_count++;
    } elseif ($member->id == '2015SOLO01') {
        db::exec("UPDATE team
                    SET contacts='$member->contacts',
                    description='Developer',
                    id = '$member->id'
                    WHERE UPPER(id) = '$member->id' ");
    }
}
?>

<p>migration.team / - <?= $member_count ?></p>