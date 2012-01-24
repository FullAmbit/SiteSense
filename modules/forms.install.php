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
function forms_settings($data)
{
	return array(
		'name' => 'forms',
		'shortName' => 'forms'
	);
}

function forms_install($data,$drop=false)
{	
	$settings = forms_settings($data);
	$structures = array(
		'forms' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'enabled' => 'tinyint(1) NOT NULL COMMENT \'0 = false 1 = true\'',
			'shortName' => 'varchar(32) NOT NULL',
			'name' => 'varchar(64) NOT NULL',
			'title' => 'varchar(256) NOT NULL',
			'rawContentBefore' => 'text NOT NULL',
			'parsedContentBefore' => 'text NOT NULL',
			'rawContentAfter' => 'text NOT NULL',
			'parsedContentAfter' => 'text NOT NULL',
			'rawSuccessMessage' => 'text NOT NULL',
			'parsedSuccessMessage' => 'text NOT NULL',
			'requireLogin'=> ' tinyint(1) NOT NULL',
			'topLevel' => 'tinyint(1) NOT NULL',
			'eMail' => 'varchar(256) NOT NULL',
			'submitTitle' => 'varchar(128) NOT NULL',
			'api' => 'varchar(256) DEFAULT NULL',
			'PRIMARY KEY (`id`)'
		),
		'form_fields' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'form' => 'int(11) NOT NULL',
			'name' => 'varchar(32) NOT NULL',
			'type' => 'varchar(16) NOT NULL',
			'description' => 'varchar(256) NOT NULL',
			'options' => 'text',
			'required' => 'tinyint(1) NOT NULL DEFAULT \'0\' COMMENT \'0 = false 1 = true\'',
			'enabled' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
			'apiFieldToMapTo' => 'varchar(256) DEFAULT NULL',
			'sortOrder' => 'int(11) NOT NULL DEFAULT \'1\'',
			'isEmail' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
			'PRIMARY KEY (`id`)'
		),
		'form_sidebars' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'form' => 'int(11) NOT NULL',
			'sidebar' => 'int(11) NOT NULL',
			'enabled' => 'tinyint(1) NOT NULL DEFAULT \'0\'',
			'sortOrder' => 'int(11) NOT NULL DEFAULT \'1\'',
			'PRIMARY KEY (`id`)',
			'UNIQUE KEY `form` (`form`,`sidebar`)'
		),
		'form_rows' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'form' => 'int(11) NOT NULL',
			'PRIMARY KEY (`id`)'
		),
		'form_values' => array(
			'id' => 'int(11) NOT NULL AUTO_INCREMENT',
			'row' => 'int(11) NOT NULL',
			'field' => 'int(11) NOT NULL',
			'value' => 'text NOT NULL',
			'PRIMARY KEY (`id`)'
		)
	);
	
	if($drop) {
		$data->dropTable('forms');
		$data->dropTable('form_fields');
		$data->dropTable('form_rows');
		$data->dropTable('form_values');
		$data->dropTable('form_sidebars');
	}
	
	$data->createTable('forms',$structures['forms'],true);
	$data->createTable('form_fields',$structures['form_fields'],true);
	$data->createTable('form_rows',$structures['form_rows'],true);
	$data->createTable('form_values',$structures['form_values'],true);
	$data->createTable('form_sidebars',$structures['form_sidebars'],true);
	
	return NULL;
}

function forms_postInstall($data)
{
}

?>