<?php
session_start();

include 'my_functions.php';
my_purge_data();

//my_debug();

//check arguments
if(!isset($_GET['id_action'])) my_gotopage("findprodebian.php");

$database = my_connectdatabase();
if(!isset($_SESSION['id_prodebian'])) my_gotopage("findprodebian.php");

//-------------------
// GET THE PACKAGE LIST
$res = pg_query($database, "SELECT actiontype,actionvalues FROM actions WHERE id_action='".$_GET['id_action']."';") or die();
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
	my_authenticate(
	pg_query($database, "UPDATE actions SET actionvalues='".my_array2string($packlist)."' WHERE id_action='".$_GET['id_action']."';") or die();
}

// REMOVE PACKAGE
if(isset($_POST['delete'])) {
	$length=count($packlist);
	foreach($_POST as $key => $value) {
		if(substr($key,0,4)=="pack") {
			$delkey=array_search($value, $packlist);
			unset($packlist[$delkey]);
		}
	}
	pg_query($database, "UPDATE actions SET actionvalues='".my_array2string($packlist)."' WHERE id_action='".$_GET['id_action']."';") or die();
}

//---------------------
// DISPLAY PACKAGE LIST
my_beginpage();
my_printmenu();
//---------------------
// PROMPT TO ADD A NEW PACKAGE

print '
Name of package to add : <form action="packagelist.php?id_action='.$_GET['id_action'].'" method="POST">
<input type="text" name="addpackage" size="32" maxlength="32" />
<button name="create" type="submit">add</button></form>
Don\'t add too many packages in the same action. Create several actions with coherent groups of packages.<br /><br />actions
';

// SHOW THE LIST
print '<b>Package list of this action :</b><br />';
if(count($packlist)==0) {
	print "
(There is no package yet for this action).<br />

";
} else {
	print '<form action="packagelist.php?id_action='.$_GET['id_action'].'" method="POST">
	<button name="delete" type="submit">remove</button><br />';
	$i=0;
	foreach($packlist as $id_package) {
		$res = pg_query($database, "SELECT pack_name, id_pack FROM packages WHERE id_pack='".$id_package."';") or die();
		$packages = pg_fetch_array($res);
		print '<input type="checkbox" name="pack'.$i.'" value="'.$packages['id_pack'].'" />'.$packages['pack_name'].'<br />';
		$i++;
	}
	print '</form>';
}

print '<hr align="left" size="2" width="50" />';


//-------------------
my_endpage();
?>
