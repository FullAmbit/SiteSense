<?php

function languages_core_en_us(){

	$phrases = array(
		'adminHeading'			=> 'Control Panel',
		'actionEdit'			=> 'Edit',
		'actionDelete'			=> 'Delete',
		'actionModify'			=> 'Modify',
		'actionConfirmDelete'	=> 'Yes, Confirm Delete',
		'actionCancelDelete'	=> 'No, Cancel Delete',
		'no'					=> "No",
		'yes'					=> "Yes",
		'name'					=> "Name",
		"controls"				=> "Controls",
		"messageRedirect" => "You should be auto redirected to the page list in three seconds.",
		"linkSkipWait" => "Click here if you do not wish to wait"
	);
	
	return array(
		'name' => 'English (US)',
		'shortName' => 'en_us',
		'phrases' => $phrases
	);
}
?>