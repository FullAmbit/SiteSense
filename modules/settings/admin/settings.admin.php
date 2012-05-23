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
common_include('libraries/forms.php');
function admin_buildContent($data,$db) {
	/**
	 *	Permission: Accessible by administrator only
	**/
	if(!checkPermission('access','settings',$data)) {
		$data->output['rejectError'] = 'Insufficient Permissions';
		$data->output['rejectText'] = 'You do not have the permissions to access this area.';
		return;
	}
	$data->output['settingsForm']=new formHandler('settings',$data,true);
	$getModules = $db->query('getEnabledModules', 'modules');
	$modules = $getModules->fetchAll();
	// All Enabled Modules
	foreach($modules as $module){
		if($module['shortName'] == 'pages' || $module['shortName'] == 'ajax' || $module['shortName'] == 'users') continue;
		$option = array(
			'text' => $module['shortName'],
			'value' => $module['shortName'],
			'optgroup' => 'Modules'
		);
		$data->output['settingsForm']->fields['homepage']['options'][] = $option;
	}
	// Get All Top Level Pages //
	$statement = $db->prepare('getTopLevelPages','pages');
	$statement->execute();
	$pageList = $statement->fetchAll();
	if(count($pageList) > 0)
	{
		foreach($pageList as $pageItem)
		{
			$option = array(
				'text' => $pageItem['shortName'],
				'value' => $pageItem['shortName'],
				'optgroup' => 'Pages'
			);
			$data->output['settingsForm']->fields['homepage']['options'][] = $option;
		}
	}
	// Get All CDN Plugins //
	$statement = $db->prepare('getCDNPlugins','plugins');
	$statement->execute();
	$pluginList = $statement->fetchAll();
	foreach($pluginList as $pluginItem)
	{
		$option = array(
			'text' => $pluginItem['name'],
			'value' => $pluginItem['name']
		);
		$data->output['settingsForm']->fields['cdnPlugin']['options'][] = $option;
	}
	// Get All WYSIWYG Plugins //
	$statement = $db->prepare('getEditorPlugins','plugins');
	$statement->execute();
	$pluginList = $statement->fetchAll();
	foreach($pluginList as $pluginItem)
	{
		$option = array(
			'text' => $pluginItem['name'],
			'value' => $pluginItem['name']
		);
		
		$data->output['settingsForm']->fields['jsEditor']['options'][] = $option;
	}
	// Get All Blogs/
	$statement = $db->prepare('getAllBlogs','blogs');
	$statement->execute();
	$blogList = $statement->fetchAll();
	foreach($blogList as $blogItem)
	{
		$option = array(
			'text' => $blogItem['name'],
			'value' => $blogItem['shortName']
		);
		
		$data->output['settingsForm']->fields['defaultBlog']['options'][] = $option;
	}
	if (isset($_POST['fromForm']) && $_POST['fromForm']==$data->output['settingsForm']->fromForm) {
		
		if ($data->output['formOk']=$data->output['settingsForm']->validateFromPost()) {
			$data->output['secondSidebar']='
				<h2>Settings Saved</h2>
				<ul class="updateList">';
			// Parse The Footer //
			if($data->settings['useBBCode']=='1') {
				if(!empty(
					$data->output['settingsForm']->fields['rawFooterContent']['updated']
				)) {
					common_loadPlugin($data,'bbcode');
					$data->output['settingsForm']->fields['parsedFooterContent']['newValue']=
					$data->plugins['bbcode']->parse(
						$data->output['settingsForm']->fields['rawFooterContent'][
							$data->output['settingsForm']->fields['rawFooterContent']['updated']
						]
						);
				}
			} else {
				$data->output['settingsForm']->fields['parsedFooterContent']['newValue']=
				htmlspecialchars(
					$data->output['settingsForm']->fields['rawFooterContent']
				);
			}
			if(isset($data->output['settingsForm']->fields['parsedFooterContent']['newValue']))
				$data->output['settingsForm']->fields['parsedFooterContent']['updated']='newValue';
			// Loop Through Form Fields //
			$statement=$db->prepare('updateSettings','settings');
			foreach ($data->output['settingsForm']->fields as $fieldKey => $fieldData) {
				if (!empty($fieldData['updated'])) {
					$data->output['secondSidebar'].='
						<li class="changed"><b>'.$fieldKey.'</b><span> updated</span></li>';
					
					$statement->execute(array(
						'value' => $fieldData[$fieldData['updated']],
						'name' => $fieldKey
					));
				} else $data->output['secondSidebar'].='
					<li><b>'.$fieldKey.'</b><span> unchanged</span></li>';
			}
			unset($data->output['settingsForm']->fields['parsedFooterContent']);
			$data->output['secondSidebar'].='
				</ul>';
		} else {
			$data->output['secondSidebar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
	/* some values need logic flow to set */
	$list=glob('themes/*');
	foreach ($list as $theme) {
		if (filetype($theme)=='dir') {
			$data->output['settingsForm']->fields['theme']['options'][]=substr(strrchr($theme,'/'),1);
		}
	}
	$data->output['pageTitle']='Global Settings';
}
function admin_content($data) {
	if(isset($data->output['rejectError']))
	{
		theme_rejectError($data);
	} else {
		theme_buildForm($data->output['settingsForm']);
	}
}
?>