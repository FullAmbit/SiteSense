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
function admin_usersBuild($data,$db) {
	$staff=false;
	if (empty($data->action[3])) {
		$data->output['userListStart']=0;
	} else if (is_numeric($data->action[3])) {
		$data->output['userListStart']=$data->action[3];
	} else if ($data->action[3]=='staff') {
		$staff=true;
		$data->output['forceMenu']='users/list/staff';
		if (empty($data->action[4])) {
			$data->output['userListStart']=0;
		} else if (is_numeric($data->action[4])) {
			$data->output['userListStart']=$data->action[4];
		}
	} else {
		$data->output['abort']=true;
		$data->output['abortMessage']='
			<h2>Unrecognized Command</h2>
			<p>Recheck the URL and try again.</p>
		';
	}
	if (empty($data->output['abort'])) {
		$data->output['userListLimit']=ADMIN_SHOWPERPAGE;
		$data->output['userListCount']=0;
		try {
			if ($staff) {
				$statement=$db->prepare('getListLimitedStaff','admin_users');
			} else {
				$statement=$db->prepare('getListLimited','admin_users');
			}
			$statement->bindParam(':start',$data->output['userListStart'],PDO::PARAM_INT);
			$statement->bindParam(':count',$data->output['userListLimit'],PDO::PARAM_INT);
			$statement->execute();
			$data->output['userList']=$statement->fetchAll();
		} catch(PDOException $e) {
			$data->output['abort']=true;
			$data->output['abortMessage']='
				<h2>There was a database connection error</h2>
				<pre>'.$e->getMessage().'</pre>
			';
		}
	}
}
function admin_usersShow($data) {
global $languageText;
	theme_usersListTableHead($data->output['userList'],$data->output['userListStart']);
	foreach($data->output['userList'] as $key => $user) {
		$userLevelText=$languageText['userLevels'][$user['userLevel']];
		$userLevelClass='userLevel_'.common_camelBack($userLevelText);

        /*
         * For new permissioning system
         * Three states:
         * banned <0
         * bannable <= 0
         * un-bannable > 0
         */
		if($user['userLevel'] < 0)
		{
			$banControl = '<a href="'.$data->linkRoot.'admin/users/unban/'.$user['id'].'">UnBan</a>';
		} else if($user['userLevel'] < USERLEVEL_ADMIN){
			$banControl = '<a href="'.$data->linkRoot.'admin/users/ban/'.$user['id'].'">Ban</a>';
		} else {
			$banControl = '';
		}
		
		theme_usersListTableRow($user['id'],$user['name'],$data->user['userLevel'],$userLevelClass,$userLevelText,$banControl,$data->linkRoot,$key);
		
	}
	theme_usersListTableFoot($data->linkRoot);
}
?>