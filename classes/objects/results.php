<?php

class results {

    public $competition;
    public $event;
    public $round;
    protected $round_number;

    function __construct($round_id) {
        ee_round::init($round_id, $competition_id, $event_id, $round_number);
        $this->event_id = $event_id;
        $this->event = new event($event_id);
        $this->competition = new competition($competition_id);
        $this->competition = $competition_id;
        $this->round = $round_number;
    }

    function get_competition() {
        return
                $this->competition;
    }

    function get_event() {
        return
                $this->event;
    }

    function get_round_number() {
        return
                $this->round_number;
    }

    static function get($round, $flags = [], $order = '1') {

        $and_flags = '';
        foreach ($flags as $v => $k) {
            if ($v === 'person') {
                $and_flags .= "AND '$k' in (r.person1,r.person2,r.person3,r.person3,r.person4)";
            } elseif (is_numeric($v)) {
                $and_flags .= " AND coalesce(r.$k,0) > 0 ";
            } elseif (!$k) {
                $and_flags .= " AND coalesce(r.$v,0) = 0 ";
            } elseif ($k) {
                $and_flags .= " AND coalesce(r.$v,0) > 0 ";
            }
        }

        if ($round) {
            $where = " r.competition_id = '{$round->competition_id}'
                    AND r.event_id = '{$round->event_id}'
                    AND r.round_number = '{$round->round_number}' ";
        } else {
            $where = ' 1=1 ';
        }

        $list = db::rows("
                SELECT 
                    r.*,
                    cn.name competition_name,
                    cn.status competition_status,
                    cn.end_date competition_end_date,
                    rt.name round_format,
                    e.name event_name,
                    e.icon_wca_revert,
                    cn_cy.name competition_country_name,
                    cn_cy.iso2 competition_country_iso2,
                    p1.name person1_name,
                    p2.name person2_name,
                    p3.name person3_name,
                    p4.name person4_name,
                    p1.id person1_id,
                    p2.id person2_id,
                    p3.id person3_id,
                    p4.id person4_id,
                    cy1.name person1_country_name,
                    cy2.name person2_country_name,
                    cy3.name person3_country_name,
                    cy4.name person4_country_name,
                    cy1.iso2 person1_country_iso2,
                    cy2.iso2 person2_country_iso2,
                    cy3.iso2 person3_country_iso2,
                    cy4.iso2 person4_country_iso2
                FROM results r
                JOIN competitions cn ON cn.id = r.competition_id
                JOIN countries cn_cy on cn_cy.id = cn.country_id
                JOIN events e ON e.id = r.event_id
                JOIN rounds ON rounds.competition_id = r.competition_id
                    AND rounds.event_id = r.event_id
                    AND rounds.round_number = r.round_number
                JOIN round_types rt on rt.id = rounds.round_type
                LEFT OUTER JOIN persons p1 on p1.id=r.person1
                LEFT OUTER JOIN persons p2 on p2.id=r.person2
                LEFT OUTER JOIN persons p3 on p3.id=r.person3
                LEFT OUTER JOIN persons p4 on p4.id=r.person4
                LEFT OUTER JOIN countries cy1 on cy1.id=p1.country_id
                LEFT OUTER JOIN countries cy2 on cy2.id=p2.country_id
                LEFT OUTER JOIN countries cy3 on cy3.id=p3.country_id
                LEFT OUTER JOIN countries cy4 on cy4.id=p4.country_id
                WHERE $where
                $and_flags
                order by $order");

        return $list;
    }

    static function get_card_id($id) {
        return db::row("SELECT card_id FROM `results`WHERE id='$id'")->card_id ?? false;
    }

    static function update_rank() {

        $rows_before = sql_query::rows('ranks_top');

        db::exec(" DELETE FROM `ranks`");
        $events = db::rows("SELECT * FROM events");
        foreach ($events as $event) {
            foreach (['best' => 'single', 'average' => 'average'] as $k => $v) {
                $ranks = [];
                $countries_rank = [];
                $continents_rank = [];
                $rows = db::rows("SELECT 
                    id,
                    competition_id,
                    round_number,
                    person1, person2, person3, person4,
                    $k result,
                    attempt1, attempt2, attempt3, attempt4, attempt5 
                    FROM `results`
                    WHERE event_id = '$event->id'
                    AND is_publish = true
                    AND $k > 0
                    ORDER BY $k
                   ");

                $default = (object) [
                            'rank' => 0,
                            'rank_' => 1,
                            'value' => 0
                ];
                $word_rank = clone($default);
                foreach ($rows as $row) {
                    foreach ([$row->person1, $row->person2, $row->person3, $row->person4] as $person_id) {
                        if (!isset($ranks[$person_id]) and $person_id <> '') {
                            $person = db::row("SELECT cy.id country_id, ct.id continent_id FROM persons p JOIN countries cy on cy.id=p.country_id JOIN continents ct on cy.continent_id=ct.id WHERE p.id = '$person_id'");
                            if (!$person) {
                                continue;
                            }
                            $country_id = $person->country_id;
                            $continent_id = $person->continent_id;
                            $countries_rank[$country_id] ??= clone($default);
                            $country_rank = $countries_rank[$country_id];
                            $continents_rank[$continent_id] ??= clone($default);
                            $continent_rank = $continents_rank[$continent_id];

                            self::resolve_rank($word_rank, $row->result);
                            self::resolve_rank($country_rank, $row->result);
                            self::resolve_rank($continent_rank, $row->result);
                            $rank = clone($row);
                            $rank->world_rank = $word_rank->rank;
                            $rank->country_rank = $country_rank->rank;
                            $rank->continent_rank = $continent_rank->rank;
                            $rank->country_id = $country_id;
                            $rank->continent_id = $continent_id;
                            $ranks[$person_id] = $rank;
                        }
                    }
                }
                foreach ($ranks as $person_id => $rank) {
                    db::exec("INSERT INTO `ranks`
                        (person,competition_id,event_id,round_number,result_type,result,attempt1,attempt2,attempt3,attempt4,attempt5,world_rank,country_rank,country_id,continent_rank,continent_id)
                            VALUES
                        ('$person_id','$rank->competition_id','$event->id','$rank->round_number','$v', {$rank->result},'{$rank->attempt1}','{$rank->attempt2}','{$rank->attempt3}','{$rank->attempt4}','{$rank->attempt5}',{$rank->world_rank},{$rank->country_rank},'{$rank->country_id}',{$rank->continent_rank},'{$rank->continent_id}')");
                }
            }
        }

        $rows_after = sql_query::rows('ranks_top');

        return
                self::notification_record($rows_before, $rows_after);
    }

    private static function notification_record($rows_before, $rows_after) {
        $records_before = [];
        foreach ($rows_before as $row) {
            $records_before
                    [$row->event_id]
                    [$row->country_id]
                    [$row->result_type] = $row;
        }

        $notifications = [];
        foreach ($rows_after as $row_after) {
            $row_before = $records_before
                    [$row_after->event_id]
                    [$row_after->country_id]
                    [$row_after->result_type] ?? false;
            if (!$row_before or $row_before->result > $row_after->result) {
                if ($row_before) {
                    $result_before = centisecond::out($row_before->result);
                }
                $competition_after = new competition($row_after->competition_id);
                $person_after = new person($row_after->person);
                $result_after = centisecond::out($row_after->result);

                $event = new event($row_after->event_id);

                $notifications
                        [$competition_after->id]
                        [$event->name]
                        [] = (object) [
                            'after' => (object) [
                                'person' => $person_after->name,
                                'country' => $person_after->country_name,
                                'result' => $result_after],
                            'before' => $row_before ? ((object) [
                                'result' => $result_before]) : null,
                            'NR' => $row_after->country_rank == 1,
                            'CR' => $row_after->continent_rank == 1,
                            'WR' => $row_after->world_rank == 1,
                            'result_type' => $row_after->result_type
                ];
            }
        }

        $notification_out = false;
        if (sizeof($notifications) > 0) {
            foreach ($notifications as $competition => $notifications_2) {
                $notification_out .= "**$competition** records\n";
                foreach ($notifications_2 as $event_name => $notifications_3) {
                    foreach ($notifications_3 as $row) {
                        $before = $row->before ?
                                " [previous {$row->before->result}]" :
                                false;
                        $record_type = $row->WR ? 'WR' : ($row->CR ? 'CR' : ($row->NR ? 'NR' : ''));
                        $notification_out .= "[$record_type] **$event_name** $row->result_type:"
                                . " **{$row->after->result}** {$row->after->person} from {$row->after->country}$before\n";
                    }
                }
            }
        }
        if ($notification_out) {
            discort::send('records', $notification_out);
        }
        return
                str_replace("\n", '<br>', markdown::convertToHtml($notification_out));
    }

    private static function resolve_rank(&$object, $value) {
        if ($value > $object->value) {
            $object->rank += $object->rank_;
            $object->rank_ = 1;
            $object->value = $value;
        } else {
            $object->rank_++;
        }
    }

    static function update_records() {
        db::exec(" DELETE FROM `records`");
        foreach (['single', 'average'] as $result_type) {
            $world_rows = db::rows("SELECT 
                    person, competition_id, event_id, round_number, result, attempt1, attempt2, attempt3, attempt4, attempt5
                    FROM `ranks`
                    WHERE world_rank = 1 AND result_type = '$result_type'
                   ");

            foreach ($world_rows as $row) {
                db::exec("INSERT INTO `records`
                            (person,competition_id, event_id, round_number,
                            record_type,result_type,result, region,
                            attempt1, attempt2, attempt3, attempt4, attempt5)
                        VALUES
                            ('{$row->person}','{$row->competition_id}','{$row->event_id}','{$row->round_number}',
                            'world','$result_type',{$row->result},'world',
                            {$row->attempt1}, {$row->attempt2}, {$row->attempt3}, {$row->attempt4}, {$row->attempt5})
                        ");
            }

            $continent_rows = db::rows("SELECT 
                    person, competition_id, event_id, round_number, result, attempt1, attempt2, attempt3, attempt4, attempt5, continent_id
                    FROM `ranks`
                    WHERE continent_rank = 1 AND result_type = '$result_type'
                   ");

            foreach ($continent_rows as $row) {
                db::exec("INSERT INTO `records`
                            (person,competition_id, event_id, round_number,
                            record_type,result_type,result, region,
                            attempt1, attempt2, attempt3, attempt4, attempt5)
                        VALUES
                            ('{$row->person}','{$row->competition_id}','{$row->event_id}','{$row->round_number}',
                            'continent','$result_type',{$row->result},'{$row->continent_id}',
                            {$row->attempt1}, {$row->attempt2}, {$row->attempt3}, {$row->attempt4}, {$row->attempt5})
                        ");
            }

            $country_rows = db::rows("SELECT 
                    person, competition_id, event_id, round_number, result, attempt1, attempt2, attempt3, attempt4, attempt5, country_id
                    FROM `ranks`
                    WHERE country_rank = 1 AND result_type = '$result_type'
                   ");

            foreach ($country_rows as $row) {
                db::exec("INSERT INTO `records`
                            (person,competition_id, event_id, round_number,
                            record_type,result_type,result, region,
                            attempt1, attempt2, attempt3, attempt4, attempt5)
                        VALUES
                            ('{$row->person}','{$row->competition_id}','{$row->event_id}','{$row->round_number}',
                            'country','$result_type',{$row->result},'{$row->country_id}',
                            {$row->attempt1}, {$row->attempt2}, {$row->attempt3}, {$row->attempt4}, {$row->attempt5})
                        ");
            }
        }
    }

    static function get_records($args = []) {

        $where = '';
        foreach ($args as $key => $arg) {
            $where .= "AND $arg = true ";
        }

        $rows = db::rows("SELECT
                    `records`.*, `results`.result attempts, `results`.round_id
                    FROM `records` 
                    JOIN `results` ON `results`.id=`records`.result_id
                    WHERE 1=1 $where");

        foreach ($rows as $r => $row) {
            $rows[$r]->attempts = json_decode($row->attempts);
        }
        return $rows;
    }

    static function get_except($attempt1, $attempt2, $attempt3, $attempt4, $attempt5) {
        if ($attempt5 == 0) {
            return [];
        }
        $except = [];
        $attempts_pre = [];
        $attempts = [$attempt1, $attempt2, $attempt3, $attempt4, $attempt5];
        foreach ($attempts as $a => $attempt) {
            if ($attempt < 0) {
                $attempts_pre[] = PHP_INT_MAX;
            }
            if ($attempt > 0) {
                $attempts_pre[] = $attempt;
            }
        }

        $min = min($attempts_pre);
        $max = max($attempts_pre);
        foreach ($attempts_pre as $a => $attempt) {
            if ($attempt == $min) {
                $except[] = $a;
                $min = null;
            }
            if ($attempt == $max) {
                $except[] = $a;
                $max = null;
            }
        }
        return $except;
    }

    static function api_records() {
        $records = [];
        foreach (db::rows("SELECT * FROM records order by event_id") as $r) {

            if ($r->region == 'world') {
                $records[$r->record_type . '_records'][$r->event_id][$r->result_type] = $r->result + 0;
            } else {
                $records[$r->record_type . '_records'][$r->region][$r->event_id][$r->result_type] = $r->result + 0;
            }
        }
        return
                $records;
    }

}
