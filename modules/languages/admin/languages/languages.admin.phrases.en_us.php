
<?php

function languages_languages_admin_en_us(){
	return array(
		'core' => array(
			'languages'                        => 'Languages',
			'permission_languages_access'      => 'Access Languages',
			'permission_languages_list'        => 'List Languages',
			'permission_languages_addPhrase'   => 'Add Phrase',
			'permission_languages_default'     => 'Set Default Language',
			'permission_languages_editPhrase'  => 'Edit Phrase',
			'permission_languages_listPhrases' => 'List Phrases',
			'permission_languages_update'      => 'Update Phrases'
		),
		'manageLanguagesHeading'     => 'Installed Languages',
		'phrase'                     => 'Phrase',
		'phrases'                    => 'Phrases',
		'module'                     => 'Module',
		'text'                       => 'Text',
		'addAPhrase'                 => 'Add A Phrase',
		'default'                    => 'Default',
		'language'                   => 'Language',
		'updateButton'               => 'Update From FileSystem',
		'languageNotFound'           => 'The language you specified could not be found.',
		'captionAddPhrase'           => 'Add A Phrase For',
		'captionEditPhrase'          => 'Edit The Phrase',
		'addPhraseSuccess'           => 'The phrase was successfully added.',
		'editPhraseSuccess'          => 'The phrase was successfully updated.',
		'captionUpdate'              => 'Update Language',
		'updateActionClear'          => 'Clear Existing Phrases And Start Fresh',
		'updateActionNew'            => 'Only Install Phrases That Don\'t Currently Exist',
		'updateActionAll'            => 'Update Existing Phrases And Install New Ones',
		'phraseAction'               => 'Phrase Action',
		'updateModuleLanguages'      => 'Update Module Languages?',
		'install'                    => 'Install Language',
		'updateLanguageSuccess'      => 'The language was successfully updated.',
		'makeDefault'                => 'Make Default',
		'submitPhraseItemForm'       => 'Save Phrase',
		'phraseExistsForModule'      => 'The phrase you specified already exists for this module.',
		'saveToDBError'              => 'There was an error in saving to the database.',
		'alreadyDefault'             => 'This language is already the default.',
		'disableDefaultError'        => 'There was an error in disabling the previous default language.',
		'setDefaultError'            => 'There was an error in setting the new default language.',
		'setDefaultSuccess'          => 'The new default language has been set successfully.',
		'missingCoreInstallerFile'   => 'The language you selected does not have a core installer file.',
		'createPhraseTableError'     => 'There was an error in creating the phrases table for the langauge.',
		'addLanguageDBError'         => 'There was an error in adding the language to the database.'
	);
}
?>