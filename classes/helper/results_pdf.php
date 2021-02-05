<?php

class results_pdf {

    private $pdf;
    private $round;
    private $results;

    public function __construct($round) {
        $this->round = $round;
        $this->results = results::get($round, ['remove' => false], 'coalesce(pos,9999)');
    }

    public function pdf() {
        $round = $this->round;
        $sort_by = $round->format_sort_by;
        $sort_by_second = $round->format_sort_by_second;

        $this->pdf = new pdf('L', 'mm');
        $pdf = $this->pdf;
        $pdf->SetLineWidth(0.1);
        $pdf->SetFillColor(230, 230, 230);

        foreach ($this->results as $r => $result) {
            if ($r % 23 == 0) {
                $this->pdf_header($round->round_number);
                $this->pdf_footer();
                $pdf->set_cursor(26);
            }
            if (($r + ceil($r / 22) - 1) % 2 != 1) {
                $pdf->rect_push(8, 2.3, $pdf->w - 16, 7, 'F');
            }

            $names = [];
            foreach (range(1, $round->person_count) as $p) {
                $names[] = $result->{"person{$p}_name"};
            }
            sort($names);
            $this->font('small');
            $pdf->shift_cursor(7);
            $pdf->text_push(10, $result->pos);

            $str = iconv('utf-8', 'cp1252//TRANSLIT', implode(', ', $names));
            $top = false;
            if ($this->round->competitor_limit->next ?? false) {
                if ($result->top) {
                    $top = true;
                }
            } else {
                if ($result->pos <= 3) {
                    $top = true;
                }
            }

            $this->font('small', $top ? 'B' : '');
            if ($top) {
                $pdf->text_push(18, '*');
            }
            $pdf->text_push(20, $str);
            $this->font('small', 'B');
            $pdf->text_push(-50, centisecond::out($result->{str_replace('single', 'best', $sort_by)} ?? false));
            $this->font('small');
            $pdf->text_push(-30, centisecond::out($result->{str_replace('single', 'best', $sort_by_second)} ?? false));

            $except = results::get_except($result->attempt1, $result->attempt2, $result->attempt3, $result->attempt4, $result->attempt5);
            foreach (range(1, $this->round->format_solve_count) as $a) {
                if (in_array($a, $except)) {
                    $pdf->text_push(-50 - 20 * ($this->round->format_solve_count - $a + 1), '(' . centisecond::out($result->{"attempt$a"}) . ')');
                } else {
                    $pdf->text_push(-50 - 20 * ($this->round->format_solve_count - $a + 1), ' ' . centisecond::out($result->{"attempt$a"}) . ' ');
                }
            }
        }
        return $pdf;
    }

    private function pdf_header() {
        $pdf = $this->pdf;
        $pdf->AddPage();
        $this->font('middle');
        $pdf->set_cursor(13);
        $pdf->text_push(10,
                $this->round->event_name . ', ' .
                $this->round->round_format);
        $this->font('small');
        $pdf->shift_cursor(13);
        $pdf->text_push(10, '#');
        $pdf->text_push(20, 'Competitor');
        $pdf->text_push(-50, ucfirst(str_replace('single', 'best', $this->round->format_sort_by)));
        $pdf->text_push(-30, ucfirst($this->round->format_sort_by_second));

        $pdf->text_push(-50 - 20 * ($this->round->format_solve_count), 'Solves');
    }

    private function pdf_footer() {
        $pdf = $this->pdf;
        $this->font('small', 'I');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->set_cursor(-8);
        $pdf->text_push(10,
                'page #' . $pdf->PageNo() .
                ' | ' . $this->round->competition_id .
                ' | ' . $this->round->event_id .
                ' | round ' . $this->round->round_number .
                ' | ' . $this->round->format_name
        );
    }

    private function font($type, $style = '') {
        switch ($type) {
            case 'small':
                $this->pdf->SetFont('Arial', $style, 12);
                break;
            case 'middle':
                $this->pdf->SetFont('Arial', $style, 16);
                break;
            default:;
                trigger_error(__FUNCTION__ . " $type");
        }
    }

    public function get_filename() {
        return
                sprintf('results_%s_%s_%s.pdf',
                $this->round->competition_id,
                $this->round->event_id,
                $this->round->round_number);
    }

}
