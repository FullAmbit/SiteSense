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
function admin_sidebarsBuild($data,$db) {
    if(!checkPermission('delete','sidebars',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    $data->output['delete']='';
	if (empty($data->action[3]) || !is_numeric($data->action[3])) {
		$data->output['rejectError']='insufficient parameters';
		$data->output['rejectText']='No ID # was entered to be deleted';
	} else {
		$qHandle=$db->prepare('getFromFileById','admin_sidebars');
		$qHandle->execute(array(
			':id' => $data->action[3]
		));
		if ($item=$qHandle->fetch()) {
			if ($item['fromFile']) {
				$data->output['rejectError']='Locked Sidebar Element';
				$data->output['rejectText']='That sidebar element cannot be deleted from the admin panel. Either disable it, or delete it\'s associated module files.';
			} else if (checkPermission('canDeleteSidebarItem','core',$data)) {
				if (isset($_POST['fromForm']) && $_POST['fromForm']==$data->action[3]) {
					if (!empty($_POST['delete'])) {
						// Delete Across All Languages
						common_deleteFromLanguageTables($data,$db,'sidebars','id',$data->action[3],TRUE);

						//--Delete Form, Page, and Module Setting For Sidebar--//
						$vars = array(':sidebar' => $data->action[3]);
						
						$q1 = $db->prepare('deleteSidebarSettingBySidebar','admin_dynamicForms');
						$q2 = $db->prepare('deleteSidebarSettingBySidebar','admin_modules');
						$q3 = $db->prepare('deleteSidebarSettingBySidebar','admin_pages');
						
						$q1->execute($vars);
						$q2->execute($vars);
						$q3->execute($vars);
						
						if ($q1 || $q2 | $q3) {
							$data->output['delete']='deleted';
						} else {
							$data->output['rejectError']='Database Error';
							$data->output['rejectText']='You attempted to delete a record, are you sure that record existed?';
						}
					} else {
						/* from form plus not deleted must == cancelled. */
						$data->output['delete']='cancelled';
					}
				}
			} else {
				$data->output['rejectError']='Insufficient User Permissions';
				$data->output['rejectText']='You do not have sufficient access to perform this action.';
			}
		}
	}
}
function admin_sidebarsShow($data) {
	$aRoot=$data->linkRoot.'admin/sidebars/';
	if (empty($data->output['rejectError'])) {
		switch ($data->output['delete']) {
			case 'deleted':
				theme_sidebarsDeleteDeleted($data,$aRoot);
			break;
			case 'cancelled':
				theme_sidebarsDeleteCancelled($aRoot);
			break;
			default:
				theme_sidebarsDeleteDefault($data,$aRoot);
			break;
		}
	} else {
		theme_rejectError($data);
	}
}
?>