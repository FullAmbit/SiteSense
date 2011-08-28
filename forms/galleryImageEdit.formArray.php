<?php
$this->caption='Edit an image';
$this->submitTitle='Edit Image';

$this->fields=array(
	'name' => array(
		'label' => 'Image Name',
		'required' => true,
		'tag' => 'input',
		'value' => $data->output['image']['name'],
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
		'value' => $data->output['image']['shortName'],
		'params' => array(
			'type' => 'text',
			'size' => 64
		),
	'description' => '
			<p>
				<b>Unique URL</b> - What URL will people go to to view this image? For example
				if you have an album with URL "ABC" and an image with URL "DEF" it will be
				accessable by gallery/images/ABC/DEF. Images under the same album cannot have
				the same URL.
			</p>
		'
	)
);