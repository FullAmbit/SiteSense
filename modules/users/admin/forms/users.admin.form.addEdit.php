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
		'label' => 'ID #',
		'tag' => 'span',
		'value' => (empty($data->output['userForm']['id']) ? '' : $data->output['userForm']['id'])
	),
	'firstName' => array(
		'label' => 'Full Name',
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['userForm']['firstName']{0}) ? '' : $data->output['userForm']['firstName']),
		'params' => array(
			'type' => 'text',
			'size' => 128,
		),
		'description' => '
			<p>
				<b>Full Name</b> - The full name of the user.
			</p>
		'
	),
	'lastName' => array(
		'label' => 'Last Name',
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['userForm']['lastName']{0}) ? '' : $data->output['userForm']['lastName']),
		'params' => array(
			'type' => 'text',
			'size' => 128,
		)
	),
	'name' => array(
		'label' => 'Username',
		'required' => true,
		'tag' => 'input',
		'value' => (empty($data->output['userForm']['name']) ? '' : $data->output['userForm']['name']),
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Username</b> - The name the user logs in with. This is different from displayName.
			</p>
		'
	),
	'registeredDate' => array(
		'label' => 'Registered on',
		'tag' => 'span',
		'value' => (empty($data->output['userForm']['registeredDate']) ? '' : $data->output['userForm']['registeredDate']),
	),
	'registeredIP' => array(
		'label' => 'Registered From',
		'tag' => 'span',
		'value' => (empty($data->output['userForm']['registeredIP']) ? '' : $data->output['userForm']['registeredIP']),
	),
	'lastAccess' => array(
		'label' => 'Last Access',
		'tag' => 'span',
		'value' => (empty($data->output['userForm']['lastAccess']) ? '' : $data->output['userForm']['lastAccess']),
	),
	'contactEMail' => array(
		'label' => 'Contact E-Mail',
		'tag' => 'input',
		'value' => (empty($data->output['userForm']['contactEMail']) ? '' : $data->output['userForm']['contactEMail']),
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Contact E-Mail</b> - E-mail Staff can use to contact user.
			</p>
		'
	),
	'publicEMail' => array(
		'label' => 'Public E-Mail',
		'tag' => 'input',
		'value' => (empty($data->output['viewUser']['publicEMail']) ? '' : $data->output['viewUser']['publicEMail']),
		'params' => array(
			'type' => 'text',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Public E-Mail</b> - E-mail shown to the public on the user\'s profile.
			</p>
		'
	),
	'password' => array(
		'label' => 'Password',
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'password',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Password</b> - What the user logs in with for a password
			</p>
		',
	),
	'password2' => array(
		'label' => 'Retype Password',
		'compareTo' => 'password',
		'tag' => 'input',
		'value' => '',
		'params' => array(
			'type' => 'password',
			'size' => 128
		),
		'description' => '
			<p>
				<b>Retype Password</b> - Enter the new password a second time to verify changes.
			</p>
		',
		'compareFailMessage' => 'The passwords you entered do not match!'
	)
);
foreach($data->output['groupList'] as $value) {
    if(checkPermission($value,'userGroups',$data)) {
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
            'group'   => 'User Groups',
            'value'   => 'checked',
            'checked' => $checked,
            'params' => array(
                'type' => 'checkbox'
            )
        );
        $this->fields[$value['groupName'].'_expiration']=array(
            'label' => 'Expires',
            'tag' => 'span',
            'value' => $expires,

        );
        $this->fields[$value['groupName'].'_update']=array(
            'label'   => 'Update Expiration',
            'tag'     => 'select',
            'group'   => 'User Groups',
            'options' => array(
                'No change',
                'Never',
                '15 minutes',
                '1 hour',
                '2 hours',
                '1 day',
                '2 days',
                '1 week'
            ),
            'value'   => 'No change'
        );
        if(isset($data->output['userForm']['permissions']['userGroups']['permissions']['allow'])) {
            if($data->output['userForm']['permissions']['userGroups']['permissions']['allow']) {
                $state='Allow';
            } else {
                $state='Forbid';
            }
        } else {
            $state='Inherited';
        }
        $this->fields['userGroups_'.$value['groupName']]=array(
            'label'   => 'Manage Membership',
            'tag'     => 'select',
            'group'   => 'User Groups',
            'options' => array(
                'Allow',
                'Inherited',
                'Forbid'
            ),
            'value'   => $state
        );
    }
}
foreach($data->permissions as $category => $permissions) {
    if(checkPermission('permissions',$category,$data)) {
        if(isset($data->output['userForm']['permissions'][$category]['permissions']['allow'])) {
            if($data->output['userForm']['permissions'][$category]['permissions']['allow']) {
                $value='Allow';
            } else {
                $value='Forbid';
            }
        } else {
            $value='Inherited';
        }
        $this->fields[$category.'_permissions']=array(
            'label'   => 'Manage Permissions',
            'tag'     => 'select',
            'group'   => ucfirst($category).' Permissions',
            'options' => array(
                'Allow',
                'Inherited',
                'Forbid'
            ),
            'value'   => $value
        );
        foreach($permissions as $permissionName => $permissionDescription) {
            if(isset($data->output['userForm']['permissions'][$category][$permissionName]['allow'])) {
               if($data->output['userForm']['permissions'][$category][$permissionName]['allow']) {
                   $value='Allow';
               } else {
                   $value='Forbid';
               }
            } else {
                $value='Inherited';
            }
            $this->fields[$category.'_'.$permissionName]=array(
                'label'   => $permissionDescription,
                'tag'     => 'select',
                'group'   => ucfirst($category).' Permissions',
                'options' => array(
                    'Allow',
                    'Inherited',
                    'Forbid'
                ),
                'value'   => $value
            );
        }
    }
}
?>