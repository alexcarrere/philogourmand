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
$formValid = false;
/*
S'il y a un slash (/) initial, cherchera le dossier à la racine du site web (localhost), sinon, cherchera dans le dossier courant

*/

$folder = '../img/';
$maxSize = 100000000 * 5;


/*"début de condition"*/

if(!empty($_GET['id'])){

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

		$error = 'Erreur lors de l\'envoi de fichier';
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
		$resUpdate->bindValue(':link', $finalFileName, PDO::PARAM_STR);




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


		}







//chargement de l'image




?>




	<style>
	#browse {

		display: none;
	}

</style>


<?php 


		if(isset($success)){
			echo $success; // Affiche le message de réussite
		}
		if(isset($error)){
			echo $error; // Affiche le message d'erreur
		}
		if($formValid){


			echo 'le resto est modifié';
		}
?>



<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="idRestaurant" value="<?php echo $idRestaurant ?>">
<input type="hidden" name="veryFinalFileName" value="<?php echo $finalFileName ?>">
<label>Titre : </label>
<input type="text" name="title" value="<?php echo $title ?>">
<br>
<label>Adresse : </label>
<input type="text" name="adress" value="<?php echo $adress ?>">
<br>
<label>Code postal : </label>
<input type="text" name="zipcode" value="<?php echo $zipcode ?>">
<br>
<label>Ville : </label>
<input type="text" name="city" value="<?php echo $city ?>">
<br>
<label>Telephone : </label>
<input type="text" name="phone" value="<?php echo $phone ?>">
<br>
<label>Email : </label>
<input type="email" name="email" value="<?php echo $email_restaurant ?>">
<br>

<label>Image : </label>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxSize; ?>">

<input id="browse" type="file" name="pictureDeux" value="<?php echo $picture ?>"> 
<input type="text" id="nomFichier" readonly="true" name="pictureDeux" value="<?php echo $picture ?>">
<input type="button" id="fakeBrowser" value="choisir un fichier">
<br>
<input type="submit" id="btnSubmit">

</form>





 <script>
	var fileInput = document.getElementById("browse");
	var textInput = document.getElementById("nomFichier");
	var fauxBouton =  document.getElementById("fakeBrowser");
	/*var vraiBouton = document.getElementById("btnSubmit");
	var tmp = document.getElementById("tmp");*/
	
	fauxBouton.addEventListener("click", clicBrowser);
	fileInput.addEventListener("change", modifNomFichier);
	/*vraiBouton.addEventListener("click", clicBtn);*/



	function clicBrowser(){

		fileInput.click();
	}

	function modifNomFichier(){

		/*document.getElementById('demo').innerHTML = '<img src="' + fileInput.value +'" />';*/
		document.getElementById('demo').innerHTML = fileInput.value;
		textInput.value = fileInput.value;
	}

	

	 /*var previewImage = function(event) {
	 	var fakeImage = URL.createObjectURL(event.target.files[0]); 

	 	var fileName = document.getElementById('nomFichier');
	 	fileName.value = fakeImage;

	 	// Remplit la prévisualisation
	    var output = document.getElementById('demo');
   		output.innerHTML = '<img src="' + fakeImage +'" alt="">';
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