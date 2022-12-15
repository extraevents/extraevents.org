<?php

if (config::get()->freeze ?? false) {
    message::set("wcaoauth.freeze!");
    form::return('');
}

$user = wcaoauth::authorize();
if ($user) {
    if ($user->ee_id != $user->wca_id) {
        update_wcaid::update($user->ee_id, $user->wca_id);
    }
    person::update($user->wca_id, $user->name, false, $user->country_iso2);
}
form::return('');
