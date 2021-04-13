# Custom Opscidia Bootstrap3 for OJS 3+

This is a custom theme for the latest OJS 3.1.2.4 release.

It is based on :
1. The community-built theme for OJS 3+ that implements Bootstrap 3 components.
https://github.com/NateWr/bootstrap3
Version 3.2.0 of this plugin which is compatible with a 3.1.2.4 OJS instance.
2. The Old Gregg Theme for OJS 3 that uses JATS Parser Plugin for automatic rendering of articles in JATS XML format and displaying them on article landing page.
https://github.com/Vitaliy-1/oldGregg

## Licensing
The opscidia-custom-theme is licensed under GPLv3 (as both bootstrap3 and oldGregg are). 

## Installation

1. Download the [latest release](https://github.com/Opscidia/opscidia-custom-theme/releases).
2. Unpack the .zip file and move the `opscidia-custom-theme` directory to your OJS installation at `/plugins/themes/opscidia-custom-theme`.
3. Make sure that the user *www-data* is the owner of the `opscidia-custom-theme` and all of it's sub-content. You can do this by executing the recursive Linux shell command :<br>
`sudo chown -R www-data:www-data opscidia-custom-theme`<br>
*or, if you are already inside the **opscidia-custom-theme** folder :*<br>
`sudo chown -R www-data:www-data ./`
4. Login to the admin area of your OJS website. Browse to the `Settings` > `Website` > `Plugins` page and enable the Bootstrap 3 Opscidia theme.
5. Browse to the `Settings` > `Website` > `Appearance` page and select Bootstrap 3 from the theme option and save your change.

You can now see the Bootstrap 3 theme on your website. Go back to the `Settings` > `Website` > `Appearance` page (or refresh the page if it is still open) to view options to switch to any of the bundled Bootstrap 3 themes.

### Tips

If you experience any issue with the theme, try :
* To (**temporarely**) allow every permissions to the folder (that is ownded by www-data, as indicated in step 3.) by executing `sudo chmod 777 opscidia-custom-theme`.
* To restart Apache service using `sudo service apache2 restart`, especially after every edit on the ojs directory.
* To make sure that you executed the `chown` command above **recursively**, in order to ensure that all the sub-content of the theme is owned by *www-data*.