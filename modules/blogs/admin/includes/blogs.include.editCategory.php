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
    if(!checkPermission('categoryEdit','blogs',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
	if(checkPermission('accessOthers','blogs',$data)) {
		$check = $db->prepare('getBlogByIdAndOwner','blogs');
		$check->execute(array(
			':id' => $data->action[3],
			':owner' => $data->user['id']
		));
	} else {
		$check = $db->prepare('getBlogById','blogs');
		$check->execute(array(':id' => $data->action[3]));
	}
	// Check To See If The Blog Exists
	if(($data->output['blogItem'] = $check->fetch()) === FALSE)	{
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>The ID does not exist in database</h2>';
		return;
	}
	$data->output['categoryForm'] = new formHandler('blogCategory',$data,true);
	if(!empty($_POST['fromForm']) && ($_POST['fromForm'] == $data->output['categoryForm']->fromForm)) {
		$data->output['categoryForm']->populateFromPostData();
		if($data->output['categoryForm']->validateFromPost()) {
			// Get Short Name
			$data->output['categoryForm']->sendArray[':shortName'] = preg_replace('/\W-/i','',str_replace(' ','-',strtolower($_POST[$data->output['categoryForm']->formPrefix.'name'])));
			$data->output['categoryForm']->sendArray[':id'] = $data->output['categoryItem']['id'];
			$statement = $db->prepare('editCategory','blogs');
			$statement->execute($data->output['categoryForm']->sendArray) or die('Saving Category Item Failed');
			if(empty($data->output['secondSidebar'])) {
				$data->output['savedOkMessage']='
					<h2>Category Item Saved Successfully</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/blogs/addCategory/'.$data->output['blogItem']['id'].'">
							Add New Category
						</a>
						<a href="'.$data->linkRoot.'admin/blogs/listCategories/'.$data->output['blogItem']['id'].'">
							Return to Categories List
						</a>
					</div>';
			}
		} else {
			// Invalid Data
			$data->output['secondSidebar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}
}
function admin_blogsShow($data) {
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['categoryForm']);
	}
}
?>