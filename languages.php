<?php
include('html.php');
beginpage();
//-------------------

// add a form to enter a new language
print '<form action="languages.php" method="POST">
  nom de la langue : <input type="text" name="langname" size="16" maxlength="16" /><br />
  code de la langue : <input type="text" name="langcode" size="16" maxlength="16" /><br />
  <button name="create" type="submit">ajouter</button></form>';
  
// get the available languages
$database = pg_connect("host=localhost dbname=prodebian user=ccomb password=prodebian");
$res = pg_query($database, "SELECT * FROM languages ORDER BY language_name;");


// insert a new language
if(isset($_POST[langname]) && isset($_POST[langcode])) {
	$language_found=FALSE;
	while($languages = pg_fetch_array($res)) {
		if($languages[language_name]==$_POST[langname]) {$language_found=TRUE;	break;}
		if($languages[language_code]==$_POST[langcode]) {$language_found=TRUE;	break;}
	}
	if($language_found) {
		echo "Cette langue existe déjà !<br /><br />";
	} else {
		pg_query("INSERT INTO languages VALUES ('$_POST[langcode]', '$_POST[langname]');");
		$res = pg_query($database, "SELECT * FROM languages ORDER BY language_name;");
	}
}
// display availables languages
echo "<u>liste des langues disponibles :</u><br />";
for($row=0; $row<pg_num_rows($res); $row++) {
	$languages = pg_fetch_array($res, $row);
	print "$languages[language_name] ($languages[language_code])<br />";
}
pg_close($database);

//-------------------
endpage();

?>

