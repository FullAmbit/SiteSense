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
$this->action=$data->linkRoot.'admin/settings';
$this->formPrefix='settings_';
$this->caption=$data->phrases['settings']['caption'];
$this->submitTitle=$data->phrases['settings']['submitButton'];
$this->fromForm='settings';
$this->fields=array(
	'siteTitle' => array(
		'label' => $data->phrases['settings']['labelSiteTitle'],
		'required' => true,
		'tag' => 'input',
		'value' => $data->settings['siteTitle'],
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelSiteTitle'].'</b><br />
				'.$data->phrases['settings']['descriptionSiteTitle'].'
			</p>
		'
	),
	'theme' => array(
		'label' => $data->phrases['settings']['labelTheme'],
		'tag' => 'select',
		'value' => $data->settings['theme'],
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelTheme'].'</b><br />
				'.$data->phrases['settings']['descriptionTheme'].'
			</p>
		'
	),
	'defaultTimeZone' => array(
		'label' => $data->phrases['settings']['labelDefaultTimeZone'],
		'tag' => 'select',
		'value' => $data->settings['defaultTimeZone'],
		'options' => $data->output['timeZones'],
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelDefaultTimeZone'].'</b><br />
				'.$data->phrases['settings']['descriptionDefaultTimeZone'].'
			</p>
		'
	),
	'homepage' => array(
		'label' => $data->phrases['settings']['labelHomepage'],
		'tag' => 'select',
		'value' => $data->settings['homepage'],
		'options' => array(),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelHomepage'].'</b><br />
				'.$data->phrases['settings']['descriptionHomepage'].'
			</p>
		'
	),
	'hideContentGuests' => array(
		'label' => $data->phrases['settings']['labelHideContentGuests'],
		'tag' => 'select',
		'value' => $data->settings['hideContentGuests'],
		'options' => array(
			'no','login','register'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelHideContentGuests'].'</b><br />
				'.$data->phrases['settings']['descriptionHideContentGuests'].'
		'
	),
	'useBBCode' => array(
		'label' => $data->phrases['settings']['labelUseBBCode'],
		'tag' => 'input',
		'checked' => ($data->settings['useBBCode'] == '1') ? 'checked' : '',
		'params' => array(
			'type' => 'checkbox'
		)
	),
	'jsEditor' => array(
		'label' => $data->phrases['settings']['labelJSEditor'],
		'tag' => 'select',
		'options' => array()
	),
	'defaultBlog' => array(
		'label' => $data->phrases['settings']['labelDefaultBlog'],
		'tag' => 'select',
		'options' => array()
	),
	'showPerPage' => array(
		'label' => $data->phrases['settings']['labelShowPerPage'],
		'required' => true,
		'tag' => 'input',
		'value' => $data->settings['showPerPage'],
		'params' => array(
			'type' => 'text',
			'size' => 2
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelShowPerPage'].'</b><br />
				'.$data->phrases['settings']['descriptionShowPerPage'].'
			</p>
		'
	),
	'rawFooterContent' => array(
		'label' => $data->phrases['settings']['labelRawFooterContent'],
		'tag' => 'textarea',
		'value' => $data->settings['rawFooterContent'],
		'useEditor' => true,
		'params' => array(
			'cols' => 40,
			'rows' => 10
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelRawFooterContent'].'</b><br />
				'.$data->phrases['settings']['descriptionRawFooterContent'].'
			</p>
		',
		'addEditor' => $data->jsEditor->addEditor('settings_rawFooterContent')
	),
	'characterEncoding' => array(
		'label' => $data->phrases['settings']['labelCharacterEncoding'],
		'tag' => 'select',
		'value' => $data->settings['characterEncoding'],
		'options' => array(
			'utf-8','iso-8859-1','windows-1252'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelCharacterEncoding'].'</b><br />
				'.$data->phrases['settings']['descriptionCharacterEncoding'].'
			</p>
		',
		'group' => $data->phrases['settings']['groupAdvanced']
	),
	'compressionEnabled' => array(
		'label' => $data->phrases['settings']['labelCompressionEnabled'],
		'tag' => 'input',
		'checked' => ($data->settings['compressionEnabled'] ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelCompressionEnabled'].'</b><br />
				'.$data->phrases['settings']['descriptionCompressionEnabled'].'
			</p>
		'
	),
	'compressionLevel' => array(
		'label' => $data->phrases['settings']['labelCompressionLevel'],
		'tag' => 'select',
		'value' => $data->settings['compressionLevel'],
		'options' => array(
			1,2,3,4,5,6,7,8,9
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelCompressionLevel'].'</b><br />
				'.$data->phrases['settings']['descriptionCompressionLevel'].'
			</p>
		',
		'group' => $data->phrases['settings']['groupAdvanced']
	),
	'userSessionTimeOut' => array(
		'label' => $data->phrases['settings']['labelUserSessionTimeOut'],
		'required' => true,
		'tag' => 'input',
		'value' => $data->settings['userSessionTimeOut'],
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelUserSessionTimeOut'].'</b><br />
				'.$data->phrases['settings']['descriptionUserSessionTimeOut'].'
			</p>
			<ul>
				<li>300 = 5 '.$data->phrases['settings']['minutes'].'</li>
				<li>1800 = 30 '.$data->phrases['settings']['minutes'].'</li>
				<li>3600 = 1 '.$data->phrases['settings']['hour'].'</li>
			</ul>
		',
		'group' => $data->phrases['settings']['groupAdvanced']
	),
	'useModRewrite' => array(
		'label' => $data->phrases['settings']['labelUseModRewrite'],
		'tag' => 'input',
		'checked' => ($data->settings['useModRewrite'] ? 'checked' : ''),
		'params' => array(
			'type' => 'checkbox'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelUseModRewrite'].'</b><br />
				'.$data->phrases['settings']['descriptionUseModRewrite'].'
			</p>
			<pre><code>'.$data->phrases['settings']['modRewriteExample'].'</code></pre>
			<p>
				'.$data->phrases['settings']['modRewriteNote'].'
			</p>
		',
		'group' => $data->phrases['settings']['groupAdvanced']
	),
	'defaultGroup' => array(
		'label' => $data->phrases['settings']['labelDefaultGroup'],
		'tag' => 'select',
		'value' => $data->settings['defaultGroup'],
		'options' => $data->output['userGroups'],
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelDefaultGroup'].'</b><br />
				'.$data->phrases['settings']['descriptionDefaultGroup'].'
			</p>
		',
		'group' => $data->phrases['settings']['groupRegistration']
	),
	'verifyEmail' => array(
		'label' => $data->phrases['settings']['labelVerifyEmail'],
		'tag' => 'select',
		'value' => $data->settings['verifyEmail'],
		'options' => array(
			array(
				'value' => 0,
				'text' => $data->phrases['settings']['optionDoNotRequireVerifyEmail']
			),
			array(
				'value' => 1,
				'text' => $data->phrases['settings']['optionRequireVerifyEmail']
			)
		),
		'group' => $data->phrases['settings']['groupRegistration']
	),
	'requireActivation' => array(
		'label' => $data->phrases['settings']['labelRequireActivation'],
		'tag' => 'select',
		'value' => $data->settings['requireActivation'],
		'options' => array(
			array(
				'value' => 0,
				'text' => $data->phrases['settings']['optionDoNotRequireActivation']
			),
			array(
				'value' => 1,
				'text' => $data->phrases['settings']['optionRequireActivation']
			)
		)
	),
	'useCDN' => array(
		'label' => $data->phrases['settings']['labelUseCDN'],
		'required' => false,
		'tag' => 'input',
		'checked' => ($data->settings['useCDN'] == '1') ? 'checked' : '',
		'value' => '1',
		'params' => array(
			'type' => 'checkbox'
		),
		'group' => $data->phrases['settings']['groupCDN']
	),
	'cdnPlugin' => array(
		'label' => $data->phrases['settings']['labelCDNPlugin'],
		'required' => false,
		'tag' => 'select',
		'options' => array(),
		'group' => $data->phrases['settings']['groupCDN']
	),
	'cdnBaseDir' => array(
		'label' => $data->phrases['settings']['labelCDNBaseDir'],
		'required' => false,
		'tag' => 'input',
		'value' => $data->settings['cdnBaseDir'],
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelCDNBaseDir'].'</b><br />
				'.$data->phrases['settings']['descriptionCDNBaseDir'].'
			</p>
		',
		'group' => $data->phrases['settings']['groupCDN']
	),'cdnLarge' => array(
		'label' => $data->phrases['settings']['labelCDNLarge'],
		'required' => false,
		'tag' => 'input',
		'value' => $data->settings['cdnLarge'],
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelCDNLarge'].'</b><br />
				'.$data->phrases['settings']['descriptionCDNLarge'].'
			</p>
		',
		'group' => $data->phrases['settings']['groupCDN']
	),
	'cdnSmall' => array(
		'label' => $data->phrases['settings']['labelCDNSmall'],
		'required' => false,
		'tag' => 'input',
		'value' => $data->settings['cdnSmall'],
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelCDNSmall'].'</b><br />
				'.$data->phrases['settings']['descriptionCDNSmall'].'
			</p>
		',
		'group' => $data->phrases['settings']['groupCDN']
	),
	'cdnFlash' => array(
		'label' => $data->phrases['settings']['labelCDNFlash'],
		'required' => false,
		'tag' => 'input',
		'value' => $data->settings['cdnFlash'],
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['settings']['labelCDNFlash'].'</b><br />
				'.$data->phrases['settings']['descriptionCDNFlash'].'
			</p>
		',
		'group' => $data->phrases['settings']['groupCDN']
	)
);