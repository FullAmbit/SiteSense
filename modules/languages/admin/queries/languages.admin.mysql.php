<?php

function admin_languages_addQueries(){
	return array(
		'getAllLanguages' => '
			SELECT
				*
			FROM 
				!prefix!languages 
			ORDER BY
				shortName ASC 
		',
		'getAllPhrasesByLanguage' => '
			SELECT
				*
			FROM 
				!table!
			ORDER BY
				module, phrase ASC
		',
		'getLanguage' => '
			SELECT 
				* 
			FROM 
				!prefix!languages 
			WHERE 
				shortName = :shortName 
			LIMIT
				1
		',
		'getPhraseByLanguage' => '
			SELECT
				*
			FROM 
				!table! 
			WHERE 
				phrase = :phrase
			LIMIT 
				1
		',
		'addPhraseByLanguage' => '
			INSERT INTO 
				!table!
				(phrase,text,module)
			VALUES
				(:phrase,:text,:module)
		',
		'getPhraseByLanguageAndModule' => '
			SELECT
				id,phrase,text
			FROM 
				!table!
			WHERE
				phrase = :phrase
				AND
				module = :module
			LIMIT 
				1
		',
		'getPhraseByLanguageAndId' => '
			SELECT
				*
			FROM 
				!table! 
			WHERE 
				id = :id
			LIMIT 
				1
		',
		'getPhrasesByModule' => '
			SELECT
				phrase,id,text
			FROM
				!table!
			WHERE
				module = :module
		',
		'updatePhraseByLanguage' => '
			UPDATE 
				!table!
			SET 
				phrase = :phrase,
				text = :text,
				module = :module
			WHERE
				id = :id
		',
		'createLanguageTable' => '
			CREATE TABLE IF NOT EXISTS !table! (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `phrase` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT "",
			  `text` text CHARACTER SET utf8,
			  `module` varchar(64) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `phrase` (`phrase`,`module`)
			) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
		',
		'deletePhrasesByModuleAndLanguage' => '
			DELETE FROM 
				!table!
			WHERE 
				module = :module
		',
		'addLanguage' => '
			INSERT INTO
				!prefix!languages
				(shortName,name)
			VALUES
				(:shortName,:name)
		',
		'addDefaultLanguage' => '
			INSERT INTO
				!prefix!languages
				(shortName,name,isDefault)
			VALUES
				(:shortName,:name,1)
		',
		'disableDefaultLanguage' => '
			UPDATE
				!prefix!languages
			SET 
				isDefault = 0
		',
		'setNewDefaultLanguage' => '
			UPDATE
				!prefix!languages 
			SET 
				isDefault = 1
			WHERE 
				shortName = :shortName
		'
	);
}