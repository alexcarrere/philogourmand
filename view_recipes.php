<?php
require_once 'inc/connect.php';


include_once 'inc/header_admin.php';

$resultatSearch = '';
$resultatSearchReplace = '';
$startRecipes = 0;
$error = '';

/***************************  AFFICHER SELON MOTEUR DE RECHERCHE ***************************/
if(!empty($_GET) && isset($_GET['search']) && !empty($_GET['search'])) { // Si recherche on affiche le résultat // on recherche les paramètres


	$resultatSearch = trim(strip_tags($_GET['search']));
	//var_dump($resultatSearch);

	$res = $pdo->prepare('SELECT * FROM recipes WHERE (title LIKE :maRecherche) OR (content LIKE :maRecherche)');
	$res->bindValue(':maRecherche', '%'.$resultatSearch.'%');
	$res->execute();

	$recipes = $res->fetchAll(PDO::FETCH_ASSOC);
	//var_dump($recip);

	if(empty($recipes)){
		$error = 'Aucun résultat à votre recherche !';
	}

	//$resultatSearchReplace = '<span style="background:yellow">'.$resultatSearch.'</span>';


} 
/*******************************     AFFICHER TOUTES LES RECETTES    ***********************/
else { // sinon on liste tous les articles


	$res = $pdo->prepare('SELECT * FROM recipes ORDER BY date_publish ASC');
	$res->execute();

	// Retourne toutes les entrées de la table "recipe" sous forme de array()
	$recipes = $res->fetchAll(PDO::FETCH_ASSOC);

}


?>

<form role="search" method="GET">
	
	<button type="submit" class="float_right">Rechercher</button>
	<input type="search" class="float_right" name="search" placeholder="Rechercher">
	
</form>

<?php if(!empty($error)) : ?>

	<div class="recipe well">
		<p><?=$error;?></p>
	</div>

<?php else : ?>

	<?php foreach($recipes as $recip) : ?>
	<!-- $recip contient chaque entrée de ma table, les colonnes deviennent les clés du tableau -->
		<div class="recipe well">
			<h2><?=$recip['title'];?></h2>
			<p>Publié le <?=date('d/m/Y', strtotime($recip['date_publish']));?></p>
			<img src="<?=$recip['link'];?>" alt="image" style="width:150px; display:inline-block"> 
			<p><?=$recip['content'];?></p>
		</div>
			

	<?php endforeach; ?>

<?php
	endif;
	include_once 'inc/footer_admin.php';
?>


