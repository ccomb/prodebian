<?php
include 'my_functions.php';
my_purge_data();
session_start();

//-------------------
// CHECK ARGUMENTS
if(isset($_GET['id'])) {
	$_GET['id']=(int)$_GET['id'];
	if($_GET['id']==0) my_gotopage("error.php?why=invalidprodebian");
	$_SESSION['id_prodebian'] = $_GET['id'];
}

if(!isset($_SESSION['id_prodebian'])) my_gotopage("findprodebian.php");

//-------------------
// GET PRODEBIAN DATA
$database = my_connectdatabase();
$res = pg_query($database, "SELECT * FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
$prodebians = pg_fetch_array($res);
if($prodebians==0) my_gotopage("error.php?why=invalidprodebian");
// package list
$actionlist = $prodebians['actionlist'];
if($actionlist=='{}') $action_number=0;
else $action_number = count(my_string2array($actionlist));

// description
if(isset($prodebians['id_desc'])) {
	$res = pg_query($database, "SELECT description FROM descriptions WHERE id_desc='".$prodebians['id_desc']."';") or die();
	$descriptions = pg_fetch_array($res);
} else $descriptions['description']="No description. Please add one.";
// owner
if(isset($prodebians['id_owner'])) {
	$res = pg_query($database, "SELECT id_owner,username FROM owners WHERE id_owner='".$prodebians['id_owner']."';") or die();
	$owners = pg_fetch_array($res);
} else { 
	$owners['username']="(click to appropriate)";
	$owners['id_owner']=0;
}

//-------------------
// DISPLAY THE PRODEBIAN SUMMARY
my_beginpage();
my_printmenu();

print "<b>Prodebian #".$prodebians['id_prodebian']."</b><br />";
print '<hr align="left" size="2" width="50%" />';
print "<a href=description.php>Title and description</a> : <b>".$prodebians['title']."</b><br />";
print $prodebians['description'];
print '<hr align="left" size="2" width="50%" />';
print "Based on Debian version : ".my_debianversion($prodebians['debversion'])."<br />";
print "list of actions: <a href=actionlist.php>".$action_number." action(s)</a><br />";
print 'owner: <a href="owner.php?id='.$owners['id_owner'].'">'.$owners['username'].'</a><br />';
print "dedicated to a particular hardware : "."yes or no TBD<br />";
print "dedicated to a particular job : TBD<br />";
print "user experience : "."TBD<br />";
print "architecture : "."TBD<br />";
//print "mailing list for this prodebian : "."yes or no TBD<br />";
print "language of users<br />";


print '
<form action="generateprodebian.php" method="GET">
	<button name="id" value="'.$prodebians['id_prodebian'].'" type="submit">generate</button>
</form>
<form action="deleteprodebian.php" method="POST">
	<button name="delete" type="submit">delete</button>
</form>
';

//-------------------
my_endpage();
?>
