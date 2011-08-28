<?php

$this->action=$data->linkRoot . implode('/', array_filter($data->action));

$this->formPrefix='comment_';
$this->caption='Make a comment';
$this->submitTitle='Comment';
$this->fromForm='commentForm';

$this->fields=array(
	'commenter' => array(
		'label' => 'Your name',
		'required' => true,
		'value' => isset($data->user['fullName']) ? $data->user['fullName'] : '',
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => 64
		),
		'description' => '
			<p>
				<b>Your Name</b> - Who are you?
			</p>
		'
	),
	'post' => array(
		'params' => array(
			'type' => 'hidden'
		)
	),
	'content' => array(
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

/*if(isset($data->user['fullName'])){
	$this->fields['commenter']['params']['disabled'] = 'disabled';
}*/