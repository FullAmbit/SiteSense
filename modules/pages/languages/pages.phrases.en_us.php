<?php

function languages_pages_en_us(){
	return array(
		'pageDeleteSuccessHeading'     => "Page Deleted",
		'pageDeleteSuccessMessage'     => "The page has been deleted. It has affected this many pages: ",
		'returnToPages'                => "Return To Pages",
		'pageDeleteCancelledHeading'   => "Delete Cancelled",
		'pageDeleteConfirmHeading'     => "Are you sure to want to delete this page?",
		'pageDeleteConfirmMessage'     => "*** WARNING *** This will also delete any child pages",
		"addNewPage"                   => "Add New Page",
		"noPagesExist"                 => "No Pages Exist",
		"managePagesHeading"           => "Manage Pages",
		"addChild"                     => "Add Child",
		"manageSidebarsHeading"        => "Manage Sidebars",
		"labelAddEditName"             => "Name",
		"descriptionAddEditName"       => "Must be unique. Used for the URL.",
		"labelAddEditTitle"            => "Page Title",
		"descriptionAddEditTitle"      => "Used as the content of the heading tag for this text.",
		"labelAddEditParent"           => "Parent Page",
		"descriptionAddEditParent"     => 'If sidebar navigation is enabled this sub-page will appear in a separate menu off the parent. If you choose to enable "show on parent" the sub-page will appear as a second contentBox and H2 below the parent. The order child pages are shown can be controlled from the \'list\' page.',
		'labelAddEditShowOnMenu'       => "Create Menu Item",
		"descriptionAddEditShowOnMenu" => "Adds this static page to the main menu",
		"labelAddEditMenuParent"       => "Parent Menu Item",
		"descriptionAddEditMenuParent" => "If this element is to be shown on the menu, this is the text that will be shown inside it's anchor.",
		"labelAddEditMenuText"         => "Menu Text",
		"descriptionAddEditMenuText"   => "The text that will appear on the menu item",
		"labelAddEditRawContent"       => "Page Content",
		"descriptionAddEditRawContent" => 'The actual text that will go inside the automatically generated \'contentBox\'. HTML is allowed in here, though any sub-headings you wish to use should start at H3, since \'Page Title\' is used as the h2.',
		"labelAddEditLive"             => "Live",
		"descriptionAddEditLive"       => "By default new blog entries are hidden from normal users until you check this box or enable them on the blog post list.",
		"captionAddPage"               => "Create New Static Page",
		"captionEditPage"              => "Edit Page",
		'submitAddEditForm'            => "Save Changes"
		
	);
}