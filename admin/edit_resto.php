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
		$picture = explode(',',$restaurant['link']); //tableau contenant les images stockées en base de données
		//unset($_GET['id']);
		}
	}


	if(!empty($_POST)){

		foreach ($_POST as $key => $value) {
		$post[$key] = trim(strip_tags($value));
		}
		

		if(!preg_match ( "#^[a-zA-Z0-9À-ú'\s_-]{5,140}$#" , $post['title'] )){
			$error[] = 'Le titre doit comporter entre 5 et 140 caractères';
		}

		/*if(strlen($post['adress']) < 3 || strlen($post['adress']) > 50){*/
		/*if(!preg_match ( "#^[a-zA-ZÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ_-]{3,50}$#"  , $post['adress'] )){*/
		if(!preg_match ( "#^[a-zA-Z0-9À-ú'\s]{5,60}$#"  , $post['adress'] )){
			$error[] = 'L \'adresse doit comporter entre 3 et 50 caractères';
		}

		if(!preg_match ( " /^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ " , $post['email'] )){
			$error[] = 'le contenu ne correspond pas à une adresse email';
		}

		if(!preg_match ( "#^[0-9]{10}$#" , $post['phone'] )){
		/*if(empty($post['phone']) && strlen($post['phone']) == 10  && !filter_var($post['phone'], FILTER_VALIDATE_INT)){*/
			$error[] = 'le numero de téléphone n\'est pas valide';
		}
		if(!preg_match ( "#^[a-zA-ZÀ-ú'\s_-]{3,50}$#" , $post['city'] )){
			$error[] = 'Le ville doit comporter entre 3 et 50 caractères';
		}
		if(!preg_match ( "#^[0-9]{5}$#" , $post['zipcode'] )){
		$error[] = 'Le code postal doit comporter 5 caractères';
		}

		
		if(count($error) > 0){
			$displayErr = true;

			$idRestaurant = $post['idRestaurant'];
			$title = $post['title'];
			$adress = $post['adress'];
			$zipcode = $post['zipcode'];
			$city = $post['city'];
			$phone = $post['phone'];
			$email_restaurant = $post['email'];
			$picture = array($post['pictureUn'],$post['pictureDeux'],$post['pictureTrois']);
		}
		else {//erreur else si pas d'erreur


			if(!empty($_FILES) && $_FILES['pictureUn']['error'] != 4 && $_FILES['pictureDeux']['error'] != 4 && $_FILES['pictureTrois']['error'] != 4){

				foreach ($_FILES as $file) {

					$nomFichier = $file['name'];
					
					$newFileName = explode('.', $nomFichier);
					$fileExtension = end($newFileName); // Récupère la dernière entrée du tableau (créé avec explode) soit l'extension du fichier

					        // nom du fichier avatar au format : user-id-timestamp.jpg
					$finalFileName = 'restaurant-'.$post['idRestaurant'].'-'.md5(uniqid()).'.'.$fileExtension;

					$picture = $finalFileName;
					$tmpFichier = $file['tmp_name'];

					/*"A cet endroit, essayez d'utiliser la fonction move_upload_file()
					Pour placer le fichier dans le dossier image... un peu de concatenation :-)"*/

					$newFichier = $folder.$finalFileName;
					if( $file['size'] <= $maxSize){

						if(move_uploaded_file( $tmpFichier , $newFichier  )){

							$success = "Fichier envoyé !!\o/";
							$arrayFinalfilename[] = $finalFileName;


							/*$picture = $finalFileName;*/
							/*retourne un boolean true si le fichier a bien été déplacé/envoye
							false si il y a une erreur*/

						}
						else {
							$errorUpdate = 'Erreur lors de l\'envoi de fichier';
						}

					}
				}	
			}
			else {//si l'image n'est pas modifier

				$arrayFinalfilename = array($post['pictureUn'],$post['pictureDeux'],$post['pictureTrois']);
			}
			

			$resUpdate = $pdo->prepare('UPDATE resto SET title = :title, adress = :adress, zipcode = :zipcode, city = :city, phone= :phone, email = :email, link = :link WHERE id= :idRestaurant' );

			$resUpdate->bindValue(':idRestaurant', $post['idRestaurant'], PDO::PARAM_INT);
			$resUpdate->bindValue(':title', $post['title'], PDO::PARAM_STR);
			$resUpdate->bindValue(':adress', $post['adress'], PDO::PARAM_STR);
			$resUpdate->bindValue(':zipcode', $post['zipcode'], PDO::PARAM_STR);
			$resUpdate->bindValue(':city', $post['city'], PDO::PARAM_STR);
			$resUpdate->bindValue(':phone', $post['phone'], PDO::PARAM_STR);
			$resUpdate->bindValue(':email', $post['email'], PDO::PARAM_STR);
			$resUpdate->bindValue(':link', implode(',',$arrayFinalfilename), PDO::PARAM_STR);//$finalFileName




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
			$picture = $arrayFinalfilename;
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

	<br>

	<div class="row">
		<div class="form-inline">
			<label class="col-md-4 control-label" for="image">Image 1: </label>
			<div class="col-md-4">
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxSize; ?>">

			
				<!-- <input type="file" class="filestyle" data-buttonName="btn-primary" name="pictureDeux"  value="<?php //echo $picture ?>"> -->

				<input id="browse1" type="file" name="pictureUn" value="<?php echo $picture[0] ?>" accept="image/*" onchange="previewImage(event)"> 
				<input type="text" id="nomFichier1" readonly="true" name="pictureUn" value="<?php echo $picture[0] ?>" class="form-control">

				  <button type="button" id="fakeBrowser1" class="btn btn-primary"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;choisir un fichier</button>

				<!-- <input type="button" id="fakeBrowser" value="choisir un fichier" class="btn btn-success"> -->
				
			
			</div>
		</div>
	</div>

	<br>

	<div class="row">
		<div class="form-inline">
			<label class="col-md-4 control-label" for="image">Image 2: </label>
			<div class="col-md-4">
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxSize; ?>">

			
				<!-- <input type="file" class="filestyle" data-buttonName="btn-primary" name="pictureDeux"  value="<?php //echo $picture ?>"> -->

				<input id="browse2" type="file" name="pictureDeux" value="<?php echo $picture[1] ?>" accept="image/*" onchange="previewImage(event)"> 
				<input type="text" id="nomFichier2" readonly="true" name="pictureDeux" value="<?php echo $picture[1] ?>" class="form-control">

				  <button type="button" id="fakeBrowser2" class="btn btn-primary"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;choisir un fichier</button>

				<!-- <input type="button" id="fakeBrowser" value="choisir un fichier" class="btn btn-success"> -->
				
			
			</div>
		</div>
	</div>

	<br>

	<div class="row">
		<div class="form-inline">
			<label class="col-md-4 control-label" for="image">Image 3: </label>
			<div class="col-md-4">
				<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxSize; ?>">

			
				<!-- <input type="file" class="filestyle" data-buttonName="btn-primary" name="pictureDeux"  value="<?php //echo $picture ?>"> -->

				<input id="browse3" type="file" name="pictureTrois" value="<?php echo $picture[2] ?>" accept="image/*" onchange="previewImage(event)"> 
				<input type="text" id="nomFichier3" readonly="true" name="pictureTrois" value="<?php echo $picture[2] ?>" class="form-control">

				  <button type="button" id="fakeBrowser3" class="btn btn-primary"><span class="glyphicon glyphicon-folder-open"></span>&nbsp;choisir un fichier</button>

				<!-- <input type="button" id="fakeBrowser" value="choisir un fichier" class="btn btn-success"> -->
				
			
			</div>
		</div>
	</div>

	<br>
	<div class="form-group">

		<label class="col-md-4 control-label" for="email"><br></label>
	
		<div class="col-md-4 col-md-offset-4">
			<button type="submit" id="btnSubmit" class="btn btn-success">Modifier</button>
		</div>
	</div>

</form>
<p id="demo"></p>


<!-- <input type="file" accept="image/*" onchange="previewImage(event)" name="picture"> -->
<p id="output"></p> 

<script>
	var fileInput1 = document.getElementById("browse1");
	var fileInput2 = document.getElementById("browse2");
	var fileInput3 = document.getElementById("browse3");
	var textInput1 = document.getElementById("nomFichier1");
	var textInput2 = document.getElementById("nomFichier2");
	var textInput3 = document.getElementById("nomFichier3");
	var fauxBouton1 =  document.getElementById("fakeBrowser1");
	var fauxBouton2 =  document.getElementById("fakeBrowser2");
	var fauxBouton3 =  document.getElementById("fakeBrowser3");
	
	
	fauxBouton1.addEventListener("click", function(){clicBrowser(1)});
	fauxBouton2.addEventListener("click", function(){clicBrowser(2)});
	fauxBouton3.addEventListener("click", function(){clicBrowser(3)});
	fileInput1.addEventListener("change", function(){modifNomFichier(1)});
	fileInput2.addEventListener("change", function(){modifNomFichier(2)});
	fileInput3.addEventListener("change", function(){modifNomFichier(3)});



	function clicBrowser(num){
		switch(num) {
			case 1 : fileInput1.click();
				break;
			case 2 : fileInput2.click();
				break;
			case 3 : fileInput3.click();
				break;
			default: console.log('clicBrowser '+num)
		}
		
	}

	function modifNomFichier(num){
		switch(num) {
			case 1 : textInput1.value = fileInput1.value;
				break;
			case 2 : textInput2.value = fileInput2.value;
				break;
			case 3 : textInput3.value = fileInput3.value;
				break;
			default: console.log('modifNomFichier '+num)
		}
		
	}

/*	var previewImage = function(event) {
	 	var fakeImage = URL.createObjectURL(event.target.files[0]); 

	 	var fileName = document.getElementById('nomFichier');
	 	fileName.value = fakeImage;

	 	// Remplit la prévisualisation
	    var output = document.getElementById('demo');
   		output.innerHTML = '<img class="img-responsive" src="' + fakeImage +'" alt="photo_couverture" >';

	};*/
</script>

<?php
}
else {

?>

<p>il n'y a pas d'identifiant</p> 

<?php

}

require_once '../inc/footer_admin.php';
?>