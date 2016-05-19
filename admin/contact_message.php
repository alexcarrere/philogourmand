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
	 			$_SESSION['del_message'] = 'ok';
	 			header('Location: contact_message.php');
	 			die;
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

include_once '../inc/header_admin.php';
?>


	
	<?php if (count($error) > 0) : ?>
		<div><?=implode('<br>', $error);?></div>
	<?php endif; ?>

	<?php 
		//Si un message à été effacé, on affiche la confirmation puis on efface la variable de session correspondante
		if(isset($_SESSION['del_message']) && $_SESSION['del_message'] == 'ok') {
			unset($_SESSION['del_message']);
			echo '<div class="alert alert-success" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					Message correctement effacé
				</div>';
		}
	?>

	<?php if($showAllMessages) : //On affiche la liste des messages seulement si la variable $showAllMessages est à true ?>
	<h2 class="text-center">Liste des messages</h2>

	<hr>
	<table class="table table-striped table-bordered table-condensed">
	   <thead>
	     <tr>
	       <th>id</th>
	       <th>Contenu</th>
	       <th>Prénom</th>
	       <th>Nom</th>
	       <th>Email</th>
	       <th>Date</th>
	       <th>Etat</th>
	       <th></th>
	       <th></th>
	       <th></th>
	     </tr>
	   </thead>

	   <tbody>
			<?php
			   foreach($message_list as $message) :
			   $date = date('d/m/Y H:i:m', strtotime($message['date_add']));
			?>
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
			<?php endforeach; ?> <!-- fin foreach -->

	   </tbody>
	</table>

	<?php endif; ?> <!-- Fin affichage de tous les messages --> 

	<?php if($showMessage) : //On affiche le message sélectionné
			$date = date('d/m/Y H:i:m', strtotime($message['date_add']));
	?>

		<div class="panel panel-default">
			<div class="panel-heading"><h3 class="text-center">Message du client :  <?php echo $message['lastname']. ' ' .$message['firstname']; ?></h3></div>
			<div class="panel-body">
				<p><strong>Email :</strong> <?=$message['email']; ?></p>
				<p><strong>Contenu :</strong> <?=$message['content']; ?> </p>
				<p><strong>Date de réception :</strong> <?=$date; ?></p>
			</div>
		</div>



		<br>

		<?php if(!$repMessage) : ?>
			<a href="contact_message.php" class="btn btn-info">Retour à la liste des messages</a>
		<?php endif; ?>

	<?php endif; ?>

	<?php if($repMessage) : ?>

		<div class="panel panel-default">
			<div class="panel-heading"><h3 class="text-center" id="titleRep">Réponse :</h3></div>
			<div class="panel-body">

				<form class="form-horizontal" id="formMessageContact" method="post">

					<div class="form-group">
						<label class="col-md-4 control-label" for="email">Destinataire</label>  
						<div class="col-md-4">
							<input id="email" name="email" type="email" placeholder="adresse@email.fr" class="form-control input-md" data-form="input-form" value="<?=$message['email']; ?>" readonly>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-4 control-label" for="content">Contenu</label>
						<div class="col-md-4">
							<textarea name="content" id="content" rows="10" cols="50" class="form-control input-md" placeholder="Saisir un texte ici..." data-form="input-form"></textarea>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-4 col-md-offset-4">
							<button type="submit" class="btn btn-success">Envoyer</button>
						</div>
					</div>

				</form>

			</div>
		</div>

		<a href="contact_message.php" class="btn btn-info">Retour à la liste des messages</a>

	<?php endif; ?>


<?php

include_once '../inc/footer_admin.php';

?>