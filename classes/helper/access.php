<?php

class access {

    private $user = null;
    private $grand = false;
    private $member = null;
    private $competition = null;
    public $allowed = false;

    function __construct($grand) {
        $this->grand = $grand;

        $this->init_user();
        $this->init_member();
        $this->init_competition();

        $this->resolve_default();
        $this->resolve_user();
        $this->resolve_member();
        $this->resolve_leader();
        $this->resolve_organizer();
    }

    static function is_leader() {
        return
                (new access('leader'))->allowed;
    }

    static function is_organizer() {
        return
                (new access('organizer'))->allowed;
    }

    static function is_member() {
        return
                (new access('member'))->allowed;
    }

    private function init_user() {
        $this->user = wcaoauth::get_user();
    }

    private function init_member() {
        if ($this->user->user_id ?? false) {
            $this->member = new member($this->user->wca_id);
        }
    }

    private function init_competition() {
        $competition = competition::get();
        if (!$competition->id) {
            return;
        }
        $this->competition = $competition;
    }

    private function resolve_default() {
        if ($this->grand == '+') {
            $this->allowed = true;
        }
    }

    private function resolve_user() {
        if ($this->grand == 'user'
                and $this->user) {
            $this->allowed = true;
        }
    }

    private function resolve_member() {
        if ($this->grand == 'member'
                and $this->check_member()) {
            $this->allowed = true;
        }
    }

    private function resolve_leader() {
        if ($this->check_leader()) {
            $this->allowed = true;
        }
    }

    private function resolve_organizer() {
        if ($this->grand == 'organizer'
                and $this->check_organizer()) {
            $this->allowed = true;
        }
    }

    private function check_member() {
        return
                $this->member->id ?? false;
    }

    private function check_leader() {
        return
                $this->member->is_leader ?? false;
    }

    private function check_organizer() {
        if ($this->competition == null) {
            return false;
        }

        $organizers = $this->competition->organizers;
        $wcaid = wcaoauth::wca_id();
        return
                in_array($wcaid, $organizers);
    }

}
