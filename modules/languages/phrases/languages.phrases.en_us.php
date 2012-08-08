<?php

function languages_core_en_us(){

	$phrases                       = array(
		'adminHeading'               => 'Control Panel',
		'actionEdit'                 => 'Edit',
		'actionDelete'               => 'Delete',
		'actionModify'               => 'Modify',
		'actionConfirmDelete'        => 'Yes, Confirm Delete',
		'actionCancelDelete'         => 'No, Cancel Delete',
		'no'                         => "No",
		'yes'                        => "Yes",
		'name'                       => "Name",
		'title'                      => "Title",
		"controls"                   => "Controls",
		"time"                       => "Time",
		"messageRedirect"            => "You should be auto redirected in three seconds.",
		"linkSkipWait"               => "Click here if you do not wish to wait",
		"sidebars"                   => "Sidebars",
		"status"                     => "Status",
		'enable'                     => "Enable",
		"disable"                    => "Disable",
		"formValidationErrorHeading" => "Error in Data",
		"formValidationErrorMessage" => "There were one or more errors. Please correct the fields with the red X next to them and try again.",
		'invalidID'                  => "The ID you specified was not found in the database.",
		"accessDeniedHeading"        => "Insufficient User Permissions",
		"accessDeniedMessage"        => "You do not have the permissions to access this area.",
		"uniqueNameConflictHeading"  => "Unique Name Conflict",
		"uniqueNameConflictMessage"  => "That name already exists in the database.",
		"databaseErrorHeading"       => "Database Error",
		"databaseErrorMessage"       => "There was an error in accessing the database."
	);
	
	return array(
		'name'                       => 'English (US)',
		'shortName'                  => 'en_us',
		'phrases'                    => $phrases
	);
}
?>