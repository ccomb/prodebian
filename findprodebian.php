<?php
session_start();
include 'html.php';
purge_data();


if(isset($_POST['nameis'])) {
	$database = connect_database();
	$res = pg_query($database, "SELECT id_prodebian FROM prodebians WHERE name='".$_POST['nameis']."';");
	$prodebian = pg_fetch_array($res);
	goto_page("prodebian.php?id=".$prodebian['id_prodebian']);
}

//-------------------
//PROMPT THE USER
beginpage();
print_menu();

print '
<form action="findprodebian.php" method="POST">
	<button name="namecontains" value="" type="submit">Display all Prodebian</button>
</form>
';
print '<b>Search a Prodebian</b><br />';
print '<form action="prodebian.php" method="GET">
	by its reference number : 
	<input type="text" name="id" size="5" maxlength="5" />
	<button name="find" type="submit">search</button>
</form>
';
/*print '
<form action="findprodebian.php" method="POST">
	Its title is : 
	<input type="text" name="nameis" size="10" maxlength="10" />
	<button name="find" type="submit">chercher</button>
</form>
';*/
print '
<form action="findprodebian.php" method="POST">
	Its title contains : 
	<input type="text" name="namecontains" size="10" maxlength="10" />
	<button name="find" type="submit">search</button>
</form>
';
//-------------------
// SEARCH AND DISPLAY THE RESULT AT THE END OF THE PAGE

print '
<hr align="left" size="2" width="100%" />
<b>Search results:</b><br />
';

if(isset($_POST['namecontains'])) {
	$database = connect_database();
	$from=array("*", "\\", "_", "%", "|", "+", "?", "^", "(", ")", "[", "]");
	$to=array("\\\*", "\\\\", "\\\_", "\\\%", "\\\|", "\\\+", "\\\?", "\\\^", "\\\(", "\\\)", "\\\[", "\\\]");
	$_POST['namecontains']=str_replace($from, $to, $_POST['namecontains']);
	$res = pg_query($database, "SELECT id_prodebian, name FROM prodebians WHERE name SIMILAR TO '%".$_POST['namecontains']."%';");
	$howmany=pg_num_rows($res);
	for($row=0; $row<$howmany; $row++) {
		$prodebian = pg_fetch_array($res, $row);
		print "<a href=prodebian.php?id=".$prodebian['id_prodebian'].">Prodebian #".$prodebian['id_prodebian']."</a> : ".$prodebian['name']."<br />";
	}
}
print '
<hr align="left" size="2" width="100%" />
';
endpage();
?>
