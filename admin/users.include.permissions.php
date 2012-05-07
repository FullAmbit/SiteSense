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
function getPermissions($data,$db) {
    $targetFunction='loadPermissions';
    // Get core permissions
    common_include('libraries/common.php');
    if (function_exists($targetFunction)) {
        $targetFunction($data);
    }
    // Get enabled modules
    $statement=$db->query('getEnabledModules','admin_modules');
    $modules=$statement->fetchAll();
    foreach($modules as $module) {
        // Check to see if loadPermission() function exists
        $filename='modules/'.$module['name'].'.php';
        if(file_exists($filename)) {
            common_include($filename);
            if (function_exists($targetFunction)) {
                $targetFunction($data);
            }
        }
    }
    // Get enabled plugins
    $statement=$db->query('getEnabledPlugins','plugins');
    $plugins=$statement->fetchAll();
    foreach($plugins as $plugin) {
        $filename='plugins/'.$plugin['name'].'/plugin.php';
        if(file_exists($filename)) {
            common_include('plugins/'.$plugin['name'].'/plugin.php');
            if (function_exists($targetFunction)) {
                $targetFunction($data);
            }
        }
    }
}
function admin_usersBuild($data,$db) {
	if(empty($data->action[3])){ // Display List of Groups
        $statement=$db->query('getAllGroups','admin_users');
        $data->output['groupList']=$statement->fetchAll();
    } elseif($data->action[3]=='group') {
        if($data->action[4]=='add') { //Add a new Group
            getPermissions($data,$db);
            $data->output['permissionGroup']=new formHandler('permissionGroup',$data,true);
        } elseif($data->action[4]=='edit') { // Edit Group
            getPermissions($data,$db);
            // Get Group Permissions

            $statement=$db->prepare('getPermissionsByGroupName');
            $statement->execute(array(
                ':groupName' =>  $data->action[5]
            ));
            if(!$permissions=$statement->fetchAll(PDO::FETCH_ASSOC)) {
                $data->output['abort'] = true;
                $data->output['abortMessage'] = 'The group you specified could not be found';
                return;
            }
            foreach($permissions as $permission) {
                $data->output['permissionGroup']['permissions'][] = $permission['permissionName'];
            }
            if(isset($data->output['permissionGroup']['permissions'])) {
                // Organize array by module (Ex. $user['permissions']['blogs'])
                foreach($data->output['permissionGroup']['permissions'] as $key => $permission) {
                    unset($data->output['permissionGroup']['permissions'][$key]);
                    $separator=strpos($permission,'_');
                    $prefix=substr($permission,0,$separator);
                    $suffix=substr($permission,$separator+1);
                    $data->output['permissionGroup']['permissions'][$prefix][] = $suffix;
                }
                // Clean up
                //asort($user['permissions']);
            }
            $data->output['permissionGroup']=new formHandler('permissionGroup',$data,true);
            // Edit Group Form Submitted
            if ((!empty($_POST['fromForm']))&&($_POST['fromForm']==$data->output['userForm']->fromForm)) {
                $data->output['permissionGroup']->populateFromPostData();
                // Check if groupName exists already
                $existing=false;
                if($data->output['permissionGroup']->sendArray[':name']!==$item['name']) {
                    $existing=checkUserName($data->output['permissionGroup']->sendArray[':name'],$db);
                }
                if($existing) {
                    $data->output['secondSideBar']='
                      <h2>Error in Data</h2>
                      <p>
                          There were one or more errors. Please correct the fields with the red X next to them and try again.
                      </p><p>
                          <strong>That username is already taken!</strong>
                      </p>';
                    $data->output['userForm']->fields['name']['error']=true;
                    return;
                }
            }
        }
    }
}
function admin_usersShow($data) {
    if(empty($data->action[3])){ // Display List of Groups

        theme_GroupsListTableHead();
        foreach($data->output['groupList'] as $key => $group) {
            theme_GroupsListTableRow($group['groupName'],$data->linkRoot,$key);
        }
        theme_GroupsListTableFoot($data->linkRoot);
    } elseif($data->action[3]=='group') {
        if($data->action[4]=='add') { //Add a new Group
            theme_buildForm($data->output['permissionGroup']);
        } elseif($data->action[4]=='edit') { //Edit Group
            theme_buildForm($data->output['permissionGroup']);
        }
    }
}
?>