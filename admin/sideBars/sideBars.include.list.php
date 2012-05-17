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
function admin_sideBarsBuild($data,$db) {
    if(!checkPermission('sidebars_list','core',$data)) {
        $data->output['abort'] = true;
        $data->output['abortMessage'] = '<h2>Insufficient User Permissions</h2>You do not have the permissions to access this area.';
        return;
    }
    if (is_numeric($data->action[4])) {
		/*if (
			($data->action[3]=='moveUp') ||
			($data->action[3]=='moveDown')
		) {
			
			$qHandle=$db->prepare('getSortOrderById','admin_sideBars');
			$qHandle->execute(array(
				':id' => $data->action[4]
			));
			if ($item=$qHandle->fetch()) {
				$rowCount = intval($db->countRows('sidebars'));
				  if($data->action[3] == 'moveUp' && intval($item['sortOrder']) > 1) {
					  $query1 = 'shiftOrderUpRelative';
					  $query2 = 'shiftOrderUpByID';
					 // echo "UP";
				  } else if($data->action[3] == 'moveDown' && intval($item['sortOrder']) < $rowCount) {
					  $query1 = 'shiftOrderDownRelative';
					  $query2 = 'shiftOrderDownByID';
					  //echo "DOWN";
				  }
				  if(isset($query1))
				  {
					 
					  $statement = $db->prepare($query1,'admin_sideBars');
					  $statement->execute(array(
						  ':sortOrder' => $item['sortOrder']
					  ));
					  $statement = $db->prepare($query2,'admin_sideBars');
					  $statement->execute(array(
						  ':id' => $item['id']
					  ));
				  }
			}
		} else if (
			($data->action[3]=='enable') ||
			($data->action[3]=='disable')
		) {
			$qHandle=$db->prepare('updateEnabledById','admin_sideBars');
			$qHandle->execute(array(
				':enabled' => ($data->action[3]=='enable'),
				':id' => $data->action[4]
			));
		} else */
		if($data->action[3] == 'switch')
		{
			if (is_numeric($data->action[4])) {
				$statement=$db->prepare('getById','admin_sideBars');
				$statement->execute(array(
					':id' => $data->action[4]
				));
				if ($item=$statement->fetch()) {
					$statement=$db->prepare('updateSideById','admin_sideBars');
					$statement->execute(array(
						':side' => ( $item['side']=='left' ? 'right' : 'left' ),
						':id' => $item['id']
					));
				}
			}
		}
	}
	$qHandle=$db->prepare('getAllOrdered','admin_sideBars');
	$qHandle->execute();
	$data->output['sideBars']=$qHandle->fetchAll();
}
function admin_sideBarsShow($data) {
	$aRoot=$data->linkRoot.'admin/sideBars/';
	theme_sideBarsListAddNewButton($aRoot);
	if (empty($data->output['sideBars'])) {
		theme_sideBarsListNoSidebars();
	} else {
		theme_sideBarsListTableHead();
		$count=0;
		foreach ($data->output['sideBars'] as $item) {
			$titleStartTag='';
			$titleEndTag='';
			if ($item['enabled']) {
				$titleStartTag.='<b>';
				$titleEndTag='</b>'.$titleEndTag;
			}
			if ($item['fromFile']) {
				$titleStartTag.='/sideBars/';
				$titleEndTag='.sideBar.php'.$titleEndTag;
			} else {
				$titleStartTag.='<a href="'.$aRoot.'edit/'.$item['id'].'">';
				$titleEndTag='</a>'.$titleEndTag;
			}
			theme_sideBarsListTableRow($item,$aRoot,$titleStartTag,$titleEndTag,$count);
			$count++;
		}
	theme_sideBarsListTableFoot();
	}
	theme_sideBarsListAddNewButton($aRoot);
}
?>
