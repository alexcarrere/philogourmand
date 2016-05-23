<?php
session_start();

if (empty($_SESSION) || !isset($_SESSION['user']['role'])){
    header('Location: ../index.php');
}

//permet de vérifier que le paramètre GET soit bien ?logout=yes
if(isset($_GET['logout']) && $_GET['logout'] == 'yes'){
	// Ici je déconnecte mon utilisateur

	# unset($_SESSION); // A ne jamais faire, empèche la réutilisation de $_SESSION. On peut touitefois supprimer un index particulier

	unset($_SESSION['user']);//permet de supprimer l'index 'user' de ma session
	session_destroy();// Détruit toutes les entrées dans $_SESSION

	header('Location: ../index.php');

}

require_once '../inc/header_admin.php';
?>

<div class="alert alert-danger" role="alert">
	<p> Voulez-vous vraiment vous déconnecter ?</p>
</div>

<div class="row">	
	<div class="col-md-offset-4 col-md-4">
		<a href="?logout=yes" class="btn btn-danger">Oui je veux partir !</a>
		<a href="administration.php" class="btn btn-info">Naaannnn je veux rester !</a>
	</div>
</div>

<?php
require_once '../inc/footer_admin.php';
?>

