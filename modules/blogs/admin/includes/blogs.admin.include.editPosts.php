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
function admin_blogPostsCheckShortName($db,$shortName) {
	$statement=$db->prepare('getBlogPostIdByName','admin_blogs');
	$statement->execute(array(
		':shortName' => $shortName
	));
	if($first=$statement->fetch()) {
		return $first['id'];
	} else {
        return false;
    }
}
function admin_blogsBuild($data,$db) {
    if(!checkPermission('postEdit','blogs',$data)) {
        $data->output['abort']=true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    global $languageText;
	$aRoot=$data->linkRoot.'admin/blogs/';
	if(is_numeric($data->action[3])) {
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
		if(($data->output['parentBlog']=$statement->fetch())==FALSE) {
        	$data->output['abort']=true;
			$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
			return;
		}
		//---Load Blog Post---
		$statement=$db->prepare('getBlogPostsById','admin_blogs');
		$statement->execute(array(
			'id' => $data->action[4]
		));
		if(($data->output['blogItem']=$statement->fetch())==FALSE){
			$data->output['abort']=true;
			$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
			return;
		}
		$item = $data->output['blogItem'];
		// Load Form
		$data->output['blogForm']=new formHandler('editPosts',$data,true);
		$data->output['blogForm']->caption=$data->phrases['blogs']['captionEditPost'];
		//--Fill Up Fields--
		foreach($data->output['blogForm']->fields as $key => $value) {
			if(
				(!empty($value['params']['type'])) &&
				($value['params']['type']=='checkbox')
			) {
				$data->output['blogForm']->fields[$key]['checked']=(
					$item[$key] ? 'checked' : ''
				);
			} else {
				$data->output['blogForm']->fields[$key]['value']=html_entity_decode($item[$key]);
			}
		}
	} else {
		$data->output['pagesError']='unknown function';
		return;
	}
	// Get Blog Categories //
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
	if((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['blogForm']->fromForm)) {
		$data->output['blogForm']->populateFromPostData();
		$shortName=common_generateShortName($_POST[$data->output['blogForm']->formPrefix.'name']);
		$data->output['blogForm']->sendArray[':shortName']=$shortName;
		// Only Run Unique Short Name Check If It's DIFFERENT
		if($shortName==$data->output['blogItem']['shortName']) {
			unset($data->output['blogForm']->fields['name']['cannotEqual']);
		} else {
			// Check To See If ShortName Exists Anywhere (Across Any Language)
			if(common_checkUniqueValueAcrossLanguages($data,$db,'blog_posts','id',array('shortName'=>$shortName))){
				$data->output['blogForm']->fields['name']['error']=true;
		        $data->output['blogForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
	            return;
			}
		}
		// ---Validate All Form Fields---
		if($data->output['blogForm']->validateFromPost()) {
			$statement=$db->prepare('updateBlogPostsById','admin_blogs');
			$data->output['blogForm']->sendArray[':id']=$data->action[4];
			// HTML Special Chars
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
			// ---Save To DB---
			$statement->execute($data->output['blogForm']->sendArray);
			// -- Push The Constant Fields Across Other Languages
			common_updateAcrossLanguageTables($data,$db,'blog_posts',array('id'=>$data->action[4]),array(
				'categoryId' => $data->output['blogForm']->sendArray[':categoryId'],
				'allowComments' => $data->output['blogForm']->sendArray[':allowComments'],
				'live' => $data->output['blogForm']->sendArray[':live']
			));
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
	} elseif(!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['blogForm']);
	}
}
?>