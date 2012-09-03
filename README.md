SiteSense
========
What is SiteSense?
--------
SiteSense is a CMS written in PHP with support for fantastic levels
of customization via the unique modules, themes, and plugin
architecture. It's extraordinarily light while at the same time still
managing to be full-featured. It's the CMS to end all CMSes.

Installation
--------
Installing SiteSense is an easy three-step process.
 1. Upload the SiteSense files you downloaded from sitesense.org.
 2. Copy and paste dbSettings.php.example to dbSettings.php and
  change the values in lines 27-47 to the correct values for your
  MySQL database.
 3. Visit yourwebsite.com/install and follow the instructions there.
  (the default install password is "startitup", without quotes)

Post-Installation
--------
After installing SiteSense, you can do just about anything. However,
it's generally good to get a few things out of the way first.
 - Change the installation password in libraries/install.php to 
  something secret.
 - If a file named INSTALL.LOCK was not created in the root of your
  SiteSense install during installation, create it now.
 - Add some menu items in the admin control panel (/admin).
 - Add some users and give them appropriate permissions.
 - Change the admin password to something you know.
 - Enable any modules you need, and disable any you don't in order to
  keep a low profile.