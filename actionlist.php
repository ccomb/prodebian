<?php
session_start();

include 'my_functions.php';
my_purge_data();

$database = my_connectdatabase();
if(!isset($_SESSION['id_prodebian'])) my_gotopage("findprodebian.php");

//-------------------
// GET THE ACTION LIST
$res = pg_query($database, "SELECT id_owner,actionlist FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
$prodebians = pg_fetch_array($res);
$actionlist = my_string2array($prodebians['actionlist']);
// create the action and redirect to the action edit page
if(isset($_POST['addaction'])) {
	 if($_POST['actiontype']=="addpackage") { $actiontype=1; $creationpage=my_getactionurl(1); }
elseif($_POST['actiontype']=="modiffile" ) { $actiontype=2; $creationpage=my_getactionurl(2); }
elseif($_POST['actiontype']=="rungroup"  ) { $actiontype=3; $creationpage=my_getactionurl(3); }
elseif($_POST['actiontype']=="runcommand") { $actiontype=4; $creationpage=my_getactionurl(4); }
elseif($_POST['actiontype']=="runscript" ) { $actiontype=5; $creationpage=my_getactionurl(5); }
elseif($_POST['actiontype']=="createfile") { $actiontype=6; $creationpage=my_getactionurl(6); }
elseif($_POST['actiontype']=="addfile"   ) { $actiontype=7; $creationpage=my_getactionurl(7); }
	$res = pg_query($database, "INSERT INTO actions (actiontype,actionvalues) VALUES ('".$actiontype."','{}');") or die();
	$last_oid = pg_last_oid($res);
	$res = pg_query($database, "SELECT id_action FROM actions WHERE oid=".$last_oid.";") or die();
	$actions = pg_fetch_array($res);
	array_push($actionlist, $actions['id_action']);
	my_authenticate($prodebians['id_owner']);
	pg_query($database, "UPDATE prodebians SET actionlist='".my_array2string($actionlist)."' WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
	my_gotopage($creationpage."?id_action=".$actions['id_action']);
}
		
// ADD PACKAGE IF ASKED
/*
if(isset($_POST['addpackage']) AND $_POST['addpackage']!="") {
	// try to find the package
	$res = pg_query($database, "SELECT id_pack FROM packages WHERE pack_name='".$_POST['addpackage']."';") or die();
	$packages = pg_fetch_array($res);
	// package doesn't exist -> add to the package list
	if($packages==0) {$actionlist
		$res = pg_query($database, "INSERT INTO packages (pack_name) VALUES ('".$_POST['addpackage']."');") or die();
		$last_oid = pg_last_oid($res);
		$res = pg_query($database, "SELECT id_pack FROM packages WHERE oid=".$last_oid.";") or die();
		$packages = pg_fetch_array($res);
	}
	$found=array_search($packages['id_pack'],$packlist);
	if(is_bool($found) AND $found==FALSE) array_push($packlist, $packages['id_pack']);
	pg_query($database, "UPDATE package_lists SET packlist='".my_array2string($packlist)."'WHERE id_packlist='".$prodebian['id_packlist']."';") or die();
}
*/

// REMOVE ACTION
$dellist=array();
if(isset($_POST['delete'])) {
	foreach($_POST as $key => $value) {
		if(substr($key,0,6)=="action") {
			$delkey=array_search($value, $actionlist);
			array_push($dellist, $actionlist[$delkey]);
			unset($actionlist[$delkey]);
		}
	}
	if(count($dellist)>0) {
		my_authenticate($prodebians['id_owner']);
		pg_query($database, "DELETE FROM actions WHERE id_action IN (".implode(',',$dellist).");") or die();
		pg_query($database, "UPDATE prodebians SET actionlist='".my_array2string($actionlist)."' WHERE id_prodebian=".$_SESSION['id_prodebian'].";") or die();
		my_gotopage("actionlist.php");
	}
}

//---------------------
// DISPLAY ACTION LIST
my_beginpage();
my_printmenu();
//---------------------
// PROMPT TO ADD A NEW ACTION

print '
What action do you want to add ?
<form action="actionlist.php" method="POST">
<select name="actiontype">
	<option value="addpackage">'.my_getactiontype(1).'</option>
	<option value="modiffile">'.my_getactiontype(2).'</option>(line by line)
	<option value="rungroup">'.my_getactiontype(3).'</option>
	<option value="runcommand">'.my_getactiontype(4).'</option>
	<option value="runscript">'.my_getactiontype(5).'</option>
	<option value="createfile">'.my_getactiontype(6).'</option>
	<option value="addfile">'.my_getactiontype(7).'</option>
</select>
<button name="addaction" type="submit">add</button></form><br />
';

// SHOW THE LIST
print '<b>Action list of this Prodebian:</b><br />';
if(count($actionlist)==0) {
	print "
You haven't added any action yet.<br />
This means that your Prodebian has no more functionalities than the Debian base system.<br />
";
} else {
print '<form action="actionlist.php" method="POST">';
$i=0;
foreach($actionlist as $action) {
	$res = pg_query($database, "SELECT * FROM actions WHERE id_action='".$action."';") or die();
	$actions = pg_fetch_array($res);
	if($actions['title']=='') $actions['title']="(no title, please add one!)";
	print '<input type="checkbox" name="action'.$i++.'" value="'.$actions['id_action'].'" />';
	print ' ['.my_getactiontype($actions['actiontype']).'] ';
	print '<a href="'.my_getactionurl($actions['actiontype']).'?id_action='.$actions['id_action'].'">';
	print $actions['title'].'</a><br />';
}
print '<button name="delete" type="submit">remove selected actions</button></form>';
}


//-------------------
my_endpage();
?>
