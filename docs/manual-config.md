## Manual Configuration

#### Config File

To start manual configuration, edit the 'includes/config-example.php' file.

HWI requires a MySQL database to store and serve the control panel configuration.

You may use either a MySQL or MariaDB database and user with read and write access to the corresponding database. This database may either be empty or populated, as long as there is no table by the name of 'hwi\_config' (or a table with your specified prefix).

'root' users and users without passwords are accepted but are not recommended for added security. 

localhost / 127.0.0.1, web addresses and IP addresses are acceptable hosts. 

* mysql\_server: Address to MySQL Server. URL, IP Address or localhost.  
* mysql\_uname: Username to MySQL user with access to database.  
* mysql\_pw: Password to MySQL user.  
* mysql\_db: Database Name, accessable by specified user.  
* mysql\_table: Unused table prefix. 'hwi\_' by default. If a table called 'config' starting with the prefix exists, it will be dropped by the installer.  

Save this file as config.php

#### MySQL Database

To initialize the MySQL Database, use the included hwi_config.sql file in the install directory.
Create a table within your specified MySQL Database either by importing the hwi_config.sql in phpmyadmin or by pasting the following commands into MySQL:

Create Table (Change Prefix If Necessary):
```SQL
CREATE TABLE `hwi_config` (
  `VARIABLE` varchar(64) NOT NULL,
  `VALUE` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

Initialize Table (Change Prefix If Necessary):
```SQL
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
('CUSTOM_THEME_PRIMARY', ''),
('CUSTOM_THEME_SECONDARY', ''),
('HEADER_AD', ''),
('FOOTER_AD', '')
('ADMIN_ADS', 'true'),
('EXT_SCRIPT', '');
COMMIT;
```

Set Primary Column

```SQL
ALTER TABLE `hwi_config`
  ADD PRIMARY KEY (`VARIABLE`);
COMMIT;
```

## MySQL Values

#### Server Configuration
* TIMEZONE: Desired timezone in order for cron jobs and cached config to function.
For a list of valid timezones and syntax, visit [http://php.net/manual/en/timezones.php](http://php.net/manual/en/timezones.php).
* SITE_NAME: The name you would like to be displayed as the site title and CP name.
* THEME: Default heme color for the control panel. Case sensitive. Accepted values: 'default', 'orange', 'blue', 'purple' and 'dark'.
* LANGUAGE: Default language for control panel. Case sensitive. 
 - Accepted values: 
   - 'ar_EG.utf8'
   - 'bs_BA.utf8'
   - 'zh_CN.utf8'
   - 'cs_CZ.utf8'
   - 'da_DK.utf8'
   - 'de_DE.utf8'
   - 'el_GR.utf8'
   - 'en_US.utf8'
   - 'es_US.utf8'
   - 'fa_IR'
   - 'fi_FI.utf8'
   - 'fr_FR.utf8'
   - 'hu_HU.utf8'
   - 'id_ID.utf8'
   - 'it_IT.utf8'
   - 'ja_JP.utf8'
   - 'ka_GE.utf8'
   - 'nl_NL.utf8'
   - 'nn_NO.utf8'
   - 'pl_PL.utf8'
   - 'pt_BR.utf8'
   - 'pt_PT.utf8'
   - 'ro_RO.utf8'
   - 'ru_RU.utf8'
   - 'sv_SE.utf8'
   - 'tr_TR.utf8'
   - 'zh_TW.utf8'
   - 'uk_UA.utf8'
   - 'vi_VN'
* DEFAULT\_TO\_ADMIN: Choose whether or not the admin should go to the admin panel by default after login. Accepted values: 'true' or 'false'.
* HESTIA\_HOST\_ADDRESS: URL or IP address of HestiaCP without 'http://', ':8083', or the following slash.
Ex: 'mydomain.com' or '8.8.8.8'.
* HESTIA\_SSL\_ENABLED: Enter 'true' or 'false' depending on if SSL is enabled for your HestiaCP installation. 'true' by default. Enter 'true' if your HestiaCP url (mydomain.com:8083) starts with 'https://'.
* HESTIA\_PORT: Port of your HestiaCP installation. '8083' by default.
* HESTIA\_METHOD: Choose whether to use an API Key or username and password for API authentication. 'api' or 'credentials'.
* HESTIA\_API\_KEY: AES-256-CBC encrypted HestiaCP generated API Key if 'api' enabled. Uses encryption keys stored in config.php (KEY3 & KEY4). Read [Encrypting Credentials](encryption) for more info.
* HESTIA\_ADMIN\_UNAME: Username of the HestiaCP admin account if 'credentials' enabled. 'admin' by default.
* HESTIA_ADMIN_PW: AES-256-CBC encrypted password for HestiaCP admin account if 'credentials' enabled. Uses encryption keys stored in config.php (KEY3 & KEY4). Read [Encrypting Credentials](encryption) for more info.
* KEY1: Encryption Key for backend authentication. Replace with random string.
* KEY2: Encryption Key for backend authentication. Replace with random string.
* WARNINGS_ENABLED: Choose who should see warning messages about server connection and security issues. Accepted values: 'none', 'admin' and 'all'.
* ICON: Path to icon from plugins/images folder.  Read [Branding section](branding) for more info.
* LOGO: Path to logo from plugins/images folder.  Read [Branding section](branding) for more info.
* FAVICON: Path to favicon from plugins/images folder.  Read [Branding section](branding) for more info.
* HEADER_AD: HTML Ad to display on all page headers.
* FOOTER_AD: HTML Ad to display on all page footers.
* ADMIN_ADS: Choose whether or not to display ads to Admin user. 'true' or 'false'.
* EXT_SCRIPT: Javascript code to include on all pages.

#### Enable / Disable Sections
* WEB\_ENABLED: Enter 'true' or 'false'. If 'false', hides all web domains and control to web domain settings. Case sensitive.
* DNS\_ENABLED: Enter 'true' or 'false'. If 'false', hides all dns domains and control to dns domain settings. Case sensitive.
* MAIL\_ENABLED: Enter 'true' or 'false'. If 'false', hides all mail domains, control to mail domain settings and webmail access. Case sensitive.
* DB\_ENABLED: Enter 'true' or 'false'. If 'false', hides all databases, control to database settings and web based database access. Case sensitive.
* ADMIN\_ENABLED: Enter 'true' or 'false'. If 'false', hides admin panel, and all admin specific control. Disables web based settings (settings must be configured from MySQL). Case sensitive.
* PROFILE\_ENABLED: Enter 'true' or 'false'. If 'false', hides user profile and user settings page. Case sensitive.
* CRON\_ENABLED: Enter 'true' or 'false'. If 'false', hides all cron jobs and control to cron job settings. Case sensitive.
* BACKUPS\_ENABLED: Enter 'true' or 'false'. If 'false', hides all backups and control to backup settings. Case sensitive.
* NOTIFICATIONS\_ENABLED: Enter 'true' or 'false'. If 'false', disables dashboard notification system. Case sensitive.
* REGISTRATIONS\_ENABLED: Enter 'true' or 'false'. If 'false', hides link to sign up. See [Registrations section](registrations) to learn how to configure. Case sensitive.
* OLD\_CP\_LINK: Enter 'true' or 'false'. If 'false', hides link to old HestiaCP interface. Case sensitive.
* HWI_BRANDING: Enter 'true' or 'false'. If 'false', hides HWI bradning from page footers. Case sensitive. Read [Branding section](branding) for more info.
* CUSTOM_FOOTER: Enter 'true' or 'false'. If 'false', uses default site name as branding in page footers. Case sensitive. Read [Branding section](branding) for more info.
* FOOTER: Enter custom footer message if 'CUSTOM_FOOTER' setting is 'true'. Read [Branding section](branding) for more info.

#### Mail Config
* PHPMAIL\_ENABLED: Enter 'true' or 'false'. Allows emailing credentials once database or email account are created.
* MAIL\_FROM: From address for PHPMail messages.
* MAIL\_NAME: From name for PHPMail messages.
* SMTP\_ENABELD: Choose whether to send email through PHP SendMail or SMTP. Accepted value: 'true' and 'false'.
* SMTP\_PORT: Port for SMTP Connection. Usually 25 (unencrypted), 465 (SSL) or 587 (TLS).
* SMTP\_HOST: SMTP server address. URL, IP or localhost.
* SMTP\_AUTH: Choose whether to authenticate SMTP with a username and password. Accepted value: 'true' and 'false'.
* SMTP\_UNAME: Username to SMTP account if auth enabled.
* SMTP\_PW: Password to SMTP account if auth enabled.
* SMTP\_ENC: SMTP Encryption Method. Accepted values: 'ssl', 'tls' or 'none'.


#### Optional Links
* FTP\_URL: Link to WebFTP client in menu. Leave blank for default or enter 'disabled' to disable. Case sensitive.
* WEBMAIL\_URL: Link to webmail client in menu. Leave blank for default or enter 'disabled' to disable. Case sensitive.
* PHPMYADMIN\_URL: Link to phpMyAdmin in menu. Leave blank for default or enter 'disabled' to disable. Case sensitive.
* PHPPGADMIN\_URL: Link to phpPgAdmin in menu. Leave blank for default or enter 'disabled' to disable. 'disabled' by default. Case sensitive.
* SUPPORT\_URL: Option link to web based support. Leave blank or enter 'disabled' to disable. 'disabled' by default. Case sensitive.

#### Integrations
* PLUGINS: Comma seperated list of installed plugins. Read the [plugins section](plugins) for more info. Case Sensitive.
* GOOGLE_ANALYTICS_ID: Create and enter your Google Analytics ID to enable site statistics tracking. Read the [Google Analytics section](ga) for more info.
* INTERAKT_APP_ID: Create and enter your Interakt App ID to enable customer support on your site. Read the [Interakt section](interakt) for more info.
* INTERAKT_API_KEY: Create and enter your Interakt API Key to enable user management and tracking on your site. Read the [Interakt section](interakt) for more info.
* CLOUDFLARE_API_KEY: Enter your Cloudflare Global API Key to enable Cloudflare DNS integration. Read the [Cloudflare section](cloudflare) for more info.
* CLOUDFLARE_EMAIL: Enter your Cloudflare account email address to enable Cloudflare DNS integration. Read the [Cloudflare section](cloudflare) for more info.


[Video Tutorial](https://www.youtube.com/watch?v=caribXoTlBU&list=PL4JkcC_rCsyf9ha5OBrWqDS4xWC3hZgfz)
