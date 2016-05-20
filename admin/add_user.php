<?php 

session_start();

require_once '../inc/connect.php'; 


$post = array(); // Contiendra les données du formulaire nettoyées
$errors = array(); // contiendra nos éventuelles erreurs
$allowRole = ['editor', 'admin'];

$showErrors = false;
$success = false; 


if (!empty($_POST)) {
	
	foreach ($_POST as $key => $value) { // Nettoyage des données
		$post[$key] = trim(strip_tags($value)); // récupération du _POST dans un tableau
	}
	if(strlen($post['nickname']) < 2 || strlen($post['nickname']) > 50){ // on défini les propriétés de 'nickname'
        $errors[] = '<div class="alert alert-danger" role="alert">Votre pseudo doit comporter entre 2 et 50 caractères</div>';
    }
    if(strlen($post['firstname']) < 2 || strlen($post['firstname']) > 50){ // on défini les propriétés de 'firstname'
        $errors[] = '<div class="alert alert-danger" role="alert">Votre prénom doit comporter entre 2 et 50 caractères</div>';
    }
    if(strlen($post['lastname']) <2 || strlen($post['lastname']) >50 ){
    	$errors[] = '<div class="alert alert-danger" role="alert">Votre nom doit comporter entre 2 et 50 caractères</div>';
    }
    if(empty($post['email']) || !filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
    	$errors[] = '<div class="alert alert-danger" role="alert">Votre email n\'est pas valide</div>';
    }
    if(isset($post['password']) && !empty($post['password']) && strlen($post['email']) < 6) {
    	$errors[] = '<div class="alert alert-danger" role="alert">Votre mot de passe n\'est pas valide</div>';
    }
	if(empty($post['role']) || !in_array($post['role'], $allowRole)){ // On vérifie que le genre est bien valide
		$errors[] = 'le role de l\'utilisateur est invalide';
	}
	else {
	    $reqEmail = $pdo->prepare('SELECT email FROM users WHERE email = :email'); // Vérification au cas ou l'email est déjà dans la pdo
        $reqEmail->bindValue(':email', $post['email']);
        $reqEmail->execute();
       
        if($reqEmail->rowCount() != 0){ // Si l'email n'est pas dans la pdo alors, on peu crée l'utilisateur
             $errors[] = '<div class="alert alert-danger" role="alert">L\'email existe déjà !</div>';
        }
	} 

	if(count($errors) > 0){  // On compte les erreurs, si il y en as (supérieur a 0), on passera la variable $showErrors à true.
        $showErrors = true; // valeur booleen // permettra d'afficher nos erreurs s'il y en a

        $nickname = $post['nickname'];
        $nickname = $post['firstname'];
        $lastname = $post['lastname'];
        $password = $post['password'];
        $email = $post['email'];
        $role = $post['role'];
    }
    else { 
    	// On sécurise notre password en le hashant
    	// IMPORTANT : On ne stocke jamais de mot de passe en clair en pdo
    	$password = password_hash($post['password'], PASSWORD_DEFAULT);

		// Insertion dans la pdo 
    	$res = $pdo->prepare('INSERT INTO users (nickname, firstname, lastname, email, password, date_reg ) VALUES(:nickname, :firstname, :lastname, :email, :password, NOW())');

        $res->bindValue(':nickname', $post['nickname'], PDO::PARAM_STR);
        $res->bindValue(':firstname', $post['firstname'], PDO::PARAM_STR);
        $res->bindValue(':lastname', $post['lastname'], PDO::PARAM_STR);
        $res->bindValue(':email', $post['email'], PDO::PARAM_STR);
        $res->bindValue(':password', $password);
        
        

         if($res->execute()){
	        $success = true; // Pour afficher le message de réussite si tout est bon
	    }
	    else {
	        die(var_dump($res->errorInfo()));
	    }






        $resRecupId = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $resRecupId->bindValue(':email', $post['email'], PDO::PARAM_STR);


		if($resRecupId->execute()){

	// $article contient mon article extrait de la bdd
			$idRecupIdTableau = $resRecupId->fetch(PDO::FETCH_ASSOC);
			$idRecupId = $idRecupIdTableau['id'];

		}
		else {

			die(var_dump($resRecupId->errorInfo()));

		}	


   

	    $resAuthorisation = $pdo->prepare('INSERT INTO authorization (role, id_user ) VALUES( :role, :id_last) ');

	    $resAuthorisation->bindValue(':role', $post['role'], PDO::PARAM_STR);
	    $resAuthorisation->bindValue(':id_last', $idRecupId, PDO::PARAM_INT );
	    if($resAuthorisation->execute()){
	        $successAuthorisation = true; // Pour afficher le message de réussite si tout est bon
	    }
	    else {
	        die(var_dump($res->errorInfo()));
	    }

    }
}

if($success == 'true' && $successAuthorisation == 'true'){ // On affiche la réussite si tout fonctionne
    echo '<div class="alert alert-success" role="alert"> L\'utilisateur est bien créer ! </div>';
}

if($showErrors){
    echo implode('<br>', $errors);
    }
include_once '../inc/header_admin.php';
?>


		<h1 class="text-center">Ajouter un utilisateur</h1>
		<br>
<div class="container">
	<div class="panel panel-default">
		<div class="alert alert-info" role="alert"> Merci de remplire tout les champs correctement</div>	
			<form method="post" class="pure-form pure-form-aligned">
				<div class="form-group input-group">
				  <span class="input-group-addon" id="basic-addon1">Pseudal</span>
				  <input type="text" class="form-control" name="nickname" placeholder="Votre prénom" aria-describedby="basic-addon1">
				</div><br>
				<div class="form-group input-group">
				  <span class="input-group-addon" id="basic-addon1">Prenom</span>
				  <input type="text" class="form-control" name="firstname" placeholder="Votre prénom" aria-describedby="basic-addon1">
				</div><br>
				<div class="form-group input-group">
				  <span class="input-group-addon" id="basic-addon1">Nom</span>
				  <input type="text" class="form-control" name="lastname" placeholder="Votre nom" aria-describedby="basic-addon1">
				</div><br>
				<div class="form-group input-group">
				  <span class="input-group-addon" id="basic-addon1">Password</span>
				  <input type="password" class="form-control" name="password" placeholder="Votre mot de passe" aria-describedby="basic-addon1">
				</div><br>
				<div class="form-group input-group">
				  <span class="input-group-addon" id="basic-addon1">@</span>
				  <input type="text" class="form-control" name="email" placeholder="Votre email" aria-describedby="basic-addon1">
				</div><br>
				<div class="form-inline">
					<label class="col-md-4 control-label" for="role">Rôle</label>
				<div class="col-md-4">
					<div class="radio">
						<label for="role-0">
							<input type="radio" name="role" id="role-0" value="editor"> Editeur
						</label>
					</div>
					<div class="radio">
						<label for="role-1">
							<input type="radio" name="role" id="role-1" value="admin"> Administrateur
						</label>
					</div>
				</div>
				</div>
			<input type="submit" class="btn btn-primary" value="S'inscrire">
			</form>
		
	</div>
</div>
<?php

include_once '../inc/footer_admin.php';

?>