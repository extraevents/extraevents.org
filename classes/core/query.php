<?php

class query {

    static function get() {
        $args = new stdClass();
        foreach (func_get_args() as $variable) {
            $args->$variable = filter_input(INPUT_GET, $variable);
        }
        return $args;
    }

    static function value($key) {
        return
                filter_input(INPUT_GET, $key);
    }

    static function value_int($key, $options = false) {
        return
                filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT,
                ['options' => $options]);
    }

    static function full() {
        return
                filter_input(INPUT_SERVER, 'QUERY_STRING');
    }

}
