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
else { header('Location: ../../login.php?to=admin/list/plugins.php'.$urlquery.$_SERVER['QUERY_STRING']); exit(); }
if($username != 'admin') { header("Location: ../../"); exit(); }

if(isset($adminenabled) && $adminenabled != 'true'){ header("Location: ../../error-pages/403.html"); exit(); }

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
        <title><?php echo $sitetitle; ?> - <?php echo __("Plugins"); ?></title>
        <link href="../../plugins/components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../plugins/components/footable/footable.bootstrap.css" rel="stylesheet">
        <link href="../../plugins/components/metismenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="../../plugins/components/animate.css/animate.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../../plugins/components/sweetalert2/sweetalert2.min.css" />
        <link href="../../css/style.css" rel="stylesheet">
        <link href="../../css/colors/<?php if(isset($_COOKIE['theme']) && $themecolor != 'custom.css') { echo base64_decode($_COOKIE['theme']); } else {echo $themecolor; } ?>" id="theme" rel="stylesheet">
        <?php if($themecolor == "custom.css") { require( '../../css/colors/custom.php'); } ?>
        <style>
            @media screen and (max-width: 1199px) {
                .resone { display:none !important;}
            }  
            @media screen and (max-width: 767px) {
                .restwo { display:none !important;}
            }    
            @media screen and (max-width: 480px) {
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
                            <img src="../../plugins/images/<?php echo $cplogo; ?>" alt="home" class="hidden-xs dark-logo" />
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
                              adminMenu("./", "plugins");
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
                            <h4 class="page-title"><?php echo __("Plugins"); ?></h4>
                        </div>
                        <?php /*
                        <ul class="side-icon-text pull-right">
                            <li style="position: relative;top: -3px;">
                                <a style="cursor: pointer;" href="../add/plugin.php"><span class="circle circle-sm bg-success di"><i class="fa fa-plus"></i></span><span class="resthree"><?php echo __("Upload Plugin"); ?></span>
                                </a>
                            </li>
                        </ul> */
                        headerad(); ?>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="white-box">
                                <h3 class="box-title m-b-0"><?php echo __("Enabled Plugins"); ?></h3><br>
                                <div class="table-responsive">
                                <table class="table footable m-b-0" data-sorting="true">
                                    <thead>
                                        <tr>
                                            <th> <?php echo __("Name"); ?> </th>
                                            <th data-sortable="false"> <?php echo __("Version"); ?> </th>
                                            <th class="restwo"> <?php echo __("Developer"); ?> </th>
                                            <th class="restwo"> <?php echo __("Descriptor"); ?> </th>
                                            <th data-sortable="false"><?php echo __("Action"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $availableplugins = str_replace(array("../../plugins/", "/manifest.xml"), array("", ""), glob("../../plugins/*/manifest.xml"));

                                        foreach ($plugins as $result) {
                                            if (file_exists('../../plugins/' . $result)) {
                                                if (file_exists('../../plugins/' . $result . '/manifest.xml')) {
                                                    $get = file_get_contents('../../plugins/' . $result . '/manifest.xml');
                                                    $xml   = simplexml_load_string($get, 'SimpleXMLElement', LIBXML_NOCDATA);
                                                    $arr = json_decode(json_encode((array)$xml), TRUE);
                                                    echo '<tr><td>';
                                                    if (isset($arr['admin-name']) && !empty($arr['admin-name'] && isset($arr['name']) && !empty($arr['name']))) {
                                                        array_push($pluginnames,$arr['name']);
                                                        echo $arr['admin-name'];
                                                    }
                                                    elseif (isset($arr['name']) && !empty($arr['name']) && (!isset($arr['admin-name']) || empty($arr['admin-name']))) {
                                                        array_push($pluginnames,$arr['name']);
                                                        echo $arr['name'];
                                                    }
                                                    echo '</td><td>';
                                                    if (isset($arr['version']) && !empty($arr['version'])) {
                                                        echo $arr['version'];
                                                    }
                                                    else { echo __('Not Provided'); }
                                                    echo '</td><td class="restwo">';
                                                    if (isset($arr['developer']) && !empty($arr['developer'])) {
                                                        echo $arr['developer'];
                                                    }
                                                    else { echo __('Not Provided'); }
                                                    echo '</td>
                                                    <td class="restwo">';
                                                    if (isset($arr['descriptor']) && !empty($arr['descriptor'])) {
                                                        echo $arr['descriptor'];
                                                    }
                                                    else { echo __('Not Provided'); }
                                                    echo '</td>
                                                    <td>
                                                        <a onclick="processLoader();" href="../delete/plugin.php?plugin=' . $result . '">
                                                            <button type="button" data-toggle="tooltip" data-original-title="' . __("Disable") . '" class="btn color-button btn-outline btn-circle btn-md m-r-5">
                                                                <i class="fa fa-power-off"></i>
                                                            </button>
                                                        </a>
                                                    </td></tr>';
                                                }    
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            <div class="white-box">
                                <h3 class="box-title m-b-0"><?php echo __("Disabled Plugins"); ?></h3><br>
                                <div class="table-responsive">
                                <table class="table footable m-b-0" data-sorting="true">
                                    <thead>
                                        <tr>
                                            <th> <?php echo __("Name"); ?> </th>
                                            <th data-sortable="false"> <?php echo __("Version"); ?> </th>
                                            <th class="restwo"> <?php echo __("Developer"); ?> </th>
                                            <th class="restwo"> <?php echo __("Descriptor"); ?> </th>
                                            <th data-sortable="false"><?php echo __("Action"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $availableplugins = str_replace(array("../../plugins/", "/manifest.xml"), array("", ""), glob("../../plugins/*/manifest.xml"));
                                        foreach($availableplugins as $key => $one) {
                                            if(strpos($one, '?') !== false)
                                                unset($array[$key]);
                                        }
                                        $disabledplugins = array_diff($availableplugins, $plugins);

                                        foreach ($disabledplugins as $result) {
                                            if (file_exists('../../plugins/' . $result)) {
                                                if (file_exists('../../plugins/' . $result . '/manifest.xml')) {
                                                    $get = file_get_contents('../../plugins/' . $result . '/manifest.xml');
                                                    $xml   = simplexml_load_string($get, 'SimpleXMLElement', LIBXML_NOCDATA);
                                                    $arr = json_decode(json_encode((array)$xml), TRUE);
                                                    echo '<tr><td>';
                                                    if (isset($arr['admin-name']) && !empty($arr['admin-name'] && isset($arr['name']) && !empty($arr['name']))) {
                                                        echo $arr['admin-name'];
                                                    }
                                                    elseif (isset($arr['name']) && !empty($arr['name']) && (!isset($arr['admin-name']) || empty($arr['admin-name']))) {
                                                        echo $arr['name'];
                                                    }
                                                    echo '</td><td>';
                                                    if (isset($arr['version']) && !empty($arr['version'])) {
                                                        echo $arr['version'];
                                                    }
                                                    else { echo __('Not Provided'); }
                                                    echo '</td><td class="restwo">';
                                                    if (isset($arr['developer']) && !empty($arr['developer'])) {
                                                        echo $arr['developer'];
                                                    }
                                                    else { echo __('Not Provided'); }
                                                    echo '</td>
                                                    <td class="restwo">';
                                                    if (isset($arr['descriptor']) && !empty($arr['descriptor'])) {
                                                        echo $arr['descriptor'];
                                                    }
                                                    else { echo __('Not Provided'); }
                                                    echo '</td>
                                                    <td>
                                                        <a onclick="processLoader();" href="../create/plugin.php?plugin=' . $result . '">
                                                            <button type="button" data-toggle="tooltip" data-original-title="' . __("Enable") . '" class="btn color-button btn-outline btn-circle btn-md m-r-5">
                                                                <i class="fa fa-power-off"></i>
                                                            </button>
                                                        </a>
                                                    </td></tr>';
                                                }    
                                            }
                                        }
                                        
                                        ?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php hotkeys($configlocation); ?>
                <?php footerad(); ?><footer class="footer text-center"><?php footer(); ?></footer>
            </div>
        </div>
        <script src="../../plugins/components/jquery/jquery.min.js"></script>
        <script src="../../plugins/components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../../plugins/components/sweetalert2/sweetalert2.min.js"></script>
        <script src="../../plugins/components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="../../plugins/components/metismenu/dist/metisMenu.min.js"></script>
        <script src="../../plugins/components/moment/moment.min.js"></script>
        <script src="../../plugins/components/footable/footable.min.js"></script>
        <script src="../../plugins/components/waves/waves.js"></script>
        <script src="../../js/notifications.js"></script>
        <script src="../../js/main.js"></script>
        <script type="text/javascript">
            Waves.attach('.button', ['waves-effect']);
            Waves.init();
            var processLocation = "../../process/";

            jQuery(function($){
                $('.footable').footable();
            });
            function processLoader(){
                Swal.fire({
                    title: '<?php echo __("Processing"); ?>',
                    text: '',
                    onOpen: function () {
                        swal.showLoading()
                    }
                })};

            <?php

            processPlugins();
            includeScript();
            
            
            if(isset($_GET['merr']) && $_GET['merr'] != "") {
                echo " swal.fire({title:'Database Error', html:'" . __("Please try again or contact support.") . "<br><br><span onclick=\"$(\'.errortoggle\').toggle();\" class=\"swal-error-title\">View Error Code <i class=\"errortoggle fa fa-angle-double-right\"></i><i style=\"display:none;\" class=\"errortoggle fa fa-angle-double-down\"></i></span><span class=\"errortoggle\" style=\"display:none;\"><br><br>(MySQL Error: " . $_GET['merr'] . ")</span>', icon:'error'});";
            } 

            if(isset($_GET['error']) && $_GET['error'] == "1") {
                echo "Swal.fire({title:'" . $errorcode[1] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            }
            if(isset($_POST['delcode']) && $_POST['delcode'] == "0") {
                echo "Swal.fire({title:'" . __("Successfully Disabled!") . "', icon:'success'});";
            } 
            if(isset($_POST['addcode']) && $_POST['addcode'] == "0") {
                echo "Swal.fire({title:'" . __("Successfully Enabled!") . "', icon:'success'});";
            } 
            if(isset($_POST['delcode']) && $_POST['delcode'] > "0") {
                echo "Swal.fire({title:'" . $errorcode[$_POST['delcode']] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            }
            if(isset($_POST['addcode']) && $_POST['addcode'] > "0") {
                echo "Swal.fire({title:'" . $errorcode[$_POST['addcode']] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            }
            ?>
        </script>
    </body>
</html>