<?php

  require_once 'inc/connect.php';
  require_once 'inc/header.php';

 ?> 
<div class="container">
 <div class="col-lg-3"></div>
            <div class="col-lg-6">
                 <h1 class="page-header css3-notification">
                    Vous avez choisie de découvrir :
                </h1>
            </div>
        <div class="col-lg-3"></div>
    <?php
    if(isset($_GET['id']) && $_GET['id']) {
        $idRecipe = $_GET['id'];
        if(!is_numeric($idRecipe)){
            $errorID = true;
        }
        else {
            $res = $pdo->prepare('SELECT * FROM recipes WHERE id = :idRecipe');
            $res->bindValue(':idRecipe', intval($idRecipe), PDO::PARAM_INT);
            if($res->execute()) {

                $recettes = $res->fetch(PDO::FETCH_ASSOC);
                

            }    
        }
    }
             // homepage par dÃ©faut (il n'y pas de param get)
                

	if(isset($errorID) && $errorID) { // Si on a pas transmi de paramÃ¨tre GET
		echo 'Erreur, vous n\'avez choisi aucune recette';			
	}
	/*elseif(isset($clientDeleted)){ // Sinon si le client est supprimÃ©
		echo $clientDeleted;
	}*/
	else { // Sinon on affiche le client
	?>
              <div class="content-section-b">
                <div class="container">
                    <div class="row">
                        <div class="col-md-7">
                            
                          
                            <?php echo '<h2 class="section-heading">'.$recettes['title'].'</h2>';
                                  echo '<p class="lead">'.$recettes['content'].'</p>';
                                  echo 'Publié le '.date('d/m/Y', strtotime($recettes['date_publish'])); ?>

                        </div>
                        <div class="col-md-5">
                            <?php echo '<img style="width: 550px; "class="img-responsive" src="img/'.$recettes['link'].'" alt="">';
                                  ;
                             ?>
                        </div>
                    </div>
                </div> <!-- /.container -->
            </div> <!-- /.content-section-b -->
            <?php
            }
    ?>
    </div> <!-- fin de div class="thumbnail"-->
        


 <?php
require_once 'inc/footer.php';
?> 
