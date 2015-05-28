<?php
	
	require_once("ConnectMySQL.php");

	$db = new ConnectMySQL();

	$view = array();

	if(!empty($_GET['id'])){
		$db->id = $_GET['id'];
		$view = $db->view("posts");
	}

	// var_dump($view);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>ConnectMySQL</title>
	<link rel="stylesheet" href="">
</head>
<body>
	<?php

	echo empty($view['name'])? null : $view['name'];

	?>
</body>
</html>