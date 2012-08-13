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
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		return;
	}	
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFieldById','admin_dynamicForms');
	$statement->execute(array(':id' => $data->action[3]));
	$data->output['fieldItem'] = $statement->fetch();
	if($data->output['fieldItem'] === false || $data->output['fieldItem']['type'] !== 'select'){
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}
	//----Modification?----//
	switch($data->action[4])
	{
		case 'moveDown':
		case 'moveUp':
			admin_sortOrder_move($data,$db,'form_fields_options',$data->action[4],$data->action[5],'sortOrder','fieldId',TRUE);
		break;
	}
	// Get Options
	$statement = $db->prepare('getOptionsByFieldId','admin_dynamicForms');
	$statement->execute(array(':fieldId' => $data->output['fieldItem']['id']));
	$data->output['optionList'] = $statement->fetchAll(PDO::FETCH_ASSOC);		
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

function admin_dynamicFormsShow($data){
	theme_dynamicFormsListOptionsButtons($data);
	
	theme_dynamicFormsListOptionsTableHead($data);
			
	if(empty($data->output['optionList']))
	{
		theme_dynamicFormsListOptionsNoOptions($data);
		return;
	} 
	
	
	$i = 0;
	foreach($data->output['optionList'] as $optionIndex => $option){
		theme_dynamicFormsListOptionsTableRow($data,$option,$i);
		$i++;
	}
	theme_dynamicFormsListOptionsTableFoot();
}