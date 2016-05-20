<?php
require_once 'inc/connect.php';

// Prépare et execute la requète SQL pour récuperer nos recettes
$res = $pdo->prepare('SELECT * FROM recipes ORDER BY date_publish ASC');
$res->execute();

// Retourne toutes les entrées de la table "recipes" sous forme de array()
$recipes = $res->fetchAll(PDO::FETCH_ASSOC);

// var_dump($recipes); // Permet de sortir en brut nos recettes

include_once 'inc/header_admin.php';

?>


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
include_once 'inc/footer_admin.php';
?>


