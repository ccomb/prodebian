<?php

function beginpage() {
print '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Prodebian systems</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
<body>
collaborative management of Prodebian systems (revision 1)<br />
';
}

function print_menu() {
print '<a href="index.php">accueil</a><br />';
if (isset($_SESSION['id_prodebian'])) print '<a href="prodebian.php?id='.$_SESSION['id_prodebian'].'">back to prodebian #'.$_SESSION['id_prodebian'].'</a><br />';
print "<br />";
}

function endpage() {
print '</body></html>
';
}

function error_page() {
beginpage();
print 'Error inserting data to the database.';
endpage();
}

function goto_page($page) {
header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).$page);
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
return(explode(",", trim($string,"}{")));
}

?>
