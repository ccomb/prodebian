<?php
session_start();
include 'my_functions.php';
my_purge_data();

//===============
// CHECK ARGUMENTS
if(!isset($_GET['id']) AND !isset($_POST['dlscript']) AND !isset($_POST['dlguide'])) my_gotopage("findprodebian.php");
if(isset($_GET['id']) AND (int)$_GET['id']==0) my_gotopage("findprodebian.php");
if(isset($_GET['id'])) $_SESSION['id_prodebian'] = $_GET['id'];

//-------------------
// GET PRODEBIAN DATA
$database = my_connectdatabase();
$res = pg_query($database, "SELECT * FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
$prodebian = pg_fetch_array($res);
if($prodebian==0) my_gotopage("error.php?why=invalidprodebian");
$id_packlist = $prodebian['id_packlist'];
if($id_packlist=='{}') $pack_number=0;
else {
	$res = pg_query($database, "SELECT * FROM package_lists WHERE id_packlist=".$id_packlist.";") or die();
	$package_lists = pg_fetch_array($res);
}

//-------------------
// DOWNLOAD THE INSTALL SCRIPT
if(isset($_POST['dlscript'])) {
	//header("Content-type: application/sh");
	header("Content-Disposition: attachment; filename=prodebian".$_SESSION['id_prodebian']."_install_script.sh");
	print '#!/bin/bash
apt-get install ';
	foreach(my_string2array($package_lists['packlist']) as $id_package) {
		$res = pg_query($database, "SELECT pack_name FROM packages WHERE id_pack=".$id_package.";") or die();
		$packages = pg_fetch_array($res);
		print $packages['pack_name']." ";
	}
	exit();
}
//-------------------
// DOWNLOAD THE INSTALL GUIDE
if(isset($_POST['dlguide'])) {
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
// GENERATE THE PRODEBIAN
my_beginpage();
my_printmenu();

print '
1) Download the Debian netinst ISO image here : <a href=http://debian.org/>netinst.iso</a><br />
<form action="generateprodebian.php" method="POST">
2) Download your Prodebian installation guide :<button name="dlguide" type="submit">download guide</button>
</form>
<form action="generateprodebian.php" method="POST">
3) Download your Prodebian installation script :<button name="dlscript" type="submit">download script</button>
</form>
';
my_endpage();
exit();


?>