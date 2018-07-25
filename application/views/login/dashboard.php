<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dashboard Of <?php echo $this->session->userdata("fnam");?></title>
</head>
<body>
    <h3>Welcome <?php echo $this->session->userdata("fnam");?>!</h3>
    <a href="<?php echo base_url('index.php/login/logout');?>"><button>Logout</button></a>
    
</body>
</html>