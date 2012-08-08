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
function admin_pagesCheckShortNameAndParent($db,$shortName,$parent) {
	$statement=$db->prepare('getPageIdByShortNameAndParent','admin_pages');
	$statement->execute(array(
		':shortName' => $shortName,
		':parent' => $parent
	));
	if ($first=$statement->fetch()) {
		return $first['id'];
	} else return false;
}
function admin_pageOptions($db, $Parent = -1, $Level = 0){ // Using a function is necessary here for recursion
	$options = array();
	if($Parent == -1){
		$options[] = array('value' => 0, 'text' => 'Site Root');
		$options = array_merge($options, admin_pageOptions($db, 0, 1));
	}else{
		$statement = $db->prepare('getPageListByParent','admin_pages');
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
function admin_pagesBuild($data,$db) {
	//permission check for pages edit
	if(!checkPermission('edit','pages',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}

	/* editing an existing from the database */
	$statement=$db->prepare('getPageById','admin_pages');
	$statement->execute(array(
		':id' => $data->action[3]
	));
	$data->output['pageItem'] = $item = $statement->fetch();
	if($item == FALSE)
	{
		$data->output['pagesError'] = 'unknown function';
		return false;
	}
	
	$data->output['pageForm']= $form = new formHandler('addEdit',$data,true);
		$data->output['pageForm']->caption=$data->phrases['pages']['captionEditPage'].' '.$data->output['pageItem']['title'];

	$data->output['pageForm']->fields['parent']['options'] = admin_pageOptions($db);
	// Unset Main Menu Options
	unset($form->fields['showOnMenu']);
	unset($form->fields['menuText']);
	unset($form->fields['menuParent']);
	
	
	foreach ($data->output['pageForm']->fields as $key => $value) {
		if ((!empty($value['params']['type'])) && ($value['params']['type']=='checkbox'))
		{
			$data->output['pageForm']->fields[$key]['checked']= ($item[$key] ? 'checked' : '');
		} else {
			$data->output['pageForm']->fields[$key]['value']=$item[$key];
		}
	}
	
	
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['pageForm']->fromForm)) 
	{
		/*
			we came from the form, so repopulate it and set up our
			sendArray at the same time.
		*/
		$data->output['pageForm']->caption='Editing Page '.$data->action[3];
		$data->output['pageForm']->populateFromPostData();
		$shortName = common_generateShortName($data->output['pageForm']->sendArray[':name']);
		$data->output['pageForm']->sendArray[':shortName'] = $shortName;
		unset($data->output['pageForm']->fields['name']['cannotEqual']);
		
		// Only Run Unique Short Name Check If It's DIFFERENT
		if($shortName !== $data->output['pageItem']['shortName'])
		{
			// Check To See If ShortName Exists Anywhere (Across Any Language)
			if(common_checkUniqueValueAcrossLanguages($data,$db,'pages','id',array('shortName'=>$shortName))){
				$data->output['pageForm']->fields['name']['error']=true;
	            $data->output['pageForm']->fields['name']['errorList'][]='<h2>Unique Name Conflict</h2> This name already exists for a page.';
	            return;
			}
			// Check And Insert URL Remaps
		    $modifiedShortName='^'.$shortName.'(/.*)?$';
		    $statement=$db->prepare('getUrlRemapByMatch','admin_dynamicURLs');
		    $statement->execute(array(
		            ':match' => $modifiedShortName,
		            ':hostname' => ''
		        )
		    );
		    $result=$statement->fetch();
		    if($result===false) {
		        $statement=$db->prepare('updateUrlRemapByMatch','admin_dynamicURLs');
		        $statement->execute(array(
		            ':match'    => '^'.$data->output['pageItem']['shortName'].'(/.*)?$',
		            ':newMatch' => '^'.$shortName.'(/.*)?$',
		            ':replace'  => 'pages/'.$shortName.'\1'
		        ));
		    } else {
		        $data->output['pageForm']->fields['name']['error']=true;
		        $data->output['pageForm']->fields['name']['errorList'][]='<h2>URL Routing Conflict:</h2> The top level route has already been assigned. Please choose a different name.';
		        return;
		    }
		}	
		
	
		// Validate Form
		if ($data->output['pageForm']->validateFromPost()) {
			if (is_numeric($data->action[3])) {
				// Parse
				if($data->settings['useBBCode'] == '1')
				{
					common_loadPlugin($data,'bbcode');
					
					$data->output['pageForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['pageForm']->sendArray[':rawContent']);
				} else {
					$data->output['pageForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['pageForm']->sendArray[':rawContent']);
				}
                $newParent = $data->output['pageForm']->sendArray[':parent'];
                if($newParent !== $data->output['pageItem']['parent']) {
                    $data->output['pageForm']->sendArray[':sortOrder'] = admin_sortOrder_new($data,$db,'pages','sortOrder','parent',$newParent);
                } else {
                    $data->output['pageForm']->sendArray[':sortOrder'] = $data->output['pageItem']['sortOrder'];
                }
				$statement=$db->prepare('updatePageById','admin_pages');
				$data->output['pageForm']->sendArray[':id']=$data->action[3];
				$statement->execute($data->output['pageForm']->sendArray);
				
				// -- Push The Constant Fields Across Other Languages
				common_updateAcrossLanguageTables($data,$db,'pages',array('id'=>$data->action[3]),array(
					'parent' => $data->output['pageForm']->sendArray[':parent'],
					'live' => $data->output['pageForm']->sendArray[':live']
				));
			}
			
			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$data->linkRoot.'admin/pages/edit/new">
						Add New Page
					</a>
					<a href="'.$data->linkRoot.'admin/pages/list/">
						Return to Page List
					</a>
				</div>';
		} else {
			$data->output['secondSidebar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
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