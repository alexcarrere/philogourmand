<?php
session_start();
require_once '../inc/header_admin.php';
//permet de vérifier que le paramètre GET soit bien ?logout=yes
if(isset($_GET['logout']) && $_GET['logout'] == 'yes'){
	// Ici je déconnecte mon utilisateur

	# unset($_SESSION); // A ne jamais faire, empèche la réutilisation de $_SESSION. On peut touitefois supprimer un index particulier

	unset($_SESSION['user']);//permet de supprimer l'index 'user' de ma session


	session_destroy();// Détruit toutes les entrées dans $_SESSION

}

// on affiche les données seulement si la session existe
if(isset($_SESSION['user']) && !empty($_SESSION['user'])){

	/*echo '<ul>';
	foreach ($_SESSION['user'] as $key => $value) {
	 echo '<li><strong>'.$key.'</strong>'.$value.'</li>';
	}
	echo '</ul>';*/

	/*echo '
	<form class="form-horizontal well well-sm" action="?logout=yes" method="GET">
	<div class="form-group">
				<div class="col-md-4 col-md-offset-4">
					<button type="submit" class="btn btn-primary">Déconnexion</button>
				</div>
			</div>
	</form>';*/
	echo '<a href="?logout=yes"><div class="col-md-4 col-md-offset-4">
					<button  class="btn btn-primary">Déconnexion</button>
				</div></a>';
}
else {

	echo '<p>Vous n\'êtes pas connecté...</p>';
}
require_once '../inc/footer_admin.php';
?>

