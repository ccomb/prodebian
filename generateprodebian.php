<?php
session_start();
include 'html.php';
purge_data();

//===============
// CHECK ARGUMENTS
if(!isset($_GET['id']) && !isset($_POST['dlscript']) && !isset($_POST['dlguide'])) goto_page("findprodebian.php");
if(isset($_GET['id']) && (int)$_GET['id']==0) goto_page("findprodebian.php");
if(isset($_GET['id'])) $_SESSION['id_prodebian'] = $_GET['id'];

//-------------------
// GET PRODEBIAN DATA
$database = connect_database();
$res = pg_query($database, "SELECT * FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';");
$prodebian = pg_fetch_array($res);
if($prodebian==0) goto_page("error.php?why=invalidprodebian");
$id_packlist = $prodebian['id_packlist'];
if($id_packlist=='{}') $pack_number=0;
else {
	$res = pg_query($database, "SELECT * FROM package_lists WHERE id_packlist=".$id_packlist.";");
	$package_lists = pg_fetch_array($res);
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
beginpage();
print_menu();
print '
1) Download the Debian netinst ISO image here : <a href=http://debian.org/>netinst.iso</a><br />
<form action="generateprodebian.php" method="POST">
2) Download your Prodebian installation guide :<button name="dlguide" type="submit">download guide</button>
</form>
<form action="generateprodebian.php" method="POST">
3) Download your Prodebian installation script :<button name="dlscript" type="submit">download script</button>
</form>
';
endpage();
exit();


?>