<?php

include 'competition.add.common.php';

$args = form::required('organizer_id');

$person = new wca_user($args->organizer_id);
$person->reload();
$user_id = $person->get_id();
if (!$user_id) {
    form::process(false,
            ['wcaid' => $wcaid],
            'competition.organizer_not_found!');
    form::return();
}
$user = new wca_user($user_id);
$user->reload();
$competition->create($user);
form::process(true,
        ['wcaid' => $wcaid],
        'competition.add');
form::return("competitions/$wcaid");

