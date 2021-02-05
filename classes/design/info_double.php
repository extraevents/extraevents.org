<?php

class info_double {

    private $left;
    private $right;

    public function __construct($left, $right) {
        $this->left = $left;
        $this->right = $right;
    }

    public function out($width_left = 50) {
        $left = $this->left;
        $right = $this->right;
        $width_right = 100 - $width_left;
        return <<<out
        <table class='table_double_info'>
            <tr>
                <td width=$width_left%>$left</td>
                <td width=$width_right%>$right</td>
            </tr>
        </table>
        
        out;
    }

}
