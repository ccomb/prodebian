<?php
session_start();
$_SESSION['id_prodebian'] = $_GET['id'];
include 'html.php';
beginpage();
print_menu();
//-------------------

$database = connect_database();

$res = pg_query($database, "SELECT * FROM prodebians WHERE id_prodebian=".$_SESSION['id_prodebian'].";");
$prodebian = pg_fetch_array($res);
$res = pg_query($database, "SELECT * FROM debversions WHERE id_debversion=".$prodebian['id_debversion'].";");
$debversion = pg_fetch_array($res);
print "<b>".$prodebian['name']."</b><br />";
print "Prodebian n°".$prodebian['id_prodebian']."<br />";
print "Version de Debian : ".$debversion['version_name']."<br />";

$packlist = $prodebian['id_packlist'];
if($packlist=='{}') $pack_number=0;
else {
	$res = pg_query($database, "SELECT * FROM package_lists WHERE id_packlist=".$packlist.";");
	$package_lists = pg_fetch_array($res);
	$pack_number = count(string2array($package_lists['packlist']));
}
print "<br />liste des paquets : <a href=packagelist.php>".$pack_number." paquet(s)</a><br />";
print "liste des actions : "."TBD<br />";


//-------------------
endpage();
?>
