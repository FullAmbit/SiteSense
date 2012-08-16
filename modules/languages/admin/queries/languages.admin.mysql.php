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
				!prefix!languages_phrases_!lang!
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
				!prefix!languages_phrases_!lang! 
			WHERE 
				phrase = :phrase
			LIMIT 
				1
		',
		'addPhraseByLanguage' => '
			INSERT INTO 
				!prefix!languages_phrases_!lang!
				(phrase,text,module,isAdmin)
			VALUES
				(:phrase,:text,:module,:isAdmin)
		',
		'getPhraseByLanguageAndModule' => '
			SELECT
				id,phrase,text
			FROM 
				!prefix!languages_phrases_!lang!
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
				!prefix!languages_phrases_!lang! 
			WHERE 
				id = :id
			LIMIT 
				1
		',
		'getPhrasesByModule' => '
			SELECT
				phrase,id,text
			FROM
				!prefix!languages_phrases_!lang!
			WHERE
				module = :module
		',
		'updatePhraseByLanguage' => '
			UPDATE 
				!prefix!languages_phrases_!lang!
			SET 
				phrase = :phrase,
				text = :text,
				module = :module,
				isAdmin = :isAdmin
			WHERE
				id = :id
		',
		'createLanguageTable' => '
			CREATE TABLE IF NOT EXISTS !prefix!languages_phrases_!lang! (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `phrase` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT "",
			  `text` text CHARACTER SET utf8,
			  `module` varchar(64) DEFAULT NULL,
			  `isAdmin` TINYINT(1) NOT NULL DEFAULT `0`
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `phrase` (`phrase`,`module`,`isAdmin`)
			) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
		',
		'deletePhrasesByModuleAndLanguage' => '
			DELETE FROM 
				!prefix!languages_phrases_!lang!
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
		',
		'truncatePhrases' => '
			TRUNCATE !prefix!languages_phrases_!lang!
		',
	);
}