<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function signup()
    {
        $email           = trim($this->input->post('email'));
        $password        = $this->input->post('password');
        $confirmPassword = $this->input->post('confirm_password');

        $inferredRole = $this->_inferRoleFromEmail($email);
        log_message('debug', 'Inferred role: ' . ($inferredRole ?? 'none'));
    }

    private function _inferRoleFromEmail($email)
    {
        $domain = substr(strrchr($email, '@'), 1);
        switch ($domain) {
            case 'eastminster.ac.uk':
                return 'alumnus';
            case 'phantasmagoria.com':
                return 'developer';
            default:
                return null;
        }
    }
}
