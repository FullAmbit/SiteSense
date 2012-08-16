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
$this->formPrefix='permissionGroup_';
$this->caption=ucfirst($data->action[4]).'ing Group'.(
	empty($data->action[5]) ? '' : ': '.ucfirst($data->action[5])
);
$this->submitTitle=$data->phrases['users']['savePermissionsButton'];
$this->fromForm='permissionGroup';
$this->fields=array(
	'groupName' => array(
		'label'     => $data->phrases['users']['labelGroupName'],
		'required'  => true,
		'tag'       => 'input',
		'value'     => ($data->action[5]) ? $data->action[5] : '',
		'params' => array(
			'type'  => 'text',
			'size'  => 63,
		)
	)
);
foreach($data->permissions as $category => $permissions) {
    if(checkPermission('permissions',$category,$data)) {
    	$value = (!isset($data->output['permissionList'][$category]['permissions']['value'])) ? '0' : $data->output['permissionList'][$category]['permissions']['value'];

        $this->fields[$category.'_permissions']=array(
            'label'   => $data->phrases['users']['labelAddEditManagePermissions'],
            'tag'     => 'select',
            'group'   => ucfirst($category).' '.$data->phrases['users']['permissions'],
            'value' => $value,
            'options' => array(
	        	array(
	        		'value' => '1',
                	'text' => $data->phrases['users']['optionPermissionAllow']
	            ),
	            array(
	            	'value' => '0',
                	'text' => $data->phrases['users']['optionPermissionNeutral']
	            ),
	            array(
	            	'value' => '-1',
                	'text' => $data->phrases['users']['optionPermissionForbid']
	            )
		    )
        );
                    
        foreach($permissions as $permissionName => $permissionDescription) {
       	 	$value = (!isset($data->output['permissionList'][$category][$permissionName]['value'])) ? '0' : $data->output['permissionList'][$category][$permissionName]['value'];
            $this->fields[$category.'_'.$permissionName]=array(
	            'label'   => $permissionDescription,
	            'tag'     => 'select',
	            'group'   => ucfirst($category).' Permissions',
	            'value'   => $value,
	            'options' => array(
		        	array(
		        		'value' => '1',
                		'text' => $data->phrases['users']['optionPermissionAllow']
		            ),
		            array(
		            	'value' => '0',
		            	'text' => $data->phrases['users']['optionPermissionNeutral']
		            ),
		            array(
		            	'value' => '-1',
		            	'text' => $data->phrases['users']['optionPermissionForbid']
		            )
		        )
            );
        }
    }
}
?>