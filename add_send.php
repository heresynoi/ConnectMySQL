<?php
	
	require_once("ConnectMySQL.php");

	$db = new ConnectMySQL();

	$data = array(
		"name" => $_POST["name"],
		"text" => $_POST["text"]
		);

	$view = $db->add("posts",$data);

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
	added
</body>
</html>