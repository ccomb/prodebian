<?php
session_start();

include 'html.php';
beginpage();
print_menu();
$database = connect_database();
if(!isset($_SESSION['id_prodebian'])) goto_page("findprodebian.php");

//-------------------
$res = pg_query($database, "SELECT id_packlist FROM prodebians WHERE id_prodebian=".$_SESSION['id_prodebian'].";");
$prodebian = pg_fetch_result($res,0,0);
$res = pg_query($database, "SELECT packlist FROM package_lists WHERE id_packlist=".$prodebian['id_packlist'].";");
$packlist = pg_fetch_result($res,0,0);

// ADD PACKAGE IF ASKED
if(isset($_POST['addpackage'])) {
	$res = pg_query($database, "SELECT id_pack FROM packages WHERE pack_name=".$_POST['addpackage'].";");
	if($res==NULL) {
		$res = pg_query($database, "INSERT INTO packages (pack_name) VALUES ('".$_POST['addpackage']."');");
		$last_oid = pg_last_oid($res);
		$res = pg_query($database, "SELECT id_pack FROM packages WHERE oid=".$last_oid.";");
		$id = pg_fetch_result($res,0,0);
	}

}	

//---------------------
// DISPLAY PACKAGE LIST

print '<b>Liste des paquets de cette Prodebian :</b>';
if($packlist=='{}') {
	print "
You haven't added any package yet.<br />
This means that your Prodebian has no more functionalities than the Debian base system.<br />
";
} else {
$packlist = string2array($packlist);

print '<form action="packagelist.php" method="POST">';
	print '<input type="checkbox" name="pack1" />';
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
