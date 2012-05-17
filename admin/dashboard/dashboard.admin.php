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
function admin_buildContent($data,$db) {
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
	$data->output['secondSideBar']='
	<table class="sysInfo">
		<caption>System Information</caption>
		';
	foreach ($info as $title => $value) {
		if (is_array($value)) {
			$data->output['secondSideBar'].='<tr>
			<th colspan="2" class="section">'.$title.'</th>';
			foreach ($value as $subTitle => $subValue) {
				$data->output['secondSideBar'].='<tr>
			<th>'.$subTitle.'</th>
			<td>'.$subValue.'</td>
		</tr>';
			}
		} else {
			$data->output['secondSideBar'].='<tr>
			<th>'.$title.'</th>
			<td>'.$value.'</td>
		</tr>';
		}
	}
	$data->output['secondSideBar'].='
	</table>';
	$data->output['pageTitle']='About This CMS -';
	//-----Call Home-----//
	$url = 'https://www.sitesense.org/dev/version/';
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
	$data->output['result'] = explode('|',$data->output['result']);
	/* 
	 *
	 * 0 = Attribution
	 * 1 = Version
	**/
	// Update Attribution Setting In The DB
	$statement = $db->prepare('updateSettings','admin_settings');
	$statement->execute(array(
		':name' => 'removeAttribution',
		':value' => $data->output['result'][0]
	));
	
}
function admin_content($data) {
	// Check Version
	switch($data->output['result'][1])
	{
		// Unknown Version
		case '1':
			$notification = 'Notice: You are running an unidentified version of SiteSense';
		break;
		// Newer Version Available
		case '2':
			$notification = 'Notice: There is a newer version available!';
		break;
		// You Got The Latest
		case '3':
			$notification = '';
		break;
		default:
			$notification = '';
		break;
	}
	
	theme_welcomeMessage($notification);
}
?>