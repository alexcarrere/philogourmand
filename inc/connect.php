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
	
	//Connexion à mysql
	$pdo = new PDO('mysql:host='.$sqlHost,$sqlUser,$sqlPassword);

	//Création de la bdd si elle n'existe pas déja
	$req = $pdo->exec("CREATE DATABASE IF NOT EXISTS ".$dbName." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

	if($req === false) {

		die(var_dump($pdo->errorInfo()));

	} 

	$pdo = new PDO('mysql:host='.$sqlHost.';dbname='.$dbName.';charset=utf8',$sqlUser,$sqlPassword);

	//Création de la table users si elle n'existe pas déja
	$req = $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
	  	`id` int(11) NOT NULL AUTO_INCREMENT,
	  	`nickname` varchar(50) NOT NULL,
	  	`firstname` varchar(50) NOT NULL,
	  	`lastname` varchar(50) NOT NULL,
	  	`email` varchar(255) NOT NULL,
	  	`password` varchar(255) NOT NULL,
	  	`date_reg` datetime NOT NULL,
	  	PRIMARY KEY (`id`), 
		UNIQUE (`email`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

	if($req === false) {

		die(var_dump($pdo->errorInfo()));

	} else {
		//Ajout de l'utilisateur par défaut (email : "lorem@ipsum.fr",  password : "password")
		$ins = $pdo->exec("INSERT INTO `users` (`nickname`, `firstname`, `lastname`, `email`, `password`, `date_reg`) VALUES (
			'PhiloAdmin', 'Brice', 'Nice', 'lorem@ipsum.fr', '\$2y\$10\$dH5//h7acd7N1PE2RJ8fW.iF8lkUE8qUZA7gkD6SIkYRjYjeX2ofa', '2016-05-26 00:00:00'
		);");

		// Si $req retourne false alors on affiche l'erreur
		if($ins === false) {

			die(var_dump($pdo->errorInfo()));

		} 

	}

	//Création de la table authorization si elle n'existe pas déja
	$req = $pdo->exec("CREATE TABLE IF NOT EXISTS `authorization` (
	  	`id` int(11) NOT NULL AUTO_INCREMENT,
	  	`role` enum('admin','editor') NOT NULL,
	  	`id_user` int(11) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

	if($req === false) {

		die(var_dump($pdo->errorInfo()));

	} else {

		//Ajout des droits de l'utilisateur par défault
		$ins = $pdo->exec("INSERT INTO `authorization` (`role`, `id_user`) VALUES ('admin', 1)");

		// Si $req retourne false alors on affiche l'erreur

		if($ins === false) {

			die(var_dump($pdo->errorInfo()));

		} 

	}

	//Création de la table contact si elle n'existe pas déja
	$req = $pdo->exec("CREATE TABLE IF NOT EXISTS `contact` (
	  	`id` int(11) NOT NULL AUTO_INCREMENT,
	  	`firstname` varchar(50) NOT NULL,
	  	`lastname` varchar(50) NOT NULL,
	  	`email` varchar(255) NOT NULL,
	  	`content` text NOT NULL,
	  	`date_add` datetime NOT NULL,
	  	`message_state` enum('read','unread') NOT NULL DEFAULT 'unread',
	  	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

	if($req === false) {

		die(var_dump($pdo->errorInfo()));

	}

	//Création de la table recipes si elle n'existe pas déja
	$req = $pdo->exec("CREATE TABLE IF NOT EXISTS `recipes` (
	  	`id` int(11) NOT NULL AUTO_INCREMENT,
	  	`title` varchar(255) NOT NULL,
	  	`content` text NOT NULL,
	  	`link` varchar(255) NOT NULL,
	  	`date_publish` datetime NOT NULL,
	  	`id_user` int(11) NOT NULL,
	  	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

	if($req === false) {

		die(var_dump($pdo->errorInfo()));

	}

	//Création de la table tokens_password si elle n'existe pas déja
	$req = $pdo->exec("CREATE TABLE `tokens_password` (
	  	`id` int(11) NOT NULL AUTO_INCREMENT,
	  	`email` varchar(255) NOT NULL,
	  	`token` varchar(255) NOT NULL,
	  	`date_create` datetime NOT NULL,
	  	`date_exp` datetime NOT NULL,
	  	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

	if($req === false) {

		die(var_dump($pdo->errorInfo()));

	}

	//Création de la table resto si elle n'existe pas déja
	$req = $pdo->exec("CREATE TABLE IF NOT EXISTS `resto` (
	  	`id` int(11) NOT NULL AUTO_INCREMENT,
	  	`link` varchar(255) NOT NULL,
	  	`adress` varchar(255) NOT NULL,
	  	`zipcode` varchar(5) NOT NULL,
	  	`city` varchar(50) NOT NULL,
	  	`email` varchar(255) NOT NULL,
	  	`phone` varchar(10) NOT NULL,
	  	`title` varchar(255) NOT NULL,
	  	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

	if($req === false) {

		die(var_dump($pdo->errorInfo()));

	} else {

		//Ajout des droits de l'utilisateur par défault
		$ins = $pdo->exec("INSERT INTO `resto` (
			`id`, `link`, `adress`, `zipcode`, `city`, `email`, `phone`, `title`) VALUES (
			1, 'default.jpg', '66 Rue Abbé de l\'Épée', '33130', 'Bordeaux', 'postmaster@philogourmand.fr', '0011223344', 'Philogourmand')"
		);

		// Si $req retourne false alors on affiche l'erreur

		if($ins === false) {

			die(var_dump($pdo->errorInfo()));

		} 

	}

 
}
?>