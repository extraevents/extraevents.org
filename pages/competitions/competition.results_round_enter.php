<?php
$round = page::get_object('round');
$registrations = results::get($round, ['team_complete'], 'card_id');
$competition = competition::get();
$event = new event($round->event_id);
$details = [];
$details[] = $round->format_name;

if ($round->cutoff) {
    $details[] = t('round.cutoff') . ' <span id="cutoff">' . centisecond::out($round->cutoff, true) . '</span>';
}
if ($round->time_limit) {
    $details[] = t('round.time_limit') . ' <span id="time_limit" >' . centisecond::out($round->time_limit, true) .
            ($round->time_limit_cumulative ? (' ' . t('round.cumulative')) : '') . '</span>';
}
if (!$round->final) {
    $details[] = t('round.competitor_limit_next', ['level' => $round->competitor_limit]);
}


foreach ($registrations as &$r) {
    $r->names ??= [];
    foreach ([$r->person1_name, $r->person2_name, $r->person3_name, $r->person4_name] as $person) {
        if ($person) {
            $r->names[] = $person;
        }
        sort($r->names);
    }
}
unset($r);
?>
<h3>
    <?= $event->line() . ', ' . $round->round_format ?>
</h3>
<?php ob_start();
?>
<select class="chosen-select" data-placeholder="Select a competitor">
    <option value=""></option>
    <?php foreach ($registrations as $registration) { ?>
        <option value="<?= $registration->card_id ?>">
            <?= $registration->card_id ?> <?= implode(", ", $registration->names); ?>
        </option>
    <?php } ?>
</select>
<form data-action="results" class="result">
    <input hidden name='card_id'/>
    <?php for ($attempt = $round->format_solve_count; $attempt < 5; $attempt++) { ?>
            <input hidden data-enter-solve='<?= $attempt ?>' >
    <?php } ?>
    <?php for ($attempt = 0; $attempt < $round->format_solve_count; $attempt++) { ?>
        <p>
            <span class="attemp_number"><?= $attempt + 1 ?></span>
            <input disabled data-enter-solve='<?= $attempt ?>' >
        </p>
    <?php } ?>
    <?php if ($competition->show_results()) { ?>
        <button disabled>
            <?= t('results.save') ?>
        </button>
    <?php } else { ?>
        <span class='error'>
            <?= t('results.close'); ?>
        </span>
    <?php } ?>
    <a href='#' data-result-clear hidden>Clear</a>
</form>
<p>
    <?= implode('</p><p>', $details) ?>
</p>
<?php
$select_competitor = ob_get_clean();
$table = new build_table(false);
$table->add_head('card_id', t('results.card_id'));
$table->add_head('competitor', t('results.competitor'));
for ($attempt = 0; $attempt < $round->format_solve_count; $attempt++) {
    $table->add_head("solve_$attempt", $attempt + 1);
}

$sort_by = $round->format_sort_by;
$sort_by_second = $round->format_sort_by_second;
$table->add_head($sort_by, t('results.sort_by.' . $sort_by));
$table->add_head($sort_by_second, t('results.sort_by.' . $sort_by_second));
$table->add_head('position', t("results.position"));
$table->add_head('next', false);
$table->add_head('remove', false);
foreach ($registrations as $r) {
    $row = new build_row(
            [
        'card_id' => $r->card_id,
        'attempts' => json_encode([$r->attempt1, $r->attempt2, $r->attempt3, $r->attempt4, $r->attempt5]),
        'single' => $r->best,
        'average' => $r->average
            ]
    );
    $result_exists = $r->best != 0;
    $removed = $r->remove;

    $row->add_value('card_id', $r->card_id);

    $competitor_str = implode("<br>", $r->names);
    $row->add_value('competitor', ($removed and!$result_exists) ? ("<s>$competitor_str</s>") : $competitor_str );
    if (!$result_exists) {
        $row->add_value('sort_by_remove', $r->remove);
    }
    $row->add_value('sort_by_position', $r->pos ?? PHP_INT_MAX);
    $row->add_value('position', $r->pos);

    if (!$round->final and $r->next_round) {
        $row->add_value('next', '<i style="color:var(--green)" class="fas fa-chevron-right"></i></i>');
    }
    if ($round->final and $r->best > 0 and $r->pos <= 3) {
        $row->add_value('next', '<i style="color:var(--green)" class="fas fa-medal"></i>');
    }

    if ($removed) {
        $row->add_value('remove',
                '<form data-action="recover">'
                . '<button><i class="fas fa-undo-alt"></i></button>'
                . '<input hidden name="card_id"/>'
                . '</form>');
    } else if (!$result_exists) {
        $row->add_value('remove',
                '<form data-action="remove">'
                . '<button><i class="fas fa-trash-alt"></i></button>'
                . '<input hidden name="card_id"/>'
                . '</form>');
    }
    $table->add_tr($row);
}
$table->sort([
    "sort_by_position" => 'acs',
    "sort_by_remove" => 'acs',
    "competitor" => 'acs',
]);
$finish_text = t('results.finish');
$results_print_link = "competitions/$round->competition_id/results/$round->event_id/{$round->round_number}/print";
$buttons = <<<out
            <form data-action="finish">
                <button style='margin-right:40px'>$finish_text <i class="fas fa-hourglass-end"></i></button>
                <a target='_blank' href='%i/$results_print_link'>
                <i class='far fa-file-alt'></i> Print
                </a>
            </form>
            out;

$info_double = new info_double($select_competitor, $table->out() . $buttons);
echo $info_double->out(10);

$data = arrayToObject(
        [
            'data' => [
                'select_card_id' => message::get_custom('select_card_id'),
                'round' => json_encode($round)
            ]
        ]
);
