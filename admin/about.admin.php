<?php

function admin_buildContent($data) {

	$info['Server time']=strftime('%B %d, %Y, %I:%M:%S %p');
	$info['Server Signature']=$_SERVER['SERVER_SIGNATURE'];
	$info['Server Name']=$_SERVER['SERVER_NAME'];
	$info['Server Address']=$_SERVER['SERVER_ADDR'];
	$info['Gateway Interface']=$_SERVER['GATEWAY_INTERFACE'];
	$info['Server Protocol']=$_SERVER['SERVER_PROTOCOL'];
	$info['PHP Version']=phpversion().'</td></tr><tr><td colspan="2">
		<img src="'.$_SERVER['PHP_SELF'].'?='.php_logo_guid().'" alt="PHP Logo" />';
	$info['Zend Version']=zend_version().'</td></tr><tr><td colspan="2">
		<img src="'.$_SERVER['PHP_SELF'].'?='.zend_logo_guid().'" alt="Zend Logo" />';
	$info['Host OS']=PHP_OS;

	$data->output['secondSideBar']='
	<table class="sysInfo">
		<caption>System Information</caption>
		';

	foreach ($info as $title => $value) {
		if (is_array($value)) {
			$data->output['secondSideBar'].='<tr>
			<th colspan="2" class="section">'.$title.'</th>';

			foreach ($value as $subTitle => $subValue) {
				$data->output['secondSideBar'].='<tr>
			<th>'.$subTitle.'</th>
			<td>'.$subValue.'</td>
		</tr>';

			}
		} else {

			$data->output['secondSideBar'].='<tr>
			<th>'.$title.'</th>
			<td>'.$value.'</td>
		</tr>';

		}
	}

	$data->output['secondSideBar'].='
	</table>';

	$data->output['pageTitle']='About This CMS -';

}

function admin_content($data) {

	echo '
	<div class="aboutBox">
		<h2>Welcome to the Control Panel for your CMS installation</h2>
		<p>
			Please choose from the options on the left to manage your settings.
		</p>
	<!-- .aboutBox --></div>
	';

}


?>