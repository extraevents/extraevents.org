<?php

class request {

    static $path;

    static function __autoload() {

        $request_uri = strtolower(parse_url(
                        filter_input(INPUT_SERVER, 'REQUEST_URI')
                )['path']);
        $script_name = filter_input(INPUT_SERVER, 'SCRIPT_NAME');

        $script_name_prepared = "*".str_replace('/index.php', '', $script_name);

            $path = explode('/',
                    str_replace($script_name_prepared . "/", '', "*".$request_uri)
            );
        
        self::$path = $path;
    }

    static function get($n) {
        return
                self::$path[$n] ?? FALSE;
    }

    static function path() {
        return
                implode('/', self::$path);
    }

}
