<?php

class member {

    public $class = __CLASS__;
    public $id;
    public $is_leader;
    public $contacts;
    public $description;

    function __construct($id) {
        $row = db::row("SELECT id, is_leader, contacts, description
                FROM team 
                WHERE lower(id) = lower('$id')");
        if ($row) {
            $this->id = $row->id;
            $this->is_leader = boolval($row->is_leader);
            $this->contacts = json_decode($row->contacts);
            $this->description = $row->description;
        }
    }

    function create($member) {
        $leader = boolval($member->is_leader ?? false) + 0;
        $contacts = json_encode($member->contacts ?? []);
        $description = $member->description ?? false;
        db::exec("INSERT INTO team 
                (id, is_leader,contacts,description) 
                VALUES 
                ('$member->id',$leader,'$contacts','$description') ");
    }

    function update($member) {
        $leader = boolval($member->is_leader ?? false) + 0;
        $contacts = json_encode($member->contacts ?? []);
        $description = $member->description ?? false;

        db::exec("UPDATE team 
                SET is_leader = $leader,
                    contacts = '$contacts',
                    description = '$description'
                WHERE id = '$member->id'     
                ");
        $affected = db::affected();
        return $affected;
    }

    function delete() {
        db::exec("DELETE FROM team WHERE id = '$this->id'");
    }

    static function get_list() {
        $rows = db::rows("SELECT id FROM team");
        $list = [];
        foreach ($rows as $row) {
            $member = new self($row->id);
            $list[] = $member;
        }
        return $list;
    }

    static function api_list() {

        $api = [];
        foreach (db::rows("
                SELECT 
                    t.id, 
                    p.name,
                    cy.name country,
                    t.is_leader,
                    t.description, 
                    t.contacts
                FROM team t
                JOIN persons p on p.id=t.id
                JOIN countries cy on cy.id = p.country_id ") as $r) {
            $r->contacts = json_decode($r->contacts);
            $r->is_leader = boolval($r->is_leader);
            $api[] = $r;
        }
        return
                $api;
    }

}
