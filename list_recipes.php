<?php
require_once 'inc/connect.php';



$resultatSearch = '';
$resultatSearchReplace = '';
$viewRecipe = false;
$error = '';
$resultatSearchRegex = '//';
/***************************  AFFICHER SELON ENTREE DANS MOTEUR DE RECHERCHE ***************************/
if(!empty($_GET) && isset($_GET['search']) && !empty($_GET['search'])) { // Si recherche on affiche le résultat // on recherche les paramètres


	$resultatSearch = trim(strip_tags($_GET['search']));
	//var_dump($resultatSearch);

	$res = $pdo->prepare('SELECT nickname, title, content, link, date_publish FROM users RIGHT JOIN recipes ON users.id = recipes.id_user WHERE (title LIKE :maRecherche) OR (content LIKE :maRecherche) ORDER BY date_publish ASC');
	$res->bindValue(':maRecherche', '%'.$resultatSearch.'%');
	$res->execute();

	$recipes = $res->fetchAll(PDO::FETCH_ASSOC);
	

	if(empty($recipes)){
		$error = 'Aucun résultat à votre recherche !';
	}

	$resultatSearchReplace = '<span style="background:yellow">'.$resultatSearch.'</span>';

	$resultatSearchRegex = '/'.$resultatSearch.'/';

} 
else if(isset($_GET['id']) && !empty($_GET['id'])) { /*Si on veux voir une recette en particulier */

	$idRecipe = $_GET['id'];
	if(!is_numeric($idRecipe)){
		$error = 'Aucun résultat ne correspond !';
	}
	else {

		$res = $pdo->prepare('SELECT nickname, title, content, link, date_publish FROM users RIGHT JOIN recipes ON users.id = recipes.id_user WHERE recipes.id = :idRecipe');
		$res->bindValue(':idRecipe', intval($idRecipe), PDO::PARAM_INT);
		if($res->execute()) {

			$recette = $res->fetch(PDO::FETCH_ASSOC);
			$viewRecipe = true;

			//Si aucune recette n'a été trouvée
			if(empty($recette)) {
				$error = 'Aucun résultat ne correspond !';	
			}

		} else {
			die(var_dump($res->errorInfo()));
		}
	}
}
/*******************************   AFFICHER TOUTES LES RECETTES AVEC AUTEUR   ***********************/
else { // sinon on liste toutes les recettes

	$res = $pdo->prepare('SELECT nickname, title, content, link, date_publish FROM users RIGHT JOIN recipes ON users.id = recipes.id_user ORDER BY date_publish ASC');
	$res->execute();

	// Retourne toutes les entrées de la table "recipe" sous forme de array()
	$recipes = $res->fetchAll(PDO::FETCH_ASSOC);

}




include_once 'inc/header.php';
?>
<br>
<form role="search" method="GET">

	<div class="row">
		<div class="col-md-offset-3 col-md-6">
			<div class="input-group">
				<input type="search" class="form-control" name="search" placeholder="Tapez votre recherche ici...">
				<span class="input-group-btn">
				<button class="btn btn-default" type="button">
					<span class="glyphicon glyphicon-search" aria-hidden="true"></span>
				</button>
				</span>
			</div>
		</div>
	</div>
	
</form>
<br>

<?php if(!empty($error)) : ?>

	<br>

	<div class="panel panel-danger">
		<div class="panel-body bg-danger">
			<p class="text-danger"><?=$error;?></p>
		</div>
	</div>

<?php elseif($viewRecipe) : ?>

	<div class="panel panel-primary">
		<div class="panel-body">
			<h1 class="page-header css3-notification">
				Vous avez choisi de découvrir :
			</h1>
		</div>
	</div>


	<div class="row" id="contentRecipe">
	    <div class="col-md-7 panel panel-info">
	    	<div class="panel-heading">
	        	<h2 class="section-heading"><?=$recette['title'];?></h2>
	        </div>
	        <div class="panel-body">
            	<p class="lead"><?=nl2br($recette['content']);?></p>
            	<?php if ($recette['nickname'] == NULL) : ?>
        			<p class="text-right">Publié le <?=date('d/m/Y', strtotime($recette['date_publish']));?>, par PhiloGourmet</p>
        		<?php else : ?>
        			<p class="text-right">Publié le <?=date('d/m/Y', strtotime($recette['date_publish']));?>, par <?=$recette['nickname'];?></p>
        		<?php endif; ?>
        	</div>
	    </div>

	    <div class="col-md-5">
	        <img style="width: 550px" class="img-responsive" src="img/<?=$recette['link'];?>" alt="img_recipes">
	    </div>
	</div>


<?php else : ?>

	<h2>Nos recettes : </h2>
	<br>
	<?php foreach($recipes as $recip) : ?> <!-- Dbt de ma boucle -->
	<!-- $recip contient chaque entrée de ma table, les colonnes deviennent les clés du tableau -->
		<div class="recipe panel panel-info">
			<div class="panel-heading">
				<h2><?=preg_replace($resultatSearchRegex,$resultatSearchReplace,$recip['title']);?></h2>
			</div>
			<br>
			<div class="row panel-body">
				<div class="col-md-2">
					<img src="img/<?=$recip['link'];?>" alt="image" style="width:150px; display:inline-block"> 
				</div>
				<div class="col-md-10">
					<p><?=preg_replace($resultatSearchRegex,$resultatSearchReplace,$recip['content']);?></p>

					<?php if ($recip['nickname'] == NULL) : ?>
						<p><strong>Auteur : </strong>PhiloGourmet</p>
					<?php else : ?>
						<p><strong>Auteur : </strong><?=$recip['nickname'];?></p>
					<?php endif; ?>
				<p class="text-right">Publié le <?=date('d/m/Y', strtotime($recip['date_publish']));?></p>
				</div>
			</div>
		</div>
			

	<?php endforeach; ?> <!-- Fin de ma boucle -->

<?php
	endif;
	include_once 'inc/footer.php';
?>
