<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Water extends CI_Controller {

    public function __construct(){
        parent::__construct();
        //load model waterm
        $this->load->model('waterm');
    }

    public function index(){
      header('Content-Type: application/json; charset=UTF-8;');
      //Getting get value what todo
      $stat = array('404' => "Not Found Error!");
      $go = $this->input->get("do", TRUE);
      switch ($go){
        case 'register'://Create New Account
          //Values requipment here
          $this->form_validation->set_rules('namadpn', 'nama', 'required');
          $this->form_validation->set_rules('namablk', 'nama blk', 'required');
          $this->form_validation->set_rules('mail', 'mail', 'required|valid_email|trim|min_length[6]|max_length[64]');
          $this->form_validation->set_rules('mail1', 'mail', 'required|valid_email|trim|min_length[6]|max_length[64]');
          $this->form_validation->set_rules('pass', 'pass', 'required|min_length[4]|max_length[64]');
          $this->form_validation->set_rules('pass1', 'pass', 'required|min_length[4]|max_length[64]');
          $this->form_validation->set_rules('tgl', 'tgl', 'required');
          $this->form_validation->set_rules('ngr', 'negara', 'required');
          $this->form_validation->set_rules('prov', 'provinsi', 'required');
          $this->form_validation->set_rules('kot', 'kota', 'required');
          $this->form_validation->set_rules('jln', 'jalan', 'required');

          //Detecting Are Value is Valid?
          if ($this->form_validation->run() == TRUE){
            $mail = $this->input->post("mail",TRUE);
            $mail1 = $this->input->post("mail1",TRUE);
            $pass = $this->input->post("pass",TRUE);
            $pass1 = $this->input->post("pass1",TRUE);

            //Detecting if Different Values On Email and Password beetween they verification
            if ($mail != $mail1){
              if($pass != $pass1){
                //If Password And Mail Not Match/Different
                $stat = array('status' => '400','msg' => 'New Email and New Password is not Match with they Confirmation!');
              }else{
                //Error Email Different
                $stat = array('status' => '400','msg' => 'Email is different!');
              }
            }
            elseif ($pass != $pass1){
              //Error Password Different
              $stat = array('status' => '400','msg' => 'Password is different!');
            }else{

              //Get data From POST method
              $ndp = $this->input->post("namadpn",TRUE);
              $nbl = $this->input->post("namablk",TRUE);
              $tgl = $this->input->post("tgl",TRUE);
              $ngr = $this->input->post("ngr",TRUE);
              $prov = $this->input->post("prov",TRUE);
              $kot = $this->input->post("kot",TRUE);
              $jln = $this->input->post("jln",TRUE);

              //Check Email avaiable?
              $cek = $this->waterm->chkreg("user",array('surel' => $mail));
              //if Email was usage before. Here!
              if ($cek == FALSE) {
                $stat = array('status' => '400','msg' => 'Email was Registered before!');
              }else{
                //Save to database
                $cek1 = $this->waterm->reg(array('surel' => $mail, 'namadepan' => $ndp,
                  'namabelakang' => $nbl, 'sandi' => $pass, 'lahir' => $tgl, 'negara' => $ngr,
                  'prov' => $prov, 'kota' => $kot, 'jalan' => $jln, 'poin' => 0));

                if ($cek1 != FALSE){
                  $tmp = tempnam('./tmp', 'imgtmp'); // might not work on some systems, specify your temp path if system temp dir is not writeable
                  file_put_contents($tmp, base64_decode($_POST['img']));
                  $imginf = getimagesize($tmp); 
                  $_FILES['userfile'] = array(
                      'name' => uniqid().'.'.preg_replace('!\w+/!', '', $imginf['mime']),
                      'tmp_name' => $tmp,
                      'size'  => filesize($mp),
                      'error' => UPLOAD_ERR_OK,
                      'type'  => $imginf['mime'],
                  );
                  //Configuration Updoad Photos
                  //$prna = time().$mail.$_FILES["profil"]['name']; //Change name of image
                  $config['upload_path'] = './asset/uspic/'; //On "userpic" upload
                  $config['allowed_types'] = 'jpg|jpeg|png'; //Jpg and Png Only
                  $config['max_size'] = '2048'; //2MB
                  $config['encrypt_name'] = TRUE;
                  //$config['file_name'] = $prna; //set new Name
                  $this->load->library('upload', $config); //load library upload
                  $fnm = null;

                  //Photos is detected OK
                  if ($this->upload->do_upload('userfile', true)){
                    //Get Filename
                    $fnm = $this->upload->data('file_name');
                    unlink($tmp);
                    //Update user table on "Photo" column where Mail as ID
                    $this->waterm->uptabusr(array('surel' => $mail), array('photo' => $fnm));
                  }
                  //JSON send OK!
                  $stat = array('msg' => "Thank You for Join Us!",
                    'user' => array('email' => $mail, 'photo' => $fnm, 'namadpn' => $ndp, 'namablk' => $nbl),
                    'status' => '200');
                }else{
                  $stat = array('status' => '400','msg' => 'Error Register!');
                }
              }
            }
          }else{
            //Fail Validation Because illegal
            $stat = array('status' => '400','msg' => 'Please Fill All Required Form!');
          }
        break;

        case 'login': //Login Application Code
          //Validation Mail and Password Required
          $this->form_validation->set_rules('mail', 'mail', 'required|valid_email|trim|min_length[6]|max_length[64]');
          $this->form_validation->set_rules('pass', 'pass', 'required|min_length[4]|max_length[64]');

          //Here if Validation Passed
          if ($this->form_validation->run() == TRUE){
            //get input from android with POST method
            $mail = $this->input->post("mail",TRUE);
            $pass = $this->input->post("pass",TRUE);
            //checking is user legal to login?
            $cek = $this->waterm->login(array('surel' => $mail), array('sandi' => $pass));

            //Checked is legal login?
            if ($cek != FALSE){
              //Getting data require to android
              foreach($cek as $cek){
                $pho = base_url()."asset/uspic/".$cek->photo;
                $ndp = $cek->namadepan;
                $nbl = $cek->namabelakang;
                $tgl = $cek->lahir;
                $ngr = $cek->negara;
                $pro = $cek->prov;
                $kot = $cek->kota;
                $jln = $cek->jalan;
                $nbl = $cek->namabelakang;
                $pnt = $cek->poin;
              }
              //JSON data to android. Login is Successfull
              $stat = array('msg' => "Success Login As $ndp",
                'user' => array('email' => $mail, 'photo' => $pho, 'namadpn' => $ndp, 'namablk' => $nbl,
                  'tgl' => $tgl, 'negara' => $ngr, 'prov' => $pro, 'kota' => $kot, 'jln' => $jln,
                  'poin' => $pnt),
                'status' => '200');
            }else{
              //Error JSON fail Login
              $stat = array('status' => '400','msg' => "Failed Login As $mail");
            }

          }else{
            //Error JSON validation is fail
            $stat = array('status' => '400','msg' => 'Bad Mail or Password!');
          }
        break;

        case 'updatepro':
          //Validasi form yang diketikkan user
          $this->form_validation->set_rules('namadpn', 'nama', 'required');
          $this->form_validation->set_rules('namablk', 'nama blk', 'required');
          $this->form_validation->set_rules('mail', 'mail', 'required|valid_email|trim|min_length[6]|max_length[64]');
          $this->form_validation->set_rules('pass', 'pass', 'required|min_length[4]|max_length[64]');
          $this->form_validation->set_rules('tgl', 'tgl', 'required');
          $this->form_validation->set_rules('ngr', 'negara', 'required');
          $this->form_validation->set_rules('prov', 'provinsi', 'required');
          $this->form_validation->set_rules('kot', 'kota', 'required');
          $this->form_validation->set_rules('jln', 'jalan', 'required');

          if ($this->form_validation->run() == TRUE){
            //ambil passwor dan email
            $mail = $this->input->post("mail",TRUE);
            $pass = $this->input->post("pass",TRUE);
            //ngecek password dan email itu benar
            $cek = $this->waterm->login(array('surel' => $mail), array('sandi' => $pass));

            //Jika autentifikasi benar kesini
            if ($cek != FALSE){
              //Ambil data yang dikirimkan!
              $ndp = $this->input->post("namadpn",TRUE);
              $nbl = $this->input->post("namablk",TRUE);
              $tgl = $this->input->post("tgl",TRUE);
              $ngr = $this->input->post("ngr",TRUE);
              $pro = $this->input->post("prov",TRUE);
              $kot = $this->input->post("kot",TRUE);
              $jln = $this->input->post("jln",TRUE);

              $tmp = tempnam('./tmp', 'imgtmp'); // might not work on some systems, specify your temp path if system temp dir is not writeable
              file_put_contents($tmp, base64_decode($_POST['img']));
              $imginf = getimagesize($tmp); 
              $_FILES['userfile'] = array(
                'name' => uniqid().'.'.preg_replace('!\w+/!', '', $imginf['mime']),
                'tmp_name' => $tmp,
                'size'  => filesize($mp),
                'error' => UPLOAD_ERR_OK,
                'type'  => $imginf['mime'],
              );
              //Configuration to Updoad Photos
              $config['upload_path'] = './asset/uspic/'; //On "userpic" upload
              $config['allowed_types'] = 'jpg|png'; //Jpg and Png Only
              $config['max_size'] = '2048'; //2MB
              $config['max_width']  = '2048'; //max width images
              $config['max_height']  = '2048'; //max height images
              $config['encrypt_name'] = TRUE;
              //$config['file_name'] = $prna; //set new Name
              $this->load->library('upload', $config); //load library upload
              //Set Null and True First
              $pho = null;
              $del = TRUE;

              //Get Name file
              $get = $this->waterm->login(array('surel' => $mail), array('sandi' => $pass));
              //Getting data
              foreach($get as $get){
                  $pho = $get->photo;
              }
              if ($pho === null){
                $del = FALSE;
              }

              //Photos is detected OK
              if ($this->upload->do_upload('userfile', true)){
                //Deleting Image Profil File
                if ($del == TRUE){
                  //Delete Photos before
                  unlink("./asset/uspic/$pho");
                  unlink($tmp);
                }

                //Get Filename Image
                $pho = $this->upload->data('file_name');
                //Update Data With Email as that ID Include Image
                $cek1 = $this->waterm->uptabusr(array('surel' => $mail),array('namadepan' => $ndp,
                        'namabelakang' => $nbl, 'lahir' => $tgl, 'negara' => $ngr,
                        'prov' => $pro, 'kota' => $kot, 'jalan' => $jln, 'photo' => $pho));

              }else{
                //Update data with email as id Exclude Image
                $cek1 = $this->waterm->uptabusr(array('surel' => $mail),array('namadepan' => $ndp,
                        'namabelakang' => $nbl, 'lahir' => $tgl, 'negara' => $ngr,
                        'prov' => $pro, 'kota' => $kot, 'jalan' => $jln));
              }

              if ($cek1 =! FALSE){
              //OK JSON Update Success
              $stat = array('msg' => "Update Profil Successfull!",
                'user' => array('email' => $mail, 'photo' => $pho, 'namadpn' => $ndp, 'namablk' => $nbl,
                'profil' => array('tgl' => $tgl, 'negara' => $ngr, 'prov' => $pro, 'kota' => $kot, 'jln' => $jln)),
                'status' => '200');
              }else{
              //Error JSON fail updating profil
              $stat = array('status' => '400','msg' => "Can't Update Profil!");
              }
            }else{
              //Error JSON fail Wrong Password
              $stat = array('status' => '400','msg' => "Failed Update! Bad Password!");
            }
          }else {
            //Fail Validation Because illegal input
            $stat = array('status' => '400','msg' => 'Please Fill All Required Form!');
          }
        break;

        case 'editmail'://Edit Email
          //Validation Email(New+Confirm) and password for change email
          $this->form_validation->set_rules('mailb', 'mailb', 'required|valid_email|trim|min_length[6]|max_length[64]');
          $this->form_validation->set_rules('mail', 'mail', 'required|valid_email|trim|min_length[6]|max_length[64]');
          $this->form_validation->set_rules('mail1', 'mail1', 'required|valid_email|trim|min_length[6]|max_length[64]');
          $this->form_validation->set_rules('pass', 'pass', 'required|min_length[4]|max_length[64]');

          if ($this->form_validation->run() == TRUE){
            //ambil passwor dan email
            $mailb = $this->input->post("mailb",TRUE);
            $mail = $this->input->post("mail",TRUE);
            $mail1 = $this->input->post("mail1",TRUE);
            $pass = $this->input->post("pass",TRUE);

            //Check Email avaiable?
            $cek = $this->waterm->chkreg("user",array('surel' => $mail));
            if ($cek == FALSE) {
              $stat = array('status' => '400','msg' => 'Email Registered with other account!');
            }else{
              //OK Email Not registered with other account
              if ($mail == $mail1){
                //ngecek password dan email itu benar
                $cek1 = $this->waterm->login(array('surel' => $mailb), array('sandi' => $pass));

                //Jika autentifikasi benar kesini
                if ($cek1 != FALSE){
                  //Updating data user
                  $cek2 = $this->waterm->uptabusr(array('surel' => $mailb),array('surel' => $mail));

                  if ($cek2 != FALSE) {
                    $stat = array('msg' => "Update Mail to: $mail Successfull!",
                      'user' => array('email' => $mail), 'status' => '200');
                  }else {
                    //Error JSON fail updating email
                    $stat = array('status' => '400','msg' => "Something went wrong!");
                  }
                }else{
                  //Error JSON fail Wrong Password
                  $stat = array('status' => '400','msg' => "Failed Update! Bad Password!");
                }
              }else{
                //Error Email Different(With New and verification)
                $stat = array('status' => '400','msg' => 'Email is different!');
              }
            }
          }else{
            //Fail Validation Because illegal input
            $stat = array('status' => '400','msg' => 'Please Fill All Required Form!');
          }
        break;

        case 'edpass'://Edit Password
          //Validation Email and password(Old+New+Confirm) for change Password
          $this->form_validation->set_rules('mail', 'mail', 'required|valid_email|trim|min_length[6]|max_length[64]');
          $this->form_validation->set_rules('pass', 'pass', 'required|min_length[4]|max_length[64]');
          $this->form_validation->set_rules('psn', 'pass new', 'required|min_length[4]|max_length[64]');
          $this->form_validation->set_rules('psn1', 'pass com', 'required|min_length[4]|max_length[64]');

          if ($this->form_validation->run() == TRUE){
            //get input from android with POST method
            $mail = $this->input->post("mail",TRUE);
            $pass = $this->input->post("pass",TRUE);
            $passn = $this->input->post("psn",TRUE);
            $passn1 = $this->input->post("psn1",TRUE);
            //checking is user legal to login?
            $cek = $this->waterm->login(array('surel' => $mail), array('sandi' => $pass));
            foreach($cek as $cek){
                $namadpn = $get->namadepan;
            }

            //Checking Is User Allowable to Change Password
            if ($cek != FALSE) {
              if ($passn != $passn1){
                //Error New Password and Confirmation Password Different
                $stat = array('status' => '400','msg' => 'New Password and Confirm Password is different!');
              }else{
                $cek2 = $this->waterm->uptabusr(array('surel' => $mail),array('sandi' => $passn));
                if ($cek2 != FALSE) {
                  $stat = array('msg' => "$namadpn, Update Password Successfull!", 'status' => '200');
                }else {
                  //Error JSON fail updating email
                  $stat = array('status' => '400','msg' => "Something went wrong!");
                }
              }
            }else {
              //Error JSON fail Wrong Password
              $stat = array('status' => '400','msg' => "Failed Change Password! Last Password Wrong!");
            }
          }else {
            //Fail Validation Because illegal input
            $stat = array('status' => '400','msg' => 'Please Fill All Required Form!');
          }
        break;

        case 'poin'://Transaksi Poin
          //Apa yang dibutuhkan
          $this->form_validation->set_rules('todo', 'Ngapain', 'required');
          $this->form_validation->set_rules('pts', 'Poin', 'required');
          $this->form_validation->set_rules('mail', 'Surel', 'required');
          $this->form_validation->set_rules('pass', 'Surel', 'required');

          if ($this->form_validation->run() == TRUE){
            $todo = $this->input->post("todo",TRUE);
            $pts = $this->input->post("pts",TRUE);
            $mail = $this->input->post("mail",TRUE);
            $pass = $this->input->post("pass",TRUE);

            //Check User Login?
            $cek = $this->waterm->login(array('surel' => $mail), array('sandi' => $pass));

            if ($cek != FALSE){
              //Jika User dan Password benar

              //What-todo
              if ($todo == 1){
                //OK, Point No to be add
                $cek1 = $this->waterm->pntud("+$pts", array('surel' => $mail));
                if($cek1 != FALSE){
                  $stat = array('status' => '200','msg' => 'OK');
                }else{
                  $stat = array('status' => '400','msg' => 'Error! Cant Addition Your point!');
                }
              }elseif ($todo == 2){
                //code for min
                if ($pts - $cek->poin >= 0){
                  //Fail Action Because point min
                  $stat = array('status' => '400','msg' => 'Sorry Point is limit!');
                }else{
                  //OK, Point No to be Minus
                  $cek1 = $this->waterm->pntud("-$pts", array('surel' => $mail));
                  if ($cek1 != FALSE){
                  $stat = array('status' => '200','msg' => 'OK');                    
                  }else{
                    $stat = array('status' => '400','msg' => 'Error! Cant Subtraction Your point!');
                  }
                }
              }else{
                //Fail Action Because is unknown
                $stat = array('status' => '400','msg' => 'Unknown Action!');
              }
            }else{
              //Fail Account unknown
              $stat = array('status' => '400','msg' => 'No Logged in!');
            }
          }else{
            //Fail Validation Because illegal input
            $stat = array('status' => '400','msg' => 'Required data not found!');
          }
        break;

        case 'newplace'://Create New Place
          //Values requipment here
          $this->form_validation->set_rules('nama', 'nama', 'required');
          $this->form_validation->set_rules('relasi', 'relasi', 'required');
          $this->form_validation->set_rules('tipe', 'tipe', 'required');
          $this->form_validation->set_rules('lon', 'longitude', 'required');
          $this->form_validation->set_rules('lat', 'latitude', 'required');
          $this->form_validation->set_rules('mail', 'mail', 'required');
          $this->form_validation->set_rules('pass', 'pass', 'required');

          if ($this->form_validation->run() == TRUE){
            $nam = $this->input->post("nama",TRUE);
            $rel = $this->input->post("relasi",TRUE);
            $typ = $this->input->post("tipe",TRUE);
            $lon = $this->input->post("lon",TRUE);
            $lat = $this->input->post("lat",TRUE);
            $mail = $this->input->post("mail",TRUE);
            $pass = $this->input->post("pass",TRUE);

            //Validation Is OK
            $cek = $this->waterm->login(array('surel' => $mail),array('sandi' => $pass));

            //Check user is allowed?
            if($cek != FALSE){
              //check this place maked before?
              $cek1 = $this->waterm->chsm('tempat_baru', array('longitude' => $lon),array('latitude' => $lat));
              $cek2 = $this->waterm->chsm('tempatisi', array('longitude' => $lon),array('latitude' => $lat));
              
              if($cek1 != FALSE && $cek2 != FALSE){
                //cek the tag of place
                $cek3 = $this->waterm->chkreg('interest_list', array('nama' => $typ));
                
                if($cek3 != FALSE){
                  //add place to database
                  $cek4 = $this->waterm->addsm('tempat_baru', array('nama' => $nam, 'relasi' => $rel, 'tipe' => $typ, 'longitude' => $lon, 'latitude' => $lat, 'pendaftar' => $mail));

                  if($cek4 != FALSE){
                    //Ok
                    $stat = array('status' => '200','msg' => "$nam Success added, This can take few days to show point!");
                  }else{
                    //Error Adding
                    $stat = array('status' => '400','msg' => 'Internal Server Error!');
                  }
                }else{
                  $stat = array('status' => '400','msg' => 'Unknown Type refill station!');
                }
              }else{
                $stat = array('status' => '400','msg' => 'Place already exists!');
              }
            }else{
              //Fail Validation Because illegal input
              $stat = array('status' => '400','msg' => 'Not Allowed!');
            }
          }else{
            //Fail Validation Because illegal input
            $stat = array('status' => '400','msg' => 'Info is Required!');
          }
        break;

        case 'refresh':
          // code...
        break;

        case 'review': // Add Review
          //Validation add review with account auth.
          $this->form_validation->set_rules('id', 'id map', 'required');
          $this->form_validation->set_rules('mail', 'Surel', 'required');
          $this->form_validation->set_rules('pass', 'Password', 'required');
          $this->form_validation->set_rules('star', 'Ngapain', 'required');
          $this->form_validation->set_rules('komentar', 'Komentar', 'required');

          //Detecting Are Value is Valid
          if ($this->form_validation->run() == TRUE){
            $mail = $this->input->post("mail",TRUE);
            $pass = $this->input->post("pass",TRUE);
            $idm = $this->input->post("id",TRUE);
            $bin = $this->input->post("star",TRUE);
            $kom = $this->input->post("komentar",TRUE);

            //check maps is true?
            $cek = $this->waterm->chkreg("tempatisi", array("id" => $idm));

            if($cek == FALSE){
              //Check User
              $cek1 = $this->waterm->login(array('surel' => $mail),array('sandi' => $pass));

              if($cek1 != FALSE){
                //This account review before?
                $cek2 = $this->waterm->chsm("review_tempat", array('id_tempat' => $idm), array('surel' => $mail));
                
                if($cek2 == FALSE){
                  //No, Its New Review
                  $cek3 = $this->waterm->addsm("review_tempat", array('surel' => $mail, 'id_tempat' => $idm , 'bintang' => $bin, 'komentar' => $kom));
                  
                  if($cek3 != FALSE){
                    //Review add successfully
                    $stat = array('status' => '200','msg' => 'Review Added!');
                  }else{
                    //adding review error
                    $stat = array('status' => '400','msg' => 'Falied to add Review!');
                  }
                }else{
                  //yes, Before review
                  $cek3 = $this->waterm->uprev("review_tempat", array('id_tempat' => $idm), array('surel' => $mail), array('bintang' => $bin, 'komentar' => $kom));
                  
                  if($cek3 != FALSE){
                    //Ok review renew
                    $stat = array('status' => '200','msg' => 'Review Renewed!');
                  }else{
                    //renew error
                    $stat = array('status' => '400','msg' => 'Falied to Renew Review!');
                  }
                }
              }else{
                //Not Login User
                $stat = array('status' => '400','msg' => 'Not Logged in!');
              }
            }else{
              //No map Point found
              $stat = array('status' => '400','msg' => 'Map Point not Found!');
            }
          }else{
            //Require Data incomplete
            $stat = array('status' => '400','msg' => 'Require Data needed!');
          }
        break;

        case 'showevent'://Showing Event
          $stat = array('status' => '200', 'data' => $this->waterm->fetchev("event"), 'msg' => 'OK' );
        break;

        case 'event-act': // Sahring Event Add Counting
          //Validation add share count. Check Account and id validation
          $this->form_validation->set_rules('id', 'Id Share', 'required');
          $this->form_validation->set_rules('todo', 'Ngapain', 'required');
          $this->form_validation->set_rules('mail', 'Surel', 'required');
          $this->form_validation->set_rules('pass', 'Password', 'required');

          //Detecting Are Value is Valid?
          if ($this->form_validation->run() == TRUE){
            //get a post
            $mail = $this->input->post("mail",TRUE);
            $pass = $this->input->post("pass",TRUE);
            $id = $this->input->post("id",TRUE);
            $to = $this->input->post("todo",TRUE);

            //Cek kebenaran event
            $cek = $this->waterm->chkev(array('id' => $id));
            if ($cek != FALSE){
              //Cek benenaran User
              $cek1 = $this->waterm->login(array('surel' => $mail), array('sandi' => $pass));

              if ($cek1 != FALSE){
                //If Ok account is true is here
                if ($to == 1) {
                  //check if already like?
                  $cek2 = $this->waterm->chsm("suka_event", array('id_event' => $id), array('surel' => $mail));

                  if($cek2 == FALSE){
                    //if no already exexute to like
                    $cek3 = $this->waterm->addsm("suka_event", array('id_event' => $id,'surel' => $mail));
                  }else{
                    //if already exexute to unlike
                    $cek3 = $this->waterm->delsm("suka_event", array('id_event' => $id,'surel' => $mail));
                  }
                  if($cek3 != FALSE){
                    //Ok
                    $stat = array('status' => '200','msg' => 'OK');
                  }else{
                    //Error
                    $stat = array('status' => '400','msg' => 'INTERNAL SERVER ERROR!');
                  }
                }elseif ($to == 2) {
                  $cek2 = $this->waterm->chsm("share_event", array('id_event' => $id), array('surel' => $mail));
                  if($cek2 == FALSE){
                    //Share is goes here
                    $this->waterm->addsm("share_event", array('id_event' => $id,'surel' => $mail));
                    $stat = array('status' => '200','msg' => 'You Share This..');
                  }else{
                    $stat = array('status' => '200','msg' => 'You Re-Share This..');
                  }
                }else{
                  //Fail Action Because is unknown
                  $stat = array('status' => '400','msg' => 'Unknown Action!');
                }
              }else{
                //Fail Account unknown
                $stat = array('status' => '400','msg' => 'No Logged in!');
              }
            }else{
              //Fail Validation Because no event
              $stat = array('status' => '400','msg' => 'No event!');
            }
          }else{
            //Fail Validation Because illegal input
            $stat = array('status' => '400','msg' => 'Info is Required!');
          }
        break;

        case 'interest'://Add or Update Interest
          //code get
          $sta = $this->input->post("state",TRUE);
          $cty = $this->input->post("city",TRUE);
          
        break;

        case 'geo'://get geo Country
          //code get
          $sta = $this->input->post("state",TRUE);
          $cty = $this->input->post("city",TRUE);

          if(isset($cty)){
            $get = $this->waterm->scfetch(array('District' => $cty), 'Name');
            //Filter
            $hit = array();
            $x = null;
            if($get != FALSE){
              foreach($get as $get){
                $hit[] = $get->Name;
              }
              $stat = array('status' => '200', 'data' => $hit, 'msg' => 'OK' );
            }else{
              $stat = array('status' => '200', 'msg' => 'NO DATA' );
            }
          }elseif(isset($sta)){
            $get = $this->waterm->scfetch(array('CountryCode' => $sta), 'District');
            //Filter
            $hit = array();
            $x = null;
            if($get != FALSE){
              foreach($get as $get){
                if($x != $get->District){
                  $hit[] = $get->District;
                  $x = $get->District;
                }
              }
            $stat = array('status' => '200', 'data' => $hit, 'msg' => 'OK' );
            }else{
              $stat = array('status' => '200', 'msg' => 'NO DATA' ); 
            }
          }else{
            $stat = array('status' => '200', 'data' => $this->waterm->cofetch(), 'msg' => 'OK' );
          }
        break;

        case 'shop': //Get Shop
          $stat = array('status' => '200', 'data' => $this->waterm->fetchdata("jual"), 'msg' => 'OK' );
        break;

        case 'map': //Get Map
          //value is allowable?
          $this->form_validation->set_rules('mail', 'Surel', 'required');
          $this->form_validation->set_rules('pass', 'Password', 'required');

          //Check validation
          if ($this->form_validation->run() == TRUE){
            //get a POST method
            $mail = $this->input->post("mail",TRUE);
            $pass = $this->input->post("pass",TRUE);

            //login check
            $cek = $this->waterm->login(array('surel' => $mail), array('sandi' => $pass));
            if($cek != FALSE){
              $cek2 = $this->waterm->fetchfaf(array('surel' => $mail));
              if($cek2 != FALSE){
                $hit = $cek2;
              }else{
                $hit = FALSE;
              }
              $stat = array('status' => '200', 'data' => $this->waterm->fetchmap("tempatisi", $hit), 'msg' => 'OK' );
            }else{
              //Wrong parameter if not identify
              $stat = array('status' => '400', 'msg' => 'Authentication Failed!');
            }
          }else{
            //Wrong parameter if not identify
            $stat = array('status' => '400', 'msg' => 'Value is Not Allowable!');
          }
        break;

        case 'voucer': //Get Voucer List Avaiable
          $stat = array('status' => '200', 'data' => $this->waterm->fetchdata("voucer"), 'msg' => 'OK' );
        break;

        case 'saved': //Get saved
          //get is value is allowable
          $this->form_validation->set_rules('mail', 'Surel', 'required');
          $this->form_validation->set_rules('pass', 'Password', 'required');

          //Check validation
          if ($this->form_validation->run() == TRUE){
            //get a POST method
            $mail = $this->input->post("mail",TRUE);
            $pass = $this->input->post("pass",TRUE);

            //login check
            $cek = $this->waterm->login(array('surel' => $mail), array('sandi' => $pass));

            //Cek login is Ok
            if($cek != FALSE){
              //Login OK
              $data = array();
              $g = 0;
              for($x=0; $x <= 11; $x++){
                $d = 0;
                $a = strtotime("-$x month");
                $b = date("Y-m", $a);
                $y = date("Y", $a);
                $m = date("m", $a);
                $day = cal_days_in_month(CAL_GREGORIAN, $m, $y);
                $cout = $this->waterm->saved_mon(array('surel' => $mail), $b);
                foreach($cout as $cout){
                  $d = $d + $cout['harian'];
                }
                $data[] = array('bulan' => $m, 'data' => array('botol_bln' => round($d), 'max_botol' => $day));
                $f = date("Y-m-d");
                $hit = $this->waterm->saved_day(array('surel' => $mail), array('tgl' => $f));
                foreach($hit as $hit){
                  $g = $hit['harian'];
                }
                $stat = array('status' => '400', 'penggunaan' => array_merge(array('monthly' => $data), array('daily' => $g)), 'msg' => 'OK');
              }
            }else{
              //No Auth!
              $stat = array('status' => '400', 'msg' => 'You dont have permission!');
            }
          }else{
            //Requipment is min
            $stat = array('status' => '400', 'msg' => 'Failed requipment!');
          }
        break;

        default:
          //Wrong parameter if not identify
          $stat = array('status' => '400', 'msg' => 'Wrong Parameters');
        break;
      }
      //encode and print the $stat to json for android
      echo json_encode($stat);
    }
}
//Credits: DzEN/DzEN[DENZVELOPER] (c)2018 For Codelabs [Codeigniter]
