<?php
include('my_functions.php');
my_purge_data();
my_beginpage();
my_printmenu();
//-------------------

// add a form to enter a new language
print '<form action="languages.php" method="POST">
  nom de la langue : <input type="text" name="langname" size="16" maxlength="16" /><br />
  code de la langue : <input type="text" name="langcode" size="16" maxlength="16" /><br />
  <button name="create" type="submit">ajouter</button></form>';
  
// get the available languages
$database = my_connectdatabase();
$res = pg_query($database, "SELECT * FROM languages ORDER BY language_name;") or die();


// insert a new language
if(isset($_POST['langname']) AND isset($_POST['langcode'])) {
	$language_found=FALSE;
	while($languages = pg_fetch_array($res)) {
		if($languages['language_name']==$_POST['langname']) {$language_found=TRUE;	break;}
		if($languages['language_code']==$_POST['langcode']) {$language_found=TRUE;	break;}
	}
	if($language_found) {
		echo "Cette langue existe déjà !<br /><br />";
	} else {
		pg_query("INSERT INTO languages VALUES ('".$_POST['langcode']."', '".$_POST['langname']."');") or die();
		$res = pg_query($database, "SELECT * FROM languages ORDER BY language_name;") or die();
	}
}
// display availables languages
echo "<b>liste des langues disponibles :</b><br />";
for($row=0; $row<pg_num_rows($res); $row++) {
	$languages = pg_fetch_array($res, $row);
	print $languages['language_name']." (".$languages['language_code'].")<br />";
}
pg_close($database);

//-------------------
my_endpage();

?>

