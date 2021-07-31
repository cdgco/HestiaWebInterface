## Automatic Install
To automatically install all HWI components on the same server, follow these steps:


#### Step 1:
Enter a blank directory within your domain's document root.

Example:
```shell
cd /home/admin/web/mydomain.com/public_html/
```
#### Step 2:
Run the automatic installer.
```shell
bash <(curl -s https://cdgco.github.io/hst-full)
```  
  
   
When promted during the automatic installer to "Enter the full web address of your installation", enter the URL where Hestia Web Interface will be accessed from, including the "http(s)://" and the following "/".

#### Step 3: 

Visit the URL of your installation in your browser, ensure all requirements are met.

Create a new MySQL / MariaDB database and user or use an existing MySQL / MariaDB database and user and enter the details into the installer. The specified user must have basic read and write access to the database.

'root' users and users without passwords are accepted but are not recommended for added security. 

localhost / 127.0.0.1, web addresses and IP addresses are acceptable hosts. 

Ensure that your selected database does not have an existing table named hwi_config (or a table with your specified prefix). If there is a table under the same name, change the prefix to a unique name.

Continue the installation and configure HWI to your liking, entering the desired settings in the web based configurator. For help, visit the [configuration documentation](web-config).

#### Step 4:
Enable API Access.

- Log into your original HestiaCP panel, e.g. https://MY-HESTIA-SERVER:8083
- Visit the Server Configuration page, https://MY-HESTIA-SERVER:8083/edit/server
- Under Server -> Security, ensure that API Access is enabled and enter the public IP address of the web server running Hestia Web Interface in the 'Allowed IP addresses for API' section (even if Hestia Web Interface is running on the same server as HestiaCP).
- Save your configuration.

#### Step 5:
Secure the installation folder.


chmod the 'includes' folder to 755 after configuration is complete.
Failure to do so will leave your configuration file open to the public to be re-written or broken.

Example:
```shell
chmod 755 includes
```

Installation is now complete. Visit your URL to start using Hestia Web Interface.


[Video Tutorial](https://www.youtube.com/watch?v=0BAzGkF5Y8Y&list=PL4JkcC_rCsyf9ha5OBrWqDS4xWC3hZgfz)
