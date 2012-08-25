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
function admin_dynamicFormsBuild($data, $db) {
	//permission check for forms edit
	if (!checkPermission('edit', 'dynamicForms', $data)) {
		$data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		return;
	}
	// Check If Form Exists //
	$formId = $data->action[3];
	$check = $db->prepare('getFormById', 'admin_dynamicForms');
	$check->execute(array(':id' => $formId));
	if (($data->output['formItem'] = $check->fetch()) === FALSE) {
		$data->output['abort'] = true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}

	$form = $data->output['fromForm'] = new formHandler('forms', $data, true);
	// Editing...So Remove Menu Options
	unset($form->fields['showOnMenu'],$form->fields['menuTitle']);
	// Load List Of Plugins
	$statement=$db->query('getEnabledPlugins');
	$statement->execute();
	$pluginList = $statement->fetchAll();
	foreach ($pluginList as $pluginItem) {
		$option['text'] = $pluginItem['name'];
		$option['value'] = $pluginItem['name'];

		$data->output['fromForm']->fields['api']['options'][] = $option;
	}
	// Handle Form Submission //
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$form->fromForm)) {
		$data->output['fromForm']->populateFromPostData();
		/**
		 * Set Up Short Name Check (ONLY if different from currently existing
		 **/
		$data->output['fromForm']->sendArray[':shortName'] = $shortName = common_generateShortName($data->output['fromForm']->sendArray[':name']);
		unset($data->output['fromForm']->fields['name']['cannotEqual']);

		if ($shortName !== $data->output['formItem']['shortName']) {
			// Check To See If ShortName Exists Anywhere (Across Any Language)
			if(common_checkUniqueValueAcrossLanguages($data,$db,'pages','id',array('shortName'=>$shortName))){
				$data->output['fromForm']->fields['name']['error']=true;
				$data->output['fromForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
	            return;
			}
			$newShortName=TRUE;
		}
		$newShortName=FALSE;
		// Validate All Form Fields
		if ($data->output['fromForm']->validateFromPost()) {
			if (intval($data->output['fromForm']->sendArray[':topLevel'])!==intval($data->output['formItem']['topLevel'])) {
				switch ($data->output['fromForm']->sendArray[':topLevel']) {
				case 0:
					$statement=$db->prepare('deleteReplacementByMatch', 'admin_urls');
					$statement->execute(array(
							':match' => '^'.$data->output['formItem']['shortName'].'(/.*)?$'
						));
					break;
				case 1:
					$modifiedShortName='^'.$shortName.'(/.*)?$';
					$statement=$db->prepare('getUrlRemapByMatch', 'admin_urls');
					$statement->execute(array(
							':match' => $modifiedShortName,
							':hostname' => ''
						)
					);
					$result=$statement->fetch();
					if ($result===false) {
						$statement=$db->prepare('insertUrlRemap', 'admin_urls');
						$statement->execute(array(
								':match'     => $modifiedShortName,
								':replace'   => 'dynamic-forms/'.$shortName.'\1',
								':sortOrder' => admin_sortOrder_new($data, $db, 'urls', 'sortOrder','hostname',''),
								':regex'     => 0,
								':hostname' => '',
								':isRedirect' => 0
							));
					} else {
						$data->output['fromForm']->fields['name']['error']=true;
						$data->output['fromForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
						return;
					}
					break;
				}
			} elseif ($newShortName) {
				if ($data->output['fromForm']->sendArray[':topLevel']) {
					$modifiedShortName='^'.$shortName.'(/.*)?$';
					$statement=$db->prepare('getUrlRemapByMatch', 'admin_urls');
					$statement->execute(array(
							':match' => $modifiedShortName
						)
					);
					$result=$statement->fetch();
					if ($result===false) {
						$statement=$db->prepare('updateUrlRemapByMatch', 'admin_urls');
						$statement->execute(array(
								':match' => '^'.$data->output['formItem']['shortName'].'(/.*)?$',
								':newMatch'   => '^'.$shortName.'(/.*)?$',
								':replace' => 'dynamic-forms/'.$shortName.'\1'
							));
					} else {
						$data->output['fromForm']->fields['name']['error']=true;
						$data->output['fromForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
						return;
					}
				}
			}
			//----Parse---//
			if ($data->settings['useBBCode'] == '1') {
				common_loadPlugin($data, 'bbcode');

				$data->output['fromForm']->sendArray[':parsedContentBefore'] = $data->plugins['bbcode']->parse($data->output['fromForm']->sendArray[':rawContentBefore']);
				$data->output['fromForm']->sendArray[':parsedContentAfter'] = $data->plugins['bbcode']->parse($data->output['fromForm']->sendArray[':rawContentAfter']);
				$data->output['fromForm']->sendArray[':parsedSuccessMessage'] = $data->plugins['bbcode']->parse($data->output['fromForm']->sendArray[':rawSuccessMessage']);
			} else {
				$data->output['fromForm']->sendArray[':parsedContentBefore'] = htmlspecialchars($data->output['fromForm']->sendArray[':rawContentBefore']);
				$data->output['fromForm']->sendArray[':parsedContentAfter'] = htmlspecialchars($data->output['fromForm']->sendArray[':rawContentAfter']);
				$data->output['fromForm']->sendArray[':parsedSuccessMessage'] = htmlspecialchars($data->output['fromForm']->sendArray[':rawSuccessMessage']);
			}
			//------------//
			// Save To DB //
			$statement = $db->prepare('editForm', 'admin_dynamicForms');

			$data->output['fromForm']->sendArray[':id'] = $formId;
			$statement->execute($data->output['fromForm']->sendArray);
			
			// -- Push The Constant Fields Across Other Languages
			common_updateAcrossLanguageTables($data,$db,'forms',array('id'=>$formId),array(
				'requireLogin' => $data->output['fromForm']->sendArray[':requireLogin'],
				'enabled' => $data->output['fromForm']->sendArray[':enabled'],
				'eMail' =>  $data->output['fromForm']->sendArray[':eMail'],
				'topLevel' =>  $data->output['fromForm']->sendArray[':topLevel'],
				'api' =>  $data->output['fromForm']->sendArray[':api']
			));

			if (empty($data->output['secondSidebar'])) {
				$data->output['savedOkMessage']='
					<h2>'.$data->phrases['dynamic-forms']['saveFormSuccessHeading'].'</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/add">
							'.$data->phrases['dynamic-forms']['newForm'].'
						</a>
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/list/">
							'.$data->phrases['dynamic-forms']['returnToForms'].'
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
?>
