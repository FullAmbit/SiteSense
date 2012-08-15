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
$this->caption=$data->phrases['urls']['captionAddRemap'];
$this->submitTitle=$data->phrases['urls']['submitAddEditForm'];
$this->fields=array(
	'match' => array(
		'label' => $data->phrases['urls']['pattern'],
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['urlremap']['match']) ? $data->output['urlremap']['match'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>'.$data->phrases['urls']['labelMatch'].'</b><br />
				'.$data->phrases['urls']['descriptionMatch'].'
			</p>
		'
	),
	'replace' => array(
		'label' => $data->phrases['urls']['replacement'],
        'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['urlremap']['replace']) ? $data->output['urlremap']['replace'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>'.$data->phrases['urls']['replacement'].'</b><br />
				'.$data->phrases['urls']['descriptionReplacement'].'
				$i (where i is an integer) means the match in the "$i"th bracket. $0 is the entire match.
			</p>
		'
	),
	'hostname' => array(
		'tag' => 'select',
		'label' => $data->phrases['urls']['hostname'],
		'options' => array(
			array(
				'value' => '',
				'text' => 'Global'
			)
		),
		'value' => (isset($data->output['urlremap']['hostname'])) ? $data->output['urlremap']['hostname'] : ''
	),
    'regex' => array(
        'label' => $data->phrases['urls']['labelRegex'],
        'tag' => 'input',
        'value' => 1,
        'checked' => (isset($data->output['urlremap']['regex']) && $data->output['urlremap']['regex'] == '1') ? 'checked' : '',
        'params' => array(
            'type' => 'checkbox'
        )
    ),
    'isRedirect' => array(
    	'label' => $data->phrases['urls']['labelIsRedirect'],
    	'tag' => 'input',
    	'value' => 1,
    	'checked' => (isset($data->output['urlremap']['isRedirect']) && $data->output['urlremap']['isRedirect']=='1') ? 'checked' : '',
    	'params' => array(
    		'type' => 'checkbox'
    	)
    )
);

if(isset($data->output['hostnameList'])){
	foreach($data->output['hostnameList'] as $hostnameItem){
		$this->fields['hostname']['options'][] = array(
			'value' => $hostnameItem['hostname'],
			'text' => $hostnameItem['hostname']
		);
	}
}

if(isset($data->output['urlremap']['regex'])) {
    unset($this->fields['regex']);
}
?>