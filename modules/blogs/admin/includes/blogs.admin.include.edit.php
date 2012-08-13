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
	if($first=$statement->fetch()) {
		return $first['id'];
	} else {
        return false;
    }
}
function admin_blogsBuild($data,$db) {
    if(!checkPermission('blogEdit','blogs',$data)) {
        $data->output['abort']=true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    global $languageText;
	$aRoot=$data->linkRoot.'admin/blogs/';
	// Check If The Blog ID Is Supplied
	if(!is_numeric($data->action[3])) {
		$data->output['abort']=true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}
	//---If You're a Blogger, You Can Only Load Your OWN Blog--
	if(!checkPermission('accessOthers','blogs',$data)) {
		$statement=$db->prepare('getBlogByIdAndOwner','admin_blogs');
		$statement->execute(array(
			':id' => $data->action[3],
			':owner' => $data->user['id']
		));
	} else {
		$statement=$db->prepare('getBlogById','admin_blogs');
		$statement->execute(array(':id' => $data->action[3]));
	}
	// Make Sure A Blog Was Found
	if(($blogItem=$data->output['blogItem']=$statement->fetch())===FALSE) {
		$data->output['abort']=true;
		$data->output['abortMessage']='<h2>'.$data->phrases['core']['invalidID'].'</h2>';
		return;
	}
	$data->output['blogForm']=new formHandler('edit',$data,true);
	$data->output['blogForm']->caption='Edit Blog';
	// Check Permission
	if(checkPermission('ownerView','blogs',$data)) {
		$data->output['blogForm']->fields['owner']=array(
			'tag' => 'input',
			'params' => array(
				'type' => 'hidden'
			),
			'value' => $data->user['id']
		);
	}
	// --Fill out Form--
	$item=$blogItem;
	foreach($data->output['blogForm']->fields as $key => $value) {
		if(
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
	if((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$data->output['blogForm']->fromForm)) {
		$data->output['blogForm']->populateFromPostData();
		$shortName=common_generateShortName($_POST[$data->output['blogForm']->formPrefix.'name']);
		$data->output['blogForm']->sendArray[':shortName']=$shortName;
		// Only Run Unique Short Name Check If It's DIFFERENT
		if($shortName==$data->output['blogItem']['shortName']) {
			unset($data->output['blogForm']->fields['name']['cannotEqual']);
			$newShortName=false;
		} else {
			// Check To See If ShortName Exists Anywhere (Across Any Language)
			if(common_checkUniqueValueAcrossLanguages($data,$db,'blogs','id',array('shortName'=>$shortName))){
				$data->output['blogForm']->fields['name']['error']=true;
		        $data->output['blogForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
	            return;
			}
			
			$newShortName=true;
		}
		//--Validate All The Form Information--//
		if($data->output['blogForm']->validateFromPost($data)) {
            if(intval($data->output['blogForm']->sendArray[':topLevel'])!=intval($data->output['blogItem']['topLevel'])) {
                switch($data->output['blogForm']->sendArray[':topLevel']) {
                    case 0:
                        $statement=$db->prepare('deleteReplacementByMatch','admin_dynamicURLs');
                        $statement->execute(array(
                          ':match' => '^'.$data->output['blogItem']['shortName'].'(/.*)?$'
                        ));
                        break;
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
                                ':replace'   => 'blogs/'.$shortName.'\1',
                                ':sortOrder' => admin_sortOrder_new($data,$db,'urls','sortOrder','hostname',''),
                                ':regex'     => 0,
                                ':hostname' => '',
                                ':isRedirect' => 0
                            ));
                        } else {
                            $data->output['blogForm']->fields['name']['error']=true;
                            $data->output['blogForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
                            return;
                        }
                        break;
                }
            } elseif($newShortName) {
                $modifiedShortName='^'.$shortName.'(/.*)?$';
                $statement=$db->prepare('getUrlRemapByMatch','admin_dynamicURLs');
                $statement->execute(array(
                        ':match' => $modifiedShortName,
                        ':hostname' => ''
                    )
                );
                $result=$statement->fetch();
                if($result===false) {
                    $statement=$db->prepare('updateUrlRemapByMatch','admin_dynamicURLs');
                    $statement->execute(array(
                        ':match'    => '^'.$data->output['blogItem']['shortName'].'(/.*)?$',
                        ':newMatch' => '^'.$shortName.'(/.*)?$',
                        ':replace'  => 'blogs/'.$shortName.'\1'
                    ));
                } else {
                    $data->output['blogForm']->fields['name']['error']=true;
                    $data->output['blogForm']->fields['name']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
                    return;
                }
            }
			// Rename To New Shortname
			if($data->cdn) {
				// CDN Rename
				$data->cdn->renameFolder($data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/'.$blogItem['shortName'],$data->settings['cdnBaseDir'].$data->themeDir.'images/blogs/'.$shortName);
			} else {
				// Local Rename
				if(is_dir($data->themeDir.'images/blogs/'.$blogItem['shortName'])) {
					rename($data->themeDir.'images/blogs/'.$blogItem['shortName'],$data->themeDir.'images/blogs/'.$shortName);
				}
			}
			unset($data->output['blogForm']->sendArray[':picture']);
			// Save To DB
			$data->output['blogForm']->sendArray[':id']=$data->output['blogItem']['id'];
			$statement=$db->prepare('updateBlogById','admin_blogs');
			$statement->execute($data->output['blogForm']->sendArray);
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
			$data->output['secondSidebar']='
				<h2>'.$data->phrases['core']['formValidationErrorHeading'].'</h2>
				<p>
					'.$data->phrases['core']['formValidationErrorMessage'].'
				</p>';
		}
	}
}
function admin_blogsShow($data) {
	if(isset($data->output['rejectError'])) {
		echo '<h2>',$data->output['rejectError'],'</h2>',$data->output['rejectText'];
	} elseif($data->output['pagesError']=='unknown function') {
		admin_unknown();
	} elseif(!empty($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['blogForm']);
	}
}
?>