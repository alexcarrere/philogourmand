<?php
$debutSession = session_start();//permet de demarrer la session

require_once '../inc/connect.php';

$post =array();
$error = array();
$mdpValide = false;
$errorSession = false;



if(!empty($_POST)){//01

	$post = array_map('strip_tags', $_POST);
	$post = array_map('trim', $post);


// On vérifie que l'adresse email est au bon format
	if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)){
		$error[] = 'L\'adresse email est invalide';
	}	
	if(empty($post['password'])){

		$error[] = 'vous devez saisir un mot de passe';
	}

			if(count($error) == 0){//02
			$select = $pdo->prepare('SELECT * FROM users INNER JOIN authorization ON users.id = authorization.id_user WHERE email = :checkEmail');//

			$select->bindValue(':checkEmail', $post['email']);
		if($select->execute()){//03

				$user = $select->fetch();//contient notre utilisateur relatif à l'adresse email
			var_dump($user);
		if(!empty($user)){//04
																

		// on vérifie le mot de passe saisi et le mot de passe hashé
		if(password_verify( $post['password'], $user['password'])){
		//ici le mot de passe est valide
		$mdpValide = true;

		$_SESSION['user'] = [

				'id'        => $user['id'],
				'firstname' => $user['firstname'],
				'lastname'  => $user['lastname'],
				'email'     => $user['email'],
				'gender'    => $user['gender'],
				'role'      => $user['role']
								];
		//je redirige vers la page "infos_users.php"


		header('Location: administration.php');
		die;
																								
		}
		else {
		// Le mot de passe est invalide
		$error[] = 'Le couple identifiant/mot de passe est invalide';

							}							
		}//fin 04

		else {
		//utilisateur inconnu
		$error[] = 'Le couple identifiant/mot de passe est invalide';
																	}
					}//fin 03
		}//fin 02

}//fin 01



?>
<!DOCTYPE html>


<html Lang="fr">
<head>
<meta charset="utf-8">
	<title></title>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	
</head>
<body>


<main class="container">
		<h1 class="text-center">Login</h1>
		<br>

<form class="form-horizontal well well-sm" method="post">

			<div class="form-group">
				<label class="col-md-4 control-label" for="email">email</label>  
				<div class="col-md-4">
					<input id="email" name="email" type="email" placeholder="Votre email" class="form-control input-md" required>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-4 control-label" for="password">Mot de passe</label>  
				<div class="col-md-4">
					<input id="password" name="password" type="password" placeholder="Votre password" class="form-control input-md" required>
				</div>
			</div>		

			<div class="form-group">
				<div class="col-md-4 col-md-offset-4">
					<button type="submit" class="btn btn-primary">Je me connecte</button>
				</div>
			</div>

</form>
<a href="lost_password.php">Mot de passe oublié</a>
</main>


</body>
</html>






