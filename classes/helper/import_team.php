<?php

class import_team {

    private $messages = [];
    private $data;
    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    function __construct() {
        if (!isset($_FILES['file']['tmp_name'])) {
            $this->add_message(false, false, 'File not found!');
            $this->out();
        }
        $data = file_get_contents($_FILES['file']['tmp_name']);
        $json = json_decode($data);
        $this->data = $json;
        if (!$json) {
            $this->add_message(false, false, "JSON parser error.");
            $this->out();
        }

        $validator = new json_schema();
        $validator->validate($json, json_decode(file_get_contents('import/team_schema.json')));
        if (!$validator->isValid()) {
            $errors = false;
            foreach ($validator->getErrors() as $error) {
                $errors .= sprintf("[%s] - %s<br>", $error['property'], $error['message']);
            }
            $this->add_message(false, false, "JSON does not validate.<br>$errors");
            $this->out();
        }
    }

    function get_data() {
        return $this->data ?? null;
    }

    function add_message($type, $member_id, $message, $data = false) {
        if ($type === true) {
            $this->messages[] = $member_id .
                    ' <i class="color_green fas fa-check"></i> ' .
                    $message;

            $person = wcaoauth::wca_id();
            db::exec(" INSERT INTO `" . self::table_log() . "`"
                    . " (`person`,`member`,`message`,`details`) "
                    . " VALUES ('$person','$member_id','$message','" . json_encode($data) . "')",
                    helper::db());
        } elseif ($type === false) {
            $this->messages[] = $member_id .
                    ' <i class="color_red fas fa-times"></i> ' .
                    $message;
        } else {
            $this->messages[] = $member_id . ' &bull; ' . $message;
        }
    }

    function out() {
        $out = '';
        foreach ($this->messages as $message) {
            $out .= "<p>$message</p>";
        }
        message::set_custom('team.import', $out);
        form::return();
    }

    static function __recreater() {
        $table = self::table_log();
        db::exec(" DROP TABLE IF EXISTS `$table`",
                helper::db());

        db::exec(" CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `person` varchar(10),
                    `member` varchar(10),
                    `message` text,
                    `details` text,
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function table_log() {
        return
                self::$config->table->log->name;
    }

}
