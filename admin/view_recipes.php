<?php 

session_start();

require_once '../inc/connect.php';
  
    $res = $pdo->prepare('SELECT * FROM recipes ORDER BY id DESC');
    $res->execute();

    $utilisateurs = $res->fetchAll(PDO::FETCH_ASSOC);

include_once '../inc/header_admin.php';
?>
<h2 class="text-center">Liste des recettes</h2>


<table class="table table-striped">
    <thead>
      <tr>
        <th>id</th>
        <th>Titre</th>
        <th>Contenu</th>
        <th>Visuel</th>
        <th>Date de publication</th>
        <th>Créateur</th>
        <th>Actions</th>
      </tr>
    </thead>
<?php         
    foreach($utilisateurs as $user){
?>
    <tbody>
      <tr>
        <td><?php echo $user['id']; ?></td>
        <td><?php echo $user['title']; ?></td>
        <td><?php echo $user['content']; ?></td>
        <td><?php echo '<img src="'.$user['link'].'" alt="avatar membre" width="50">'?></td>
        <td><?php echo $user['date_publish']; ?></td>
        <td><?php echo $user['id_user']; ?></td>
        <td>
          <a type="button" class="btn btn-primary" href="edit_recipe.php?id=<?php echo $user['id'];?>">Modifier</a>
          <a type="button" class="btn btn-danger" href="delete_recipe.php?id=<?php echo $user['id'];?>">Supprimer</a>
          
        </td> 
      </tr>
    </tbody>
<?php } ?>
</table>
<?php

include_once '../inc/footer_admin.php';

?>

