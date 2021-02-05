<?php

$user = wcaoauth::authorize();
if ($user->wca_id ?? false) {
    person::update($user->wca_id, $user->name, false, $user->country_iso2);
}
form::return('');
