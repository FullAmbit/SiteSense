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
function admin_formsBuild($data,$db){
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFormById', 'form');
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
			$qHandle = $db->prepare('getFieldById','form');
			$qHandle->execute(array(':id' => $data->action[5]));
			
			if($fieldItem = $qHandle->fetch())
			{				
				$statement = $db->prepare('countFieldsByForm','form');
				$statement->execute(array(':formId' => $form['id']));
				list($rowCount) = $statement->fetch();
				
				if($data->action[4] == 'moveUp' && intval($fieldItem['sortOrder']) > 1) {
					$query1 = 'shiftFieldOrderUpRelative';
					$query2 = 'shiftFieldOrderUpByID';
				} else if($data->action[4] == 'moveDown' && intval($fieldItem['sortOrder']) < $rowCount) {
					$query1 = 'shiftFieldOrderDownRelative';
					$query2 = 'shiftFieldOrderDownByID';
				}
				
				if(isset($query1))
				{
					$statement = $db->prepare($query1,'form');
					$statement->execute(array(
						':sortOrder' => $fieldItem['sortOrder'],
						':formId' => $fieldItem['form']
					));
					$statement = $db->prepare($query2,'form');
					$statement->execute(array(
						':id' => $fieldItem['id']
					));
				}
			}
		break;
	}
	
	$statement = $db->prepare('getFieldsByForm', 'form');
	$statement->execute(array(':form' => $form['id']));
	$data->output['fields'] = $statement->fetchAll();
}
function admin_formsShow($data){
	theme_formsListFieldsTableHead();
	foreach($data->output['fields'] as $field){
		 theme_formsListFieldsTableRow($data,$field);
	}
	theme_formsListFieldsTableFoot($data);
}