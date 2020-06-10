## If Selective Install was used

NOTE: v2.0.0 and older require a clean install and cannot be installed through upgrade.

#### Step 1:
Delete all files in your existing HWI web directory except includs/config.php. Ensure you delete any files starting with a period such as '.htaccess', '.git', or '.gitignore'.


#### Step 2:
[Download the latest release](https://github.com/cdgco/HestiaWebInterface/archive/v0.1.0-Beta.zip) of HWI from GitHub.

#### Step 3:
Extract Hestia Web Interface to the blank domain directory.


#### Step 4:
Update the Hestia Web Interface backend on your Hestia server.
```shell
bash <(curl -s https://cdgco.github.io/hst-backend)
```

Upgrade Complete
