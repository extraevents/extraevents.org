<?php

class training {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    static function dir() {
        return
                $path = file::build_path(
                        [
                            file::dir(self::$config->dir->image->parent),
                            self::$config->dir->image->name,
                        ]
        );
    }

    static function filename($event_id) {
        $dir = self::dir();
        $session_id = session_id();
        return
                "$dir/{$session_id}_{$event_id}.png";
    }

}
