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
function admin_modulesBuild($data,$db) {
    if(!checkPermission('modules_edit','core',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    if($data->action[3] === false){
		$existing = false;
	}else{
		$existing = (int)$data->action[3];
		$check = $db->prepare('getModuleById', 'admin_modules');
		$check->execute(array(':id' => $existing));
		if(($data->output['module'] = $check->fetch()) === false){
			$data->output['abort'] = true;
			$data->output['abortMessage'] = '<h2>ID does not exist in database</h2>';
			return;
		}
	}
	$form = $data->output['moduleForm'] = new formHandler('module',$data,true);
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$form->fromForm)
	) {
		if($existing){
			$form->caption = 'Editing Module';
		}else{
			$form->caption = 'New Module';
		}
		$form->populateFromPostData();
		if ($form->validateFromPost()) {
			if($existing){
				$statement = $db->prepare('editModule', 'modules');
				$form->sendArray[':id'] = $existing;
			}else{
				$statement = $db->prepare('newModule', 'modules');
			}
			$statement->execute($form->sendArray) or die('Saving module failed');
			if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>module Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/modules/edit/">
							Add New Module
						</a>
						<a href="'.$data->linkRoot.'admin/modules/list/">
							Return to Module List
						</a>
					</div>';
			}
		} else {
			/*
				invalid data, so we want to show the form again
			*/
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}
function admin_modulesShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['moduleForm']);
	}
}
?>