<?php
//session_start();
include 'html.php';
purge_data();
//===============
// CREATE THE PRODEBIAN
if (isset($_POST['debianversion']) && isset($_POST['prodebname']) && $_POST['debianversion']!="" && $_POST['prodebname']!="") {
	$database = connect_database();
	//get the debian version id
	$res = pg_query($database, "SELECT id_debversion FROM debversions WHERE version_nb='$_POST[debianversion]';");
	$id_debversion = pg_fetch_result($res, 0, 0);
	//create an empty package list and get its id
	$res_insert = pg_query("INSERT INTO package_lists (packlist) VALUES ('{}');");
	if(!$res_insert) goto_page("error.php?why=inserterror");
	$last_oid = pg_last_oid($res_insert);
	$res = pg_query($database, "SELECT id_packlist FROM package_lists WHERE oid=".$last_oid.";");
	$id_packlist = pg_fetch_result($res, 0, 0);
	//create a new prodebian
	$res_insert = pg_query("INSERT INTO prodebians (id_debversion, name, id_packlist) VALUES ('".$id_debversion['0']."', '".$_POST['prodebname']."', ".$id_packlist.");");
	if(!$res_insert) goto_page("error.php?why=inserterror");
	$last_oid = pg_last_oid($res_insert);
	$res = pg_query($database, "SELECT id_prodebian FROM prodebians WHERE oid=".$last_oid.";");
	$id = pg_fetch_result($res, 0, 0);
	beginpage();
	print_menu();
	print '
	Votre Prodebian a été créée sous le numéro de référence #'.$id.'.<br />
	Notez ce numéro car il vous permettra de retrouver rapidement votre Prodebian.<br />
	<br />Pour pouvez maintenant accéder à la page de configuration de votre Prodebian en cliquant sur le lien ci-dessous :<br />
	<a href="prodebian.php?id='.$id.'" size="4">Prodebian #'.$id.'</a>
	';
	//goto_page("prodebian.php?id=$id");
}	

//===============
// PROMPT THE USER
else {
	beginpage();
	print_menu();

	$database = connect_database();
	$res_debversions = pg_query($database, "SELECT * FROM debversions ORDER BY version_nb;");

	print '<br />
	<form action="createprodebian.php" method="POST">
		based on Debian version :	<select name="debianversion" size="1">';
		for($row=0; $row < pg_num_rows($res_debversions); $row++) {
			$debversions = pg_fetch_array($res_debversions, $row);
			if ($debversions['version_nb']=='3.1') $selected='selected';
			else $selected="";
			print "<option value=\"".$debversions['version_nb']."\" $selected>".$debversions['version_name']."</option>";
		}
	print '</select><br />';
	print '
		Choose a short descriptive title for your Prodebian.<br />
		Prodebian title: <input type="text" name="prodebname" size="32" maxlength="32" /><br />
		Enter the creator of this prodebian<br />
		Prodebian creator: <input type="text" name="prodebname" value="<enter your username>" size="32" maxlength="32" /><br />
		<button name="create" type="submit">create</button>
	</form>';
	
	endpage();
}
?>
