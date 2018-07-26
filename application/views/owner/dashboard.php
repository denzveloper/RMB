<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard Of <?php echo $this->session->userdata("fnam");?></title>
</head>
<body>
    <h1>Welcome <?php echo $this->session->userdata("fnam");?>!</h1>
    <a href="<?php echo base_url('index.php/login/logout');?>"><button>Logout</button></a>
    <h3>Tambah poin</h3>
        //nambah poin ke user
    <hr>
    <h3>Atur Tempat</h3>
        //Berisi Pengaturan Nama, deskripsi, poin, dll
    <hr>
    <h3>Pengaturan Profil</h3>
        //Pengaturan Nama profil dan sandi
    <hr>
</body>
</html>