<?php
session_start();
include 'html.php';
purge_data();

//===============
// CHECK ARGUMENTS
if(!isset($_SESSION['id_prodebian'])) goto_page("findprodebian.php");
if((int)$_SESSION['id_prodebian']==0) goto_page("findprodebian.php");

//===============
// CONFIRM THE DELETION
if(!isset($_POST['confirm'])) {
	beginpage();
	print	'Voulez vous réellement effacer cette Prodebian #'.$_SESSION['id_prodebian'].' ?
	<form action="deleteprodebian.php" method="POST">
		<button name="confirm" value="yes" type="submit">yes</button>
		<button name="confirm" value="no" type="submit">no</button>
	</form>';
	endpage();
	exit();
}

//===============
// DON'T DELETE THE PRODEBIAN
if($_POST['confirm']=="no") goto_page("prodebian.php?id=".$_SESSION['id_prodebian']);

//===============
// DELETE THE PRODEBIAN
if($_POST['confirm']=="yes") {
	$database = connect_database();
	$res = pg_query($database, "DELETE FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';");
	if(!$res) goto_page("error.php?why=deleteerror");
	$id_prodebian=$_SESSION['id_prodebian'];
	unset($_SESSION['id_prodebian']);
	beginpage();
	print_menu();
	print 'La prodebian #'.$id_prodebian.' a bien été effacée.';
	endpage();
	exit();
}


?>
	