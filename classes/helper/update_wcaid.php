<?php

class update_wcaid {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    public static function cron() {

        $persons_without_wcaid = sql_query::rows('persons_not_wcaid');
        $update = 0;
        foreach ($persons_without_wcaid as $person_without_wcaid) {
            $ee_id = $person_without_wcaid->id;
            $user_id = substr($ee_id, 3);
            $wca_id = wcaapi::get("users/$user_id")->user->wca_id ?? false;
            if ($wca_id) {
                wcaoauth::session_end_all($user_id);
                update_wcaid::update($ee_id, $wca_id);
                $update++;
            }
            sql_query::exec('log_update_wcaid',
                    [
                        'ee_id' => $ee_id,
                        'wca_id' => $wca_id,
                        'table' => self::table_log()
                    ], helper::db());
        }
        return ['all' => sizeof($persons_without_wcaid), 'update' => $update];
    }

    public static function update($ee_id, $wca_id) {
        sql_query::execs('update_wcaid',
                [
                    'ee_id' => $ee_id,
                    'wca_id' => $wca_id
        ]);
    }

    private static function table_log() {
        return
                self::$config->table->log->name;
    }

}
