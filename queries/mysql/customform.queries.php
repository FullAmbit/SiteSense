<?php

/*
	!table! = $tableName
	!prefix! = dynamicPDO::tablePrefix
*/

function customform_addQueries() {
	return array(
		'getAllForms' => '
			SELECT * FROM !prefix!customforms ORDER BY id DESC
		',
		'getFormById' => '
			SELECT * FROM !prefix!customforms WHERE id = :id
		',
		'getFormByShortName' => '
			SELECT * FROM !prefix!customforms WHERE shortName = :shortName
		', 
		'getFieldById' => '
			SELECT * FROM !prefix!customformfields WHERE id = :id
		',
		'getRowById' => '
			SELECT * FROM !prefix!customformrows WHERE id = :id
		',
		'getValueById' => '
			SELECT * FROM !prefix!customformvalues WHERE id = :id
		',
		'getFieldsByForm' => '
			SELECT * FROM !prefix!customformfields WHERE form = :form ORDER BY ID ASC
		',
		'getRowsByForm' => '
			SELECT * FROM !prefix!customformrows WHERE form = :form ORDER BY ID DESC
		',
		'getValuesByRow' => '
			SELECT * FROM !prefix!customformrows WHERE row = :row
		',
		'getValuesByField' => '
			SELECT * FROM !prefix!customformvalues WHERE field = :field
		',
		'getValuesByRow' => '
			SELECT * FROM !prefix!customformvalues WHERE row = :row AND field = :field
		',
		'getValuesByForm' => '
			SELECT v.* FROM !prefix!customformvalues v
				INNER JOIN !prefix!customformrows r
					ON r.id = v.row
			WHERE r.form = :form
		',
		'newForm' => '
			INSERT INTO !prefix!customforms (name, shortName, successMessage, requireLogin) VALUES (:name, :shortName, :successMessage, :requireLogin)
		',
		'newField' => '
			INSERT INTO !prefix!customformfields (form, name, type) VALUES (:form, :name, :type)
		',
		'newRow' => '
			INSERT INTO !prefix!customformrows (form) VALUES (:form)
		',
		'newValue' => '
			INSERT INTO !prefix!customformvalues (row, field, value) VALUES (:row, :field, :value)
		',
		'editForm' => '
			UPDATE !prefix!customforms SET name = :name, shortName = :shortName, successMessage = :successMessage, requireLogin = :requireLogin WHERE id = :id
		',
		'editField' => '
			UPDATE !prefix!customformfields SET name = :name, type = :type WHERE id = :id
		',
		'editValue' => '
			UPDATE !prefix!customformvalues SET value = :value WHERE id = :id
		',
		'deleteForm' => '
			DELETE FROM !prefix!customforms WHERE id = :id
		',
		'deleteField' => '
			DELETE FROM !prefix!customformfields WHERE id = :id
		',
		'deleteRow' => '
			DELETE FROM !prefix!customformrows WHERE id = :id
		',
		'deleteValue' => '
			DELETE FROM !prefix!customformvalues WHERE id = :id
		',
		
	);
}
?>