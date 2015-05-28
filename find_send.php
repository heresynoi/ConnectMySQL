<?php
	
	require_once("ConnectMySQL.php");

	$db = new ConnectMySQL();

	$data = array(
		"name" => $_POST["name"]
		);

	$find = $db->find("posts",$data);

	var_dump($find);

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
</body>
</html>