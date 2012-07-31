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
				phrase ASC
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
		'
	);
}