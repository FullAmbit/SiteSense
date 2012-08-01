<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author     Full Ambit Media, LLC <pr@fullambit.com>
* @copyright  Copyright (c) 2011 Full Ambit Media, LLC (http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
function dbSettings() {
	dbSettingsSecurity();
	/* begin user edits */
	return array(
		'dsn' => 'mysql:host=localhost;port=3306;dbname=sitesense',
		/*
			example DSN value:
			mysql:host=localhost;port=3306;dbname=sitesense
			See php.net's PDO Drivers section for more info on DSN's.
			http://us3.php.net/manual/en/pdo.drivers.php
		*/
		'username' => 'root',
		'password' => 'mufasa15',
		'tablePrefix' => 'sitesense_'
		/*
			table prefix is appended before all table names in
			queries automatically. This allows multiple copies of
			this CMS to be run from a single database
			Handy when your hosting provider restricts the
			number of databases you can use.
		*/
	);
	/* do not edit past this point */
}
function dbSettingsSecurity() { 
     /* 
          extra security to prevent injected scripts from reading this file 
          This code will not allow this script to run from anything but the 
          index.php in the root directory. (or appropriate parent directory) 
     */ 
     $includeList=get_included_files(); 
     /* 
          Swap slashes so our check works in winblows 
     */ 
     $fixedInclude=str_replace('\\','/',$includeList[0]); 
     $fixedFile=str_replace('\\','/',__FILE__); 
     $self=str_replace(dirname($fixedInclude),'',$fixedFile); 
     $compare=str_replace($self,'/index.php',$fixedFile); 
     if ( 
          ($compare==$fixedInclude) && 
          (!defined('dbInfoDefined')) 
     ) { 
          define('dbInfoDefined',true); 
     } else killHacker('Attempt to call dbSettings directly'); 
}
?>