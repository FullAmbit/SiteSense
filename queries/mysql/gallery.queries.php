<?php
/*
!table! = $tableName
!prefix! = dynamicPDO::tablePrefix
*/

function gallery_addQueries() {
	return array(
		'getAlbumByUserAndShortName' => '
			SELECT * FROM !prefix!galleryalbums
			WHERE shortName = :shortName
			AND user = :user
			
		',
		'getAlbumsByUser' => '
			SELECT * FROM !prefix!galleryalbums
			WHERE user = :user
			ORDER BY ID DESC
		',
		'getImagesFromAlbum' => '
			SELECT * FROM !prefix!galleryimages
			WHERE album = :album
		',
		'getImageByAlbumAndName' => '
			SELECT * FROM !prefix!galleryimages
			WHERE album = :album
			AND shortName = :shortName
		',
		'getProfilePictureAlbum' => '
			SELECT * FROM !prefix!galleryalbums
			WHERE user = :user
			AND shortName = \'profile-pictures\'
		',
		'getImageComments' => '
			SELECT c.*, u.name user_name FROM !prefix!gallerycomments c
				INNER JOIN !prefix!users u
				ON u.id = c.user
			WHERE image = :image
		',
		'getProfilePictures' => '
			SELECT i.*, a.shortName album_shortName
			FROM !prefix!galleryimages i
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
			INSERT INTO !prefix!galleryalbums
			(name, shortName, allowComments, user)
			VALUES
			(:name, :shortName, :allowComments, :user) 
		',
		'editAlbum' => '
			UPDATE !prefix!galleryalbums
			SET
				name = :name,
				shortName = :shortName,
				allowComments = :allowComments
			WHERE id = :id
		',
		'addImage' => '
			INSERT INTO !prefix!galleryimages
			(name, shortName, album, image, thumb, icon)
			VALUES
			(:name, :shortName, :album, :image, :thumb, :icon)
		',
		'editImage' => '
			UPDATE !prefix!galleryimages
			SET
				name = :name,
				shortName = :shortName
			WHERE id = :id
		',
		'addComment' => '
			INSERT INTO !prefix!gallerycomments
			(image, user, content)
			VALUES
			(:image, :user, :content)
		',
		'deleteAlbum' => '
			DELETE FROM !prefix!galleryalbums
			WHERE id = :id
		',
		'deleteImage' => '
			DELETE FROM !prefix!galleryimages
			WHERE id = :id
		' 
	);
}