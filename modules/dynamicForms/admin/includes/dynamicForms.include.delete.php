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
	//permission check for forms delete
	if(!checkPermission('delete','dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	$data->output['delete'] = $data->output['rejectError'] = $data->output['rejectText'] = FALSE;
	
	// Check to see if the form exists
	$formID = $data->action[3];
	$statement = $db->prepare('getFormById','admin_dynamicForms');
	$statement->execute(array(':id' => $formID));
	$data->output['formItem'] = $statement->fetch();
	if($data->output['formItem'] == FALSE || $formID === FALSE)
	{
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='The form specified could not be found.';
		return;
	}
	// Delete Confirmed
	if (isset($_POST['fromForm']) && $_POST['fromForm']==$data->action[3])
	{
		if(!empty($_POST['delete']))
		{
			// Get A List of ROW IDs
			$statement = $db->prepare('getRowsByForm','admin_dynamicForms');
			$statement->execute(array(':form' => $formID));
			$rowList = $statement->fetchAll();
			foreach($rowList as $rowItem)
			{
				$rowID = $rowItem['id'];
				// Delete All Values Part Of This Row
				$statement = $db->prepare('deleteValueByRow','admin_dynamicForms');
				$statement->execute(array(':rowID' => $rowID));
			}
			// Now Delete The Rows
			$statement = $db->prepare('deleteRowsByForm','admin_dynamicForms');
			$statement->execute(array(':formID' => $formID));
			// Delete The Fields
			$statement = $db->prepare('deleteFieldsByForm','admin_dynamicForms');
			$statement->execute(array(':formID' => $formID));
			// Delete The Form Itself
			$statement = $db->prepare('deleteForm','admin_dynamicForms');
			$statement->execute(array(':id' => $formID));
			
			$data->output['delete'] = 'deleted';
		} else {
			$data->output['delete'] = 'cancelled';
		}
	}
}
function admin_dynamicFormsShow($data)
{
	$aRoot = $data->linkRoot . 'admin/'.$data->output['moduleShortName']['dynamicForms'].'/';
	if($data->output['rejectText'])
	{
		theme_dynamicFormsDeleteReject($data,$aRoot);
		return;
	}
	switch($data->output['delete'])
	{	
		case 'cancelled':
			theme_dynamicFormsDeleteCancelled($aRoot);
		break;
		
		case 'deleted':
			theme_dynamicFormsDeleteDeleted($aRoot);
		break;
		
		default:
			theme_dynamicFormsDeleteDefault($data,$aRoot);
		break;
	}
}

?>