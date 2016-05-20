<?php
// On démarre la session PHP
session_start(); 
// On se connecte à la base de données
require_once '../inc/connect.php';
require_once '../vendor/autoload.php';

$error = [];
$post = [];

$showFormEmail = true; // Permet d'afficher le premier formulaire de saisi d'email
$showFormPassword = false; // Permet d'afficher le second formulaire de mise à jour du mot de passe

// Si on a un token et une adresse mail dans l'url (en GET) on masque le 1er formulaire et on affiche le second
if(isset($_GET['token']) && !empty($_GET['token']) && isset($_GET['email']) && !empty($_GET['email'])){
	$showFormEmail = false; 
	$showFormPassword = true;
}

// On traite nos formulaires
if(!empty($_POST)){
	// On nettoie les données
	foreach($_POST as $key => $value){
		$post[$key] = trim(strip_tags($value));
	}

	// Ici on traite le formulaire de l'adresse email
	if(isset($post['action']) && $post['action'] == 'generateToken'){
		// Ici, l'adresse email est au bon format (note : il n'y a pas le point d'exclamation (!) devant filter_var())
		if(filter_var($post['email_password'], FILTER_VALIDATE_EMAIL)){
			$req = $pdo->prepare('SELECT email FROM users WHERE email = :email');
			$req->bindValue(':email', $post['email_password']);
			$req->execute();

			$emailExist = $req->fetchColumn();
			if(!empty($emailExist)){  // On trouve une correspondance avec l'email

				$token = md5(uniqid()); // On créer le token 

				// (NOW() + INTERVAL 2 DAY) = Maintenant + 2 jours
				$insert = $pdo->prepare('INSERT INTO tokens_password (email, token, date_create, date_exp) VALUES(
						:emailInsert, 
						:tokenInsert, 
						NOW(), 
						(NOW() + INTERVAL 2 DAY)
					)');
				$insert->bindValue(':emailInsert', $post['email_password']);
				$insert->bindValue(':tokenInsert', $token);
				if($insert->execute()){
					// Ici on envoi un mail qui contient le lien avec le token et l'email en GET 
					// Pour l'exercice on affichera seulement ce lien
					$linkChangePassword = 'lost_password.php?email='.$post['email_password'].'&token='.$token;
				}

			}
		}
		else {
			$error[] = 'Votre adresse email est incorrecte';
		}
	}
	// Ici on traite le formulaire de mise à jour du mot de passe
	elseif(isset($post['action']) && $post['action'] == 'updatePassword'){
		// Le mot de passe doit faire entre 8 et 20 caractères
		if(strlen($post['new_password']) < 8 || strlen($post['new_password']) > 20){
			$error[] = 'Le mot de passe doit comporter entre 8 et 20 caractères';
		}
		// Le mot de passe et sa confirmation doivent correspondre
		if($post['new_password'] != $post['new_password_conf']){
			$error[] = 'Les mots de passe doivent correspondre!';
		}

		if(count($error) == 0){ // Il n'y a pas d'erreurs dans le formulaire, on peut vérifier le token & l'adresse email ... et même la date d'expiration
			$tok = $pdo->prepare('SELECT * FROM tokens_password WHERE email = :postEmail AND token = :postToken AND date_exp > NOW()');
			$tok->bindValue(':postEmail', $post['email']);
			$tok->bindValue(':postToken', $post['token']);
			$tok->execute();

			$tokenExist = $tok->fetch();

			if(empty($tokenExist)){
				$error[] = 'Le token et l\'adresse email ne correspondent pas.'; // Ou le token est expiré, mais on va pas trop donner d'infos quand même :-)
			}
			else {
				// Ici, on peut ENFIN changer ce putain de mot de passe :-)
				$update = $pdo->prepare('UPDATE users SET password = :newPassword WHERE email = :email');
				$update->bindValue(':newPassword', password_hash($post['new_password'], PASSWORD_DEFAULT)); // On insère le mot de passe hashé
				$update->bindValue(':email', $post['email']);
				if($update->execute()){
					$successUpdate = true;

					// On supprime le token puisque le mdp est modifié
					$delete = $pdo->prepare('DELETE FROM tokens_password WHERE id = :idToken');
					$delete->bindValue(':idToken', $tokenExist['id'], PDO::PARAM_INT); // $tokenExist contient les infos de mon token extraites de la base de données.. et donc son ID
					$delete->execute();
					header('Location: login.php');
					die;
				}

			}

		}
	}

}
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Mot de passe oublié</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>

	<main class="container">
		<h1 class="text-center">Mot de passe oublié</h1>
		<br>

		<?php if(!empty($error)): // On affiche les erreurs si $error n'est pas vide ?>
			<div class="alert alert-danger">
				<?=implode('<br>', $error); ?>
			</div>
		<?php endif; ?>

		<?php if(isset($successUpdate) && $successUpdate == true): ?>
			<div class="alert alert-success">
				Cool ! Le mot de passe est à jour.
			</div>
		<?php endif; ?>
			

		<?php if(isset($showFormEmail) && $showFormEmail == true): // On affiche le premier formulaire ?>

			<?php if(isset($linkChangePassword)): // Si l'adresse email est ok et que le token est inséré ?>






				<?php

					$mail = new PHPMailer;

											//$mail->SMTPDebug = 3;                               // Enable verbose debug output

											$mail->isSMTP();                                      // Set mailer to use SMTP
											/*$mail->Host = 'smtp1.example.com;smtp2.example.com';*/  // Specify main and backup SMTP servers
											$mail->Host = '';
											$mail->SMTPAuth = true;                               // Enable SMTP authentication
											/*$mail->Username = 'user@example.com'; */                // SMTP username
											$mail->Username = ''; 
											/*$mail->Password = 'secret'; */
											$mail->Password = '';                           // SMTP password
											$mail->SMTPSecure = '';                            // Enable TLS encryption, `ssl` also accepted
											$mail->Port = 587;                                    // TCP port to connect to

											$mail->setFrom('monsite', 'amoi');
											$mail->addAddress('@', '');     // Add a recipient
											/*$mail->addAddress('ellen@example.com');*/               // Name is optional deuxieme adresse 
											//$mail->addReplyTo('info@example.com', 'Information');
											/*$mail->addCC('cc@example.com');
											$mail->addBCC('bcc@example.com');*/

											/*$mail->addAttachment('/var/tmp/file.tar.gz');  */       // Add attachments
											/*$mail->addAttachment('/tmp/image.jpg', 'new.jpg'); */   // Optional name
											$mail->isHTML(true);                                  // Set email format to HTML

											$mail->Subject = 'Here is the subject';
											$mail->Body    = '<p>Vous pouvez réinitialiser votre mot de passe en cliquant sur le lien suivant :<br>
											<a href="'.$linkChangePassword.'">Modifier mon mot de passe</a>	</p><br>				
											<code>'.$linkChangePassword.'</code>';

											$mail->AltBody = 'Vous pouvez réinitialiser votre mot de passe en cliquant sur le lien suivant :'.$linkChangePassword;

										


											if(!$mail->send()) {
											    echo 'Message could not be sent.';
											    echo 'Mailer Error: ' . $mail->ErrorInfo;
											} else {
											    echo 'Message has been sent';
											}










				?>
				<p>Vous pouvez réinitialiser votre mot de passe en cliquant sur le lien suivant :
				<br>
				<a href="<?=$linkChangePassword; ?>">Modifier mon mot de passe</a>
				</p>
				<br>
				<!-- 
					On affiche le lien en dur juste pour la forme 
					Rappel : le token ou le lien de changement de mot de passe ne doit jamais apparaitre en clair sur la page. Celui-ci sera obligatoirement envoyé par email
				-->
				<code><?=$linkChangePassword; ?></code>


















			<?php else: // Sinon on affiche le formulaire ?>

			<form class="form-horizontal well well-sm" method="post">
				<input type="hidden" name="action" value="generateToken">
				<div class="form-group">
					<label class="col-md-4 control-label" for="email_password">Votre adresse email</label>
					<div class="col-md-4">
						<input type="email" name="email_password" id="email_password" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-4 col-md-offset-4">
						<button type="submit" class="btn btn-default">Réinitialiser mon mot de passe</button>
					</div>
				</div>
			</form>
		<?php endif; // ferme le if/else de $linkChangePassword ?>
	<?php endif; ?>

	<?php if($showFormPassword == true): // Permet d'afficher le formulaire de changement de mot de passe ?>

		<form class="form-horizontal well well-sm" method="post">
			<input type="hidden" name="action" value="updatePassword">
			<input type="hidden" name="email" value="<?=$_GET['email'];?>">
			<input type="hidden" name="token" value="<?=$_GET['token'];?>">
			<div class="form-group">
				<label class="col-md-4 control-label" for="new_password">Votre nouveau mot de passe</label>
				<div class="col-md-4">
					<input type="password" name="new_password" id="new_password" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label class="col-md-4 control-label" for="new_password_conf">Confirmation du mot de passe</label>
				<div class="col-md-4">
					<input type="password" name="new_password_conf" id="new_password_conf" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-4 col-md-offset-4">
					<button type="submit" class="btn btn-default">Mettre à jour mon mot de passe</button>
				</div>
			</div>
		</form> 
	<?php endif; ?>



	</main>
</body>
</html>
