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
function mainMenu_settings() {
	return array(
		'name'      => 'mainMenu',
		'shortName' => 'main-menu'
	);
}
function mainMenu_install($db,$drop=false,$lang="en_us") {
	$lang = rtrim($lang,'_').'_';
	$structures = array(
        'main_menu' => array(
            'id'           => SQR_IDKey,
            'text'         => SQR_name,
            'title'        => SQR_title,
            'url'          => SQR_URL,
            'side'         => SQR_side.' DEFAULT \'left\'',
            'sortOrder'    => SQR_sortOrder.' DEFAULT \'1\'',
            'enabled'      => SQR_boolean,
            'parent'       => SQR_ID.' DEFAULT \'0\'',
            'KEY `sortOrder` (`sortOrder`,`side`)'
        )
	);
	if($drop)
        mainMenu_uninstall($db);

    $db->createTable($lang.'main_menu',$structures['main_menu'],false);
}
function mainMenu_uninstall($db) {
	$db->dropTable('main_menu');
}
?>