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
function gallery_settings($data)
{
	return array(
		'name' => 'gallery',
		'shortName' => 'gallery'
	);
}

function gallery_install($data,$drop=false)
{	
	$settings = gallery_settings($data);
	$structures = array(
		'gallery_albums' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'name' => 'varchar(64) NOT NULL',
			'shortName' => 'varchar(32) NOT NULL',
			'user' => 'int(11) NOT NULL',
			'allowComments' => 'tinyint(1) NOT NULL',
			'PRIMARY KEY (`id`)'
		),
		'gallery_comments' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'image' => 'int(11) NOT NULL',
			'user' => 'int(11) NOT NULL',
			'content' => 'text NOT NULL',
			'time' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
			'PRIMARY KEY (`id`)'
		),
		'gallery_images' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'album' => 'int(11) NOT NULL',
			'name' => 'varchar(128) NOT NULL',
			'shortName' => 'varchar(32) NOT NULL',
			'image' => 'varchar(64) NOT NULL',
			'thumb' => 'varchar(64) NOT NULL',
			'time' => 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
			'PRIMARY KEY (`id`)'
		)
	);
	
	if($drop) {
		$data->dropTable('gallery_albums');
		$data->dropTable('gallery_images');
		$data->dropTable('friends');
	}
	
	$data->createTable('gallery_albums',$structures['gallery_albums'],true);
	$data->createTable('gallery_images',$structures['gallery_images'],true);
	$data->createTable('gallery_comments',$structures['gallery_comments'],true);
	
	return NULL;
}

function gallery_postInstall($data)
{
}

?>