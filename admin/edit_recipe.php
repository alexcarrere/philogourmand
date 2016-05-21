<?php

session_start();

// connection à la base
require_once '../inc/connect.php';

$post = array();
$error = array();

$errorUpdate  = false; // erreur lors de la mise à jour de la table
$displayErr   = false; 
$formValid    = false;
$recipeExist    = false;

$folder = '../img/'; // création de la variable indiquant le chemin du répertoire destination pour les fichiers uploadés (important  : le slash à la fin de la chaine de caractère).
$maxSize = 1000000 * 5; // 5Mo


// vérification des paramètres GET et appel des champs recipe correspondants
if(isset($_GET['id']) AND !empty($_GET['id']) AND is_numeric($_GET['id'])) {

    $idRecipe = intval($_GET['id']);

    // Prépare et execute la requète SQL pour récuperer notre recipe de manière dynamique
    $req = $pdo->prepare('SELECT * FROM recipes WHERE id = :idRecipe');
    $req->bindParam(':idRecipe', $idRecipe, PDO::PARAM_INT);
    if($req->execute()) {
    	// $editrecipe contient mon recette extrait de la pdo
    	$editrecipe = $req->fetch(PDO::FETCH_ASSOC);
    	if(!empty($editrecipe) && is_array($editrecipe)){ // Ici l'recette existe donc on fait le traitement nécessaire
    		$recipeExist = true; // Mon recipe existe.. donc bon paramètre GET et requête SQL ok

    		// Si l'utilsateur existe, j'instancie la variable $idlink qui me permet de stcocker l'id recipe dans le nom du fichier
    		$idlink = $recipeExist['id'];
    	}
	}
}

//var_dump($_FILES);

// UPLOAD DE FICHIER : Contrôle de l'upload de fichier et de la supergloable $_FILES
if(!empty($_FILES) && isset($_FILES['picture'])) {

    if ($_FILES['picture']['error'] == UPLOAD_ERR_OK AND $_FILES['picture']['size'] <= $maxSize) {

        $nomFichier = $_FILES['picture']['name']; // récupère le nom de mon fichier au sein de la superglobale $_FILES (tableau multi-dimentionnel)
        $tmpFichier = $_FILES['picture']['tmp_name']; // Stockage temporaire du fichier au sein de la superglobale $_FILES (tableau multi-dimentionnel)
        
        
        /* CONTROLE DU TYPE MIME
        *  $file = new finfo(); // Classe FileInfo
           $mimeType = $file->file($_FILES['image']['tmp_name'], FILEINFO_MIME_TYPE); // Retourne le VRAI mimeType

        *
        *
        *
        */

        $file = new finfo(); // Classe FileInfo
        $mimeType = $file->file($_FILES['picture']['tmp_name'], FILEINFO_MIME_TYPE); // Retourne le VRAI mimeType

        $mimTypeOK = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');

        if (in_array($mimeType, $mimTypeOK)) { // in_array() permet de tester si la valeur de $mimeType est contenue dans le tableau $mimTypeOK
                    
           /* CHANGER LE NOM DU FICHIER PAR MESURE DE SECURITE
            * explode() permet de séparer une chaine de caractère en un tableau
            * Ici on aura donc : 
            *                   $newFileName  = array(
                                                    0 => 'nom-de-mon-fichier',
                                                    1 => '.jpg'
                                                );
            */
            $newFileName = explode('.', $nomFichier);
            $fileExtension = end($newFileName); // Récupère la dernière entrée du tableau (créé avec explode) soit l'extension du fichier

            // nom du fichier link au format : recipe-id-timestamp.jpg
            $finalFileName = 'recette-'.$idlink.'-'.time().'.'.$fileExtension; // Le nom du fichier sera donc recipe-id-timestamp.jpg (time() retourne un timsestamp à la seconde)



                if(move_uploaded_file($tmpFichier, $folder.$finalFileName)) { // move_uploaded_file()  retourne un booleen (true si le fichier a été envoyé et false si il y a une erreur)
                    // Ici je suis sur que mon image est au bon endroit
                    $dirlink = $finalFileName;
                    
                    $success = 'Votre fichier a été uplaodé avec succés !';
                   
                }
                else {
                    // Permet d'assigner un link par defaut
                    $dirlink = "link-default.jpg";
                }
        } // if (in_array($mimeType, $mimTypeOK))

        else {
            $error[] = 'Le type de fichier est interdit mime type incorrect !';
        } 


    } // end if ($_FILES['picture']['error'] == UPLOAD_ERR_OK AND $_FILES['picture']['size'] <= $maxSize)
    else {
        $error[] = 'Merci de chosir un fichier image (uniquement au format jpg) à uploader et ne dépassant pas 5Mo !';
    }
} // end if (!empty($_FILES) AND isset($_FILES['picture'])

else {
    // Permet d'assigner l'link par defaut si l'recette n'en a aucun
    $dirlink = "link-default.jpg";
}


// Si le formulaire est soumis et que $recipeExist est vrai (donc qu'on a un recette)
if(!empty($_POST) && $recipeExist == true) {
	foreach($_POST as $key => $value) {
		$post[$key] = trim(strip_tags($value));
	}

	if(empty($post['title']) OR strlen($post['title']) < 2) {
		$error[] = 'Votre prénom doit comporter au moins 2 caractères';
	}

	if(empty($post['content']) OR strlen($post['content']) < 2) {
		$error[] = 'Votre prénom doit comporter au moins 2 caractères';
	}

	if(count($error) > 0) {
		$displayErr = true;
	}
	else {

		//var_dump($post);

		// insertion de la news dans la table "news"
		$upd = $pdo->prepare('UPDATE recipes SET title = :titrerecipe, content = :contentrecipe, link = :linkrecipe WHERE id = :idRecipe');

		// On assigne les valeurs associées au champs de la table (au dessus) aux valeurs du formulaire
		// On passe l'id de l'article pour ne mettre à jour que l'article en cours d'édition (clause WHERE).

        $upd->bindValue(':idRecipe',        $idRecipe,  PDO::PARAM_STR);
        $upd->bindValue(':titrerecipe',     $post['title'],  PDO::PARAM_STR);
		$upd->bindValue(':contentrecipe',   $post['content'], PDO::PARAM_STR);
		$upd->bindValue(':linkrecipe',      $dirlink,         PDO::PARAM_STR);
	
		// Vue que la fonction "execute" retourne un booleen on peut si nécéssaire le mettre dans un if
		if($upd->execute()) { // execute : retourne un booleen -> true si pas de problème, false si souci.
			$formValid    = true;
            // On refait le SELECT pour afficher les infos à jour dans le formulaire
            // Puisque le premier SELECT est avant l'UPDATE
            $req = $pdo->prepare('SELECT * FROM recipes WHERE id = :idRecipe');
            $req->bindParam(':idRecipe', $idRecipe, PDO::PARAM_INT);
            if($req->execute()) {
            // $editrecipe contient ma recette extrait de la pdo
                $editrecipe = $req->fetch(PDO::FETCH_ASSOC);
            }
		}
		else {
			$errorUpdate  = true; // Permettre d'afficher l'erreur
		}

	}
}
include_once '../inc/header_admin.php';
?>

	

	<div id="page-content-wrapper">
            <div class="container-fluid">
        


                <?php if($recipeExist == false): ?>
                <div clas="col-md-12">   
                <!-- message d'erreur si problème url -->
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-times fa-2x" aria-hidden="true"></i> Vous devez choisir un recette avant de le modifier
                    </div>
                    <a class="btn btn-default btn-md" href="view_recipes.php" role="button">Liste des membres</a>
                </div>
                <?php endif; ?>
                
                <?php if($errorUpdate): ?>
                <div clas="col-md-12">   
                <!-- message d'erreur si problème url -->
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-times fa-2x" aria-hidden="true"></i> Problème lors de la mise à jour de votre profil ! <br /> <?php //echo print_r($res->errorInfo()); ?>
                    </div>
                    <a class="btn btn-default btn-md" href="index.php" role="button">Page d'accueil</a>
                </div>
                <?php endif; ?>


                <?php if($displayErr): ?>
                <!-- affichage du tableau d'erreur $error si le formulaire est mal renseigné -->
                <div clas="col-md-12">
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-times fa-2x" aria-hidden="true"></i> <?php echo implode('<br> <i class="fa fa-times fa-2x" aria-hidden="true"></i> ', $error); ?>
                    </div>                    
                </div>
                <?php endif; ?>


                <?php if($formValid): ?>
                <!-- message de confirmation après une modification de news -->
                <div clas="col-md-12">
                    <h1>Modification de la recette <strong><?php echo $editrecipe['title']; ?></strong> effectuée</h1>
                    <div class="alert alert-success" role="alert">
                        <i class="fa fa-check fa-2x" aria-hidden="true"></i> Votre recette a bien été modifié.
                    </div>
                    <a class="btn btn-default btn-md" href="view_recipes.php" role="button">Liste des recettes</a>
                </div>
                <?php endif; ?>


                <?php if($recipeExist == true): ?>
                <div class="row">
					<div class="col-md-12">
                    <h1>Edition de la recette : <strong><?php echo $editrecipe['title']; ?></strong></h1>

                        <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                            <fieldset>
                                <legend>Merci de renseigner les champs obligatoires ;-) </legend>

                                	<div class="form-group input-group">
                                      <span class="input-group-addon" id="basic-addon1">Titre</span>
                                      <input type="text" class="form-control" name="title" value="<?= $editrecipe['title']; ?>" aria-describedby="basic-addon1">
                                    </div><br>
                                    <div class="form-group input-group">
                                        <span class="input-group-addon" id="basic-addon1">Ingrédient</span>
                                        <textarea id="content" name="content" rows="15" class="form-control input-md" ><?= $editrecipe['content']; ?></textarea>
                                    </div><br>
     
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="picture"></label> 
                                        <div class="col-md-10">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxSize; ?>">
                                        <input type="file" class="filestyle" name="picture" data-buttonName="btn-primary">
                                        </div>
                                    </div><!--.form-group-->

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="singlebutton"></label>
                                        <div class="col-md-10">
                                            <input type="hidden" name="id" value="<?php echo $editrecipe['id']; ?>">
                                            <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-primary">Modifier</button> <a href="view_recipes.php" class="btn btn-default">Ne rien changer et retourner à la liste des recettes</a>
                                        </div>
                                    </div>
                            </fieldset>
                        </form>

                	</div>
                </div><!--row-->
            <?php endif; ?>

            </div><!--.container-fluid-->
        </div><!--#page-content-wrapper-->

    </div><!--#wrapper // start in sidebar.php -->
<?php

include_once '../inc/footer_admin.php';

?>
