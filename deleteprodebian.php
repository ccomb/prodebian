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
	print	'Are you sure you want to definitely erase this Prodebian #'.$_SESSION['id_prodebian'].' as well as its package list?
	<form action="deleteprodebian.php" method="POST">
		<button name="confirm" value="yes" type="submit">delete</button>
		<a href=prodebian.php?id='.$_SESSION['id_prodebian'].'>cancel</a>
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
	$actionlist=my_array_psql2php($prodebian['actionlist']);
	my_authenticate($prodebian['id_owner']);
	if(count($actionlist)>0) {
		$res = pg_query($database, "DELETE FROM actions WHERE id_action IN (".implode(',',$actionlist).");") or die();
	}
	$res = pg_query($database, "DELETE FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
	$id_prodebian=$_SESSION['id_prodebian'];
	unset($_SESSION['id_prodebian']);
	my_beginpage();
	my_printmenu();
	print 'The prodebian #'.$id_prodebian.' has been successfully deleted.';
	my_endpage();
	exit();
}


?>
	