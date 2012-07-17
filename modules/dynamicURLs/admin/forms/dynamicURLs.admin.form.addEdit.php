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
$this->caption='Create URL Remap';
$this->submitTitle='Save URL Remap';
$this->fields=array(
	'match' => array(
		'label' => 'Pattern',
		'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['urlremap']['match']) ? $data->output['urlremap']['match'] : '',
		'params' => array(
			'type' => 'text',
			'size' => 256
		),
		'description' => '
			<p>
				<b>Match</b> - Regex matching pattern
			</p>
		'
	),
	'replace' => array(
		'label' => 'Replacement',
        'required' => true,
		'tag' => 'input',
		'value' => isset($data->output['urlremap']['replace']) ? $data->output['urlremap']['replace'] : '',
		'params' => array(
			'type' => 'text'
		),
		'description' => '
			<p>
				<b>Replacement</b> - $i (where i is an integer) means the match in the "$i"th bracket. $0 is the entire match.
			</p>
		'
	),
    'regex' => array(
        'label' => 'Use Regular Expressions',
        'tag' => 'input',
        'value' => 1,
        'checked' => (isset($data->output['urlremap']['regex']) && $data->output['urlremap']['regex'] == '1') ? 'checked' : '',
        'params' => array(
            'type' => 'checkbox'
        )
    )
);
if(isset($data->output['urlremap']['regex'])) {
    unset($this->fields['regex']);
}
?>