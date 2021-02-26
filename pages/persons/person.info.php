<?php

$person = page::get_object('person');

$results = results::get(false, ['is_publish', 'person' => $person->id]);
$block = new build_block();
$block->add_element(t('persons.country'), $person->country_name);
$block->add_element(t('persons.wca_id'), "<a data-external-link='https://www.worldcubeassociation.org/persons/{$person->id}'>{$person->id}</a>");

$build_block_member = new build_block();
$member = new member($person->id);
if ($member->id) {
    $build_block_member->set_title(t('team.member'));
    $build_contacts = [];
    foreach ($member->contacts as $contact) {
        $build_contact = new build_contact($contact);
        $build_contacts[] = $build_contact->out();
    }

    $build_block_member->add_element(t('team.leader'), $member->is_leader ? ' ' : false);
    $build_block_member->add_element(t('team.contacts'), implode(' ', $build_contacts));
    $build_block_member->add_element(t('team.description'), $member->description);
}

$ranks = db::rows(str_replace('@person', $person->id, $sql));


$table_personal = new build_table(t('persons.records'));
$table_personal->add_head('event', t('results.event'));
$table_personal->add_head('single_country_rank', t('persons.country_rank'));
$table_personal->add_head('single_continent_rank', t('persons.continent_rank'));
$table_personal->add_head('single_world_rank', t('persons.world_rank'));
$table_personal->add_head('single', t('results.types.single'));
$table_personal->add_head('average', t('results.types.average'));
$table_personal->add_head('average_world_rank', t('persons.world_rank'));
$table_personal->add_head('average_continent_rank', t('persons.continent_rank'));
$table_personal->add_head('average_country_rank', t('persons.country_rank'));

foreach ($ranks as $r) {
    $row = new build_row();
    $row->add_value('event', event::get_image($r->event_id, $r->event_name, $r->icon_wca_revert) . ' ' .
            "<a href='%i/events/$r->event_id/rankings/single'>$r->event_name</a>");
    $row->add_value('single_country_rank', $r->single_country_rank);
    $row->add_value('single_continent_rank', $r->single_continent_rank);
    $row->add_value('single_world_rank', $r->single_world_rank);
    $row->add_value('single', centisecond::out($r->single_result));
    $row->add_value('average', centisecond::out($r->average_result));
    $row->add_value('average_world_rank', $r->average_world_rank);
    $row->add_value('average_continent_rank', $r->average_continent_rank);
    $row->add_value('average_country_rank', $r->average_country_rank);

    $table_personal->add_tr($row);
}
$table_personal->sort(['event_sort' => 'asc']);


$table_results = new build_table(t('persons.results'));
$table_results->add_head('event', t('results.event'));
$table_results->add_head('competition', t('results.competition'));
$table_results->add_head('round', t('results.round'));
$table_results->add_head('pos', t('persons.position'));
$table_results->add_head('single', t('results.types.single'));
$table_results->add_head('average', t('results.types.average'));
$table_results->add_head("attempt_1", false);
$table_results->add_head("attempt_2", false);
$table_results->add_head("attempt_3", false);
$table_results->add_head("attempt_4", false);
$table_results->add_head("attempt_5", false);

foreach ($results as $r) {
    $row = new build_row([
        'except' => json_encode(results::get_except(
                        $r->attempt1,
                        $r->attempt2,
                        $r->attempt3,
                        $r->attempt4,
                        $r->attempt5
        ))
    ]);
    $row->add_value('event', event::get_image($r->event_id, $r->event_name, $r->icon_wca_revert) . ' ' . $r->event_name);
    $row->add_value('event_sort', $r->event_name);
    $row->add_value('competition', competition::get_line($r->competition_id, $r->competition_name, $r->competition_country_name, $r->competition_country_iso2, "/{$r->event_id}/{$r->round_number}"));
    $row->add_value('competition_sort', $r->competition_end_date);
    $row->add_value('round_sort', $r->round_number);
    $row->add_value('round', str_replace('Combined ', '', $r->round_format));
    $row->add_value('pos', $r->pos);
    $row->add_value('single', centisecond::out($r->best));
    $row->add_value('average', centisecond::out($r->average));

    $row->add_value("attempt_1", centisecond::out($r->attempt1));
    $row->add_value("attempt_2", centisecond::out($r->attempt2));
    $row->add_value("attempt_3", centisecond::out($r->attempt3));
    $row->add_value("attempt_4", centisecond::out($r->attempt4));
    $row->add_value("attempt_5", centisecond::out($r->attempt5));

    $table_results->add_tr($row);
}

$table_results->sort([
    'event_sort' => 'asc',
    'competition_sort' => 'desc',
    'round_sort' => 'desc'
]);

$data = arrayToObject(
        [
            'title' => $person->line(),
            'block' => (new info_double($block->out(), $build_block_member->out()))->out(),
            'personal' => $table_personal->out(),
            'results' => $table_results->out(),
        ]
);
