<?php

class grand {

    static $grand = false;

    static function __autoload() {
        
    }

    static function set_grand($dir) {
        self::$grand = self::static_set_grand($dir);
    }

    static function include_check_grand($file) {
        $check = self::resolve_access($file);
        if (!$check) {
            message::set('form.errors.access_page!');
            form::return('');
        }
        page::include($file);
    }

    static function action_check_grand($file) {
        $check = self::resolve_access($file);
        if (!$check) {
            form::process(
                    false,
                    ['grand' => $file],
                    'form.errors.access_action!');
            form::return('');
        }
        page::action($file);
    }

    static function resolve_access($file) {
        $grand = self::$grand->$file ?? false;
        $access = new access($grand);
        return
                $access->allowed;
    }

    static function static_set_grand($dir) {
        $file = "$dir/grand.yml";
        if (file_exists($file)) {
            return
                    yml::get($file);
        } else {
            return
                    false;
        }
    }

    static function resolve_access_global($dir, $file) {
        $grand_dir = self::static_set_grand("includes/$dir");
        $grand_file = $grand_dir->$file ?? false;
        return
                (new access($grand_file))->allowed;
    }

}
