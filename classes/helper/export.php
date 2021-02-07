<?php

class export {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    public static function db() {
        return self::$config->db;
    }

    public static function build() {

        db::add_connection(self::db());

        self::build_competitions();
        self::build_events();
        self::build_ranks();
        self::build_results();
        return true;
    }

    private static function build_competitions() {
        db::exec("DELETE FROM `ee_competitions`",
                export::db());
        $complete = competition::COMPLETED;
        $rows = db::rows("SELECT * FROM competitions 
                WHERE status='$complete'
                ORDER BY id");

        $rows = db::rows("SELECT 
                    cn.id,
                    cn.name,
                    cn.city,
                    cy.name country,
                    cn.start_date,
                    cn.end_date,
                    cn.contact,
                    GROUP_CONCAT(distinct r.event_id ORDER BY r.event_id SEPARATOR ' ' ) extra_events,
                    GROUP_CONCAT(distinct o.person ORDER BY o.person SEPARATOR ', ' ) extra_events_organizers
                            FROM competitions cn
                            JOIN countries cy on cy.id=cn.country_id
                            JOIN rounds r on r.competition_id=cn.id
                            JOIN `organizers` o on o.competition_id=cn.id
                    group by cn.id, cn.name, cn.city, cy.name, cn.start_date, cn.end_date,cn.contact
                            ORDER BY cn.id");

        foreach ($rows as $r) {
            $r->name = db::escape($r->name);
            db::exec(" INSERT INTO `ee_competitions` 
               ( `id`,`name`,`city`,`country`,`start_date`,`end_date`,`extra_events`,`extra_events_organizers`,`extra_events_contact`) 
               values ( '$r->id','$r->name','$r->city','$r->country','$r->start_date','$r->end_date','$r->extra_events','$r->extra_events_organizers','$r->contact')",
                    export::db());
        }
    }

    private static function build_events() {
        db::exec("DELETE FROM `ee_events`",
                export::db());

        $event = db::rows("SELECT id, name, person_count FROM events ORDER BY id");

        foreach ($event as $event) {
            $id = $event->id;
            $name = $event->name;
            $person_count = $event->person_count;

            db::exec(" INSERT INTO `ee_events` 
               ( `id`,`name`,`person_count`) 
               values ( '$id','$name','$person_count')",
                    export::db());
        }
    }

    private static function build_ranks() {

        db::exec("DELETE FROM `ee_ranks_single`",
                export::db());

        db::exec("DELETE FROM `ee_ranks_average`",
                export::db());

        $rows = db::rows("SELECT * FROM ranks ORDER BY event_id,world_rank");
        foreach ($rows as $row) {
            if ($row->result_type == 'single') {
                db::exec(" INSERT INTO `ee_ranks_single` 
               ( `person`,`event`,`best`,`world_rank`,`continent_rank`,`country_rank`) 
               values ( '$row->person','$row->event_id','$row->result','$row->world_rank','$row->continent_rank','$row->country_rank')",
                        export::db());
            }
            if ($row->result_type == 'average') {
                db::exec(" INSERT INTO `ee_ranks_average` 
               ( `person`,`event`,`best`,`world_rank`,`continent_rank`,`country_rank`) 
               values ( '$row->person','$row->event_id','$row->result','$row->world_rank','$row->continent_rank','$row->country_rank')",
                        export::db());
            }
        }
    }

    private static function build_results() {

        db::exec("DELETE FROM `ee_results`",
                export::db());

        $rows = db::rows("SELECT 
                    r.competition_id,
                    r.event_id,
                    rounds.round_type,
                    r.pos,
                    r.best,
                    r.average,
                    r.person1,
                    r.person2,
                    r.person3,
                    r.person4,
                    rounds.round_format,
                    r.attempt1 value1,
                    r.attempt2 value2,
                    r.attempt3 value3,
                    r.attempt4 value4,
                    r.attempt5 value5
                    FROM results r 
                    JOIN rounds 
                        ON rounds.competition_id = r.competition_id
                        AND rounds.event_id = r.event_id
                        AND rounds.round_number = r.round_number
                    WHERE r.is_publish 
                    ORDER by r.competition_id, r.event_id, r.round_number, r.pos");

        foreach ($rows as $r) {
            db::exec(" INSERT INTO `ee_results` 
               ( `competition`,`event`,`round_type`,`format`,`pos`,`best`,`average`,
               `person1`,`person2`,`person3`,`person4`,
               `value1`,`value2`,`value3`,`value4`,`value5`)
               values ( '$r->competition_id','$r->event_id','$r->round_type','$r->round_format','$r->pos','$r->best','$r->average',
               '$r->person1','$r->person2','$r->person3','$r->person4',
               '$r->value1','$r->value2','$r->value3','$r->value4','$r->value5')",
                    export::db());
        }
    }

}
