<?php

class template {

    static $config;
    static $templates;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        self::$templates = yml::get(self::$config->yml);
    }

    static function t($key, $values) {
        if (is_object($values)) {
            $values = objectToArray($values);
        }
        if (!$key) {
            return false;
        }
        $template = clone self::$templates;
        foreach (explode('.', $key) as $k) {
            $template = $template->$k ?? FALSE;
        }
        if (is_object($template)) {
            $template = json_encode($template);
        }

        if ($template) {
            foreach ($values as $k => $v) {
                $template = str_replace('{%' . $k . '}', $v, $template);
            }
            return $template;
        } else {
            return '{%' . $key . '}';
        }
    }

}

function t($key, $values = []) {
    return
            template::t($key, $values);
}
