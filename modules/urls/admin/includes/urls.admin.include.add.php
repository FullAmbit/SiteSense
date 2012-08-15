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
function admin_urlsBuild($data,$db) {
    if(!checkPermission('add','urls',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    // Load Hostnames
    $statement = $db->prepare('getAllHostnames','admin_hostnames');
    $statement->execute();
    $data->output['hostnameList'] = $statement->fetchAll(PDO::FETCH_ASSOC);
    $form = $data->output['remapForm'] = new formHandler('addEdit',$data,true);
	
	if ((!empty($_POST['fromForm'])) && ($_POST['fromForm']==$form->fromForm)) {
		// Populate The Send Array
		$form->populateFromPostData();
		if ($form->validateFromPost()) {
			// Check Hostname
			if(!isset($form->sendArray[':hostname'])) $form->sendArray[':hostname'] = '';
			
            if(!$form->sendArray[':regex']) {
                // Standard
                $form->sendArray[':match']=str_replace('^','',$form->sendArray[':match']);
                $form->sendArray[':match']=str_replace('(/.*)?$','',$form->sendArray[':match']);
                $form->sendArray[':replace']=str_replace('\1','',$form->sendArray[':replace']);
                // Trim Forward Slashes + Whitespace from Beginning and End
                $form->sendArray[':match']=trim($form->sendArray[':match']);
                $form->sendArray[':replace']=trim($form->sendArray[':replace']);
                $form->sendArray[':match']=trim($form->sendArray[':match'],'/');
                $form->sendArray[':replace']=trim($form->sendArray[':replace'],'/');
                // Add Regex
                $form->sendArray[':match']='^'.$form->sendArray[':match'].'(/.*)?$';
                $form->sendArray[':replace']=$form->sendArray[':replace'].'\1';

                $modifiedMatch=$form->sendArray[':match'];
                $statement=$db->prepare('getUrlRemapByMatch','admin_urls');
                $statement->execute(array(
                        ':match' => $modifiedMatch,
                        ':hostname' => $form->sendArray[':hostname']
                    )
                );
                $result=$statement->fetch();
                if($result===false) {
                    $statement=$db->prepare('insertUrlRemap','admin_urls');
                    $statement->execute(array(
                        ':match'     => $modifiedMatch,
                        ':replace'   => $form->sendArray[':replace'],
                        ':sortOrder' => admin_sortOrder_new($data,$db,'urls','sortOrder','hostname',$form->sendArray[':hostname']),
                        ':regex'     => 0,
                        ':hostname' => $form->sendArray[':hostname'],
                        ':isRedirect' => $form->sendArray[':isRedirect']
                    ));
                } else {
                    $data->output['remapForm']->fields['match']['error']=true;
                    $data->output['remapForm']->fields['match']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
                    return;
                }
            } else {
                $form->sendArray[':sortOrder']=admin_sortOrder_new($data,$db,'urls','sortOrder');
                $statement = $db->prepare('insertUrlRemap','admin_urls','sortOrder','hostname',$form->sendArray[':hostname']);
                $result = $statement->execute($form->sendArray);
                if($result == FALSE) {
                    $data->output['remapForm']->fields['match']['error']=true;
                    $data->output['remapForm']->fields['match']['errorList'][]='<h2>'.$data->phrases['core']['uniqueNameConflictHeading'].'</h2>'.$data->phrases['core']['uniqueNameConflictMessage'];
                    return;
                }
            }
			if (empty($data->output['secondSidebar'])) {
				$data->output['savedOkMessage']='
					<h2>'.$data->phrases['urls']['saveRemapSuccess'].'</h2>
					<div class="panel buttonList">
						<a href="'.$data->linkRoot.'admin/urls/add">
							'.$data->phrases['urls']['addRemap'].'
						</a>
						<a href="'.$data->linkRoot.'admin/urls/list/">
							'.$data->phrases['urls']['returnToList'].'
						</a>
					</div>';
			}
			
		}
	}
}

function admin_urlsShow($data)
{
	if (isset($data->output['savedOkMessage'])) {
		echo $data->output['savedOkMessage'];
	} else {
		theme_buildForm($data->output['remapForm']);
	}
}
?>