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
function messages_settings($data)
{
	return array(
		'name' => 'messages',
		'shortName' => 'messages'
	);
}

function messages_install($data,$drop=false)
{	
	$settings = messages_settings($data);
	$structures = array(
			'user_pms' => array(
			'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
			'from' => 'int(11) unsigned NOT NULL',
			'to' => 'int(11) unsigned NOT NULL',
			'message' => 'text NOT NULL',
			'read' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
			'deleted' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
			'sent' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'PRIMARY KEY (`id`)'
		)
	);
	
	if($drop)
		$data->dropTable('user_pms');
	
	$data->createTable('user_pms',$structures['user_pms'],true);
	
	return NULL;
}

function messages_postInstall($data)
{
}

?>