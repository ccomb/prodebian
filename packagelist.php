<?php
session_start();

include 'html.php';
purge_data();

$database = connect_database();
if(!isset($_SESSION['id_prodebian'])) goto_page("findprodebian.php");

//-------------------
// GET THE PACKAGE ID LIST
$res = pg_query($database, "SELECT id_packlist FROM prodebians WHERE id_prodebian=".$_SESSION['id_prodebian'].";");
$prodebian = pg_fetch_array($res);
$res = pg_query($database, "SELECT packlist FROM package_lists WHERE id_packlist=".$prodebian['id_packlist'].";");
$package_lists = pg_fetch_array($res);
$packlist = $package_lists['packlist'];
if($packlist=='{}') $packlist=array();
else $packlist = string2array($packlist);

// ADD PACKAGE IF ASKED
if(isset($_POST['addpackage']) && $_POST['addpackage']!="") {
	// try to find the package
	$res = pg_query($database, "SELECT id_pack FROM packages WHERE pack_name='".$_POST['addpackage']."';");
	$packages = pg_fetch_array($res);
	// package doesn't exist -> add to the package list
	if($packages==0) {
		$res = pg_query($database, "INSERT INTO packages (pack_name) VALUES ('".$_POST['addpackage']."');");
		$last_oid = pg_last_oid($res);
		$res = pg_query($database, "SELECT id_pack FROM packages WHERE oid=".$last_oid.";");
		$packages = pg_fetch_array($res);
	}
	$found=array_search($packages['id_pack'],$packlist);
	if(!$found)	array_push($packlist, $packages['id_pack']);
	pg_query($database, "UPDATE package_lists SET packlist='".array2string($packlist)."'WHERE id_packlist='".$prodebian['id_packlist']."';");
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
	pg_query($database, "UPDATE package_lists SET packlist='".array2string($packlist)."' WHERE id_packlist='".$prodebian['id_packlist']."';");
}

//---------------------
// DISPLAY PACKAGE LIST
beginpage();
print_menu();
print '<b>Liste des paquets de cette Prodebian :</b><br />';
if(count($packlist)==0) {
	print "
You haven't added any package yet.<br />
This means that your Prodebian has no more functionalities than the Debian base system.<br />
";
} else {

print '<form action="packagelist.php" method="POST">';
$i=0;
foreach($packlist as $id_package) {
	$res = pg_query($database, "SELECT pack_name, id_pack FROM packages WHERE id_pack=".$id_package.";");
	$packages = pg_fetch_array($res);
	print '<input type="checkbox" name="pack'.$i.'" value="'.$packages['id_pack'].'" />'.$packages['pack_name'].'<br />';
	$i++;
}
print '<button name="delete" type="submit">effacer</button></form><br />';
}

//---------------------
// PROMPT TO ADD A NEW PACKAGE
print '<hr align="left" size="2" width="50" />';
print '
Nom du paquet à ajouter : <form action="packagelist.php" method="POST">
<input type="text" name="addpackage" size="32" maxlength="32" />
<button name="create" type="submit">ajouter</button></form><br />
';



//-------------------
endpage();
?>
