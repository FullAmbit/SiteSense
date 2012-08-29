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
		'getAllOverriddenPhrasesByLanguage' => '
			SELECT
				*
			FROM 
				!prefix!languages_phrases_!lang!
			WHERE
				override = 1
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
				AND isAdmin = :isAdmin
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
				AND NOT EXISTS
				(SELECT * FROM !prefix!languages_phrases_!lang! WHERE override=1 AND phrase = :phrase AND module = :module AND isAdmin = :isAdmin)
		',
		'updatePhraseByLanguageOverride' => '
			UPDATE 
				!prefix!languages_phrases_!lang!
			SET 
				phrase = :phrase,
				text = :text,
				module = :module,
				isAdmin = :isAdmin,
				override = :override
			WHERE
				id = :id
			LIMIT 1
		',
		'updatePhraseTextByLanguage' => '
			UPDATE
				!prefix!languages_phrases_!lang!
			SET
				text = :text
			WHERE
				phrase = :phrase
				AND
				module = :module
				AND
				isAdmin = :isAdmin
				AND NOT EXISTS
				(SELECT * FROM !prefix!languages_phrases_!lang! WHERE override=1 AND phrase = :phrase AND module = :module AND isAdmin = :isAdmin)
			LIMIT 1
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
			DELETE FROM !prefix!languages_phrases_!lang! WHERE override=0
		', // can't use TRUNCATE cuz it'll kill our overrides
		'insertOrUpdatePhrase' => '
			INSERT INTO 
				!prefix!languages_phrases_!lang!
				(phrase,text,module,isAdmin)
			VALUES
				(:phrase,:text,:module,:isAdmin)
			ON DUPLICATE KEY
				UPDATE
					phrase = :phrase,
					text = :text,
					module = :module,
					isAdmin = :isAdmin
				WHERE	
					(SELECT * FROM !prefix!languages_phrases_!lang! WHERE override=0 AND phrase = :phrase AND module = :module AND isAdmin = :isAdmin)
		', 
		'getPhraseByUniqueParams' => '
			SELECT
				*
			FROM
				!prefix!languages_phrases_!lang!
			WHERE
				phrase = :phrase
				AND
				module = :module
				AND
				isAdmin = :isAdmin
			LIMIT
				1
		',
	);
}