<?php

class person {

    public $id = false;
    public $name;
    public $country_name;
    public $country_iso2;

    function __construct($id) {
        $row = db::row("SELECT
                p.id,
                p.name,
                cy.name country_name,
                cy.iso2 country_iso2
                FROM `persons` p 
                JOIN `countries` cy ON cy.id=p.country_id
                WHERE LOWER(p.id) = LOWER('$id')");
        if ($row) {
            $this->id = $row->id;
            $this->name = $row->name;
            $this->country_name = $row->country_name;
            $this->country_iso2 = $row->country_iso2;
        }
    }

    function line() {
        return
                self:: get_line($this->id, $this->name, $this->country_name, $this->country_iso2);
    }

    static function get_line($id, $name, $country_name = false, $country_iso2 = false) {
        $url = "%i/persons/$id";
        if ($country_name) {
            $country_flag = region::flag($country_name, $country_iso2);
        } else {
            $country_flag = false;
        }
        return
                "$country_flag <a href = '$url'>{$name}</a>";
    }

    static function update($id, $name, $country_id, $country_iso2 = false) {

        $name = db::escape(trim(preg_replace('/\(.*?\)/', '', $name)));
        if ($country_iso2) {
            $country_id = db::row("SELECT * FROM countries WHERE iso2='$country_iso2'")->id ?? false;
        }

        db::exec("INSERT INTO persons (id,country_id,name)
                        VALUES ('$id','$country_id','$name') 
                        ON DUPLICATE KEY
                        UPDATE country_id='$country_id', name='$name'
                ");
    }

    static function api($id) {
        $api = [];
        $api['person'] = new person($id);
        if (!$api['person']->id) {
            return ['errors' => "Person with id $id not found"];
        }

        $rows = db::rows("
        SELECT 
            e.id event_id,
            r.result_type,
            r.result,
            r.world_rank,
            r.continent_rank,
            r.country_rank
        FROM events e
            JOIN ranks r ON r.event_id = e.id 
        WHERE r.person='$id'        
        order by e.id, r.result_type 
        ");

        $personal_records = [];
        foreach ($rows as $r) {
            $personal_records[$r->event_id][$r->result_type] = [
                'best' => $r->result + 0,
                'world_rank' => $r->world_rank + 0,
                'continent_rank' => $r->continent_rank + 0,
                'country_rank' => $r->country_rank + 0
            ];
        }
        $api['personal_records'] = $personal_records;
        return $api;
    }

}
