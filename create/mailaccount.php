<?php

/** 
*
* Hestia Web Interface
*
* Copyright (C) 2020 Carter Roeser <carter@cdgtech.one>
* https://cdgco.github.io/HestiaWebInterface
*
* Hestia Web Interface is free software: you can redistribute it and/or modify
* it under the terms of version 3 of the GNU General Public License as published 
* by the Free Software Foundation.
*
* Hestia Web Interface is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with Hestia Web Interface.  If not, see
* <https://github.com/cdgco/HestiaWebInterface/blob/master/LICENSE>.
*
*/

session_set_cookie_params(['samesite' => 'none']); session_start();
$configlocation = "../includes/";
if (file_exists( '../includes/config.php' )) { require( '../includes/includes.php'); }  else { header( 'Location: ../install' ); exit();};
if(base64_decode($_SESSION['loggedin']) == 'true') {}
else { header('Location: ../login.php'); exit(); }

if(isset($mailenabled) && $mailenabled != 'true'){ header("Location: ../error-pages/403.html"); exit(); }
require '../plugins/components/phpmailer/src/PHPMailer.php';
require '../plugins/components/phpmailer/src/SMTP.php';
require '../plugins/components/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

$v_domain = $_POST['v_domain'];
$v_account = $_POST['v_account'];

// Check forward-only option
if (!empty($_POST['v_fwd_only'])) {
    $v_fwd_only = 'yes';
} else {
    $v_fwd_only = 'no';
}


// Check autoreply option
if (!empty($_POST['v_autoreply'])) {
    $v_autoreply = 'yes';
} else {
    $v_autoreply = 'no';
}

if ((!isset($_POST['v_domain'])) || ($_POST['v_domain'] == '')) { header('Location: ../list/mail.php?error=1'); exit();}
elseif ((!isset($_POST['v_account'])) || ($_POST['v_account'] == '')) { header('Location: ../add/mailaccount.php?error=1&domain=' . $v_domain); exit();}
elseif ((!isset($_POST['password'])) || ($_POST['password'] == '')) { header('Location: ../add/mailaccount.php?error=1&domain=' . $v_domain); exit();}
else {
    $postvars0 = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-add-mail-account','arg1' => $username,'arg2' => $_POST['v_domain'], 'arg3' => $_POST['v_account'], 'arg4' => $_POST['password'], 'arg5' => $_POST['v_quota']);

    $curl0 = curl_init();
    curl_setopt($curl0, CURLOPT_URL, $vst_url);
    curl_setopt($curl0, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl0, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl0, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl0, CURLOPT_POST, true);
    curl_setopt($curl0, CURLOPT_POSTFIELDS, http_build_query($postvars0));
    $r0 = curl_exec($curl0);

    if ($_POST['v_alias']){
        $postvars1 = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-add-mail-account-alias','arg1' => $username,'arg2' => $_POST['v_domain'], 'arg3' => $_POST['v_account'], 'arg4' => $_POST['v_alias']);

        $curl1 = curl_init();
        curl_setopt($curl1, CURLOPT_URL, $vst_url);
        curl_setopt($curl1, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl1, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl1, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl1, CURLOPT_POST, true);
        curl_setopt($curl1, CURLOPT_POSTFIELDS, http_build_query($postvars1));
        $r1 = curl_exec($curl1);
    } else { $r1 = '0'; }
    if ($_POST['v_fwd']){
        $postvars2 = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-add-mail-account-forward','arg1' => $username,'arg2' => $_POST['v_domain'], 'arg3' => $_POST['v_account'], 'arg4' => $_POST['v_fwd']);

        $curl2 = curl_init();
        curl_setopt($curl2, CURLOPT_URL, $vst_url);
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl2, CURLOPT_POST, true);
        curl_setopt($curl2, CURLOPT_POSTFIELDS, http_build_query($postvars2));
        $r2 = curl_exec($curl2);
    } else { $r2 = '0'; }
    if (isset($_POST['v_fwd']) && $v_fwd_only == 'yes' ) {
        $postvars3 = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-add-mail-account-fwd-only','arg1' => $username,'arg2' => $_POST['v_domain'], 'arg3' => $_POST['v_account']);

        $curl3 = curl_init();
        curl_setopt($curl3, CURLOPT_URL, $vst_url);
        curl_setopt($curl3, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl3, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl3, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl3, CURLOPT_POST, true);
        curl_setopt($curl3, CURLOPT_POSTFIELDS, http_build_query($postvars3));
        $r3 = curl_exec($curl3);
    } else { $r3 = '0'; }
    if ($v_autoreply == 'yes' && isset($_POST['v_message'])) {
        $postvars4 = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-add-mail-account-autoreply','arg1' => $username,'arg2' => $_POST['v_domain'], 'arg3' => $_POST['v_account'], 'arg4' => $_POST['v_message']);

        $curl4 = curl_init();
        curl_setopt($curl4, CURLOPT_URL, $vst_url);
        curl_setopt($curl4, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl4, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl4, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl4, CURLOPT_POST, true);
        curl_setopt($curl4, CURLOPT_POSTFIELDS, http_build_query($postvars4));
        $r4 = curl_exec($curl4);
    } else { $r4 = '0'; }
}
if($phpmailenabled == "true" && isset($_POST['v_sendemail']) && $_POST['v_sendemail'] != '') {
    
    if($webmailurl != ''){ $webmailurlx0 = "Webmail URL: <a href='" . $webmailurl . "'>" . $webmailurl . "</a>"; $webmailurlx1 = "Webmail URL: " . $webmailurl; } 
    else { $webmailurlx0 = ''; $webmailurlx1 = ''; }
    $mail = new PHPMailer;
    $mail->setFrom($mailfrom, $mailname);
    $mail->addAddress($_POST['v_sendemail']);
    $mail->Subject = 'Email Credentials';
    $mail->Body = 'Username: ' . $_POST['v_account'] . '@' . $_POST['v_domain'] . '<br>IMAP Hostname: ' . addslashes(HESTIA_HOST_ADDRESS) . '<br>IMAP Port: 143<br>IMAP Security: STARTTLS<br>IMAP Auth Method: Normal Password<br>SMTP Hostname: ' . addslashes(HESTIA_HOST_ADDRESS) . '<br>SMTP Port: 587<br>SMTP Security: STARTTLS<br>SMTP Auth Method: Normal Password<br>Password: ' . $_POST['password'] . '<br>' . addslashes($webmailurlx1); 
    $mail->AltBody = 'Username: ' . $_POST['v_account'] . '@' . $_POST['v_domain'] . '\nIMAP Hostname: ' . addslashes(HESTIA_HOST_ADDRESS) . '\nIMAP Port: 143\nIMAP Security: STARTTLS\nIMAP Auth Method: Normal Password\nSMTP Hostname: ' . addslashes(HESTIA_HOST_ADDRESS) . '\nSMTP Port: 587\nSMTP Security: STARTTLS\nSMTP Auth Method: Normal Password\nPassword: ' . $_POST['password'] . '\n' . addslashes($webmailurlx0);

    if($smtpenabled == "true" && $smtphost != '' && $smtpport != '') {
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host = $smtphost;
        $mail->Port = $smtpport;
        if($smtpauth == "true") {
            $mail->SMTPAuth = true;
            $mail->Username = $smtpuname;
            $mail->Password = $smtppw;
        }
        if($smtpenc == 'tls') {
         $mail->SMTPSecure = 'tls';  
        }
        elseif($smtpenc == 'ssl') {
         $mail->SMTPSecure = 'ssl';  
        }
    }
    $mail->send();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link href="../css/style.css" rel="stylesheet">
    </head>
    <body class="fix-header">
        <div class="preloader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
            </svg>
        </div>

        <form id="form" action="../list/maildomain.php?domain=<?php echo $_POST['v_domain']; ?>" method="post">
            <?php 
            echo '<input type="hidden" name="a1" value="'.$r0.'">';
            echo '<input type="hidden" name="a2" value="'.$r1.'">';
            echo '<input type="hidden" name="a3" value="'.$r2.'">';
            echo '<input type="hidden" name="a4" value="'.$r3.'">';
            echo '<input type="hidden" name="a5" value="'.$r4.'">';
            ?>
        </form>
        <script type="text/javascript">
            document.getElementById('form').submit();
        </script>
    </body>
    <script src="../plugins/components/jquery/jquery.min.js"></script>
</html>
