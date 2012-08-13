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
	//---If You're a Blogger, You Can Only Load Your OWN Blog--//
    if(!checkPermission('categoryAdd','blogs',$data)) {
        $data->output['abort']=true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    if(!checkPermission('accessOthers','blogs',$data)) {
		$check=$db->prepare('getBlogByIdAndOwner','admin_blogs');
		$check->execute(array(
			':id' => $data->action[3],
			':owner' => $data->user['id']
		));
	} else {
		$check=$db->prepare('getBlogById','admin_blogs');
		$check->execute(array(':id' => $data->action[3]));
	}
	if(($data->output['blogItem']=$check->fetch())===FALSE) {
		$data->output['abort']=true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}
	$data->output['categoryForm']=new formHandler('category',$data,true);
	if(!empty($_POST['fromForm']) && ($_POST['fromForm']==$data->output['categoryForm']->fromForm)) {
		$data->output['categoryForm']->populateFromPostData();
		if($data->output['categoryForm']->validateFromPost()) {
			// Get Short Name
			$data->output['categoryForm']->sendArray[':shortName']=$shortName=common_generateShortName($_POST[$data->output['categoryForm']->formPrefix.'name']);
			// Check To See If ShortName Exists Anywhere (Across Any Language)
			if(common_checkUniqueValueAcrossLanguages($data,$db,'blog_categories','id',array('shortName'=>$shortName))){
				$data->output['categoryForm']->fields['name']['error']=true;
		            $data->output['categoryForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
	            return;
			}
			$data->output['categoryForm']->sendArray[':blogId']=$data->output['blogItem']['id'];
			$statement=$db->prepare('addCategory','admin_blogs');
			$statement->execute($data->output['categoryForm']->sendArray) or die('Saving Category Item Failed');
			// Now Replicate Across Other Languages
			common_populateLanguageTables($data,$db,'blog_categories','shortName',$data->output['categoryForm']->sendArray[':shortName']);
			
			if(empty($data->output['secondSidebar'])) {
				$data->output['savedOkMessage']='
					<h2>'.$data->phrases['blogs']['saveCategorySuccessHeading'].'</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/blogs/addCategory/'.$data->output['blogItem']['id'].'">
							'.$data->phrases['blogs']['addCategory'].'
						</a>
						<a href="'.$data->linkRoot.'admin/blogs/listCategories/'.$data->output['blogItem']['id'].'">
							'.$data->phrases['blogs']['returnToCategories'].'
						</a>
					</div>';
			}
		} else {
			// Invalid Data
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
				</p>';
		}
	}
}

function admin_blogsShow($data) {
	if(isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['categoryForm']);
	}
}
?>