<?php

class build_row {

    private $tr;
    private $data;

    public function __construct($data = []) {
        $this->tr = new stdClass();
        $this->data = $data;
    }

    public function add_value($key, $value) {
        $this->tr->$key = $value;
    }

    public function get_value($key) {
        return $this->tr->$key ?? false;
    }

    function out($thead) {
        ob_start();
        $data = $this->data;
        $data_list = '';
        foreach ($data as $k => $v) {
            $data_list .= " data-$k='$v'";
        }

        echo <<<out
                <tr $data_list>
        out;

        foreach ($thead as $key => $tmp) {
            $value = $this->tr->$key ?? false;
            if (in_array($key, ['attempt_1', 'attempt_2', 'attempt_3', 'attempt_4', 'attempt_5'])) {
                echo "<td data-tr-$key class='attempt attempt_not_except'>$value</td>";
            } elseif (in_array($key, ['result', 'single', 'average','count'])) {
                echo "<td data-tr-$key class='attempt grid_bold'>$value</td>";
            } elseif (strpos($key, 'rank') !== false) {
                echo "<td data-tr-$key class='attempt'>$value</td>";
            } else {
                echo "<td data-tr-$key>$value</td>";
            }
        }

        echo <<<out
                </tr>
        out;

        return ob_get_clean();
    }

}
