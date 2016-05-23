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
        <h2>Les recettes de la Philo</h2>
    <div class="row"><!-- dbt div row 3-->

       <?php                   
$res = $pdo->prepare('SELECT * FROM recipes ORDER BY RAND() LIMIT 3');
$res->execute();

$recettes = $res->fetchAll(PDO::FETCH_ASSOC);

//Dans le case ou il y n'a aucune recettes
if (empty($recettes)) {

    for ($i = 0; $i < 3; $i++) {
        $recettes[] =[
            'link'  => 'link-default.jpg',
            'title' => 'Recette de test'
        ];
    }
}

foreach($recettes as $recipe){ 
?>
        <div class="col-md-4">
            <img class="img-responsive img_recette_index alt="entree" src="img/<?php echo $recipe['link']; ?>">
            <br>
            <?php if(isset($recipe['id'])) : ?>
                <p class="text-center"><a  href="list_recipes.php?id=<?=$recipe['id'];?>" class="link_recipes">Lire la recette</a></p>
            <?php else: ?>
                <p class="text-center"><a  href="#" class="link_recipes"><?=$recipe['title'];?></a></p>
            <?php endif; ?>
        </div>
<?php }
?>    </div>

</div><!-- fin div container 3 -->
<?php
require_once 'inc/footer.php';
?> 
