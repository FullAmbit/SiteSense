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
$this->caption='Editing Group: '.(
	empty($data->output['permissionGroup']) ? '' : $data->output['permissionGroup']['groupName']
);
$this->submitTitle='Save Changes';
$this->fromForm='permissionGroup';

$this->fields=array(
	'groupName'     => array(
		'label'     => 'Group Name',
		'required'  => true,
		'tag'       => 'input',
		'value'     => $data->output['permissionGroup']['groupName'],
		'params'    => array(
			'type'  => 'text',
			'size'  => 128,
		)
	)
);
// Get Group Permissions
$statement=$db->prepare('getPermissionsByGroupName');
$statement->execute(array(
    ':groupName' =>  $data->output['permissionGroup']['groupName']
));
if (($permissions=$statement->fetchAll(PDO::FETCH_ASSOC)) == FALSE) {
    $data->output['abort'] = true;
    $data->output['abortMessage'] = 'The group you specified could not be found';
}
foreach($permissions as $permission) {
    $data->output['permissionGroup']['permissions'][] = $permission['permissionName'];
}
if(isset($data->output['permissionGroup']['permissions'])) {
    // Organize array by module (Ex. $user['permissions']['blogs'])
    foreach($data->output['permissionGroup']['permissions'] as $key => $permission) {
        unset($data->output['permissionGroup']['permissions'][$key]);
        $separator = strpos($permission,'_');
        $prefix = substr($permission,0,$separator);
        $suffix = substr($permission,$separator+1);
        $data->output['permissionGroup']['permissions'][$prefix][] = $suffix;
    }
    // Clean up
    asort($user['permissions']);
}
foreach($data->permissions as $category) {
    $this->fields[]=array(
        $category . $data->output['permissionGroup']['permissions'][$category][$permission] => array(
            'label'   => $data->permissions[$category][$permission],
            'tag'     => 'input',
            'checked' => (isset($data->output['permissionGroup']['permissions'][$category][$permission])) ? 'checked' : '',
            'group'   => $data->permissions[$category],
            'params' => array(
                'type' => 'checkbox'
            )
        )
    );
}