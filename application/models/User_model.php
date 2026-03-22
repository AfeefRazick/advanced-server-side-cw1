<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function get_by_email($email)
    {
        return $this->db->get_where('users', ['email' => $email])->row();
    }

    public function create_user($data)
    {
        $success = $this->db->insert('users', $data);

        if (!$success) {
            return false;
        }

        return $this->db->insert_id();
    }
}
