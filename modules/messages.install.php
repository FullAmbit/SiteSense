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
function messages_settings() {
	return array(
		'name'      => 'messages',
		'shortName' => 'messages'
	);
}
function messages_install($data,$drop=false) {
	$structures = array(
			'user_pms'  => array(
			'id'        => SQR_IDKey,
			'from'      => SQR_ID,
			'to'        => SQR_ID,
			'message'   => 'TEXT NOT NULL',
			'read'      => SQR_boolean.' DEFAULT \'0\'',
			'deleted'   => SQR_boolean.' DEFAULT \'0\'',
			'sent'      => SQR_added
		)
	);
	if($drop) $data->dropTable('user_pms');
	$data->createTable('user_pms',$structures['user_pms'],false);
}
function messages_uninstall($data) {
	$data->dropTable('user_pms');
}
?>