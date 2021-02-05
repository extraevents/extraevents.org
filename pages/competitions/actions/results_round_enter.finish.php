<?php

$round = page::get_object('round');
round::finish($round);
form::process(true, true, 'results.finish');
