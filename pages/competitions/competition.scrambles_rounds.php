<?php

$competition = competition::get();
$events = [];

$table = new build_table(t('round.scrambles.title'));
$table->add_head('image', false);
$table->add_head('name', t('round.event'));
$table->add_head('round', t('round.round'));
$table->add_head('generate', t('round.scrambles.generate'));
$table->add_head('generate_method', false);
$table->add_head('timestamp', t('round.scrambles.timestamp'));
$table->add_head('print', false);
$table->add_head('download', false);

foreach ($competition->rounds as $round) {
    $file_scramble= new file(round::file_scramble($round));
    
    $row = new build_row();
    $row->add_value('image', event::get_image($round->event_id, $round->event_name, $round->icon_wca_revert));
    $row->add_value('round', $round->round_format);
    $row->add_value('name', $round->event_name);
    if ($file_scramble->exists) {
        $print = "%i/competitions/{$round->competition_id}/scrambles/{$round->event_id}/{$round->round_number}/print";
        $download = "%i/competitions/{$round->competition_id}/scrambles/{$round->event_id}/{$round->round_number}/download";
        $row->add_value('timestamp', "<span data-utc-time='{$file_scramble->timestamp}'/>");
        $row->add_value('print',
                <<<out
                        <a title='print' target='_blank' href='$print'>
                            <i class='fas fa-print'></i> Print
                        </a>
                        out);
        $row->add_value('download',
                <<<out
                        <a title='download' href='$download'>
                            <i class='fas fa-download'></i> Download
                        </a>
                        out);
    }
    if ($round->scramble) {
        $row->add_value('generate',
                <<<out
                        <form data-action="generate" data-add-date>
                            <input hidden name='event' value='$round->event_id'/>
                            <input hidden name='round' value='$round->round_number'/>
                            <button name='set_count' data-value='2'></button>
                            <button name='set_count' data-value='3'></button>
                            <button name='set_count' data-value='4'></button>
                            <button name='set_count' data-value='5'></button>
                        </form>
                        out);
        $row->add_value('generate_method',
                '<i title="Generating with this website" class="fas fa-hammer"></i>');
    }
    if ($round->scramble_tnoodle_format) {
        $generate_wca = "%i/competitions/$round->competition_id/scrambles/$round->event_id/$round->round_number/generate";
        $row->add_value('generate',
                <<<out
                        <button data-link='$generate_wca?set_count=2'>2</button>
                        <button data-link='$generate_wca?set_count=3'>3</button>
                        <button data-link='$generate_wca?set_count=4'>4</button>
                        <button data-link='$generate_wca?set_count=5'>5</button>
                        out);
        $row->add_value('generate_method',
                '<i title="Generating with TNoodle" class="fas fa-cubes"></i>');
    }


    $table->add_tr($row);
}

$out_scramble = $table->out();
$data = (object) [
            'out_scramble' => $out_scramble
];
