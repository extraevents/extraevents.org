<?php

$list = sql_query::rows('team');

$table = new build_table(false);
$table->add_head('name', t('persons.name'));
$table->add_head('wcaid', t('persons.wca_id'));
$table->add_head('country', t('persons.country'));
$table->add_head('leader', false);
$table->add_head('contacts', false);
$table->add_head('description', t('team.description'));
$table->add_filter(['name', 'country', 'description']);
foreach ($list as $member) {
    $row = new build_row();
    $row->add_value('name', person::get_line($member->id, $member->name, $member->country_name, $member->country_iso2));
    $row->add_value('wcaid', $member->id);
    $row->add_value('country', $member->country_name);
    if ($member->is_leader) {
        $row->add_value('leader', t('team.leader'));
    }
    $row->add_value('description', $member->description);
    $build_contacts = [];
    foreach (json_decode($member->contacts) as $contact) {
        $build_contact = new build_contact($contact);
        $build_contacts[] = $build_contact->out();
    }
    $row->add_value('contacts', implode(' ', $build_contacts));
    $table->add_tr($row);
}

$data = (object) [
            'team' => $table->out()
];
