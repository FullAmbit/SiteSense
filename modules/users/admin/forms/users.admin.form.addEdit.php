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
$this->formPrefix='viewUser_';
$this->caption='Editing User: '.(
	empty($data->output['viewUser']) ? '' : $data->output['viewUser']['name']
);
$this->submitTitle='Save Changes';
$this->fromForm='viewUser';
$levelOptions = array();

$this->fields=array(
	'id' => array(
		'label' => $data->phrases['users']['labelAddEditIDNumber'],
		'tag' => 'span',
		'value' => (empty($data->output['userForm']['id']) ? '' : $data->output['userForm']['id'])
	),
	'firstName' => array(
		'label' => $data->phrases['users']['labelAddEditFirstName'],
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['userForm']['firstName']{0}) ? '' : $data->output['userForm']['firstName']),
		'params' => array(
			'type' => 'text',
			'size' => 128,
		),
		'description' => '
			<p>
				<b>'.$data->phrases['users']['labelAddEditFirstName'].'</b><br />
				'.$data->phrases['users']['descriptionAddEditFirstName'].'
			</p>
		'
	),
	'lastName' => array(
		'label' => $data->phrases['users']['labelAddEditLastName'],
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['userForm']['lastName']{0}) ? '' : $data->output['userForm']['lastName']),
		'params' => array(
			'type' => 'text',
			'size' => 128,
		),
		'description' => '
			<p>
				<b>'.$data->phrases['users']['labelAddEditLastName'].'</b><br />
				'.$data->phrases['users']['descriptionAddEditLastName'].'
			</p>
		'
	),
	'name' => array(
		'label' => $data->phrases['users']['labelAddEditName'],
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['userForm']['name']) ? '' : $data->output['userForm']['name']),
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>'.$data->phrases['users']['labelAddEditName'].'</b><br />
				'.$data->phrases['users']['descriptionAddEditName'].'
			</p>
		'
	),
	'registeredDate' => array(
		'label' => $data->phrases['users']['labelAddEditRegisteredDate'],
		'tag' => 'span',
		'value' => (empty($data->output['userForm']['registeredDate']) ? '' : $data->output['userForm']['registeredDate']),
	),
	'registeredIP' => array(
		'label' => $data->phrases['users']['labelAddEditRegisteredIP'],
		'tag' => 'span',
		'value' => (empty($data->output['userForm']['registeredIP']) ? '' : $data->output['userForm']['registeredIP']),
	),
	'lastAccess' => array(
		'label' => $data->phrases['users']['labelAddEditLastAccess'],
		'tag' => 'span',
		'value' => (empty($data->output['userForm']['lastAccess']) ? '' : $data->output['userForm']['lastAccess']),
	),
	'contactEMail' => array(
		'label' => $data->phrases['users']['labelAddEditContactEmail'],
		'tag' => 'input',
		'value' => (empty($data->output['userForm']['contactEMail']) ? '' : $data->output['userForm']['contactEMail']),
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>'.$data->phrases['users']['labelAddEditContactEmail'].'</b><br />
				'.$data->phrases['users']['descriptionAddEditContactEmail'].'
			</p>
		'
	),
	'publicEMail' => array(
		'label' => $data->phrases['users']['labelAddEditPublicEmail'],
		'tag' => 'input',
		'value' => (empty($data->output['userForm']['publicEMail']) ? '' : $data->output['viewUser']['publicEMail']),
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>'.$data->phrases['users']['labelAddEditPublicEmail'].'</b><br />
				'.$data->phrases['users']['descriptionAddEditPublicEmail'].'
			</p>
		'
	),
	'password' => array(
		'label' => $data->phrases['users']['labelAddEditPassword'],
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'password',
			'size' => 128
		),
		'description' => '
			<p>
				<b>'.$data->phrases['users']['labelAddEditPassword'].'</b><br />
				'.$data->phrases['users']['descriptionAddEditPassword'].'
			</p>
		',
	),
	'password2' => array(
		'label' => $data->phrases['users']['labelAddEditPassword2'],
		'compareTo' => 'password',
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'password',
			'size' => 128
		),
		'description' => '
			<p>
				<b>'.$data->phrases['users']['labelAddEditPassword2'].'</b><br />
				'.$data->phrases['users']['descriptionAddEditPassword2'].'
			</p>
		',
		'compareFailMessage' => $data->phrases['users']['passwordsMismatch']
	),
    'timeZone' => array(
		'label' => $data->phrases['users']['labelAddEditTimeZone'],
        'required' => true,
        'tag' => 'select',
        'value' => (empty($data->output['userForm']['timeZone']) ? $data->settings['defaultTimeZone'] : $data->output['userForm']['timeZone']),
        'options' => $data->output['timeZones']
    )
);
foreach($data->output['groupList'] as $value) {
    if(checkPermission($value['groupName'],'manageGroups',$data)) {
        $checked='';
        $expires='Never';
        if(isset($data->output['userGroupList'])) {
            foreach($data->output['userGroupList'] as $subKey => $subValue) {
                if($subValue['groupName']==$value['groupName']) {
                    // User must be already a member of the group
                    $checked='checked';
                    // Find out when the group expires
                    if($subValue['expires']==0) {
                        $expires='Never';
                    } else {
                        $expires=gmdate('d F Y - G:i:s',$subValue['expires']);
                    }
                }
            }
        }
                
        $this->fields[$value['groupName']]=array(
            'label'   => $value['groupName'],
            'tag'     => 'input',
            'group'   => $data->phrases['users']['userGroupsHeading'],
            'value'   => 'checked',
            'checked' => $checked,
            'params' => array(
                'type' => 'checkbox'
            )
        );
        $this->fields[$value['groupName'].'_expiration']=array(
            'label' => $data->phrases['users']['labelAddEditExpires'],
            'tag' => 'span',
            'value' => $expires,

        );
        $this->fields[$value['groupName'].'_update']=array(
            'label'   => $data->phrases['users']['labelAddEditUpdateExpiration'],
            'tag'     => 'select',
            'group'   => $data->phrases['users']['userGroupsHeading'],
            'options' => array(
                $data->phrases['users']['optionUpdateExpirationNoChange'],
                $data->phrases['users']['optionUpdateExpirationNever'],
                $data->phrases['users']['optionUpdateExpiration15Min'],
                $data->phrases['users']['optionUpdateExpiration1Hr'],
                $data->phrases['users']['optionUpdateExpiration2Hr'],
                $data->phrases['users']['optionUpdateExpiration1Day'],
                $data->phrases['users']['optionUpdateExpiration2Day'],
                $data->phrases['users']['optionUpdateExpiration1Week'],
            ),
            'value'   => $data->phrases['users']['optionUpdateExpirationNoChange']
        );
        $state = (!isset($data->output['userForm']['permissions']['manageGroups'][$value['groupName']]['value'])) ? '0' : $data->output['userForm']['permissions']['manageGroups'][$value['groupName']]['value'];
        $this->fields['manageGroups_'.$value['groupName']]=array(
            'label'   => $data->phrases['users']['labelAddEditManageMembership'],
            'tag'     => 'select',
            'group'   => $data->phrases['users']['userGroupsHeading'],
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
            ),
            'value'   => $state
        );
    }
}
foreach($data->permissions as $category => $permissions) {
    if(checkPermission('permissions',$category,$data)) {
    
    	$value = (!isset($data->output['userForm']['permissions'][$category]['permissions']['value'])) ? '0' : $data->output['userForm']['permissions'][$category]['permissions']['value'];
    	
        $this->fields[$category.'_permissions']=array(
            'label'   => $data->phrases['users']['labelAddEditManagePermissions'],
            'tag'     => 'select',
            'group'   => ucfirst($category).' '.$data->phrases['users']['permissions'],
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
            ),
            'value'   => $value
        );
        foreach($permissions as $permissionName => $permissionDescription) {
        	
        	$value = (!isset($data->output['userForm']['permissions'][$category][$permissionName]['value'])) ? '0' : $data->output['userForm']['permissions'][$category][$permissionName]['value'];
        	        	
        	if(isset($data->output['userFinalPermissions'][$category][$permissionName])){
	        	if($data->output['userFinalPermissions'][$category][$permissionName]['value'] == '0'){
		        	$verdict = $data->phrases['users']['optionPermissionNeutral'];
	        	}elseif($data->output['userFinalPermissions'][$category][$permissionName]['value'] == '-1'){
	        		$verdict = $data->phrases['users']['permissionVerdictForbidden'].': '.$data->output['userFinalPermissions'][$category][$permissionName]['source'];
		        }elseif($data->output['userFinalPermissions'][$category][$permissionName]['value'] == '1'){
	        		$verdict = $data->phrases['users']['permissionVerdictAllowed'].': '.$data->output['userFinalPermissions'][$category][$permissionName]['source'];
		        }
        	} else{
	        	$verdict = $data->phrases['users']['optionPermissionNeutral'];
        	}
        	
        	
            $this->fields[$category.'_'.$permissionName]=array(
                'label'   => $permissionDescription,
                'tag'     => 'select',
                'group'   => ucfirst($category).' Permissions',
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
	            ),
                'value' => $value,
                'description' => '
                	<p><b>'.$data->phrases['users']['verdict'].':</b> '.$verdict.'</p>'
            );
        }
    }
}
?>