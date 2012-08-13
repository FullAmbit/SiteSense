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
function admin_dynamicFormsBuild($data,$db){
	//permission check for forms edit
	if(!checkPermission('edit','dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFormById','admin_dynamicForms');
	$statement->execute(array(':id' => $data->action[3]));
	$form = $statement->fetch();
	if($form === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Form Doesn\'t Exist</h2>';
		return;
	}
	$data->output['form'] = $form;
	
	//--Any Changes To Be Made?--//
	switch($data->action[4])
	{
		case 'moveUp':
		case 'moveDown':
            admin_sortOrder_move($data,$db,'form_fields',$data->action[4],$data->action[5],'sortOrder','form',TRUE);
		break;
	}
	
	$statement = $db->prepare('getFieldsByForm','admin_dynamicForms');
	$statement->execute(array(':form' => $form['id']));
	$data->output['fields'] = $statement->fetchAll();
}
function admin_dynamicFormsShow($data){
	theme_dynamicFormsListFieldsTableHead($data);
	$count=0;
	foreach($data->output['fields'] as $field){
		theme_dynamicFormsListFieldsTableRow($data,$field,$count);
		$count++;
	}
	theme_dynamicFormsListFieldsTableFoot();
}