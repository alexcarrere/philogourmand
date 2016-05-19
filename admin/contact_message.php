<?php
session_start();
require_once '../inc/connect.php';
require_once '../inc/fonctions.php';

$post = []; //Variable qui contiendra les données de la variable $_POST
$error = []; //Tableau qui contiendra les messages d'erreurs 
$showMessage = false; //Variable qui servira à afficher un message seul
$showAllMessages = false; //Variable qui servira à afficher la liste de tous les messages
$deleteConfirm = false; //Variable qui servira à confirmer la suppression
$repMessage = false; //Variable qui servira à afficher le formulaire de réponse

if (!empty($_SESSION) && isset($_SESSION['user']['role'])){

	if ($_SESSION['user']['role'] != 'admin') {
		header('Location: administration.php');
	}
	
} else {
	header('Location: ../index.php');
}

//On éxécute le code seulement si on à eu une requête Ajax
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

	//Si on a bien reçu les données en POST
	if (!empty($_POST)) {

		//on les nettoye puis on les mets dans $post 
		$post = cleanArray($_POST);

		//Vérification du format de l'email
		if (!filter_var($post['email'], FILTER_VALIDATE_EMAIL))  {
			$error[] = 'l\'adresse mail n\'est pas valide';
		} 

		//Vérification si le contenu n'est pas vide
		if (empty($post['content'])) {
			$error[] = 'Le contenu ne doit pas être vide';
		}

		//Si il y a une erreur 
		if (!empty($error)) {
			echo implode('<br>', $error);
			die;
		} 
		else {

			/*$mail = new PHPMailer;

			//$mail->SMTPDebug = 3;                               	// Enable verbose debug output

			$mail->isSMTP();                                      	// Set mailer to use SMTP
			$mail->Host = '';  										// Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               	// Enable SMTP authentication
			$mail->Username = '';             						// SMTP username
			$mail->Password = '';                   				// SMTP password
			$mail->SMTPSecure = '';                            		// Enable TLS encryption, `ssl` also accepted
			$mail->Port = 587;                                    	// TCP port to connect to

			$mail->setFrom($post['email'], $post['firstname'].' '.$post['lastname']);
			$mail->addAddress('adress@email.com');   				// Add a recipient, Name is optional
			$mail->isHTML(true);                                  	// Set email format to HTML

			$mail->Subject = 'Contact du site';
			$mail->Body    = nl2br($post['formContent']);
			$mail->AltBody = $post['content'];

			if(!$mail->send()) {
			    echo 'Message could not be sent.';
			    echo 'Mailer Error: ' . $mail->ErrorInfo;
			} else {
				$mailSuccess = true;
			}*/

			echo 'Votre message à bien été envoyé !';
			die;

		}

	}

} //Fin traitement requête Ajax

//Si on veut consulter un message
if (!empty($_GET) && isset($_GET['id_message'])) {

	//On vérifie l'id du message
	$id_message = checkId($_GET['id_message']);

	//si l'id est incorrect (NULL)
	if(empty($id_message)) {
		$error[] = 'le message recherché n\'existe pas';
	}

	if (count($error) == 0) {
	 
	 	if (isset($_GET['action']) && $_GET['action'] == 'delete') {

	 		$del = $pdo->prepare('DELETE FROM contact WHERE id = :id');
	 		$del->bindValue(':id',$id_message,PDO::PARAM_INT);

	 		if($del->execute()) {
	 			header('Location: contact_message.php');
	 		}

	 	} 
	 	else {

			$message = showMessageContact($id_message);

			//on vérifie que le message existe en base de données
			if (empty($message)) {
				$error[] = 'le message recherché n\'existe pas';
			} 

			if (count($error) == 0) {
				$showMessage = true;

				if (isset($_GET['action']) && $_GET['action'] == 'rep') {
 					$repMessage = true; //On autorise l'affichage du formulaire de réponse
 				}

			}

		}

	}

} //Fin Gestion Get
else { //Sinon on affiche tous les messages

	$message_list = showAllMessageContact(); // onrécupère tous les messages en bdd

	if (empty($message_list)) { //Si le tableau retourné est vide -> pas de messages
		$error[] = 'Il n\'y a aucun message à afficher';
	}

	if (count($error) == 0) { 
		$showAllMessages = true; //si aucune erreur on autorise l'affichage des messages
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Gestion des messages de contact</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>

	
	<?php if (count($error) > 0) : ?>
		<div><?=implode('<br>', $error);?></div>
	<?php endif; ?>


	<?php if($showAllMessages) : //On affiche la liste des messages seulement si la variable $showAllMessages est à true ?>
	<h2 class="text-center">Liste des messages</h2>

	<hr>
	<table class="table table-striped table-bordered">
	   <thead>
	     <tr>
	       <th>id</th>
	       <th>Contenu</th>
	       <th>Prénom</th>
	       <th>Nom</th>
	       <th>Email</th>
	       <th>Date</th>
	       <th>Etat</th>
	     </tr>
	   </thead>
	<?php
	   foreach($message_list as $message) :
	   $date = date('d/m/Y H:i:m', strtotime($message['date_add']));
	?>
	   <tbody>
	     <tr>
	       	<td><?=$message['id']; ?></td>
	       	<td><?=$message['content']; ?></td>
	       	<td><?=$message['firstname']; ?></td>
	       	<td><?=$message['lastname']; ?></td>
	       	<td><?=$message['email']?></td>
	       	<td><?=$date; ?></td>
	       	<?php if ($message['message_state'] == 'read') : ?>
	       		<td>Lu</td>
	   		<?php else : ?>
	   			<td>Non lu</td>
	   		<?php endif; ?>

	       <td>
	         <a type="button" class="btn btn-info" href="?id_message=<?=$message['id'];?>">Voir</a>
	       </td>
	       <td>
	         <a type="button" class="btn btn-primary" href="?id_message=<?=$message['id'];?>&action=rep">Répondre</a>
	       </td>
	       <td>
	         <a type="button" class="btn btn-danger" href="?id_message=<?=$message['id'];?>&action=delete">Supprimer</a>
	       </td>

	     </tr>
	   </tbody>
	<?php endforeach; ?> <!-- fin foreach -->
	</table>

	<?php endif; ?> <!-- Fin affichage de tous les messages --> 

	<?php if($showMessage) : //On affiche le message sélectionné ?>

		<h2 class="text-center">Message du client :  <?php echo $message['lastname']. ' ' .$message['firstname'];; ?> </h2>

		<p>Email : <?=$message['email']; ?></p>
		<p>Contenu : <?=$message['content']; ?> </p>
		<p>Date de réception : <?php echo $message['date_add']; ?></p>

		<br>
	<?php endif; ?>

	<?php if($repMessage) : ?>

		<h2 id="titleRep">Réponse : </h2>
		<form class="form-horizontal well well-sm" id="formMessageContact" method="post">

			<div class="form-group">
				<label class="col-md-4 control-label" for="email">Destinataire</label>  
				<div class="col-md-4">
					<input id="email" name="email" type="text" placeholder="adresse@email.fr" class="form-control input-md" data-form="input-form">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-4 control-label" for="content">Contenu</label>
				<div class="col-md-4">
					<textarea name="content" id="content" rows="10" cols="50" placeholder="Saisir un texte ici..." data-form="input-form"></textarea>
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-4 col-md-offset-4">
					<button type="submit" class="btn btn-success">Envoyer</button>
				</div>
			</div>

		</form>

	<?php endif; ?>

	<a href="contact_message.php">Retour à la liste des messages</a>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="../js/script.js"></script>
</body>
</html>