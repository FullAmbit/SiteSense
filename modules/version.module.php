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
function page_buildContent($data,$db)
{
	$data->output['error'] = FALSE;
	
	// Make Sure We Have A Post
	if(empty($_POST))
	{
		$data->output['error'] = 'invalid';
		return;
	}
		
	$statement = $db->prepare('getAllVersions','version');
	$statement->execute();
	$versionList = $statement->fetchAll();
	$data->output['latest'] = 0;
	foreach($versionList as $versionItem)
	{
		// Determine the latest version while we're looping through
		$data->output['latest'] = ($versionItem['sortOrder'] > $data->output['latest']) ? $versionItem['sortOrder'] : $data->output['latest'];
		//--If We Found A Matching One, Grab It (Find Client Version)--//
		if($versionItem['name'] == $_POST['version'])
		{
			$data->output['clientVersion'] = $versionItem;
			$data->output['error'] = FALSE;
		}
		// No Version Match
		if(!isset($data->output['clientVersion']))
		{
			$data->output['error'] = 'unknown';
		}
	}
	//---Save The Shit---//
		$statement = $db->prepare('saveClientData','version');
		$result = $statement->execute(array(
			':host' => $_POST['host'],
			':version' => $_POST['version'],
			':removeAttribution' => $_POST['removeAttribution'],
			':serverName' => $_POST['serverName'],
			':serverAddress' => $_POST['serverAddress'],
			':gatewayInterface' => $_POST['gatewayInterface'],
			':serverProtocol' => $_POST['serverProtocol'],
			':phpVersion' => $_POST['phpVersion'],
			':zendVersion' => $_POST['zendVersion']
		)) or die(print_r($statement->errorInfo()));
	//--Is This Domain Allowed To Remove Attribution?
	$check = $db->prepare('checkAttributionRemoval','version');
	$check->execute(array(
		':host' => $_POST['host']
	));
	$row = $check->fetch();
	if($row['isAllowed'] == 1)
	{
		$data->output['removeAttribution'] = 1;
	} else {
		// Not Allowed To Remove Attribution
		$data->output['removeAttribution'] = 0;
	}
}

function page_content($data)
{
	$return = array(
		'removeAttribution' => $data->output['removeAttribution'],
		'version' => 1
	);
	
	ob_clean();
	switch($data->output['error'])
	{
		case 'invalid':
			common_redirect($data->linkRoot);
		break;
		
		case 'unknown':
			// Unknown Version
			$return['version'] = '1';
		break;
		case FALSE:
			if($data->output['clientVersion']['sortOrder'] < $data->output['latest'])
			{
				// Newer Version Available
				$return['version'] = '2';
			} else {
				// You Got The Latest
				$return['version'] = '3';
			}
		break;
	}
	$string = implode('|',$return);
	echo $string;
	die();
}
?>