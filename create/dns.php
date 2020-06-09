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

if(isset($dnsenabled) && $dnsenabled != 'true'){ header("Location: ../error-pages/403.html"); exit(); }

$v_1 = $_POST['v_domain'];
$v_2 = $_POST['v_ip'];
$v_3 = $_POST['v_ns1'];
$v_4 = $_POST['v_ns2'];
$v_5 = $_POST['v_ns3'];
$v_6 = $_POST['v_ns4'];
$v_7 = $_POST['v_ns5'];
$v_8 = $_POST['v_ns6'];
$v_9 = $_POST['v_ns7'];
$v_10 = $_POST['v_ns8'];
if (!empty($_POST['v_cf'])) {
    $v_11 = 'yes';
} else {
    $v_11 = 'no';
}
$v_12 = $_POST['v_cf_level'];
$v_13 = $_POST['v_cf_ssl'];

if ((!isset($_POST['v_domain'])) || ($_POST['v_domain'] == '')) { header('Location: ../add/dns.php?error=1'); exit();}
elseif ((!isset($_POST['v_ip'])) || ($_POST['v_ip'] == '')) { header('Location: ../add/dns.php?error=1'); exit();}
elseif ((!isset($_POST['v_ns1'])) || ($_POST['v_ns1'] == '')) { header('Location: ../add/dns.php?error=1'); exit();}
elseif ((!isset($_POST['v_ns2'])) || ($_POST['v_ns2'] == '')) { header('Location: ../add/dns.php?error=1'); exit();}

$postvars = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-add-dns-domain','arg1' => $username,'arg2' => $v_1, 'arg3' => $v_2, 'arg4' => $v_3, 'arg5' => $v_4, 'arg6' => $v_5, 'arg7' => $v_6, 'arg8' => $v_7, 'arg9' => $v_8, 'arg10' => $v_9, 'arg11' => $v_10, 'arg12' => 'no');

$curl0 = curl_init();
curl_setopt($curl0, CURLOPT_URL, $vst_url);
curl_setopt($curl0, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl0, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl0, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl0, CURLOPT_POST, true);
curl_setopt($curl0, CURLOPT_POSTFIELDS, http_build_query($postvars));
$r1 = curl_exec($curl0);

if ($v_11 == "yes") {

    header("Location: cloudflare.php?domain=" . $v_1 . "&cflevel=" . $v_12 . "&cfssl=" . $v_13);
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

        <form id="form" action="../list/dns.php" method="post">
            <?php 
            echo '<input type="hidden" name="addcode" value="'.$r1.'">';
            ?>
        </form>
        <script type="text/javascript">
            document.getElementById('form').submit();
        </script>
    </body>
    <script src="../plugins/components/jquery/jquery.min.js"></script>
</html>