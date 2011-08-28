<?php

$this->caption='Create/Edit URL Remap';
$this->submitTitle='Save URL Remap';

$this->fields=array(
	'match' => array(
		'label' => 'Regex Match',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['urlremap']['match']) ? $data->output['urlremap']['match'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Match</b> - Regex matching pattern
			</p>
		'
	),
	'replace' => array(
		'label' => 'Replacement',
		'tag' => 'input',
		'value' => isset($data->output['urlremap']['replace']) ? $data->output['urlremap']['replace'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>Replacement</b> - $i (where i is an integer) means the match in the "$i"th bracket. $0 is the entire match.
			</p>
		'
	),
	'redirect' => array(
		'label' => 'Redirect?',
		'tag' => 'input',
		'checked' => ((isset($data->output['urlremap']['redirect']) && $data->output['urlremap']['redirect']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				Do you want the browser to redirect? Otherwise the user will see the original url but a different page. 
			</p>
		'
	)
);
