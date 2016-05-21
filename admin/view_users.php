<?php 

session_start();

require_once '../inc/connect.php';

if (!empty($_SESSION) && isset($_SESSION['user']['role'])){

    if ($_SESSION['user']['role'] != 'admin') {
        header('Location: administration.php');
    }
    
} else {
    header('Location: ../index.php');
}
  
$res = $pdo->prepare('SELECT * FROM users INNER JOIN authorization ON users.id = authorization.id_user ORDER BY users.id ASC');
$res->execute();

$utilisateurs = $res->fetchAll(PDO::FETCH_ASSOC);


include_once '../inc/header_admin.php';
?>

<h2 class="text-center">Liste des Utilisateurs</h2>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-condensed">
        <thead>
          <tr>
            <th>id</th>
            <th>Pseudal </th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Date d'enregistrement</th>
            <th>Rôle</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
    <?php         
        foreach($utilisateurs as $user){
    ?>
          <tr>
            <td class="text-center"><?php echo $user['id']; ?></td>
            <td class="text-center"><?php echo $user['nickname']; ?></td>
            <td class="text-center"><?php echo $user['firstname']; ?></td>
            <td class="text-center"><?php echo $user['lastname']; ?></td>
            <td class="text-center"><?php echo $user['email']; ?></td>
            <td class="text-center"><?php echo date('d/m/Y H:i:m', strtotime($user['date_reg'])); ?></td>
            <td class="text-center"><?php echo $user['role']?></td>
            <td class="text-center">
                <a type="button" class="btn btn-primary" href="edit_user.php?id=<?php echo $user['id'];?>">Modifier</a>

                <!-- On empèche l'utilisateur courant de pouvoir supprimer son compte -->
                <?php if($_SESSION['user']['id'] == $user['id']) : ?>
                    <a type="button" class="btn btn-danger" href="#" disabled="disabled">Supprimer</a>
                <?php else : ?>
                    <a type="button" class="btn btn-danger" href="delete_user.php?id=<?php echo $user['id'];?>">Supprimer</a>
                <?php endif; ?>
              
            </td> 
          </tr>

    <?php } ?>
        </tbody>
    </table>
</div>
<?php

include_once '../inc/footer_admin.php';

?>