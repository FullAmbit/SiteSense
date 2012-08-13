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

function admin_pagesBuild($data, $db) {
	//permission check for pages add
	if (!checkPermission('add', 'pages', $data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
		return;
	}
	$data->output['pageForm']=new formHandler('addEdit', $data, true);
	$data->output['pageForm']->fields['parent']['options'] = admin_pageOptions($db);

	if (($data->action[3]=='childOf') && is_numeric($data->action[4])) {
		$data->output['pageForm']->fields['parent']['value']=$data->action[4];
	}

	// Get Menu Items To Select Parent
	common_include('modules/mainMenu/admin/includes/mainMenu.admin.include.add.php');
	$data->output['pageForm']->fields['menuParent']['options'] = array_merge($data->output['pageForm']->fields['menuParent']['options'], admin_mainMenuOptions($db));

	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['pageForm']->fromForm)) {
		$data->output['pageForm']->populateFromPostData();
		$shortName = common_generateShortName($_POST[$data->output['pageForm']->formPrefix.'name']);
		$data->output['pageForm']->sendArray[':shortName'] = $shortName;

		// Check To See If ShortName Exists Anywhere (Across Any Language)
		if (common_checkUniqueValueAcrossLanguages($data, $db, 'pages', 'id', array('shortName'=>$shortName))) {
			$data->output['pageForm']->fields['name']['error']=true;
			$data->output['pageForm']->fields['name']['errorList'][]='<h2>Unique Name Conflict</h2> This name already exists for a page.';
			return;
		}

		// Validate Form
		if ($data->output['pageForm']->validateFromPost()) {
			if ($data->output['pageForm']->sendArray[':parent']==0) {
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
							':replace'   => 'pages/'.$shortName.'\1',
							':sortOrder' => admin_sortOrder_new($data, $db, 'urls', 'sortOrder','hostname',''),
							':regex'     => 0,
							':hostname'  => '',
							':isRedirect' => 0
						));
				} else {
					$data->output['pageForm']->fields['name']['error']=true;
					$data->output['pageForm']->fields['name']['errorList'][]='<h2>URL Routing Conflict:</h2> The top level route has already been assigned. Please choose a different name.';
					return;
				}
			}
			// Get Sort Order
			$data->output['pageForm']->sendArray[':sortOrder']=
				admin_sortOrder_new($data, $db, 'pages', 'sortOrder', 'parent', $data->output['pageForm']->sendArray[':parent'], TRUE);

			// Parse
			if ($data->settings['useBBCode'] == '1') {
				common_loadPlugin($data, 'bbcode');

				$data->output['pageForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['pageForm']->sendArray[':rawContent']);
			} else {
				$data->output['pageForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['pageForm']->sendArray[':rawContent']);
			}

			if ($data->output['pageForm']->sendArray[':showOnMenu']) {
				//----Build The Menu Item----//
				$title = (isset($data->output['pageForm']->sendArray[':menuText']{1})) ? $data->output['pageForm']->sendArray[':menuText'] : $data->output['pageForm']->sendArray[':name'];

                $sortOrder = admin_sortOrder_new($data,$db,'main_menu','sortOrder','parent','0',TRUE);

				$statement = $db->prepare('newMenuItem', 'admin_mainMenu');
				$statement->execute(array(
						':text' => $title,
						':title' => $title,
						':url' => 'pages/'.$data->output['pageForm']->sendArray[':shortName'].'/',
						':enabled' => '1',
						':parent' => '0',
						':sortOrder' => $sortOrder
					));
				$menuId = $db->lastInsertId();
				
				//--Push Menu Item To All Other Languages
				common_populateLanguageTables($data,$db,'main_menu','id',$menuId);
			}

			unset(
				$data->output['pageForm']->sendArray[':menuText'],
				$data->output['pageForm']->sendArray[':showOnMenu'],
				$data->output['pageForm']->sendArray[':menuParent']
			);


			// Save To DB
			$statement=$db->prepare('insertPage', 'admin_pages');
			if ($statement->execute($data->output['pageForm']->sendArray)) {
				//--Push Page To All Other Languages
				common_populateLanguageTables($data,$db,'pages','shortName',$data->output['pageForm']->sendArray[':shortName']);

				$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$data->linkRoot.'admin/pages/add/">
						Add New Page
					</a>
					<a href="'.$data->linkRoot.'admin/pages/list/">
						Return to Page List
					</a>'.
					(isset($menuId) ? '<a href="'.$data->linkRoot.'admin/main-menu/edit/'.$menuId.'/">Edit Menu Item</a>' : NULL)
					.
					'</div>';
			} else {
				var_dump($statement->errorInfo());
				$data->output['secondSidebar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
			}
		}
	}
}

function admin_pageOptions($db, $Parent = -1, $Level = 0) { // Using a function is necessary here for recursion
	$options = array();
	if ($Parent == -1) {
		$options[] = array('value' => 0, 'text' => 'Site Root');
		$options = array_merge($options, admin_pageOptions($db, 0, 1));
	}else {
		$statement = $db->prepare('getPageListByParent', 'admin_pages');
		$statement->execute(array(':parent' => $Parent));
		while ($item = $statement->fetch()) {
			$options[] = array(
				'value' => $item['id'],
				'text' => str_repeat('-', $Level * 4) . ' ' . $item['shortName']
			);
			$options = array_merge($options, admin_pageOptions($db, $item['id'], $Level + 1));
		}
	}
	return $options;
}

function admin_pagesShow($data) {
	if ($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} else if (!empty($data->output['savedOkMessage'])) {
			echo $data->output['savedOkMessage'];
		} else {
		theme_buildForm($data->output['pageForm']);
	}
}
?>