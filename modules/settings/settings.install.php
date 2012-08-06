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
function settings_settings() {
    return array(
        'name'      => 'settings',
        'shortName' => 'settings'
    );
}

function settings_install($db,$drop=false,$firstInstall=false,$lang="en_us") {
	$structures = array(
		'settings' => array(
			'id'		=> SQR_IDKey,
			'name'		=> 'VARCHAR(127) NOT NULL',
			'category'	=> 'VARCHAR(31) DEFAULT NULL',
			'value'		=> 'MEDIUMTEXT'
		)
	);
	$defaultSettings = array(
        'siteTitle' => 'SiteSense',
        'homepage' => 'default',
        'theme' => 'default',
        'characterEncoding' => 'utf-8',
        'compressionEnabled' => 0,
        'compressionLevel' => 9,
        'userSessionTimeOut' => 1800, /* in seconds */
        'useModRewrite' => true,
        'hideContentGuests' => 'no',
        'showPerPage' => 5,
        'rawFooterContent' => '&copy; SiteSense',
        'parsedFooterContent' => '&copy; SiteSense',
        'cdnSmall' => '',
        'cdnFlash' => '',
        'cdnLarge' => '',
        'useCDN' => '0',
        'cdnBaseDir' => '',
        'defaultBlog' => 'news',
        'useBBCode' => '1',
        'jsEditor' => 'ckeditor',
        'version' => 'Pre-Alpha',
        'verifyEmail' => 1,
        'requireActivation' => 0,
        'removeAttribution' => 0,
        'defaultGroup' => 0,
        'defaultTimeZone' => 'America/New_York'
    );
    
    $db->createTable('settings',$structures['settings'],$lang);
    
    if($firstInstall){
	    $statement = $db->prepare('addSetting','installer',array('!lang!'=>'en_us'));
	    foreach($defaultSettings as $settingName => $settingValue){
		    $statement->execute(array(
		    	':name' => $settingName,
		    	':category' => 'cms',
		    	':value' => $settingValue
		    ));
	    }
    }
    
    return NULL;
}

?>
