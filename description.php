<?php
include 'html.php';
purge_data();
session_start();
$database = connect_database();

//-------------------
// CHECK ARGUMENTS
if(!isset($_SESSION['id_prodebian'])) goto_page("findprodebian.php");

// UPDATE TITLE AND DESCRIPTION
if(isset($_POST['name']) && isset($_POST['desc'])) { // get the description ID
	$res = pg_query($database, "SELECT id_desc FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';");
	$prodebian = pg_fetch_array($res);
	if($prodebian['id_desc']=='') { // if no ID, create a description
		$res = pg_query($database, "INSERT INTO descriptions (description) VALUES ('".$_POST['desc']."');");
		$last_oid = pg_last_oid($res);
		$res = pg_query($database, "SELECT id_desc FROM descriptions WHERE oid=".$last_oid.";");
		$descriptions = pg_fetch_array($res);
		$res = pg_query($database, "UPDATE prodebians SET id_desc='".$descriptions['id_desc']."' WHERE id_prodebian='".$_SESSION['id_prodebian']."';");
	} else { // otherwise update the existing description
		$res = pg_query($database, "UPDATE prodebians SET name='".$_POST['name']."',id_desc='".$prodebian['id_desc']."' WHERE id_prodebian='".$_SESSION['id_prodebian']."';");
		$res = pg_query($database, "UPDATE descriptions SET description='".$_POST['desc']."' WHERE id_desc='".$prodebian['id_desc']."';");
	}
	goto_page("prodebian.php?id=".$_SESSION['id_prodebian']);
}

//-------------------
// GET PRODEBIAN DATA
$res = pg_query($database, "SELECT name,id_desc FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';");
$prodebian = pg_fetch_array($res);
if($prodebian['id_desc']!=NULL) {
	$res = pg_query($database, "SELECT description FROM descriptions WHERE id_desc='".$prodebian['id_desc']."';");
	$descriptions = pg_fetch_array($res);
	if($descriptions==0) $desc="<none>";
	else $desc=$descriptions['description'];
} else $desc="<none>";

//-------------------
// PROMPT FOR TITLE AND DESCRIPTION
beginpage();
print_menu();

print '<b>Prodebian #'.$_SESSION['id_prodebian'].'</b>
<form action="description.php" method="POST">
Short descriptive title :
<input type="text" name="name" value="'.$prodebian['name'].'"size="32" maxlength="32" /><br /><br />
Detailed description: (Limited to 900 chars)<br /><textarea name="desc" rows="15" cols="60">'.$desc.'</textarea><br />
<button name="save" type="submit">save</button>
</form>';



//-------------------
endpage();
?>