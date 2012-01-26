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
function gallery_addQueries() {
	return array(
		'getAlbumByUserAndShortName' => '
			SELECT * FROM !prefix!gallery_albums
			WHERE shortName = :shortName
			AND user = :user
		',
		'getAlbumsByUser' => '
			SELECT * FROM !prefix!gallery_albums
			WHERE user = :user
			ORDER BY ID DESC
		',
		'getImagesFromAlbum' => '
			SELECT * FROM !prefix!gallery_images
			WHERE album = :album
		',
		'getImageByAlbumAndName' => '
			SELECT * FROM !prefix!gallery_images
			WHERE album = :album
			AND shortName = :shortName
		',
		'getProfilePictureAlbum' => '
			SELECT * FROM !prefix!gallery_albums
			WHERE user = :user
			AND shortName = \'profile-pictures\'
		',
		'getImageComments' => '
			SELECT c.*, u.name user_name FROM !prefix!gallery_comments c
				INNER JOIN !prefix!users u
				ON u.id = c.user
			WHERE image = :image
		',
		'getProfilePictures' => '
			SELECT i.*, a.shortName album_shortName
			FROM !prefix!gallery_images i
				INNER JOIN !prefix!galleryalbums a
				ON
					i.album = a.id
			WHERE
				a.user = :user
			AND
				a.shortName = \'profile-pictures\'
			ORDER BY ID DESC
		',
		'addAlbum' => '
			INSERT INTO !prefix!gallery_albums
			(name, shortName, allowComments, user)
			VALUES
			(:name, :shortName, :allowComments, :user) 
		',
		'editAlbum' => '
			UPDATE !prefix!gallery_albums
			SET
				name = :name,
				shortName = :shortName,
				allowComments = :allowComments
			WHERE id = :id
		',
		'addImage' => '
			INSERT INTO !prefix!gallery_images
			(name, shortName, album, image, thumb, icon)
			VALUES
			(:name, :shortName, :album, :image, :thumb, :icon)
		',
		'editImage' => '
			UPDATE !prefix!gallery_images
			SET
				name = :name,
				shortName = :shortName
			WHERE id = :id
		',
		'addComment' => '
			INSERT INTO !prefix!gallery_comments
			(image, user, content)
			VALUES
			(:image, :user, :content)
		',
		'deleteAlbum' => '
			DELETE FROM !prefix!gallery_albums
			WHERE id = :id
		',
		'deleteImage' => '
			DELETE FROM !prefix!gallery_images
			WHERE id = :id
		' 
	);
}