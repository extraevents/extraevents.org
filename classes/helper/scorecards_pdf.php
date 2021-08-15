<?php

class scorecards_pdf {

    private $ee_round;
    private $wca_competition;
    private $event;
    private $pdf;
    private $person_count;
    private $need_scrambler;
    private $long_inspection;
    private $round;
    private $registrations;

    public function __construct($round) {
        $this->round = $round;
        $this->event = new event($this->round->event_id);
        $this->registrations = results::get($round, ['team_complete'], 'card_id');

        $this->person_count = $this->event->person_count;
        $this->need_scrambler = !($this->event->scrambling ??= false);
        $this->long_inspection = $this->event->long_inspection;
    }

    public function pdf() {
        $round = $this->round;
        $this->pdf = new pdf_xy('P', 'mm');
        $pdf = $this->pdf;
        $pdf->SetLineWidth(0.1);

        $points = [
            [5, 5],
            [$pdf->w / 2 + 5, 5],
            [5, $pdf->h / 2 + 5],
            [$pdf->w / 2 + 5, $pdf->h / 2 + 5]
        ];

        $registrations = $this->registrations;
        $pages = max([ceil(sizeof($registrations) / 4), 1]);
        for ($l = 0; $l < $pages; $l++) {
            $pdf->AddPage();
            $pdf->Line(5, $pdf->h / 2, $pdf->w - 5, $pdf->h / 2);
            $pdf->Line($pdf->w / 2, 5, $pdf->w / 2, $pdf->h - 5);
            for ($i = 0; $i < 4; $i++) {
                $team = $registrations[$i + $l * 4] ?? null;
                if (is_null($team)) {
                    $team = (object) [
                                'card_id' => false,
                                'person1_id' => false,
                                'person2_id' => false,
                                'person3_id' => false,
                                'person4_id' => false,
                                'person1_name' => false,
                                'person2_name' => false,
                                'person3_name' => false,
                                'person4_name' => false
                    ];
                }
                $point = $points[$i];

                $pdf->set_cursor('point', $point[0], $point[1]);

                $pdf->set_cursor('header_1', 0, 5, 'point');
                $str = iconv('utf-8', 'cp1252//TRANSLIT', $round->competition_name);
                $this->font('small');
                $pdf->text_cursor($str, 5);
                $this->font('big');
                $pdf->text_cursor(sprintf('%3s', $team->card_id), 83, 3);

                $pdf->set_cursor('header_2', 0, 5, 'header_1');
                $this->font('small');
                $pdf->text_cursor($round->event_name . ', round ' . $round->round_number, 5);

                $pdf->set_cursor('persons', 3, 3, 'header_2');
                $this->draw_persons([$team->person1_name, $team->person2_name, $team->person3_name, $team->person4_name]);

                $pdf->set_cursor('marks', 0, 7, ['point', 'last']);
                $this->draw_marks();

                $pdf->set_cursor('solves', 0, 2, 'marks');
                foreach (range(1, $round->format_solve_count) as $k) {
                    $this->draw_solve($k);
                    $this->font('middle');
                    if ($round->format_cutoff_count == $k) {
                        $this->draw_cutoff();
                    }
                }

                $this->draw_limit();

                $this->draw_solve('Ex');
                $this->draw_inspection();

                $pdf->set_cursor('wca_id', 5, $pdf->h / 2 - 10, 'point');
                $this->font('small');
                $pdf->text_cursor(implode(' ', [$team->person1_id, $team->person2_id, $team->person3_id, $team->person4_id]));
            }
        }

        return $pdf;
    }

    private function font($type) {
        switch ($type) {
            case 'small':
                $this->pdf->SetFont('Arial', '', 10);
                break;
            case 'middle':
                $this->pdf->SetFont('Arial', '', 14);
                break;
            case 'big':
                $this->pdf->SetFont('Arial', '', 20);
                break;
            default:
                trigger_error(__FUNCTION__ . " $type");
        }
    }

    private function draw_comp_sign($x, $y, $dx, $dy, $person_count) {
        $pdf = $this->pdf;
        $pdf->rect_cursor($dx, $dy, $x, $y);
        $name = $pdf->get_name();
        $pdf->set_cursor('draw_comp_sign', $x, $y, 'last');
        if ($person_count == 2) {
            $pdf->line_cursor($dx, 0, 0, $dy);
        }

        if ($person_count == 3) {
            $pdf->line_cursor(0, 0, $dx / 2, $dy / 2);
            $pdf->line_cursor($dx, 0, $dx / 2, $dy / 2);
            $pdf->line_cursor($dx / 2, $dy, $dx / 2, $dy / 2);
        }

        if ($person_count == 4) {
            $pdf->line_cursor($dx, 0, 0, $dy);
            $pdf->line_cursor(0, 0, $dx, $dy);
        }

        $pdf->set_current($name);
    }

    private function draw_solve($k) {
        $pdf = $this->pdf;
        $this->font(is_numeric($k) ? 'middle' : 'small');
        $pdf->text_cursor($k, 0, 8);
        if ($this->need_scrambler) {
            $pdf->rect_cursor(15, 13, 5, 0);
            $pdf->rect_cursor(42, 13, 21, 0);
        } else {
            $pdf->rect_cursor(58, 13, 5, 0);
        }
        $pdf->rect_cursor(15, 13, 64, 0,);

        $this->draw_comp_sign(80, 0, 15, 13, $this->person_count);

        $pdf->shift_cursor(0, 14);
    }

    private function draw_persons($persons) {
        $pdf = $this->pdf;
        foreach (range(0, $this->round->person_count - 1) as $u) {
            $person = $persons[$u] ?? '';
            $person_name = iconv('utf-8', 'cp1252//TRANSLIT//IGNORE', $person);
            $pdf->shift_cursor(0, 1);
            $pdf->rect_cursor(90, 9, 2);
            $pdf->shift_cursor(0, 6);
            $this->font('middle');
            $pdf->text_cursor($person_name, 4);
            $pdf->shift_cursor(0, 2);
        }
    }

    private function draw_marks() {
        $pdf = $this->pdf;
        $this->font('small');
        if ($this->need_scrambler) {
            $pdf->text_cursor('Scr', 10);
            $pdf->text_cursor('Result', 36);
        } else {
            $pdf->text_cursor('Result', 30);
        }
        $pdf->text_cursor('Judge', 67);
        $pdf->text_cursor('Comp', 83);
    }

    private function draw_inspection() {
        $pdf = $this->pdf;
        if ($this->long_inspection) {
            $pdf->shift_cursor(0, 4);
            $this->font('small');
            $pdf->text_cursor("Inspection 20 sec; calls 10 and 17; >20 (+3); >23 (DNF)", 5);
        }
    }

    private function draw_cutoff() {
        $pdf = $this->pdf;
        $cutoff_out = centisecond::out($this->round->cutoff, true);
        if ($this->round->cutoff) {
            $pdf->shift_cursor(0, 2.5);
            $this->font('small');
            $pdf->text_cursor("Cutoff $cutoff_out", 8);
            $pdf->shift_cursor(0, 1.5);
        }
    }

    private function draw_limit() {
        $pdf = $this->pdf;
        $limit_out = centisecond::out($this->round->time_limit);

        if ($this->round->time_limit) {
            $pdf->shift_cursor(0, 2.5);
            $this->font('small');
            $pdf->text_cursor("Limit $limit_out", 8);
            $pdf->shift_cursor(0, 1.5);
        }
    }

    public function get_filename() {
        return
                sprintf('scorecards_%s_%s_%s.pdf',
                $this->round->competition_id,
                $this->round->event_id,
                $this->round->round_number);
    }

}
