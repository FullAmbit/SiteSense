<?php
$this->caption='Add an image';
$this->submitTitle='Add Image';

$this->fields=array(
	'name' => array(
		'label' => 'Image Name',
		'required' => true,
		'tag' => 'input',
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
	),
	'image' => array(
		'label' => 'Image File',
		'tag' => 'input',
		'params' => array(
			'type' => 'file'
		),
		'required' => true,
		'images' => array(
			'full' => array(
				'maxsize' => array(
					'width' => 720,
					'height' => 540
				),
				'path' => 'images/gallery/pictures'
			),
			'thumb' => array(
				'maxsize' => array(
					'width' => 160,
					'height' => 120
				),
				'path' => 'images/gallery/thumbs'
			),
			'icon' => array(
				'maxsize' => array(
					'width' => 64,
					'height' => 64
				),
				'path' => 'images/gallery/icons'
			)
		),
		'description' => '
			<p>
				<b>Image File</b> - Select the image to upload. Only images (jpg,gif,png) are
				allowed.
			</p>
		'
	)
);