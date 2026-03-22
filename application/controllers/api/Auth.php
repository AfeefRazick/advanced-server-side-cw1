<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/RestController.php';

use chriskacerguis\RestServer\RestController;

class Auth extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function signup_post()
    {
        log_message('debug', 'Signup endpoint called');

        $fullName = trim((string) $this->post('full_name'));
        $email = trim((string) $this->post('email'));
        $password = (string) $this->post('password');
        $confirmPassword = (string) $this->post('confirm_password');
        $role = $this->_inferRoleFromEmail($email);

        $this->form_validation->set_data(array(
            'full_name' => $fullName,
            'email' => $email,
            'password' => $password,
            'confirm_password' => $confirmPassword,
        ));

        $this->form_validation->set_rules('full_name', 'Full Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() === false) {
            log_message('debug', 'Signup validation failed: ' . validation_errors(' ', ' '));

            return $this->response(array(
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $this->form_validation->error_array(),
            ), 400);
        }

        if ($role === null) {
            log_message('debug', 'Signup rejected for unsupported email domain: ' . $email);

            return $this->response(array(
                'status' => false,
                'message' => 'Please use an approved email address',
            ), 400);
        }

        $existingUser = $this->User_model->get_by_email($email);
        if ($existingUser) {
            log_message('debug', 'Signup rejected for duplicate email: ' . $email);

            return $this->response(array(
                'status' => false,
                'message' => 'Email already registered',
            ), 409);
        }

        $userData = array(
            'full_name' => $fullName,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'role' => $role,
            'is_verified' => 0,
        );

        $userId = $this->User_model->create_user($userData);

        if ($userId ===    false) {
            log_message('error', 'Signup database insert failed for email: ' . $email);

            return $this->response(array(
                'status' => false,
                'message' => 'Signup failed',
            ), 500);
        }

        log_message('debug', 'Signup successful for user ID ' . $userId);

        return $this->response(array(
            'status' => true,
            'message' => 'Signup successful',
            'data' => array(
                'id' => $userId,
                'full_name' => $fullName,
                'email' => $email,
                'role' => $role,
                'is_verified' => false,
            ),
        ), 201);
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
