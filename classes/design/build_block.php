<?php

class build_block {

    private $elements = [];
    private $title = false;

    public function __construct($title = false) {
        $this->set_title($title);
    }

    public function set_title($title) {
        $this->title = $title;
    }

    function add_element($name, $value) {
        if (!$value) {
            return;
        }
        $element = new info_element($name);
        $element->set_value($value);
        $this->elements[] = $element;
    }

    function out() {
        ob_start();

        $title = $this->title;
        if ($title) {
            echo <<<out
            <h2>$title</h2>
            out;
        }

        echo <<<out
        <table class='table_info'>
        out;

        foreach ($this->elements as $element) {
            echo $element->out();
        }

        echo <<<out
        </table>
        out;

        return ob_get_clean();
    }

}
