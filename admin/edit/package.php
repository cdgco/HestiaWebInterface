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
else { header('Location: ../../login.php?to=admin/edit/package.php'.$urlquery.$_SERVER['QUERY_STRING']); exit(); }
if($username != 'admin') { header("Location: ../../"); exit(); }

if(isset($adminenabled) && $adminenabled != 'true'){ header("Location: ../../error-pages/403.html"); exit(); }

if(!isset($_GET['package'])) { header("Location: ../list/packages.php"); exit(); }
$postvars = array(
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user','arg1' => $username,'arg2' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user-package','arg1' => $_GET['package'],'arg2' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-web-templates','arg1' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-web-templates-proxy','arg1' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-dns-templates','arg1' => 'json'),
);

$curl0 = curl_init();
$curl1 = curl_init();
$curl2 = curl_init();
$curl3 = curl_init();
$curl4 = curl_init();
$curlstart = 0; 


while($curlstart <= 4) {
    curl_setopt(${'curl' . $curlstart}, CURLOPT_URL, $vst_url);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_RETURNTRANSFER,true);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_POST, true);
    curl_setopt(${'curl' . $curlstart}, CURLOPT_POSTFIELDS, http_build_query($postvars[$curlstart]));
    $curlstart++;
} 

$admindata = json_decode(curl_exec($curl0), true)[$username];
$packname = array_keys(json_decode(curl_exec($curl1), true));
$packdata = array_values(json_decode(curl_exec($curl1), true));
$webtemplates = array_values(json_decode(curl_exec($curl2), true));
$proxytemplates = array_values(json_decode(curl_exec($curl3), true));
$dnstemplates = array_values(json_decode(curl_exec($curl4), true));
$useremail = $admindata['CONTACT'];
if(isset($admindata['LANGUAGE'])){ $locale = $ulang[$admindata['LANGUAGE']]; }
_setlocale('LC_CTYPE', $locale); _setlocale('LC_MESSAGES', $locale);
_bindtextdomain('messages', '../../locale');
_textdomain('messages');

foreach ($plugins as $result) {
    if (file_exists('../../plugins/' . $result)) {
        if (file_exists('../../plugins/' . $result . '/manifest.xml')) {
            $get = file_get_contents('../../plugins/' . $result . '/manifest.xml');
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
        <link rel="icon" type="image/ico" href="../../plugins/images/<?php echo $cpfavicon; ?>">
        <title><?php echo $sitetitle; ?> - <?php echo __("Packages"); ?></title>
        <link href="../../plugins/components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../plugins/components/metismenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="../../plugins/components/select2/select2.min.css" rel="stylesheet">
        <link href="../../plugins/components/animate.css/animate.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../plugins/components/sweetalert2/sweetalert2.min.css" />
        <link href="../../css/style.css" rel="stylesheet">
        <link href="../../css/colors/<?php if(isset($_COOKIE['theme']) && $themecolor != 'custom.css') { echo base64_decode($_COOKIE['theme']); } else {echo $themecolor; } ?>" id="theme" rel="stylesheet">
        <?php if($themecolor == "custom.css") { require( '../../css/colors/custom.php'); } ?>
        <?php if(GOOGLE_ANALYTICS_ID != ''){ echo "<script async src='https://www.googletagmanager.com/gtag/js?id=" . GOOGLE_ANALYTICS_ID . "'></script>
        <script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', '" . GOOGLE_ANALYTICS_ID . "');</script>"; } ?> 
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="fix-header">
        <div class="preloader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
            </svg>
        </div>
        <div id="wrapper">
            <nav class="navbar navbar-default navbar-static-top m-b-0">
                <div class="navbar-header">
                    <div class="top-left-part">
                        <a class="logo" href="../../index.php">
                            <img src="../../plugins/images/<?php echo $cpicon; ?>" alt="home" class="logo-1 dark-logo" />
                            <span class="hidden-xs">
                                <img src="../../plugins/images/<?php echo $cplogo; ?>" alt="home" class="hidden-xs dark-logo" />
                            </span>
                        </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-left">
                        <li><a href="javascript:void(0)" class="open-close waves-effect waves-light visible-xs"><i class="ti-close ti-menu"></i></a></li> 
                        <?php notifications(); ?>
                    </ul>
                    <ul class="nav navbar-top-links navbar-right pull-right">
                        <li>
                            <form class="app-search m-r-10" id="searchform" action="../../process/search.php" method="get">
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
                                <li><a href="../../profile.php"><i class="ti-home"></i> <?php echo __("My Account"); ?></a></li>
                                <li><a href="../../profile.php?settings=open"><i class="ti-settings"></i> <?php echo __("Account Settings"); ?></a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="../../process/logout.php"><i class="fa fa-power-off"></i> <?php echo __("Logout"); ?></a></li>
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
                        <?php indexMenu("../../"); 
                              adminMenu("../list/", "packages");
                              profileMenu("../../");
                              primaryMenu("../../list/", "../../process/", "");
                        ?>
                    </ul>
                </div>
            </div>
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row bg-title">
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                            <h4 class="page-title"><?php echo __("Edit Package"); ?></h4>
                        </div>
                        <?php headerad(); ?>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="white-box"><?php if(checkService('vsftpd') === false && checkService('proftpd') === false) { echo "Error: HestiaCP FTP must be enabled to edit packages."; } ?>
                                <form class="form-horizontal form-material"<?php if(checkService('vsftpd') === false && checkService('proftpd') === false) { echo "style='display:none;"; } ?> autocomplete="off" method="post" id="form" action="../change/package.php">
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Package Name"); ?></label>
                                        <div class="col-md-12">

                                            <input type="text" disabled value="<?php print_r($packname[0]); ?>" style="background-color: #eee;padding-left: 0.6%;border-radius: 2px;border: 1px solid rgba(120, 130, 140, 0.13);bottom: 19px;background-image: none;" class="form-control uneditable-input form-control-static"> 
                                            <input type="hidden" name="v_package-name" value="<?php print_r($packname[0]); ?>"> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Web Template"); ?></label>
                                        <div class="col-md-12">
                                            <select class="form-control select3 select2" name="v_webtpl" id="select3">
                                                <?php
                                                if($webtemplates[0] != '') {
                                                    $x2 = 0; 

                                                    do {
                                                        echo '<option value="' . $webtemplates[$x2] . '">' . $webtemplates[$x2] . '</option>';
                                                        $x2++;
                                                    } while ($webtemplates[$x2] != ''); }

                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Proxy Template"); ?></label>
                                        <div class="col-md-12">
                                            <select class="form-control select4 select2" name="v_prxtpl" id="select4">
                                                <?php
                                                if($proxytemplates[0] != '') {
                                                    $x3 = 0; 

                                                    do {
                                                        echo '<option value="' . $proxytemplates[$x3] . '">' . $proxytemplates[$x3] . '</option>';
                                                        $x3++;
                                                    } while ($proxytemplates[$x3] != ''); }

                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("DNS Template"); ?></label>
                                        <div class="col-md-12">
                                            <select class="form-control select5 select2" name="v_dnstpl" id="select5">
                                                <?php
                                                if($dnstemplates[0] != '') {
                                                    $x4 = 0; 

                                                    do {
                                                        echo '<option value="' . $dnstemplates[$x4] . '">' . $dnstemplates[$x4] . '</option>';
                                                        $x4++;
                                                    } while ($dnstemplates[$x4] != ''); }

                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Web Domains"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['WEB_DOMAINS']); ?>" name="v_web-domains" id="ul1" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul1').value == 'unlimited') { document.getElementById('ul1').value = '100';} else { document.getElementById('ul1').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Web Aliases"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['WEB_ALIASES']); ?>" name="v_web-aliases" id="ul2" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul2').value == 'unlimited') { document.getElementById('ul2').value = '100';} else { document.getElementById('ul2').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted" style="top: -20px;position: relative;"><?php echo __("Per Domain"); ?></small>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("DNS Domains"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['DNS_DOMAINS']); ?>" name="v_dns-domains" id="ul3" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul3').value == 'unlimited') { document.getElementById('ul3').value = '100';} else { document.getElementById('ul3').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("DNS Records"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['DNS_RECORDS']); ?>" name="v_dns-records" id="ul4" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul4').value == 'unlimited') { document.getElementById('ul4').value = '100';} else { document.getElementById('ul4').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted" style="top: -20px;position: relative;"><?php echo __("Per Domain"); ?></small>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Mail Domains"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['MAIL_DOMAINS']); ?>" name="v_mail-domains" id="ul5" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul5').value == 'unlimited') { document.getElementById('ul5').value = '100';} else { document.getElementById('ul5').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Mail Accounts"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['MAIL_ACCOUNTS']); ?>" name="v_mail-accounts" id="ul6" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul6').value == 'unlimited') { document.getElementById('ul6').value = '100';} else { document.getElementById('ul6').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted" style="top: -20px;position: relative;"><?php echo __("Per Domain"); ?></small>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Databases"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['DATABASES']); ?>" name="v_databases" id="ul7" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul7').value == 'unlimited') { document.getElementById('ul7').value = '100';} else { document.getElementById('ul7').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Cron Jobs"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['CRON_JOBS']); ?>" name="v_cron-jobs" id="ul8" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul8').value == 'unlimited') { document.getElementById('ul8').value = '100';} else { document.getElementById('ul8').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Backups"); ?></label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['BACKUPS']); ?>" name="v_backups" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Quota"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['DISK_QUOTA']); ?>" name="v_quota" id="ul9" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul9').value == 'unlimited') { document.getElementById('ul9').value = '100';} else { document.getElementById('ul9').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted" style="top: -20px;position: relative;"><?php echo __("In Megabytes"); ?></small>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Bandwidth"); ?></label>
                                        <div class="col-md-12 input-group" style="padding-left: 15px;">
                                            <input type="text" class="form-control form-control-line" value="<?php print_r($packdata[0]['BANDWIDTH']); ?>" name="v_bandwidth" id="ul10" required>
                                            <span class="input-group-btn"> 
                                                <button class="btn btn-outline-secondary" style="margin-right: 15px;" onclick="if(document.getElementById('ul10').value == 'unlimited') { document.getElementById('ul10').value = '100';} else { document.getElementById('ul10').value = 'unlimited';}" type="button">
                                                    <i class="ti-infinite"></i>
                                                </button> 
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted" style="top: -20px;position: relative;"><?php echo __("In Megabytes"); ?></small>
                                    <div class="form-group" style="overflow: visible;">
                                        <label class="col-md-12"><?php echo __("SSH Access"); ?></label>
                                        <div class="col-md-12">
                                            <select class="form-control select2" name="ssh" id="sshselect">
                                                <option value="bash">bash</option>
                                                <option value="dash">dash</option>
                                                <option value="nologin">nologin</option>
                                                <option value="rbash">rbash</option>
                                                <option value="rssh">rssh</option>
                                                <option value="screen">screen</option>
                                                <option value="sh">sh</option>
                                                <option value="tcsh">tcsh</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Default Nameservers"); ?></label>
                                        <div class="col-md-12">

                                            <div><input type="text" value="<?php print_r(explode(',', ($packdata[0]['NS']))[0]); ?>" class="form-control form-control-line" name="ns1" id="ns1x" required><br></div>

                                            <div><input type="text" value="<?php print_r(explode(',', ($packdata[0]['NS']))[1]); ?>" class="form-control form-control-line" name="ns2" id="ns2x" required><br><div id="ns2wrapper"><a style="cursor:pointer;" id="addmore" onclick="add1();"><?php echo __("Add One"); ?></a></div></div>

                                            <div id="ns3" style="display:<?php if(explode(',', ($packdata[0]['NS']))[2] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($packdata[0]['NS']))[2]); ?>" class="form-control form-control-line" name="ns3" id="ns3x"><br><div id="ns3wrapper"><a style="cursor:pointer;" id="addmore1" onclick="add2();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove1" onclick="rem2();"><?php echo __("Remove One"); ?></a></div></div>

                                            <div id="ns4" style="display:<?php if(explode(',', ($packdata[0]['NS']))[3] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($packdata[0]['NS']))[3]); ?>" class="form-control form-control-line" name="ns4" id="ns4x"><br><div id="ns4wrapper"><a style="cursor:pointer;" id="addmore2" onclick="add3();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove2" onclick="rem3();"><?php echo __("Remove One"); ?></a></div></div>

                                            <div id="ns5" style="display:<?php if(explode(',', ($packdata[0]['NS']))[4] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($packdata[0]['NS']))[4]); ?>" class="form-control form-control-line" name="ns5" id="ns5x"><br><div id="ns5wrapper"><a style="cursor:pointer;" id="addmore3" onclick="add4();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove3" onclick="rem4();"><?php echo __("Remove One"); ?></a></div></div>

                                            <div id="ns6" style="display:<?php if(explode(',', ($packdata[0]['NS']))[5] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($packdata[0]['NS']))[5]); ?>" class="form-control form-control-line" name="ns6" id="ns6x"><br><div id="ns6wrapper"><a style="cursor:pointer;" id="addmore4" onclick="add5();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove4" onclick="rem5();"><?php echo __("Remove One"); ?></a></div></div>

                                            <div id="ns7" style="display:<?php if(explode(',', ($packdata[0]['NS']))[6] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($packdata[0]['NS']))[6]); ?>" class="form-control form-control-line" name="ns7" id="ns7x"><br><div id="ns7wrapper"><a style="cursor:pointer;" id="addmore5" onclick="add6();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove5" onclick="rem6();"><?php echo __("Remove One"); ?></a></div></div>

                                            <div id="ns8" style="display:<?php if(explode(',', ($packdata[0]['NS']))[7] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($packdata[0]['NS']))[7]); ?>" class="form-control form-control-line" name="ns8" id="ns8x"><br><div id="ns8wrapper"><a style="cursor:pointer;" id="remove6" onclick="rem7();"><?php echo __("Remove One"); ?></a></div></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button class="btn btn-success" <?php if($apienabled == "true") { echo 'disabled'; } ?> type="submit"><?php echo __("Update Package"); ?></button> &nbsp;
                                            <a href="../list/packages.php" style="color: inherit;text-decoration: inherit;"><button onclick="loadLoader();" class="btn btn-muted" type="button"><?php echo __("Back"); ?></button></a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script> 
                    function submitForm() { document.getElementById("form").submit(); };
                    function exitForm() { window.location.href="../list/packages.php"; };
                </script>
                <?php hotkeys($configlocation); ?>
                <?php footerad(); ?><footer class="footer text-center"><?php footer(); ?></footer>
            </div>
        </div>
        <script src="../../plugins/components/jquery/jquery.min.js"></script>
        <script src="../../plugins/components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../plugins/components/sweetalert2/sweetalert2.min.js"></script>
        <script src="../../plugins/components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="../../plugins/components/metismenu/dist/metisMenu.min.js"></script>
        <script src="../../plugins/components/select2/select2.min.js"></script>
        <script src="../../plugins/components/waves/waves.js"></script>
        <script src="../../js/notifications.js"></script>
        <script src="../../js/main.js"></script>
        <script type="text/javascript">
            Waves.attach('.button', ['waves-effect']);
            Waves.init();
            var processLocation = "../../process/";
            
            document.getElementById('select3').value = '<?php print_r($packdata[0]['WEB_TEMPLATE']); ?>';
            document.getElementById('select4').value = '<?php print_r($packdata[0]['PROXY_TEMPLATE']); ?>';
            document.getElementById('select5').value = '<?php print_r($packdata[0]['DNS_TEMPLATE']); ?>';
            document.getElementById('sshselect').value = '<?php print_r($packdata[0]['SHELL']); ?>';

            $('#form').submit(function(ev) {
                ev.preventDefault();
                processLoader();
                this.submit();
            });
            $(document).ready(function() {
                $('.select2').select2();
            });
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

            $checkcount = 2;
            $check1count = 3;

            while($checkcount <= 7) {
                echo "if( document.getElementById('ns" . $check1count . "x').value != '') {
                    document.getElementById('ns" . $checkcount . "wrapper').style.display = 'none';
                }";

                $checkcount++;
                $check1count++;
            }

            $addcount = 1;
            $add1count = 2; 
            $add2count = 3; 

            while($addcount <= 6) {
                echo "function add" . $addcount ."() {
                    if( document.getElementById('ns" . $add2count . "').style.display = 'none' ) {
                        document.getElementById('ns" . $add2count . "').style.display = 'block'; 
                        document.getElementById('ns" . $add1count . "wrapper').style.display = 'none';
                    } 
                }";
                $addcount++;
                $add1count++;
                $add2count++;
            } 

            $remcount = 2;
            $rem1count = 3; 


            while($remcount <= 7) {
                echo "function rem" . $remcount ."() {
                    if( document.getElementById('ns" . $rem1count . "').style.display = 'block' ) {
                        document.getElementById('ns" . $rem1count . "').style.display = 'none'; 
                        document.getElementById('ns" . $remcount . "wrapper').style.display = 'block';
                        document.getElementById('ns" . $rem1count . "x').value = '';
                    } 
                }";
                $remcount++;
                $rem1count++;
            } 

            processPlugins();
            includeScript();
            
           if(isset($_GET['error']) && $_GET['error'] == "1") {
                echo "Swal.fire({title:'" . $errorcode[1] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            } 
            $returntotal = $_POST['r1'] + $_POST['r2'];
            if(isset($_POST['r1']) && $returntotal == 0) {
                echo "Swal.fire({title:'" . __("Successfully Updated!") . "', icon:'success'});";
            } 

            if(isset($_POST['r1']) && $returntotal != 0) {
                echo "Swal.fire({title:'" . __("Error Updating Package") . "', html:'" . __("Please try again or contact support.") . "<br><br><span onclick=\"$(\'.errortoggle\').toggle();\" class=\"swal-error-title\">View Error Code <i class=\"errortoggle fa fa-angle-double-right\"></i><i style=\"display:none;\" class=\"errortoggle fa fa-angle-double-down\"></i></span><span class=\"errortoggle\" style=\"display:none;\"><br><br>(E: " . $_POST['r1'] . "." . $_POST['r2'] . ")</span>', icon:'error'});";
            }
           if($warningson == "all"){
                if(isset($apienabled) && $apienabled == 'true') {
                    echo "toast2.fire({
                            title: '" . __("Feature Disabled") . "',
                            text: '" . __("Packages are incompatible with API Key Authentication.") . "',
                            icon: 'error'
                        });";
                } 
            }
            elseif($warningson == "admin" && $initialusername == "admin"){
                if(isset($apienabled) && $apienabled == 'true') {
                    echo "toast2.fire({
                            title: '" . __("Feature Disabled") . "',
                            text: '" . __("Packages are incompatible with API Key Authentication.") . "',
                            icon: 'error'
                        });";

                } 
            }
                ?>
        </script>
    </body>
</html>