<?php

class round {

    public $id;
    public $competition_id;
    public $competition_name;
    public $event_id;
    public $round_number;
    public $format;
    public $cutoff;
    public $final;
    public $time_limit;
    public $time_limit_cumulative;
    public $competitor_limit;
    public $settings;
    private static $config;
    private static $last_key;

    const AUTOTEAM = 'autoteam:';
    const NOT_PUBLISH = 'not_publish';

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    static function get_list_by_competition($competition) {
        $rows = db::rows("SELECT competition_id,event_id,round_number FROM rounds
                        WHERE competition_id = '$competition'");
        $list = [];
        foreach ($rows as $row) {
            $list[] = new self($row->competition_id, $row->event_id, $row->round_number);
        }
        return
                $list;
    }

    static function check_settings($key, $settings) {
        return in_array($key, explode(";", $settings));
    }

    static function set_result($round, $card_id, $result) {
        if ($result->best > 0) {
            $set_remove = ', remove = false';
        } else {
            $set_remove = '';
        }
        db::exec("  UPDATE `results`
                    SET 
                        attempt1 = $result->attempt1,
                        attempt2 = $result->attempt2,
                        attempt3 = $result->attempt3,
                        attempt4 = $result->attempt4,
                        attempt5 = $result->attempt5,
                        best = $result->best,
                        average = $result->average
                        $set_remove
                    WHERE competition_id = '$round->competition_id'
                    AND event_id = '$round->event_id'
                    AND round_number = '$round->round_number'    
                    AND card_id = '$card_id'");

        $affected = db::affected();
        if ($affected) {
            self::log_scoretaker($round, $card_id, $result);
        }
        return
                $affected;
    }

    static function init($round_id, &$competition_id, &$event_id, &$round_number) {
        list($competition_id, $event_id, $round_number) = explode('_', $round_id);
    }

    static function update_pos($round) {
        $this_where = "WHERE competition_id = '$round->competition_id' 
            AND event_id = '$round->event_id'
            AND round_number = '$round->round_number'";
        db::exec(" UPDATE `results` SET pos = null $this_where");

        $rows = db::rows(" SELECT 
                    id, best single, average
                    FROM `results`
                    $this_where 
                    AND best <> 0");

        array_walk($rows, function(&$row) {
            if ($row->single <= 0) {
                $row->single = PHP_INT_MAX;
            }
            if ($row->average <= 0) {
                $row->average = PHP_INT_MAX;
            }
        });
        usort($rows, function($a, $b) use ($round) {
            $sort_by = $round->format_sort_by;
            $sort_by_second = $round->format_sort_by_second;
            if ($a->$sort_by != $b->$sort_by) {
                return $a->$sort_by > $b->$sort_by;
            }
            return $a->$sort_by_second > $b->$sort_by_second;
        });

        $sort_by = $round->format_sort_by;
        $sort_by_second = $round->format_sort_by_second;

        $pos = 0;
        $pos_ = 1;
        $value = 0;
        $value_second = 0;
        foreach ($rows as $row) {
            if ($row->$sort_by > $value) {
                $pos += $pos_;
                $pos_ = 1;
            } elseif ($row->$sort_by_second > $value_second) {
                $pos += $pos_;
                $pos_ = 1;
            } else {
                $pos_++;
            }
            $value = $row->$sort_by;
            $value_second = $row->$sort_by_second;

            db::exec(" UPDATE `results`
                    SET pos = '$pos'
                    WHERE id = '$row->id'");
        }
    }

    static function remove($round, $card_id, $remove) {

        db::exec("  UPDATE `results`
                    SET remove = $remove
                    WHERE competition_id = '$round->competition_id'
                        AND event_id = '$round->event_id'
                        AND round_number = '$round->round_number'    
                        AND card_id = '$card_id'
                        AND best=0
                    ");
        $affected = db::affected();

        return
                $affected ?? false;
    }

    static function finish($round) {


        $this_where = "WHERE competition_id = '$round->competition_id' 
                        AND event_id = '$round->event_id'
                        AND round_number = '$round->round_number'";

        $next_round_number = $round->round_number + 1;
        $next_where = "WHERE competition_id = '$round->competition_id' 
                        AND event_id = '$round->event_id'
                        AND round_number = '$next_round_number'";

        db::exec(" UPDATE `results` SET remove = true $this_where AND best = 0 ");

        if ($round->final) {
            return;
        }

        db::exec(" DELETE FROM `results` $next_where AND best = 0 AND remove = false ");

        $count_this = db::row(" SELECT count(*) count FROM`results` $this_where AND best != 0 ")->count ?? 0;
        $count_next_remove = db::row(" SELECT count(*) count FROM`results` $next_where AND remove = true ")->count ?? 0;
        $competitor_limit = db::row(" SELECT competitor_limit FROM`rounds` $next_where ")->competitor_limit ?? 0;
        $count_to_next_round = min($count_this * 75 / 100, $competitor_limit) + $count_next_remove;

        db::exec(" UPDATE `results` SET next_round = false $this_where");

        $rows = db::rows(" SELECT group_concat(id) ids, count(*) count,  pos
                    FROM `results`
                        $this_where
                        AND best > 0
                    GROUP BY pos
                    ORDER BY pos
                ");
        $ids = [-1];
        $collect = 0;
        foreach ($rows as $row) {
            if ($collect + $row->count <= $count_to_next_round) {
                $ids[] = $row->ids;
                $collect += $row->count;
            } else {
                break;
            }
        }

        db::exec(" UPDATE `results` SET next_round = true WHERE id in (" . implode(',', $ids) . ")");

        if (!$round->final) {
            $rows = db::rows(" SELECT card_id, person1, person2, person3, person4 FROM `results` $this_where AND next_round = true");
            foreach ($rows as $row) {
                $next_row = db::row(" SELECT card_id, competition_id, event_id FROM `results` $next_where AND card_id = '$row->card_id' ");
                if (!$next_row) {
                    db::exec("INSERT INTO results
                    (competition_id, event_id, round_number, person1 ,person2 ,person3 ,person4 , card_id, team_complete) VALUES
                    ('$round->competition_id', '$round->event_id', '$next_round_number', '$row->person1','$row->person2','$row->person3','$row->person4',$row->card_id, 1) ");
                }
            }
        }

        db::exec(" UPDATE `results` SET next_round = false $this_where");
        $rows = db::rows("SELECT person1, person2, person3, person4 FROM `results` $next_where AND remove = false");

        foreach ($rows as $row) {
            db::exec("UPDATE `results` set next_round = true $this_where 
                AND person1 = '{$row->person1}' 
                AND person2 = '{$row->person2}' 
                AND person3 = '{$row->person3}' 
                AND person4 = '{$row->person4}' ");
        }


        return;
    }

    static function register($person, $round, $key = false, $not_publish = false) {
        $row = db::row("SELECT id,person1,person2,person3,person4 FROM results 
                WHERE competition_id='$round->competition_id'
                AND event_id ='$round->event_id' 
                AND round_number = 1
                AND '$person' in (person1,person2,person3,person4) ");
        if ($row) {
            return false;
        }
        if ($key) {
            $exec = self::register_join($person, $round, $key);
        } else {
            if ($not_publish) {
                $exec = self::register_create($person, $round, round::NOT_PUBLISH, $not_publish);
            } else {
                $exec = self::register_create($person, $round);
            }
        }
        if ($exec) {
            competition::generate_card_number();
        }
        return
                $exec;
    }

    static function autoteam($round) {

        $rows_incomplete = db::rows("SELECT person1, person2, person3, person4 FROM results 
                WHERE competition_id='$round->competition_id'
                AND event_id ='$round->event_id' 
                AND round_number = 1
                AND team_complete = 0 ");
        $persons_incomplete = [];
        $persons_complete = [];
        foreach ($rows_incomplete as $row) {
            foreach (range(1, 4) as $i) {
                if ($row->{"person{$i}"}) {
                    $persons_incomplete[] = $row->{"person{$i}"};
                }
            }
        }

        $rows_complete = db::rows("SELECT person1, person2, person3, person4 FROM results 
                WHERE competition_id='$round->competition_id'
                AND event_id ='$round->event_id' 
                AND round_number = 1
                AND team_complete = 1 ");

        foreach ($rows_complete as $row) {
            foreach (range(1, 4) as $i) {
                if ($row->{"person{$i}"}) {
                    $persons_complete[] = $row->{"person{$i}"};
                }
            }
        }

        $persons_incomplete = array_unique($persons_incomplete);
        $persons_incomplete = array_diff($persons_incomplete, $persons_complete);

        $persons_selected = [];
        foreach ($persons_incomplete as $person) {
            $register_order = self::get_log_register_id($person, $round);
            $persons_selected[] = ['id' => $person, 'order' => $register_order];
        }

        usort($persons_selected, function ($a, $b) {
            $a_r = $a['order'];
            $b_r = $b['order'];
            if (!$a_r) {
                return +1;
            }
            if (!$b_r) {
                return -1;
            }
            return $a_r > $b_r;
        });

        $person_count = $round->person_count;
        $free_places = ($round->competitor_limit - sizeof($rows_complete)) * $person_count;
        $excluded = [];
        foreach ($persons_selected as $key => $person) {
            if ($free_places <= 0) {
                $excluded[] = $person['id'];
                unset($persons_selected[$key]);
            }
            $free_places--;
        }
        shuffle($persons_selected);

        $teams = [];
        $team = [];
        foreach ($persons_selected as $person) {
            if (sizeof($team) < $person_count) {
                $team[] = $person['id'];
            }
            if (sizeof($team) == $person_count) {
                $teams[] = $team;
                $team = [];
            }
        }

        foreach ($teams as $team) {
            foreach ($team as $p => $person) {
                if ($p == 0) {
                    self::register_create($team[$p], $round, self::AUTOTEAM);
                } else {
                    self::register_join($team[$p], $round, self::$last_key, self::AUTOTEAM);
                }
            }
        }
        competition::generate_card_number();
        if (sizeof($excluded)) {
            return
                    "{{$round->event_id}.autoteam} Excluded: " . implode(", ", $excluded);
        }
    }

    static function autoteam_rollback($round) {
        $rows = db::rows("SELECT id,person1,person2,person3,person4 FROM results 
                WHERE competition_id='$round->competition_id'
                AND event_id ='$round->event_id' 
                AND round_number = 1
                AND autoteam = 1");
        foreach ($rows as $row) {
            db::exec("DELETE FROM results WHERE id = $row->id");
            $persons = [$row->person1, $row->person2, $row->person3, $row->person4];
            self::log_register($row->id, $round, self::AUTOTEAM . __FUNCTION__, ['before' => $persons, 'after' => null]);
        }
    }

    private static function register_create($person, $round, $option = false, $ext_option = false) {
        $complete = ($round->person_count == 1) + 0;
        $key = random_string(6);
        self::$last_key = $key;

        $exec = db::exec("INSERT INTO results
                    (competition_id, event_id, round_number, person1, team_complete, `key`) VALUES
                    ('$round->competition_id', '$round->event_id', '$round->round_number', '$person', $complete, '$key') ");
        $results_id = db::id();
        if ($exec) {
            self::log_register($results_id, $round, $option . __FUNCTION__, ['before' => null, 'after' => [$person]]);
        }

        if ($option == self::AUTOTEAM) {
            db::exec("UPDATE results set autoteam = 1 where id = $results_id");
        }
        if ($option == self::NOT_PUBLISH) {
            db::exec("UPDATE results set reason_not_publish = '$ext_option' where id = $results_id");
        }

        return
                $exec;
    }

    private static function register_join($person, $round, $key, $option = false) {

        $row = db::row("SELECT id, person1, person2, person3 ,person4
                FROM results
                WHERE competition_id='$round->competition_id'
                AND event_id ='$round->event_id' 
                AND round_number = 1
                and not team_complete
                and `key` = '$key' ");

        if (!$row) {
            return false;
        }
        $persons = [];
        foreach (range(1, 4
        ) as $i) {
            $person_i = $row->{"person{$i}"};
            if ($person_i != '') {
                $persons[] = $person_i;
            }
        }
        $persons_before = $persons;
        $persons[] = $person;
        sort($persons);
        if (sizeof(array_unique($persons)) != sizeof($persons)) {
            return false;
        }

        $team_complete = boolval($round->person_count == sizeof($persons));
        if (sizeof($persons) > $round->person_count) {
            return false;
        }

        $persons[1] ??= '';
        $persons[2] ??= '';
        $persons[3] ??= '';
        $exec = db::exec("UPDATE results
                            SET team_complete = $team_complete,
                            person1 = '$persons[0]',
                            person2 = '$persons[1]',
                            person3 = '$persons[2]',
                            person4 = '$persons[3]'
                            WHERE id = '$row->id'");
        if ($exec) {
            self::log_register($row->id, $round, $option . __FUNCTION__, ['before' => $persons_before, 'after' => $persons]);
        }
        return $exec;
    }

    static function unregister($person, $round, $prefix = false) {
        $row = db::row("SELECT id,person1,person2,person3,person4 FROM results 
                WHERE competition_id='$round->competition_id'
                AND event_id ='$round->event_id' 
                AND round_number = 1
                AND '$person' in (person1,person2,person3,person4) ");
        if (!$row) {
            return false;
        }
        $persons = [$row->person1, $row->person2, $row->person3, $row->person4, ''];
        $persons_before = $persons;

        $key = false;
        if (($key = array_search($person, $persons)) !== FALSE) {
            unset($persons[$key]);
            $persons = array_values($persons);
        }

        if ($persons[0] == '') {
            $exec = db::exec("DELETE FROM results WHERE id = $row->id");
        } else {

            $exec = db::exec("UPDATE results
                    SET team_complete = false, 
                        person1 = '$persons[0]',
                        person2 = '$persons[1]',
                        person3 = '$persons[2]',
                        person4 = '$persons[3]'
                    WHERE id = $row->id");
        }
        if ($exec) {
            self::log_register($row->id, $round, $prefix . __FUNCTION__, ['before' => $persons_before, 'after' => $persons]);
        }
        return $exec;
    }

    function link() {
        return
                "/results/" . $this->event_id . "/" . $this->round->number;
    }

    public static function set_rounds_type() {
        db::exec("update rounds r
            join `round_types`rt on 
            (rt.number = r.round_number or r.final = 1) 
		and rt.cutoff = (r.cutoff>0) and rt.final = r.final
            SET r.round_type=rt.id");
    }

    public static function import($competition_id, $round) {
        $round->cutoff ??= 0;
        $round->time_limit_cumulative ??= 0;
        $round->time_limit_cumulative += 0;
        $settings = implode(';', $round->settings ?? []);
        db::exec(" INSERT INTO `rounds` "
                . " (`competition_id`,`event_id`,`round_number`,`round_format`,`cutoff`,`time_limit`,`time_limit_cumulative`,`competitor_limit`,`settings`) "
                . " VALUES ('$competition_id','$round->id','$round->round','$round->format','$round->cutoff','$round->time_limit',$round->time_limit_cumulative,'$round->competitor_limit','$settings')");
    }

    static function get_log_register_id($person, $round) {
        return
                db::row("SELECT max(id) id FROM " . self::table_register() . " 
                                WHERE competition_id = '$round->competition_id'
                                    AND event_id = '$round->event_id'
                                    AND person = '$person'
                                    AND action not like '" . self::AUTOTEAM . "%'",
                        helper::db())->id ?? false;
    }

    static function log_register($id, $round, $action, $details = false) {
        $person = wcaoauth::wca_id();
        $details = json_encode($details);
        db::exec(" INSERT INTO `" . self::table_register() . "` "
                . " (`result_id`,`person`,`competition_id`,`event_id`,`action`,`details`) "
                . " VALUES ($id,'$person','$round->competition_id','$round->event_id','$action','$details')",
                helper::db());
    }

    static function log_scoretaker($round, $card_id, $details) {
        $person = wcaoauth::wca_id();
        $details = json_encode($details);
        db::exec(" INSERT INTO `" . self::table_scoretaker() . "` "
                . " (`competition_id`,`event_id`,`round_number`,`person`,`card_id`,`details`) "
                . " VALUES ('$round->competition_id','$round->event_id','$round->round_number','$person','$card_id','$details')",
                helper::db());
    }

    private static function table_register() {
        return
                self::$config->table->register->name;
    }

    private static function table_scoretaker() {
        return
                self::$config->table->scoretaker->name;
    }

    static function file_scramble($round) {
        return
                sprintf('%s/%s_%s_%s.pdf',
                file::build_path(
                        [
                            file::dir(self::$config->dir->scramble->parent),
                            self::$config->dir->scramble->name
                ]),
                $round->competition_id,
                $round->event_id,
                $round->round_number);
    }

}
