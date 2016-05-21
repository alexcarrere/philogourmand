<?php 

session_start();

require_once '../inc/connect.php';

if (empty($_SESSION) || !isset($_SESSION['user']['role'])){
    header('Location: ../index.php');
}

  $res = $pdo->prepare('SELECT recipes.id, title, content, link, date_publish, nickname FROM recipes LEFT JOIN users ON recipes.id_user = users.id ORDER BY recipes.id ASC');
  $res->execute();

  $utilisateurs = $res->fetchAll(PDO::FETCH_ASSOC);

include_once '../inc/header_admin.php';
?>
<h2 class="text-center">Liste des recettes</h2>
<hr>

<div class="table-responsive">
  <table class="table table-striped table-bordered table-condensed">
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
      <tbody>
      <?php         
          foreach($utilisateurs as $user){
      ?>
            <tr>
              <td class="text-center"><?php echo $user['id']; ?></td>
              <td class="text-center"><?php echo $user['title']; ?></td>
              <td class="text-center"><?php echo $user['content']; ?></td>
              <td class="text-center"><?php echo '<img src="'.$user['link'].'" alt="Photo recette" width="50">'?></td>
              <td class="text-center"><?php echo date('d/m/Y H:i:m', strtotime($user['date_publish'])); ?></td>

              <!-- Si l'utilisateur qui à posté la recette n'existe plus --> 
              <?php if (empty($user['nickname'])) : ?> 
                <td class="text-center">PhiloGourmet</td>  <!-- On affiche un pseudo par défaut --> 
              <?php else : ?>
                <td class="text-center"><?php echo $user['nickname']; ?></td> <!-- Sinon on affiche le pseudo de l'auteur --> 
              <?php endif; ?>
              <td class="text-center">
                <a type="button" class="btn btn-primary" href="edit_recipe.php?id=<?php echo $user['id'];?>">Modifier</a>
                <a type="button" class="btn btn-danger" href="delete_recipe.php?id=<?php echo $user['id'];?>">Supprimer</a>
              </td> 
            </tr>
      <?php } ?>
      </tbody>
  </table>
</div>
<?php

include_once '../inc/footer_admin.php';

?>

