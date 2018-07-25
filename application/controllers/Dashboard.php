<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct(){
        parent::__construct();
        //Must to Load!
        $this->load->library('session');
        //load model loginm
        $this->load->model('loginm');
    }

    //Index
    public function index(){
		if($this->loginm->chksess()){
			//If Logged in here
			$this->load->view("login/dashboard");
		}else{
			//If Doesnt Login go to login
			redirect("login");
		}
	}
}