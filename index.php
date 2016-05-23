<?php

  require_once 'inc/connect.php';
  require_once 'inc/header.php';

 ?> 

<div id="myCarousel"  class="carousel slide jumbotron" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner"  role="listbox">
    <div class="item active" >
      <img  src="img/<?php echo $picture[0]; ?>" alt="Chania">
    </div>

    <div class="item">
      <img  src="img/<?php echo $picture[1]; ?>" alt="entree">
    </div>

    <div class="item">
      <img  src="img/<?php echo $picture[2]; ?>" alt="plat">
    </div>

  </div>

  <!-- Left and right controls -->
  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div> <!-- fin caroussel -->


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
