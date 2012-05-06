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
function admin_blogsCheckShortName($db,$shortName) {
	$statement=$db->prepare('getBlogIdByName','admin_blogs');
	$statement->execute(array(
		':shortName' => $shortName
	));
	if ($first=$statement->fetch()) {
		return $first['id'];
	} else return false;
}
function admin_blogsBuild($data,$db) {
	global $languageText;
	$aRoot=$data->linkRoot.'admin/blogs/';
	// Check If The Blog ID Is Supplied
	if(!is_numeric($data->action[3]))
	{
		$data->output['rejectError'] = 'Insufficient Parameters';
		$data->output['rejectText'] = 'Please provide a blog ID';
		return;
	}
	//---If You're a Blogger, You Can Only Load Your OWN Blog--//
	if(in_array('canEditBlog',$data->user['permissions']['blogs']))
	{
		$statement = $db->prepare('getBlogByIdAndOwner','admin_blogs');
		$statement->execute(array(
			':id' => $data->action[3],
			':owner' => $data->user['id']
		));
	} else {
		$statement = $db->prepare('getBlogById','admin_blogs');
		$statement->execute(array(':id' => $data->action[3]));
	}
	
	// Make Sure A Blog Was Found
	if(($blogItem = $data->output['blogItem'] = $statement->fetch()) == FALSE)
	{
		$data->output['rejectError'] = 'Invalid Parameters';
		$data->output['rejectText'] = 'The blog you requested could not be found.';
		return;
	}
				
	$data->output['blogForm']=new formHandler('blogsEdit',$data,true);
	$data->output['blogForm']->caption = 'Edit Blog';
	
	
	/*	Owner Permissions
	 *	The owner of the blog defaults to the "blogger" if the userLevel = USERLEVEL_BLOGGER (< USERLEVEL_MODERATOR)
	 *	If the user is >= USERLEVEL_MODERATOR, give a drop down list of blog owners
	**/
	if(in_array('canSeeBlogOwners',$data->user['permissions']['blogs']))
	{
		$data->output['blogForm']->fields['owner'] = array(
			'tag' => 'input',
			'params' => array(
				'type' => 'hidden'
			),
			'value' => $data->user['id']
		);
		
	} else {
		$statement = $db->query('getBloggersByUserLevel','admin_blogs');
		$statement->execute();
		while ($item=$statement->fetch()) {
			$data->output['blogForm']->fields['owner']['options'][]=array(
				'value' => $item['id'],
				'text' => $item['name'].' - '.$languageText['userLevels'][$item['userLevel']]
			);
		}
	}
	
	//--Fill out Form--//
	$item = $blogItem;
	foreach ($data->output['blogForm']->fields as $key => $value) {
		if (
			(!empty($value['params']['type'])) &&
			($value['params']['type']=='checkbox')
		) {
			$data->output['blogForm']->fields[$key]['checked']=(
				$item[$key] ? 'checked' : ''
			);
		} else {
			@$data->output['blogForm']->fields[$key]['value']=$item[$key];
		}
	}
	
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['blogForm']->fromForm))
	{
		/*
			we came from the form, so repopulate it and set up our
			sendArray at the same time.
		*/
		$data->output['blogForm']->populateFromPostData();
		
		$shortName = common_generateShortName($_POST[$data->output['blogForm']->formPrefix.'name']);
		$data->output['blogForm']->sendArray[':shortName'] = $shortName;
		// Only Run Unique Short Name Check If It's DIFFERENT
		if($shortName == $data->output['blogItem']['shortName'])
		{
			unset($data->output['blogForm']->fields['name']['cannotEqual']);
		} else {
			$statement = $db->prepare('getExistingBlogShortNames','admin_blogs');
			$statement->execute();
			$blogShortNameList = $statement->fetchAll();
			foreach($blogShortNameList as $item)
			{
				$cannotEqual[] = $item['shortName'];
			}
			$data->output['blogForm']->fields['name']['cannotEqual'] = $cannotEqual;
			// Apply ShortName Convention To Name For Use In Comparison //
			$_POST[$data->output['blogForm']->formPrefix.'name'] = $shortName;
		}
		//--Validate All The Form Information--//
		if ($data->output['blogForm']->validateFromPost($data)) {
			
			// Rename To New Shortname
			if($data->cdn)
			{
				// CDN Rename
				$data->cdn->renameFolder($data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/'.$blogItem['shortName'],$data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/'.$shortName);
			} else {
				// Local Rename
				if(is_dir($data->themeDir.'images/blogs/'.$blogItem['shortName']))
				{
					rename($data->themeDir.'images/blogs/'.$blogItem['shortName'],$data->themeDir.'images/blogs/'.$shortName);
				}
			}
			
			unset($data->output['blogForm']->sendArray[':picture']);
			
			// Save To DB
			$data->output['blogForm']->sendArray[':id'] = $data->output['blogItem']['id'];
			$statement=$db->prepare('updateBlogById','admin_blogs');
			$statement->execute($data->output['blogForm']->sendArray);
			
			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$aRoot.'add/">
						Add New Blog
					</a>
					<a href="'.$aRoot.'list/">
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
	if(isset($data->output['rejectError']))
	{
		echo '<h2>',$data->output['rejectError'],'</h2>',$data->output['rejectText'];
	} else if ($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} else if (!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		//var_dump($data->output['blogForm']->fields['picture']);
		theme_buildForm($data->output['blogForm']);
	}
}
?>