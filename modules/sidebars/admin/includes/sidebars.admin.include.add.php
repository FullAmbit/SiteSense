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

function admin_sidebarsBuild($data, $db) {
	if (!checkPermission('add', 'sidebars', $data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>'.$data->phrases['sidebars']['insufficientUserPermissions'].'</h2>'.$data->phrases['sidebars']['insufficientUserPermissions2'];
		return;
	}
	// Load Form
	$data->output['sidebarForm'] = new formHandler('sidebars', $data, true);

	if (!empty($_POST['fromForm']) && ($_POST['fromForm'] == $data->output['sidebarForm']->fromForm)) {
		$data->output['sidebarForm']->populateFromPostData();

		$data->output['sidebarForm']->sendArray[':shortName'] = $shortName = common_generateShortName($_POST[$data->output['sidebarForm']->formPrefix.'name']);
		// Check To See If ShortName Exists Anywhere (Across Any Language)
		if (common_checkUniqueValueAcrossLanguages($data, $db, 'sidebars', 'id', array('shortName'=>$shortName))) {
			$data->output['sidebarForm']->fields['name']['error']=true;
			$data->output['sidebarForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['sidebars']['uniqueNameConflict'].'</h2>'.$data->phrases['sidebars']['uniqueNameConflict2'];
			return;
		}

		if ($data->output['sidebarForm']->validateFromPost()) {
			//--Parsing--//
			if ($data->settings['useBBCode'] == '1') {
				common_loadPlugin($data, 'bbcode');
				$data->output['sidebarForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['sidebarForm']->sendArray[':rawContent']);
			} else {
				$data->output['sidebarForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['sidebarForm']->sendArray[':rawContent']);
			}
			$data->output['sidebarForm']->sendArray[':sortOrder']=admin_sortOrder_new($data, $db, 'sidebars','sortOrder',NULL,NULL,TRUE);
			// Save To DB
			$statement = $db->prepare('insertSidebar', 'admin_sidebars');
			$result = $statement->execute($data->output['sidebarForm']->sendArray);
			$sidebarId = $db->lastInsertId();

			if ($result == FALSE) {
				$data->output['error'] = TRUE;
				return;
			}
			$sortOrder=$data->output['sidebarForm']->sendArray[':sortOrder'];
			// Duplicate Across Other Languages
			common_populateLanguageTables($data,$db,'sidebars','shortName',$shortName);

			//---Pages---//
			$pageQ = $db->prepare('createSidebarSetting', 'admin_pages');
			$statement = $db->prepare('getAllPageIds', 'admin_pages');
			$statement->execute();
			$pageList = $statement->fetchAll();

			foreach ($pageList as $pageItem) {
				$vars = array(
					':pageId' => $pageItem['id'],
					':sidebarId' => $sidebarId,
					':enabled' => 0,
					':sortOrder' => $sortOrder
				);
				$pageQ->execute($vars);
			}
			//---Modules---//
			$moduleQ = $db->prepare('createSidebarSetting', 'admin_modules');
			$statement = $db->prepare('getAllModuleIds', 'admin_modules');
			$statement->execute();
			$moduleList = $statement->fetchAll();
			foreach ($moduleList as $moduleItem) {
				$vars = array(
					':moduleId' => $moduleItem['id'],
					':sidebarId' => $sidebarId,
					':enabled' => 0,
					':sortOrder' => $sortOrder
				);
				$moduleQ->execute($vars);
			}
			//---Forms---//
			$formQ = $db->prepare('createSidebarSetting', 'admin_dynamicForms');
			$statement = $db->prepare('getAllFormIds', 'admin_dynamicForms');
			$statement->execute();
			$formList = $statement->fetchAll();
			foreach ($formList as $formItem) {
				$vars = array(
					':formId' => $formItem['id'],
					':sidebarId' => $sidebarId,
					':enabled' => 0,
					':sortOrder' => $sortOrder
				);
				$formQ->execute($vars);
			}

			$data->output['savedOkMessage']='
				<h2>'.$data->phrases['sidebars']['valuesSaved'].'</h2>
				<p>
					'.$data->phrases['sidebars']['valuesSaved2'].$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$aRoot.'edit/new">
						'.$data->phrases['sidebars']['addPage'].'
					</a>
					<a href="'.$aRoot.'list/">
						'.$data->phrases['sidebars']['returnToPageList'].'
					</a>
				</div>';
		} else {
			// Throw Form Error //
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['sidebars']['errorInData'].'</h2>
				<p>
					'.$data->phrases['sidebars']['errorInData2'].'
				</p>';
		}
	}
}

function admin_sidebarsShow($data) {
	if (isset($data->output['error']) && $data->output['error'] === TRUE) {
		echo $data->phrases['sidebars']['errorSaving'];
	}
	else if (isset($data->output['savedOkMessage'])) {
			echo $data->output['savedOkMessage'];
		} else {
		theme_buildForm($data->output['sidebarForm']);
	}
}

?>