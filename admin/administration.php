<!DOCTYPE html>
<html>
<head>
	<title>menu pour administration</title>
</head>
<body>
<?php 
session_start();
if($_SESSION['user']['role'] == 'admin'){
	?>

<a href="view_recipes.php" class="btn btn-default">Les recettes</a><br>
<a href="add_recipe.php" class="btn btn-default">Créer une recette</a><br>
<a href="view_users.php" class="btn btn-default">Les utilisateurs</a><br>
<a href="add_user.php" class="btn btn-default">Créer un utilisateur</a><br>

<?php

}
elseif($_SESSION['user']['role'] == 'editor'){
	?>

<a href="view_recipes.php" class="btn btn-default">Les recettes</a><br>
<a href="add_recipe.php" class="btn btn-default">Créer une recette</a><br>
<?php
}
else {

	echo 'vous n\'êtes pas autorisé à aporter des modifications';
}
?> 

</body>
</html>