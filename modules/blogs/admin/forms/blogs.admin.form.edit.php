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
$this->enctype='multipart/form-data';
$this->formPrefix='blogEdit_';
$this->caption=$data->phrases['blogs']['captionAddBlog'];
$this->submitTitle=$data->phrases['blogs']['submitEditForm'];
$this->fromForm='blogEdit';
$this->fields=array(
	'name' => array(
		'label' => $data->phrases['blogs']['labelEditName'],
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditName'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditName'].'
			</p>
		',
		'cannotEqual' => array()
	),
	'title' => array(
		'label' => $data->phrases['blogs']['labelEditTitle'],
		'required' => true,
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditTitle'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditTitle'].'
			</p>
		',
		'cannotEqual' => array()
	),
	'owner' => array(
		'label' => $data->phrases['blogs']['labelEditOwner'],
		'tag' => 'select',
		'options' => array(
			array(
				'value' => 0,
				'text' => 'NONE'
			)
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditOwner'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditOwner'].'
			</p>
		'
	),
	'picture' => array(
		'contentAfter' => (is_numeric($data->action[3])) ? '<a href="'.$data->settings['cdnLarge'].$data->themeDir.'images/blogs/'.$data->output['blogItem']['shortName'].'/rss.jpg">Current Image</a>' : '',
		'required' => false,
		'label' => $data->phrases['blogs']['labelEditPicture'],
		'tag' => 'input',
		'params' => array(
			'type' => 'file',
		),
		'images' => array(
			
			'full' => array(
				'mandateType' => array(
					'jpeg'
				),
				'path' => (is_numeric($data->action[3])) ?'themes/'.$data->settings['theme'].'/images/blogs/'.$data->output['blogItem']['shortName'] : 'themes/'.$data->settings['theme'].'/images/blogs/tmp',
				'overwrite' => true,
				'maxsize' => array(
					'width' => 1404,
					'height' => 4000
				),
				'customName' => 'rss',
			)
		)
	),
	'managingEditor' => array(
		'label' => $data->phrases['blogs']['labelEditManagingEditor'],
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditManagingEditor'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditManagingEditor'].'
			</p>'
	),
	'webMaster' => array(
		'label' => $data->phrases['blogs']['labelEditWebMaster'],
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditWebMaster'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditWebMaster'].'
			</p>'
	),
	'commentsRequireLogin' => array(
		'label' => $data->phrases['blogs']['labelEditCommentsRequireLogin'],
		'tag' => 'input',
		'checked' => ((isset($data->output['commentsRequireLogin']) && $data->output['commentsRequireLogin']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				'.$data->phrases['blogs']['descriptionEditCommentsRequireLogin'].'
			</p>
		'
	),
	'topLevel' => array(
		'label' => $data->phrases['blogs']['labelEditTopLevel'],
		'tag' => 'input',
		'checked' => ((isset($data->output['topLevel']) && $data->output['topLevel']) ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				'.$data->phrases['blogs']['descriptionEditTopLevel'].'
			</p>
		'
	),
	'allowComments' => array(
		'label' => $data->phrases['blogs']['labelEditAllowComments'],
		'tag' => 'input',
		'params' => array(
			'type' => 'checkbox',
		),
		'description' => '
			<p>
				'.$data->phrases['blogs']['descriptionEditAllowComments'].'
			</p>
		'
	),
	'numberPerPage' => array(
		'label' => $data->phrases['blogs']['labelEditNumberPerPage'],
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
			'size' => 2
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditNumberPerPage'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditNumberPerPage'].'
			</p>
		'
	),
	'rssOverride' => array(
		'label' => $data->phrases['blogs']['labelEditRSSLinkOverride'],
		'tag' => 'input',
		'params' => array(
			'type' => 'text',
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditRSSLinkOverride'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditRSSLinkOverride'].'
			</p>
		',
	),
	'description' => array(
		'label' => $data->phrases['blogs']['labelEditDescription'],
		'tag' => 'textarea',
		'required' => true,
		'value' => isset($data->output['content']) ? $data->output['content'] : '',
		'params' => array(
			'cols' => 40,
			'rows' => 20
		),
		'description' => '
			<p>
				<b>'.$data->phrases['blogs']['labelEditDescription'].'</b><br />
				'.$data->phrases['blogs']['descriptionEditDescription'].'
			</p>
		'
	)
);
?>