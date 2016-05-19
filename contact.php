<?php
// Je me connecte à la bdd
require_once 'inc/connect.php';

/************************************  Partie traitement du formulaire     ***************************************************/
// Je déclare mes variables :
$post = array(); // tabl qui contiendra les données du formulaire nettoyées 
$errors = array(); // tabl qui contiendra les éventuelles erreurs
$success = false; // Passera à true s'il n'a pas d'erreurs et permettra d'afficher un message de réussite
$showError = false; // Affichera les messages d'erreurs s'il y en a

// Je vérifie la soumission du formulaire 
if(!empty($_POST)){ // vérifie que $_POST est définie et non vide :          
// var_dump($_POST); // vérif pour moi pour voir mon tableau d'origine
	
	// On nettoie les données 
	foreach($_POST as $key => $value){
		$post[$key] = trim(strip_tags($value)); // on récupère du tableau initial un nouveau tableau perso avec mes nouvelles données sans html ni espaces... puis on vérifie
	}
	// On commence nos vérifications :

	if(strlen($post['lastname']) < 3 || strlen($post['lastname']) > 25){
        $errors[] = 'Le nom doit comporter entre 3 et 25 caractères';
    }

	if(strlen($post['firstname']) < 3 || strlen($post['firstname']) > 25){
        $errors[] = 'Le prénom doit comporter entre 3 et 25 caractères';
    }

    if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)){ // si la syntaxe n'est pas bonne
        $errors[] = 'L\'adresse email est invalide';
    }
	
	if(count($errors) > 0){ // On compte les erreurs, si elles sont supérieures à 0, on passe la variable $showErrors à true, afin de pouvoir les afficher
		$showError = true; // permettra d'afficher nos erreurs s'il y en a
	}
	else { //sinon, s'il n'y a pas d'erreur au vu du if précédent "if(count($error))"" :			
		
        $requete = $pdo->prepare('INSERT INTO contact (firstname, lastname, email, content, date_add, message_state) VALUES (:firstnameInser, :lastnameInser, :emailInser, :contentInser, NOW(), "unread")');

        $requete->bindValue(':firstnameInser',   $post['firstname']);
        $requete->bindValue(':lastnameInser',   $post['lastname']);
        $requete->bindValue(':emailInser',  $post['email']);
        $requete->bindValue(':contentInser',  $post['content']);

        if($requete->execute()){  // Si la requete s'exécute correctement
            $success = true;
        }
	}		
	
	//var_dump($error);
}

//var_dump($post) pour voir les nouvelles données




?>

<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Formulaire</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>

	<main class="container">
		<h1 class="text-center">Formulaire</h1>
		<br>
		<!-- Message suite traitement formulaire -->
		<?php if($showError == true): ?>
            <p style="color:red">Veuillez corriger les erreurs suivantes :</p>
         		<ul style="color:red">
        		<?php foreach($errors as $err): ?>
                    <li><?=$err;?></li>
                <?php endforeach;?>
                </ul>
        <?php endif; ?>

        <?php if($success == true): ?>
            <p style="color:green">Ok, le formulaire est valide.</p>
        <?php endif; ?>

		<form class="form-horizontal well well-sm" method="post" action="">

			<div class="form-group">
				<label class="col-md-4 control-label" for="lastname">Nom</label>  
				<div class="col-md-4">
					<input id="lastname" name="lastname" type="text" placeholder="Votre nom de famille" class="form-control input-md" required>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-4 control-label" for="firstname">Prénom</label>  
				<div class="col-md-4">
					<input id="firstname" name="firstname" type="text" placeholder="Votre prénom" class="form-control input-md" required>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-4 control-label" for="email">Adresse email</label>  
				<div class="col-md-4">
					<input id="email" name="email" type="text" placeholder="votreadresse@email.fr" class="form-control input-md" required>
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-4 control-label" for="">Contenu</label>
				<div class="col-md-4">
					<textarea name="content" rows="10" cols="50" placeholder="Saisir un texte ici..." ></textarea>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-4 col-md-offset-4">
					<button type="submit" class="btn btn-success">Envoyer</button>
				</div>
			</div>

	</form>
	</main>


</body>
</html>