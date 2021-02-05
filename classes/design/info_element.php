<?php

class info_element {

    private $name;
    private $value;

    public function __construct($name) {
        $this->name = $name;
    }

    function set_value($value) {
        $this->value = $value;
    }

    function out() {
        $value = $this->value;
        $name = $this->name;
        return <<<out
        <tr>
            <td>$name</td><td>$value</td>
        </tr>
        out;
    }

}
