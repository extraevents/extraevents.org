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
        if ($id) {
            $competition = db::row("SELECT
                                c.id,
                                c.city,
                                c.start_date,
                                c.end_date,
                                c.contact,
                                c.name,
                                countries.name country_name,
                                countries.iso2 country_iso2,
                                UNIX_TIMESTAMP(c.registration_close) registration_close,
                                c.registration_close > current_timestamp registration_open,
                                c.status
                                FROM competitions c 
                                LEFT OUTER JOIN countries on countries.id=c.country_id 
                                WHERE lower(c.id) = lower('$id')");
            if ($competition) {
                $this->id = $competition->id;
                $organizers_rows = db::rows("select * from organizers where competition_id='$id'");

                foreach ($organizers_rows as $organizer) {
                    $this->organizers[] = $organizer->person;
                }
                $this->contact = $competition->contact;

                $this->registration_enable = boolval($competition->registration_open
                        and $competition->status == self::ANNOUNCED);
                $this->registration_close = date('Y-m-d\TH:i:s\Z', $competition->registration_close);
                $this->status = $competition->status;
                $this->rounds = self::get_rounds($id);

                $this->country = (object) [
                            'name' => $competition->country_name,
                            'iso2' => $competition->country_iso2
                ];

                $this->name = $competition->name;
                $this->city = $competition->city;
                $this->start_date = $competition->start_date;
                $this->end_date = $competition->end_date;
            }
        }
        self::$object = $this;
    }

    static function get_rounds($competition_id) {
        return db::rows("SELECT 
                    r.competition_id,
                    c.name competition_name,
                    e.id event_id,
                    e.name event_name,
                    e.icon_wca_revert,
                    r.round_number,
                    e.person_count,
                    e.scramble,
                    e.scramble_tnoodle_format,
                    rt.name round_format,
                    rt.id round_id,
                    f.name format_name,
                    f.id format_id,
                    f.sort_by format_sort_by,
                    f.sort_by_second format_sort_by_second,
                    f.solve_count format_solve_count,
                    f.extra_count format_extra_count,
                    f.cutoff_count format_cutoff_count,
                    r.time_limit,
                    r.time_limit_cumulative,
                    r.cutoff,
                    r.competitor_limit,
                    r.final
                    FROM rounds r
                    JOIN competitions c on c.id=r.competition_id
                    JOIN events e on e.id=r.event_id
                    JOIN round_types rt on rt.id = r.round_type
                    JOIN formats f on f.id = r.round_format
                    WHERE competition_id = '$competition_id'
                    ORDER BY e.name, r.round_number
        ");
    }

    function import($json) {

        $country_id = db::row("SELECT id FROM countries WHERE iso2 ='{$json->api_competition->country_iso2}'")->id ?? false;
        $registration_close = strtotime($json->registration_close);

        if (!$this->id) {
            db::exec("INSERT INTO competitions  (id, status) VALUES 
                ('$json->id', '" . self::DRAFT . "') ");
            $this->id = $json->id;
            $this->log_status(false, self::DRAFT);
        }

        db::exec("  UPDATE competitions 
                    SET 
                        country_id = '$country_id',
                        name = '{$json->api_competition->name}',
                        city = '{$json->api_competition->city}',
                        start_date = '{$json->api_competition->start_date}',
                        end_date = '{$json->api_competition->end_date}',
                        contact = '{$json->contact}',
                        registration_close = FROM_UNIXTIME($registration_close)
                    WHERE id = '{$json->id}'     
                ");

        db::exec("DELETE FROM organizers WHERE competition_id = '$json->id'");

        foreach ($json->organizers as $organizer) {
            db::exec("INSERT INTO organizers  (competition_id, person) VALUES 
                ('$json->id', '$organizer') ");
        }

        db::exec("DELETE FROM rounds WHERE competition_id = '$json->id'");

        foreach ($json->events as $event) {
            round::import($json->id, $event);
        }
        self::set_final($this->id);
        round::set_rounds_type();
        return true;
    }

    public static function set_final($competition_id = false) {
        if ($competition_id) {
            $where = "WHERE competition_id='$competition_id'";
        } else {
            $where = '';
        }
        $rows = db::rows("SELECT t2.competition_id, t2.event_id, t2.round_number
            FROM (SELECT competition_id,event_id, max(round_number) round_number FROM rounds 
                $where
                GROUP BY competition_id, event_id) t1 
            JOIN rounds t2 ON t2.competition_id = t1.competition_id
                AND t2.event_id = t1.event_id
                AND t2.round_number = t1.round_number");
        foreach ($rows as $row) {
            db::exec("UPDATE rounds SET final = 1 
                    WHERE competition_id = '$row->competition_id' 
                    AND event_id = '$row->event_id' 
                    AND round_number = '$row->round_number' ");
        }
    }

    private function select() {
        $id = $this->get_id();
        $this->competition = db::row("SELECT * FROM competitions WHERE lower(id) = lower('$id')");
    }

    public function delete() {
        if (in_array(self::DELETE, $this->actions())) {
            db::exec("DELETE  FROM competitions WHERE id = '$this->id'");
            db::exec("DELETE  FROM organizers WHERE competition_id = '$this->id'");
            db::exec("DELETE  FROM rounds WHERE competition_id = '$this->id'");
            $this->log_status($this->status, self::DELETE);
            return true;
        }
        return false;
    }

    public function set_status($status) {
        if (in_array($status, $this->actions())) {
            $this->log_status($this->status, $status);
            db::exec("  UPDATE competitions 
                        SET status ='$status'
                        WHERE id = '$this->id'");

            if ($status == self::COMPLETED) {
                db::exec(" UPDATE results 
                        SET is_publish = true
                        WHERE competition_id = '$this->id' 
                        AND best > 0
                        AND pos > 0 ");
            } else {
                db::exec("  UPDATE results 
                        SET is_publish = null
                        WHERE competition_id = '$this->id' ");
            }

            if ($status == self::COMPLETED or $this->status == self::COMPLETED) {
                results::update_rank();
                results::update_records();
            }
            return true;
        }
        return false;
    }

    function get_round($event_id, $round_number) {
        foreach ($this->rounds as $round) {
            if ($round->event_id == $event_id and $round->round_number == $round_number) {
                return $round;
            }
        }
        return false;
    }

    static function get_list_id() {
        $rows = db::rows("SELECT id FROM competitions");
        $list_id = [];
        foreach ($rows as $row) {
            $list_id[] = $row->id;
        }
        return $list_id;
    }

    static function get_list_announced() {
        $rows = db::rows("SELECT id 
                            FROM competitions 
                            WHERE status='" . SELF::ANNOUNCED . "'");
        $list = [];
        foreach ($rows as $row) {
            $list[$row->id] = new competition($row->id);
        }
        return $list;
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
                and $this->registration_enable;
    }

    function show_scrambles() {

        return
                in_array($this->status, [
            self::ANNOUNCED,
            self::RUNNING,
            self::RESULTS_APPROVAL
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
        $url = strtolower("%i/competitions/$id$ext");
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

    static function get_list() {
        $rows = db::rows("SELECT id FROM competitions");
        $list = [];
        foreach ($rows as $row) {
            $competition = new self($row->id);
            $list[] = $competition;
        }
        return $list;
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

    private function log_status($status_old, $status_new) {
        $person = wcaoauth::wca_id();
        if (!$person) {
            $person = wcaoauth::user_id();
        }
        db::exec(" INSERT INTO `" . self::table_status() . "`"
                . " (`person`,`competition`,`status_old`,`status_new`) "
                . " VALUES ('$person','$this->id','$status_old','$status_new')",
                helper::db());
    }

    static function generate_card_number() {

        $max_card_rows = [];
        foreach (db::rows("SELECT 
                        max(card_id) card_id, competition_id, event_id, round_number
                        from `results`
                        WHERE card_id is not null
                        group by event_id, competition_id, round_number") as $row) {
            $max_card_rows[$row->competition_id][$row->event_id][$row->round_number] = $row->card_id;
        }

        foreach (db::rows("SELECT id, competition_id, event_id, round_number
                            FROM `results`
                            WHERE card_id is null
                            ORDER BY id") as $row) {
            $max_card_rows[$row->competition_id][$row->event_id][$row->round_number] ??= 0;
            $max_card_rows[$row->competition_id][$row->event_id][$row->round_number]++;
            $card_id = $max_card_rows[$row->competition_id][$row->event_id][$row->round_number];
            db::exec("UPDATE `results`
                    SET card_id = $card_id
                    WHERE id = $row->id");
        }
    }

    static function api_list() {
        return
                self::get_list();
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

            $rounds[] = [
                'id' => $round->event_id,
                'round' => $round->round_number + 0,
                'format' => $round->format_id,
                'cutoff' => $round->cutoff / 100 + 0,
                'time_limit' => $round->time_limit / 100 + 0,
                'time_limit_cumulative' => boolval($round->time_limit_cumulative),
                'competitor_limit' => $round->competitor_limit + 0
            ];
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

    static function __recreater() {
        $table = self::table_status();
        db::exec(" DROP TABLE IF EXISTS `$table`",
                helper::db());
        db::exec(" CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `person` varchar(10),
                    `competition` varchar(50),
                    `status_old` varchar(50),
                    `status_new` varchar(50),
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function table_status() {
        return
                self::$config->table->status->name;
    }

}
