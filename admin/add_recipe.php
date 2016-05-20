<?php 

session_start();

require_once '../inc/connect.php'; 


$post = array(); // Contiendra les données du formulaire nettoyées
$errors = array(); // contiendra nos éventuelles erreurs

$showErrors = false;
$success = false; 


if (!empty($_POST)) {
	
	foreach ($_POST as $key => $value) { // Nettoyage des données
		$post[$key] = trim(strip_tags($value)); // récupération du _POST dans un tableau
	}
	if(strlen($post['title']) < 2 || strlen($post['title']) > 50){ // on défini les propriétés de 'title'
        $errors[] = '<div class="alert alert-danger" role="alert">Votre nom de recette doit comporter entre 2 et 50 caractères</div>';
    }
    if(strlen($post['content']) < 2 ){ // on défini les propriétés de 'content'
        $errors[] = '<div class="alert alert-danger" role="alert">La recette doit comporter au minimum 2 ingrédients</div>'; 
	}
	else {
	    $reqEmail = $pdo->prepare('SELECT title FROM recipes WHERE title = :title'); // Vérification au cas ou l'email est déjà dans la pdo
        $reqEmail->bindValue(':title', $post['title']);
        $reqEmail->execute();
       
        if($reqEmail->rowCount() != 0){ // Si l'email n'est pas dans la pdo alors, on peu crée l'utilisateur
             $errors[] = '<div class="alert alert-danger" role="alert">La recette existe déjà !</div>';
        }
	} 

	if(count($errors) > 0){  // On compte les erreurs, si il y en as (supérieur a 0), on passera la variable $showErrors à true.
        $showErrors = true; // valeur booleen // permettra d'afficher nos erreurs s'il y en a

        $title = $post['title'];
        $title = $post['content'];
        $title = $post['date_publish'];
    }
    else { 
    	// Insertion dans la pdo 
    	$res = $pdo->prepare('INSERT INTO recipes (title, content, date_publish ) VALUES(:title, :content, NOW())');

        $res->bindValue(':title', $post['title'], PDO::PARAM_STR);
        $res->bindValue(':content', $post['content'], PDO::PARAM_STR);
        
        
    
	    if($res->execute()){
	        $success = true; // Pour afficher le message de réussite si tout est bon
	    }
	    else {
	        die;
	    }
    }
}

if($success){ // On affiche la réussite si tout fonctionne
    echo '<div class="alert alert-success" role="alert"> La recette à bien été créer ! </div>';
}

if($showErrors){
    echo implode('<br>', $errors);
    }
include_once '../inc/header_admin.php';
?>


		<h1 class="text-center">Ajouter une recette</h1>
		<br>
<div class="container">
	<div class="panel panel-default">
		<div class="alert alert-info" role="alert"> Merci de remplire tout les champs correctement</div>	
			<form method="post" class="pure-form pure-form-aligned">
				<div class="form-group input-group">
				  <span class="input-group-addon" id="basic-addon1">Titre</span>
				  <input type="text" class="form-control" name="title" placeholder="Nom de la recette" aria-describedby="basic-addon1">
				</div><br>
				<div class="form-group input-group">
				  <span class="input-group-addon" id="basic-addon1">Ingrédient</span>
				  <textarea id="content" name="content" rows="15" class="form-control input-md" placeholder="Déscriptif complet de la recette pour le client"></textarea>
				</div><br>
			<input type="submit" class="btn btn-primary" value="Ajouter la recette">
			</form>
		
	</div>
</div>
<?php

include_once '../inc/footer_admin.php';

?>