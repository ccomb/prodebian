<?php

function beginpage() {
	print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Prodebian systems</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
<body>
';
}

function print_menu() {
	print '<a href="index.php">home</a> <a href="findprodebian.php">find</a> ';
	if (isset($_SESSION['id_prodebian'])) print '<a href="prodebian.php?id='.$_SESSION['id_prodebian'].'">back to prodebian #'.$_SESSION['id_prodebian'].'</a><br />';
	print "<br />";
}

function endpage() {
	// AJOUTER UN FORMULAIRE POUR ENVOYER UN COMMENTAIRE SUR LA PAGE
	print '</body></html>';
}

function goto_page($page) {
	header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).$page);
	exit();
}

function connect_database() {
	return pg_connect("host=localhost dbname=prodebian user=ccomb password=prodebian");
}

function array2string($array) {
	//PHP array -> pgsql array
	return "{".implode(',',$array)."}";
}

function string2array($string) {
	//pgsql array -> PHP array
	if($string=="{}") return array(); 
	return(explode(",", trim($string,"}{")));
}

// REMOVE HTML AND PHP TAGS, AND LIMIT THE VARIABLES TO 32 CHAR.
function purge_data() {
	foreach($_POST as $key => $value) {
		// this purges the value but not the key!
		if($key=="desc") $_POST[$key]=substr(strip_tags($value),0,900);
		else $_POST[$key]=substr(strip_tags($value),0,32);
	}
	foreach($_GET as $key => $value) {
		// this purges the value but not the key!
		$_GET[$key]=substr(strip_tags($value),0,32);
	}
}

?>
