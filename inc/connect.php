<?php 
//Informations de connexion à la bdd

$sqlHost     = 'localhost'; 		//hôte de la bdd
$sqlUser     = 'root';				//identifiant de connexion à la bdd
$sqlPassword = '';					//mot de passe de connexion à la bdd
$dbName      = 'philogourmand';  	//Nom de la bdd

try{
	$pdo = new PDO('mysql:host='.$sqlHost.';dbname='.$dbName.';charset=utf8',$sqlUser,$sqlPassword) or die($pdo->errorInfo());
}
catch (Exception $e) {
	require_once 'inc/install.php';
}

?>