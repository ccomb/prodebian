<?php
include 'html.php';
purge_data();
session_start();

//-------------------
// CHECK ARGUMENTS
if(isset($_GET['id'])) {
	$_GET['id']=(int)$_GET['id'];
	if($_GET['id']==0) goto_page("error.php?why=invalidprodebian");
	$_SESSION['id_prodebian'] = $_GET['id'];
}

if(!isset($_SESSION['id_prodebian'])) goto_page("findprodebian.php");

//-------------------
// GET PRODEBIAN DATA
$database = connect_database();
$res = pg_query($database, "SELECT * FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';");
$prodebian = pg_fetch_array($res);
if($prodebian==0) goto_page("error.php?why=invalidprodebian");
$res = pg_query($database, "SELECT * FROM debversions WHERE id_debversion=".$prodebian['id_debversion'].";");
$debversion = pg_fetch_array($res);
$id_packlist = $prodebian['id_packlist'];
if($id_packlist=='{}') $pack_number=0;
else {
	$res = pg_query($database, "SELECT * FROM package_lists WHERE id_packlist=".$id_packlist.";");
	$package_lists = pg_fetch_array($res);
	$pack_number = count(string2array($package_lists['packlist']));
}


//-------------------
// DOWNLOAD THE INSTALL SCRIPT
if(isset($_POST['dlscript'])) {
	//header("Content-type: application/sh");
	header("Content-Disposition: attachment; filename=prodebian".$_SESSION['id_prodebian']."_install_script.sh");
	print '#!/bin/bash
apt-get install ';
	foreach(string2array($package_lists['packlist']) as $id_package) {
		$res = pg_query($database, "SELECT pack_name FROM packages WHERE id_pack=".$id_package.";");
		$packages = pg_fetch_array($res);
		print $packages['pack_name']." ";
	}
	exit();
}
//-------------------
// DOWNLOAD THE INSTALL GUIDE
if(isset($_POST['dldoc'])) {
	//header("Content-type: text/txt");
	header("Content-Disposition: attachment; filename=prodebian".$_SESSION['id_prodebian']."_install_guide.txt");
	print 'Prodebian blah blah blah
- boot from the Debian netinst CDROM
- install...
- login as root
- run script.sh
- enjoy the prodebian
';
exit();
}
//-------------------
// DISPLAY THE PRODEBIAN SUMMARY
beginpage();
print_menu();

print "<b>".$prodebian['name']."</b><br />";
print "Prodebian n°".$prodebian['id_prodebian']."<br />";
print "Version de Debian : ".$debversion['version_name']."<br />";


print "<br />liste des paquets : <a href=packagelist.php>".$pack_number." paquet(s)</a><br />";
print "liste des actions : "."TBD<br />";
print '
<form action="generateprodebian.php" method="GET">
	<button name="id" value="'.$prodebian['id_prodebian'].'" type="submit">générer</button>
</form>
<form action="deleteprodebian.php" method="POST">
	<button name="delete" type="submit">delete</button>
</form>
';

//-------------------
endpage();
?>
