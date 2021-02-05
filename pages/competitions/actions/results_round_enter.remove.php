<?php

$card_id = $args->card_id;
$round = page::get_object('round');
$affected = round::remove($round, $card_id, $remove);
if ($affected) {
    if (!$remove) {
        message::set_custom('select_card_id', $card_id);
    }
    form::process(true, true, 'results.remove');
} else {
    form::process(false, true, 'results.remove!');
}
