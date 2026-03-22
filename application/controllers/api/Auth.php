<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/RestController.php';

use chriskacerguis\RestServer\RestController;

class Auth extends RestController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function signup_post()
    {
        $email = trim((string) $this->post('email'));
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');

        $inferredRole = $this->_inferRoleFromEmail($email);
        log_message('debug', 'Inferred role: ' . ($inferredRole ?? 'none'));

        return $this->response(
            array(
                'status' => true,
                'email' => $email,
                'password_present' => !empty($password),
                'confirm_password_present' => !empty($confirmPassword),
                'role' => $inferredRole,
            ),
            200
        );
    }

    private function _inferRoleFromEmail($email)
    {
        if (!$email || strpos($email, '@') === false) {
            return null;
        }

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
