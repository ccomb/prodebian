<?php
session_start();
include 'html.php';

//===============
// PROMPT THE USER
if (!isset($_POST[debianversion]) || !isset($_POST[prodebname])) {

	beginpage();
	print_menu();

	$database = pg_connect("host=localhost dbname=prodebian user=ccomb password=prodebian");
	$res_debversions = pg_query($database, "SELECT * FROM debversions ORDER BY version_nb;");

	print '<br />
	<form action="createprodebian.php" method="POST">
		version de Debian :	<select name="debianversion" size="1">';
		for($row=0; $row < pg_num_rows($res_debversions); $row++) {
			$debversions = pg_fetch_array($res_debversions, $row);
			if ($debversions[version_nb]=='3.1') $selected='selected';
			else $selected="";
			print "<option value=\"" . $debversions[version_nb] . "\" $selected>$debversions[version_name]</option>";
		}

	print '
		</select><br />
		Choisissez un nom pour votre prodebian.<br />
		Ce nom n\'a pas de garantie d\'unicité et pourra être modifié.<br />
		Nom de votre prodebian : <input type="text" name="prodebname" size="32" maxlength="32" /><br />
		<button name="create" type="submit">créer</button>
	</form>';

	endpage();
}
//===============
// CREATE THE PRODEBIAN
else {
	$database = pg_connect("host=localhost dbname=prodebian user=ccomb password=prodebian");
	$res = pg_query($database, "SELECT id_debversion FROM debversions WHERE version_nb = \"$_POST[debianversion]\";");
	$id = pg_fetch_result($res, 0, 0);
	$res_insert = pg_query("INSERT INTO prodebians (id_debversion, name) VALUES ('$id[0]', '$_POST[prodebname]');");
	if(!$res_insert) error_page();
	$last_oid = pg_last_oid($res_insert);
	$res = pg_query($database, "SELECT id_prodebian FROM prodebians WHERE oid=$last_oid;");
	$id = pg_fetch_result($res, 0, 0);
	beginpage();
	print '
	Votre Prodebian a bien été créée sous le numéro de référence #'.$id.'.<br />
	Veuillez noter ce numéro car il vous permettra de retrouver rapidement votre Prodebian.<br />
	<br />Pour pouvez maintenant accéder à la page de configuration de votre Prodebian en cliquant sur le lien ci-dessous :<br />
	<a href="prodebian.php?id='.$id.'" size="4">Prodebian #'.$id.'</a>
	';
	//goto_page("prodebian.php?id=$id");
}

?>
