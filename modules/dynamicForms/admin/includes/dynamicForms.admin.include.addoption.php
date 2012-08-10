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

function admin_dynamicFormsBuild($data,$db) {
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
	if($data->output['fieldItem']  === false){
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}

	$form = $data->output['fromForm'] = new formHandler('options',$data,true);
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$form->fromForm)
	) {
		$form->caption = 'New Form Option';
		$form->populateFromPostData();
		
		if ($form->validateFromPost()) {
			$form->sendArray[':formId'] = $data->output['fieldItem']['form'];			
			$form->sendArray[':fieldId'] = $data->output['fieldItem']['id'];			
			$form->sendArray[':sortOrder'] = admin_sortOrder_new($data,$db,'form_fields_options','sortOrder','fieldId',$data->action[3],true);

			$statement = $db->prepare('addOption','admin_dynamicForms');
			$statement->execute($form->sendArray) or die($statement->errorInfo());
			//--Push Form To All Other Languages
			common_populateLanguageTables($data,$db,'form_fields_options','id',$db->lastInsertId());
			
			if (empty($data->output['secondSidebar'])) {
				$data->output['savedOkMessage']='
					<h2>'.$data->phrases['dynamic-forms']['saveOptionSuccessHeading'].'</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/addOption/' . $data->output['fieldItem']['id'] . '">
							'.$data->phrases['dynamic-forms']['addOption'].'
						</a>
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/listOptions/' . $data->output['fieldItem']['id'] . '">
							'.$data->phrases['dynamic-forms']['returnToOptions'].'
						</a>
					</div>';
			}
		} else {
			/*
				invalid data, so we want to show the form again
			*/
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
				</p>';
		}
	}
}

function admin_dynamicFormsShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['fromForm']);
	}
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
?>
