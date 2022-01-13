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
* Table structure for table `hwi_auth0-users`
*/

CREATE TABLE IF NOT EXISTS `hwi_auth0-users` (
  `HWI_USER` varchar(64) NOT NULL,
  `AUTH0_USER` varchar(1024) NOT NULL,
  PRIMARY KEY (`HWI_USER`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `hwi_config` (`VARIABLE`, `VALUE`) VALUES
('AUTH0_DOMAIN', ''),
('AUTH0_CLIENT_ID', ''),
('AUTH0_CLIENT_SECRET', '');
