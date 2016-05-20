<?php

  require_once 'inc/connect.php';
  require_once 'inc/header.php';

 ?> 

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
    
    <?php
    require_once 'inc/footer.php';
    ?> 