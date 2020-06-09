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
else { header('Location: ../login.php?to=list/backups.php' . $urlquery . $_SERVER['QUERY_STRING']); exit(); }

if(isset($backupsenabled) && $backupsenabled != 'true'){ header("Location: ../error-pages/403.html"); exit(); }

$postvars = array(
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user','arg1' => $username,'arg2' => 'json'),
    array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user-backups','arg1' => $username,'arg2' => 'json'));

$curl0 = curl_init();
$curl1 = curl_init();
$curlstart = 0; 

while($curlstart <= 1) {
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
$backupname = array_keys(json_decode(curl_exec($curl1), true));
$backupdata = array_values(json_decode(curl_exec($curl1), true));
if(isset($admindata['LANGUAGE'])){ $locale = $ulang[$admindata['LANGUAGE']]; }
_setlocale(LC_CTYPE, $locale);
_setlocale(LC_MESSAGES, $locale);
_bindtextdomain('messages', './locale');
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
        <title><?php echo $sitetitle; ?> - <?php echo __("Backups"); ?></title>
        <link href="../plugins/components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../plugins/components/footable/footable.bootstrap.css" rel="stylesheet">
        <link href="../plugins/components/metismenu/dist/metisMenu.min.css" rel="stylesheet">
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
                .resthree { display:none !important;}
            } 
            @media screen and (max-width: 540px) {
                .resfour { display:none !important;}
                td { font-size: 11px; }
            } 
            @media screen and (max-width: 410px) {
                .resfive { display:none !important;}
                td { font-size: 10px; }
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
        <form id="download" method="post">
            <input type="hidden" form="download" id="formurl" name="url"/>
            <input type="hidden" form="download" name="download" value="yes"/>
            <script>
                async function getPassword(){
                    Swal.fire({
                        title: '<?php echo __("Confirm Password"); ?>',
                        html:
                        '<input type="hidden" form="download" name="user" value="<?php echo $username; ?>"/>' +
                        '<br><div class="form-group"><input form="download" type="password" class="form-control" name="password" id="pwinput" placeholder="<?php echo __("Password"); ?>"></div><button type="submit" form="download" class="btn btn-primary"><?php echo __("Submit"); ?></button>',
                        showCloseButton: true,
                        showCancelButton: false,
                        showConfirmButton: false
                    }).catch(swal.noop)};
            </script>
        </form>
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
                                            <p class="text-muted"><?php print_r($useremail); ?></p>
                                        </div>
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
                              primaryMenu("./", "../process/", "");
                        ?>
                    </ul>
                </div>
            </div>
            <div id="page-wrapper">
                <div class="container-fluid">
                    <div class="row bg-title">
                        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                            <h4 class="page-title"><?php echo __("Manage Backups"); ?></h4>
                        </div>
                        <?php headerad(); ?>
                    </div>
                    <div class="row restwo">
                        <div class="col-lg-12 col-lg-12 col-lg-12">
                            <div class="panel">
                                <div class="sk-chat-widgets">
                                    <div class="panel panel-themecolor">
                                        <div class="panel-heading">
                                            <center><?php echo __("BACKUPS"); ?></center>
                                        </div>
                                        <div class="panel-body">
                                            <center><h2><?php print_r($admindata['U_BACKUPS']); ?></h2></center>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="white-box"> <ul class="side-icon-text pull-right">
                                <li><a href="../add/backup.php?verified=yes"><span class="circle circle-sm bg-inverse di"><i class="fa fa-calendar-check-o"></i></span><span class="resthree"><wrapper class="restwo"><?php echo __("Schedule "); ?></wrapper><?php echo __("Backup"); ?></span></a></li>
                                <li class="resfive"><a href="../edit/backupexclusions.php"><span class="circle circle-sm bg-inverse di"><i class="fa fa-ban"></i></span><span class="resthree"><wrapper class="restwo"><?php echo __("Backup "); ?></wrapper><?php echo __("Exclusions"); ?></span></a></li>
                                </ul>
                                <h3 class="box-title m-b-0"><?php echo __("Backups"); ?></h3><br>
                                <div class="table-responsive">
                                <table class="table footable m-b-0" data-sorting="true">
                                    <thead>
                                        <tr>
                                            <th data-toggle="true"> <?php echo __("Backup Name"); ?></th>
                                            <th data-type="numeric"> <?php echo __("Size"); ?> </th>
                                            <th data-sortable="false"> <?php echo __("Action"); ?> </th>
                                            <th data-breakpoints="all"> <?php echo __("Web"); ?> </th>
                                            <th data-breakpoints="all"> <?php echo __("DNS"); ?> </th>
                                            <th data-breakpoints="all"> <?php echo __("Mail"); ?> </th>
                                            <th data-breakpoints="all"> <?php echo __("Databases"); ?> </th>
                                            <th data-breakpoints="all"> <?php echo __("Cron"); ?> </th>
                                            <th data-breakpoints="all"> <?php echo __("User Directories"); ?> </th>
                                            <th data-breakpoints="all"> <?php echo __("Type"); ?> </th>
                                            <th data-breakpoints="all" data-type="numeric"> <?php echo __("Runtime"); ?> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if($backupname[0] != '') {
                                            $x1 = 0; 

                                            do {
                                                echo '<tr>
                                                        <td>' . $backupname[$x1] . '</td>
                                                        <td data-sort-value="' . $backupdata[$x1]['SIZE'] . '">' . formatMB($backupdata[$x1]['SIZE']) . '</td>
                                                        <td>
                                                            <button onclick="document.getElementById(\'formurl\').value = \'' . $url8083 . '/download/backup/hwi.php?backup=' . $backupname[$x1] . '\';getPassword();" type="button" data-toggle="tooltip" data-original-title="' . __("Download") .'" class="btn color-button btn-outline btn-circle btn-md m-r-5"><i class="fa fa-download"></i></button>
                                                            <a href="../list/backup.php?backup=' . $backupname[$x1] . '"><button type="button" data-toggle="tooltip" data-original-title="' . __("Configure Restore") .'" class="btn color-button btn-outline btn-circle btn-md m-r-5"><i class="fa fa-cog"></i></button></a>
                                                            <button onclick="confirmDelete(\'' . $backupname[$x1] . '\')" type="button" data-toggle="tooltip" data-original-title="' . __("Delete") .'" class="btn color-button btn-outline btn-circle btn-md m-r-5"><i class="fa fa-trash-o"></i></button>
                                                        </td>
                                                        <td>'; if(implode(', ', explode(",", $backupdata[$x1]['WEB'])) == "") { echo __("Not Backed Up");} else{ print_r(implode(', ', explode(",", $backupdata[$x1]['WEB']))); } echo '<br><br></td>
                                                        <td>'; if(implode(', ', explode(",", $backupdata[$x1]['DNS'])) == "") { echo __("Not Backed Up");} else{ print_r(implode(', ', explode(",", $backupdata[$x1]['DNS']))); } echo '<br><br></td>
                                                        <td>'; if(implode(', ', explode(",", $backupdata[$x1]['MAIL'])) == "") { echo __("Not Backed Up");} else{ print_r(implode(', ', explode(",", $backupdata[$x1]['MAIL']))); } echo '<br><br></td>
                                                        <td>'; if(implode(', ', explode(",", $backupdata[$x1]['DB'])) == "") { echo __("Not Backed Up");} else{ print_r(implode(', ', explode(",", $backupdata[$x1]['DB']))); } echo '<br><br></td>
                                                        <td>'; if(implode(', ', explode(",", $backupdata[$x1]['CRON'])) == "") { echo __("Not Backed Up");} else{ print_r(implode(', ', explode(",", $backupdata[$x1]['CRON']))); } echo '<br><br></td>
                                                        <td>'; if(implode(', ', explode(",", $backupdata[$x1]['UDIR'])) == "") { echo __("Not Backed Up");} else{ print_r(implode(', ', explode(",", $backupdata[$x1]['UDIR']))); } echo '</td>
                                                        <td>' . $backupdata[$x1]['TYPE'] . '</td>
                                                        <td data-sort-value="' . $backupdata[$x1]['RUNTIME'] . '">' . $backupdata[$x1]['RUNTIME'] . ' min</td>
                                                    </tr>';
                                                $x1++;
                                            } while ($backupname[$x1] != ''); }
                                        ?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script> 
                    function addNewObj() { window.location.href="../add/backup.php?verified=yes"; };
                </script>
                <?php hotkeys($configlocation); ?>
                <?php footerad(); ?><footer class="footer text-center"><?php footer(); ?></footer>
            </div>
        </div>
        <?php if(isset($_POST['download']) && $_POST['download'] == "yes") { echo '
                <form method="post" target="_blank" id="formx" action="' . $_POST['url'] . '">
                    <input type="hidden" name="user" value="' . $_POST['user'] . '"/>
                    <input type="hidden" name="password" value="' . $_POST['password'] . '">
                </form>'; 
            } ?> 
        <script src="../plugins/components/jquery/jquery.min.js"></script>
        <script src="../plugins/components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
        <script src="../plugins/components/sweetalert2/sweetalert2.min.js"></script>
        <script src="../plugins/components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="../plugins/components/metismenu/dist/metisMenu.min.js"></script>
        <script src="../plugins/components/moment/moment.min.js"></script>
        <script src="../plugins/components/footable/footable.min.js"></script>
        <script src="../plugins/components/waves/waves.js"></script>
        <script src="../js/notifications.js"></script>
        <script src="../js/main.js"></script>
        <script type="text/javascript">
            Waves.attach('.button', ['waves-effect']);
            Waves.init();
            var processLocation = "../process/";

            jQuery(function($){
                $('.footable').footable();
            });
            function confirmDelete(e){
                e1 = String(e)
                Swal.fire({
                  title: '<?php echo __("Delete Backup"); ?>:<br> ' + e1 +' ?',
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
                   window.location.replace("../delete/backup.php?backup=" + e1);
                  }
                })}
            <?php           
            processPlugins();
            includeScript();
            
            if(isset($_GET['error']) && $_GET['error'] == "1") {
                echo "Swal.fire({title:'" . $errorcode[1] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            }
            if(isset($_POST['delcode']) && $_POST['delcode'] == "0") {
                echo "Swal.fire({title:'" . __("Successfully Deleted!") . "', icon:'success'});";
            } 
            if(isset($_POST['addcode']) && $_POST['addcode'] == "0") {
                echo "Swal.fire({title:'" . __("Backup Scheduled!") . "', icon:'success'});";
            } 
            if(isset($_POST['restore']) && $_POST['restore'] == "0") {
                echo "Swal.fire({title:'" . __("Restore Scheduled!") . "', icon:'success'});";
            } 
            if(isset($_POST['delcode']) && $_POST['delcode'] > "0") {
                echo "Swal.fire({title:'" . $errorcode[$_POST['delcode']] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            }
            if(isset($_POST['addcode']) && $_POST['addcode'] > "0") {
                echo "Swal.fire({title:'" . $errorcode[$_POST['addcode']] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            }
            if(isset($_POST['restore']) && $_POST['restore'] > "0") {
                echo "Swal.fire({title:'" . $errorcode[$_POST['restore']] . "', html:'" . __("Please try again or contact support.") . "', icon:'error'});";
            }
            ?>
            <?php if(isset($_POST['download']) && $_POST['download'] == "yes") {    
                echo 'document.getElementById(\'formx\').submit();'; } 
            ?> 
        </script>
    </body>
</html>