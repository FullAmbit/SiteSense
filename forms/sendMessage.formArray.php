<?php

$this->action=$data->linkRoot . implode('/', array_filter($data->action));

$this->formPrefix='sendMessage_';
$this->caption='Send a message';
$this->submitTitle='Send';
$this->fromForm='sendMessage';

$this->fields=array(
	'message' => array(
		'label' => 'Message',
		'required' => true,
		'tag' => 'textarea',
		'useEditor' => true,
		'params' => array(
			'cols' => 80,
			'rows' => 20
		)
	)
);
