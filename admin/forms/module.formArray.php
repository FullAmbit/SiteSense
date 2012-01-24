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
$this->caption='Create/Edit Module';
$this->submitTitle='Save Module';
$this->fields=array(
	'name' => array(
		'label' => 'Module Name',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['module']['name']) ? $data->output['module']['name'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Name</b> - What is the name of the module, in the filesystem?
			</p>
		'
	),
	'shortName' => array(
		'label' => 'URL',
		'tag' => 'input',
		'required' => true,
		'value' => isset($data->output['module']['shortName']) ? $data->output['module']['shortName'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>URL</b> - What do you want the url of this module to be?
			</p>
		'
	),
	'enabled' => array(
		'label' => 'Enable?',
		'tag' => 'input',
		'checked' => ((isset($data->output['module']['enabled']) && $data->output['module']['enabled']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>Enable</b> - Is the module live? 
			</p>
		'
	)
);
