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
if (file_exists( '../includes/config.php' )) { require( '../includes/includes.php'); }  else { header( 'Location: ../install' ); exit(); };

if($auth0) { $auth0->logout(); }

if(base64_decode($_SESSION['loggedin']) == 'true') {}
else { header('Location: ../login.php'); exit();}

if($initialusername == "admin" && isset($_SESSION['proxied']) && base64_decode($_SESSION['proxied']) != '')   {
    $_SESSION['proxied'] = '';
    unset($_SESSION['proxied']);
    header("Location: ../admin/list/users.php");
}  
else {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    header('Location: ../login.php'); 
    exit;
}

?>