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
function admin_formsBuild($data,$db){
	//permission check for forms access
	if(!checkPermission('access','dynamicForms',$data)) {
		$data->output['abort'] = true;
		$data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';	
		return;
	}	
	$statement = $db->query('getAllForms', 'dynamicForms');
	$data->output['forms'] = $statement->fetchAll();
}
function admin_formsShow($data){
	theme_formsListNewButton($data);
	if (empty($data->output['forms'])) {
		theme_formsListNoForms();
	} else {
		theme_formsListTableHead();
	$count=0;
	foreach($data->output['forms'] as $form){
		theme_formsListTableRow($data,$form,$count);
		$count++;
	}
	theme_formsListTableFoot();
  }
}
