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
if(!isset($data->output['album'])){
	$this->caption='Create an Album';
	$this->submitTitle='Create Album';
}else{
	$this->caption='Modify Album';
	$this->submitTitle='Edit Album';
}
$this->fields=array(
	'name' => array(
		'label' => 'Album Name',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['album']) ? $data->output['album']['name'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 64
		),
		'description' => '
			<p>
				<b>Album Name</b> - Name your album!
			</p>
		'
	),
	'shortName' => array(
		'label' => 'Unique URL',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['album']) ? $data->output['album']['shortName'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 64
		),
		'description' => '
			<p>
				<b>Unique URL</b> - What URL will people go to to view this album? For example
				if you enter "ABC" it will be accessable by gallery/albums/view/ABC 
			</p>
		'
	),
	'allowComments' => array(
		'label' => 'Allow Comments?',
		'tag' => 'input',
		'value' => isset($data->output['album']) ? $data->output['album']['allowComments'] : '',
		'params' => array(
			'type' => 'checkbox'
		)
	)
);