<?php

class centisecond {

    private $minute = false;
    private $second = false;
    private $centisecond = false;
    private $centisecond_in = null;

    public function __construct($centisecond_in) {
        $this->centisecond_in = $centisecond_in;
        $this->minute = floor($centisecond_in / 100 / 60);
        $this->second = floor(($centisecond_in - $this->minute * 100 * 60) / 100);
        $this->centisecond = $centisecond_in - $this->minute * 100 * 60 - $this->second * 100;
    }

    public function get_minute() {
        return
                $this->minute;
    }

    public function get_second() {
        return
                $this->second;
    }

    public function get_centisecond() {
        return
                $this->centisecond;
    }

    public function exists() {
        return
                $this->centisecond_in ?? false;
    }

    public static function out($centisecond_in, $full = false) {
        if (!$centisecond_in) {
            return
                    '';
        }

        if ($centisecond_in == -1) {
            return
                    'DNF';
        }
        if ($centisecond_in == -2) {
            return
                    'DNS';
        }

        $minute = floor($centisecond_in / 100 / 60);
        $second = floor(($centisecond_in - $minute * 100 * 60) / 100);
        $centisecond = $centisecond_in - $minute * 100 * 60 - $second * 100;

        $second_str = $second < 10 ? "0$second" : $second;
        $centisecond_str = $centisecond < 10 ? "0$centisecond" : $centisecond;

        if ($full or $minute) {
            return
                    "$minute:$second_str:$centisecond_str";
        } else {
            return
                    "$second:$centisecond_str";
        }
    }

}
