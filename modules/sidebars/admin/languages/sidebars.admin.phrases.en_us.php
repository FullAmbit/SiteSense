
<?php

function languages_sidebars_admin_en_us(){
	return array(
		'core'                            => array(
			'sidebars' => 'Sidebars'
		),
		'redirectedInThreeSeconds'        => 'You should be auto redirected to the page list in three seconds.',
		'clickToWait'                     => 'Click Here if you do not wish to wait.',
		'deleteSidebarSure'               => 'Are you sure you want to delete sidebar #',
		'yesToDelete'                     => 'Yes, Delete it',
		'delete'                          => 'Delete',
		'addNewSidebar'                   => 'Add New Sidebar',
		'noSidebars'                      => 'No sidebars exist',
		'manageSidebars'                  => 'Manage Sidebars',
		'sidebarTitle'                    => 'Sidebar Title',
		'side'                            => 'Side',
		'controls'                        => 'Controls',
		'moveUp'                          => 'Move Up',
		'moveDown'                        => 'Move Down',
		'switchSide'                      => 'Switch Side',
		'left'                            => 'Left',
		'right'                           => 'Right',
		'captionSidebarsAdd'              => 'Add Sidebar',
		'labelSidebarsName'               => 'Name',
		'descriptionSidebarsName'         => 'Used for reference in the admin panel. Must be unique.',
		'labelSidebarsTitle'              => 'Title',
		'descriptionSidebarsTitle'        => 'The title displayed over the sidebar on the user end.',
		'labelSidebarsTitleURL'           => 'Title URL',
		'descriptionSidebarsTitleURL'     => 'Optional Field that will turn the title into a link. Should be a full url - IF the first character is a vertical break (|) then the \\\'linkRoot\\\' will be appended before it -- in other words a local link in the CMS.',
		'labelSidebarsSide'               => 'Side',
		'labelSidebarsRawContent'         => 'Content',
		'descriptionSidebarsRawContent'   => 'The actual text that will go inside the automatically generated <code>blockquote</code> tag. HTML is allowed in here, though any sub-headings you wish to use should start at H3, since H2 is already in use.',
		'submitSidebarsForm'              => 'Save Sidebar',
		'insufficientUserPermissions'     => 'Insufficient User Permissions',
		'insufficientUserPermissions2'    => 'You do not have the permissions to access this area.',
		'uniqueNameConflict'              => 'Unique Name Conflict',
		'uniqueNameConflict2'             => 'This name already exists for a sidebar.',
		'valuesSaved'                     => 'Values Saved Successfully',
		'valuesSaved2'                    => 'Auto generated short name was: ',
		'addPage'                         => 'Add New Page',
		'returnToPageList'                => 'Return to Page List',
		'errorInData'                     => 'Error in Data',
		'errorInData2'                    => 'There were one or more errors. Please correct the fields with the red X next to them and try again.',
		'errorSaving'                     => 'There was an error in saving your sidebar at this time.',
		'insufficientParameters'          => 'insufficient parameters',
		'noIdEntered'                     => 'No ID # was entered to be deleted',
		'lockedSidebarElement'            => 'Locked Sidebar Element',
		'lockedSidebarElement2'           => 'That sidebar element cannot be deleted from the admin panel. Either disable it, or delete it\'s associated module files.',
		'databaseError'                   => 'Database Error',
		'databaseError2'                  => 'You attempted to delete a record, are you sure that record existed?',
		'unknownFunction'                 => 'unknown function',
		'captionSidebarsEdit'             => 'Edit Sidebar'
	);
}
?>