<?php

require_once( 'vendor/fpdf182/fpdf.php' );

class pdf_xy extends FPDF {

    private $cursor_x;
    private $cursor_y;
    private $cursor_base_x = null;
    private $cursor_base_y = null;
    private $name = false;
    private $names = null;

    function set_cursor($name, $cursor_x, $cursor_y, $name_pre = false) {
        $new_cursor_x = $cursor_x;
        $new_cursor_y = $cursor_y;
        $this->names['last'] = ['x' => $this->get_cursor_x(), 'y' => $this->get_cursor_y()];
        if (is_array($name_pre)) {
            $new_cursor_x += $this->names[$name_pre[0]]['x'];
            $new_cursor_y += $this->names[$name_pre[1]]['y'];
        } elseif ($name_pre) {
            $new_cursor_x += $this->names[$name_pre]['x'];
            $new_cursor_y += $this->names[$name_pre]['y'];
        }

        $this->names[$name] = [
            'x' => $new_cursor_x,
            'y' => $new_cursor_y,
            'x_save' => $new_cursor_x,
            'y_save' => $new_cursor_y];
        $this->set_current($name);
    }

    function set_current($name) {
        $this->name = $name;
        $this->cursor_x = $this->names[$name]['x_save'];
        $this->cursor_y = $this->names[$name]['y_save'];
    }

    function shift_cursor($delta_cursor_x, $delta_cursor_y) {
        $this->cursor_x += $delta_cursor_x;
        $this->cursor_y += $delta_cursor_y;
    }

    function get_name() {
        $name = $this->name;
        $this->names[$name]['x_save'] = $this->get_cursor_x();
        $this->names[$name]['y_save'] = $this->get_cursor_y();
        return
                $name;
    }

    function get_cursor_x() {
        return
                $this->cursor_x;
    }

    function get_cursor_y() {
        return
                $this->cursor_y;
    }

    function line_cursor($x0, $y0, $x1, $y1) {
        $x = $this->get_cursor_x();
        $y = $this->get_cursor_y();
        $this->Line($x0 + $x, $y0 + $y, $x1 + $x, $y1 + $y);
    }

    function text_cursor($text, $dx = 0, $dy = 0) {
        $x = $this->get_cursor_x();
        $y = $this->get_cursor_y();
        $this->Text($x + $dx, $y + $dy, $text);
    }

    function image_cursor($filename, $dx, $dy, $w, $h) {
        $x = $this->get_cursor_x();
        $y = $this->get_cursor_y();

        $size = getimagesize($filename);
        $k = min($w / $size[0], $h / $size[1]);
        $img_dx = ($w - $k * $size[0]) / 2;
        $img_dy = ($h - $k * $size[1]) / 2;

        $this->Image($filename, $x + $dx + $img_dx, $y + $dy + $img_dy, $k * $size[0], $k * $size[1]);
    }

    function rect_cursor($w, $h, $dx = 0, $dy = 0, $style = '') {
        $x = $this->get_cursor_x();
        $y = $this->get_cursor_y();
        $this->Rect($x + $dx, $y + $dy, $w, $h, $style);
    }

    function line_center($dx) {
        $y = $this->get_cursor_y();
        $this->Line($dx, $y, $this->w - $dx, $y);
    }

    function rect_center($dx, $h, $style) {
        $y = $this->get_cursor_y();
        $this->Rect($dx, $y, $this->w - $dx * 2, $h, $style);
    }

}
