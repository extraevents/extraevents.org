<?php

class message {

    CONST MESSAGE = 'message';

    public static function get() {
        return self::get_custom(self::MESSAGE);
    }

    public static function set($message) {
        self::set_custom(self::MESSAGE, $message);
    }

    public static function set_custom($name, $message) {
        $_SESSION[self::MESSAGE][$name] = $message;
    }

    public static function get_custom($name) {
        $message = $_SESSION[self::MESSAGE][$name] ?? FALSE;
        unset($_SESSION[self::MESSAGE][$name]);
        return $message;
    }

}
