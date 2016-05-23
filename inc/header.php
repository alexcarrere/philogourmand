<?php
session_start();
// selection des informations du restaurant dans la table resto pour l'id  1
      $res = $pdo->prepare('SELECT * FROM resto WHERE id = :id');
      $res->bindValue(':id' ,1  , PDO::PARAM_INT);
        
      if($res->execute()){


      $restaurant = $res->fetch(PDO::FETCH_ASSOC);
      $idRestaurant = $restaurant['id'];
      $title = $restaurant['title'];
      $adress = $restaurant['adress'];
      $zipcode = $restaurant['zipcode'];
      $city = $restaurant['city'];
      $phone = $restaurant['phone'];
      $email_restaurant = $restaurant['email'];
      $picture = explode(',',$restaurant['link']);


      //unset($_GET['id']);
      }


?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- les 3 balises meta qui doivent être positionnées en premier, tout autre contenu de la partie head doit venir après ces balises -->

    <meta name="description" content="Projet en groupe Resto Philo">
    <meta name="keyword" content="Restaurant,Philomatique,Philo,Produits naturels"> 
    <meta name="auteur" content="Brice, Hugues, Carine, Alexandre, Thibaut"> 

    <title>PhiloGourmand</title>

    <!-- lien CDN Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <!-- lien Police Google Lobster Bootstrap -->
    <link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <!-- lien Police Google Satisfy -->
    <link href='https://fonts.googleapis.com/css?family=Satisfy' rel='stylesheet' type='text/css'>
    <!-- lien CDN Font Awesome -->
    <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css' rel='stylesheet' type='text/css'>
    <!-- lien feuille de style CSS -->
    <link type="text/css" rel="stylesheet" href="css/style.css">
  </head>

  <body class="container">

    <nav id="background_navbar" class="navbar navbar-inverse navbar-fixed-top"><!-- dbt nav barre -->
      <div class="container"> <!-- dbt div container 1 -->

        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a id="logo" class="navbar-brand" href="index.php"><?php echo $title ?></a><br>
        </div>
        

        <p class="navbar-text">
            <i class="fa fa-map-marker pr-5 pl-10"></i>&nbsp;<?php echo $adress ?>,&nbsp;
            <?php echo $zipcode; ?>&nbsp;
            <?php echo $city ?>&nbsp;
            <i class="fa fa-phone pr-5 pl-10"></i>&nbsp;<?php echo $phone ?>
        </p>

        <?php if(isset($_SESSION['user']['nickname']) && !empty($_SESSION['user']['nickname'])) : ?>
            <p class="navbar-text navbar-right">Connecté en tant que <a href="admin/administration.php" class="navbar-link">"<?=$_SESSION['user']['nickname'];?>"</a></p>
        <?php endif; ?>

        <ul class="nav navbar-nav navbar-right">
            <li><a href="list_recipes.php">Nos recettes</a></li>
            <li><a href="contact.php">Nous contacter</a></li><!-- renvoie vers la page de contact contact.php! -->
        </ul>
      

      </div> <!-- fin div container 1 -->
    </nav> <!-- fin nav barre -->
    <main class="container">
      
   
