<?php
session_start();

include 'html.php';
beginpage();
print_menu();
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
if(isset($_POST['addpackage'])) {
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
	array_push($packlist, $packages['id_pack']);
	$res = pg_query($database, "UPDATE package_lists SET packlist='".array2string($packlist)."';");
}

//---------------------
// DISPLAY PACKAGE LIST

print '<b>Liste des paquets de cette Prodebian :</b><br />';
if(count($packlist)==0) {
	print "
You haven't added any package yet.<br />
This means that your Prodebian has no more functionalities than the Debian base system.<br />
";
} else {

print '<form action="packagelist.php" method="POST">';
foreach($packlist as $id_package) {
	$res = pg_query($database, "SELECT pack_name FROM packages WHERE id_pack=".$id_package.";");
	$packages = pg_fetch_array($res);
	print '<input type="checkbox" name="'.$packages['pack_name'].'" />'.$packages['pack_name'].'<br />';
}
print '</form><br />';
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
