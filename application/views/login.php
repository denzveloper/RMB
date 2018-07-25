<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login Page Landing</title>
    <style>
        .content {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 10px;
            align: center;
        }
    </style>
</head>
<body>
    <div class="content">
        <h2>Refill My Bottle</h2>
        <h4>To sign in, a registered account is needed</h4>
        <form method="POST" action="<?php echo base_url('index.php/login'); ?>">
            <?php if(isset($error)) { echo $error; }; ?>
            <table frame="box">
                <tr>
                    <td>Email</td>
                    <td><input type="email" name="mail" placeholder="Email" required autocomplete="off"><br><?php echo form_error('mail'); ?></td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td><input type="password" name="pass" placeholder="Password" required autocomplete="off"><br><?php echo form_error('pass'); ?></td>
                </tr>
                <tr>
                    <td colspan='2' style="text-align:right"><button name="btn-login" type="submit">Masuk</button>&nbsp;&#183;&nbsp;<button name="reset" type="reset">Reset</button></td>
                </tr>
            </table>
        </form>
    </div>
</body>
</html>