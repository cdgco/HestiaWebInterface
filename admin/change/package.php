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

$configlocation = "../../includes/";
if (file_exists( '../../includes/config.php' )) { require( '../../includes/includes.php'); }  else { header( 'Location: ../../install' ); exit();};
if(base64_decode($_SESSION['loggedin']) == 'true') {}
else { header('Location: ../../login.php'); exit(); }
if($username != 'admin') { header("Location: ../../"); exit(); }

if(isset($adminenabled) && $adminenabled != 'true'){ header("Location: ../../error-pages/403.html"); exit(); }
if(isset($apienabled) && $apienabled == 'true'){ header("Location: ../../error-pages/403.html"); exit(); }

if ((!isset($_POST['v_package-name'])) || (!isset($_POST['v_webtpl'])) || (!isset($_POST['v_prxtpl'])) || (!isset($_POST['v_dnstpl'])) || (!isset($_POST['v_web-domains'])) || (!isset($_POST['v_web-aliases'])) || (!isset($_POST['v_dns-domains'])) || (!isset($_POST['v_dns-records'])) || (!isset($_POST['v_mail-domains'])) || (!isset($_POST['v_mail-accounts'])) || (!isset($_POST['v_databases'])) || (!isset($_POST['v_cron-jobs'])) || (!isset($_POST['v_quota'])) || (!isset($_POST['v_bandwidth'])) || (!isset($_POST['ns1'])) || (!isset($_POST['ns2'])) || (!isset($_POST['ssh'])) || (!isset($_POST['v_backups']))) { header('Location: ../add/package.php?error=1'); exit();}

function ftp_file_put_contents($remote_file, $file_string) {

    $ftp_server=HESTIA_HOST_ADDRESS; 
    $ftp_user_name=HESTIA_ADMIN_UNAME; 
    $ftp_user_pass=HESTIA_ADMIN_PW;
    $local_file=fopen('php://temp', 'r+');
    fwrite($local_file, $file_string);
    rewind($local_file);       
    $ftp_conn=ftp_connect($ftp_server); 
    @$login_result=ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass); 
    if($login_result) ftp_fput($ftp_conn, $remote_file, $local_file, FTP_ASCII);
    ftp_close($ftp_conn);
    fclose($local_file); }

$nsfull = '';
if(isset($_POST['ns3']) && $_POST['ns3'] != ''){ $nsfull = "," . $_POST['ns3'];}
if(isset($_POST['ns3']) && $_POST['ns3'] != '' && isset($_POST['ns4']) && $_POST['ns4'] != ''){ $nsfull = "," . $_POST['ns3'] . "," . $_POST['ns4'];}
if(isset($_POST['ns3']) && $_POST['ns3'] != '' && isset($_POST['ns4']) && $_POST['ns4'] != '' && isset($_POST['ns5']) && $_POST['ns5'] != ''){ $nsfull = "," . $_POST['ns3'] . "," . $_POST['ns4'] . "," . $_POST['ns5']; }
if(isset($_POST['ns3']) && $_POST['ns3'] != '' && isset($_POST['ns4']) && $_POST['ns4'] != '' && isset($_POST['ns5']) && $_POST['ns5'] != '' && isset($_POST['ns6']) && $_POST['ns6'] != ''){ $nsfull = "," . $_POST['ns3'] . "," . $_POST['ns4'] . "," . $_POST['ns5'] . "," . $_POST['ns6']; }
if(isset($_POST['ns3']) && $_POST['ns3'] != '' && isset($_POST['ns4']) && $_POST['ns4'] != '' && isset($_POST['ns5']) && $_POST['ns5'] != '' && isset($_POST['ns6']) && $_POST['ns6'] != '' && isset($_POST['ns7']) && $_POST['ns7'] != ''){ $nsfull = "," . $_POST['ns3'] . "," . $_POST['ns4'] . "," . $_POST['ns5'] . "," . $_POST['ns6'] . "," . $_POST['ns7']; }
if(isset($_POST['ns3']) && $_POST['ns3'] != '' && isset($_POST['ns4']) && $_POST['ns4'] != '' && isset($_POST['ns5']) && $_POST['ns5'] != '' && isset($_POST['ns6']) && $_POST['ns6'] != '' && isset($_POST['ns7']) && $_POST['ns7'] != '' && isset($_POST['ns8']) && $_POST['ns8'] != ''){ $nsfull = "," . $_POST['ns3'] . "," . $_POST['ns4'] . "," . $_POST['ns5'] . "," . $_POST['ns6'] . "," . $_POST['ns7'] . "," . $_POST['ns8']; }

$writestr = "WEB_TEMPLATE='".$_POST['v_webtpl']."'\n
PROXY_TEMPLATE='".$_POST['v_prxtpl']."'\n
DNS_TEMPLATE='".$_POST['v_dnstpl']."'\n
WEB_DOMAINS='".$_POST['v_web-domains']."'\n
WEB_ALIASES='".$_POST['v_web-aliases']."'\n
DNS_DOMAINS='".$_POST['v_dns-domains']."'\n
DNS_RECORDS='".$_POST['v_dns-records']."'\n
MAIL_DOMAINS='".$_POST['v_mail-domains']."'\n
MAIL_ACCOUNTS='".$_POST['v_mail-accounts']."'\n
DATABASES='".$_POST['v_databases']."'\n
CRON_JOBS='".$_POST['v_cron-jobs']."'\n
DISK_QUOTA='".$_POST['v_quota']."'\n
BANDWIDTH='".$_POST['v_bandwidth']."'\n
NS='".$_POST['ns1'].",".$_POST['ns2'].$nsfull."'\n
SHELL='".$_POST['ssh']."'\n
BACKUPS='".$_POST['v_backups']."'\n
TIME='".date('H:i:s')."'\n
DATE='".date('Y-m-d')."'\n";
$writestr = str_replace("\r\n", "\n",  $writestr);
ftp_file_put_contents($_POST['v_package-name'] . '.pkg', $writestr);

$postvars1 = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-add-user-package','arg1' => "/home/admin/",'arg2' => $_POST['v_package-name'], 'arg3' => 'yes');

$curl1 = curl_init();
curl_setopt($curl1, CURLOPT_URL, $vst_url);
curl_setopt($curl1, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl1, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl1, CURLOPT_POST, true);
curl_setopt($curl1, CURLOPT_POSTFIELDS, http_build_query($postvars1));
$r1 = curl_exec($curl1);

$postvars2 = array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'returncode' => 'yes','cmd' => 'v-update-user-package','arg1' => $_POST['v_package-name']);

$curl2 = curl_init();
curl_setopt($curl2, CURLOPT_URL, $vst_url);
curl_setopt($curl2, CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl2, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl2, CURLOPT_POST, true);
curl_setopt($curl2, CURLOPT_POSTFIELDS, http_build_query($postvars2));
$r2 = curl_exec($curl2);

sleep(3); // Give Hestia some time to process files before deleting.

$ftp_server=HESTIA_HOST_ADDRESS; 
$ftp_user_name=HESTIA_ADMIN_UNAME; 
$ftp_user_pass=HESTIA_ADMIN_PW;
$ftp_conn=ftp_connect($ftp_server); 
$login_result = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass);

ftp_delete($ftp_conn, $_POST['v_package-name'] . '.pkg');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link href="../../css/style.css" rel="stylesheet">
    </head>
    <body class="fix-header">
        <div class="preloader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
            </svg>
        </div>

        <form id="form" action="../edit/package.php?package=<?php echo $_POST['v_package-name']; ?>" method="post">
            <?php 
                echo '<input type="hidden" name="r1" value="'.$r1.'">';
                echo '<input type="hidden" name="r2" value="'.$r2.'">';
            ?>
        </form>
        <script type="text/javascript">
            document.getElementById('form').submit();
        </script>
    </body>
    <script src="../../plugins/components/jquery/jquery.min.js"></script>
</html>