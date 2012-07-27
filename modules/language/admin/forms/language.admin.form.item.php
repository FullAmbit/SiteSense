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
$this->caption='Add/Edit Phrase';
$this->submitTitle='Save Phrase';
$this->fields=array(
	'name' => array(
		'label' => 'Name',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['phrase']['name']) ? $data->output['phrase']['name'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 64
		),
		'description' => '
			<p>
				<b>Phrase Name</b>
			</p>
		'
	),
	'value' => array(
		'label' => 'Phrase',
		'tag' => 'input',
		'required' => false,
		'value' => isset($data->output['phrase']['value']) ? $data->output['phrase']['value'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>Phrase</b>
			</p>
		'
	),
	'module' => array(
		'label' => 'Module',
		'tag' => 'select',
		'options' => array(
			array(
				'text' => 'Core',
				'value' => 0
			)
		),
		'value' => (isset($data->output['phrase']['module'])) ? $data->output['phrase']['module'] : ''
	)
);
