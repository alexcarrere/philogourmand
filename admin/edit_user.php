<?php

session_start();

// connection à la base
require_once '../inc/connect.php';

$post = array();
$error = array();

$errorUpdate  = false; // erreur lors de la mise à jour de la table
$displayErr   = false; 
$formValid    = false;
$userExist    = false;
$allowRole = ['editor', 'admin']; // Liste des genres autorisés

$folder = 'img/'; // création de la variable indiquant le chemin du répertoire destination pour les fichiers uploadés (important  : le slash à la fin de la chaine de caractère).
$maxSize = 1000000 * 5; // 5Mo


// vérification des paramètres GET et appel des champs user correspondants
if(isset($_GET['id']) AND !empty($_GET['id']) AND is_numeric($_GET['id'])) {

    $idUser = intval($_GET['id']);

    // Prépare et execute la requète SQL pour récuperer notre user de manière dynamique
    $req = $pdo->prepare('SELECT * FROM users WHERE id = :idUser');
    $req->bindParam(':idUser', $idUser, PDO::PARAM_INT);
    if($req->execute()) {
        // $edituser contient mon utilisateur extrait de la pdo
        $edituser = $req->fetch(PDO::FETCH_ASSOC);
        if(!empty($edituser) && is_array($edituser)){ // Ici l'utilisateur existe donc on fait le traitement nécessaire
            $userExist = true; // Mon user existe.. donc bon paramètre GET et requête SQL ok
        }
    }
}




// Si le formulaire est soumis et que $userExist est vrai (donc qu'on a un utilisateur)
if(!empty($_POST) && $userExist == true) {
    foreach($_POST as $key => $value) {
        $post[$key] = trim(strip_tags($value));
    }

    if(empty($post['nickname']) OR strlen($post['nickname']) < 2) {
        $error[] = 'Votre prénom doit comporter au moins 2 caractères';
    }

    if(empty($post['firstname']) OR strlen($post['firstname']) < 2) {
        $error[] = 'Votre prénom doit comporter au moins 2 caractères';
    }
    if(empty($post['lastname']) OR strlen($post['lastname']) < 2) {
        $error[] = 'Votre prénom doit comporter au moins 2 caractères';
    }
    if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)){ 
        $errors[] = 'L\'adresse email est invalide';
    }

    if(count($error) > 0) {
        $displayErr = true;
    }
    else {

        //var_dump($post);

        // insertion de la news dans la table "news"
        $upd = $pdo->prepare('UPDATE users SET nickname = :titreUser, firstname = :firstnameuser, lastname = :lastnameuser, email = :emailuser, role = :roleuser WHERE id = :idUser');

        // On assigne les valeurs associées au champs de la table (au dessus) aux valeurs du formulaire
        // On passe l'id de l'article pour ne mettre à jour que l'article en cours d'édition (clause WHERE).

        $upd->bindValue(':idUser',          $idUser,  PDO::PARAM_INT);
        $upd->bindValue(':titreUser',       $post['nickname'],  PDO::PARAM_STR);
        $upd->bindValue(':firstnameuser',   $post['firstname'],  PDO::PARAM_STR);
        $upd->bindValue(':lastnameuser',    $post['lastname'], PDO::PARAM_STR);
        $upd->bindValue(':emailuser',       $post['email']);
        $upd->bindValue(':roleuser',        $post['role']);
        
    
        // Vue que la fonction "execute" retourne un booleen on peut si nécéssaire le mettre dans un if
        if($upd->execute()) { // execute : retourne un booleen -> true si pas de problème, false si souci.
            $formValid    = true;
            // On refait le SELECT pour afficher les infos à jour dans le formulaire
            // Puisque le premier SELECT est avant l'UPDATE
            $req = $pdo->prepare('SELECT * FROM users WHERE id = :idUser');
            $req->bindParam(':idUser', $idUser, PDO::PARAM_INT);
            if($req->execute()) {
            // $edituser contient ma utilisateur extrait de la pdo
                $edituser = $req->fetch(PDO::FETCH_ASSOC);
            }
        }
        else {
            $errorUpdate  = true; // Permettre d'afficher l'erreur
        }

    }
}
include_once '../inc/header_admin.php';
?>

    

    <div id="page-firstname-wrapper">
            <div class="container-fluid">
        


                <?php if($userExist == false): ?>
                <div clas="col-md-12">   
                <!-- message d'erreur si problème url -->
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-times fa-2x" aria-hidden="true"></i> Vous devez choisir un utilisateur avant de le modifier
                    </div>
                    <a class="btn btn-default btn-md" href="view_users.php" role="button">Liste des membres</a>
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
                    <h1>Modification de la utilisateur <strong><?php echo $edituser['nickname']; ?></strong> effectuée</h1>
                    <div class="alert alert-success" role="alert">
                        <i class="fa fa-check fa-2x" aria-hidden="true"></i> Votre utilisateur a bien été modifié.
                    </div>
                    <a class="btn btn-default btn-md" href="view_users.php" role="button">Liste des utilisateurs</a>
                </div>
                <?php endif; ?>


                <?php if($userExist == true): ?>
                <div class="row">
                    <div class="col-md-12">
                    <h1>Edition de l'utilisateur : <strong><?php echo $edituser['nickname']; ?></strong></h1>

                        <form class="form-horizontal" method="POST" enctype="multipart/form-data">
                            <fieldset>
                                <legend>Merci de renseigner les champs obligatoires</legend>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="nickname">Pseudal de l'utilisateur</label>  
                                        <div class="col-md-10">
                                            <input id="nickname" name="nickname" type="text" class="form-control input-md" required="true" value="<?php echo $edituser['nickname']; ?>">
                                        </div>
                                    </div><!--.form-group-->
                                
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="firstname">Nom de l'utilisateur</label>  
                                        <div class="col-md-10">
                                            <input id="firstname" name="firstname" type="text" class="form-control input-md" required="true" value="<?php echo $edituser['firstname']; ?>">
                                        </div>
                                    </div><!--.form-group-->

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="lastname">Prénom de l'utilisateur</label>  
                                        <div class="col-md-10">
                                            <input id="lastname" name="lastname" type="text" class="form-control input-md" required="true" value="<?php echo $edituser['lastname']; ?>">
                                        </div>
                                    </div><!--.form-group-->

                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="email">Email *</label>  
                                        <div class="col-md-10">
                                            <input id="email" name="email" type="email" class="form-control input-md" required="true" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required="true" value="<?php echo $edituser['email']; ?>">
                                            <span class="help-block">Merci d'indiquer votre email</span>  
                                        </div>
                                    </div><!--.form-group-->

                                    <div class="form-group">
                                        <label class="col-md-4 control-label" for="role">Rôle</label>
                                        <div class="col-md-4">
                                            <div class="radio">
                                                <label for="role-0">
                                                    <input type="radio" name="role" id="role-0" value="editor"> Editeur
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label for="role-1">
                                                    <input type="radio" name="role" id="role-1" value="admin"> Administrateur
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-12 control-label" for="singlebutton"></label>
                                        <div class="col-md-10">
                                            <input type="hidden" name="id" value="<?php echo $edituser['id']; ?>">
                                            <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-primary">Modifier</button> <a href="view_users.php" class="btn btn-default">Ne rien changer et retourner à la liste des utilisateurs</a>
                                        </div>
                                    </div>
                            </fieldset>
                        </form>

                    </div>
                </div><!--row-->
            <?php endif; ?>

            </div><!--.container-fluid-->
        </div><!--#page-firstname-wrapper-->

    </div><!--#wrapper // start in sidebar.php -->
<?php

include_once '../inc/footer_admin.php';

?>