<!DOCTYPE html>
<html>
<head>
	<title>menu pour administration</title>
</head>
<body>
<?php 
session_start();
if($_SESSION['role'] == 'admin'){



}
elseif($_SESSION['role'] == 'editor'){


}
else {

	echo 'vous n\'êtes pas autorisé à aporter des modifications';
}
?>
</body>
</html>