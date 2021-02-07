<?php

class log_clear {

    static function run() {
        $config_full = config::get_full();
        foreach ($config_full as $class => $config) {
            foreach ($config->table ?? [] as $table) {
                $clear = $table->clear ?? FALSE;
                if ($clear) {
                    get_class_methods($class);
                    cron::exec_task(
                            __CLASS__ . "::go",
                            $table,
                            $clear
                    );
                }
            }
        }
    }

    static function go($table) {
        return db::clearing_table(
                        $table->name,
                        $table->depth,
                        helper::db()
        );
    }

}
