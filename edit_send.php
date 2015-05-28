<?php
	
	require_once("ConnectMySQL.php");

	$db = new ConnectMySQL();

	$db->id = $_POST["id"];

	$data = array(
		"name" => $_POST["name"],
		"text" => $_POST["text"]
		);

	$view = $db->edit("posts",$data);

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
	edited
</body>
</html>