<?php
session_start();
if (!empty($_SESSION) && isset($_SESSION['user']['role'])){

	if ($_SESSION['user']['role'] != 'admin') {
		header('Location: administration.php');
	}
	
} else {
	header('Location: ../index.php');
}
require_once '../inc/header_admin.php';
require_once '../inc/connect.php';
$affichageFormulaire = false;
$displayErr = false;
$formValid = false;
$error = array();
/*
S'il y a un slash (/) initial, cherchera le dossier à la racine du site web (localhost), sinon, cherchera dans le dossier courant

*/

$folder = '../img/';
$maxSize = 100000000 * 5;


/*"début de condition"*/

if(!empty($_GET['id']) && $_GET['id'] == 1){

	if(empty($_POST)){
		
		$res = $pdo->prepare('SELECT * FROM resto WHERE id = :id');
		$res->bindValue(':id' ,$_GET['id']  , PDO::PARAM_INT);
			
		if($res->execute()){


		$restaurant = $res->fetch(PDO::FETCH_ASSOC);

		$idRestaurant = $restaurant['id'];
		$title = $restaurant['title'];
		$adress = $restaurant['adress'];
		$zipcode = $restaurant['zipcode'];
		$city = $restaurant['city'];
		$phone = $restaurant['phone'];
		$email_restaurant = $restaurant['email'];
		$picture = $restaurant['link'];
		//unset($_GET['id']);
		}
	}


	if(!empty($_POST)){

		foreach ($_POST as $key => $value) {
		$post[$key] = trim(strip_tags($value));
		}
		

		if(strlen($post['title']) < 3 || strlen($post['title']) > 15){
		$error[] = 'Le titre doit comporter entre 3 et 15 caractères';
		}

		if(strlen($post['adress']) < 3 || strlen($post['adress']) > 50){
			$error[] = 'L \'adresse doit comporter entre 3 et 50 caractères';
		}

		if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)){
			$error[] = 'le contenu ne correspond pas à une adresse email';
		}

		if(empty($post['phone']) && strlen($post['phone']) == 10  && !filter_var($post['phone'], FILTER_VALIDATE_INT)){
			$error[] = 'le numero de telephone n\'est pas valide';
		}
		if(strlen($post['city']) < 3 || strlen($post['city']) > 50){
			$error[] = 'Le ville doit comporter entre 3 et 50 caractères';
		}
		if(strlen($post['zipcode']) != 5){
		$error[] = 'Le code postal doit comporter 5 caractères';
		}

		
		if(count($error) > 0){
			$displayErr = true;
		}
		else {//erreur else si pas d'erreur


			if(!empty($_FILES) && isset($_FILES['pictureDeux'])){
				
					$nomFichier = $_FILES['pictureDeux']['name'];
					
					$newFileName = explode('.', $nomFichier);
					$fileExtension = end($newFileName); // Récupère la dernière entrée du tableau (créé avec explode) soit l'extension du fichier

					        // nom du fichier avatar au format : user-id-timestamp.jpg
					$finalFileName = 'restaurant-'.$post['idRestaurant'].'-'.time().'.'.$fileExtension;

					
					$picture = $finalFileName;
					$tmpFichier = $_FILES['pictureDeux']['tmp_name'];

					/*"A cet endroit, essayez d'utiliser la fonction move_upload_file()
					Pour placer le fichier dans le dossier image... un peu de concatenation :-)"*/

					$newFichier = $folder.$finalFileName;
					if(  $_FILES['pictureDeux']['size'] <= $maxSize){

						if(move_uploaded_file( $tmpFichier , $newFichier  )){

							$success = "Fichier envoyé !!\o/";
							/*$picture = $finalFileName;*/
							/*retourne un boolean true si le fichier a bien été déplacé/envoye
							false si il y a une erreur*/

						}

						else {

							$errorUpdate = 'Erreur lors de l\'envoi de fichier';
						}

				}	
			}

			

			$resUpdate = $pdo->prepare('UPDATE resto SET title = :title, adress = :adress, zipcode = :zipcode, city = :city, phone= :phone, email = :email, link = :link WHERE id= :idRestaurant' );

			$resUpdate->bindValue(':idRestaurant', $post['idRestaurant'], PDO::PARAM_INT);
			$resUpdate->bindValue(':title', $post['title'], PDO::PARAM_STR);
			$resUpdate->bindValue(':adress', $post['adress'], PDO::PARAM_STR);
			$resUpdate->bindValue(':zipcode', $post['zipcode'], PDO::PARAM_STR);
			$resUpdate->bindValue(':city', $post['city'], PDO::PARAM_STR);
			$resUpdate->bindValue(':phone', $post['phone'], PDO::PARAM_STR);
			$resUpdate->bindValue(':email', $post['email'], PDO::PARAM_STR);
			$resUpdate->bindValue(':link', $finalFileName, PDO::PARAM_STR);//$finalFileName




			// On execute notre requete et si tout est ok, on créer une variable qui contient un message de confirmation
			if($resUpdate->execute()){
				/*$articleIsUpdate = 'Contact modifié avec succès';*/
				
			$formValid = true;
			$idRestaurant = $post['idRestaurant'];
			$title = $post['title'];
			$adress = $post['adress'];
			$zipcode = $post['zipcode'];
			$city = $post['city'];
			$phone = $post['phone'];
			$email_restaurant = $post['email'];
			$picture = $finalFileName;
			}

		}//fin erreur else
	}
?>

<?php
	if(isset($success)){
		echo '<div class="alert alert-success">';
		echo $success; // Affiche le message de réussite de l'envoi du fichier image
		echo '</div>';
	}
	if(isset($errorUpdate)){
		echo '<div class="alert alert-error">';
		echo $errorUpdate; // Affiche le message d'erreur de l'envoi du fichier image
		echo '</div>';
	}
	if($formValid){//Affiche le message de réussite de la mise à jour
		echo '<div class="alert alert-success">';
		echo 'le resto est modifié';
		echo '</div>';
	}

	if($displayErr){ // Si on a des erreurs, on les affiche
		echo '<div class="alert alert-error">';
		echo implode('<br>', $error); // Permet de convertir le tableau $error en chaine de caractère
		echo '</div>';
	}
?>




<form class="form-horizontal" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="idRestaurant" value="<?php echo $idRestaurant ?>">
	<input type="hidden" name="veryFinalFileName" value="<?php echo $finalFileName ?>">

	<div class="form-group">
		<label class="col-md-4 control-label" for="title">Titre : </label>
		<div class="col-md-4">
			<input class="form-control" type="text" name="title" value="<?php echo $title ?>" required>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-4 control-label" for="adress">Adresse : </label>
		<div class="col-md-4">
			<input class="form-control" type="text" name="adress" value="<?php echo $adress ?>" required>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-4 control-label" for="zipcode">Code postal : </label>
		<div class="col-md-4">
			<input class="form-control" type="text" name="zipcode" value="<?php echo $zipcode ?>" required>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-4 control-label" for="city">Ville : </label>
		<div class="col-md-4">
			<input class="form-control" type="text" name="city" value="<?php echo $city ?>" required>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-4 control-label" for="phone">Telephone : </label>
		<div class="col-md-4">
			<input class="form-control" type="text" name="phone" value="<?php echo $phone ?>" required>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-4 control-label" for="email">Email : </label>
		<div class="col-md-4">
			<input class="form-control" type="email" name="email" value="<?php echo $email_restaurant ?>" required>
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-4 control-label" for="image">Image : </label>
		<div class="col-md-4">
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxSize; ?>">
			<input type="file" class="filestyle" data-buttonName="btn-primary" name="pictureDeux" value="<?php echo $picture; ?>">
		</div>	
	</div>

	<div class="form-group">
		<div class="col-md-4 col-md-offset-4">
			<button type="submit" id="btnSubmit" class="btn btn-success">Modifier</button>
		</div>
	</div>

</form>

 
<?php
}
else {

?>

<p>il n'y a pas d'identifiant</p> 

<?php

}

require_once '../inc/footer_admin.php';
?>