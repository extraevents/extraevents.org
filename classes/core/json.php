<?php

class json {

    static function out($var) {
        if (!is_object($var) and!is_array($var)) {
            $obj = json_decode($var);
        } else {
            $obj = $var;
        }

        return
                json_encode($obj,
                JSON_PRETTY_PRINT +
                JSON_UNESCAPED_SLASHES +
                JSON_UNESCAPED_UNICODE
        );
    }

}
