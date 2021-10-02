<?php
$round = page::get_object('round');
$event = new event($round->event_id);
$competition = competition::get();
$set_count = query::value('set_count');
if (!$set_count) {
    page_404();
}
$ee_option = (object) [
            'format' => $event->scramble_tnoodle_format,
            'competition_id' => $round->competition_id . '_' . $round->event_id . '_' . $round->round_number . '_s' . $set_count,
            'filename' => $filename,
            'set_count' => $set_count + 0,
            'solve_count' => $round->format_solve_count,
            'extra_count' => $round->format_extra_count,
            'need_to_cut' => $event->scrambling,
            'event_id' => $event->id,
            'event_name' => $event->name,
            'wca_events' => $event->scramble_tnoodle_events,
            'competition_name' => $competition->name,
            'round_number' => $round->round_number
];
$ee_option->total = $ee_option->set_count * ($ee_option->solve_count + $ee_option->extra_count);

$wca_options = [];

foreach ($ee_option->wca_events as $wca_event) {
    $wca_option = (object) [
                'wca_event' => $wca_event,
                'solve_count' => in_array($wca_event, ['666', '777']) ? 3 : 5,
                'extra_count' => in_array($wca_event, ['666', '777']) ? 1 : 2,
                'format' => in_array($wca_event, ['666', '777']) ? 'm' : 'a',
                'need' => $ee_option->total
    ];

    $wca_option->set_count = ceil(($ee_option->total) / ($wca_option->solve_count + $wca_option->extra_count));

    while ($wca_option->set_count > 26) {
        $need = $wca_option->need;
        $wca_option_add = clone $wca_option;
        if ($need) {
            $wca_option->need = false;
            $wca_option_add->need = $need;
        }
        $wca_option_add->set_count = 26;
        $wca_options[] = $wca_option_add;
        $wca_option->set_count -= 26;
    }
    $wca_options[] = $wca_option;
}

foreach ($wca_options as $id => $wca_option) {
    $wca_options[$id]->total = $wca_option->set_count * ($wca_option->solve_count + $wca_option->extra_count);
}
?>  
<?php
$scramble_info_ee = new build_table('Scambles for Extra Event');

$scramble_info_ee->add_head('event', 'Event');
$scramble_info_ee->add_head('round_number', 'Round');
$scramble_info_ee->add_head('set_count', 'Set count');
$scramble_info_ee->add_head('solve_count', 'Solves');
$scramble_info_ee->add_head('extra_count', 'Extra');
$scramble_info_ee->add_head('total', 'Total');

$scramble_info_ee_tr = new build_row();
$scramble_info_ee_tr->add_value('event', $event->line());
$scramble_info_ee_tr->add_value('round_number', $ee_option->round_number);
$scramble_info_ee_tr->add_value('set_count', $ee_option->set_count);
$scramble_info_ee_tr->add_value('solve_count', $ee_option->solve_count);
$scramble_info_ee_tr->add_value('extra_count', $ee_option->extra_count);
$scramble_info_ee_tr->add_value('total', $ee_option->total);
$scramble_info_ee->add_tr($scramble_info_ee_tr);

$scramble_info_wca = new build_table('Scambles in TNoodle WCA');
$scramble_info_wca->add_head('event', 'Event');
$scramble_info_wca->add_head('set_count', 'Set count');
$scramble_info_wca->add_head('solve_count', 'Solves');
$scramble_info_wca->add_head('extra_count', 'Extra');
$scramble_info_wca->add_head('need', 'Need');
$scramble_info_wca->add_head('total', 'Total');
foreach ($wca_options as $wca_option) {
    $scramble_info_wca_tr = new build_row();
    $scramble_info_wca_tr->add_value('event', event::get_image_wca($wca_option->wca_event));
    $scramble_info_wca_tr->add_value('set_count', $wca_option->set_count);
    $scramble_info_wca_tr->add_value('solve_count', $wca_option->solve_count);
    $scramble_info_wca_tr->add_value('extra_count', $wca_option->extra_count);
    $scramble_info_wca_tr->add_value('need', $wca_option->need);
    $scramble_info_wca_tr->add_value('total', $wca_option->total);
    $scramble_info_wca->add_tr($scramble_info_wca_tr);
}

$scramble_info = new info_double($scramble_info_ee->out(), $scramble_info_wca->out());
?>
<?= $scramble_info->out(); ?>

<?php
$data_tnoodle = [
    "wcif" => [
        "formatVersion" => "1.0",
        "name" => $ee_option->competition_id,
        "shortName" => $ee_option->event_id . '_set' . $ee_option->set_count,
        "id" => "",
        "events" => [],
        "persons" => [],
        "schedule" => [
            "venues" => [],
            "numberOfDays" => 0
        ]
    ]
];

foreach ($wca_options as $wca_option) {
    $data_tnoodle["wcif"]["events"][] = [
        "id" => $wca_option->wca_event,
        "rounds" => [
            [
                "format" => $wca_option->format,
                "id" => $wca_option->wca_event . "-r1",
                "scrambleSetCount" => $wca_option->set_count
            ]
        ]
    ];
}

$scramble_program = wcaapi::get('scramble-program');
$scramble_allowed = json_encode($scramble_program->allowed);

$instruction = $ee_option->format == 'json' ?
        "$ee_option->competition_id/Interchange/{$data_tnoodle['wcif']['shortName']}.json" :
        "$ee_option->competition_id/Printing/{$data_tnoodle['wcif']['shortName']} - All Scrambles.pdf";
?>
<h3>Step 1. Run <?= $scramble_program->current->name ?></h3>
<a data-external-link="<?= $scramble_program->current->information ?>">
    Detailed Instructions for TNoodle
</a>

<h3>Step 2. Download ZIP archive from TNoodle</h3>
<a target='_blank'href='http:<?= page::get_index() ?>/tnoodle_redirect.php/?data=<?= json_encode($data_tnoodle) ?>&filename=<?= $ee_option->competition_id ?>&allowed=<?= $scramble_allowed ?>'>
    Generate and download
</a>
<h3>Step 3. Unpack the downloaded archive</h3>
<h3>Step 4. Upload <?= $ee_option->format ?> file</h3>
[ <?= $instruction ?> ]

<form data-add-date enctype="multipart/form-data" data-action="<?= $ee_option->format ?>">           
    <input type="file" required accept="application/<?= $ee_option->format ?>" name="file" multiple="false"/>
    <button>Upload <?= $ee_option->format ?></button> 
    <input hidden value='<?= json_encode($ee_option) ?>' name='ee_option'>
    <input hidden value='<?= json_encode($wca_options) ?>' name='wca_options'>
    <input hidden value='<?= json_encode($data_tnoodle) ?>' name='data_tnoodle'>
</form>     