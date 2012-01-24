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