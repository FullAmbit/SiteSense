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
function dynamicURLs_settings() {
	return array(
		'name'      => 'dynamicURLs',
		'shortName' => 'dynamic-urls'
	);
}
function dynamicURLs_install($db,$drop=false) {
	$structures = array(
		'urls' => array(
			'id'        => SQR_IDKey,
			'match'     => 'VARCHAR(127) NOT NULL',
			'replace'   => 'VARCHAR(127) NOT NULL',
			'hostname'	=> 'VARCHAR(64) NOT NULL DEFAULT ""',
            'regex'     => SQR_boolean,
            'sortOrder' => SQR_sortOrder,
            'isRedirect'=> SQR_boolean,
            'UNIQUE KEY `match_hostName` (`match`,`hostname`)'
		)
	);
	if($drop)
		dynamicURLs_uninstall($db);

	$db->createTable('urls',$structures['urls'],false);
}
function dynamicURLs_uninstall($db) {
	$db->dropTable('urls');
}
?>