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
function admin_modulesBuild($data,$db){
	if(!checkPermission('upgrade','modules',$data)) {
		$data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		return;
	}
	$data->output['upgrade'] = array();
	//$url = 'http://localhost/sitesense.org/version/'; // base url for version 
	$url = 'https://sitesense.org/dev/version/'; // base url for version 
	$statement = $db->prepare('getModuleByShortName','admin_modules');
	$statement->execute(array(':shortName'=>$data->action[3]));
	$module = $statement->fetch(PDO::FETCH_ASSOC);
	$moduleUrl = $url . 'modules?modules[' . $module['shortName'] . ']=' . $module['version'];
	$ch = curl_init($moduleUrl);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$update = curl_exec($ch);
	$update = json_decode($update,TRUE);
	$update = $update[$module['shortName']];
	if (count($update['newerVersions'])<1) {
		$data->output['upgrade'][] = 'No upgrades available for ' . $module['name'];
	} elseif (empty($data->action[4])) {
		$data->output['upgrade'][] = '<h2>Choose a Version for ' . $update['name'] . '</h2>';
		$data->output['upgrade'][] = 'Hey there! Let\'s get you upgraded. Which version of ' . $update['name'] . ' would you like to upgrade to?';
		//var_dump($update);
	} else {
		$data->output['upgrade'][] = '<h2>Upgrade Information for ' . $update['name'] . '</h2>';
		$data->output['upgrade'][] = 'Upgrading from version ' . $update['oldVersion'] . ' to version ' . $update['newVersion'] . ', released ' . $update['lastUpdated'] . '.';
		if (!is_numeric($data->action[4])) {
			$data->action[4] = 1;
		}
		$data->output['upgrade'][] = '<h2>Upgrading: Step ' . $data->action[4] . '</h2><ol>';
		switch ($data->action[5]) {
			case 1:
				$data->output['upgrade'][] = '<li>Welcome to the upgrade process for your module. We\'ll have you up and running in no time.</li>';
				$data->output['upgrade'][] = '<li>The first thing you\'ll need to do is download a .zip containing the latest version of this module. The download link is below:';
				$data->output['upgrade'][] = '<ul><li style="margin-left:10px;">Version ' . $update['newVersion'] . ': <a href="' . $update['zipBall'] . '">' . $update['zipBall'] . '</a></li></ul></li>';
				$data->output['upgrade'][] = '<li>Now that you have done that, unzip the .zip file you just downloaded on your local machine.</li>';
				$data->output['upgrade'][] = '<li>Enter the folder which has a name beginning with "' . $update['githubUser'] . '-' . $update['githubRepo'] . '".</li>';
				$data->output['upgrade'][] = '<li>You should see a folder named "' . $update['shortName'] . '".</li>';
				$data->output['upgrade'][] = '<li>Upload the folder named "' . $update['shortName'] . '" to the "modules" directory of your SiteSense install using FTP. Make sure to overwrite any files which already exist.</li>';
				$data->output['upgrade'][] = '<li>Once you have completed the above steps correctly, completely, and with no errors, please click the button below to proceed.</li>';
				$data->output['upgrade'][] = '<li class="buttonList"><a href="' . $data->linkRoot . 'admin/modules/upgrade/' . $module['shortName'] . '/2">Proceed to step 2</a></li>';
				break;
			case 2:
				
				break;
		}
		$data->output['upgrade'][] = '</ol>';
	}
}
function admin_modulesShow($data){
	foreach ($data->output['upgrade'] as $out) {
		echo $out;
	}
}