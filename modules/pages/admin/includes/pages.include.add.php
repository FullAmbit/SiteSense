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

function admin_pagesBuild($data,$db)
{
	//permission check for pages add
	if(!checkPermission('add','pages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}
	$data->output['pageForm']=new formHandler('pagesEdit',$data,true);
	$data->output['pageForm']->fields['parent']['options'] = admin_pageOptions($db);
	
	if (($data->action[3]=='childOf') && is_numeric($data->action[4]))
	{
		$data->output['pageForm']->fields['parent']['value']=$data->action[4];
	}
	
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['pageForm']->fromForm)) 
	{
		$data->output['pageForm']->populateFromPostData();
		$shortName = common_generateShortName($_POST[$data->output['pageForm']->formPrefix.'name']);
		$data->output['pageForm']->sendArray[':shortName'] = $shortName;
		
		$statement = $db->prepare('getExistingShortNames','pages');
		$statement->execute();
		//if(count($pageShortNameList = $statement->fetchAll())){
		//	echo 'test';
		//}
		//foreach($pageShortNameList as $item)
		//{
		//	$cannotEqual[] = $item['shortName'];
		//}
		$data->output['pageForm']->fields['name']['cannotEqual'] = NULL;
		// Apply ShortName Convention To Name For Use In Comparison //
		$_POST[$data->output['pageForm']->formPrefix.'name'] = $shortName;
		
		// Validate Form
		if ($data->output['pageForm']->validateFromPost())
		{
			// Get Sort Order
			$statement = $db->prepare('countPagesByParent','pages');
			$statement->execute(array(':parent' => $data->output['pageForm']->sendArray[':parent']));
			list($rowCount) = $statement->fetch();
			
			$data->output['pageForm']->sendArray[':sortOrder'] = $rowCount + 1;
			
			// Parse
			if($data->settings['useBBCode'] == '1')
			{
				common_loadPlugin($data,'bbcode');
				
				$data->output['pageForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['pageForm']->sendArray[':rawContent']);
			} else {
				$data->output['pageForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['pageForm']->sendArray[':rawContent']);
			}
			
			if($data->output['pageForm']->sendArray[':showOnMenu'])
			{
				//----Build The Menu Item----//
				$title = (isset($data->output['pageForm']->sendArray[':menuText']{1})) ? $data->output['pageForm']->sendArray[':menuText'] : $data->output['pageForm']->sendArray[':name'];
				// Sort Order
				$rowCount = $db->countRows('main_menu');
				$sortOrder = $rowCount + 1;
				
				$statement = $db->prepare('newMenuItem','mainMenu');
				$statement->execute(array(
					':text' => $title,
					':title' => $title,
					':url' => 'pages/'.$data->output['pageForm']->sendArray[':shortName'].'/',
					':enabled' => '1',
					':parent' => '0',
					':sortOrder' => $sortOrder
				));
				
				$menuId = $db->lastInsertId();
			}
			
			unset(
				$data->output['pageForm']->sendArray[':menuText'],
				$data->output['pageForm']->sendArray[':showOnMenu']
			);
			
			
			// Save To DB
			var_dump($statement = $db->prepare('insertPage','pages'));
			var_dump($data->output['pageForm']->sendArray);
			if($statement->execute($data->output['pageForm']->sendArray)) {
				
				
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
                $data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
            }
		}
	}
}

function admin_pageOptions($db, $Parent = -1, $Level = 0){ // Using a function is necessary here for recursion
	$options = array();
	if($Parent == -1){
		$options[] = array('value' => 0, 'text' => 'Site Root');
		$options = array_merge($options, admin_pageOptions($db, 0, 1));
	}else{
		$statement = $db->prepare('getPageListByParent', 'pages');
		$statement->execute(array(':parent' => $Parent));
		while($item = $statement->fetch()){
			$options[] = array(
				'value' => $item['id'],
				'text' => str_repeat('-', $Level * 4) . ' ' . $item['shortName']
			);
			$options = array_merge($options, admin_pageOptions($db, $item['id'], $Level + 1));
		}
	}
	return $options;
}

function admin_pagesShow($data)
{
	if ($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} else if (!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['pageForm']);
	}
}
?>