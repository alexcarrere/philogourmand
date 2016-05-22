<?php
require_once 'inc/connect.php';



$resultatSearch = '';
$resultatSearchReplace = '';
$startRecipes = 0;
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
/*******************************   AFFICHER TOUTES LES RECETTES AVEC AUTEUR   ***********************/
else { // sinon on liste toutes les recettes

	$res = $pdo->prepare('SELECT nickname, title, content, link, date_publish FROM users RIGHT JOIN recipes ON users.id = recipes.id_user ORDER BY date_publish ASC');
	$res->execute();

	// Retourne toutes les entrées de la table "recipe" sous forme de array()
	$recipes = $res->fetchAll(PDO::FETCH_ASSOC);

}


include_once 'inc/header.php';
?>

<h2 class="text-center">Nos recettes</h2>
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

<?php else : ?>

	<?php foreach($recipes as $recip) : ?> <!-- Dbt de ma boucle -->
	<!-- $recip contient chaque entrée de ma table, les colonnes deviennent les clés du tableau -->
		<div class="recipe well">
			<h2><?=preg_replace($resultatSearchRegex,$resultatSearchReplace,$recip['title']);?></h2>
			<br>
			<div class="row">
				<div class="col-md-2">
					<img src="img/<?=$recip['link'];?>" alt="image" style="width:150px; display:inline-block"> 
				</div>
				<div class="col-md-10">
					<p><?=preg_replace($resultatSearchRegex,$resultatSearchReplace,$recip['content']);?></p>

					<?php if ($recip['nickname'] == NULL) : ?>
						<p><strong>Auteur : </strong>PhiloGourmand</p>
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
