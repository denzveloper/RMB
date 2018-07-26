<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct(){
        parent::__construct();
        //Must to Load!
        $this->load->library('session');
        //load model loginm
        $this->load->model('loginm');
    }

    public function index(){
        if($this->loginm->chksess()){
			//jika memang session sudah terdaftar, maka dialihkan ke halaman dahsboard
			redirect("dashboard");
		}else{
			//set form validation
            $this->form_validation->set_rules('mail', 'Email', 'required|valid_email|trim|min_length[6]|max_length[64]');
            $this->form_validation->set_rules('pass', 'Password', 'required|min_length[4]|max_length[64]');
            //set message form validation
            $this->form_validation->set_message('required', 'harus diisi!');
            //check Validation
			if ($this->form_validation->run() == TRUE){
                $mail = $this->input->post("mail", TRUE);
                $pass = $this->input->post("pass", TRUE);
                //check user mail and password
                $cek = $this->loginm->login(array('surel' => $mail), array('sandi' => $pass));
                if ($cek != FALSE){
                    foreach ($cek as $hit){
            	        $sesar = array(
                            'logged_in' => TRUE,
                            'mail' => $hit->surel,
                            'fnam' => $hit->namadepan,
                            'lnam' => $hit->namabelakang,
                            'lv' => $hit->level
                         );
                    }
                    //set session userdata
                    $this->session->set_userdata($sesar);
    	            redirect('dashboard/');
                }else{
                    $data['error'] = 'Username or Password Wrong!';
                    $this->load->model('waterm');
                    $cek = $this->waterm->login(array('surel' => $mail), array('sandi' => $pass));
                    if ($cek != FALSE){
                        $data['error'] = 'Sorry, This Platform for Administrator only!\nYou only can use on Platform Mobile app!';
                    }
                	$this->load->view('login', $data);
                }
            }else{
                $this->load->view('login');
            }
        }
    }

    public function logout(){
		//logout
		$arraydata = array(
            'logged_in' => FALSE, 'mail'=> '', 'fnam'=>'', 'lnam'=>'', 'lv'=>''
        );
        $this->session->unset_userdata($arraydata);
        $this->session->sess_destroy();
		redirect('login');
	}
}