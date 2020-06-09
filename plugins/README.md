# HWI Plugins
The HWI Plugin System allows simple development and installation for custom apps or third party integrations to HWI.

## Plugins

- [example-plugin](https://github.com/cdgco/HestiaWebInterface/tree/master/plugins/example-plugin) - Template for developing plugins for Hestia Web Interface


Development Instructions:
1. Name your project somthing unique as there cannot be two plugins with the same name.
2. Every plugin must include a manifest.xml file in the base of the plugin folder. (An example of this file can be found in the plugins/example-plugin folder).
3. The manifest.xml file must include a plugin name, icon, section, admin-only, new-tab, hide, custom-tag, and custom-tag-content value. Further details are available within the example manifest.xml.
4. Include a README.md with installation instructions, a link to this page, and a link to the HestiaWebInterface repository home.
4. Once finished and made available online, create a pull request to this file adding your plugin.

Installation Instructions:

1. Upload your plugin to the 'plugins' folder of your HWI installation.
2. Edit the HWI settings from either the settings page in the admin panel or from your MySQL database and add the plugin name to the plugin list.
3. Follow any further instructions as specified by the developer.
