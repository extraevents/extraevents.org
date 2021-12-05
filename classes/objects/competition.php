<?php

class competition {

    public $id;
    public $name;
    public $country;
    public $city;
    public $status;
    public $start_date;
    public $end_date;
    public $registration_close;
    public $registration_enable;
    public $organizers = [];
    public $contact;
    public $rounds = [];

    const DRAFT = 'draft';
    const ANNOUNCEMENT_APPROVAL = 'announcement_approval';
    const ANNOUNCED = 'announced';
    const RUNNING = 'running';
    const RESULTS_APPROVAL = 'results_approval';
    const COMPLETED = 'completed';
    const RESULTS_CANCELED = 'results_canceled';
    const DELETE = 'delete';

    private static $object = null;
    private static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        self::$object = new self(false);
    }

    function __construct($id) {
        if (!$id) {
            return;
        }
        $competition = sql_query::row('competition_by_id', ['id' => $id]);
        $organizers = sql_query::rows('competition_organizers', ['competition' => $id]);
        if ($competition) {
            $this->id = $competition->id;
            foreach ($organizers as $organizer) {
                $this->organizers[] = $organizer->person;
            }
            $this->status = $competition->status;
            $this->rounds = sql_query::rows('competition_rounds', ['competition' => $id]);
            $this->contact = $competition->contact;
            $this->registration_close = date('Y-m-d\TH:i:s\Z', $competition->registration_close);

            $this->country = (object) [
                        'name' => $competition->country_name,
                        'iso2' => $competition->country_iso2
            ];

            $this->name = $competition->name;
            $this->city = $competition->city;
            $this->end_date = $competition->end_date;
            $this->start_date = $competition->start_date;
        }
        self::$object = $this;
    }

    function set_settings($settings) {

        if (!$this->id) {
            $status = self::DRAFT;
            sql_query::exec('insert_competition', ['id' => $settings->id, 'status' => $status]);
            $this->id = $settings->id;
            $this->log_status(false, $status);
        }

        self::update_from_settings($settings);
        self::update_from_wca();
        return true;
    }

    function update_from_settings($settings) {
        self::update_organizers($settings->organizers);
        self::update_rounds($settings->events);
        $values = [];
        $values['id'] = $this->id;
        $values['contact'] = $settings->contact;
        $values['registration_close'] = strtotime($settings->registration_close);
        sql_query::exec('update_competition_settings', $values);
        return true;
    }

    function update_organizers($organizers) {

        sql_query::exec('delete_organizers_by_competition', ['competition' => $this->id]);
        foreach ($organizers as $person) {
            sql_query::exec('insert_organizer', ['competition' => $this->id, 'person' => $person]);
        }
        return true;
    }

    function update_rounds($events) {
        sql_query::exec('delete_rounds_by_competition', ['competition' => $this->id]);
        foreach ($events as $event) {
            round::import($this->id, $event);
        }
        sql_query::exec('update_rounds_final_by_competition', ['competition' => $this->id]);
        round::set_rounds_type();
        return true;
    }

    function update_from_wca() {
        $api_competition = wcaapi::get("competitions/$this->id");
        if (!$api_competition) {
            sql_query::exec('update_competition_draft', ['id' => $this->id]);
            return false;
        }
        $country_iso2 = $api_competition->country_iso2;
        $country = sql_query::row('country_by_iso2', ['iso2' => $country_iso2]);
        $values = [];
        $values['id'] = $this->id;
        $values['name'] = $api_competition->name;
        $values['city'] = $api_competition->city;
        $values['country'] = $country->id ?? false;
        $values['end_date'] = $api_competition->end_date;
        $values['start_date'] = $api_competition->start_date;
        sql_query::exec('update_competition_wca', $values);
        return true;
    }

    public function delete($description = false) {
        if (!in_array(self::DELETE, $this->actions())) {
            return false;
        }
        sql_query::exec('delete_competition', ['id' => $this->id]);
        sql_query::exec('delete_rounds_by_competition', ['competition' => $this->id]);
        sql_query::exec('delete_organizers_by_competition', ['competition' => $this->id]);
        sql_query::exec('update_results_notpublish_by_competition', ['competition' => $this->id]);

        $this->log_status($this->status, self::DELETE, $description);
        return true;
    }

    public function set_status($new_status, $description = false) {
        $old_status = $this->status;
        if (!in_array($new_status, $this->actions())) {
            return 'ERROR';
        }
        $update_from_wca = self::update_from_wca();
        $need_update_from_wca = self::need_update_from_wca($old_status, $new_status);
        if (!$update_from_wca and $need_update_from_wca) {
            return 'NOT_FOUND';
        }
        $description .= self::update_results($old_status, $new_status);
        sql_query::exec('update_competition_status', ['id' => $this->id, 'status' => $new_status]);

        $this->log_status($old_status, $new_status, $description);
        return 'OK';
    }

    private function need_update_from_wca($old_status, $new_status) {
        if ($old_status == self::DRAFT and $new_status == self::DRAFT) {
            return true;
        }
        if ($old_status == self::DRAFT and $new_status == self::ANNOUNCEMENT_APPROVAL) {
            return true;
        }
        return false;
    }

    private function update_results($old_status, $new_status) {
        $description = false;
        if ($new_status == self::COMPLETED) {
            sql_query::exec('update_results_publish_by_competition', ['competition' => $this->id]);
        } else {
            sql_query::exec('update_results_notpublish_by_competition', ['competition' => $this->id]);
        }

        if ($new_status == self::COMPLETED or $old_status == self::COMPLETED) {
            $description .= results::update_rank();
            results::update_records();
        }

        if ($new_status == self::RUNNING and $old_status == self::ANNOUNCED) {
            foreach ($this->rounds as $round) {
                if ($round->person_count > 1 and round::check_settings('autoteam', $round->settings)) {
                    $description .= round::autoteam($round);
                }
            }
        }

        if ($new_status == self::ANNOUNCED and $old_status == self::RUNNING) {
            foreach ($this->rounds as $round) {
                if ($round->person_count > 1 and round::check_settings('autoteam', $round->settings)) {
                    round::autoteam_rollback($round);
                }
            }
        }
        return $description;
    }

    function get_round($event_id, $round_number) {
        foreach ($this->rounds as $round) {
            if ($round->event_id == $event_id and $round->round_number == $round_number) {
                return $round;
            }
        }
        return false;
    }

    static function get_statuses() {
        return[
            self::DELETE,
            self::DRAFT,
            self::ANNOUNCEMENT_APPROVAL,
            self::ANNOUNCED,
            self::RUNNING,
            self::RESULTS_APPROVAL,
            self::COMPLETED,
            self::RESULTS_CANCELED
        ];
    }

    static function get() {
        return
                self::$object;
    }

    function url() {
        return
                '%i/competitions/' . $this->id;
    }

    function show_registrations() {

        return
                in_array($this->status, [
            self::ANNOUNCED,
            self::RUNNING]);
    }

    function show_register() {

        return
                in_array($this->status, [
                    self::ANNOUNCED
                ])
                and strtotime($this->registration_close) > strtotime('now');
    }

    function show_scrambles() {

        return
                in_array($this->status, [
            self::ANNOUNCED,
            self::RUNNING,
            self::RESULTS_APPROVAL,
            self::COMPLETED
        ]);
    }

    function enable_regenerate_scrambles() {

        return
                in_array($this->status, [
            self::ANNOUNCED,
            self::RUNNING
        ]);
    }

    function enable_enter_results() {

        return
                in_array($this->status, [
            self::RUNNING
        ]);
    }

    function show_scorecards() {

        return
                in_array($this->status, [
            self::ANNOUNCED,
            self::RUNNING]);
    }

    function show_results() {
        return
                in_array($this->status, [
            self::RUNNING,
            self::RESULTS_APPROVAL,
            self::COMPLETED,
            self::RESULTS_CANCELED]);
    }

    function show_settings() {
        return
                in_array($this->status, [
            self::DRAFT,
            self::ANNOUNCEMENT_APPROVAL]);
    }

    function line($additional_url = false) {
        $name = $this->name;
        $url = $this->url();
        $flag = region::flag($this->country->name, $this->country->iso2);

        return <<<out
            $flag <a href='$url$additional_url'>$name</a>
        out;
    }

    static function get_line($id, $name, $country_name, $country_iso2, $ext = false) {
        $url = "%i/competitions/$id$ext";
        $flag = region::flag($country_name, $country_iso2);

        return <<<out
            $flag <a href='$url'>$name</a>
        out;
    }

    function status_icon() {
        return
                self::get_status_icon($this->status);
    }

    static function status_line($status) {
        return
                self::get_status_icon($status) . ' ' . self::status_name($status);
    }

    static function get_status_icon($status) {
        $icons = [
            self::DELETE => 'fas fa-trash-alt',
            self::DRAFT => 'fas fa-question-circle',
            self::ANNOUNCEMENT_APPROVAL => 'far fa-pause-circle',
            self::ANNOUNCED => 'fas fa-door-open',
            self::RUNNING => 'fas fa-running',
            self::RESULTS_APPROVAL => 'fas fa-pause-circle',
            self::COMPLETED => 'fas fa-check-circle',
            self::RESULTS_CANCELED => 'fas fa-times-circle',
        ];
        return
                '<i title="' . self::status_name($status) . ' "class="' . $icons[$status] . '"></i>';
    }

    private static function status_name($status) {
        return
                t("competition.statuses.$status");
    }

    function actions() {
        $access_organizer = access::is_organizer();
        $access_leader = access::is_leader();
        $actions = [];

        if ($access_organizer) {
            $actions = array_merge(
                    $actions, $this->organizer_actions());
        }

        if ($access_leader) {
            $actions = array_merge(
                    $actions, $this->leader_actions());
        }
        return $actions;
    }

    private function organizer_actions() {

        $flow = [
            self::DRAFT => [
                self::DELETE,
                self::DRAFT,
                self::ANNOUNCEMENT_APPROVAL
            ],
            self::ANNOUNCEMENT_APPROVAL => [
                self::DRAFT
            ],
            self::ANNOUNCED => [
                self::RUNNING
            ],
            self::RUNNING => [
                self::RESULTS_APPROVAL
            ],
            self::RESULTS_APPROVAL => [
                self::RUNNING
            ]
        ];

        return
                $flow[$this->status] ?? [];
    }

    private function leader_actions() {
        $flow = [
            self::ANNOUNCEMENT_APPROVAL => [
                self::DRAFT,
                self::ANNOUNCED
            ],
            self::ANNOUNCED => [
                self::ANNOUNCEMENT_APPROVAL
            ],
            self::RUNNING => [
                self::ANNOUNCED
            ],
            self::RESULTS_APPROVAL => [
                self::COMPLETED,
                self::RESULTS_CANCELED
            ],
            self::COMPLETED => [
                self::RESULTS_APPROVAL
            ],
            self::RESULTS_CANCELED => [
                self::RESULTS_APPROVAL
            ]
        ];
        return
                $flow[$this->status] ?? [];
    }

    private function log_status($status_old, $status_new, $description = false) {
        $values = [];
        $values['person'] = wcaoauth::wca_id();
        $values['status_old'] = $status_old;
        $values['status_new'] = $status_new;
        $values['competition'] = $this->id;
        $values['description'] = $description;
        $values['table'] = self::table_status();
        sql_query::exec('log_competition_status', $values, helper::db());
    }

    static function generate_card_number() {

        $select_max_card = sql_query::rows('results_max_card');
        $max_card_rows = [];
        foreach ($select_max_card as $row) {
            $max_card_rows[$row->competition][$row->event][$row->round] = $row->card;
        }

        $select_null_card = sql_query::rows('results_null_card');
        foreach ($select_null_card as $row) {
            $card = ($max_card_rows[$row->competition][$row->event][$row->round] ?? 0) + 1;
            $max_card_rows[$row->competition][$row->event][$row->round] = $card;
            sql_query::exec('update_result_card', ['id' => $row->id, 'card' => $card]);
        }
    }

    static function api($id, $type) {

        $competition = new competition($id);
        if (!$competition->id) {
            return ['errors' => "Competition with id $id not found"];
        }
        switch ($type) {
            case 'registrations':
                return $competition->api_registrations();
            case 'results':
                return $competition->api_results();
            case null:
                return $competition->api_info();
            default: page_404();
        }
    }

    function api_registrations() {
        $api = [];
        foreach ($this->rounds as $round) {
            if ($round->round_number > 1) {
                continue;
            }
            foreach (results::get($round, ['team_complete'], 'event_id') as $r) {
                $persons = [
                    $r->person1,
                    $r->person2,
                    $r->person3,
                    $r->person4
                ];
                foreach ($persons as $p => $person) {
                    if ($person == '') {
                        unset($persons[$p]);
                    }
                }
                $persons_key = implode($persons);
                if (!isset($api[$persons_key])) {
                    $api[$persons_key] = [
                        'persons' => $persons
                    ];
                }
                $api[$persons_key]['event_ids'][] = $round->event_id;
            }
        }
        $registrations = [];
        foreach ($api as $registration_api) {
            $registrations[] = $registration_api;
        }
        return
                $registrations;
    }

    function api_results() {
        $results = [];
        foreach ($this->rounds as $round) {
            foreach (results::get($round, ['pos'], 'event_id, round_number, pos') as $r) {
                $persons = [
                    $r->person1,
                    $r->person2,
                    $r->person3,
                    $r->person4
                ];
                foreach ($persons as $p => $person) {
                    if ($person == '') {
                        unset($persons[$p]);
                    }
                }
                $results[] = [
                    'event_id' => $round->event_id,
                    'round_number' => $round->round_number + 0,
                    'round_type_id' => $round->round_id,
                    'format_id' => $round->format_id,
                    'pos' => $r->pos + 0,
                    'persons' => $persons,
                    'attempts' => [$r->attempt1 + 0, $r->attempt2 + 0, $r->attempt3 + 0, $r->attempt4 + 0, $r->attempt5 + 0],
                    'best' => $r->best + 0,
                    'average' => $r->average + 0,
                ];
            }
        }
        return
                $results;
    }

    function api_info() {
        $rounds = [];
        foreach ($this->rounds as $round) {
            $rounds[] = [
                'id' => $round->event_id,
                'round_number' => $round->round_number + 0,
                'round_type_id' => $round->round_id,
                'format' => $round->format_id,
                'cutoff' => $round->cutoff / 100 + 0,
                'time_limit' => $round->time_limit / 100 + 0,
                'time_limit_cumulative' => boolval($round->time_limit_cumulative),
                'competitor_limit' => $round->competitor_limit + 0
            ];
        }

        return
                [
                    'id' => $this->id,
                    'name' => $this->name,
                    'city' => $this->city,
                    'country_iso2' => $this->country->iso2,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'registration_close' => $this->registration_close,
                    'organizers' => $this->organizers,
                    'contact' => $this->contact,
                    'rounds' => $rounds
        ];
    }

    function settings() {
        $rounds = [];
        foreach ($this->rounds as $round) {

            $add_round = [
                'id' => $round->event_id,
                'round' => $round->round_number + 0,
                'format' => $round->format_id,
                'cutoff' => $round->cutoff / 100 + 0,
                'time_limit' => $round->time_limit / 100 + 0,
                'time_limit_cumulative' => boolval($round->time_limit_cumulative),
                'competitor_limit' => $round->competitor_limit + 0,
            ];
            if ($round->settings) {
                $add_round['settings'] = explode(";", $round->settings);
            }
            $rounds[] = $add_round;
        }

        return[
            'id' => $this->id,
            'organizers' => $this->organizers,
            'registration_close' => $this->registration_close,
            'contact' => $this->contact,
            'events' => $rounds
        ];
    }

    function get_date_range() {
        return
                self::date_range($this->start_date, $this->end_date);
    }

    static function date_range($start, $end = '') {
        if (!$end)
            $end = $start;
        if (sizeof(explode("-", $start)) != 3 or sizeof(explode("-", $end)) != 3) {
            return '-';
        }

        list($ys, $ms, $ds) = explode("-", $start);
        list($ye, $me, $de) = explode("-", $end);
        $Month_ms = t('date.month.' . $ms);
        $Month_me = t('date.month.' . $me);

        if ($ys != $ye) {
            return "$Month_ms $ds, $ys - $Month_me $de, $ye";
        } else {
            if ($ms != $me) {
                return "$Month_ms $ds - $Month_me $de, $ys";
            } else {
                if ($ds != $de) {
                    return "$Month_ms $ds - $de, $ys";
                } else {
                    return "$Month_ms $ds, $ys";
                }
            }
        }
        $ss = "{$Month[$ms]} $ds";
        return "$ss, $ys";
    }

    function get_country_line() {
        return
                self::country_line($this->country->name, $this->city);
    }

    static function country_line($country_name, $city) {
        return
                "<b>{$country_name}</b>, {$city}";
    }

    private static function table_status() {
        return
                self::$config->table->status->name;
    }

}
