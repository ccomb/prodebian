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
$prodebian = pg_fetch_result($res,0,0);
$res = pg_query($database, "SELECT packlist FROM package_lists WHERE id_packlist=".$prodebian['id_packlist'].";");
$packlist = pg_fetch_result($res,0,0);
if($packlist=='{}') $packlist=array(NULL);
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
	print_r( $packages);
	print_r ($packlist);
	$packlist = array_push($packlist, string($packages['id_pack']));
	$res = pg_query($database, "INSERT INTO package_lists (packlist) VALUES ('".array2string($packlist)."');");
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
	$pack_name = pg_fetch_result($res,0,0);
	print '<input type="checkbox" name="$pack_name" />';
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
