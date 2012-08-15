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
function admin_urlsBuild($data,$db) {
    if(!checkPermission('edit','urls',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
	$remapId = (int)$data->action[3];
	$check = $db->prepare('getUrlRemapById','admin_urls');
	$check->execute(array(
		':id' => $remapId
	));
	// Check To Make Sure It Exists
	if(($data->output['urlremap'] = $check->fetch()) === FALSE){
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}
    if(!$data->output['urlremap']['regex']) {
        // Standard Mode
        $data->output['urlremap']['match']=str_replace('^','',$data->output['urlremap']['match']);
        $data->output['urlremap']['match']=str_replace('(/.*)?$','',$data->output['urlremap']['match']);
        $data->output['urlremap']['replace']=str_replace('\1','',$data->output['urlremap']['replace']);
    }
    // Load Hostnames
    $statement = $db->prepare('getAllHostnames','admin_hostnames');
    $statement->execute();
    $data->output['hostnameList'] = $statement->fetchAll(PDO::FETCH_ASSOC);
    
	// Create The Form
	$form = $data->output['remapForm'] = new formHandler('addEdit',$data,true);
	$form->caption = $data->phrases['urls']['captionEditRemap'];
	
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$form->fromForm)) {
		// Populate The Send Array
		$form->populateFromPostData();
		if ($form->validateFromPost()) {
			// Check Hostname
			if(!isset($form->sendArray[':hostname'])) $form->sendArray[':hostname'] = '';
			
			if(!$data->output['urlremap']['regex']) {
                // Remove
                $form->sendArray[':match']=str_replace('^','',$form->sendArray[':match']);
                $form->sendArray[':match']=str_replace('(/.*)?$','',$form->sendArray[':match']);
                $form->sendArray[':replace']=str_replace('\1','',$form->sendArray[':replace']);
                // Trim Forward Slashes + Whitespace from Beginning and End
                $form->sendArray[':match']=trim($form->sendArray[':match']);
                $form->sendArray[':replace']=trim($form->sendArray[':replace']);
                $form->sendArray[':match']=trim($form->sendArray[':match'],'/');
                $form->sendArray[':replace']=trim($form->sendArray[':replace'],'/');
                // Add Regex
                $form->sendArray[':match']='^'.$form->sendArray[':match'].'(/.*)?$';
                $form->sendArray[':replace']=$form->sendArray[':replace'].'\1';
            }
            $statement = $db->prepare('editUrlRemap','admin_urls');
            $form->sendArray[':id'] = $remapId;
            $result = $statement->execute($form->sendArray) ;
			
			if($result == FALSE) {
                $data->output['remapForm']->fields['match']['error']=true;
                $data->output['remapForm']->fields['match']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
                return;
			}
			
			if (empty($data->output['secondSidebar'])) {
				$data->output['savedOkMessage']='
					<h2>'.$data->phrases['urls']['saveRemapSuccess'].'</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/urls/add">
							'.$data->phrases['urls']['addRemap'].'
						</a>
						<a href="'.$data->linkRoot.'admin/urls/list/">
							'.$data->phrases['urls']['returnToList'].'
						</a>
					</div>';
			}
		} else {
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
				</p>';
		}
	}
}
function admin_urlsShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['remapForm']);
	}
}
?>