<?php
function my_debug() {
//print_r($_SERVER);
print "GET=";print_r($_GET);print "<br />POST=";print_r($_POST);print "<br />SESSION=";print_r($_SESSION);print "<br />";
}
//--------------------------
function my_beginpage() {
	print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN">
<html>
	<head>
		<title>Prodebian systems</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="generator" content="Bluefish 0.12 http://bluefish.openoffice.nl/" />
	</head>
<body>
';
}
//--------------------------
function my_printmenu() {
	// HOME
	print '<a href="index.php">home</a>';
	// NEW
	print ' | <a href="createprodebian.php">new</a> ';
	// SEARCH
	print ' | <a href="findprodebian.php">search</a> ';
	// NEXT RESULT
	if(isset($_SESSION['id_prodebian']) AND isset($_SESSION['searchresult'][$_SESSION['id_prodebian']])) {
		$id_list = array_keys($_SESSION['searchresult']);
		$current = array_search($_SESSION['id_prodebian'], $id_list);
		if(isset($id_list[$current+1])) {
			print ' (<a href="prodebian.php?id='.$id_list[$current+1].'">next</a>)';
		}
	}
	// CURRENT PRODEBIAN
	if (isset($_SESSION['id_prodebian']) AND $_SERVER['SCRIPT_NAME']!="/prodebian.php") { 
		print ' | <a href="prodebian.php">back to #'.$_SESSION['id_prodebian'].'</a> (<a href="actionlist.php">actions</a>)';
	}
	if (isset($_GET['logout'])) { unset($_SESSION['username']); unset($_SESSION['password']); }
	// LOGOUT
	if (isset($_SESSION['username']) AND isset($_SESSION['password'])) {
		// display logout link only if logged in
		if($_SERVER['QUERY_STRING']=='') $and='';
		else $and='&';
		print ' | <a href="owner.php">'.$_SESSION['username'].'</a>';
		print ' (<a href="'.$_SERVER['PHP_SELF'].'?logout'.$and.$_SERVER['QUERY_STRING'].'">logout</a>)';
	}
	print '<br /><hr size="1" width="100%" />';
}
//--------------------------
function my_endpage() {
	// AJOUTER UN FORMULAIRE POUR ENVOYER UN COMMENTAIRE SUR LA PAGE
	print '<hr size="1" width="100%" /><div style="text-align: right"><span style="font-size: smaller;">For new features, bug reports or any other comments, mail to <a href="mailto:ccomb@prodebian.org">ccomb</a></span></div></body></html>';
}
//--------------------------
function my_gotopage($page) {
	header("Location: http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).$page);
	exit();
}
//--------------------------
function my_connectdatabase() {
	return pg_connect("host=localhost dbname=prodebian user=ccomb password=prodebian");
}
//--------------------------
function my_array_php2psql($array) {
	//PHP array -> pgsql array
	return '{"'.implode(',',$array).'"}';
}
//--------------------------
function my_array_psql2php($string) {
	//pgsql array -> PHP array
	if($string=="{}") return array(); 
	return(explode(",", trim($string,"}{")));
}
function my_string_php2psql($string) {
	//PHP string -> pgsql safe string in array
	return '{'.addslashes($string).'}';
}
function my_string_psql2php($string) {
	//pgsql string in array -> PHP string
	$string=stripslashes($string);
	$stringlen=strlen($string);
	if($string=="{}") return "";print($stringlen);
	if(substr($string,1,1)!='"' OR substr($string,-2,1)!='"') {
		return(substr($string,1,$stringlen-2));
	}
	return(substr($string,2,$stringlen-4));
}
//--------------------------
// REMOVE HTML AND PHP TAGS, LIMIT THE LENGTH OF THE VARIABLES, AND REMOVE DANGEROUS CHARS.
function my_purge_data() {
	$from=array("'", "\"");
	$to=array("'", "\"");
	foreach($_POST as $key => $value) {
		// this purges the value but not the key!
		if($key=="desc" OR $key=="runscript") $_POST[$key]=substr(str_replace($from, $to, strip_tags($value,'<a><b><i><u>')),0,900);
		else $_POST[$key]=substr(str_replace($from, $to, strip_tags($value)),0,64);
	}
	foreach($_GET as $key => $value) {
		// this purges the value but not the key!
		$_GET[$key]=substr(str_replace($from, $to, strip_tags($value)),0,64);
	}
}
//--------------------------
// authenticate and retry the url after authentication
function my_authenticate($id_owner) {
	if($id_owner=='') return 1;
	global $database;
	$res = pg_query($database, "SELECT username,password FROM owners WHERE id_owner='".$id_owner."';") or die();
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
	Password for user "'.$owners['username'].'": <input type="password" name="password" size="16" maxlength="16" />
	<input type="hidden" name="username" value="'.$owners['username'].'" />
	';
	foreach($_POST as $key => $value) print '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
	print '
	<button name="authenticate" type="submit">retry</button>
	</form>
	';
	my_endpage();
	exit();
}
//--------------------------
function my_debianversion($version) {
	    if($version==2.2) return "2.2 Potato (Stable)";
	elseif($version==3.0) return "3.0 Woody (Stable)";
	elseif($version==3.1) return "3.1 Sarge (Testing)";
	elseif($version==99) return "Sid (unstable)";
	else die("invalid Debian version number");
}
//--------------------------
function my_getactiontype($actiontype) {
	    if($actiontype==1) return "install packages";
	elseif($actiontype==2) return "Modify a config file";
//	elseif($actiontype==3) return "Run a group of actions";
	elseif($actiontype==4) return "Run a small script";
	elseif($actiontype==5) return "Create and edit a file";
	elseif($actiontype==6) return "Add an external file";
	else die("invalid action type number");
}
//--------------------------
function my_getactionurl($actiontype) {
	    if($actiontype==1) return "packagelist.php";
	elseif($actiontype==2) return "modiffile.php";
//	elseif($actiontype==3) return "rungroup.php";
	elseif($actiontype==4) return "runscript.php";
	elseif($actiontype==5) return "createfile.php";
	elseif($actiontype==6) return "addfile.php";
	else die("invalid action type number");
}
//--------------------------


?>
