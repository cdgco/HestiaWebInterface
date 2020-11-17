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

 session_start();
$configlocation = "../includes/";
if (file_exists( '../includes/config.php' )) { require( '../includes/includes.php'); }  else { header( 'Location: ../install' ); exit(); };

if(base64_decode($_SESSION['loggedin']) == 'true') {}
else { header('Location: ../login.php?to=add/dns.php'); exit();  }

if(isset($dnsenabled) && $dnsenabled != 'true'){ header("Location: ../error-pages/403.html"); exit(); }

$postvars = array(
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user','arg1' => $username,'arg2' => 'json'));

$curl0 = curl_init();
$curlstart = 0; 

while($curlstart <= 0) {
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

if (CLOUDFLARE_EMAIL == '' || CLOUDFLARE_API_KEY == ''){ $cfenabled = 'off'; }
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/ico" href="../plugins/images/<?php echo $cpfavicon; ?>">
        <title><?php echo $sitetitle; ?> - <?php echo __("DNS"); ?></title>
        <link href="../plugins/components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../plugins/components/metismenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="../plugins/components/select2/select2.min.css" rel="stylesheet">
        <link href="../plugins/components/animate.css/animate.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../plugins/components/sweetalert2/sweetalert2.min.css" />
        <link href="../css/style.css" rel="stylesheet">
        <link href="../css/colors/<?php if(isset($_COOKIE['theme']) && $themecolor != 'custom.css') { echo base64_decode($_COOKIE['theme']); } else {echo $themecolor; } ?>" id="theme" rel="stylesheet">
        <?php if($themecolor == "custom.css") { require( '../css/colors/custom.php'); } ?>
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
                              primaryMenu("../list/", "../process/", "dns");
                        ?>
                    </ul>
                </div>
            </div>
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row bg-title">
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                            <h4 class="page-title"><?php echo __("Add DNS Domain"); ?></h4>
                        </div>
                        <?php headerad(); ?>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="white-box">
                                <form class="form-horizontal form-material" autocomplete="off" method="post" id="form" action="../create/dns.php">
                                    <div class="form-group">
                                        <label class="col-md-12"><?php echo __("Domain"); ?></label>
                                        <div class="col-md-12">
                                            <input type="text" name="v_domain" id="v_domain" onkeyup="subDomain()" class="form-control" required> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="col-md-12"><?php echo __("IP Address"); ?></label>
                                        <div class="col-md-12">
                                            <input type="text" name="v_ip" class="form-control" required> </div>
                                    </div>
                                    <div id="cloudflare">
                                        <?php if ($cfenabled != "off") { echo ' 
                                    <div class="form-group">
                                        <label class="col-md-12">' . __("Cloudflare Support") . '</label>
                                        <div class="col-md-12">
                                            <div class="checkbox checkbox-info">
                                                <input id="checkbox4" type="checkbox" name="v_cf" onclick="checkDiv();">
                                                <label for="checkbox4">' . __("Enabled") . '</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="cf-div" style="margin-left: 4%;">
                                        <div class="form-group">
                                            <label class="col-md-12">' . __("Security Level") . '</label>
                                            <div class="col-md-12">
                                                <select class="form-control select3 select2" name="v_cf_level" id="select3">
                                                    <option value="essentially_off">' . __("Essentially Off") . '</option>
                                                    <option value="low">' . __("Low") . '</option>
                                                    <option value="medium">' . __("Medium") . '</option>
                                                    <option value="high">' . __("High") . '</option>
                                                    <option value="Under Attack">' . __('I\'m Under Attack!') . '</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">' . __("SSL Setting") . '</label>
                                            <div class="col-md-12">
                                                <select class="form-control select4 select2" name="v_cf_ssl" id="select4">
                                                    <option value="off" selected>' . __("Off") . '</option>
                                                    <option value="flexible">' . __("Flexible") . '</option>
                                                    <option value="full">' . __("Full") . '</option>
                                                    <option value="strict">' . __("Full (Strict)") . '</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>'; } ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12"><a style="cursor: pointer;" onclick="toggle_visibility('togglediv');"><?php echo __("Advanced Options"); ?></a></label>
                                    </div>
                                    <div id="togglediv" style="display:none;">
                                        <div class="form-group">
                                            <label class="col-md-12"><?php echo __("Nameservers"); ?></label>
                                            <div class="col-md-12">

                                                <div><input type="text" value="<?php print_r(explode(',', ($admindata['NS']))[0]); ?>" class="form-control form-control-line" name="v_ns1" id="ns1x" required><br></div>

                                                <div><input type="text" value="<?php print_r(explode(',', ($admindata['NS']))[1]); ?>" class="form-control form-control-line" name="v_ns2" id="ns2x" required><br><div id="ns2wrapper"><a style="cursor:pointer;" id="addmore" onclick="add1();"><?php echo __("Add One"); ?></a></div></div>

                                                <div id="ns3" style="display:<?php if(explode(',', ($admindata['NS']))[2] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($admindata['NS']))[2]); ?>" class="form-control form-control-line" name="v_ns3" id="ns3x"><br><div id="ns3wrapper"><a style="cursor:pointer;" id="addmore1" onclick="add2();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove1" onclick="rem2();"><?php echo __("Remove One"); ?></a></div></div>

                                                <div id="ns4" style="display:<?php if(explode(',', ($admindata['NS']))[3] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($admindata['NS']))[3]); ?>" class="form-control form-control-line" name="v_ns4" id="ns4x"><br><div id="ns4wrapper"><a style="cursor:pointer;" id="addmore2" onclick="add3();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove2" onclick="rem3();"><?php echo __("Remove One"); ?></a></div></div>

                                                <div id="ns5" style="display:<?php if(explode(',', ($admindata['NS']))[4] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($admindata['NS']))[4]); ?>" class="form-control form-control-line" name="v_ns5" id="ns5x"><br><div id="ns5wrapper"><a style="cursor:pointer;" id="addmore3" onclick="add4();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove3" onclick="rem4();"><?php echo __("Remove One"); ?></a></div></div>

                                                <div id="ns6" style="display:<?php if(explode(',', ($admindata['NS']))[5] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($admindata['NS']))[5]); ?>" class="form-control form-control-line" name="v_ns6" id="ns6x"><br><div id="ns6wrapper"><a style="cursor:pointer;" id="addmore4" onclick="add5();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove4" onclick="rem5();"><?php echo __("Remove One"); ?></a></div></div>

                                                <div id="ns7" style="display:<?php if(explode(',', ($admindata['NS']))[6] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($admindata['NS']))[6]); ?>" class="form-control form-control-line" name="v_ns7" id="ns7x"><br><div id="ns7wrapper"><a style="cursor:pointer;" id="addmore5" onclick="add6();"><?php echo __("Add One"); ?></a> / <a style="cursor:pointer;" id="remove5" onclick="rem6();"><?php echo __("Remove One"); ?></a></div></div>

                                                <div id="ns8" style="display:<?php if(explode(',', ($admindata['NS']))[7] == ''){ echo "none"; } else { echo "block"; } ?>"><input type="text" value="<?php print_r(explode(',', ($admindata['NS']))[7]); ?>" class="form-control form-control-line" name="v_ns8" id="ns8x"><br><div id="ns8wrapper"><a style="cursor:pointer;" id="remove6" onclick="rem7();"><?php echo __("Remove One"); ?></a></div></div>
                                            </div>
                                        </div>                  
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <button class="btn btn-success" type="submit" onclick="processLoader();"><?php echo __("Add Domain"); ?></button> &nbsp;
                                            <a href="../list/dns.php" style="color: inherit;text-decoration: inherit;"><button onclick="loadLoader();" class="btn btn-muted" type="button"><?php echo __("Back"); ?></button></a>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <script> 
                    function submitForm() { document.getElementById("form").submit(); };
                    function exitForm() { window.location.href="../list/dns.php"; };
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
            function subDomain() {

                url = document.getElementById("v_domain").value;
                url = url.replace(new RegExp(/^\s+/),"");
                url = url.replace(new RegExp(/\s+$/),"");
                url = url.replace(new RegExp(/\\/g),"/");
                url = url.replace(new RegExp(/^http\:\/\/|^https\:\/\/|^ftp\:\/\//i),"");
                url = url.replace(new RegExp(/^www\./i),"");
                url = url.replace(new RegExp(/\/(.*)/),"");
                if (url.match(new RegExp(/\.[a-z]{2,3}\.[a-z]{2}$/i))) {
                    url = url.replace(new RegExp(/\.[a-z]{2,3}\.[a-z]{2}$/i),"");
                } else if (url.match(new RegExp(/\.[a-z]{2,4}$/i))) {
                    url = url.replace(new RegExp(/\.[a-z]{2,4}$/i),"");
                }
                var subDomain = (url.match(new RegExp(/\./g))) ? true : false;

                if(subDomain === false) {
                    document.getElementById("cloudflare").style.display = "block";
                }
                else { document.getElementById("cloudflare").style.display = "none"; }

            }

            <?php if ($cfenabled != "off") { echo '

            if(document.getElementById("checkbox4").checked) {
                    document.getElementById("cf-div").style.display = "block";
                }
            else { document.getElementById("cf-div").style.display = "none"; }
            function checkDiv(){
                if(document.getElementById("checkbox4").checked) {
                    document.getElementById("cf-div").style.display = "block";
                }
                else { document.getElementById("cf-div").style.display = "none"; }
            }'; } 

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
            ?>
            function toggle_visibility(id) {
                var e = document.getElementById(id);
                if(e.style.display == 'block')
                    e.style.display = 'none';
                else
                    e.style.display = 'block';
            }
            document.getElementById('select2').value = '<?php print_r($dnsdata[0]['TPL']); ?>'; 
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
            
            if(isset($_GET['error']) && $_GET['error'] == "1") {
                echo "Swal.fire({title:'" . $errorcode[1] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            }
            ?>
        </script>
    </body>
</html>