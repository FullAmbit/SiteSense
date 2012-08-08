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
if(!isset($data->output['blogItem'])) {
	$checked=($data->output['parentBlog']['allowComments']=='1') ? 'checked' : '';
} else {
	$checked='';
}
$this->formPrefix='blogEdit_';
$this->caption=$data->phrases['blogs']['captionAddPost'];
$this->submitTitle=$data->phrases['blogs']['submitEditPostsForm'];
$this->fromForm='blogEdit';
$this->fields=array(
	'name' => array(
		'label' => $data->phrases['blogs']['labelEditPostsName'],
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditPostsName'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditPostsName'].'
			</p>
		'
	),
	'title' => array(
		'label' => $data->phrases['blogs']['labelEditPostsTitle'],
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditPostsTitle'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditPostsTitle'].'
			</p>
		'
	),
	'tags' => array(
		'label' => $data->phrases['blogs']['labelEditPostsTags'],
		'required' => false,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditPostsTags'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditPostsTag'].'
			</p>
		'
	),
	'categoryId' => array(
		'label' => $data->phrases['blogs']['labelEditPostsCategoryId'],
		'tag' => 'select',
		'options' => array(
			array(
				'value' => '0',
				'text' => $data->phrases['blogs']['optionEditPostsCategoryIdNoCategory']
			)
		)
	),
	'rawSummary' => array(
		'label' => $data->phrases['blogs']['labelEditPostsRawSummary'],
		'tag' => 'textarea',
		'required' => true,
		'value' => isset($data->output['rawSummary']) ? $data->output['rawSummary'] : '',
		'useEditor' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>???
				<b>'.$data->phrases['blogs']['labelEditPostsRawSummary'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditPostsRawSummary'].'
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawSummary')
	),
	'rawContent' => array(
		'label' => $data->phrases['blogs']['labelEditPostsRawContent'],
		'tag' => 'textarea',
		'required' => true,
		'value' => isset($data->output['rawContent']) ? $data->output['rawContent'] : '',
		'useEditor' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>???
				<b>'.$data->phrases['blogs']['labelEditPostsRawContent'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditPostsRawContent'].'
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor($this->formPrefix.'rawContent')
	),
	'allowComments' => array(
		'label' => $data->phrases['blogs']['labelEditPostsAllowComments'],
		'tag' => 'input',
		'value' => '1',
		'checked' => $checked,
		'params' => array(
			'type' => 'checkbox'
		)
	),
	'live' => array(
		'label' => $data->phrases['blogs']['labelEditPostsLive'],
		'tag' => 'input',
		'checked' => ((isset($data->output['live']) && $data->output['live']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		)
	)
);
?>