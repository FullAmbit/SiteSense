<?php

function languages_core_en_us(){

	$phrases = array(
		'greeting'			=>			"Hello World",
		'insult'			=>			"Screw you!",
		'apology'			=>			"I'm So sorry"
	);
	
	return array(
		'name' => 'English (US)',
		'shortName' => 'en_us',
		'phrases' => $phrases
	);
}
?>