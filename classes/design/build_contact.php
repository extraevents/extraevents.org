<?php

class build_contact {

    private $contact;

    public function __construct($contact) {
        $this->contact = $contact;
    }

    function out() {
        if ($this->contact->type == 'url') {
            return $this->out_url();
        }

        if ($this->contact->type == 'email') {
            return $this->out_email();
        }

        if ($this->contact->type == 'phone') {
            return $this->out_phone();
        }
    }

    function out_url() {
        $value = $this->contact->value;
        $host = parse_url($this->contact->value ?? false)['host'] ?? false;
        $host = strtolower($host);
        $host = str_replace('www.', '', $host);
        switch ($host) {
            case 'vk.com':
                $link = '<i class="fab fa-vk"></i>';
                break;

            case 't.me':
            case 'telegram.org':
                $link = '<i class="fab fa-telegram-plane"></i>';
                break;

            case 'instagram.com':
                $link = '<i class="fab fa-instagram"></i>';
                break;

            case 'facebook.com':
            case 'fb.com':
                $link = '<i class="fab fa-facebook-f"></i>';
                break;

            default:
                $link = $host;
        }

        $url = $this->contact->value ?? false;
        return
                "<a title='$value' href = '$url'>$link</a>";
    }

    function out_email() {
        $email = $this->contact->value ?? false;
        return
                "<a title='$email' href = 'mailto:$email'><i class='far fa-envelope'></i></a>";
    }

    function out_phone() {
        $phone = $this->contact->value ?? false;
        return
                "<a title='$phone' href = 'tel:$phone'><i class='fas fa-phone'></i></a>";
    }

}
