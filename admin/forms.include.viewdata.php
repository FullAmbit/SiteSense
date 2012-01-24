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
		$data->output['abortMessage'] = '<h2>No Form ID Given</h2>';
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
	$statement = $db->prepare('getFieldsByForm', 'form');
	$statement->execute(array(':form' => $form['id']));
	$data->output['fields'] = $statement->fetchAll();
	$statement = $db->prepare('getRowsByForm', 'form');
	$statement->execute(array(':form' => $form['id']));
	$data->output['rows'] = $statement->fetchAll();
	$statement = $db->prepare('getValuesByForm', 'form');
	$statement->execute(array(':form' => $form['id']));
	$results = $statement->fetchAll();
	$values = array();
	foreach($results as $value){
		if(!isset($values[$value['row']])){
			$values[$value['row']] = array();
		}
		$values[$value['row']][$value['field']] = $value['value']; 
	}
	$data->output['values'] = $values;
}
function admin_formsShow($data){
	theme_viewdataTableHead();
	foreach($data->output['fields'] as $field){
		theme_viewdataTableHeadCell($field);
	}
	foreach($data->output['rows'] as $row){
		theme_viewdataTableStartRow() ;
		foreach($data->output['fields'] as $field){
			if(isset($data->output['values'][$row['id']][$field['id']])){
				$value = $data->output['values'][$row['id']][$field['id']];
				if($field['type'] == 'checkbox'){
					$value = ($value == 1) ? 'Yes' : 'No'; 
				}
			}else{
				$value = "-unset-";
			}
			theme_viewdataTableCell($value);
		}
	}
	theme_viewdataTableFoot();
}