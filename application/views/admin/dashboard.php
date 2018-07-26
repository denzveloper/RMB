<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard Of Refill My Bottle(Administrator)</title>
</head>
<body>
    <h1>Welcome to Administrator Page. Hi <?php echo $this->session->userdata("fnam");?>!</h1>
    <a href="<?php echo base_url('index.php/login/logout');?>"><button>Logout</button></a>
    <h3>Tambah Tempat</h3>
        //tambah tempat belum terjamah
    <hr>
    <h3>Saran Tempat baru</h3>
        //Lihat saran dan memindahkan ke Peta
    <hr>
    <h3>Tambah Pengguna</h3>
        //Tabah pengguna
    <hr>
    <h3>Manage Tempat</h3>
        //Atur tempat seperti di owner(kayanya bakalan gak ada|Satus: dipikirkan)
    <hr>
    <h3>Manage User</h3>
        //Atur pengguna aktif
    <hr>
    <h3>Pengaturan Profil</h3>
        //Atur profil seperti sandi dan lainnya
    <br>
</body>
</html>