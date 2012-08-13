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
function admin_urlsBuild($data,$db) {
    if(!checkPermission('delete','urls',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    $staff=false;
	if (empty($data->action[3])) {
		$data->output['abort'] = true;
		$data->output['abortremap'] = '<h2>No ID Given</h2>';
	}else{
		$remap = $db->prepare('getUrlRemapById','admin_urls');
		$remap->execute(array(':id' => (int)$data->action[3]));
		$remap = $remap->fetch();
		$data->output['exists'] = $remap !== false;
		if($data->action[4] == 'confirm'){
			$remaps = $db->prepare('deleteUrlRemap','admin_urls');
			$remaps->execute(array(':id' => (int)$data->action[3]));
			$data->output['success'] = ($remaps->rowCount() == 1);
		}
	}
}
function admin_urlsShow($data) {
	if(isset($data->output['success'])){
		if($data->output['success']){
			theme_urlsDeleteSuccess($data->linkRoot);
		}else{
			theme_urlsDeleteError($data->output['exists'],$data->linkRoot);
		}
	}else{
		theme_urlsDeleteConfirm($data->action[3],$data->linkRoot);
	}
}
?>
