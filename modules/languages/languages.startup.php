<?php

function languages_startup($data,$db){
	// Load Languages
    $statement = $db->prepare("getAllLanguages","common");
    $statement->execute();
    $data->languageList = $db->languageList = $statement->fetchAll(PDO::FETCH_ASSOC);
}