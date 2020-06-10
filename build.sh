#!/bin/bash
printf "Hestia Web Interface Release Builder\n\n"
printf "Enter the desired release version (WITHOUT LEADING 'V'): "
read VERSION

git checkout -b v$VERSION 

if [ -f includes/version.php ] ; then
    rm includes/version.php
fi

echo "<?php \$currentversion = 'v$VERSION'; \$ghv2 = 'QXV0aG9yaXphdGlvbjogdG9rZW4gNDgzODAzOWVhYmIwYjNhNTlkMzA1NGNiYzk2YjI4YTcyZDA1MTRmYw==';" > includes/version.php

if [ -f docs/README.md ] ; then
    rm docs/README.md
fi

echo "# Hestia Web Interface v$VERSION
<hr>

## About

Hestia Web Interface is a PHP Control Panel and Web Interface that integrates with the HestiaCP API to provide a beautiful user friendly experience. 

HWI features the ability to rebrand the control panel, change the theme, install it wherever you want, restrict access to users, easily edit options and offers integrations to services such as Google Analytics, Cloudflare, Interakt and many more coming soon. 

## Features

- Seamless integration with HestiaCP
- Dynamic design with mobile support
- Multiple themes including dark theme
- Integrations with Auth0, Cloudflare, Interakt, Net2FTP and Google Analytics
- Plugin system with webmail (Rainloop), FTP (MonstaFTP) and billing plugins coming soon.
- Web based installation and configuration within admin panel

## Demo

Check out the [Demo](https://cdgtech.one/hwi/demo.php) to test out HWI before you download.

## Donate

If you like HWI, please consider [donating](http://paypal.me/CJREvents) to show your support and help me focus more time on it. I'm a full time student and have many other side jobs and projects, thanks for the support!

## License

GNU General Public License v3.0" > docs/README.md

sed -i "s#Hestia Web Interface.*>#Hestia Web Interface v$VERSION\n>#g" docs/_coverpage.md
sed -i "s#archive/.*.zip#archive/v$VERSION.zip#g" docs/manual-install.md
sed -i "s#archive/.*.zip#archive/v$VERSION.zip#g" docs/manual-upgrade.md
sed -i "s#archive/.*.zip#archive/v$VERSION.zip#g" docs/select-install.md
sed -i "s#archive/.*.zip#archive/v$VERSION.zip#g" docs/select-upgrade.md
sed -i "s#HestiaWebInterface@.*/css/style.css#HestiaWebInterface@$VERSION/css/style.css#g" install/hestia/web/download/backup/hwi.php
sed -i "s#HestiaWebInterface@.*/plugins/images/favicon.png#HestiaWebInterface@$VERSION/plugins/images/favicon.png#g" install/hestia/web/templates/r_1.php
sed -i "s#HestiaWebInterface@.*/css/style.css#HestiaWebInterface@$VERSION/css/style.css#g" install/hestia/web/templates/r_1.php
sed -i "s#HestiaWebInterface@.*/css/colors/default.css#HestiaWebInterface@$VERSION/css/colors/default.css#g" install/hestia/web/templates/r_1.php
sed -i "s#HestiaWebInterface@.*/js/main.js#HestiaWebInterface@$VERSION/js/main.js#g" install/hestia/web/templates/r_1.php
sed -i "s#HestiaWebInterface@.*/plugins/images/favicon.png#HestiaWebInterface@$VERSION/plugins/images/favicon.png#g" install/hestia/web/templates/r_2.php
sed -i "s#HestiaWebInterface@.*/css/style.css#HestiaWebInterface@$VERSION/css/style.css#g" install/hestia/web/templates/r_2.php
sed -i "s#HestiaWebInterface@.*/css/colors/default.css#HestiaWebInterface@$VERSION/css/colors/default.css#g" install/hestia/web/templates/r_2.php
sed -i "s#HestiaWebInterface@.*/js/main.js#HestiaWebInterface@$VERSION/js/main.js#g" install/hestia/web/templates/r_2.php
sed -i "s#HestiaWebInterface@.*/plugins/images/favicon.png#HestiaWebInterface@$VERSION/plugins/images/favicon.png#g" install/hestia/web/templates/r_3.php
sed -i "s#HestiaWebInterface@.*/css/style.css#HestiaWebInterface@$VERSION/css/style.css#g" install/hestia/web/templates/r_3.php
sed -i "s#HestiaWebInterface@.*/css/colors/default.css#HestiaWebInterface@$VERSION/css/colors/default.css#g" install/hestia/web/templates/r_3.php
sed -i "s#HestiaWebInterface@.*/js/main.js#HestiaWebInterface@$VERSION/js/main.js#g" install/hestia/web/templates/r_3.php
sed -i "s#HestiaWebInterface@.*/css/colors/blue.css#HestiaWebInterface@$VERSION/css/style.css\" rel=\"stylesheet\"><link href=\"https://cdn.jsdelivr.net/gh/cdgco/HestiaWebInterface@$VERSION/css/colors/blue.css#g" .htaccess

printf "Enter the changelog in HTML (<br>) format: "
read CHANGELOG

sed -i "s/## Changelog.*###/## Changelog\n\n### v$VERSION\n$CHANGELOG\n\n###/g" docs/changelog.md

if [ -f install/hestia.tar.gz ] ; then
    rm install/hestia.tar.gz
fi

tar -cvzf install/hestia.tar.gz -C install hestia

git commit -a -m "v$VERSION"
git push origin v$VERSION
git checkout master

printf "Build complete. New branch on Github named v$VERSION.\n\n"
read -p "Press [Enter] key to close ..."
exit 1

fi
