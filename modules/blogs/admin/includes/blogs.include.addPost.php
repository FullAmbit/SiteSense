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

function admin_blogsBuild($data,$db)
{
    if(!checkPermission('postAdd','blogs',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
	//---Load Parent Blog (Anything Below Moderators Can Only Load Their OWN Blog---//
	if(!checkPermission('accessOthers','blogs',$data)) {
		$statement = $db->prepare('getBlogByIdAndOwner','blogs');
		$statement->execute(array(
			':id' => $data->action[3],
			':owner' => $data->user['id']
		));
	} else {
		$statement=$db->prepare('getBlogById','blogs');
		$statement->execute(array(
			':id' => $data->action[3]
		));
	}
	$data->output['parentBlog'] = $statement->fetch();
	if($data->output['parentBlog'] == FALSE)
	{
		$data->output['pagesError']=='unknown function';
		return;
	}
	// Load Form //		
	$data->output['blogForm']=new formHandler('blogsEditPosts',$data,true);
	// Get Blog Categories //
	$statement = $db->prepare('getAllCategoriesByBlog','blogs');
	$statement->execute(array(
		':blogId' => $data->action[3]
	));
	$data->output['categoryList']=$statement->fetchAll();
	$x=1;
	foreach($data->output['categoryList'] as $categoryItem) {
		$data->output['blogForm']->fields['categoryId']['options'][$x]['value'] = $categoryItem['id'];
		$data->output['blogForm']->fields['categoryId']['options'][$x]['text'] = $categoryItem['name'];
		$x++;
	}
	// Handle Post Request
	if(!empty($_POST['fromForm']) && ($_POST['fromForm']==$data->output['blogForm']->fromForm))
	{
		$data->output['blogForm']->populateFromPostData();
		/**
		 * Set up Short Name Check
		**/
		$shortName = common_generateShortName($_POST[$data->output['blogForm']->formPrefix.'name']);
		// Since we're comparing the name field against shortName, set the name value equal to the new shortName for comparison
		$data->output['blogForm']->sendArray[':shortName'] = $_POST[$data->output['blogForm']->formPrefix.'name'] = $shortName;
		// Load All Existing SideBar ShortNames For Comparison
		$statement = $db->prepare('getExistingShortNames','blogs');
		$statement->execute();
		$postList = $statement->fetchAll();
		$existingShortNames = array();
		foreach($postList as $postItem)
		{
			$existingShortNames[] = $postItem['shortName'];
		}
		$data->output['blogForm']->fields['name']['cannotEqual'] = $existingShortNames;
		/*----------------*/
		if($data->output['blogForm']->validateFromPost())
		{
			//--Various Parsing--//
			$data->output['blogForm']->sendArray[':title'] = htmlspecialchars($data->output['blogForm']->sendArray[':title']);
			
			if($data->settings['useBBCode'] == '1')
			{
				common_loadPlugin($data,'bbcode');
				
				$data->output['blogForm']->sendArray[':parsedContent'] = $data->plugins['bbcode']->parse($data->output['blogForm']->sendArray[':rawContent']);
				$data->output['blogForm']->sendArray[':parsedSummary'] = $data->plugins['bbcode']->parse($data->output['blogForm']->sendArray[':rawSummary']);
			} else {
				$data->output['blogForm']->sendArray[':parsedContent'] = htmlspecialchars($data->output['blogForm']->sendArray[':rawContent']);
				$data->output['blogForm']->sendArray[':parsedSummary'] = htmlspecialchars($data->output['blogForm']->sendArray[':rawContent']);
			}
			$data->output['blogForm']->sendArray[':tags'] = strtolower(str_replace(" ","",$data->output['blogForm']->sendArray[':tags']));
			$data->output['blogForm']->sendArray[':blogId']=$data->action[3];
			$data->output['blogForm']->sendArray[':user']=$data->user['id'];
			//--Save To DB--//
			$statement=$db->prepare('insertBlogPost','blogs');
			$result = $statement->execute($data->output['blogForm']->sendArray);
			
			if($result == FALSE)
			{
				$data->output['pagesError']=='db error';
				return;
			}
			$aRoot = $data->linkRoot.'admin/blogs/';
			// Success ! //
			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$aRoot.'addPost/'.$data->action[3].'/">
						Add New Post to "'.$data->output['parentBlog']['name'].'"
					</a>
					<a href="'.$aRoot.'listPosts/'.$data->action[3].'">
						Return to Page List
					</a>
				</div>';
			
		} else {
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}

function admin_blogsShow($data) {
	if ($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} else if($data->output['pagesError'] == 'db error')
	{
		theme_databaseSaveError();
	} else if (!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['blogForm']);
	}
}

?>