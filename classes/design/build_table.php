<?php

class build_table {

    private $title = false;
    private $thead;
    private $tfoots = [];
    private $filters = [];
    private $thead_style;
    private $trs = [];

    public function __construct($title = false) {
        $this->title = $title;
        $this->thead = new stdClass();
        $this->thead_style = new stdClass();
    }

    public function add_head($key, $value, $style = false) {
        $this->thead->$key = $value;
        $this->thead_style->$key = $style;
    }

    public function add_filter($fields) {
        $this->filters[] = [
            'fields' => $fields
        ];
    }

    public function remove_head($key) {
        unset($this->thead->$key);
    }

    public function add_tr($row) {
        $this->trs[] = $row;
    }

    public function add_foot($row) {
        $this->tfoots[] = $row;
    }

    function sort($keys) {
        usort($this->trs, function($a, $b) use ($keys) {
            foreach ($keys as $key => $desc) {
                $a_key = $a->get_value($key);
                $b_key = $b->get_value($key);
                if ($a_key == $b_key) {
                    continue;
                }
                return $desc == 'desc' ? $a_key < $b_key : $a_key > $b_key;
            }
        });
    }

    function out() {
        $id = random_string(6);
        ob_start();
        foreach ($this->filters as $filter) {
            $filter_fields = json_encode($filter['fields']);
            $placeholders = [];
            foreach ($filter['fields'] as $field) {
                $name = $this->thead->$field ?? false;
                if ($name) {
                    $placeholders[] = $name;
                }
            }
            $placeholder = implode(' or ', $placeholders);
            echo <<<out
                <i class="fas fa-filter"></i>
                <input placeholder='$placeholder' autofocus autocomplete="false" data-table-id='$id' data-filter='$filter_fields'></input>
            out;
        }
        $title = $this->title;
        if ($title) {
            echo "<h2>$title</h2>";
        }
        echo "<table class='grid' data-table-id='$id'><thead><tr>";
        foreach ($this->thead as $key => $value) {
            $style = $this->thead_style->$key ?? false;
            echo "<td style='$style'>$value</td>";
        }
        echo "</tr></thead>";
        foreach ($this->trs as $row) {
            echo $row->out($this->thead);
        }

        echo "<tfoot>";
        foreach ($this->tfoots as $row) {
            echo $row->out($this->thead);
        }
        echo "</tfoot>";
        echo "</table>";

        return ob_get_clean();
    }

}
