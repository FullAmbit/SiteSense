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

function admin_blogsBuild($data,$db) {
    if(!checkPermission('postAdd','blogs',$data)) {
        $data->output['abort']=true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
	//---Load Parent Blog (Anything Below Moderators Can Only Load Their OWN Blog---//
	if(!checkPermission('accessOthers','blogs',$data)) {
		$statement=$db->prepare('getBlogByIdAndOwner','admin_blogs');
		$statement->execute(array(
			':id' => $data->action[3],
			':owner' => $data->user['id']
		));
	} else {
		$statement=$db->prepare('getBlogById','admin_blogs');
		$statement->execute(array(
			':id' => $data->action[3]
		));
	}
	$data->output['parentBlog']=$statement->fetch();
	if($data->output['parentBlog']==FALSE) {
		$data->output['pagesError']=='unknown function';
		return;
	}
	// Load Form
	$data->output['blogForm']=new formHandler('editPosts',$data,true);
	// Get Blog Categories
	$statement=$db->prepare('getAllCategoriesByBlog','admin_blogs');
	$statement->execute(array(
		':blogId' => $data->action[3]
	));
	$data->output['categoryList']=$statement->fetchAll();
	$x=1;
	foreach($data->output['categoryList'] as $categoryItem) {
		$data->output['blogForm']->fields['categoryId']['options'][$x]['value']=$categoryItem['id'];
		$data->output['blogForm']->fields['categoryId']['options'][$x]['text']=$categoryItem['name'];
		$x++;
	}
	// Handle Post Request
	if(!empty($_POST['fromForm']) && ($_POST['fromForm']==$data->output['blogForm']->fromForm)) {
		$data->output['blogForm']->populateFromPostData();
		// Validate Form Data
		if($data->output['blogForm']->validateFromPost()) {
			// Check For Existing ShortName..
			$data->output['blogForm']->sendArray[':shortName']=$shortName=common_generateShortName($data->output['blogForm']->sendArray[':name']);
			// Check To See If ShortName Exists Anywhere (Across Any Language)
			if(common_checkUniqueValueAcrossLanguages($data,$db,'blog_posts','id',array('shortName'=>$shortName))){
				$data->output['blogForm']->fields['name']['error']=true;
		        $data->output['blogForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
	            return;
			}
		
			//--Various Parsing--//
			$data->output['blogForm']->sendArray[':title']=htmlspecialchars($data->output['blogForm']->sendArray[':title']);
			
			if($data->settings['useBBCode']=='1') {
				common_loadPlugin($data,'bbcode');
				$data->output['blogForm']->sendArray[':parsedContent']=$data->plugins['bbcode']->parse($data->output['blogForm']->sendArray[':rawContent']);
				$data->output['blogForm']->sendArray[':parsedSummary']=$data->plugins['bbcode']->parse($data->output['blogForm']->sendArray[':rawSummary']);
			} else {
				$data->output['blogForm']->sendArray[':parsedContent']=htmlspecialchars($data->output['blogForm']->sendArray[':rawContent']);
				$data->output['blogForm']->sendArray[':parsedSummary']=htmlspecialchars($data->output['blogForm']->sendArray[':rawSummary']);
			}
			$data->output['blogForm']->sendArray[':tags']=strtolower(str_replace(" ","",$data->output['blogForm']->sendArray[':tags']));
			$data->output['blogForm']->sendArray[':blogId']=$data->action[3];
			$data->output['blogForm']->sendArray[':user']=$data->user['id'];
			// --Save To DB--
			$statement=$db->prepare('insertBlogPost','admin_blogs');
			$result=$statement->execute($data->output['blogForm']->sendArray);
			if($result==FALSE) {
				$data->output['pagesError']=='db error';
				return;
			}
			// Now Replicate Across Other Languages
			common_populateLanguageTables($data,$db,'blog_posts','shortName',$data->output['blogForm']->sendArray[':shortName']);
			
			$aRoot=$data->linkRoot.'admin/blogs/';
			// Success !
			$data->output['savedOkMessage']='
				<h2>'.$data->phrases['blogs']['savePostSuccessHeading'].'</h2>
				<p>
					'.$data->phrases['blogs']['savePostSuccessMessage'].'
				</p>
				<div class="panel buttonList">
					<a href="'.$aRoot.'addPost/'.$data->action[3].'/">
						'.$data->phrases['blogs']['addNewPost'].'
					</a>
					<a href="'.$aRoot.'listPosts/'.$data->action[3].'">
						'.$data->phrases['blogs']['returnToPosts'].'
					</a>
				</div>';
			
		} else {
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
				</p>';
		}
	}
}

function admin_blogsShow($data) {
	if($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} elseif($data->output['pagesError']=='db error') {
		theme_databaseSaveError();
	} elseif(!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['blogForm']);
	}
}
?>