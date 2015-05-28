<?php
	
	require_once("ConnectMySQL.php");

	$db = new ConnectMySQL();

	if(!empty($_GET['id'])){
		$db->id = $_GET['id'];
		$delete = $db->delete("posts");
	}

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

		if(!empty($delete)){
			echo 'deleted';
		}else{
			echo 'error';
		}
	?>
</body>
</html>