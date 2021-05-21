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
* Table structure for table `hwi_config`
*/

CREATE TABLE IF NOT EXISTS `hwi_config` (
  `VARIABLE` varchar(64) NOT NULL,
  `VALUE` varchar(1024) NOT NULL,
  PRIMARY KEY (`VARIABLE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `hwi_config` (`VARIABLE`, `VALUE`) VALUES
('TIMEZONE', 'America/Los_Angeles'),
('SITE_NAME', ''),
('THEME', 'default'),
('LANGUAGE', 'en_US.utf8'),
('DEFAULT_TO_ADMIN', 'true'),
('HESTIA_HOST_ADDRESS', ''),
('HESTIA_SSL_ENABLED', 'true'),
('HESTIA_PORT', '8083'),
('HESTIA_METHOD', 'credentials'),
('HESTIA_API_KEY', ''),
('HESTIA_ADMIN_UNAME', 'admin'),
('HESTIA_ADMIN_PW', ''),
('KEY1', 'INSERT-KEY-HERE'),
('KEY2', 'INSERT-KEY-HERE'),
('WARNINGS_ENABLED', 'admin'),
('ICON', 'admin-logo.png'),
('LOGO', 'admin-text.png'),
('FAVICON', 'favicon.ico'),
('WEB_ENABLED', 'true'),
('DNS_ENABLED', 'true'),
('MAIL_ENABLED', 'true'),
('DB_ENABLED', 'true'),
('ADMIN_ENABLED', 'true'),
('PROFILE_ENABLED', 'true'),
('CRON_ENABLED', 'true'),
('BACKUPS_ENABLED', 'true'),
('NOTIFICATIONS_ENABLED', 'true'),
('REGISTRATIONS_ENABLED', 'false'),
('OLD_CP_LINK', 'false'),
('HWI_BRANDING', 'true'),
('CUSTOM_FOOTER', 'false'),
('FOOTER', ''),
('PHPMAIL_ENABLED', 'false'),
('MAIL_FROM', ''),
('MAIL_NAME', ''),
('SMTP_ENABLED', 'true'),
('SMTP_PORT', '587'),
('SMTP_HOST', ''),
('SMTP_AUTH', 'true'),
('SMTP_UNAME', ''),
('SMTP_PW', ''),
('SMTP_ENC', 'tls'),
('FTP_URL', 'disabled'),
('WEBMAIL_URL', 'disabled'),
('PHPMYADMIN_URL', ''),
('PHPPGADMIN_URL', ''),
('SUPPORT_URL', ''),
('PLUGINS', ''),
('GOOGLE_ANALYTICS_ID', ''),
('INTERAKT_APP_ID', ''),
('INTERAKT_API_KEY', ''),
('CLOUDFLARE_API_KEY', ''),
('CLOUDFLARE_EMAIL', ''),
('AUTH0_DOMAIN', ''),
('AUTH0_CLIENT_ID', ''),
('AUTH0_CLIENT_SECRET', ''),
('CUSTOM_THEME_PRIMARY', ''),
('CUSTOM_THEME_SECONDARY', ''),
('HEADER_AD', ''),
('FOOTER_AD', '')
('ADMIN_ADS', 'true'),
('EXT_SCRIPT', '');

/*
* Table structure for table `hwi_auth0-users`
*/

CREATE TABLE IF NOT EXISTS `hwi_auth0-users` (
  `HWI_USER` varchar(64) NOT NULL,
  `AUTH0_USER` varchar(1024) NOT NULL
  PRIMARY KEY (`HWI_USER`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
