<?php
include 'my_functions.php';
my_purge_data();
session_start();

function my_appropriate($id_owner, $id_prodebian) {
	if(!isset($_SESSION['username']) OR !isset($_SESSION['password'])) my_gotopage("error.php?why=autherror");
	global $database;
	$res = pg_query($database, "UPDATE prodebians SET id_owner='".$id_owner."' WHERE id_prodebian='".$id_prodebian."';");
}

function my_createuser($username, $password) {
	global $database;
	$res = pg_query($database, "INSERT INTO owners (username,password) VALUES ('".$username."', '".$password."');");
	if(!$res) my_gotopage("error.php?why=inserterror");
	$last_oid = pg_last_oid($res);
	$res = pg_query($database, "SELECT id_owner,username,password FROM owners WHERE oid=".$last_oid.";");
	$owners = pg_fetch_array($res);
	$_SESSION['username']=$owners['username'];
	$_SESSION['password']=$owners['password'];
	return $owners['id_owner'];
}

if(!isset($_SESSION['id_prodebian'])) my_gotopage("findprodebian.php");
$database = my_connectdatabase();

// if we get a user id, work on it.
if(isset($_GET['id']) AND (int)$_GET['id']!=0) {
	// get data
	$res = pg_query($database, "SELECT * FROM owners WHERE id_owner='".$_GET['id']."';");
	$owners = pg_fetch_array($res);
	if($owners['id_desc']=='') $owners['id_desc']=0;
	if($owners['email']=='') $owners['email']='none';
	$res = pg_query($database, "SELECT description FROM descriptions WHERE id_desc='".$owners['id_desc']."';");
	$descriptions = pg_fetch_array($res);
	if($descriptions['description']=='') $descriptions['description']='(no comment)';
	$resprodebians = pg_query($database, "SELECT id_prodebian,name FROM prodebians WHERE id_owner='".$owners['id_owner']."';");
	//$prodebians = pg_fetch_array($res);
	// UPDATE DATA
	if(isset($_POST['update'])) {
	//my_beginpage();
		my_authenticate($_GET['id']);
		if($owners['id_desc']=='') {
			$res = pg_query($database, "INSERT INTO descriptions (description) VALUES ('".$_POST['desc']."');");
			$last_oid = pg_last_oid($res);
			$res = pg_query($database, "SELECT id_desc FROM descriptions WHERE oid='".$last_oid."';");
			$descriptions = pg_fetch_array($res);
			$res = pg_query($database, "UPDATE owners SET id_desc='".$descriptions['id_desc']."' WHERE id_owner='".$_GET['id']."';");
		} else {
			$res = pg_query($database, "UPDATE descriptions SET description='".$_POST['desc']."' WHERE id_desc='".$owners['id_desc']."';");
		}
		$res = pg_query($database, "UPDATE owners SET email='".$_POST['email']."' WHERE id_owner='".$_GET['id']."';");
		if($_POST['password']!='') { 
			$res = pg_query($database, "UPDATE owners SET password='".$_POST['password']."' WHERE id_owner='".$_GET['id']."';");
			$_SESSION['password']=$_POST['password'];
		}
		my_gotopage("owner.php?id=".$_GET['id']);
	}
	if(!isset($_GET['modify'])) {
		// DEFAULT DETAILS PAGE
		my_beginpage();
		my_printmenu();
		print 'Prodebian owner: '.$owners['username'].'<br />';
		print 'password: <a href="owner.php?id='.$owners['id_owner'].'&modify=1">(modify)</a><br />';
		print 'e-mail: <a href="owner.php?id='.$owners['id_owner'].'&modify=1">(modify)</a><br /><br />';
		print 'owners\'s comments or details:<br />'.$descriptions['description'];
		my_endpage();
		exit();
	}
	if(isset($_GET['modify'])) {
		my_authenticate($owners['id_owner']);
		my_beginpage();
		my_printmenu();
		print '
			<form action="owner.php?id='.$owners['id_owner'].'" method="POST">
	your username: '.$owners['username'].'<br />
	your password: <input type="text" name="password" size="16" maxlength="16" />(leave blank to keep)<br />
	your e-mail: <input type="text" name="email" value="'.$owners['email'].'" size="32" maxlength="64" /><br />
	Your comments or details: (Limited to 900 chars)<br /><textarea name="desc" rows="15" cols="60">'.$descriptions['description'].'</textarea><br />
	<button name="update" type="submit">save</button>
	</form>
	';
		my_endpage();
		exit();
	}
}


//-------------------
// if we provided user/pass and the prodebian has no user,
// CREATE USER IF NECESSARY, THEN CREATE TOKEN, AND GIVE HIM THE PRODEBIAN
$res = pg_query($database, "SELECT id_owner,name FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';");
$prodebians = pg_fetch_array($res);
if(isset($_POST['username']) AND isset($_POST['password']) AND (int)$prodebians['id_owner']==0) {
	$res = pg_query($database, "SELECT username,id_owner,password FROM owners WHERE username='".$_POST['username']."';");
	$owners = pg_fetch_array($res);
	if($owners==0) { // create the user, and give him the prodebian
		$id_owner = my_createuser($_POST['username'], $_POST['password']);
		my_appropriate($id_owner, $_SESSION['id_prodebian']);
		my_beginpage();
		my_printmenu();
		print 'A Prodebian account has been created with username "'.$_POST['username'].'".<br />';
		print 'The <a href="prodebian.php?id='.$_SESSION['id_prodebian'].'">Prodebian #'.$_SESSION['id_prodebian'].'</a> now belongs to you<br />'.
		my_endpage();
	} else { // the user exists : check the password and give him the prodebian
		if($_POST['password']==$owners['password'] AND $_POST['username']==$owners['username']) {
			$_SESSION['username']=$_POST['username'];
			$_SESSION['password']=$_POST['password'];
			my_appropriate($owners['id_owner'], $_SESSION['id_prodebian']);
			my_beginpage();
			my_printmenu();
			print 'The <a href="prodebian.php?id='.$_SESSION['id_prodebian'].'">Prodebian #'.$_SESSION['id_prodebian'].'</a> now belongs to you<br />'.
			my_endpage();
		} else {
			my_beginpage();
			my_printmenu();
			print 'The user already exists but the password you entered is not correct.';
			my_endpage();
		}
	}
	exit();
}

my_beginpage();
my_printmenu();
print '
	<form action="owner.php" method="POST">
	Enter your nickname:<br />
	Prodebian creator: <input type="text" name="username" size="32" maxlength="32" /><br />
	Creator\'s password: <input type="text" name="password" size="32" maxlength="32" /><br />
	<button name="save" type="submit">save</button>
	</form>
';
my_endpage();



?>