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
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>PhiloGourmand</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>

    <link type="text/css" rel="stylesheet" href="css/style.css">
  </head>

  <body>

    <nav id="background_navbar" class="navbar navbar-inverse navbar-fixed-top"><!-- dbt nav barre -->
      <div class="container"> <!-- dbt div container 1 -->
        
          <div class="navbar-header"><!-- dbt div navbar header -->

            <!-- <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button> -->
            <a id="logo" class="navbar-brand" href="#"><?php echo $title ?></a><br>
            <p class="navbar-brand lead"><?php echo $adress ?>,&nbsp;<?php echo $zipcode; ?>&nbsp;<?php echo $city ?><br>
            <?php echo $phone ?></p>
          </div> <!-- fin div navbar header -->

          <div id="text-align-right" class="contact">
            <ul id="menu-nav">
              <li><a href="view_recipes.php">Nos recettes</a></li>
              <li><a href="contact.php">Nous contacter</a></li><!-- renvoie vers la page de contact contact.php!!!!! -->
            </ul>
          </div>
        
        <!-- ce qu'il y avait a l'origine sur l'exemple :
        <div id="navbar" class="navbar-collapse collapse">
           <form class="navbar-form navbar-right">
            <div class="form-group">
              <input type="text" placeholder="Email" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
          </form> 
        </div><!--/.navbar-collapse -->

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
      <!-- Example row of columns -->
      <div class="row">
          <h1>Les recettes de la Philo</h1>
        <div class="col-md-4">
          <img class="img-responsive" alt="entree" src="img/entre.jpg">
          <br>    
          <p><a  href="#" class="link_recipes">lire la recette1 </a></p>
        </div>
        <div class="col-md-4">
          <img class="img-responsive" alt="plat" src="img/plat.jpg"> 
          <br>
          <p><a href="#" class="link_recipes">lire la recette2 </a></p>
       </div>
        <div class="col-md-4">
          <img class="img-responsive" alt="dessert" src="img/dessert.jpg">
          <br>           
          <p><a href="#" class="link_recipes">lire la recette3 </a></p>
        </div>
      </div>

      <hr>

    </div> <!-- /container -->
      <footer>
        <a href="admin/login.php">Admin</p>
      </footer>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    
  </body>
</html>
