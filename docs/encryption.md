## Encryption

### About
Hestia Web Interface stores credentials using AES-256-CBC encryption based off of four randomly generated keys upon install.

### Encrypting Credentials for MySQL

In order to save your credentials in MySQL, you must encrypt them first, using the hwicryptx function in the includes.php file.
This can be done in two ways:
1. Run the following command from your install directory, replacing "PASSWORD OR API KEY HERE" with your HestiaCP admin password or API key.
```bash
php -r 'error_reporting(0); include "includes/includes.php"; echo "\n".hwicryptx("PASSWORD OR API KEY HERE")."\n\n";'
```
2. Create the following PHP file in your install directory, replacing "PASSWORD OR API KEY HERE" with your HestiaCP admin password or API key. Then visit the PHP file to retrieve your encrypted password, deleting the file when complete.
```php
<?php include 'includes/includes.php';

echo hwicryptx('PASSWORD OR API KEY HERE');
```
