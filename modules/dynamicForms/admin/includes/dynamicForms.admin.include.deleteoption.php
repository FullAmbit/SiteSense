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
function admin_dynamicFormsBuild($data,$db)
{
	//permission check for forms edit
	if(!checkPermission('edit','dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	$data->output['delete'] = "";
	
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
		return;
	}
	
	// Check To See If The Field Exists
	$check = $db->prepare('getFieldById','admin_dynamicForms');
	$check->execute(array(':id' => $data->action[3]));
	if(($data->output['fieldItem'] = $check->fetch()) === FALSE)
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>The ID does not exist in database</h2>';
		return;
	}
	// Check for User Permissions
	if (!checkPermission('canDeleteFormOption','dynamicForms',$data))
	{
		$data->output['rejectError']='Insufficient User Permissions';
		$data->output['rejectText']='You do not have sufficient access to perform this action.';
		return;
	}
	// Get Options
	$statement = $db->prepare('getOptionsByFieldId','admin_dynamicForms');
	$statement->execute(array(':fieldId' => $data->output['fieldItem']['id']));
	$optionsSerialized = $statement->fetch();
	$data->output['optionList'] = unserialize($optionsSerialized[0]);
	
	// Does Our Option Exist?
	if(!isset($data->output['optionList'][$data->action[4]]))
	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Option Not Found</h2>';
		return;
	}
	
	if (isset($_POST['fromForm']) && $_POST['fromForm']==$data->action[3])
	{
		if(!empty($_POST['delete']))
		{
			unset($data->output['optionList'][$data->action[4]]);
			
			$options = serialize($data->output['optionList']);
			
			$statement = $db->prepare('updateOptions','admin_dynamicForms');
			$statement->execute(array(':fieldId' => $data->output['fieldItem']['id'],':options' => $options));
			
			$data->output['delete']='deleted';
			// Success Message
			if (empty($data->output['secondSidebar'])) {
			  $data->output['savedOkMessage']='
				  <h2>Option Deleted Successfully</h2>
				  <div class="panel buttonList">
					  <a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/addOption/'.$data->output['fieldItem']['id'].'">
						  Add New Option
					  </a>
					  <a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listOptions/'.$data->output['fieldItem']['id'].'">
						  Return to Options List
					  </a>
				  </div>';
		  }
		} else {
			$data->output['delete']='cancelled';
		}
	}
}
function admin_dynamicFormsShow($data)
{
	$aRoot=$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/';
	if(empty($data->output['rejectError']))
	{
		switch($data->output['delete'])
		{
			case 'cancelled':
					theme_dynamicFormsDeleteOptionCancelled($data,$aRoot);
				break;
			case 'deleted':
					theme_dynamicFormsDeleteOptionDeleted($aRoot);
				break;
			default:
					theme_dynamicFormsDeleteOptionDefault($data,$aRoot);
				break;
		}
	} else {
		theme_rejectError($data);
	}
}
?>