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
function populateTimeZones($data) {
	$currentTime=time();
	$times=array();
	$start=$currentTime-date('G',$currentTime)*3600;
	for($i=0;$i<24*60;$i+=15) {
		$times[date('g:i A',$start+$i*60)]=array();
	}
	$timezones=DateTimeZone::listIdentifiers();
	foreach($timezones AS $timezone) {
		$dt=new DateTime('@'.$currentTime);
		$dt->setTimeZone(new DateTimeZone($timezone));
		$time=$dt->format('g:i A');
		$times[$time][]=$timezone;
	}
	$timeZones=array_filter($times);
	foreach($timeZones as $time => $timeZoneList) {
		foreach($timeZoneList as $timeZone) {
			$data->output['timeZones'][]=array(
				'text'  => $time.' - '.$timeZone,
				'value' => $timeZone
			);
		}
	}
}
function settings_admin_buildContent($data,$db) {
	/**
	 *	Permission: Accessible by administrator only
	**/
	if(!checkPermission('access','settings',$data)) {
		$data->output['rejectError'] = 'Insufficient Permissions';
		$data->output['rejectText'] = 'You do not have the permissions to access this area.';
		return;
	}
	// Poulate Time Zone List
	populateTimeZones($data);
	// Get all groups
	$statement=$db->query('getAllGroups','admin_users');
	$userGroups=$statement->fetchAll();
	$userGroups[]['groupName']='Administrators';
	sort($userGroups);
	$data->output['userGroups'][]=array(
		'text'  => 'Disable auto assigning of groups',
		'value' => '0'
	);
	foreach($userGroups as $userGroup) {
		$data->output['userGroups'][]=array(
			'text'  => $userGroup['groupName'],
			'value' => $userGroup['groupName']
		);
	}
	$data->output['settingsForm']=new formHandler('edit',$data,true);
	$getModules = $db->query('getEnabledModules','admin_modules');
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
	$statement = $db->prepare('getTopLevelPages','admin_pages');
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
	$statement = $db->prepare('getCDNPlugins','admin_plugins');
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
	$statement = $db->prepare('getEditorPlugins','admin_plugins');
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
	$statement = $db->prepare('getAllBlogs','admin_blogs');
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
				if(!empty(
					$data->output['settingsForm']->fields['rawFooterContent']['updated']
				)) {
					$data->output['settingsForm']->fields['parsedFooterContent']['newValue']=
						$data->output['settingsForm']->fields['rawFooterContent'][
							$data->output['settingsForm']->fields['rawFooterContent']['updated']
						];
				}
			}
			if(isset($data->output['settingsForm']->fields['parsedFooterContent']['newValue']))
				$data->output['settingsForm']->fields['parsedFooterContent']['updated']='newValue';
			// Loop Through Form Fields //
			$languageExceptions = array('siteTitle','rawFooterContent','parsedFooterContent');
			$statement=$db->prepare('updateSettings','admin_settings');
			foreach ($data->output['settingsForm']->fields as $fieldKey => $fieldData) {
				if (!empty($fieldData['updated'])) {
					$data->output['secondSidebar'].='
						<li class="changed"><b>'.$fieldKey.'</b><span> updated</span></li>';
					
					$statement->execute(array(
						'value' => $fieldData[$fieldData['updated']],
						'name' => $fieldKey
					));
					
					//---Update Across All Langauges--//
					if(!in_array($fieldKey,$languageExceptions)){
						common_updateAcrossLanguageTables($data,$db,'settings',array('name'=>$fieldKey),array('value'=>$fieldData[$fieldData['updated']]),TRUE);
					}
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
function settings_admin_content($data) {
	if(isset($data->output['rejectError']))
	{
		theme_rejectError($data);
	} else {
		theme_buildForm($data->output['settingsForm']);
	}
}
?>