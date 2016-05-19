<?php require_once '../inc/connect.php';
  
    $res = $pdo->prepare('SELECT * FROM users ORDER BY id DESC');
    $res->execute();

    $utilisateurs = $res->fetchAll(PDO::FETCH_ASSOC);

?>

<h2 class="text-center">Liste des Utilisateurs</h2>

  <hr>
<table class="table table-striped">
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
<?php         
    foreach($utilisateurs as $user){
?>
    <tbody>
      <tr>
        <td><?php echo $user['id']; ?></td>
        <td><?php echo $user['nickname']; ?></td>
        <td><?php echo $user['firstname']; ?></td>
        <td><?php echo $user['lastname']; ?></td>
        <td><?php echo $user['email']; ?></td>
        <td><?php echo $user['date_reg']; ?></td>
        <td><?php echo $user['role']?></td>
        <td>
          <a type="button" class="btn btn-primary" href="edit_user.php?id=<?php echo $user['id'];?>">Modifier</a>
          <a type="button" class="btn btn-primary" href="delete_user.php?id=<?php echo $user['id'];?>">Supprimer</a>
        
          
        </td> 
      </tr>
    </tbody>

<?php } ?>
</table>


