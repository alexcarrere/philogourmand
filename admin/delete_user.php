<?php
session_start();
require_once '../inc/connect.php';
require_once '../inc/fonctions.php';


$error = []; //Tableau qui contiendra les users d'erreurs 
$needConfirm = false; //Variable qui servira à confirmer la suppression


if (!empty($_SESSION) && isset($_SESSION['user']['role'])){

	if ($_SESSION['user']['role'] != 'admin') {
		header('Location: administration.php');
	}
	
} else {
	header('Location: ../index.php');
}

if (!empty($_GET) && isset($_GET['id'])) {
	//On vérifie l'id du user
	$id_user = checkId($_GET['id']);
	//si l'id est incorrect (NULL)
	if(empty($id_user)) {
		$error[] = 'le utilisateur recherché n\'existe pas';
	}

	if($_SESSION['user']['id'] == $id_user) {
		$error[] = 'Impossible d\'effacer le compte sur lequel vous êtes connecté !';
	}

	if (count($error) == 0) {

		if(isset($_GET['confirm']) && $_GET['confirm'] == 'ok') {
	 		$del = $pdo->prepare('DELETE FROM users WHERE id = :id');
	 		$del->bindValue(':id',$id_user,PDO::PARAM_INT);
	 		if($del->execute()) {
	 				$_SESSION['del_user'] = 'ok';
	 				header('Location: view_users.php');
	 				die;		
			} 
	 	}
	 	else {
				  $res = $pdo->prepare('SELECT * FROM users WHERE id = :id');
				  $res->bindValue(':id', $id_user, PDO::PARAM_INT);
				  $res->execute();

				  $utilisateur = $res->fetch(PDO::FETCH_ASSOC);
				$needConfirm = true;
		}
	}
}

 //Si un message à été effacé, on affiche la confirmation puis on efface la variable de session correspondante
	if(isset($_SESSION['del_user']) && $_SESSION['del_user'] == 'ok') {
		unset($_SESSION['del_user']);
	}

include_once '../inc/header_admin.php';
?>

<!-- Si il y a des erreurs, on affiche les erreurs -->
<?php if (count($error) > 0) : ?>

	<div class="alert alert-danger" role="alert"><?=implode('<br>', $error);?></div>

<?php else : ?> <!-- Sinon on affiche le message de confirmation et le bouton -->

	<div class="alert alert-danger" role="alert">
		<p> ATTENTION ! Vous souhaitez surprimé l'utilisateur <?= $utilisateur['nickname'] ?> La sentence sera irrémédiable !!!!</p>
	</div>

	<a type="button" class="btn btn-danger" href="delete_user.php?id=<?php echo $id_user;?>&confirm=ok">Cliquez ici si vous souhaitez vraiment supprimer l'utilisateur</a>

<?php endif; ?>

<?php include_once '../inc/footer_admin.php'; ?>