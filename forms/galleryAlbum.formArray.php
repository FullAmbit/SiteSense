<?php

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