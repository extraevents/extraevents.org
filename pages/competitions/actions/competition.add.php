<?php

include 'competition.add.common.php';

$user = new wca_user(wcaoauth::get_user_id());

$competition->create($user);
form::process(true,
        ['wcaid' => $wcaid],
        'competition.add');
form::return("competitions/$wcaid");