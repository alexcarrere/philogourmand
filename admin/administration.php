
<?php 
session_start();
require_once '../inc/header_admin.php';
require_once '../inc/connect.php';
if (empty($_SESSION) || !isset($_SESSION['user']['role'])){
	header('Location: ../index.php');
	die;
}

if($_SESSION['user']['role'] == 'admin' || $_SESSION['user']['role'] == 'editor'){

  // selection des informations du restaurant dans la table resto pour l'id  1
      $res = $pdo->prepare('SELECT * FROM resto WHERE id = :id');
      $res->bindValue(':id' ,1  , PDO::PARAM_INT);
        
      if($res->execute()){

      $restaurant = $res->fetch(PDO::FETCH_ASSOC);
      $idRestaurant = $restaurant['id'];
      $title = $restaurant['title'];
      $adress = $restaurant['adress'];
      $zipcode = $restaurant['zipcode'];
      $city = $restaurant['city'];
      $phone = $restaurant['phone'];
      $email_restaurant = $restaurant['email'];


  
	echo '<p>Bonjour <strong>'.$_SESSION['user']['nickname'].'</strong>, <br><br> Vous vous trouvez dans la parti administrative de votre restaurant <strong>'.$title.'</strong></p>';
	echo '<p> Les coordonnées actuelles sont :<br><br>'.$adress.'</p>';
	echo '<p>'.$zipcode.'&nbsp;'.$city.'</p>';
	echo '<p><span class="glyphicon glyphicon-phone-alt" aria-hidden="true"></span>&nbsp;&nbsp;'.$phone.'</p>';
	echo '<p>'.$email_restaurant.'</p>';
	echo '<br><br><p>N\'oubliez pas de vous déconnecter en partant...</p>';
	}
}
else {

	echo 'vous n\'êtes pas autorisé à aporter des modifications';
}
require_once '../inc/footer_admin.php';
?> 

