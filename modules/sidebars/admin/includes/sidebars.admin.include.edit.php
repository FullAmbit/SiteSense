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

function admin_sidebarsBuild($data,$db) {
    if(!checkPermission('edit','sidebars',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>'.$data->phrases['sidebars']['insufficientUserPermissions'].'</h2>'.$data->phrases['sidebars']['insufficientUserPermissions2'];
        return;
    }
	$aRoot=$data->linkRoot.'admin/sidebars/';

	// Check To See If Sidebar Exists
	$sidebarId = $data->action[3];

	$statement=$db->prepare('getById','admin_sidebars');
	$statement->execute(array(
		':id' => $sidebarId
	));
	$data->output['sidebarItem'] = $item = $statement->fetch();
	if($data->output['sidebarItem'] == FALSE)
	{
		$data->output['pagesError'] = $data->phrases['sidebars']['unknownFunction'];
		return;
	}
	// Load Form
	$data->output['sidebarForm']=new formHandler('sidebars',$data,true);
	$data->output['sidebarForm']->caption = $data->phrases['sidebars']['captionSidebarsEdit'];
	// Populate With Data
	foreach ($data->output['sidebarForm']->fields as $key => $value) {
		if (
			(!empty($value['params']['type'])) &&
			($value['params']['type']=='checkbox')
		) {
			$data->output['sidebarForm']->fields[$key]['checked']=(
				$item[$key] ? 'checked' : ''
			);
		} else {

			$data->output['sidebarForm']->fields[$key]['value']=$item[$key];
		}
	}

	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['sidebarForm']->fromForm))
	{
		$data->output['sidebarForm']->populateFromPostData();

		/**
		 * Set Up Short Name Check (ONLY if different from currently existing
		**/
		$data->output['sidebarForm']->sendArray[':shortName'] = $shortName = common_generateShortName($data->output['sidebarForm']->sendArray[':name']);
		unset($data->output['sidebarForm']->fields['name']['cannotEqual']);
		if($shortName !== $data->output['sidebarItem']['shortName'])
		{
			// Check To See If ShortName Exists Anywhere (Across Any Language)
			if(common_checkUniqueValueAcrossLanguages($data,$db,'sidebars','id',array('shortName'=>$shortName))){
				$data->output['sidebarForm']->fields['name']['error']=true;
	            $data->output['sidebarForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['sidebars']['uniqueNameConflict'].'</h2>'.$data->phrases['sidebars']['uniqueNameConflict2'];
	            return;
			}
		}

		if ($data->output['sidebarForm']->validateFromPost()) {

			//--Parsing--//
			if($data->settings['useBBCode'] == '1')
			{
				common_loadPlugin($data,'bbcode');
				$data->output['sidebarForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['sidebarForm']->sendArray[':rawContent']);
			} else {
				$data->output['sidebarForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['sidebarForm']->sendArray[':rawContent']);
			}

			// Save TO DB
			$statement=$db->prepare('updateById','admin_sidebars');
			$data->output['sidebarForm']->sendArray[':id'] = $data->action[3];
			$statement->execute($data->output['sidebarForm']->sendArray);

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
			// -- Push The Constant Fields Across Other Languages
			common_updateAcrossLanguageTables($data,$db,'sidebars',array('id'=>$data->action[3]),array(
				'side' => $data->output['sidebarForm']->sendArray[':side']
			));

		} else {
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['sidebars']['errorInData'].'</h2>
				<p>
					'.$data->phrases['sidebars']['errorInData2'].'
				</p>';
		}
	}
}
function admin_sidebarsShow($data) {
	if ($data->output['pagesError']==$data->phrases['sidebars']['unknownFunction']) {
		admin_unknown();
	} else if (!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['sidebarForm']);
	}
}
?>