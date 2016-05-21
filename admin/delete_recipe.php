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
	$id_recipe = checkId($_GET['id']);
	//si l'id est incorrect (NULL)
	if(empty($id_recipe)) {
		$error[] = 'le recette recherché n\'existe pas';
	}

	if (count($error) == 0) {

		if(isset($_GET['confirm']) && $_GET['confirm'] == 'ok') {
	 		$del = $pdo->prepare('DELETE FROM recipes WHERE id = :id');
	 		$del->bindValue(':id',$id_recipe,PDO::PARAM_INT);
	 		if($del->execute()) {
	 				$_SESSION['del_recipe'] = 'ok';
	 				header('Location: view_recipes.php');
	 				die;		
			} 
	 	}
	 	else {
				  $res = $pdo->prepare('SELECT * FROM recipes WHERE id = :id');
				  $res->bindValue(':id', $id_recipe, PDO::PARAM_INT);
				  $res->execute();

				  $recette = $res->fetch(PDO::FETCH_ASSOC);
				$needConfirm = true;
		}
	}
}
if (count($error) > 0) : ?>
	<div><?=implode('<br>', $error);?></div>
<?php endif; ?>

<?php //Si un message à été effacé, on affiche la confirmation puis on efface la variable de session correspondante
	if(isset($_SESSION['del_recipe']) && $_SESSION['del_recipe'] == 'ok') {
		unset($_SESSION['del_recipe']);
	}

include_once '../inc/header_admin.php';
?>
<div class="alert alert-danger" role="alert">
<p> ATTENTION ! Vous souhaitez surprimé la recette <?= $recette['title'] ?>!!! La sentence sera irrévocable !!!!</p>



</div>
<a type="button" class="btn btn-danger" href="delete_recipe.php?id=<?php echo $id_recipe;?>&confirm=ok">Cliquez ici si vous souhaitez vraiment supprimer l'recette</a>

<?php include_once '../inc/footer_admin.php'; ?>
