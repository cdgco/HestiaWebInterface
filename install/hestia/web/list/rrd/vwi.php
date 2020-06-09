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

if(strpos($_SERVER[HTTP_REFERER], 'admin/list/graphs.php')) {
    $real_path = realpath($_SERVER["DOCUMENT_ROOT"].$_SERVER['QUERY_STRING']);
    if (empty($real_path)) exit;
    $dir_name = dirname($real_path);
    $dir_name = dirname($dir_name);
    if ($dir_name != $_SERVER["DOCUMENT_ROOT"].'/rrd') exit;
    header("X-Accel-Redirect: ".$_SERVER['QUERY_STRING']);
    header("Content-Type: image/png");
}
else {
    echo 'Access Denied';
    exit();
}