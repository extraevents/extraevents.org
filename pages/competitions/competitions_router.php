<?php

$request_1 = request::get(1);
switch ($request_1):
    case null:
        grand::include_check_grand('competitions.list');
        break;
    case 'create':
        if (page::is_post()) {
            grand::action_check_grand('competition.create');
        } else {
            grand::include_check_grand('competition.create');
        }
        break;
    case 'mine':
        if (grand::resolve_access('competitions.mine')) {
            grand::include_check_grand('competitions.mine');
            page::set_title(t('competition.mine'));
        } else {
            message::set('competition.login_required!');
            form::return('competitions');
        }
        break;
    default:
        $competition_id = $request_1;
        $section = request::get(2);
        $event_id = request::get(3);
        $round_number = request::get(4);
        $action = request::get(5);
        $competition = new competition($competition_id);
        if (!$competition->id) {
            page_404();
        }
        page::set_title($competition->name);
        page::set_parrent_url("competitions/$competition_id/$section");
        grand::include_check_grand('competition.title');

        $round = $competition->get_round($event_id, $round_number);
        page::add_object('round', $round);

        switch ($section):
            case null:
                grand::include_check_grand('competition.info');
                break;

            case 'register':
                if (page::is_post()) {
                    switch (form::action()) {
                        case 'register':
                        case 'unregister':
                            grand::action_check_grand('competition.register');
                            break;
                        default:page_404();
                    }
                } else {
                    grand::include_check_grand('competition.register');
                }
                break;

            case 'scrambles':
                switch ($action):
                    case null:
                        if (page::is_post()) {
                            switch (form::action()) {
                                case 'generate':
                                    grand::action_check_grand('competition.scrambles_round_generate');
                                    break;
                                default:page_404();
                            }
                        } else {
                            grand::include_check_grand('competition.scrambles_rounds');
                            break;
                        }
                        break;
                    case 'print':
                    case 'download':
                        if (!$round) {
                            page_404();
                        }
                        if (file_exists(round::file_scramble($round))) {
                            grand::include_check_grand('competition.scrambles_round_print');
                        } else {
                            page_404();
                        }
                        exit();
                        break;
                    case 'generate':
                        if (page::is_post()) {
                            grand::action_check_grand('competition.scrambles_round_generate_wca');
                        } else {
                            grand::include_check_grand('competition.scrambles_round_generate_wca');
                        }
                        break;
                    default: page_404();
                endswitch;
                break;

            case 'scorecards':
                switch ($event_id):
                    case null:
                        grand::include_check_grand('competition.scorecards_rounds');
                        break;
                    default:
                        if (!$round) {
                            page_404();
                        }
                        grand::include_check_grand('competition.scorecards_round_print');
                endswitch;
                break;

            case 'results':
                switch ($event_id):
                    case null:
                        grand::include_check_grand('competition.results_rounds');
                        break;
                    default:
                        if (!$round) {
                            page_404();
                        }
                        switch ($action):
                            case null:
                                grand::include_check_grand('competition.results_round');
                                break;
                            case 'enter':
                                if (page::is_post()) {
                                    grand::action_check_grand('competition.results_round_enter');
                                } else {
                                    grand::include_check_grand('competition.results_round_enter');
                                }
                                break;
                            case 'print':
                                grand::include_check_grand('competition.results_round_print');
                                break;
                            default: page_404();
                        endswitch;

                endswitch;
                break;


            case 'status':
                if (page::is_post()) {
                    grand::action_check_grand('competition.status');
                } else {
                    grand::include_check_grand('competition.status');
                }
                break;

            case 'settings':
                if (page::is_post()) {
                    grand::action_check_grand('competition.settings');
                } else {
                    grand::include_check_grand('competition.settings');
                }
                break;

            case 'registrations':
                grand::include_check_grand('competition.registrations');
                break;
            default:page_404();
        endswitch;
        grand::include_check_grand('competition.footer');
endswitch;
