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

function dashboard_admin_buildContent($data,$db) {
	//$url = 'http://localhost/sitesense.org/version/'; // base url for version 
	$url = 'https://sitesense.org/dev/version/'; // base url for version 
	// modules versions contact
	$statement = $db->prepare('getEnabledModules','admin_modules'); // modules don't register versions until they're enabled, so this function is borderline useless if you get every module
	$statement->execute();
	$modules = $statement->fetchAll();
	$moduleQuery = array();
	foreach ($modules as $module) {
		$moduleQuery[$module['shortName']] = $module['version'];
	}
	$moduleQuery = http_build_query(array('modules'=>$moduleQuery));
	$moduleQuery = rawurldecode($moduleQuery);
	$moduleUrl = $url . 'modules?' . $moduleQuery;
	$ch = curl_init($moduleUrl);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$data->output['moduleUpdates'] = curl_exec($ch);
	$data->output['moduleUpdates'] = json_decode($data->output['moduleUpdates'],TRUE);

	// sitesense version contact
    $info['SiteSense Version'] = $data->settings['version'];
	$info['Server time']=strftime('%B %d, %Y, %I:%M:%S %p');
	$info['Server Signature']=$_SERVER['SERVER_SIGNATURE'];
	$info['Server Name']=$_SERVER['SERVER_NAME'];
	$info['Server Address']=$_SERVER['SERVER_ADDR'];
	$info['Gateway Interface']=$_SERVER['GATEWAY_INTERFACE'];
	$info['Server Protocol']=$_SERVER['SERVER_PROTOCOL'];
	$info['PHP Version']=phpversion().'</td></tr><tr><td colspan="2">
		<img src="'.$_SERVER['PHP_SELF'].'?='.php_logo_guid().'" alt="PHP Logo" />';
	$info['Zend Version']=zend_version().'</td></tr><tr><td colspan="2">
		<img src="'.$_SERVER['PHP_SELF'].'?='.zend_logo_guid().'" alt="Zend Logo" />';
	$info['Host OS']=PHP_OS;
	$data->output['secondSidebar']='
	<table class="sysInfo">
		<caption>System Info</caption>
		';
	foreach ($info as $title => $value) {
		if (is_array($value)) {
			$data->output['secondSidebar'].='<tr>
			<th colspan="2" class="section">'.$title.'</th>';
			foreach ($value as $subTitle => $subValue) {
				$data->output['secondSidebar'].='<tr>
			<th>'.$subTitle.'</th>
			<td>'.$subValue.'</td>
		</tr>';
			}
		} else {
			$data->output['secondSidebar'].='<tr>
			<th>'.$title.'</th>
			<td>'.$value.'</td>
		</tr>';
		}
	}
	$data->output['secondSidebar'].='
	</table>';
	$data->output['pageTitle']='About This CMS -';
	//-----Call Home-----//
	$field = array(
		'version' => $data->settings['version'],
		'host' => $data->domainName . $data->linkRoot,
		'removeAttribution' => $data->settings['removeAttribution'],
		'serverName' => $info['Server Name'],
		'serverAddress' => $info['Server Address'],
		'gatewayInterface' => $info['Gateway Interface'],
		'serverProtocol' => $info['Server Protocol'],
		'phpVersion' => phpversion(),
		'zendVersion' => zend_version()
	);
	
	$ch = curl_init($url);
	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$field);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$data->output['result'] = curl_exec($ch);
	$data->output['result'] = json_decode($data->output['result'],TRUE);

	/* 
	 *
	 * 0 = Attribution
	 * 1 = Version
	**/
	// Update Attribution Setting In The DB
	$statement = $db->prepare('updateSettings','admin_settings');
	$statement->execute(array(
		':name' => 'removeAttribution',
		':value' => $data->output['result']['removeAttribution']
	));
	// Push Across All Languages...
	common_updateAcrossLanguageTables($data,$db,'settings',array('name'=>'removeAttribution'),array('value'=>$data->output['result']['removeAttribution']));
	
}
function dashboard_admin_content($data) {
	// Check Version
	switch($data->output['result']['version'])
	{
		// Unknown Version
		case '1':
			$notification = 'Notice: You are running an unidentified version of SiteSense';
		break;
		// Newer Version Available
		case '2':
			$notification = 'Notice: There is a newer version available!';
		break;
		default:
			$notification = '';
		break;
	}
	
	theme_welcomeMessage($data,$notification);
	
	if (count($data->output['moduleUpdates'])>0) {
		theme_dashboardUpdateList($data);
		foreach ($data->output['moduleUpdates'] as $moduleUpdate) {
			theme_dashboardUpdateListRow($data,$moduleUpdate);
		}
		theme_dashboardUpdateListFoot();
	}
	
	theme_dashboardFoot();
}
?>