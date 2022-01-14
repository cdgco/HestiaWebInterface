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

$configlocation = "../includes/";
if (file_exists( '../includes/config.php' )) { require( '../includes/includes.php'); }  else { header( 'Location: ../install' ); exit();};

if(base64_decode($_SESSION['loggedin']) == 'true') {}
else { header('Location: ../login.php?to=edit/domain.php'.$urlquery.$_SERVER['QUERY_STRING']); exit(); }

if(isset($webenabled) && $webenabled != 'true'){ header("Location: ../error-pages/403.html"); exit(); }

$requestdomain = $_GET['domain'];

if (isset($requestdomain) && $requestdomain != '') {}
else { header('Location: ../list/web.php'); exit(); }

$postvars = array(
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user','arg1' => $username,'arg2' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-web-domain','arg1' => $username,'arg2' => $requestdomain, 'arg3' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-web-templates','arg1' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-web-templates-proxy','arg1' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user-ips','arg1' => $username,'arg2' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-web-domain-ssl','arg1' => $username,'arg2' => $requestdomain,'arg3' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-web-stats','arg1' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-sys-config','arg1' => 'json'));

$curl0 = curl_init();
$curl1 = curl_init();
$curl2 = curl_init();
$curl3 = curl_init();
$curl4 = curl_init();
$curl5 = curl_init();
$curl6 = curl_init();
$curl7 = curl_init();
$curlstart = 0; 

while($curlstart <= 7) {
    curl_setopt(${'curl' . $curlstart}, CURLOPT_URL, $vst_url);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_RETURNTRANSFER,true);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_POST, true);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_POSTFIELDS, http_build_query($postvars[$curlstart]));
    $curlstart++;
} 
$admindata = json_decode(curl_exec($curl0), true)[$username];
$useremail = $admindata['CONTACT'];
$domainname = array_keys(json_decode(curl_exec($curl1), true));
$domaindata = array_values(json_decode(curl_exec($curl1), true));
$webtemplates = array_values(json_decode(curl_exec($curl2), true));
$proxytemplates = array_values(json_decode(curl_exec($curl3), true));
$userips = array_keys(json_decode(curl_exec($curl4), true));
$domainssl = array_values(json_decode(curl_exec($curl5), true));
$webstats = array_values(json_decode(curl_exec($curl6), true));
$sysconfigdata = array_values(json_decode(curl_exec($curl7), true))[0];
if ($domainname[0] == '') { header('Location: ../list/web.php'); }
if(isset($admindata['LANGUAGE'])){ $locale = $ulang[$admindata['LANGUAGE']]; }
_setlocale('LC_CTYPE', $locale); _setlocale('LC_MESSAGES', $locale);
_bindtextdomain('messages', '../locale');
_textdomain('messages');

foreach ($plugins as $result) {
    if (file_exists('../plugins/' . $result)) {
        if (file_exists('../plugins/' . $result . '/manifest.xml')) {
            $get = file_get_contents('../plugins/' . $result . '/manifest.xml');
            $xml   = simplexml_load_string($get, 'SimpleXMLElement', LIBXML_NOCDATA);
            $arr = json_decode(json_encode((array)$xml), TRUE);
            if (isset($arr['name']) && !empty($arr['name']) && isset($arr['fa-icon']) && !empty($arr['fa-icon']) && isset($arr['section']) && !empty($arr['section']) && isset($arr['admin-only']) && !empty($arr['admin-only']) && isset($arr['new-tab']) && !empty($arr['new-tab']) && isset($arr['hide']) && !empty($arr['hide'])){
                array_push($pluginlinks,$result);
                array_push($pluginnames,$arr['name']);
                array_push($pluginicons,$arr['fa-icon']);
                array_push($pluginsections,$arr['section']);
                array_push($pluginadminonly,$arr['admin-only']);
                array_push($pluginnewtab,$arr['new-tab']);
                array_push($pluginhide,$arr['hide']);
            }
        }    
    }
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/ico" href="../plugins/images/<?php echo $cpfavicon; ?>">
        <title><?php echo $sitetitle; ?> - <?php echo __("Web"); ?></title>
        <link href="../plugins/components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../plugins/components/metismenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="../plugins/components/select2/select2.min.css" rel="stylesheet">
        <link href="../plugins/components/animate.css/animate.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../plugins/components/sweetalert2/sweetalert2.min.css" />
        <link href="../css/style.css" rel="stylesheet">
        <link href="../css/colors/<?php if(isset($_COOKIE['theme']) && $themecolor != 'custom.css') { echo base64_decode($_COOKIE['theme']); } else {echo $themecolor; } ?>" id="theme" rel="stylesheet">
        <?php if($themecolor == "custom.css") { require( '../css/colors/custom.php'); } ?>
        <style>
            @media screen and (max-width: 1199px) {
                .resone { display:none !important;}
            }  
            @media screen and (max-width: 991px) {
                .restwo { display:none !important;}
            }    
            @media screen and (max-width: 767px) {
                .resfour { display:none !important; }
                h2 { font-size: 4vw; }
                .bg-title ul.side-icon-text {
                    position: relative;
                    top: -20px;
                }
                h4.page-title {
                    position: relative;
                    top: 20px;
                }
            }
            @media screen and (max-width: 540px) {
                .resthree { display:none !important;}
            } 
        </style>
        <?php if(GOOGLE_ANALYTICS_ID != ''){ echo "<script async src='https://www.googletagmanager.com/gtag/js?id=" . GOOGLE_ANALYTICS_ID . "'></script>
        <script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', '" . GOOGLE_ANALYTICS_ID . "');</script>"; } ?> 
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="fix-header" onload="checkDiv();checkDiv2();checkDiv3();checkDiv4();showauth();">
        <div class="preloader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
            </svg>
        </div>
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top m-b-0">
                <div class="navbar-header">
                    <div class="top-left-part">
                        <a class="logo" href="../index.php">
                            <img src="../plugins/images/<?php echo $cpicon; ?>" alt="home" class="logo-1 dark-logo" />
                            <img src="../plugins/images/<?php echo $cplogo; ?>" alt="home" class="hidden-xs dark-logo" />
                        </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-left">
                        <li><a href="javascript:void(0)" class="open-close waves-effect waves-light visible-xs"><i class="ti-close ti-menu"></i></a></li>
                        <?php notifications(); ?>
                    </ul>
                    <ul class="nav navbar-top-links navbar-right pull-right">
                        <li>
                            <form class="app-search m-r-10" id="searchform" action="../process/search.php" method="get">
                                <input type="text" placeholder="<?php echo __("Search..."); ?>" class="form-control" name="q"> <a href="javascript:void(0);" onclick="document.getElementById('searchform').submit();"><i class="fa fa-search"></i></a> </form>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"><b class="hidden-xs"><?php print_r($displayname); ?></b><span class="caret"></span> </a>
                            <ul class="dropdown-menu dropdown-user animated flipInY">
                                <li>
                                    <div class="dw-user-box">
                                        <div class="u-text">
                                            <h4><?php print_r($displayname); ?></h4>
                                            <p class="text-muted"><?php print_r($useremail); ?></p></div>
                                    </div>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li><a href="../profile.php"><i class="ti-home"></i> <?php echo __("My Account"); ?></a></li>
                                <li><a href="../profile.php?settings=open"><i class="ti-settings"></i> <?php echo __("Account Settings"); ?></a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="../process/logout.php"><i class="fa fa-power-off"></i> <?php echo __("Logout"); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav slimscrollsidebar">
                    <div class="sidebar-head">
                        <h3>
                            <span class="fa-fw open-close">
                                <i class="ti-menu hidden-xs"></i>
                                <i class="ti-close visible-xs"></i>
                            </span> 
                            <span class="hide-menu"><?php echo __("Navigation"); ?></span>
                        </h3>  
                    </div>
                    <ul class="nav" id="side-menu">
                        <?php indexMenu("../"); 
                              adminMenu("../admin/list/", "");
                              profileMenu("../");
                              primaryMenu("../list/", "../process/", "web");
                        ?>
                    </ul>
                </div>
            </div>
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row bg-title">
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                            <h4 class="page-title"><?php echo __("Edit Web Domain"); ?></h4>
                        </div>
                        <ul class="side-icon-text pull-right">
                            <li style="position: relative;top: -3px;">
                                <a onclick="confirmDelete();" style="cursor: pointer;"><span class="circle circle-sm bg-danger di"><i class="ti-trash"></i></span><span class="resfour"><wrapper class="restwo"><?php echo __("Delete"); ?> </wrapper><?php echo __("Domain"); ?></span>
                                </a>
                            </li>
                        </ul>
                        <?php headerad(); ?>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12">
                            <div class="panel"> 
                                <div class="sk-chat-widgets">
                                    <div class="panel panel-themecolor">
                                        <div class="panel-heading">
                                            <center><?php echo __("DOMAIN"); ?></center>
                                        </div>
                                        <div class="panel-body">
                                            <center><h2><?php print_r($domainname[0]); ?></h2></center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 resone">
                            <div class="panel">
                                <div class="sk-chat-widgets">
                                    <div class="panel panel-themecolor">
                                        <div class="panel-heading">
                                            <center><?php echo __("CREATED"); ?></center>
                                        </div>
                                        <div class="panel-body">
                                            <center>
                                                <h2>
                                                    <?php $date=date_create($domaindata[0]['DATE'] . ' ' . $domaindata[0]['TIME']); echo date_format($date,"F j, Y - g:i A"); ?>
                                                </h2>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 restwo">
                            <div class="panel">
                                <div class="sk-chat-widgets">
                                    <div class="panel panel-themecolor">
                                        <div class="panel-heading">
                                            <center><?php echo __("STATUS"); ?></center>
                                        </div>
                                        <div class="panel-body">
                                            <center>
                                                <h2>
                                                    <?php if ($domaindata[0]['SUSPENDED'] == 'no') {echo __("Active");} else {echo __("Suspended");}?>
                                                </h2>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="white-box">
                                <form class="form-horizontal form-material" autocomplete="off" id="form" method="post" action="../change/domain.php">
                                    <div class="form-group" style="overflow: visible;">
                                        <label class="col-md-12"><?php echo __("IP Address"); ?></label>
                                        <div class="col-md-12">
                                            <input type="hidden" name="v_domain" value="<?php echo $requestdomain; ?>">
                                            <input type="hidden" name="v_ip-x" value="<?php echo $domaindata[0]['IP']; ?>">
                                            <select class="form-control select1 select2" name="v_ip" id="select1">
                                                <?php
                                                if($userips[0] != '') {
                                                    $x4 = 0; 

                                                    do {
                                                        echo '<option value="' . $userips[$x4] . '">' . $userips[$x4] . '</option>';
                                                        $x4++;
                                                    } while ($userips[$x4] != ''); }

                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Aliases"); ?></label>
                                        <div class="col-md-12">
                                            <input type="hidden" name="v_alias-x" value="<?php echo $domaindata[0]['ALIAS']; ?>"> 
                                            <textarea class="form-control" rows="4" name="v_alias"><?php 
                                                $aliasArray = explode(',', ($domaindata[0]['ALIAS']));

                                                foreach ($aliasArray as &$value) {
                                                    $value = $value . "&#013;&#010;";
                                                }
                                                foreach($aliasArray as $val) {
                                                    echo $val;
                                                } ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Web Template"); ?></label>
                                        <div class="col-md-12">
                                            <input type="hidden" name="v_tpl-x" value="<?php echo $domaindata[0]['TPL']; ?>">
                                            <select class="form-control select2" name="v_tpl" id="select2"><?php
                                                if($webtemplates[0] != '') {
                                                    $x1 = 0; 

                                                    do {
                                                        echo '<option value="' . $webtemplates[$x1] . '">' . $webtemplates[$x1] . '</option>';
                                                        $x1++;
                                                    } while ($webtemplates[$x1] != ''); }

                                                ?></select>
                                        </div>
                                    </div>
                                    <?php if($sysconfigdata['PROXY_SYSTEM'] != '') { echo ""; ?>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Proxy Support"); ?></label>
                                        <div class="col-md-12">
                                            <div class="checkbox checkbox-info">
                                                <input type="hidden" name="v_prxenabled-x" value="<?php if($domaindata[0]['PROXY'] != '') {echo 'yes';} else { echo 'no'; } ?>">
                                                <input id="checkbox4" type="checkbox" name="v_prxenabled" onclick="checkDiv();" <?php if($domaindata[0]['PROXY'] != '') {echo 'checked';} ?> >
                                                <label for="checkbox4"> <?php echo __("Enabled"); ?> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="prxy-div" style="margin-left: 4%;">
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("Proxy Template"); ?></label>
                                            <div class="col-md-12">
                                                <input type="hidden" name="v_prxtpl-x" value="<?php echo $domaindata[0]['PROXY']; ?>">
                                                <select class="form-control select3 select2" name="v_prxtpl" id="select3">
                                                    <?php
                                                    if($proxytemplates[0] != '') {
                                                        $x2 = 0; 

                                                        do {
                                                            echo '<option value="' . $proxytemplates[$x2] . '">' . $proxytemplates[$x2] . '</option>';
                                                            $x2++;
                                                        } while ($proxytemplates[$x2] != ''); }

                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("Proxy Extensions"); ?></label>
                                            <div class="col-md-12">
                                                <input type="hidden" name="v_prxext-x" value="<?php echo $domaindata[0]['PROXY_EXT']; ?>">
                                                <textarea class="form-control" rows="2" id="prxext" name="v_prxext"><?php echo $domaindata[0]['PROXY_EXT']; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <?php echo ""; }?>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("SSL Support"); ?></label>
                                        <div class="col-md-12">
                                            <div class="checkbox checkbox-info">
                                                <input type="hidden" name="v_sslenabled-x" value="<?php echo $domaindata[0]['SSL']; ?>">
                                                <input id="checkbox5" type="checkbox" name="v_sslenabled" onclick="checkDiv2();" <?php if($domaindata[0]['SSL'] == 'no') {} else {echo 'checked';} ?> >
                                                <label for="checkbox5"> <?php echo __("Enabled"); ?> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="ssl-div" style="margin-left: 4%;">
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("Let's Encrypt Support"); ?></label>
                                            <div class="col-md-12">
                                                <div class="checkbox checkbox-info">
                                                    <input type="hidden" name="v_leenabled-x" value="<?php echo $domaindata[0]['LETSENCRYPT']; ?>">
                                                    <input id="checkbox6" name="v_leenabled" type="checkbox" <?php if($domaindata[0]['LETSENCRYPT'] == 'no') {} else {echo 'checked';} ?>>
                                                    <label for="checkbox6"> <?php echo __("Enabled"); ?> </label>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("SSL Directory"); ?></label>
                                            <div class="col-md-12">
                                                <input type="hidden" name="v_ssldir-x" value="<?php echo $domaindata[0]['SSL_HOME']; ?>" >
                                                <select class="form-control form-control-static select2" name="v_ssldir" <?php if(checkService('vsftpd') === false && checkService('proftpd') === false) { echo "disabled"; } ?> <?php if($apienabled == 'true'){ echo "disabled"; } ?>>
                                                    <option value="same" <?php if($domaindata[0]['SSL_HOME'] == 'same') {echo 'selected';} ?>>public_html</option>
                                                    <option value="single" <?php if($domaindata[0]['SSL_HOME'] == 'single') {echo 'selected';} ?>>public_shtml</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("SSL Certificate"); ?> / <a href="../process/generatecsr.php?domain=<?php echo $requestdomain; ?>" target="_blank"><?php echo __("Generate CSR"); ?></a></label>
                                            <div class="col-md-12">
                                                <input type="hidden" name="v_sslcrt-x" value="<?php echo $domainssl[0]['CRT']; ?>">
                                                <textarea class="form-control" rows="4" class="form-control form-control-static" name="v_sslcrt" <?php if($apienabled == 'true'){ echo "disabled"; } ?> <?php if(checkService('vsftpd') === false && checkService('proftpd') === false) { echo "disabled"; } ?>><?php print_r($domainssl[0]['CRT']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("SSL Key"); ?></label>
                                            <div class="col-md-12">
                                                <input type="hidden" name="v_sslkey-x" value="<?php echo $domainssl[0]['KEY']; ?>">
                                                <textarea class="form-control" rows="4" class="form-control form-control-static" name="v_sslkey" <?php if($apienabled == 'true'){ echo "disabled"; } ?> <?php if(checkService('vsftpd') === false && checkService('proftpd') === false) { echo "disabled"; } ?>><?php print_r($domainssl[0]['KEY']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("SSL Certificate Authority / Intermediate"); ?></label>
                                            <div class="col-md-12">
                                                <input type="hidden" name="v_sslca-x" value="<?php echo $domainssl[0]['CA']; ?>">
                                                <textarea class="form-control" rows="4" class="form-control form-control-static" name="v_sslca" <?php if($apienabled == 'true'){ echo "disabled"; } ?> <?php if(checkService('vsftpd') === false && checkService('proftpd') === false) { echo "disabled"; } ?>><?php print_r($domainssl[0]['CA']); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group" style="margin-left: 0.1%;display:<?php if($domainssl[0]['NOT_BEFORE'] != ''){echo 'block';} else { echo 'none';} ?>">
                                            <ul class="list-unstyled">
                                                <li><?php echo __("Subject"); ?>:  <?php print_r($domainssl[0]['SUBJECT']); ?></li>
                                                <li><?php echo __("Aliases"); ?>:  <?php print_r($domainssl[0]['ALIASES']); ?></li>
                                                <li><?php echo __("Not Before"); ?>:  <?php print_r($domainssl[0]['NOT_BEFORE']); ?></li>
                                                <li><?php echo __("Not After"); ?>:  <?php print_r($domainssl[0]['NOT_AFTER']); ?></li>
                                                <li><?php echo __("Signature"); ?>:  <?php print_r($domainssl[0]['SIGNATURE']); ?></li>
                                                <li><?php echo __("Pub Key"); ?>:  <?php print_r($domainssl[0]['PUB_KEY']); ?></li>
                                                <li><?php echo __("Issuer"); ?>:  <?php print_r($domainssl[0]['ISSUER']); ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Web Statistics"); ?></label>
                                        <div class="col-md-12">
                                            <input type="hidden" name="v_webstats-x" value="<?php if ($domaindata[0]['STATS'] == '') {echo 'none'; } else { echo $domaindata[0]['STATS']; } ?>">
                                            <select class="form-control select6 select2" name="v_webstats" onchange="showauth()" id="select6">
                                                <?php
                                                if($webstats[0] != '') {
                                                    $x6 = 0; 

                                                    do {
                                                        echo '<option value="' . $webstats[$x6] . '">' . $webstats[$x6] . '</option>';
                                                        $x6++;
                                                    } while ($webstats[$x6] != ''); }

                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div id="statsauth" style="margin-left: 4%;">
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("Statistics Authorization"); ?></label>
                                            <div class="col-md-12">
                                                <div class="checkbox checkbox-info">
                                                    <input type="hidden" name="v_statsuserenabled-x" value="<?php if($domaindata[0]['STATS_USER'] == '') {echo '';} else {echo 'yes';} ?>">
                                                    <input id="checkbox10" type="checkbox" name="v_statsuserenabled" <?php if($domaindata[0]['STATS_USER'] != '') {echo 'checked';} ?> onclick="checkDiv4();">
                                                    <label for="checkbox10"> <?php echo __("Enabled"); ?> </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="stats-div" style="margin-left: 4%;">
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("Username"); ?></label><br>
                                            <div class="col-md-12">
                                                <input type="hidden" name="v_statsuname-x" value="<?php echo $domaindata[0]['STATS_USER']; ?>">
                                                <input type="text" name="v_statsuname" autocomplete="new-password" class="form-control" value="<?php echo $domaindata[0]['STATS_USER']; ?>"> 
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="v_statspassword" class="col-md-12"><?php echo __("Password"); ?> / <a style="cursor:pointer" onclick="generatePassword(10, 'statspassword', 'tgstats')"> <?php echo __("Generate"); ?></a></label>
                                            <div class="col-md-12 input-group" style="padding-left: 15px;">
                                                <input type="password" autocomplete="new-password" class="form-control form-control-line" name="v_statspassword" id="statspassword">                                    <span class="input-group-btn"> 
                                                <button class="btn btn-inverse" style="margin-right: 15px;" name="Show" onclick="toggler(this, 'statspassword')" id="tgstats" type="button"><i class="ti-eye"></i></button> 
                                                </span>  
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                    
                                    $ftpuser = explode(':', ($domaindata[0]['FTP_USER'])); 
                                    $ftpdir = explode(':', ($domaindata[0]['FTP_PATH'])); 
                                    
                                    if(checkService('vsftpd') !== false || checkService('proftpd') !== false) { echo ""; ?> 

                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Additional FTP"); ?></label>
                                        <div class="col-md-12">
                                            <div class="checkbox checkbox-info">
                                                <input type="hidden" name="v_additionalftpenabled-x" value="<?php if($ftpuser[0]) {echo 'yes';} else { echo 'no'; } ?>">
                                                <input id="checkbox9" type="checkbox" name="v_additionalftpenabled" <?php if($ftpuser[0]) {echo 'checked';} ?> onclick="checkDiv3();">
                                                <label for="checkbox9"><?php echo __("Enabled"); ?></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="ftp-div" style="margin-left: 4%;">
                                        <?php 
                                                                                                               
                                        if($ftpuser[0] != '') { 
                                            $x1 = 0; 
                                            $x11 = $x1 + 1;

                                            do { 
                                                
                                                echo '<input type="hidden" name="v_ftpuname-x'.$x11.'" value="';
                                                $prefix = $uname . '_'; $str = $ftpuser[$x1]; 
                                                if (substr($str, 0, strlen($prefix)) == $prefix) { 
                                                    $str = substr($str, strlen($prefix));
                                                }
                                                echo $str.'">
                                                <input type="hidden" name="v_ftpdir-x'.$x11.'" value="'.ltrim($ftpdir[$x1], '/').'">';
                                                
                                                $x1++;$x11++;
                                            } while (isset($ftpuser[$x1]));
                                        }
                                                                                                               
                                        if($ftpuser[0] != '') { 
                                            $x1 = 0; 
                                            $x11 = $x1 + 1;

                                            do { echo ""; ?>
                                        <div class="ftp-account" accnum="<?php echo $x11; ?>">
                                            <div class="form-group">
                                                        <label class="col-md-12"><?php echo __("FTP Account"); ?> #<?php echo $x11; ?></label><hr>
                                                    </div>
                                            <div class="form-group">
                                                <label class="col-md-12"><?php echo __("Username"); ?></label><br>
                                                <div class="col-md-12">
                                                    <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                                        <div class="input-group-addon"><?php echo $displayname; ?>_</div>
                                                        <input type="text" class="form-control" name="v_ftpuname<?php echo $x11; ?>" readonly value="<?php $prefix = $uname . '_'; $str = $ftpuser[$x1]; if (substr($str, 0, strlen($prefix)) == $prefix) { $str = substr($str, strlen($prefix));}
                                                        echo $str; ?>" style="padding-left: 0.5%;cursor: not-allowed;">    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="password" class="col-md-12"><?php echo __("Password"); ?> / <a style="cursor:pointer" onclick="generatePassword(10, 'password<?php echo $x11; ?>', 'tg<?php echo $x11; ?>')"> <?php echo __("Generate"); ?></a></label>
                                                <div class="col-md-12 input-group" style="padding-left: 15px;">
                                                    <input type="password" class="form-control form-control-line" name="v_ftppw<?php echo $x11; ?>" id="password<?php echo $x11; ?>">           <span class="input-group-btn"> 
                                                    <button class="btn btn-inverse" style="margin-right: 15px;" name="Show" onclick="toggler(this, 'password<?php echo $x11; ?>')" id="tg<?php echo $x11; ?>" type="button"><i class="ti-eye"></i></button> 
                                                    </span>  </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-12"><?php echo __("Path"); ?></label>
                                                <div class="col-md-12">
                                                    <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                                        <div class="input-group-addon">/home/<?php echo $displayname . '/web/' . $requestdomain; ?>/</div>
                                                        <input type="text" class="form-control" name="v_ftpdir<?php echo $x11; ?>" value="<?php echo ltrim($ftpdir[$x1], '/'); ?>" style="padding-left: 0.5%;">    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $x1++;$x11++;
                                            } while (isset($ftpuser[$x1])); }
                                            else { echo ""; ?>
                                        <div class="ftp-account" accnum="1">
                                            <div class="form-group">
                                                        <label class="col-md-12"><?php echo __("FTP Account"); ?> #1</label><hr>
                                                    </div>
                                            <div class="form-group">
                                                <label class="col-md-12"><?php echo __("Username"); ?></label><br>
                                                <div class="col-md-12">
                                                    <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                                        <div class="input-group-addon"><?php echo $displayname; ?>_</div>
                                                        <input type="text" class="form-control" name="v_ftpuname1" value="<?php $prefix = $uname . '_'; $str = $ftpuser[$x1]; if (substr($str, 0, strlen($prefix)) == $prefix) { $str = substr($str, strlen($prefix));}
                                                        echo $str; ?>" style="padding-left: 0.5%;">    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="password" class="col-md-12"><?php echo __("Password"); ?> / <a style="cursor:pointer" onclick="generatePassword(10, 'password1', 'tg1')"> <?php echo __("Generate"); ?></a></label>
                                                <div class="col-md-12 input-group" style="padding-left: 15px;">
                                                    <input type="password" class="form-control form-control-line" name="v_ftppw1" id="password1">           <span class="input-group-btn"> 
                                                    <button class="btn btn-inverse" style="margin-right: 15px;" name="Show" onclick="toggler(this, 'password1')" id="tg1" type="button"><i class="ti-eye"></i></button> 
                                                    </span>  </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-12"><?php echo __("Path"); ?></label>
                                                <div class="col-md-12">
                                                    <div class="input-group mb-2 mr-sm-2 mb-sm-0">
                                                        <div class="input-group-addon">/home/<?php echo $displayname . '/web/' . $requestdomain; ?>/</div>
                                                        <input type="text" class="form-control" name="v_ftpdir1" value="<?php echo ltrim($ftpdir[$x1], '/'); ?>" style="padding-left: 0.5%;">    
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <p id="FtpControl"><a href="javascript:void(0);" onclick="addFtpAccount();">Add One</a><span id="removeFtpBtn"> / <a href="javascript:void(0);" onclick="removeFtpAccount();">Remove One</a></span></p><br><br>
                                    </div>
                                    <?php echo ""; } ?>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button class="btn btn-success" type="submit" type="submit"><?php echo __("Update Domain"); ?></button> &nbsp;
                                            <a href="../list/web.php" style="color: inherit;text-decoration: inherit;"><button onclick="loadLoader();" class="btn btn-muted" type="button"><?php echo __("Back"); ?></button></a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script> 
                    function submitForm() { document.getElementById("form").submit(); };
                    function exitForm() { window.location.href="../list/web.php"; };
                </script>
                <?php hotkeys($configlocation); ?>
                <?php footerad(); ?><footer class="footer text-center"><?php footer(); ?></footer>
            </div>
        </div>
        <script src="../plugins/components/jquery/jquery.min.js"></script>
        <script src="../plugins/components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../plugins/components/sweetalert2/sweetalert2.min.js"></script>
        <script src="../plugins/components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="../plugins/components/metismenu/dist/metisMenu.min.js"></script>
        <script src="../plugins/components/select2/select2.min.js"></script>
        <script src="../plugins/components/waves/waves.js"></script>
        <script src="../js/notifications.js"></script>
        <script src="../js/main.js"></script>
        <script type="text/javascript">
            Waves.attach('.button', ['waves-effect']);
            Waves.init();
            var processLocation = "../process/";

            $('#form').submit(function(ev) {
                ev.preventDefault();
                processLoader();
                this.submit();
            });
            $(document).ready(function() {
                $('.select2').select2();
            });
            document.getElementById('select1').value = '<?php print_r($domaindata[0]['IP']); ?>'; 
            document.getElementById('select2').value = '<?php print_r($domaindata[0]['TPL']); ?>'; 

           <?php if($sysconfigdata['PROXY_SYSTEM'] != '') { echo ""; ?>
            if ('<?php print_r($domaindata[0]['PROXY']); ?>' == '') {  document.getElementById('select3').value = 'default';  }
            else { document.getElementById('select3').value = '<?php print_r($domaindata[0]['PROXY']); ?>'; }
            if ('<?php print_r($domaindata[0]['PROXY_EXT']); ?>' == '') {  document.getElementById('prxext').value = 'jpeg, jpg, png, gif, bmp, ico, svg, tif, tiff, css, js, htm, html, ttf, otf, webp, woff, txt, csv, rtf, doc, docx, xls, xlsx, ppt, pptx, odf, odp, ods, odt, pdf, psd, ai, eot, eps, ps, zip, tar, tgz, gz, rar, bz2, 7z, aac, m4a, mp3, mp4, ogg, wav, wma, 3gp, avi, flv, m4v, mkv, mov, mp4, mpeg, mpg, wmv, exe, iso, dmg, swf';  }
            else { document.getElementById('prxext').value = '<?php print_r($domaindata[0]['PROXY_EXT']); ?>'; } 
            <?php echo ""; } ?>
            if ('<?php print_r($domaindata[0]['STATS']); ?>' == '') {  document.getElementById('select6').value = 'none';  }
            else { document.getElementById('select6').value = '<?php print_r($domaindata[0]['STATS']); ?>'; }

            function showauth(){
                if(document.getElementById('select6').value != 'none') {
                    document.getElementById('statsauth').style.display = "block";
                }
                else {
                    document.getElementById('statsauth').style.display = "none";
                }}
            function checkDiv(){
                <?php if($sysconfigdata['PROXY_SYSTEM'] != '') { echo ""; ?>
                if(document.getElementById("checkbox4").checked) {
                    document.getElementById('prxy-div').style.display = 'block';
                }
                else {document.getElementById('prxy-div').style.display = 'none';}
                <?php echo ""; } ?>
            }
            function checkDiv2(){
                if(document.getElementById("checkbox5").checked) {
                    document.getElementById('ssl-div').style.display = 'block';
                }
                else {document.getElementById('ssl-div').style.display = 'none';}
            }
            function checkDiv3(){
                <?php if(checkService('vsftpd') !== false || checkService('proftpd') !== false) {
                echo '
                if(document.getElementById("checkbox9").checked) {
                    document.getElementById("ftp-div").style.display = "block";
                }
                else {document.getElementById("ftp-div").style.display = "none";}
                '; } ?>  
            }
            
            function checkDiv4(){
                if(document.getElementById("checkbox10").checked) {
                    document.getElementById('stats-div').style.display = 'block';
                }
                else {document.getElementById('stats-div').style.display = 'none';}
            }
            if($('.ftp-account').length >= 2) { $('#removeFtpBtn').show(); }
            else { $('#removeFtpBtn').hide(); }
            
            function addFtpAccount(){
                var startingAcc = $('#ftp-div').find('.ftp-account:last').attr('accnum');
                startingAcc++;
                var objTo = document.getElementById('FtpControl');
                var newAcc = document.createElement("div");
                newAcc.setAttribute("class", "ftp-account");
                newAcc.setAttribute("accnum", startingAcc);
                 newAcc.innerHTML = '<div class="form-group"><label class="col-md-12"><?php echo __("FTP Account"); ?> #'+startingAcc+'</label><hr></div><div class="form-group"><label class="col-md-12"><?php echo __("Username"); ?></label><br><div class="col-md-12"><div class="input-group mb-2 mr-sm-2 mb-sm-0"><div class="input-group-addon"><?php echo $uname; ?>_</div><input type="text" class="form-control" autocomplete="new-password" name="v_ftpuname'+startingAcc+'" style="padding-left: 0.5%;"></div></div></div><div class="form-group"><label for="password" class="col-md-12"><?php echo __("Password"); ?> / <a style="cursor:pointer" onclick="generatePassword(10, \'password'+startingAcc+'\', \'tg'+startingAcc+'\')"> <?php echo __("Generate"); ?></a></label><div class="col-md-12 input-group" style="padding-left: 15px;"><input type="password" class="form-control form-control-line" autocomplete="new-password" name="v_ftppw'+startingAcc+'" id="password'+startingAcc+'"><span class="input-group-btn"><button class="btn btn-inverse" style="margin-right: 15px;" name="Show" onclick="toggler(this, \'password'+startingAcc+'\')" id="tg'+startingAcc+'" type="button"><i class="ti-eye"></i></button></span></div></div><div class="form-group"><label class="col-md-12"><?php echo __("Path"); ?></label><div class="col-md-12"><div class="input-group mb-2 mr-sm-2 mb-sm-0"><div class="input-group-addon">/home/<?php echo $displayname . '/web/' . $requestdomain; ?>/</div><input type="text" class="form-control" name="v_ftpdir'+startingAcc+'" style="padding-left: 0.5%;"></div></div></div><?php if($phpmailenabled == 'true') { echo ""; ?><div class="form-group"><label class="col-md-12"><?php echo __("Send FTP Credentials to Email:"); ?></label><div class="col-md-12"><input type="email" name="v_ftpnotif'+startingAcc+'" autocomplete="new-password" class="form-control"></div></div><?php echo ""; } ?>';
                $('#ftp-div').find('.ftp-account:last').append(newAcc);
                if($('.ftp-account').length >= 2) { $('#removeFtpBtn').show(); }
                else { $('#removeFtpBtn').hide(); }
            }
            
            function removeFtpAccount() {
               $('#ftp-div').find('.ftp-account:last').remove();
               if($('.ftp-account').length >= 2) { $('#removeFtpBtn').show(); }
                else { $('#removeFtpBtn').hide(); }
           }
            
            function toggle_visibility(id) {
                var e = document.getElementById(id);
                if(e.style.display == 'block')
                    e.style.display = 'none';
                else
                    e.style.display = 'block';
            }
            function toggler(e, f) {
                if( e.name == 'Hide' ) {
                    e.name = 'Show'
                    document.getElementById(f).type="password";
                } else {
                    e.name = 'Hide'
                    document.getElementById(f).type="text";
                }
            }
            function generatePassword(length, g, h) {
                var password = '', character; 
                while (length > password.length) {
                    if (password.indexOf(character = String.fromCharCode(Math.floor(Math.random() * 94) + 33), Math.floor(password.length / 94) * 94) < 0) {
                        password += character;
                    }
                }
                document.getElementById(g).value = password;
                document.getElementById(h).name='Hide';
                document.getElementById(g).type="text";
            }
            function confirmDelete(){
                Swal.fire({
                  title: '<?php echo __("Delete Domain"); ?>:<br> <?php echo $requestdomain; ?>' + ' ?',
                  text: "<?php echo __("You won't be able to revert this!"); ?>",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: '<?php echo __("Yes, delete it!"); ?>'
                }).then((result) => {
                  if (result.value) {
                    Swal.fire({
                        title: '<?php echo __("Processing"); ?>',
                        text: '',
                        onOpen: function () {
                            swal.showLoading()
                        }
                    });
                  window.location.replace("../delete/domain.php?domain=<?php echo $requestdomain; ?>");
                  }
                })}
            function processLoader(){
                Swal.fire({
                    title: '<?php echo __("Processing"); ?>',
                    text: '',
                    onOpen: function () {
                        swal.showLoading()
                    }
                })};
            function loadLoader(){
                Swal.fire({
                    title: '<?php echo __("Loading"); ?>',
                    text: '',
                    onOpen: function () {
                        swal.showLoading()
                    }
                })};
            <?php 
            processPlugins();
            includeScript();
            if($warningson == "all"){
                if(isset($apienabled) && $apienabled == 'true') {
                    echo "toast2.fire({
                            title: '" . __("Feature Disabled") . "',
                            text: '" . __("Custom SSL Certificates are incompatible with API Key Authentication.") . "',
                            icon: 'error'
                        });";
                } 
            }
            elseif($warningson == "admin" && $initialusername == "admin"){
                if(isset($apienabled) && $apienabled == 'true') {
                    echo "toast2.fire({
                            title: '" . __("Feature Disabled") . "',
                            text: '" . __("Custom SSL Certificates are incompatible with API Key Authentication.") . "',
                            icon: 'error'
                        });";

                } 
            }
            
            if(isset($_GET['error']) && $_GET['error'] == "1") {
                echo "Swal.fire({title:'" . $errorcode[1] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            }
            $returntotal = $_POST['r1'] + $_POST['r2'] + $_POST['r3'] + $_POST['r4'] + $_POST['r5'] + $_POST['r6'] + $_POST['r7'] + $_POST['r8'] + $_POST['r9'] + $_POST['r10'] + $_POST['r11'] + $_POST['r12'];
            if(isset($_POST['r1']) && $returntotal == 0) {
                echo "Swal.fire({title:'" . __("Successfully Updated!") . "', icon:'success'});";
            } 
            if(isset($_POST['r1']) && $returntotal != 0) {
                echo "Swal.fire({title:'" . __("Error Updating Web Domain") . "', html:'" . __("Please try again or contact support.") . "<br><br><span onclick=\"$(\'.errortoggle\').toggle();\" class=\"swal-error-title\">View Error Code <i class=\"errortoggle fa fa-angle-double-right\"></i><i style=\"display:none;\" class=\"errortoggle fa fa-angle-double-down\"></i></span><span class=\"errortoggle\" style=\"display:none;\"><br><br>(E: " . $_POST['r1'] . "." . $_POST['r2'] . "." . $_POST['r3'] . "." . $_POST['r4'] . "." . $_POST['r5'] . "." . $_POST['r6'] . "." . $_POST['r7'] . "." . $_POST['r8'] . "." . $_POST['r9'] . "." . $_POST['r10'] . "." . $_POST['r11'] . "." . $_POST['r12'] . ")</span>', icon:'error'});";
            }

            ?>
        </script>
    </body>
</html>