<?php

class person {

    public $id = false;
    public $name;
    public $country_name;
    public $country_iso2;
    public $is_real = true;

    function __construct($id) {

        $person = sql_query::row('person_by_id', ['id' => $id]);

        if ($person) {
            $this->id = $person->id;
            $this->name = $person->name;
            $this->country_name = $person->country_name;
            $this->country_iso2 = $person->country_iso2;
            $this->is_real = substr($this->id, 0, 3) != 'EE_';
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
            $country = sql_query::row('country_by_iso2', ['iso2' => $country_iso2]);
            $country_id = $country->id ?? false;
        }

        sql_query::exec('person_actual', ['id' => $id, 'country' => $country_id, 'name' => $name]);
    }

    static function api($id) {
        $api = [];
        $api['person'] = new person($id);
        if (!$api['person']->id) {
            return ['errors' => "Person with id $id not found"];
        }
        $rows = sql_query::rows('rank_by_person_for_api', ['person' => $id]);
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
