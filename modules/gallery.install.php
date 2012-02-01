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
function gallery_settings($data) {
	return array(
		'name' => 'gallery',
		'shortName' => 'gallery'
	);
}
function gallery_install($data,$drop=false) {
	$structures = array(
		'gallery_albums' => array(
			'id'										 => SQR_IDKey,
			'name'									 => SQR_name,
			'shortName'							 => SQR_shortName,
			'user'									 => SQR_ID,
			'allowComments'					 => SQR_boolean
		),
		'gallery_comments' => array(
			'id'										 => SQR_IDKey,
			'image'									 => SQR_ID,
			'user'									 => SQR_ID,
			'content'								 => 'TEXT NOT NULL',
			'time'									 => SQR_lastModified
		),
		'gallery_images' => array(
			'id'										 => SQR_IDKey,
			'album'									 => SQR_ID,
			'name'									 => SQR_name,
			'shortName'							 => SQR_shortName,
			'image'									 => 'VARCHAR(63) NOT NULL',
			'thumb'									 => 'VARCHAR(63) NOT NULL',
			'time'									 => SQR_lastModified
		)
	);
	if($drop) {
		$data->dropTable('gallery_albums');
		$data->dropTable('gallery_images');
		$data->dropTable('gallery_comments');
	}
	$data->createTable('gallery_albums',$structures['gallery_albums'],true);
	$data->createTable('gallery_images',$structures['gallery_images'],true);
	$data->createTable('gallery_comments',$structures['gallery_comments'],true);
}
function gallery_uninstall($data) {
	$data->dropTable('gallery_albums');
	$data->dropTable('gallery_images');
	$data->dropTable('gallery_comments');
}
?>