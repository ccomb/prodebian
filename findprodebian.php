<?php
session_start();
include 'my_functions.php';
my_purge_data();

// search by name
if(isset($_POST['namecontains'])) {
	$_SESSION['searchresult'] = array();
	$_SESSION['searchresult']['namecontains'] = $_POST['namecontains'];
	$database = my_connectdatabase();
	$from=array("*", "\\", "_", "%", "|", "+", "?", "^", "(", ")", "[", "]");
	$to=array("\\\*", "\\\\", "\\\_", "\\\%", "\\\|", "\\\+", "\\\?", "\\\^", "\\\(", "\\\)", "\\\[", "\\\]");
	$_POST['namecontains']=str_replace($from, $to, $_POST['namecontains']);
	$result = pg_query($database, "SELECT id_prodebian, name FROM prodebians WHERE name SIMILAR TO '%".$_POST['namecontains']."%';");
}

// store the result in SESSION
if(isset($result)) {
	$howmany=pg_num_rows($result);
	for($row=0; $row<$howmany; $row++) {
		$prodebian = pg_fetch_array($result, $row);
		$_SESSION['searchresult'][$prodebian['id_prodebian']]=$prodebian['name'];
	}
	// don't display now, just reload the find page so that there is no POST data. (can click back without resubmit)
	my_gotopage("findprodebian.php");
}

/*
if(isset($_POST['nameis'])) {
	$database = my_connectdatabase();
	$res = pg_query($database, "SELECT id_prodebian FROM prodebians WHERE name='".$_POST['nameis']."';");
	$prodebian = pg_fetch_array($res);
	my_gotopage("prodebian.php?id=".$prodebian['id_prodebian']);
}
*/
//-------------------
//PROMPT THE USER
my_beginpage();
my_printmenu();

print '
<form action="findprodebian.php" method="POST">
	<button name="namecontains" value="" type="submit">Display all Prodebian</button>
</form>
';
print '<b>Search a Prodebian</b><br />';
print '<form action="prodebian.php" method="GET">
	by its reference number : 
	<input type="text" name="id" size="5" maxlength="5" />
	<button type="submit">search</button>
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
	<input type="text" name="namecontains" value="'.$_SESSION['searchresult']['namecontains'].'" size="10" maxlength="10" />
	<button name="find" type="submit">search</button>
</form>
';

//-------------------
// DISPLAY THE STORED RESULT AT THE END OF THE PAGE

print '
<hr align="left" size="2" width="100%" />
<b>Search result:</b><br />
';

if(isset($_SESSION['searchresult'])) {
	// display the stored search result
	foreach($_SESSION['searchresult'] as $id => $title) {
			if($id!=0) print "<a href=prodebian.php?id=".$id.">Prodebian #".$id."</a> : ".$title."<br />";
	}
}
print '
<hr align="left" size="2" width="100%" />
';
my_endpage();
?>
