<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loginm extends CI_Model{

    //You Know lah ini login
    function login($f1,$f2){
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where($f1);
        $this->db->where($f2);
        $this->db->where('level <', '2');
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            return FALSE;
        } else {
            return $query->result();
        }
    }

    //Function check access
    function chksess(){
        return $this->session->userdata('mail');
    }

    function chklp($add)
    {
        # code...
    }
}