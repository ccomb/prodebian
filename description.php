<?php
include 'my_functions.php';
my_purge_data();
session_start();
$database = my_connectdatabase();

//-------------------
// CHECK ARGUMENTS
if(!isset($_SESSION['id_prodebian'])) my_gotopage("findprodebian.php");

// UPDATE TITLE AND DESCRIPTION
if(isset($_POST['title']) AND isset($_POST['desc'])) { // get the description ID
	$res = pg_query($database, "SELECT id_owner FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
	$prodebian = pg_fetch_array($res);
	my_authenticate($prodebian['id_owner']);
	$res = pg_query($database, "UPDATE prodebians SET description='".$_POST['desc']."', title='".$_POST['title']."' WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
	my_gotopage("prodebian.php?id=".$_SESSION['id_prodebian']);
}

//-------------------
// GET PRODEBIAN DATA
$res = pg_query($database, "SELECT title,description FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
$prodebians = pg_fetch_array($res);

//-------------------
// PROMPT FOR TITLE AND DESCRIPTION
my_beginpage();
my_printmenu();

print '<b>Prodebian #'.$_SESSION['id_prodebian'].'</b>
<form action="description.php" method="POST">
Short descriptive title :
<input type="text" name="title" value="'.$prodebians['title'].'"size="32" maxlength="32" /><br /><br />
Detailed description: (Limited to 900 chars. Allowed html tags = &lt;a&gt;&lt;b&gt;&lt;i&gt;&lt;u&gt;)<br /><textarea name="desc" rows="15" cols="60">'.$prodebians['description'].'</textarea><br />
<button name="save" type="submit">save</button>
</form>';



//-------------------
my_endpage();
?>
