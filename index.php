<?php
	
	require_once("ConnectMySQL.php");

	$db = new ConnectMySQL();

	$all = $db->all("posts");



	// var_dump($all);

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

	$c = 0;
	foreach ($all as $key => $value) {
		echo $value[$c]['id'].':'.$value[$c]['name'].'<br>';
		$c++;
	}

	?>
</body>
</html>