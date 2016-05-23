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


// vérification des paramètres GET et appel des champs user correspondants
if(isset($_GET['id']) AND !empty($_GET['id']) AND is_numeric($_GET['id'])) {

    $idUser = intval($_GET['id']);

    // Prépare et execute la requète SQL pour récuperer notre user de manière dynamique
    $req = $pdo->prepare('SELECT * FROM users INNER JOIN authorization ON users.id = authorization.id_user WHERE users.id = :idUser');
    $req->bindParam(':idUser', $idUser, PDO::PARAM_INT);
    if($req->execute()) {
        // $edituser contient mon utilisateur extrait de la pdo
        $edituser = $req->fetch(PDO::FETCH_ASSOC);
        if(!empty($edituser) && is_array($edituser)){ // Ici l'utilisateur existe donc on fait le traitement nécessaire
            $userExist = true; // Mon user existe.. donc bon paramètre GET et requête SQL ok
        }
    }
    else {

         die(var_dump($req->errorInfo()));
    }
}




// Si le formulaire est soumis et que $userExist est vrai (donc qu'on a un utilisateur)
if(!empty($_POST) && $userExist == true) {
    foreach($_POST as $key => $value) {
        $post[$key] = trim(strip_tags($value));
    }

    if(!preg_match( "#^[A-Z]+[a-zA-Z0-9À-ú]{1,}#" , $post['nickname'] )) {
        $error[] = 'Votre pseudo doit comporter au moins 2 caractères';
    }

    if(!preg_match( "#^[A-Z]+[a-zA-Z0-9À-ú]{1,}#" , $post['firstname'] )) {
        $error[] = 'Votre prénom doit comporter au moins 2 caractères';
    }
    if(!preg_match( "#^[A-Z]+[a-zA-Z0-9À-ú]{1,}#" , $post['lastname'] )) {
        $error[] = 'Votre nom doit comporter au moins 2 caractères';
    }
    if(!preg_match( " /^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ " , $post['email'] )){ 
        $error[] = 'L\'adresse email est invalide';
    }

    if(count($error) > 0) {
        $displayErr = true;
    }
    else {

        //var_dump($post);
            /*update t1 inner join t2 on t1.c0 = t2.c0
set t1.c1 = 10, t2.c1 = 10;*/

        //Si un admin essaye de changer son profil, il ne peut pas modifier son propre role
        if($_SESSION['user']['id'] == $idUser) {
            $role = 'admin';
        } else {
            $role = $post['role'];
        }
        
        // insertion de la news dans la table "news"
        $upd = $pdo->prepare('UPDATE users INNER JOIN authorization ON users.id = authorization.id_user SET nickname = :titreUser, firstname = :firstnameuser, lastname = :lastnameuser, email = :emailuser, role = :roleuser WHERE users.id = :idUser');

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
            $reqDeux = $pdo->prepare('SELECT * FROM users INNER JOIN authorization ON users.id = authorization.id_user WHERE users.id = :idUser');
            $reqDeux->bindParam(':idUser', $idUser, PDO::PARAM_INT);

            if($reqDeux->execute()) {
            // $edituser contient ma utilisateur extrait de la pdo
                $edituser = $reqDeux->fetch(PDO::FETCH_ASSOC);

                //Si l'utilisateur modifié est celui qui fait la modification
                if($_SESSION['user']['id'] == $idUser) {
                    $_SESSION['user'] = [
                        'id'        => $idUser,
                        'nickname'  => $edituser['nickname'],
                        'firstname' => $edituser['firstname'],
                        'lastname'  => $edituser['lastname'],
                        'email'     => $edituser['email'],
                        'role'      => $edituser['role']
                    ];
                }
            }
            else {

                 die(var_dump($reqDeux->errorInfo()));
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
        


                <?php if(!$userExist): ?>
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

                <?php if($userExist) : ?>
                <div class="container">
                    <div class="col-md-12">
                    <h1 class="text-center">Edition de l'utilisateur : <strong><?php echo $edituser['nickname']; ?></strong></h1>

                        <form class="pure-form pure-form-aligned" method="POST" enctype="multipart/form-data">
                            <div class="alert alert-info" role="alert"> Merci de remplir tous les champs correctement</div>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Pseudo</span>
                                    <input type="text" class="form-control" name="nickname" value="<?php echo $edituser['nickname']; ?>" aria-describedby="basic-addon1">
                                </div>
                                <br>
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Prénom</span>
                                    <input type="text" class="form-control" name="firstname" value="<?php echo $edituser['firstname']; ?>" aria-describedby="basic-addon1">
                                </div>
                                <br>   
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon1">Nom</span>
                                    <input type="text" class="form-control" name="lastname" value="<?php echo $edituser['lastname']; ?>" aria-describedby="basic-addon1">
                                </div>
                                <br>
                                <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon1">@</span>
                                        <input type="email" class="form-control" name="email" value="<?php echo $edituser['email']; ?>" aria-describedby="basic-addon1">
                                </div>
                                <br>  
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <input type="radio" aria-label="radio_editor" value="editor" name="role" <?php if($edituser['role'] == 'editor'){?>checked<?php }?>>
                                            </span>
                                            <input type="text" class="form-control" aria-label="role_editor" placeholder="Rôle : Editeur" disabled="disabled">
                                            </div><!-- /input-group -->
                                    </div><!-- /.col-lg-6 -->

                                    <div class="col-lg-6">
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                            <input type="radio" aria-label="radio_admin" value="admin" name="role" <?php if($edituser['role'] == 'admin'){?>checked<?php }?>>
                                            </span>
                                            <input type="text" class="form-control" aria-label="role_admin" placeholder="Rôle : Administrateur" disabled="disabled">
                                        </div><!-- /input-group -->
                                    </div><!-- /.col-lg-6 -->
                                </div><!-- /.row -->

                                <div class="form-group">
                                    <label class="col-md-12 control-label" for="singlebutton"></label>
                                    <div class="col-md-10">
                                        <input type="hidden" name="id" value="<?php echo $edituser['id']; ?>">
                                        <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-primary">Modifier</button> <a href="view_users.php" class="btn btn-default">Ne rien changer et retourner à la liste des utilisateurs</a>
                                    </div>
                                </div>
                        </form>

                    </div>
                </div><!--container -->
            <?php endif; ?>

            </div><!--.container-fluid-->
        </div><!--#page-firstname-wrapper-->

<?php

include_once '../inc/footer_admin.php';

?>

?>
