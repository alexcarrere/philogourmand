<?php 

session_start();

require_once '../inc/connect.php'; 

if (empty($_SESSION) || !isset($_SESSION['user']['role'])){
    header('Location: ../index.php');
}

$post = array(); // Contiendra les données du formulaire nettoyées
$errors = array(); // contiendra nos éventuelles erreurs

$showErrors = false;
$success = false; 

$title = '';
$content = '';
$dirlink = "link-default.jpg";

$folder = '../img/'; // création de la variable indiquant le chemin du répertoire destination pour les fichiers uploadés (important  : le slash à la fin de la chaine de caractère).
$maxSize = 1000000 * 5; // 5Mo

if(!empty($_FILES) && isset($_FILES['picture'])) {

    if ($_FILES['picture']['error'] == UPLOAD_ERR_OK AND $_FILES['picture']['size'] <= $maxSize) {

        $nomFichier = $_FILES['picture']['name']; // récupère le nom de mon fichier au sein de la superglobale $_FILES (tableau multi-dimentionnel)
        $tmpFichier = $_FILES['picture']['tmp_name']; // Stockage temporaire du fichier au sein de la superglobale $_FILES (tableau multi-dimentionnel)
        
        $file = new finfo(); // Classe FileInfo
        $mimeType = $file->file($_FILES['picture']['tmp_name'], FILEINFO_MIME_TYPE); // Retourne le VRAI mimeType

        $mimTypeOK = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');

        if (in_array($mimeType, $mimTypeOK)) { // in_array() permet de tester si la valeur de $mimeType est contenue dans le tableau $mimTypeOK
                    

            $newFileName = explode('.', $nomFichier);
            $fileExtension = end($newFileName); // Récupère la dernière entrée du tableau (créé avec explode) soit l'extension du fichier

            // nom du fichier link au format : recipe-id-timestamp.jpg
            $finalFileName = 'recette-'.time().'.'.$fileExtension; // Le nom du fichier sera donc recipe-id-timestamp.jpg (time() retourne un timsestamp à la seconde)


                if(move_uploaded_file($tmpFichier, $folder.$finalFileName)) { // move_uploaded_file()  retourne un booleen (true si le fichier a été envoyé et false si il y a une erreur)
                    // Ici je suis sur que mon image est au bon endroit
                    $dirlink = $finalFileName;
                    
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

if (!empty($_POST)) {
	
	foreach ($_POST as $key => $value) { // Nettoyage des données
		$post[$key] = trim(strip_tags($value)); // récupération du _POST dans un tableau
	}
	if(strlen($post['title']) < 2 || strlen($post['title']) > 50){ // on défini les propriétés de 'title'
        $errors[] = '<div class="alert alert-danger" role="alert">Votre nom de recette doit comporter entre 2 et 50 caractères</div>';
    }
    if(strlen($post['content']) < 2 ){ // on défini les propriétés de 'content'
        $errors[] = '<div class="alert alert-danger" role="alert">La recette doit comporter au minimum 2 ingrédients</div>'; 
	}
	else {
	    $reqEmail = $pdo->prepare('SELECT title FROM recipes WHERE title = :title'); // Vérification au cas ou l'email est déjà dans la pdo
        $reqEmail->bindValue(':title', $post['title']);
        $reqEmail->execute();
       
        if($reqEmail->rowCount() != 0){ // Si l'email n'est pas dans la pdo alors, on peu crée l'utilisateur
             $errors[] = '<div class="alert alert-danger" role="alert">La recette existe déjà !</div>';
        }
	} 

	if(count($errors) > 0){  // On compte les erreurs, si il y en as (supérieur a 0), on passera la variable $showErrors à true.
        $showErrors = true; // valeur booleen // permettra d'afficher nos erreurs s'il y en a

        $title = $post['title'];
        $content = $post['content'];
    }
    else { 
    	// Insertion dans la pdo 
    	$res = $pdo->prepare('INSERT INTO recipes (title, content, date_publish, link, id_user) VALUES(:title, :content, NOW(), :linkrecipe, :id_user )');

        $res->bindValue(':title',		 $post['title'], 	PDO::PARAM_STR);
        $res->bindValue(':content', 	 $post['content'],	PDO::PARAM_STR);
        $res->bindValue(':linkrecipe',   $dirlink,          PDO::PARAM_STR);
        $res->bindValue(':id_user',   $_SESSION['user']['id'],   	    PDO::PARAM_INT);
        
    
	    if($res->execute()){
	        $success = true; // Pour afficher le message de réussite si tout est bon
	    }
	    else {
	        die;
	    }
    }
}

include_once '../inc/header_admin.php';

if($success){ // On affiche la réussite si tout fonctionne
    echo '<div class="alert alert-success" role="alert"> La recette à bien été créer ! </div>';
}

if($showErrors){
    echo implode('<br>', $errors);
}
?>


<h1 class="text-center">Ajouter une recette</h1>
<br>

<div class="container">

	<div class="alert alert-info" role="alert">Merci de remplir tout les champs correctement</div>	

	<form method="post" class="pure-form pure-form-aligned" enctype="multipart/form-data">

        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Titre</span>
            <input type="text" class="form-control" name="title" placeholder="Nom de la recette" aria-describedby="basic-addon1" value="<?=$title;?>">
        </div>
        <br>

        <div class="input-group">
            <span class="input-group-addon" id="basic-addon1">Descriptif de la recette</span>
            <textarea id="content" name="content" rows="15" class="form-control input-md" placeholder="Descriptif complet de la recette pour le client"><?=$content;?></textarea>
        </div>
        <br>

        <div class="form-group">
            <div class="row">
                <div class="col-md-10">
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $maxSize; ?>">
                    <input type="file" class="filestyle" name="picture" data-buttonName="btn-primary">
                </div>

                <div class="col-md-2">
                    <input type="submit" class="btn btn-success" value="Ajouter la recette">
                </div>
            </div>
        </div><!--.form-group-->

	</form>

</div>
<?php

include_once '../inc/footer_admin.php';

?>
