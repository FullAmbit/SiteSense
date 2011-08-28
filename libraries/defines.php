<?php

define('ADMIN_SHOWPERPAGE',16);
/*
	This array is for displaying text, NOT for setting or verifying values!
	Should be incorporated into language strings at later date
*/
$languageText['userLevels']=array(
	 0xFF => 'Admin',      // Admins have full access rights
	 0xEF => 'Writer',     // Writers can do anything except edit user accounts
	 0x8F => 'Moderator',  // Moderators can approve user posts, ban users, move user posts into evidence
	 0x7F => 'Blogger',    // Bloggers are normal users who are allowed to 'own' a blog in the system
	 0x01 => 'User',       // Normal users can post replies to blog entries, edit their own profile, and that's it
	 0x00 => 'Guest',      // Guests can't do shit
	-0x01 => 'Banned'      // Banned ARE SHIT.
);

/*
	You want to check the level, use the DEFINES
*/

foreach ($languageText['userLevels'] as $key => $value) {
	define('USERLEVEL_'.strtoupper($value),(int)$key);
}


?>