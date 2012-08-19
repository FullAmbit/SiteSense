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
    if(!checkPermission('list','urls',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage']='<h2>'.$data->phrases['core']['accessDeniedHeading'].'</h2>'.$data->phrases['core']['accessDeniedMessage'];
        return;
    }
    if($data->action[3]=='moveUp' || $data->action[3]=='moveDown') {
        admin_sortOrder_move($data,$db,'urls',$data->action[3],$data->action[4],'sortOrder',NULL,FALSE);
    }
    $data->output['messageListLimit']=ADMIN_SHOWPERPAGE;
	$messages = $db->query('getAllUrlRemaps','admin_urls');
	$data->output['remapList'] = $messages->fetchAll();
}
function admin_urlsShow($data) {
	theme_urlsListTableHead($data);
	$key = 0;
	foreach($data->output['remapList'] as $key => $remap) {
		theme_urlsListTableRow($remap,$data->linkRoot,$key);
	}
	$key++;
	theme_urlsListTableFoot($data);
}
?>	