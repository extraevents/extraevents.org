<?php

wcaoauth::session_end();
form::process(true, wcaoauth::get_user(), 'competitor.logout');
form::return('');
