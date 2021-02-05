<?php

wcaoauth::session_end_all();
form::process(true, wcaoauth::get_user(), 'competitor.logout_all');
form::return('');

