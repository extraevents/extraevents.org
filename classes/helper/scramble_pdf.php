<?php

class scramble_pdf {

    const LETTER = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
    const Y_CONTENT_S = 33;
    const Y_COMMENT_0 = 10;
    const X_IMG_0 = 140;
    const X_IMG_1 = 205;
    const X_TEXT_0 = 20;
    const X_TEXT_1 = 135;
    const SCRAMBLE_FONT_SIZE = 12;

    protected static $config;
    protected $event;
    protected $competition_name;
    protected $scrambles;
    protected $scrambles_cut;
    protected $scrambles_format;
    protected $solve_count;
    protected $round;
    protected $date;
    protected $pdf;
    protected $training = false;
    protected $drawing_mode = false;
    protected $glue_mode = false;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    public function __construct($event, $competition_name, $solve_count, $round_number, $date, $training) {
        $this->date = $date;
        $this->event = $event;
        $this->round_number = $round_number;
        $this->solve_count = $solve_count;
        $this->competition_name = $competition_name;
        $this->training = $training;
        $this->scrambling = $event->scrambling;
    }

    public function set_scrambles($scrambles) {
        $this->scrambles = $scrambles;
        $this->drawing_mode = true;
        if ($this->event->custom_wrap) {
            $this->scrambles_custom_cut();
        } else {
            $this->scrambles_cut();
        }
    }

    public function set_image_scrambles($scrambles) {
        $this->scrambles = $scrambles;
        $this->glue_mode = true;
    }

    static function dir() {
        return
                $path = file::build_path(
                        [
                            file::dir(self::$config->dir->image->parent),
                            self::$config->dir->image->name
                        ]
        );
    }

    private function scrambles_cut() {
        $text_area = self::X_TEXT_1 - self::X_TEXT_0;
        $rows_scramble = 3;
        $font_size = self::SCRAMBLE_FONT_SIZE;
        $symbols_row = $text_area / $font_size / 0.22;
        foreach ($this->scrambles as $g => $set_count_scrambles) {
            foreach ($set_count_scrambles as $s => $scramble) {
                $blocks = explode(" ", trim($scramble));
                $row = "";
                foreach ($blocks as $b => $block) {
                    if (strlen("$row $block") < $symbols_row) {
                        $row .= " $block";
                    } else {
                        $this->scrambles_cut[$g][$s][] = trim($row);
                        $row = "$block";
                    }
                }
                if (trim($row)) {
                    $this->scrambles_cut[$g][$s][] = trim($row);
                }
                $rows_scramble = max([$rows_scramble, sizeof($this->scrambles_cut[$g][$s])]);
            }
        }

        $this->scrambles_format = (object) [
                    'rows_scramble' => $rows_scramble,
                    'font_size' => $font_size,
                    'symbols_row' => $symbols_row
        ];
    }

    private function scrambles_custom_cut() {
        $text_area = self::X_TEXT_1 - self::X_TEXT_0;
        $rows_scramble = 3;
        $symbols_row = 0;
        foreach ($this->scrambles as $g => $set_count_scrambles) {
            foreach ($set_count_scrambles as $s => $scramble) {
                $rows = explode(" & ", $scramble);
                $rows_scramble = max([$rows_scramble, sizeof($rows)]);
                foreach ($rows as $r => $row) {
                    $this->scrambles_cut[$g][$s][$r] = trim($row);
                    $symbols_row = max([$symbols_row, strlen(trim($row))]);
                }
            }
        }
        $font_size = min([self::SCRAMBLE_FONT_SIZE,
            $text_area / ($symbols_row * 0.22)]);

        $this->scrambles_format = (object) [
                    'rows_scramble' => $rows_scramble,
                    'font_size' => $font_size,
                    'symbols_row' => $symbols_row
        ];
    }

    private function font($type) {
        switch ($type) {
            case 'small':
                $this->pdf->SetFont('Arial', '', 8);
                break;
            case 'middle':
                $this->pdf->SetFont('Arial', '', 14);
                break;
            default:
                trigger_error(__FUNCTION__ . " $type");
        }
    }

    private function pdf_create() {
        $this->pdf = new pdf('P', 'mm');
        $this->pdf->SetLineWidth(0.2);
        $this->pdf->SetFillColor(230, 230, 230);
    }

    private function pdf_new_page($round_number, $set_count) {
        self::pdf_header($round_number, $set_count);
        self::pdf_footer();
        $this->pdf->set_cursor(self::Y_CONTENT_S);
    }

    private function pdf_header($round_number, $set_count) {
        $pdf = $this->pdf;
        $page = $this->get_page_count($round_number, $set_count);
        $event_name = $this->event->name;
        $competition_name = $this->competition_name;

        $pdf->AddPage();
        $this->font('middle');
        $pdf->set_cursor(13);
        $pdf->text_push(10, $event_name);
        $pdf->text_push(186, "Round " . $round_number);
        $pdf->shift_cursor(7);
        $pdf->text_push(10, 'Group ' . self::LETTER[$set_count]);
        $pdf->text_push(186, "Page $page");
        $pdf->shift_cursor(7);
        $pdf->text_push(10, $competition_name);
        $this->font('small');
        $pdf->set_cursor(self::Y_COMMENT_0);
        if ($this->event->comments) {
            foreach ($this->event->comments as $comment) {
                $pdf->text_push(100, $comment);
                $pdf->shift_cursor(5);
            }
        }
    }

    private function pdf_footer() {
        $pdf = $this->pdf;
        $page_no = $pdf->PageNo();
        $date = $this->date;
        $this->font('small');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->set_cursor(286);
        $pdf->text_push(10, "$date #$page_no");
    }

    private function get_page_count($round_number, $set_count) {
        $count = ($this->pages[$round_number][$set_count] ?? 0) + 1;
        $this->pages[$round_number][$set_count] = $count;
        return
                $count;
    }

    public function build() {
        $drawing_mode = $this->drawing_mode;
        $glue_mode = $this->glue_mode;
        if (!$drawing_mode and!$glue_mode) {
            trigger_error(__FUNCTION__ . ' not set mode', E_ERROR);
        }
        $this->pdf_create();
        $event = $this->event;
        $round_number = $this->round_number;
        $solve_count = $this->solve_count;
        $pdf = $this->pdf;

        foreach ($this->scrambles as $set_count => $set_count_scrambles) {
            $extra = false;
            $new_page = true;
            foreach ($set_count_scrambles as $solve_number => $scrambles) {
                if ($drawing_mode) {
                    $scrambles = [$scrambles];
                }
                $attemp_over = 0;
                $attemp_over_print = false;
                foreach ($scrambles as $scramble_seq => $scramble) {
                    if ($new_page) {
                        $this->pdf_new_page($round_number, $set_count);
                        $new_page = false;
                        $attemp_over++;
                        $attemp_over_print = $attemp_over > 1;
                    }
                    if ($drawing_mode or
                            ($glue_mode and $scramble_seq == 0 and sizeof($scrambles) > 1)) {
                        $pdf->line_center(10);
                    }
                    if ($solve_number == $solve_count + 1 and $scramble_seq == 0) {
                        if (!$this->scrambling) {
                            $this->font('small');
                            $pdf->shift_cursor(1);
                            if ($this->drawing_mode) {
                                $pdf->rect_center(10, 4, 'F');
                            }

                            $pdf->shift_cursor(3);
                            $pdf->text_push(90, 'Extra scrambles');
                            $pdf->shift_cursor(2);
                            if ($drawing_mode) {
                                $pdf->line_center(10);
                            }
                        }
                        $extra = true;
                    }
                    $solve_height = 33;
                    if ($drawing_mode) {
                        $rows = $this->scrambles_cut[$set_count][$solve_number];
                        $scramble_row = $this->scrambles_format->rows_scramble;
                        $scramble_size = $this->scrambles_format->font_size;
                        $this->pdf->SetFont('courier', '', $scramble_size);
                        $row_height = $scramble_size * 0.3 + 1;
                        $solve_height = max([$scramble_row * $row_height + 8, $solve_height]);

                        foreach ($rows as $r => $row) {
                            if ($r % 2 != 0) {
                                $pdf->rect_push(self::X_TEXT_0 - 3, $row_height * ($r + 1) - 1,
                                        self::X_TEXT_1 - self::X_TEXT_0 + 6, $row_height, 'F');
                            }
                            $pdf->text_push(self::X_TEXT_0, $row,
                                    $row_height * ($r + 1) + 5 - $scramble_size * .3 + 1);
                        }
                    }
                    if ($scramble_seq == 0 or $attemp_over_print) {
                        $this->font('middle');
                        $attemp_text = $extra ? ('E' . ($solve_number - $solve_count)) : $solve_number;
                        $pdf->text_push(10, $attemp_text, $solve_height / 2);
                        if ($attemp_over_print) {
                            $attemp_over_print = false;
                            $pdf->text_push(10, "/$attemp_over", $solve_height / 2 + 7);
                        }
                    }
                    if ($drawing_mode) {
                        $filename = self::dir() . "/" . session_id() . '_' . random_string(6) . '.png';
                        $event->drawing_scramble($scramble, $filename, $this->training);
                        $pdf->image_box($filename, self::X_IMG_0, 1,
                                self::X_IMG_1 - self::X_IMG_0, $solve_height - 2);
                        unlink($filename);
                    }
                    if ($glue_mode) {
                        $pdf->image_box($scramble, self::X_TEXT_0, 1,
                                self::X_IMG_1 - self::X_TEXT_0, $solve_height - 2);
                        if ($this->scrambling) {
                            $pdf->line_center(10);
                            $pdf->image_box('images/cut_solid.png', self::X_TEXT_0 - 10, -2.5, 5, 5);
                        }
                    }

                    $pdf->shift_cursor($solve_height);
                    if ($pdf->get_cursor() + $solve_height * sizeof($scrambles) > $this->pdf->h - 15
                            and $scramble_seq == array_key_last($scrambles)
                            and $set_count_scrambles[$solve_number + 1] ?? FALSE
                    ) {
                        $new_page = true;
                    }
                    if ($pdf->get_cursor() + $solve_height > $this->pdf->h - 15
                            and
                            ($set_count_scrambles[$solve_number + 1] ?? FALSE
                            or
                            $scrambles[$scramble_seq + 1] ?? FALSE
                            )
                    ) {
                        $new_page = true;
                    }
                }
            }
        }
        return $this->pdf;
    }

}
