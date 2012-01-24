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
	$statement = $db->prepare('getFieldById', 'form');
	$statement->execute(array(':id' => $data->action[3]));
	$data->output['fieldItem'] = $statement->fetch();
	if($data->output['fieldItem'] === false || $data->output['fieldItem']['type'] !== 'select'){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Select Field Doesn\'t Exist</h2>';
		return;
	}
	//----Modification?----//
	switch($data->action[4])
	{
		case 'moveDown':
		case 'moveUp':
			$optionArray = unserialize($data->output['fieldItem']['options']);
			$optionIndex = $data->action[5];
			// If Option Doesn't Exist, DIP.
			if(!isset($optionArray[$optionIndex]))
			{
				return;
			}
			$count = count($optionArray);
			if($data->action[4] == 'moveUp' && $optionIndex > 0)
			{
				$optionArray[$optionIndex]['sortOrder']--;
				$optionArray[$optionIndex - 1]['sortOrder']++;
			} else if($data->action[4] =='moveDown' && $optionIndex < ($count-1))
			{
				$optionArray[$optionIndex]['sortOrder']++;
				$optionArray[$optionIndex + 1]['sortOrder']--;
			}
			
			usort($optionArray,'sortCmp');
			
			$options = serialize($optionArray);
			$statement = $db->prepare('updateOptions', 'form');
			$statement->execute(array(
				':options' => $options,
				':fieldId' => $data->action[3]
			));
		break;
	}
	// Get Options
	$statement = $db->prepare('getOptionsByFieldId','form');
	$statement->execute(array(':fieldId' => $data->output['fieldItem']['id']));
	$optionsSerialized = $statement->fetch();
	$optionList = unserialize($optionsSerialized[0]);
		
	$data->output['optionList'] = $optionList;
}

function sortCmp($a,$b)
{
	if($a['sortOrder'] > $b['sortOrder'])
	{
		return 1;
	} else {
		return -1;
	}
}

function admin_formsShow($data){
	theme_formsListOptionsButtons();
	
	theme_formsListOptionsTableHead();
			
	if(empty($data->output['optionList']))
	{
		theme_formsListOptionsNoOptions();
		return;
	} 
	
	
	$i = 0;
	foreach($data->output['optionList'] as $optionIndex => $option){
		theme_formsListOptionsTableRow($data,$option,$optionIndex,$i);
		$i++;
	}
	theme_formsListOptionsTableFoot();
}