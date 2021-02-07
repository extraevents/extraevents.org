<?php

class cron {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        self::check_table_log();
    }

    public static function run() {
        $tasks = self::$config->task;
        foreach ($tasks as $task) {
            self::exec_task($task->command, $task->attributes ?? [], $task->run);
        }
    }

    public static function exec_task($task, $argument, $interval) {
        $task_exec = $task . json_encode($argument);

        $row = db::row(" SELECT id, task_begin, task_end, TIME_TO_SEC(TIMEDIFF(current_timestamp,task_end)) diff_sec
            FROM `" . self::table_log() . "` WHERE task_exec='$task_exec'  ORDER BY ID DESC LIMIT 1",
                        helper::db());

        if ($row and!$row->task_end) {
            return false;
        }

        if ($row and $row->diff_sec < $interval) {
            return false;
        }

        $taks_exec_id = self::log_task_begin($task_exec);
        $details = $task($argument);
        self::log_task_end($taks_exec_id, json_encode($details));
        return true;
    }

    private static function log_task_begin($task_exec) {
        db::exec(" INSERT INTO `" . self::table_log() . "` (`task_exec`) VALUES ('$task_exec')",
                helper::db());
        return db::id();
    }

    private static function log_task_end($id, $details) {
        $details_escape = db::escape($details);
        db::exec(" UPDATE `" . self::table_log() . "` SET details = '$details_escape' WHERE id = $id",
                helper::db());
    }

    public static function get_logs() {
        return db::rows("SELECT * FROM `" . self::table_log() . "` ORDER by `id` DESC",
                        helper::db());
    }

    static function clearing_table_error($attributes) {
        db::exec(" UPDATE `" . self::table_log() . "` "
                . " SET details = '" . json_encode(['error' => 'skip']) . "'"
                . " WHERE `task_begin` < (NOW() - INTERVAL " . $attributes->depth . " SECOND) AND `task_end` is null",
                helper::db());
        return db::affected();
    }

    private static function check_table_log() {
        $table = self::table_log();
        db::check_table($table,
                " CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `task_exec` varchar(255) DEFAULT NULL,
                    `task_begin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `task_end` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    `details` text,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function table_log() {
        return self::$config->table->log->name;
    }

}
