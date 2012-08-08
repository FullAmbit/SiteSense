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
        $data->output['abort']=true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    $data->output['blogForm']=new formHandler('edit',$data,true);
    if(!checkPermission('accessOthers','blogs',$data))	{
		$data->output['blogForm']->fields['owner']=array(
			'tag' => 'input',
			'params' => array(
				'type' => 'hidden'
			),
			'value' => $data->user['id']
		);
	}
	if((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['blogForm']->fromForm)) {
		$data->output['blogForm']->populateFromPostData();
		// Generate Short Name
		$shortName=common_generateShortName($_POST[$data->output['blogForm']->formPrefix.'name']);
		$data->output['blogForm']->sendArray[':shortName']=$shortName;
		// Check To See If ShortName Exists Anywhere (Across Any Language)
		if(common_checkUniqueValueAcrossLanguages($data,$db,'blogs','id',array('shortName'=>$shortName))){
			$data->output['blogForm']->fields['name']['error']=true;
		    $data->output['blogForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
            return;
		}
		// Validate Form
		if($data->output['blogForm']->validateFromPost($data)) {
		    if($data->output['blogForm']->sendArray[':topLevel']==1) {
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
                        ':replace'   => 'blogs/'.$shortName.'\1',
                        ':sortOrder' => admin_sortOrder_new($data,$db,'url_remap','sortOrder'),
                        ':regex'     => 0,
                        ':hostname' => ''
                    ));
                } else {
                    $data->output['blogForm']->fields['name']['error']=true;
                    $data->output['blogForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
                    return;
                }
            }
			// "Picture" Is Not Used In The Query
			unset($data->output['blogForm']->sendArray[':picture']);
			// Save To Database (For Current Language)
			$statement=$db->prepare('insertBlog','admin_blogs');
			$statement->execute($data->output['blogForm']->sendArray);
			// Now Replicate Across Other Languages
			common_populateLanguageTables($data,$db,'blogs','shortName',$data->output['blogForm']->sendArray[':shortName']);
			// Rename The TMP Folder To The Name Of The Blog Folder
			if($data->cdn) {
				$data->cdn->renameFolder($data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/tmp',$data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/'.$shortName);
			} else {
				if(is_dir($data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/tmp')) {
					rename($data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/tmp',$data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/'.$shortName);
				}
			}
			$aRoot=$data->linkRoot . 'admin/blogs/';
			$data->output['savedOkMessage']='
				<h2>'.$data->phrases['blogs']['saveBlogSuccessHeading'].'</h2>
				<p>
					'.$data->phrases['blogs']['saveBlogSuccessMessage'].' - '.$shortName.'
				</p>
				<div class="panel buttonList">
					<a href="'.$aRoot.'add/">
						'.$data->phrases['blogs']['addNewBlog'].'
					</a>
					<a href="'.$aRoot.'list/">
						'.$data->phrases['blogs']['returnToBlogs'].'
					</a>
				</div>';

		} else {
			// Form Validation Fail
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
        theme_buildForm($data->output['blogForm']);
    }
}
?>