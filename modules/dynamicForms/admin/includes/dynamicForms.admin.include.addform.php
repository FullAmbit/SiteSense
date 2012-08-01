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
function admin_dynamicFormsBuild($data,$db)
{
	//permission check for forms add
	if(!checkPermission('add','dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	$data->output['fromForm'] = new formHandler('forms',$data,true);
	// Load List Of Plugins
	$statement = $db->prepare('getEnabledPlugins');
	$statement->execute();
	$pluginList = $statement->fetchAll();
	
	foreach($pluginList as $pluginItem)
	{
		$option['text'] = $pluginItem['name'];
		$option['value'] = $pluginItem['name'];
			
		$data->output['fromForm']->fields['api']['options'][] = $option;
	}
	// Handle Post Request
	if(!empty($_POST['fromForm']) && ($_POST['fromForm']==$data->output['fromForm']->fromForm))
	{
		$data->output['fromForm']->populateFromPostData();
		/**
		 * Set up Short Name Check
		**/
		$shortName = common_generateShortName($_POST[$data->output['fromForm']->formPrefix.'name']);
		// Since we're comparing the name field against shortName, set the name value equal to the new shortName for comparison
		$data->output['fromForm']->sendArray[':shortName'] = $_POST[$data->output['fromForm']->formPrefix.'name'] = $shortName;
		// Load All Existing Sidebar ShortNames For Comparison
		$statement = $db->prepare('getExistingShortNames','admin_dynamicForms');
		$statement->execute();
		$formList = $statement->fetchAll();
		$existingShortNames = array();
		foreach($formList as $formItem)
		{
			$existingShortNames[] = $formItem['shortName'];
		}
		$data->output['fromForm']->fields['name']['cannotEqual'] = $existingShortNames;
        $_POST[$data->output['fromForm']->formPrefix.'name'] = $shortName;
		/*----------------*/
		// Run And Validate All Fields //
		if($data->output['fromForm']->validateFromPost())
		{
		    switch($data->output['fromForm']->sendArray[':topLevel']) {
          case 1:
              $modifiedShortName='^'.$shortName.'(/.*)?$';
              $statement=$db->prepare('getUrlRemapByMatch','admin_dynamicURLs');
              $statement->execute(array(
                      ':match' => $modifiedShortName,
                      ':hostname' => ''
                  )
              );
              $result=$statement->fetch();
              if($result===false) {
                  $statement=$db->prepare('insertUrlRemap','admin_dynamicURLs');
                  $statement->execute(array(
                      ':match'     => $modifiedShortName,
                      ':replace'   => 'dynamic-forms/'.$shortName.'\1',
                      ':sortOrder' => admin_sortOrder_new($db,'url_remap','sortOrder'),
                      ':regex'     => 0,
                      ':hostname'  => ''
                  ));
              } else {
                  $data->output['fromForm']->fields['name']['error']=true;
                  $data->output['fromForm']->fields['name']['errorList'][]='<h2>URL Routing Conflict:</h2> The top level route has already been assigned. Please choose a different name.';
                  return;
              }
          break;
        }
			/**
			 *	Are We Saving A Menu Item?
			 *	--------------------------
			**/
			if($data->output['fromForm']->sendArray[':showOnMenu'])
			{
				//----Build The Menu Item----//
				$title = (isset($data->output['fromForm']->sendArray[':menuTitle']{1})) ? $data->output['fromForm']->sendArray[':menuTitle'] : $data->output['fromForm']->sendArray[':name'];
				// Sort Order
				$rowCount = $db->countRows('main_menu');
				$sortOrder = $rowCount + 1;
				
				$statement = $db->prepare('newMenuItem','admin_mainMenu');
				$statement->execute(array(
					':text' => $title,
					':title' => $title,
					':url' => 'forms/'.$data->output['fromForm']->sendArray[':shortName'].'/',
					':enabled' => '1',
					':parent' => '0',
					':sortOrder' => $sortOrder
				));
				
				$menuId = $db->lastInsertId();
			}
			unset($data->output['fromForm']->sendArray[':menuTitle'],$data->output['fromForm']->sendArray[':showOnMenu']);
			//----------------------------//
			//----Parse---//
			if($data->settings['useBBCode'] == '1')
			{
				common_loadPlugin($data,'bbcode');
				
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
			$statement = $db->prepare('newForm','admin_dynamicForms');
			$result = $statement->execute($data->output['fromForm']->sendArray);
			if($result)
			{
				$data->output['savedOkMessage']='
					<h2>Form Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/add">
							Add New Form
						</a>
						<a href="'.$data->linkRoot.'admin/'.$data->output['moduleShortName']['dynamicForms'].'/list/">
							Return to Form List
						</a>
					</div>';
			} else {
				$data->output['savedOkMessage'] =
				'<h2>Error</h2>
				We were unable to save to the database at this time
				';
			}
		} else {
			$data->output['secondSidebar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
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