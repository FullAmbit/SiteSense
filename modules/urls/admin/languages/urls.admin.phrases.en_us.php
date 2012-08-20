<?php
function languages_urls_admin_en_us(){
	return array(
		'core' => array(
			'urls'                    => 'URL Routing',
			'permission_urls_access'  => 'Access URL Routing',
			'permission_urls_add'     => 'Add URL Routes',
			'permission_urls_edit'   => 'Edit URL Routes',
			'permission_urls_delete' => 'Delete URL Routes',
			'permission_urls_list'   => 'List URL Routes'
		),
		'addRemap'                  => 'Add URL Route',
		'manageURLsHeading'         => 'URL Routes',
		'pattern'                   => 'Pattern',
		'replacement'               => 'Replacement',
		'type'                      => 'Type',
		'hostname'                  => 'Hostname',
		'mode'                      => 'Mode',
		'deleteURLSuccessHeading'   => 'URL Route Deleted',
		'deleteURLSuccessMessage'   => 'The route was removed from the database.',
		'returnToList'              => 'Return To List',
		'deleteURLErrorHeading'     => 'Deletion Error',
		'deleteURLConfirmHeading'   => 'Confirm Deletion',
		'deleteURLConfirmMessage'   => 'Are you sure you want to delete this URL?',
		'labelMatch'                => 'Match',
		'descriptionMatch'          => 'What part of the URL to match and replace',
		'descriptionReplacement'    => '$i (where i is an integer) means the match in the \'$i\'th bracket. $0 is the entire match.',
		'labelRegex'                => 'Use Regex?',
		'labelIsRedirect'           => 'Redirect The URL',
		'captionAddRemap'           => 'Create A URL Remap',
		'submitAddEditForm'         => 'Save URL Remap',
		'saveRemapSuccess'          => 'Remap Successfully Saved',
		'captionEditRemap'          => 'Edit URL Remap'
	);
}
?>