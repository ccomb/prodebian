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
// GET DATA
if(isset($_POST['dlscript']) OR isset($_POST['dlguide'])) {
	$database = my_connectdatabase();
	$res = pg_query($database, "SELECT * FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
	$prodebian = pg_fetch_array($res);
	if($prodebian==0) my_gotopage("error.php?why=invalidprodebian");
	$actionlist=my_array_psql2php($prodebian['actionlist']);
}
//-------------------
// DOWNLOAD THE INSTALL SCRIPT
if(isset($_POST['dlscript'])) {
	//header("Content-type: application/sh");
	header("Content-Disposition: attachment; filename=prodebian".$_SESSION['id_prodebian']."_install_script.sh");


	foreach($actionlist as $action) {
		$res = pg_query($database, "SELECT title,actiontype,actionvalues FROM actions WHERE id_action='".$action."';") or die();
		$actions = pg_fetch_array($res);
		print "\n\n#".$actions['title']."\n########################";
		// INSTALL PACKAGE
		if($actions['actiontype']==1) {
			print "\napt-get install ";
			//get the package list
			foreach(my_array_psql2php($actions['actionvalues']) as $id_package) {
				$res = pg_query($database, "SELECT pack_name FROM packages WHERE id_pack=".$id_package.";") or die();
				$packages = pg_fetch_array($res);
				print $packages['pack_name']." ";
			}
		}
		if($actions['actiontype']==4) {
			$script = my_array_psql2php($actions['actionvalues']);
			print "\n".substr($script['0'],1,strlen($script['0'])-2);
		}
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
- install the base
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