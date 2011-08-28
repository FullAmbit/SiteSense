<?php

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
