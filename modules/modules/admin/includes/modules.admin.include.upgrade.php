<?php
/*
* SiteSense
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License(OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web,please send an email
* to license@sitesense.org so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade SiteSense to newer
* versions in the future. If you wish to customize SiteSense for your
* needs please refer to http://www.sitesense.org for more information.
*
* @author     Full Ambit Media,LLC <pr@fullambit.com>
* @copyright  Copyright(c) 2011 Full Ambit Media,LLC(http://www.fullambit.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License(OSL 3.0)
*/
function admin_modulesBuild($data,$db){
	common_include('modules/modules/admin/modules.admin.common.php');
	if(!checkPermission('upgrade','modules',$data)){
		$data->output['abort']=true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		return;
	}
	$data->output['upgrade']=array();
	$url='http://localhost/sitesense.org/version/'; // base url for version 
	//$url='https://sitesense.org/dev/version/'; // base url for version 
	$statement=$db->prepare('getModuleByShortName','admin_modules');
	$statement->execute(array(':shortName'=>$data->action[3]));
	$module=$statement->fetch(PDO::FETCH_ASSOC);
	$moduleUrl=$url.'modules?modules['.$module['shortName'].']='.$module['version'];
	$ch=curl_init($moduleUrl);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
	$update=curl_exec($ch);
	$update=json_decode($update,TRUE);
	$update=$update[$module['shortName']];
	if(count($update['newerVersions'])<1){
		$data->output['upgrade'][]='No upgrades available for '.$module['name'];
	}elseif(empty($data->action[4])){
		$data->output['upgrade'][]='<h2>Choose a Version for '.$update['name'].'</h2>
			Hey there! Let\'s get you upgraded. Which version of '.$update['name'].' would you like to upgrade to?
			<ul>';
		foreach($update['newerVersions'] as $version=>$newerVersion){
			if($newerVersion['release']){ $color='#000000'; }else{ $color='#ABABAB'; }
			$data->output['upgrade'][]='<li><a href="'.$data->linkRoot.'admin/modules/upgrade/'.$update['shortName'].'/'.$version.'/1" style="color:'.$color.';">Upgrade to <strong>'.$version.'</strong></a>';
			if($version===$update['newVersion']){
				$data->output['upgrade'][]=' <em>(latest stable release - recommended)</em>';
			}
			$data->output['upgrade'][]='</li>';
		}
		$data->output['upgrade'][]='</ul>';
	}else{
		$data->output['upgrade'][]='<h2>Upgrade Information for '.$update['name'].'</h2>
			Upgrading from version '.$update['oldVersion'].' to version '.$data->action[4].' the easy way.';
		if(!is_numeric($data->action[5])){
			$data->action[5]=1;
		}
		$latestVersion=$update['newerVersions'][$data->action[4]];
		$data->output['upgrade'][]='<h2>Upgrading: Part '.$data->action[4].'</h2><ol>';
		$baseUpgradeLink=$data->linkRoot.'admin/modules/upgrade/'.$module['shortName'].'/'.$latestVersion['version'].'/';
		switch($data->action[5]){
			case 1:
				$data->output['upgrade'][]='<li>Welcome to the upgrade process for your module. We\'ll have you up and running in no time.</li>
					<li>Disable this module. You can do that <a href="'.$data->linkRoot.'admin/modules/disable/'.$update['shortName'].'" target="_blank">here</a>. Do not uninstall it or you will lose all your data from that particular module.</li>
					<li>Now, delete the folder named "'.$update['shortName'].'" from the "modules" directory in your SiteSense installation folder.</li>
					<li>The next thing you\'ll need to do is download either a .zip or a .tar.gz containing the latest version of this module. The download links for both are below:
					<ul><li style="margin-left:10px;">Version '.$latestVersion['version'].': <a href="'.$latestVersion['zipLink'].'">'.$latestVersion['zipLink'].'</a>(.zip)</li></ul></li>
					<ul><li style="margin-left:10px;">Version '.$latestVersion['version'].': <a href="'.$latestVersion['tarLink'].'">'.$latestVersion['tarLink'].'</a>(.tar.gz)</li></ul></li>
					<li>Now that you have done that,unzip the .zip or untar the .tar.gz file you just downloaded on your local machine.</li>
					<li>Enter the folder which has a name beginning with "'.$update['githubUser'].'-'.$update['githubRepo'].'-".</li>
					<li>You should see a folder named "'.$update['shortName'].'" inside.</li>
					<li>Upload the folder named "'.$update['shortName'].'" to the "modules" directory of your SiteSense install using FTP.</li>
					<li>Once you have completed the above steps correctly, completely, and with no errors, please click the button below to proceed.</li>
					<li class="buttonList"><a href="'.$baseUpgradeLink.'2">Proceed to part 2</a></li>';
				break;
			case 2:
				$data->output['upgrade'][]='<li>'.$data->phrases['modules']['validUpdateCheck'].'</li>';
				$data->output['upgrade'][]='<li>';
				if(!file_exists('modules/'.$update['shortName'].'/'.$update['shortName'].'.install.php')){
					$data->output['upgrade'][]=$data->phrases['modules']['errorPrefix'].$data->phrases['modules']['noInstallPhp'];
					break;
				}
				common_include('modules/'.$update['shortName'].'/'.$update['shortName'].'.install.php');
				if(!function_exists($update['shortName'].'_settings')){
					$data->output['upgrade'][]=$data->phrases['modules']['errorPrefix'].$data->phrases['modules']['settingsNotSet'];
					break;
				}
				$modSettings=call_user_func($update['shortName'].'_settings');
				if(!is_array($modSettings)||!isset($modSettings['name'])||!isset($modSettings['shortName'])){
					$data->output['upgrade'][]=$data->phrases['modules']['errorPrefix'].$data->phrases['modules']['settingsNotArray'];
					break;
				}
				$data->output['upgrade'][]=sprintf($data->phrases['modules']['uploadedVersions'],$modSettings['shortName'],$modSettings['version']);
				$data->output['upgrade'][]='</li>
					<li>'.$data->phrases['modules']['loadingUpdaters'];
				$updaters=glob('modules/'.$update['shortName'].'/updaters/'.$update['shortName'].'.updater.*to*.php');
				$path=modules_admin_common_getUpgradePath($data->action[4],$update['oldVersion'],$update['shortName'],$updaters);
				if(!$path){
					$data->output['upgrade'][]='</li>
						<li>'.$data->phrases['modules']['noUpdatersFound'].'</li>
						<li>'.sprintf($data->phrases['modules']['upgradeSuccessful'],$module['name']).'</li>';
					break;
				}else{
					foreach($path as $step){
						$data->output['upgrade'][]='<br>'.sprintf($data->phrases['modules']['runningUpdater'],$step['file'],$step['from'],$step['to']);
						$updaterOutput=modules_admin_common_runUpgrader($data,$db,$step['to'],$step['from'],$update['shortName'],$step['file']);
						if ($updaterOutput){
							$data->output['upgrade'][]=$data->phrases['modules']['updaterSuccessful'];
							$statement=$db->prepare('updateModule','admin_modules');
							$statement->execute(array(
								':name'=>$module['name'],
								':enabled'=>$module['enabled'],
								':version'=>$step['to'],
								':shortName'=>$update['shortName'],
							));
						}else{
							$data->output['upgrade'][]='<br>'.$data->phrases['modules']['errorPrefix'].$data->phrases['modules']['updaterError'].'</li>';
							break 2;
						}
					}
					$data->output['upgrade'][]='</li>';
					$data->output['upgrade'][]='<li>'.sprintf($data->phrases['modules']['upgradeSuccessful'],$module['name']).'</li>';
				}
				break;
		}
		$data->output['upgrade'][]='</ol>';
	}
}
function admin_modulesShow($data){
	foreach($data->output['upgrade'] as $out){
		echo $out;
	}
}