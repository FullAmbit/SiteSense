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
	//permission check for forms add
	if (!checkPermission('add', 'dynamicForms', $data)) {
		$data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
		return;
	}
	$data->output['fromForm'] = new formHandler('forms', $data, true);
	// Load List Of Plugins
	$statement = $db->prepare('getEnabledPlugins');
	$statement->execute();
	$pluginList = $statement->fetchAll();

	foreach ($pluginList as $pluginItem) {
		$option['text'] = $pluginItem['name'];
		$option['value'] = $pluginItem['name'];

		$data->output['fromForm']->fields['api']['options'][] = $option;
	}
	// Handle Post Request
	if (!empty($_POST['fromForm']) && ($_POST['fromForm']==$data->output['fromForm']->fromForm)) {
		$data->output['fromForm']->populateFromPostData();

		// Check To See If ShortName Exists Anywhere (Across Any Language)
		$data->output['fromForm']->sendArray[':shortName'] = $shortName = common_generateShortName($data->output['fromForm']->sendArray[':name']);
		if (common_checkUniqueValueAcrossLanguages($data, $db, 'forms', 'id', array('shortName'=>$shortName))) {
			$data->output['fromForm']->fields['name']['error']=true;
		    $data->output['fromForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
			return;
		}

		// Run And Validate All Fields //
		if ($data->output['fromForm']->validateFromPost()) {
			switch ($data->output['fromForm']->sendArray[':topLevel']) {
			case 1:
				$modifiedShortName='^'.$shortName.'(/.*)?$';
				$statement=$db->prepare('getUrlRemapByMatch', 'admin_dynamicURLs');
				$statement->execute(array(
						':match' => $modifiedShortName,
						':hostname' => ''
				));
				$result=$statement->fetch();
				if ($result===false) {
					$statement=$db->prepare('insertUrlRemap', 'admin_dynamicURLs');
					$statement->execute(array(
							':match'     => $modifiedShortName,
							':replace'   => 'dynamic-forms/'.$shortName.'\1',
							':sortOrder' => admin_sortOrder_new($data, $db, 'url_remap', 'sortOrder'),
							':regex'     => 0,
							':hostname'  => ''
						));
				} else {
					$data->output['fromForm']->fields['name']['error']=true;
					$data->output['fromForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
					return;
				}
				break;
			}
			/**
			 * Are We Saving A Menu Item?
			 * --------------------------
			 **/
			if ($data->output['fromForm']->sendArray[':showOnMenu']) {
				//----Build The Menu Item----//
				$title = (isset($data->output['fromForm']->sendArray[':menuTitle']{1})) ? $data->output['fromForm']->sendArray[':menuTitle'] : $data->output['fromForm']->sendArray[':name'];

				$sortOrder = admin_sortOrder_new($data, $db, 'main_menu', 'sortOrder', 'parent', '0', TRUE);

				$statement = $db->prepare('newMenuItem', 'admin_mainMenu');
				$statement->execute(array(
						':text' => $title,
						':title' => $title,
						':url' => 'pages/'.$data->output['fromForm']->sendArray[':shortName'].'/',
						':enabled' => '1',
						':parent' => '0',
						':sortOrder' => $sortOrder
					));
				$menuId = $db->lastInsertId();

				//--Push Menu Item To All Other Languages
				common_populateLanguageTables($data, $db, 'main_menu', 'id', $menuId);
			}
			unset($data->output['fromForm']->sendArray[':menuTitle'], $data->output['fromForm']->sendArray[':showOnMenu']);
			//----------------------------//
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
			// Save to DB //
			$statement = $db->prepare('newForm', 'admin_dynamicForms');
			$result = $statement->execute($data->output['fromForm']->sendArray);
			if ($result) {
				//--Push Form To All Other Languages
				common_populateLanguageTables($data,$db,'forms','shortName',$shortName);

				$data->output['savedOkMessage']='
					<h2>'.$data->phrases['dynamic-forms']['saveFormSuccessHeading'].'</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/add">
							'.$data->phrases['dynamic-forms']['addForm'].'
						</a>
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/list/">
							'.$data->phrases['dynamic-forms']['returnToForms'].'
						</a>
					</div>';
			} else {
				$data->output['savedOkMessage'] = '<h2>'.$data->phrases['databaseErrorHeading'].'</h2>'.$data->phrases['databaseErrorMessage'];
			}
		} else {
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
				</p>';
		}
	}
}

function admin_dynamicFormsShow($data) {
	if (!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['fromForm']);
	}
}
?>