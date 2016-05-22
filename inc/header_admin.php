<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Administration</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <!-- lien Police Google Lobster Bootstrap -->
    <link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <!-- lien Police Google Satisfy -->
    <link href='https://fonts.googleapis.com/css?family=Satisfy' rel='stylesheet' type='text/css'>
    <!-- lien CDN Font Awesome -->
    <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" type="text/css" href="../css/style_admin.css">
</head>
<body>

	<div id="navbarBootstrap">
        <nav class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="navbar-header page-scroll">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Activer navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a id="logo" class="navbar-brand page-scroll" href="../index.php">Philogourmand</a>
                </div>
                
                <!-- Collect the nav links, forms, and other content for toggling -->
              	<div class="collapse navbar-collapse navbar-ex1-collapse navbar-right">
                    <ul class="nav navbar-nav"> 
                        <li>
                            <a href="../index.php">Retour site</a>
                        </li>

                        <!-- Si l'utilisateur est un admin, on affiche toutes les options possibles -->
                        <?php if (!empty($_SESSION) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin') :?>
                            <li>
                                <a href="administration.php">Administration</a>
                            </li>

                            <li class="dropdown">
    							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Affichage des données<span class="caret"></span></a>
    							<ul class="dropdown-menu">
                                    <li><a href="view_users.php">Afficher la liste des utilisateurs</a></li>
                                    <li><a href="contact_message.php">Afficher la liste des messages</a></li>
    								<li><a href="view_recipes.php">Afficher la liste des recettes</a></li>

    							</ul>
    						</li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Ajout de données<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="add_user.php">Ajouter un utilisateur</a></li>
                                    <li><a href="add_recipe.php">Ajouter une recette</a></li>
                                </ul>
                            </li>

                            <li>
                                <a href="edit_resto.php?id=1">Modifier les infos du site</a>
                            </li>

                        <?php endif; ?>

                        <?php if(isset($_SESSION['user']) && !empty($_SESSION['user'])) : ?>
                        <li>
                            <a href="deconnexion.php">Déconnexion</a>
                        </li>
                        <?php endif; ?>
                            
                        <!-- Si l'utilisateur est un editeur, on affiche seulement les liens en rapport avec les recettes -->
                        <?php if (!empty($_SESSION) && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'editor') :?>
                            <li><a href="add_recipe.php">Ajouter une recette</a></li>
                            <li><a href="contact_message.php">Afficher la liste des messages</a></li>
                        <?php endif;?>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->

            </div>
            <!-- /.container -->
        </nav>
    </div>

	<main class="container">

