<?php

  require_once 'inc/connect.php';

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
      $picture = $restaurant['link'];


      //unset($_GET['id']);
      }
      echo $picture;


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
    <!-- lien feuille de style CSS -->
    <link type="text/css" rel="stylesheet" href="css/style.css">
  </head>

  <body>

    <nav id="background_navbar" class="navbar navbar-inverse navbar-fixed-top"><!-- dbt nav barre -->
      <div class="container"> <!-- dbt div container 1 -->
        
          <div class="navbar-header"><!-- dbt div navbar header -->

            <a id="logo" class="navbar-brand" href="#"><?php echo $title ?></a><br>
            <p class="navbar-brand lead"><?php echo $adress ?>,&nbsp;<?php echo $zipcode; ?>&nbsp;<?php echo $city ?><br>
            <?php echo $phone ?></p>
          </div> <!-- fin div navbar header -->

          <div id="text-align-right" class="contact">
            <ul id="menu-nav">
              <li><a href="view_recipes.php">Nos recettes</a></li>
              <li><a href="contact.php">Nous contacter</a></li><!-- renvoie vers la page de contact contact.php! -->
            </ul>
          </div>
      

      </div> <!-- fin div container 1 -->
    </nav> <!-- fin nav barre -->

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div id="background_1" class="jumbotron">
      <div class="container"> <!-- dbt div container 2 -->
        <div class="row"><!-- dbt div row 2-->
          <div class="col-lg-2 text-center"></div>
          <div class="col-lg-8 text-center"><img class="img-responsive" alt="" src="img/<?php echo $picture; ?>"</div>   
          <div class="col-lg-2 text-center"></div> 
            
          </div> <!-- fin div row 2 -->
        </div> <!-- fin div container 2 -->
      </div> <!-- fin div jumbotron -->


    <div id="background_2" class="container">
      <!-- row of columns -->
      <div class="row"><!-- dbt div row 3-->
          <h2>Les recettes de la Philo</h2>
        <div class="col-md-4">
          <img class="img-responsive" alt="entree" src="img/entre.jpg">
          <br>    
          <p><a  href="#" class="link_recipes">Lire la recette</a></p>
        </div>
        <div class="col-md-4">
          <img class="img-responsive" alt="plat" src="img/plat.jpg"> 
          <br>
          <p><a href="#" class="link_recipes">Lire la recette</a></p>
       </div>
        <div class="col-md-4">
          <img class="img-responsive" alt="dessert" src="img/dessert.jpg">
          <br>           
          <p><a href="#" class="link_recipes">Lire la recette</a></p>
        </div>
      </div>


    </div><!-- fin div container 3 -->
      <footer>
      
<div id="footer" class="container"><!-- dbt div container 4-->
  <div class="row"><!-- dbt div row 4-->

  <div class="col-md-4"><!-- lien vers user admin-->
 <p><a href="admin/login.php" id="admin">Admin</a></p>
  </div>


  <div class="col-md-4 text-center"></div>

  <div class="col-md-4">
    <div id="legal"><p>Mentions Légales l © 2016 La Philo</p>
      </div><!-- fin id legal-->
    </div><!-- fin col-md-4-->
  </div><!-- fin div row 4-->
</div><!-- fin div container 4 -->

      
      </footer>


    <!-- Bootstrap contenant JavaScript
    ================================================== -->
    <!-- placé à la fin pour chargemement plus rapide -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    
  </body>
</html>
