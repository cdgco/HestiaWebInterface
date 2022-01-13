<?php

ini_set('display_errors', 0);

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


/*
* Hestia Web Interface Configuration, Variables and Functions
*/

// Require MySQL Credentials & Arrays of Countries, Languages and Error Codes in all pages
require("config.php"); require("arrays.php");
require __DIR__ . '/../vendor/autoload.php';
PhpMyAdmin\MoTranslator\Loader::loadFunctions();
use Auth0\SDK\Auth0;

$configstyle = '1';

$con=mysqli_connect($mysql_server,$mysql_uname,$mysql_pw,$mysql_db);

if($configstyle != '2') {
    // Method 1: Initiate connection to MySQL DB and convert data into PHP Array
    if (!$con) { $mysqldown = 'yes'; }
    $config = array(); $result=mysqli_query($con,"SELECT VARIABLE,VALUE FROM `" . $mysql_table . "config`");
    while ($row = mysqli_fetch_assoc($result)) { $config[$row["VARIABLE"]] = $row["VALUE"]; }
    mysqli_free_result($result); 
    $auth0_users = array(); $result=mysqli_query($con,"SELECT HWI_USER,AUTH0_USER FROM `" . $mysql_table . "auth0-users`");
    while ($row = mysqli_fetch_assoc($result)) { $auth0_users[$row["HWI_USER"]] = $row["AUTH0_USER"]; }
    mysqli_free_result($result); mysqli_close($con);
}
else {
    // Method 2: Connection to MySQL, save config locally every 30 min, grab locally if connection fails.
    
    // Location of config.json. If possible, place outside of document root to ensure access is blocked.
    $co1 = $configlocation . "../tmp/";
    
    if (!$con) { $config = json_decode(file_get_contents( $co1 . 'config.json'), true); $mysqldown = 'yes'; }
    else { 
        $config = array(); $result=mysqli_query($con,"SELECT VARIABLE,VALUE FROM `" . $mysql_table . "config`");
        while ($row = mysqli_fetch_assoc($result)) { $config[$row["VARIABLE"]] = $row["VALUE"]; }
        mysqli_free_result($result); 
	$auth0_users = array(); $result=mysqli_query($con,"SELECT HWI_USER,AUTH0_USER FROM `" . $mysql_table . "auth0-users`");
	while ($row = mysqli_fetch_assoc($result)) { $auth0_users[$row["HWI_USER"]] = $row["AUTH0_USER"]; }
	mysqli_free_result($result); mysqli_close($con);
        if (!file_exists( $co1 . 'config.json' )) { 
            file_put_contents( $co1 . "config.json",json_encode($config));
        }  
        // Reload Config Every Hour (1800 Seconds) or if DB has been updated
        elseif ((time()-filemtime( $co1 . "config.json")) > 1800 || $config != json_decode(file_get_contents( $co1 . 'config.json'), true)) { 
            file_put_contents( $co1 . "config.json",json_encode($config)); 
        }
	if (!file_exists( $co1 . 'auth0-users.json' )) { 
            file_put_contents( $co1 . "auth0-users.json",json_encode($auth0_users));
        } 
	// Reload Auth0 Users Every Hour (1800 Seconds) or if DB has been updated
        elseif ((time()-filemtime( $co1 . "auth0-users.json")) > 1800 || $auth0_users != json_decode(file_get_contents( $co1 . 'auth0-users.json'), true)) { 
            file_put_contents( $co1 . "auth0-users.json",json_encode($auth0_users)); 
        }

    }
}


// Grab Session data for username & status
$initialusername = base64_decode($_SESSION['username']);
$loggedin = base64_decode($_SESSION['loggedin']);

// System for login as user
if($initialusername == "admin" && isset($_SESSION['proxied']) && base64_decode($_SESSION['proxied']) != '')   {
    $username = base64_decode($_SESSION['proxied']);
    $uname = base64_decode($_SESSION['proxied']);
    $displayname = $initialusername . " &rarr; " . base64_decode($_SESSION['proxied']);
}  
else {
    $uname = $initialusername;
    $username = $initialusername;
    $displayname = $initialusername;
}

/*             
* Hestia Web Interface Configuration Conversions & Definitions
* Ordered the same as settings.php
*/

// Server Configuration

date_default_timezone_set($config["TIMEZONE"]);
$sitetitle = $config["SITE_NAME"];
$themecolor = $config["THEME"] . '.css';
$locale = $config["LANGUAGE"];
if($config["DEFAULT_TO_ADMIN"] != 'true'){
    $defaulttoadmin = '';
}
else{
    $defaulttoadmin = $config["DEFAULT_TO_ADMIN"];
}

if(substr( $config["HESTIA_HOST_ADDRESS"], 0, 7 ) === "http://") {
    $config["HESTIA_HOST_ADDRESS"] = substr($config["HESTIA_HOST_ADDRESS"], 7);
}
elseif(substr( $config["HESTIA_HOST_ADDRESS"], 0, 8 ) === "https://") {
    $config["HESTIA_HOST_ADDRESS"] = substr($config["HESTIA_HOST_ADDRESS"], 8);
}
DEFINE('HESTIA_HOST_ADDRESS', rtrim($config["HESTIA_HOST_ADDRESS"], '/')); 
if($config["HESTIA_SSL_ENABLED"] == 'false'){
    $vst_ssl = 'http://';
}
else{
    $vst_ssl = 'https://';
}
if($config["HESTIA_PORT"] == ''){
    $hestia_port = '8083';
}
else{
    $hestia_port = $config["HESTIA_PORT"];
}
$vst_url = $vst_ssl . $config["HESTIA_HOST_ADDRESS"] . ':' . $hestia_port . '/api/';
$url8083 = $vst_ssl . $config["HESTIA_HOST_ADDRESS"] . ':' . $hestia_port;
if(!isset($KEY3) || (isset($KEY3) && $KEY3 == '')) { $KEY3 = 'Default HWI Secret Key'; }
if(!isset($KEY4) || (isset($KEY4) && $KEY4 == '')) { $KEY4 = 'Default HWI Secret IV'; }
if ($config["HESTIA_METHOD"] == "api"){
    DEFINE('HESTIA_ADMIN_UNAME', '');
    $vst_username = '';

    DEFINE('HESTIA_ADMIN_PW', '');
    $vst_password = '';

    $deckey = hwicryptx($config["HESTIA_API_KEY"], d);
    DEFINE('HESTIA_API_KEY', $deckey);
    $vst_apikey = $deckey;
    
    $apienabled = 'true';
}
else {
    DEFINE('HESTIA_API_KEY', '');
    $vst_apikey = '';
    
    DEFINE('HESTIA_ADMIN_UNAME', $config["HESTIA_ADMIN_UNAME"]);
    $vst_username = $config["HESTIA_ADMIN_UNAME"];

    $decpassword = hwicryptx($config["HESTIA_ADMIN_PW"], 'd');
    DEFINE('HESTIA_ADMIN_PW', $decpassword);
    $vst_password = $decpassword;
    
    $apienabled = 'false';
}

$vcpservices = curl_init();
    
curl_setopt($vcpservices, CURLOPT_URL, $vst_url);
curl_setopt($vcpservices, CURLOPT_RETURNTRANSFER,true);
curl_setopt($vcpservices, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($vcpservices, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($vcpservices, CURLOPT_POST, true);
curl_setopt($vcpservices, CURLOPT_POSTFIELDS, http_build_query(array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-sys-services', 'arg1' => 'json')));

$servicedata = curl_exec($vcpservices);

$KEY1 = $config["KEY1"]; $key1 = $config["KEY1"];
$KEY2 = $config["KEY2"]; $key2 = $config["KEY2"];
$warningson = strtolower($config["WARNINGS_ENABLED"]);

$cpicon = $config["ICON"];
$cplogo = $config["LOGO"];
$cpfavicon = $config["FAVICON"];

// Enable / Disable Sections
if(checkService('apache2') !== false || checkService('httpd') !== false || checkService('/php(.*)-fpm/') !== false) {
    if($config["WEB_ENABLED"] != 'true'){
        $webenabled = '';
    }
    else{
        $webenabled = $config["WEB_ENABLED"];
    }
}
else { $webenabled = ''; }
if(checkService('bind9') !== false || checkService('named') !== false) {
    if($config["DNS_ENABLED"] != 'true'){
        $dnsenabled = '';
    }
    else{
        $dnsenabled = $config["DNS_ENABLED"];
    }
}
else { $dnsenabled = ''; }
if(checkService('exim') !== false) {
    if($config["MAIL_ENABLED"] != 'true'){
        $mailenabled = '';
    }
    else{
        $mailenabled = $config["MAIL_ENABLED"];
    }
}
else { $mailenabled = ''; }
if(checkService('mysql') !== false || checkService('mariadb') !== false || checkService('postgresql') !== false) {
    if($config["DB_ENABLED"] != 'true'){
        $dbenabled = '';
    }
    else{
        $dbenabled = $config["DB_ENABLED"];
    }
}
else { $dbenabled = ''; }
if($config["ADMIN_ENABLED"] != 'true'){
    $adminenabled = '';
}
else{
    $adminenabled = $config["ADMIN_ENABLED"];
}
if($config["PROFILE_ENABLED"] != 'true'){
    $profileenabled = '';
}
else{
    $profileenabled = $config["PROFILE_ENABLED"];
}
if($config["CRON_ENABLED"] != 'true'){
    $cronenabled = '';
}
else{
    $cronenabled = $config["CRON_ENABLED"];
}
if($config["BACKUPS_ENABLED"] != 'true'){
    $backupsenabled = '';
}
else{
    $backupsenabled = $config["BACKUPS_ENABLED"];
}
if($config["NOTIFICATIONS_ENABLED"] != 'true'){
    $notifenabled = '';
}
else{
    $notifenabled = $config["NOTIFICATIONS_ENABLED"];
}
if($config["REGISTRATIONS_ENABLED"] != 'true'){
    $regenabled = '';
}
else{
    $regenabled = $config["REGISTRATIONS_ENABLED"];
}
if($config["OLD_CP_LINK"] == 'false'){
    $oldcpurl = '';
}
else{
    $oldcpurl = $url8083;
}

// MAIL

$phpmailenabled = strtolower($config["PHPMAIL_ENABLED"]);
$mailfrom = $config["MAIL_FROM"];
$mailname = $config["MAIL_NAME"];
$smtpenabled = strtolower($config["SMTP_ENABLED"]);
$smtpport = $config["SMTP_PORT"];
$smtphost = $config["SMTP_HOST"];
$smtpauth = strtolower($config["SMTP_AUTH"]);
$smtpuname = $config["SMTP_UNAME"];
$smtppw = $config["SMTP_PW"];
$smtpenc = strtolower($config["SMTP_ENC"]);

// Optional Links
if(checkService('vsftpd') !== false || checkService('proftpd') !== false) {
    if($config["FTP_URL"] == ''){
        $ftpurl = 'http://net2ftp.com/';
    }
    elseif($config["FTP_URL"] == 'disabled'){
        $ftpurl = '';
    }
    else{
        $ftpurl = $config["FTP_URL"];
    }
}
else { $ftpurl = ''; }
if(checkService('exim') !== false) {
    if($config["WEBMAIL_URL"] == '' || $config["WEBMAIL_URL"] == 'disabled'){
        $webmailurl = '';
    }
    else{
        $webmailurl = $config["WEBMAIL_URL"];
    }
}
else { $webmailurl = ''; }
if(checkService('mysql') !== false || checkService('mariadb') !== false) {
    if($config["PHPMYADMIN_URL"] == ''){
        $phpmyadmin = 'http://' . $config["HESTIA_HOST_ADDRESS"] . '/phpmyadmin';
    }
    elseif($config["PHPMYADMIN_URL"] == 'disabled'){
        $phpmyadmin = '';
    }
    else{
        $phpmyadmin = $config["PHPMYADMIN_URL"];
    }
}
else { $phpmyadmin = ''; }
if(checkService('postgresql') !== false) {
    if($config["PHPPGADMIN_URL"] == ''){
        $phppgadmin = 'http://' . $config["HESTIA_HOST_ADDRESS"] . '/phppgadmin';
    }
    elseif($config["PHPPGADMIN_URL"] == 'disabled'){
        $phppgadmin = '';
    }
    else{
        $phppgadmin = $config["PHPPGADMIN_URL"];
    }
}
else { $phppgadmin = ''; }
if($config["SUPPORT_URL"] == ''){
    $supporturl = '';
}
elseif($config["SUPPORT_URL"] == 'disabled'){
    $supporturl = '';
}
else{
    $supporturl = $config["SUPPORT_URL"];
}

// Optional Integration

$plugins = explode(",", str_replace(' ', '', $config["PLUGINS"]));
$pluginlinks = array();
$pluginnames = array();
$pluginicons = array();
$pluginsections = array();
$pluginadminonly = array();
$pluginnewtab = array();
$pluginhide = array();

DEFINE('GOOGLE_ANALYTICS_ID', $config["GOOGLE_ANALYTICS_ID"]);

DEFINE('INTERAKT_APP_ID', $config["INTERAKT_APP_ID"]);
DEFINE('INTERAKT_API_KEY', $config["INTERAKT_API_KEY"]);
DEFINE('CLOUDFLARE_API_KEY', $config["CLOUDFLARE_API_KEY"]);
$cfapikey = $config["CLOUDFLARE_API_KEY"];
DEFINE('CLOUDFLARE_EMAIL', $config["CLOUDFLARE_EMAIL"]);
DEFINE('CUSTOM_THEME_PRIMARY', $config["CUSTOM_THEME_PRIMARY"]);
DEFINE('CUSTOM_THEME_SECONDARY', $config["CUSTOM_THEME_SECONDARY"]);
DEFINE('AUTH0_DOMAIN', $config["AUTH0_DOMAIN"]);
DEFINE('AUTH0_CLIENT_ID', $config["AUTH0_CLIENT_ID"]);
DEFINE('AUTH0_CLIENT_SECRET', $config["AUTH0_CLIENT_SECRET"]);

// Auth0 Integration
if (isset($config["AUTH0_DOMAIN"], $config["AUTH0_CLIENT_ID"], $config["AUTH0_CLIENT_SECRET"], $_SERVER["HTTPS"]) && $config["AUTH0_DOMAIN"] != '' && $config["AUTH0_CLIENT_ID"] != '' && $config["AUTH0_CLIENT_SECRET"] != '' && $_SERVER['HTTPS'] === 'on') {

    $numremoveloc = substr_count($configlocation, '..');
    $auth0location = "https://" . $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
    if($numremoveloc > 0) {
        $i = 0;
        do {
            $auth0location = substr($auth0location, 0, strrpos( $auth0location, '/'));
            $i++;
        } while ($i < $numremoveloc);
    }
    $auth0location = $auth0location . '/process/callback.php';
    $auth0 = new Auth0([
      'domain' => $config["AUTH0_DOMAIN"],
      'client_id' => $config["AUTH0_CLIENT_ID"],
      'client_secret' => $config["AUTH0_CLIENT_SECRET"],
      'redirect_uri' => $auth0location,
      'scope' => 'openid profile email'
    ]);
} 

// HWI Functions

function hwicrypt($cs,$ca='e') { 
    $op = false; $ecm ="AES-256-CBC"; $key=hash('sha256',$KEY1); $iv=substr(hash('sha256',$KEY2),0,16); 
    if($ca=='e'){
        $op=base64_encode(openssl_encrypt($cs,$ecm,$key,0,$iv));
    } 
    elseif($ca=='d'){
        $op=openssl_decrypt(base64_decode($cs),$ecm,$key,0,$iv);
    }
    return $op;
}
function hwicryptx($cs,$ca='e') { 

    $op = false; $ecm ="AES-256-CBC"; $key=hash('sha256',$KEY3); $iv=substr(hash('sha256',$KEY4),0,16); 
    if($ca=='e'){
        $op=base64_encode(openssl_encrypt($cs,$ecm,$key,0,$iv));
    } 
    elseif($ca=='d'){
        $op=openssl_decrypt(base64_decode($cs),$ecm,$key,0,$iv);
    }
    return $op;
}
function indexMenu($l1) {
    echo '<li> 
            <a href="' . $l1 . 'index.php" class="waves-effect">
                <i class="fa fa-home fa-fw"></i> <span class="hide-menu">' . __("Home") . '</span>
            </a> 
        </li>';
}
function adminMenu($l2, $a1) {
    global $adminenabled;
    global $initialusername;
    if($initialusername == "admin" && isset($adminenabled) && $adminenabled != ''){
    echo '<li class="devider"></li>
            <li> <a href="#" class="waves-effect"><i class="fa fa-wrench fa-fw" data-icon="v"></i> <span class="hide-menu">' . __("Administration") . '<span class="fa arrow"></span> </span></a>
                <ul class="nav nav-second-level'; if(isset($a1) && $a1 != '') { echo ' in'; } echo '" id="appendadministration"> 
                    <li> <a href="' . $l2 . 'users.php"'; if($a1 == 'users') { echo ' class="active"'; } echo '><i class="ti-user fa-fw"></i><span class="hide-menu">' . __("Users") . '</span></a> </li>
                    <li> <a href="' . $l2 . 'packages.php"'; if($a1 == 'packages') { echo ' class="active"'; } echo '><i class="ti-package fa-fw"></i><span class="hide-menu">' . __("Packages") . '</span></a> </li>
                    <li> <a href="' . $l2 . 'ip.php"'; if($a1 == 'ip') { echo ' class="active"'; } echo '><i class="fa fa-sliders fa-fw"></i><span class="hide-menu">' . __("IP") . '</span></a> </li>
                    <li> <a href="' . $l2 . 'graphs.php"'; if($a1 == 'graph') { echo ' class="active"'; } echo '><i class="ti-pie-chart fa-fw"></i><span class="hide-menu">' . __("Graphs") . '</span></a> </li>
                    <li> <a href="' . $l2 . 'stats.php"'; if($a1 == 'stats') { echo ' class="active"'; } echo '><i class="ti-stats-up fa-fw"></i><span class="hide-menu">' . __("Statistics") . '</span></a> </li>
                    <li> <a href="' . $l2 . 'updates.php"'; if($a1 == 'updates') { echo ' class="active"'; } echo '><i class="fa fa-cloud fa-fw"></i><span class="hide-menu">' . __("Updates") . '</span></a> </li>';
        
                    if(checkService('iptables') !== false){ echo '
                    <li> <a href="' . $l2 . 'firewall.php"'; if($a1 == 'firewall') { echo ' class="active"'; } echo '><i class="fa fa-shield fa-fw"></i><span class="hide-menu">' . __("Firewall") . '</span></a> </li>';
                    }
                    echo '
                    <li> <a href="' . $l2 . 'server.php"'; if($a1 == 'server') { echo ' class="active"'; } echo '><i class="fa fa-server fa-fw"></i><span class="hide-menu">' . __("Server") . '</span></a> </li>
                    <li> <a href="' . $l2 . 'settings.php"'; if($a1 == 'settings') { echo ' class="active"'; } echo '><i class="fa fa-cogs fa-fw"></i><span class="hide-menu">' . __("Settings") . '</span></a> </li>
                    <li> <a href="' . $l2 . 'plugins.php"'; if($a1 == 'plugins') { echo ' class="active"'; } echo '><i class="fa fa-puzzle-piece fa-fw"></i><span class="hide-menu">' . __("Plugins") . '</span></a> </li>
                    <li> <a href="' . $l2 . 'notifications.php"'; if($a1 == 'notifications') { echo ' class="active"'; } echo '><i class="fa fa-bell fa-fw"></i><span class="hide-menu">' . __("Notifications") . '</span></a> </li>
                </ul>
            </li>';
    } 
}
function profileMenu($l3) {
    global $displayname; global $profileenabled;
    if(isset($profileenabled) && $profileenabled != ''){
    echo
        '<li class="devider"></li>
        <li>
            <a href="#" class="waves-effect"><i  class="ti-user fa-fw"></i><span class="hide-menu">' . $displayname . '<span class="fa arrow"></span></span>
            </a>
            <ul class="nav nav-second-level" id="appendaccount">
                <li> <a href="' . $l3 . 'profile.php"><i class="ti-home fa-fw"></i> <span class="hide-menu">' . __("My Account") . '</span></a></li>
                <li> <a href="' . $l3 . 'profile.php?settings=open"><i class="ti-settings fa-fw"></i> <span class="hide-menu">' . __("Account Settings") . '</span></a></li>
                <li> <a href="' . $l3 . 'log.php"><i class="ti-layout-list-post fa-fw"></i><span class="hide-menu">' . __("Log") . '</span></a> </li>
            </ul>
        </li>';
    }
}
function primaryMenu($l4, $l5, $a2) {
        global $webenabled; global $dnsenabled; global $mailenabled; global $dbenabled; global $ftpurl; global $webmailurl; global $phpmyadmin; global $phppgadmin; global $oldcpurl; global $supporturl; global $cronenabled; global $backupsenabled; global $pluginsections;
    
        if ($webenabled == 'true' || $dnsenabled == 'true' || $mailenabled == 'true' || $dbenabled == 'true') { echo '<li class="devider"></li>'; }
    
    
        if ($webenabled == 'true') { echo '<li> <a href="' . $l4 . 'web.php" class="waves-effect'; if($a2 == 'web') { echo ' active'; } echo '"><i class="ti-world fa-fw"></i><span class="hide-menu">' . __("Web") . '</span></a> </li>'; }
        if ($dnsenabled == 'true') { echo '<li> <a href="' . $l4 . 'dns.php" class="waves-effect'; if($a2 == 'dns') { echo ' active'; } echo '"><i class="fa fa-sitemap fa-fw"></i><span class="hide-menu">' . __("DNS") . '</span></a> </li>'; }
        if ($mailenabled == 'true') { echo '<li> <a href="' . $l4 . 'mail.php" class="waves-effect'; if($a2 == 'mail') { echo ' active'; } echo '"><i class="fa fa-envelope fa-fw"></i><span class="hide-menu">' . __("Mail") . '</span></a> </li>'; }
        if ($dbenabled == 'true') { echo '<li> <a href="' . $l4 . 'db.php" class="waves-effect'; if($a2 == 'db') { echo ' active'; } echo '"><i class="fa fa-database fa-fw"></i><span class="hide-menu">' . __("Database") . '</span></a> </li>'; }
    
    
        if(isset($cronenabled) && $cronenabled != ''){
        echo '<li> <a href="' . $l4 . 'cron.php" class="waves-effect'; if($a2 == 'cron') { echo ' active'; } echo '"><i  class="fa fa-calendar-plus-o fa-fw"></i> <span class="hide-menu">' . __("Cron Jobs") . '</span></a> </li>'; }
        if(isset($backupsenabled) && $backupsenabled != ''){
        echo '<li> <a href="' . $l4 . 'backups.php" class="waves-effect'; if($a2 == 'backups') { echo ' active'; } echo '"><i  class="fa fa-cloud-upload fa-fw"></i> <span class="hide-menu">' . __("Backups") . '</span></a> </li>'; }
        
    
        if ($ftpurl != '' || $webmailurl != '' || $phpmyadmin != '' || $phppgadmin != '' || in_array('apps', $pluginsections)) { echo '<li class="devider"></li>    
        <li><a href="#" class="waves-effect"><i class="fa fa-th fa-fw"></i> <span class="hide-menu">' . __("Apps") . '<span class="fa arrow"></span></span></a>
                <ul class="nav nav-second-level" id="appendapps">'; }
        if ($ftpurl != '') { echo '<li><a href="' . $ftpurl . '" target="_blank"><i class="fa fa-file-code-o fa-fw"></i><span class="hide-menu">' . __("FTP") . '</span></a></li>';}
        if ($webmailurl != '') { echo '<li><a href="' . $webmailurl . '" target="_blank"><i class="fa fa-envelope-o fa-fw"></i><span class="hide-menu">' . __("Webmail") . '</span></a></li>';}
        if ($phpmyadmin != '') { echo '<li><a href="' . $phpmyadmin . '" target="_blank"><i class="fa fa-edit fa-fw"></i><span class="hide-menu">' . __("phpMyAdmin") . '</span></a></li>';}
        if ($phppgadmin != '') { echo '<li><a href="' . $phppgadmin . '" target="_blank"><i class="fa fa-edit fa-fw"></i><span class="hide-menu">' . __("phpPgAdmin") . '</span></a></li>';}
        if ($ftpurl != '' || $webmailurl != '' || $phpmyadmin != '' || $phppgadmin != '' || in_array('apps', $pluginsections)) { echo '</ul></li>';}
        echo '<li class="devider"></li>
        <li><a href="' . $l5 . 'logout.php" class="waves-effect"><i class="fa fa-sign-out fa-fw"></i> <span class="hide-menu">' . __("Log out") . '</span></a></li>';
        if ($oldcpurl != '' || $supporturl != '') { echo '<li class="devider"></li>'; }
        if ($oldcpurl != '') { echo '<li><a href="' . $oldcpurl . '" class="waves-effect"> <i class="fa fa-tachometer fa-fw"></i> <span class="hide-menu"> ' . __("Control Panel v1") . '</span></a></li>'; }
        if ($supporturl != '') { echo '<li><a href="' . $supporturl . '" class="waves-effect" target="_blank"> <i class="fa fa-life-ring fa-fw"></i> <span class="hide-menu">' . __("Support") . '</span></a></li>'; }
}
function processPlugins() {
    global $configlocation; global $pluginnames; global $pluginhide; global $pluginnewtab; global $pluginsections; global $pluginicons; global $pluginlinks; global $username;
    $pluginlocation = $configlocation . '../plugins/';
    if(isset($pluginnames[0]) && $pluginnames[0] != '') { 
    $curplg = 0; 
    do {     
        if(strtolower($pluginhide[$curplg]) != 'y' && strtolower($pluginhide[$curplg]) != 'yes') { 
            if($username == 'admin'){
                if(strtolower($pluginnewtab[$curplg]) == 'y' || strtolower($pluginnewtab[$curplg]) == 'yes'){
                    echo "var plugincontainer" . $curplg . " = document.getElementById ('append" . $pluginsections[$curplg] . "');\n var plugindata" . $curplg . " = \"<li><a href='" . $pluginlocation . $pluginlinks[$curplg] . "/' target='_blank'><i class='fa " . $pluginicons[$curplg] . " fa-fw'></i><span class='hide-menu'>" . __($pluginnames[$curplg] ) . "</span></a></li>\";\n plugincontainer" . $curplg . ".innerHTML += plugindata" . $curplg . ";\n"; 
                }
                else {
                    echo "var plugincontainer" . $curplg . " = document.getElementById ('append" . $pluginsections[$curplg] . "');\n var plugindata" . $curplg . " = \"<li><a href='" . $pluginlocation . $pluginlinks[$curplg] . "/'><i class='fa " . $pluginicons[$curplg] . " fa-fw'></i><span class='hide-menu'>" . __($pluginnames[$curplg] ) . "</span></a></li>\";\n plugincontainer" . $curplg . ".innerHTML += plugindata" . $curplg . ";\n"; 
                }
            }
            elseif($username != 'admin' && strtolower($pluginsections[$curplg]) != 'administration')  {
                if (strtolower($pluginnewtab[$curplg]) == 'y' || strtolower($pluginnewtab[$curplg]) == 'yes') { 
                    echo "var plugincontainer" . $curplg . " = document.getElementById ('append" . $pluginsections[$curplg] . "');\n var plugindata" . $curplg . " = \"<li><a href='" . $pluginlocation . $pluginlinks[$curplg] . "/' target='_blank'><i class='fa " . $pluginicons[$curplg] . " fa-fw'></i><span class='hide-menu'>" . __($pluginnames[$curplg] ) . "</span></a></li>\";\n plugincontainer" . $curplg . ".innerHTML += plugindata" . $curplg . ";\n"; 
                }
                else {  
                    echo "var plugincontainer" . $curplg . " = document.getElementById ('append" . $pluginsections[$curplg] . "');\n var plugindata" . $curplg . " = \"<li><a href='".$pluginlocation.$pluginlinks[$curplg]."/'><i class='fa ".$pluginicons[$curplg]." fa-fw'></i><span class='hide-menu'>".__($pluginnames[$curplg])."</span></a></li>\";\n plugincontainer" . $curplg . ".innerHTML += plugindata" . $curplg . ";\n"; 
                }
            } 
        } 
        $curplg++; 
    } while ($pluginnames[$curplg] != ''); 
}
    
}

function includeScript() {
    global $configlocation; global $mysqldown; global $initialusername; global $warningson; global $config;

    if(isset($config["EXT_SCRIPT"]) && $config["EXT_SCRIPT"] != '') {
        echo $config["EXT_SCRIPT"];
    }
    echo '
        function closeModal() {    
            swal.close() 
        }
    
        const toast1 = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false,
              timerProgressBar: true,
              timer: 3500
            });
          const toast2 = Swal.mixin({
              toast: true,
              position: "top-end",
              showConfirmButton: false
            });';
      
    
    if($warningson == "all"){
        if(substr(sprintf('%o', fileperms($configlocation)), -4) == '0777') {
            echo "toast1.fire({
                    icon: 'warning',
                    title: '".__("Includes folder has not been secured")."'
                });";

        } 
        if(isset($mysqldown) && $mysqldown == 'yes') {
            echo "toast2.fire({
                    icon: 'error',
                    title: '" . __("Database Error") . "',
                    title: '" . __("MySQL Server Failed To Connect") . "'
                });";

        } 
    }
    elseif($warningson == "admin" && $initialusername == "admin"){
        if(substr(sprintf('%o', fileperms($configlocation)), -4) == '0777') {
            echo "toast1.fire({
                    icon: 'warning',
                    title: '".__("Includes folder has not been secured")."'
                });";

        } 
        if(isset($mysqldown) && $mysqldown == 'yes') {
            echo "toast2.fire({
                    icon: 'error',
                    title: '" . __("Database Error:") . "',
                    title: '" . __("MySQL Server Failed To Connect") . "'
                });";

        } 
    }
}
function formatMB($number, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB'); 
    $number = max($number, 0)*pow(1024,2);
    $pow = floor(($number ? log($number) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $number /= pow(1024, $pow);

    return round($number, $precision) . ' ' . $units[$pow]; 
} 
function formatMBNumOnly($number, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB'); 
    $number = max($number, 0)*pow(1024,2);
    $pow = floor(($number ? log($number) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $number /= pow(1024, $pow);

    return round($number, $precision); 
} 
function formatMBUnitOnly($number, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB'); 
    $number = max($number, 0)*pow(1024,2);
    $pow = floor(($number ? log($number) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $number /= pow(1024, $pow);

    return $units[$pow];
} 
function hotKeys($conflocation) {
    echo 
        '<div class="shortcuts" id="shortcuts" style="display:none;">
                        <div class="header">
                          <div class="title">'.__("Shortcuts").'</div>
                          <div class="close-s" onclick="toggleShortcuts()">x</div>

                        </div>
                            <ul>
                              <li><span class="key">1</span>'.__("Go to DASHBOARD").'</li>
                              <li><span class="key">2</span>'.__("Go to WEB").'</li>
                              <li><span class="key">3</span>'.__("Go to DNS").'</li>
                              <li><span class="key">4</span>'.__("Go to MAIL").'</li>
                              <li><span class="key">5</span>'.__("Go to DATABASE").'</li>
                              <li><span class="key">6</span>'.__("Go to CRON JOBS").'</li>
                              <li><span class="key">7</span>'.__("Go to BACKUPS").'</li>
                              <li><span class="key">8</span>'.__("Go to PROFILE").'</li>
                            </ul>
                            <ul>
                          <li><span class="key">H</span>'.__("Display/Close shortcuts").'</li>

                          <li class="step-top"><span class="key">A</span>'.__("Add New object").'</li>
                          <li><span class="key">Ctrl + Enter</span>'.__("Save Form").'</li>
                          <li><span class="key">Ctrl + Backspace</span>'.__("Exit form").'</li>
                          
                          <li class="step-top"><span class="key">Esc</span>'.__("Hide Notifications").'</li>
                          <li><span class="key">Backspace</span>'.__("Go back").'</li>
                          <li><span class="key">Ctrl + L</span>'.__("Logout").'</li>
                        
                        </ul>
                        
                        
                      </div>
                    <button class="keyboard-shortcuts theme-color" onclick="toggleShortcuts()" type="button"></button>
                    <button class="back-to-top theme-color" type="button"></button>
                    <script src="' . $conflocation . '../plugins/components/hotkeysjs/hotkeys.min.js"></script>
                    <script type="text/javascript">
                        hotkeys("1,2,3,4,5,6,7,8,h,a,ctrl+enter,ctrl+e,ctrl+backspace,backspace,ctrl+l,esc", function(event,handler) {
                          switch(handler.key){
                            case "1":window.location.href = "' . $conflocation . '../index.php";break;
                            case "2":window.location.href = "' . $conflocation . '../list/web.php";break;
                            case "3":window.location.href = "' . $conflocation . '../list/dns.php";break;
                            case "4":window.location.href = "' . $conflocation . '../list/mail.php";break;
                            case "5":window.location.href = "' . $conflocation . '../list/db.php";break;
                            case "6":window.location.href = "' . $conflocation . '../list/cron.php";break;
                            case "7":window.location.href = "' . $conflocation . '../list/backups.php";break;
                            case "8":window.location.href = "' . $conflocation . '../profile.php";break;
                            case "h":toggleShortcuts();break;
                            case "a":addNewObj();break;
                            case "ctrl+enter":submitForm();break;
                            case "ctrl+backspace":exitForm();break;
                            case "backspace":{if (document.referrer.indexOf(window.location.host) !== -1) { history.go(-1); return false; } else { window.location.href = \'' . $conflocation . '../index.php\'; };break;}
                            case "ctrl+l":window.location.href = "' . $conflocation . '../process/logout.php";break;
                            case "esc":{Swal.close();break;}
                          }
                        });
                        if(typeof addNewObj != \'function\'){
                           addNewObj = function(){ return undefined; };
                        }
                        if(typeof submitForm != \'function\'){
                           submitForm = function(){ return undefined; };
                        }
                        if(typeof exitForm != \'function\'){
                           exitForm = function(){ return undefined; };
                        }
                        </script>';
    
}
if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '') { $urlquery = '?'; } else { $urlquery = ''; }

function searchURL($result, $type, $key, $parent) { 
    if($type == "db") { $outputlink = "../edit/db.php?db=" . $result; return $outputlink; }
    if($type == "cron") { $outputlink = "../edit/cron.php?job=" . $parent; return $outputlink; }
    if($type == "mail") { 
        if($key == "ACCOUNT") { $outputlink = "../edit/mailaccount.php?domain=" . $parent . "&account=" . $result; return $outputlink; }
        if($key == "DOMAIN") { $outputlink = "../list/maildomain.php?domain=" . $result; return $outputlink; }    
    }
    if($type == "web") { $outputlink = "../edit/domain.php?domain=" . $result; return $outputlink; }
    if($type == "dns") { 
        if($key == "RECORD") { $outputlink = "../process/record-to-id.php?dnsdomain=" . $parent . "&recordvalue=" . $result; return $outputlink; }
        if($key == "DOMAIN") { $outputlink = "../list/dnsdomain.php?domain=" . $result; return $outputlink; }    
    }
}

function notifications() {
    
    global $notifenabled;
    
    if($notifenabled == "true") {

        global $vst_apikey;
        global $vst_url;
        global $vst_username;
        global $vst_password;
        global $username;

        $curlnotif = curl_init();

        curl_setopt($curlnotif, CURLOPT_URL, $vst_url);
        curl_setopt($curlnotif, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curlnotif, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlnotif, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlnotif, CURLOPT_POST, true);
        curl_setopt($curlnotif, CURLOPT_POSTFIELDS, http_build_query(array('hash' => $vst_apikey, 'user' => $vst_username,'password' => $vst_password,'cmd' => 'v-list-user-notifications','arg1' => $username,'arg2' => 'json')));

        $notifications = array_values(json_decode(curl_exec($curlnotif), true));
        $notificationkeys = array_keys(json_decode(curl_exec($curlnotif), true));


        echo '<li class="dropdown hwi-notif">
                <a class="dropdown-toggle waves-effect waves-light" href="javascript:void(0);">';

                    if($notifications[0] != '') {
                        $ack1 = 0; 
                        $ack = 0;
                        do {
                            if($notifications[$ack1]['ACK'] != 'yes') { $ack = $ack + 1; }
                            $ack1++;
                        } while (isset($notifications[$ack1])); 
                    }
                    if($ack != 0) { echo '<i id="bell" class="fa fa-bell"></i><div id="activenotification" class="notify"><span id="heartbeat" class="heartbit"></span><span id="point" class="point"></span>'; } 
                else { echo '<i class="fa fa-bell-o"></i><div class="notify">'; }
                echo '</div>
                </a>
                <ul class="dropdown-menu mailbox" style="position: absolute;width: 36vw;padding: 15px;">
                    <li>
                        <div class="message-center">
                            <div class="mail-content" id="nonotifications"><hr><h5>' . __("No Notifications") . '</h5></div>';
                                if($notifications[0] != '') {
                                    $x1 = 0;
                                    do {
                                        if($notifications[$x1]['ACK'] != 'yes') { echo '<div class="mail-content mail-content-notif" id="notification'.$notificationkeys[$x1].'"><hr>
                                                <h5><b>'.$notifications[$x1]['TOPIC'].'</b></h5><span class="pull-right" style="cursor:pointer;"><i onclick="dismissNotification('.$notificationkeys[$x1].');" class="fa fa-close" style="position: relative;top: -30px;"></i></span> <span class="mail-desc">'.$notifications[$x1]['NOTICE'].'</span><br><br><span class="time">' . $notifications[$x1]['DATE'] . ' ' . $notifications[$x1]['TIME'] . '</span></div>'; }
                                        $x1++;
                                    } while (isset($notifications[$x1])); 
                                    echo '<hr>';
                                }
                        echo '</div>
                    </li>
                </ul>
            </li>';

     }
}

function footer() {
    
    global $sitetitle;
    global $configlocation;
    global $config;
    
    if($config["CUSTOM_FOOTER"] != 'true') {
    
        echo '&copy; ' . date("Y") . ' ' . $sitetitle . '.';
    }
    else { echo $config["FOOTER"]; }
    if($config["HWI_BRANDING"] != 'false') {  
        echo ' ' . __("Hestia Web Interface") . ' ';
        require $configlocation . 'versioncheck.php';
        echo ' ' . __("by Carter Roeser") . '.';
    }
}
function headerad() {
    global $config; global $username;
    if((isset($config["HEADER_AD"]) && $config["HEADER_AD"] != '') && (isset($config["ADMIN_ADS"]) && $config["ADMIN_ADS"] == 'true') && $username == 'admin') {
        echo '<center>' . $config["HEADER_AD"] . '</center>';
    }
}
function footerad() {
    global $config; global $username;
    if((isset($config["FOOTER_AD"]) && $config["FOOTER_AD"] != '') && (isset($config["ADMIN_ADS"]) && $config["ADMIN_ADS"] == 'true') && $username == 'admin') {
        echo '<center>' . $config["FOOTER_AD"] . '</center>';
    }
}
function checkService($requestedService) {
    global $servicedata;
    if($requestedService[0] == '/') {return preg_match($requestedService, $servicedata);}

    if( strpos($servicedata, $requestedService) !== false ) { return true; }
    else { return false; }
}
?>
