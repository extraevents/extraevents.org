<?php

require_once( 'vendor/fpdf182/fpdf.php' );

class pdf extends FPDF {

    private $cursor;

    function set_cursor($cursor) {
        if ($cursor < 0) {
            $cursor += $this->h;
        }
        $this->cursor = $cursor;
    }

    function shift_cursor($delta_cursor) {
        $this->cursor += $delta_cursor;
    }

    function get_cursor() {
        return
                $this->cursor;
    }

    function line_push($x0, $x1) {
        $y = $this->get_cursor();
        $this->Line($x0, $y, $x1, $y);
    }

    function line_center($x) {
        $y = $this->get_cursor();
        $this->Line($x, $y, $this->w - $x, $y);
    }

    function rect_center($x, $h, $style) {
        $y = $this->get_cursor();
        $this->Rect($x, $y, $this->w - $x * 2, $h, $style);
    }

    function text_push($x, $text, $delta_cursor = 0) {
        if ($x < 0) {
            $x += $this->w;
        }
        $this->Text($x, $this->cursor + $delta_cursor, $text);
    }

    function image_box($filename, $x, $y, $w, $h) {

        $size = getimagesize($filename);
        $k = min($w / $size[0], $h / $size[1]);
        $img_dx = ($w - $k * $size[0]) / 2;
        $img_dy = ($h - $k * $size[1]) / 2;

        $this->Image($filename,
                $x + $img_dx,
                $y + $img_dy + $this->cursor,
                $k * $size[0],
                $k * $size[1]);
    }

    function rect_push($x, $y, $w, $h, $style = '') {
        $this->Rect($x,
                $y + $this->cursor,
                $w,
                $h,
                $style);
    }

}
