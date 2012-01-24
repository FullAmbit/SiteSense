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

function admin_formsBuild($data,$db) {
	if($data->action[3] === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>No ID Given</h2>';
		return;
	}
	$data->action[3] = intval($data->action[3]);
	$statement = $db->prepare('getFieldById', 'form');
	$statement->execute(array(':id' => $data->action[3]));
	$data->output['fieldItem'] = $statement->fetch();
	if($data->output['fieldItem']  === false){
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Form Doesn\'t Exist</h2>';
		return;
	}

	$form = $data->output['formForm'] = new formHandler('formoptions',$data,true);
	if (
		(!empty($_POST['fromForm'])) &&
		($_POST['fromForm']==$form->fromForm)
	) {
		$form->caption = 'New Form Option';
		$form->populateFromPostData();
		
		if ($form->validateFromPost()) {
			
			$form->sendArray[':fieldId'] = $data->output['fieldItem']['id'];
			$options = ($data->output['fieldItem']['options'] !== NULL) ? unserialize($data->output['fieldItem']['options']) : array();
			$options[count($options)] = array('text' => $form->sendArray[':text'],'value' => $form->sendArray[':value'],'sortOrder' => count($options) + 1);
			
			usort($options,"sortCmp");
			
			$form->sendArray[':options'] = serialize($options);
			unset($form->sendArray[':text'],$form->sendArray[':value']);
			
			$statement = $db->prepare('updateOptions', 'form');
			$statement->execute($form->sendArray) or die($statement->errorInfo());
			if (empty($data->output['secondSideBar'])) {
				$data->output['savedOkMessage']='
					<h2>Form Option Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/forms/addoption/' . $data->output['fieldItem']['id'] . '">
							Add New Option
						</a>
						<a href="'.$data->linkRoot.'admin/forms/listoptions/' . $data->output['fieldItem']['id'] . '">
							Return to Options List
						</a>
					</div>';
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
		}
	}
}

function admin_formsShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['formForm']);
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
