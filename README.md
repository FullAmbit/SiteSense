SiteSense
========
What is SiteSense?
--------
SiteSense is a simple but powerful, flexible, secure and high-performance 
web application platform. At the foundation of the platform is a content 
management and blog publishing system. SiteSense is extraordinarily 
lightweight while at the same time still managing to be full-featured.

SiteSense is easily extendable making it capable of being the starting point 
for any web application. The framework is constantly scrutinized for security 
purposes and is written in PHP &amp; MySQL utilizing the latest best practices.

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

More
--------
[SiteSense.org](http://www.sitesense.org/)
[User Manual](http://sitesense.org/docs/user-manual/index.html)
[Developer Documentation](https://github.com/FullAmbit/SiteSense/wiki)
[GitHub](https://github.com/FullAmbit/SiteSense)
[License](https://raw.github.com/FullAmbit/SiteSense/development/LICENSE.txt)
[#SiteSense on freenode](irc://irc.freenode.net/SiteSense)