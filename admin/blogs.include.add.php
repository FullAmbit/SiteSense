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

    if(!checkPermission('blogAdd','blogs',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    $data->output['blogForm'] = new formHandler('blogsEdit',$data,true);
    if(!checkPermission('accessOthers','blogs',$data))	{
		$data->output['blogForm']->fields['owner'] = array(
			'tag' => 'input',
			'params' => array(
				'type' => 'hidden'
			),
			'value' => $data->user['id']
		);

	} else {
		/*
		// Get all users with 'Blog access'
        // Start by purging expired groups
        $statement = $db->query('purgeExpiredGroups');
        $statement->execute();
        $statement = $db->prepare('getUserByPermissionNameGroupOnlyScope');
        $statement->execute(array(
            ':permissionName' => 'blogs_access'
        ));
        $usersFromGroupPermissionCheck=$statement->fetchAll();
        $statement = $db->prepare('getUserByPermissionNameUserOnlyScope');
        $statement->execute(array(
            ':permissionName' => 'blogs_access'
        ));
        $usersFromUserPermissionCheck=$statement->fetchAll();
        $usersWithBlogAccess = array();
        foreach($usersFromGroupPermissionCheck as $user){
            if(is_set($usersWithBlogAccess)) {
                // Run Checks
            } else {
                // Empty, add the first user
                $usersWithBlogAccess[] = $user['userID'];
            }
        }
		*/
        $statement = $db->query('getBloggersByUserLevel','admin_blogs');
		$statement->execute();
		while ($item=$statement->fetch()) {
			$data->output['blogForm']->fields['owner']['options'][]=array(
				'value' => $item['id'],
				'text' => $item['name']
			);
		}
	}

	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['blogForm']->fromForm))
	{
		$data->output['blogForm']->populateFromPostData();
		// Generate Short Name
		$shortName = common_generateShortName($_POST[$data->output['blogForm']->formPrefix.'name']);
		$data->output['blogForm']->sendArray[':shortName'] = $shortName;
		// We Need To Check And Make Sure This ShortName Isn't Taken
		$statement = $db->prepare('getExistingBlogShortNames','admin_blogs');
		$statement->execute();
		$blogShortNameList = $statement->fetchAll();
		$cannotEqual = array();
		foreach($blogShortNameList as $item)
		{
			$cannotEqual[] = $item['shortName'];
		}
		$data->output['blogForm']->fields['name']['cannotEqual'] = $cannotEqual;
		// Apply ShortName Convention To Name For Use In Comparison //
		$_POST[$data->output['blogForm']->formPrefix.'name'] = $shortName;

		// Validate Form
		if($data->output['blogForm']->validateFromPost($data))
		{
			// "Picture" Is Not Used In The Query
			unset($data->output['blogForm']->sendArray[':picture']);
			// Save To Database
			$statement=$db->prepare('insertBlog','admin_blogs');
			$statement->execute($data->output['blogForm']->sendArray);
			// Rename The TMP Folder To The Name Of The Blog Folder
			if($data->cdn)
			{
				$data->cdn->renameFolder($data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/tmp',$data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/'.$shortName);
			} else {
				if(is_dir($data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/tmp'))
				{
					rename($data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/tmp',$data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/'.$shortName);
				}
			}

			$aRoot = $data->linkRoot . 'admin/blogs/';

			$data->output['savedOkMessage']='
				<h2>Values Saved Successfully</h2>
				<p>
					Auto generated short name was: '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$aRoot.'add">
						Add Another Blog
					</a>
					<a href="'.$aRoot.'list/">
						Return to Blog List
					</a>
				</div>';

		} else {
			// Form Validation Fail
			$data->output['secondSideBar']='
				<h2>Error in Data</h2>
				<p>
					There were one or more errors. Please correct the fields with the red X next to them and try again.
				</p>';
		}
	}

}

function admin_blogsShow($data)
{
	if(isset($data->output['savedOkMessage']))
	{
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['blogForm']);
	}
}
?>