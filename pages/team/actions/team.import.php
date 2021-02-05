<?php

$dcc = new import_team();
foreach ($dcc->get_data() ?? [] as $member) {
    $member_id = $member->id;
    $obj_member = new member($member_id);
    if (!$obj_member->id) {
        $user = wcaapi::get("users/$member_id")->user ?? false;
        if (!$user) {
            $dcc->add_message(false, $member_id, 'WCA user not found.');
            continue;
        } else {
            person::update($member_id, $user->name, false, $user->country_iso2);
        }
    }

    if ($member->description == 'delete') {
        if ($member_id == wcaoauth::wca_id()) {
            $dcc->add_message(false, $member_id, "You can't delete yourself");
            continue;
        }
        if ($obj_member->id) {
            $obj_member->delete();
            $dcc->add_message(true, $member_id, 'deleted', $member);
        } else {
            $dcc->add_message(null, $member_id, 'already deleted');
        }
    } elseif (!$obj_member->id) {
        $obj_member->create($member);
        $dcc->add_message(true, $member_id, 'created', $member);
    } else {
        $affected = $obj_member->update($member);
        if ($affected) {
            $dcc->add_message(true, $member_id, 'updated', $member);
        } else {
            $dcc->add_message(null, $member_id, 'identical');
        }
    }
}
$dcc->out();
