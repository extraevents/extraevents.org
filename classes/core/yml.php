<?php

use \Dallgoot\Yaml;

class yml {

    static function get($file) {
        return
                self::build(file_get_contents($file));
    }

    static function build($content) {
        return
                json_decode(
                json_encode(Yaml::parse($content)));
    }
    
}
