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
/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/
function admin_urls_addQueries() {
	return array(
		'getAllUrlRemaps' => '
			SELECT * FROM !prefix!urls
			ORDER BY sortOrder ASC
		',
		'getUrlRemapById' => '
			SELECT * FROM !prefix!urls WHERE id = :id
			ORDER BY sortOrder ASC
		',
        'getUrlRemapByMatch' => '
			SELECT * FROM !prefix!urls WHERE `match` = :match AND `hostname` = :hostname
			ORDER BY sortOrder ASC
		',
		'editUrlRemap' => '
			UPDATE !prefix!urls
			SET `match` = :match, `replace` = :replace, `hostname` = :hostname, `isRedirect` = :isRedirect
			WHERE id = :id
		',
		'insertUrlRemap' => '
			INSERT INTO !prefix!urls
			SET `match` = :match, `replace` = :replace, `sortOrder` = :sortOrder, `regex`=:regex, `hostname` = :hostname, `isRedirect` = :isRedirect
		',
		'deleteUrlRemap' => '
			DELETE FROM !prefix!urls WHERE id = :id
		',
		
		'deleteByHostname' => '
			DELETE FROM !prefix!urls WHERE hostname = :hostname
		',
		'deleteReplacementByMatch' => '
      DELETE FROM !prefix!urls
      WHERE `match` = :match
    ',
    'updateUrlRemapByMatch' => '
			UPDATE !prefix!urls
			SET `match` = :newMatch, `replace` = :replace
			WHERE `match` = :match
    '
	);
}
?>