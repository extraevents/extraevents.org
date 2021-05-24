<?php

class event {

    public $id = false;
    public $name;
    public $person_count;
    public $scramble_tnoodle_events;
    public $scramble_tnoodle_hook;
    public $scramble_tnoodle_format;
    public $long_inspection;
    public $scramble;
    public $scramble_training;
    public $drawing;
    public $comments;
    public $custom_wrap;
    public $scrambling;
    public $icon_wca_revert;

    public function __construct($id) {
        $row = db::row("SELECT
                id,
                name,
                person_count,
                scramble_tnoodle_events,
                scramble_tnoodle_hook,
                scramble_tnoodle_format,
                long_inspection,
                scramble,
                scramble_training,
                drawing,
                comments,
                custom_wrap,
                scrambling,
                icon_wca_revert
                FROM events WHERE id='$id'");
        if ($row) {
            $this->id = $row->id;
            $this->name = $row->name;
            $this->person_count = $row->person_count;
            $this->scramble_tnoodle_events = json_decode($row->scramble_tnoodle_events);
            $this->scramble_tnoodle_hook = $row->scramble_tnoodle_hook;
            $this->scramble_tnoodle_format = $row->scramble_tnoodle_format;
            $this->long_inspection = $row->long_inspection;
            $this->scramble = $row->scramble;
            $this->scramble_training = $row->scramble_training;
            $this->drawing = $row->drawing;
            $this->comments = json_decode($row->comments);
            $this->custom_wrap = $row->custom_wrap;
            $this->scrambling = $row->scrambling;
            $this->icon_wca_revert = $row->icon_wca_revert;
        }
    }

    static function get_yml() {
        $config = config::get(__CLASS__);
        return yml::get($config->yml);
    }
 
    public function image() {
        return self::get_image($this->id, $this->name, $this->icon_wca_revert);
    }

    static public function get_image($id, $name, $icon_wca_revert) {
        if ($icon_wca_revert) {
            return
                    "<i title='$name' class='cubing-icon event-$icon_wca_revert icon_revert'></i>";
        } else {
            return
                    "<i title='$name' class='fas ee-$id'></i>";
        }
    }

    static public function get_image_wca($id) {
        return
                "<i title='id:$id' class='cubing-icon event-$id'></i>";
    }

    public function line() {
        $image = $this->image();
        return
                "$image $this->name";
    }

    function line_rankings($type = 'single') {
        $image = $this->image();
        return
                "$image <a href='%i/events/$this->id/rankings/$type' >$this->name</a>";
    }

    public function thoodle_hook_function(&$rows) {
        $function = $this->scramble_tnoodle_hook;
        if (!$function) {
            return false;
        }
        if (!function_exists($function)) {
            user_error(__FUNCTION__ . ':' . $function);
        }
        $function($rows);
    }

    public function generate_scramble($training) {
        return
                ($training ? $this->scramble_training : $this->scramble)();
    }

    public function drawing_scramble($scramble, $filename, $training) {
        $image = ($this->drawing)($scramble, $training);
        if ($filename) {
            imagepng($image, $filename);
        }
        return $image;
    }

    static function filter($where = false) {
        $where = $where ? " AND $where " : '';
        ob_start();
        ?>
        <select data-navigation-event>
            <?php foreach (db::rows("
                    SELECT id, name 
                    FROM events 
                    WHERE 1 = 1 
                    $where 
                    ORDER BY name") as $event) { ?>
                <option value='<?= $event->id ?>'>
                    <?= $event->name ?>
                </option>
            <?php } ?>
        </select>
        <?php
        $content = ob_get_contents();
        ob_end_clean();
        return
                $content;
    }

}
