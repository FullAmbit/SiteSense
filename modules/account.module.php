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
function page_getUniqueSettings($data) {
	$data->output['pageShortName']='account';
}
function build_accountSettings($data, $db){
	if(!isset($data->user['id'])){
		common_redirect_local($data, 'login');
	} else {
		$data->output['userForm'] = new formHandler('user', $data);
		if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$data->output['userForm']->fromForm)
		) {
			//$data->output['userForm']->caption = 'Editing Page ' . $data->action[3];
			$data->output['userForm']->populateFromPostData();
			if ($data->output['userForm']->validateFromPost()) {
				unset($data->output['userForm']->sendArray[':password2']);
				if ($data->output['userForm']->sendArray[':password']=='') {
					$statement=$db->prepare('updateUserByIdNoPw','user');
					unset($data->output['userForm']->sendArray[':password']);
					$data->output['userForm']->sendArray[':id']=$data->user['id'];
				} else {
					$data->output['userForm']->sendArray[':password']=hash('sha256',$data->output['userForm']->sendArray[':password']);
					$statement=$db->prepare('updateUserById','user');
					$data->output['userForm']->sendArray[':id']=$data->user['id'];
				}
				$statement->execute($data->output['userForm']->sendArray);
				if (empty($data->output['secondSideBar'])) {
					$data->output['savedOkMessage']=true;
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
				if ($data->output['userForm']->sendArray[':password'] != $data->output['userForm']->sendArray[':password2']) {
					$data->output['secondSideBar'].='
						<p>
							<strong>Password fields do not match!</strong>
						</p>';
					$data->output['userForm']->fields['password']['error']=true;
					$data->output['userForm']->fields['password2']['error']=true;
				}
			}
		} else {
			$data->output['userForm']->caption='Editing User Details';
			$statement=$db->prepare('getById','user');
			$statement->execute(array(
					':id' => $data->user['id']
			));
			if (false !== ($item = $statement->fetch())) {
				foreach ($data->output['userForm']->fields as $key => $value) {
					if (empty($value['params']['type'])){
						$value['params']['type'] = '';
						switch ($value['params']['type']) {
							case 'checkbox':
								$data->output['userForm']->fields[$key]['checked']=(
								$item[$key] ? 'checked' : ''
								);
								break;
							case 'password':
								/* NEVER SEND PASSWORD TO A FORM!!! */
								break;
							default:
								$data->output['userForm']->fields[$key]['value']=$item[$key];
						}
					}
				}
			}
		}
	}
}
function page_buildContent($data,$db) {
	build_accountSettings($data,$db);
}
function page_content($data) {
	common_include($data->themeDir . 'formGenerator.template.php');
	theme_contentBoxHeader('Manage UrAccount Settings');
	theme_accountSettings($data);
	theme_contentBoxFooter();
	echo $data->jsEditor->addEditor('useredit_aboutSummary');
}
?>