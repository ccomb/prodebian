<?php
session_start();

include 'my_functions.php';
my_purge_data();

//my_debug();

//check arguments
if(!isset($_GET['id_action'])) my_gotopage("findprodebian.php");

$database = my_connectdatabase();
if(!isset($_SESSION['id_prodebian'])) my_gotopage("findprodebian.php");

// check that the action exists in the prodebian (for my_authenticate)
$res = pg_query($database, "SELECT id_owner,actionlist FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
$prodebians = pg_fetch_array($res);
$found = array_search($_GET['id_action'], my_string2array($prodebians['actionlist']));
if(is_bool($found) AND $found==FALSE) my_gotopage("error.php?why=badaction");
//-------------------
// GET THE PACKAGE LIST
$res = pg_query($database, "SELECT title,actiontype,actionvalues FROM actions WHERE id_action='".$_GET['id_action']."';") or die();
$actions = pg_fetch_array($res);
if($actions['actiontype']!=1) my_gotopage("error.php");
$packlist = my_string2array($actions['actionvalues']);
//-------------------
// ADD PACKAGE IF ASKED
if(isset($_POST['addpackage']) AND $_POST['addpackage']!="") {
	// try to find the package
	$res = pg_query($database, "SELECT id_pack FROM packages WHERE pack_name='".$_POST['addpackage']."';") or die();
	$packages = pg_fetch_array($res);
	// package doesn't exist -> add to the package list
	if($packages==0) {
		$res = pg_query($database, "INSERT INTO packages (pack_name) VALUES ('".$_POST['addpackage']."');") or die();
		$last_oid = pg_last_oid($res);
		$res = pg_query($database, "SELECT id_pack FROM packages WHERE oid=".$last_oid.";") or die();
		$packages = pg_fetch_array($res);
	}
	$found=array_search($packages['id_pack'],$packlist);
	if(is_bool($found) AND $found==FALSE) array_push($packlist, $packages['id_pack']);
	my_authenticate($prodebians['id_owner']);
	pg_query($database, "UPDATE actions SET actionvalues='".my_array2string($packlist)."' WHERE id_action='".$_GET['id_action']."';") or die();
	my_gotopage("packagelist.php?id_action=".$_GET['id_action']);
}
//-------------------
// REMOVE PACKAGE
if(isset($_POST['delete'])) {
	foreach($_POST as $key => $value) {
		if(substr($key,0,4)=="pack") {
			$delkey=array_search($value, $packlist);
			unset($packlist[$delkey]);
		}
	}
	my_authenticate($prodebians['id_owner']);
	pg_query($database, "UPDATE actions SET actionvalues='".my_array2string($packlist)."' WHERE id_action='".$_GET['id_action']."';") or die();
	my_gotopage("packagelist.php?id_action=".$_GET['id_action']);
}
//-------------------
// SAVE TITLE
if(isset($_POST['title'])) {
	my_authenticate($prodebians['id_owner']);
	pg_query($database, "UPDATE actions SET title='".$_POST['title']."' WHERE id_action='".$_GET['id_action']."';") or die();
	my_gotopage("packagelist.php?id_action=".$_GET['id_action']);
}
//---------------------
// DISPLAY PACKAGE LIST
my_beginpage();
my_printmenu();
//---------------------
// PROMPT TO ADD A NEW PACKAGE

print '<b>Title: '.$actions['title'].'</b><br />
<form action="packagelist.php?id_action='.$_GET['id_action'].'" method="POST">
<button type="submit">save</button>
<input type="text" name="title" value="'.$actions['title'].'" size="64" maxlength="64" />(short descriptive title of this action)
</form>

<hr align="left" size="1" width="100%" />

<b>Package list of this action :</b><br />
(Don\'t add too many packages in the same action. Create several actions with coherent groups of packages.)<br />
<form action="packagelist.php?id_action='.$_GET['id_action'].'" method="POST">
<button type="submit">add</button>
<input type="text" name="addpackage" size="32" maxlength="64" />(name of the package to add)
</form>
';

// SHOW THE LIST

if(count($packlist)==0) {
	print "
(There is no packages yet for this action).<br />

";
} else {
	print '<form action="packagelist.php?id_action='.$_GET['id_action'].'" method="POST">';
	$i=0;
	foreach($packlist as $id_package) {
		$res = pg_query($database, "SELECT pack_name, id_pack FROM packages WHERE id_pack='".$id_package."';") or die();
		$packages = pg_fetch_array($res);
		print '<input type="checkbox" name="pack'.$i++.'" value="'.$packages['id_pack'].'" />'.$packages['pack_name'].'<br />';
	}
	print '<button name="delete" type="submit">remove selected packages</button></form>';
}


//-------------------
my_endpage();
?>
