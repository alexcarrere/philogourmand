<?php

  require_once 'inc/connect.php';
  require_once 'inc/header.php';

 ?> 

<!-- Main jumbotron for a primary marketing message or call to action -->
<div id="background_1" class="jumbotron">
    <div class="row"><!-- dbt div row -->
        <div class="col-lg-offset-2 col-lg-8">
            <img class="img-responsive" alt="photo_couverture" src="img/<?php echo $picture; ?>">
        </div>  
    </div> <!-- fin div row  -->
</div> <!-- fin div jumbotron -->


<div id="background_2" class="recipes">

    <!-- row of columns -->
    <div class="row"><!-- dbt div row 3-->
        <h2>Les recettes de la Philo</h2>

       <?php                   
$res = $pdo->prepare('SELECT * FROM recipes ORDER BY RAND() LIMIT 3');
$res->execute();

$recettes = $res->fetchAll(PDO::FETCH_ASSOC);

foreach($recettes as $recipe){ 
?>
        <div id="img_recette_index" class="col-md-4">
            <img class="img-responsive" alt="entree" src="img/<?php echo $recipe['link']; ?>">
            <br>    
            <p style="text-align:center;"><a  href="view_recipe.php" class="link_recipes">Lire la recette</a></p>
        </div>
<?php }
?>    </div>

</div><!-- fin div container 3 -->
<?php
require_once 'inc/footer.php';
?> 
