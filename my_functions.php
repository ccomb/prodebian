<?php

function my_beginpage() {
	print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Prodebian systems</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
<body>
';
// DEBUG: uncomment to show data on each page.
//print_r($_SERVER);
//print "GET=";print_r($_GET);print "<br />POST=";print_r($_POST);print "<br />SESSION=";print_r($_SESSION);print "<br />";
}

function my_printmenu() {
	print '<a href="index.php">home</a> ';
	print '<a href="findprodebian.php">find</a> ';
	if (isset($_SESSION['id_prodebian'])) print '<a href="prodebian.php?id='.$_SESSION['id_prodebian'].'">back to prodebian #'.$_SESSION['id_prodebian'].'</a> ';
	if (isset($_GET['logout'])) { unset($_SESSION['username']); unset($_SESSION['password']); }
	if (isset($_SESSION['username']) AND isset($_SESSION['password'])) {
		// logout link only if logged in
		if($_SERVER['QUERY_STRING']=='') $and='';
		else $and='&';
		print '<a href="'.$_SERVER['PHP_SELF'].'?logout'.$and.$_SERVER['QUERY_STRING'].'">logout('.$_SESSION['username'].')</a><br />';
	}	
	print "<br />";
}

function my_endpage() {
	// AJOUTER UN FORMULAIRE POUR ENVOYER UN COMMENTAIRE SUR LA PAGE
	// uncomment to show POST and GET on each page.
	//print "GET=";print_r($_GET);print "<br />POST=";print_r($_POST);print "<br />SESSION=";print_r($_SESSION);print "<br />";
	print '</body></html>';
}

function my_gotopage($page) {
	header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).$page);
	exit();
}

function my_connectdatabase() {
	return pg_connect("host=localhost dbname=prodebian user=ccomb password=prodebian");
}

function my_array2string($array) {
	//PHP array -> pgsql array
	return "{".implode(',',$array)."}";
}

function my_string2array($string) {
	//pgsql array -> PHP array
	if($string=="{}") return array(); 
	return(explode(",", trim($string,"}{")));
}

// REMOVE HTML AND PHP TAGS, AND LIMIT THE VARIABLES TO 32 CHAR.
function my_purge_data() {
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
// authenticate and retry the url after authentication
function my_authenticate($id_owner) {
	global $database;
	$res = pg_query($database, "SELECT username,password FROM owners WHERE id_owner='".$id_owner."';");
	$owners = pg_fetch_array($res);
	// if user/password are provided, store them in session.
	if(isset($_POST['username']) AND isset($_POST['password'])) {
		$_SESSION['username']=$_POST['username'];
		$_SESSION['password']=$_POST['password'];
	}
	// get the needed user/password and check with current session user/password
	if(isset($_SESSION['username']) AND isset($_SESSION['password'])) {
		if($_SESSION['password']==$owners['password'] AND $_SESSION['username']==$owners['username']) return 1;
	}
	// authentication failed, we must authenticate and resubmit query with previous POST and GET data
	my_beginpage();
	my_printmenu();
	if(isset($_POST['authenticate'])) print 'INVALID PASSWORD !<br />';
	unset($_POST['username']);
	unset($_POST['password']);
	print '
	<form action="'.$_SERVER['REQUEST_URI'].'" method="POST">
	<input type="hidden" name="username" value="'.$value.'" />
	Password for user "'.$owners['username'].'": <input type="text" name="password" size="32" maxlength="32" /><br />
	<input type="hidden" name="username" value="'.$owners['username'].'" />
	';
	foreach($_POST as $key => $value) print '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
	print '
	<button name="authenticate" type="submit">submit</button>
	</form>
	';
	my_endpage();
	exit();
}
?>
