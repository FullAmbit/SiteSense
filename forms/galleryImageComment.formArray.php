<?php

$this->action=$data->linkRoot . implode('/', array_filter($data->action));

$this->formPrefix='comment_';
$this->caption='Make a comment';
$this->submitTitle='Comment';
$this->fromForm='commentForm';

$this->fields=array(
	'content' => array(
		'label' => 'Message',
		'required' => true,
		'tag' => 'textarea',
		'params' => array(
			'cols' => 80,
			'rows' => 20
		)
	)
);