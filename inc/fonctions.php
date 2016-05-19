<?php 

/*
* @param int $id L'id à tester
* @return mixed l'id vérifié (NULL si l'id n'est pas un chiffre)
*/
function checkId($id) {

	$tmpId = NULL;

	if(isset($id) && !empty($id) && is_numeric($id)){
	   $tmpId = $id; 
	}

	return $tmpId;
}

/*
* @param array $array Tableau à nettoyer
* @return array Le tableau nettoyé
*/
function cleanArray($array) {
	
	$tmpArray = [];

	foreach ($array as $key => $value) {
		$tmpArray[$key] = trim(strip_tags($value));
	}

	return $tmpArray;
}

/*
* @param int $id L'id du message à récupérer
* @return array Les information du message
*/
function showMessageContact($id) {

	global $pdo;
    
	$res = $pdo->prepare('SELECT * FROM contact WHERE id = :id');
	$res->bindValue(':id', $id, PDO::PARAM_INT);
	$res->execute();

	return $res->fetch(PDO::FETCH_ASSOC);

}

/*
* @return array Les information du message
*/
function showAllMessageContact() {

	global $pdo;
    
	$res = $pdo->prepare('SELECT * FROM contact');
	$res->execute();

	return $res->fetchAll(PDO::FETCH_ASSOC);

}

/**
 * Fonction permettant de vérifier la longueur d'une chaine de caractères
 * @param string $data La chaine à vérifier
 * @param int $minlength La longueur minimale
 * @param int $maxlength La longueur maximale
 * @return booleen true si la longueur est ok, false sinon
 */
function minOrMaxLength($data, $minlength, $maxlength){
	if(strlen($data) < $minlength || strlen($data) > $maxlength){
		return false;
	}
	else {
		return true;
	}
}