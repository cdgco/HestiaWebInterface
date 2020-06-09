## Uninstalling HWI

#### Step 1:
Delete all files in your existing HWI web directory. Ensure you delete any files starting with a period such as '.htaccess', '.git', or '.gitignore'.

#### Step 2:
Run the automatic uninstaller to remove backend.
```shell
bash <(curl -s https://cdgco.github.io/hst-uninstall)
```