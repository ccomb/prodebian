<?php
session_start();
include 'my_functions.php';
my_purge_data();

//===============
// CHECK ARGUMENTS
if(!isset($_SESSION['id_prodebian'])) my_gotopage("findprodebian.php");
if((int)$_SESSION['id_prodebian']==0) my_gotopage("findprodebian.php");

//===============
// CONFIRM THE DELETION
if(!isset($_POST['confirm'])) {
	my_beginpage();
	print	'Voulez vous réellement effacer cette Prodebian #'.$_SESSION['id_prodebian'].' ainsi que sa liste de paquets ?
	<form action="deleteprodebian.php" method="POST">
		<button name="confirm" value="yes" type="submit">yes</button>
		<button name="confirm" value="no" type="submit">no</button>
	</form>';
	my_endpage();
	exit();
}

//===============
// DON'T DELETE THE PRODEBIAN
if($_POST['confirm']=="no") my_gotopage("prodebian.php?id=".$_SESSION['id_prodebian']);

//===============
// DELETE THE PRODEBIAN
if($_POST['confirm']=="yes") {
	$database = my_connectdatabase();
	$res = pg_query($database, "SELECT id_owner,actionlist FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
	$prodebian = pg_fetch_array($res);
	$actionlist=my_string2array($prodebian['actionlist']);
	my_authenticate($prodebian['id_owner']);
	if(count($actionlist)>0) {
		$res = pg_query($database, "DELETE FROM actions WHERE id_action IN (".implode(',',$actionlist).");") or die();
	}
	$res = pg_query($database, "DELETE FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
	$id_prodebian=$_SESSION['id_prodebian'];
	unset($_SESSION['id_prodebian']);
	my_beginpage();
	my_printmenu();
	print 'La prodebian #'.$id_prodebian.' a bien été effacée.';
	my_endpage();
	exit();
}


?>
	