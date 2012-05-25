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
function sidebars_settings() {
	return array(
		'name'      => 'sidebars',
		'shortName' => 'sidebars'
	);
}
function sidebars_install($db,$drop=false) {
	$structures = array(
        'sidebars' => array(
            'id'            => SQR_IDKey,
            'name'          => SQR_name,
            'shortName'     => SQR_shortName,
            'enabled'       => SQR_boolean,
            'fromFile'      => SQR_boolean,
            'title'         => 'VARCHAR(255)',
            'titleURL'      => SQR_URL,
            'rawContent'    => 'TEXT',
            'parsedContent' => 'TEXT',
            'side'          => SQR_side.' DEFAULT \'left\'',
            'sortOrder'     => SQR_sortOrder,
            'KEY `sortOrder` (`sortOrder`,`side`)'
        )
	);
	if($drop)
        sidebars_uninstall($db);

	$db->createTable('sidebars',$structures['sidebars'],false);

}
function sidebars_uninstall($db) {
    $db->dropTable('sidebars');
}
?>