<?php

$args = form::required('wcaid');

$competition = new competition($args->wcaid);
if ($competition->exists()) {
    form::process(false,
            ['wcaid' => $args->wcaid],
            'competition.already_exists!');
    form::return("competitions/$args->wcaid");
}

$wca_competition = new wca_competition($args->wcaid);
$wca_competition->reload();

if (!$wca_competition->exists()) {
    form::process(false,
            ['wcaid' => $args->wcaid],
            'competition.not_found!');
    form::return();
}

$wcaid = $wca_competition->get_id();

if ($wca_competition->cancelled()) {
    form::process(false,
            ['wcaid' => $wcaid],
            'competition.canceled!');
    form::return();
}

if ($wca_competition->started()) {
    form::process(false,
            ['wcaid' => $wcaid],
            'competition.already_begin!');
    form::return();
}