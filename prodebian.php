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
$res = pg_query($database, "SELECT * FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';");
$prodebian = pg_fetch_array($res);
if($prodebian==0) my_gotopage("error.php?why=invalidprodebian");
// debian version
$res = pg_query($database, "SELECT * FROM debversions WHERE id_debversion=".$prodebian['id_debversion'].";");
$debversion = pg_fetch_array($res);
// package list
$id_packlist = $prodebian['id_packlist'];
if($id_packlist=='{}') $pack_number=0;
else {
	$res = pg_query($database, "SELECT * FROM package_lists WHERE id_packlist=".$id_packlist.";");
	$package_lists = pg_fetch_array($res);
	$pack_number = count(my_string2array($package_lists['packlist']));
}
// description
if(isset($prodebian['id_desc'])) {
	$res = pg_query($database, "SELECT description FROM descriptions WHERE id_desc='".$prodebian['id_desc']."';");
	$descriptions = pg_fetch_array($res);
} else $descriptions['description']="No description. Please add one.";
// owner
if(isset($prodebian['id_owner'])) {
	$res = pg_query($database, "SELECT id_owner,username FROM owners WHERE id_owner='".$prodebian['id_owner']."';");
	$owners = pg_fetch_array($res);
} else { 
	$owners['username']="(click to appropriate)";
	$owners['id_owner']=0;
}

//-------------------
// DISPLAY THE PRODEBIAN SUMMARY
my_beginpage();
my_printmenu();

print "<b>Prodebian #".$prodebian['id_prodebian']."</b><br />";
print '<hr align="left" size="2" width="50%" />';
print "<a href=description.php>Title and description</a> : <b>".$prodebian['name']."</b><br />";
print $descriptions['description'];
print '<hr align="left" size="2" width="50%" />';
print "Based on Debian version : ".$debversion['version_name']."<br />";
print "packages : <a href=packagelist.php>".$pack_number." package(s)</a><br />";
print "postinstall actions: "."TBD<br />";
print 'owner: <a href="owner.php?id='.$owners['id_owner'].'">'.$owners['username'].'</a><br />';
print "dedicated to a particular hardware : "."yes or no TBD<br />";
print "dedicated to a particular job : TBD<br />";
print "user experience : "."TBD<br />";
print "architecture : "."TBD<br />";
//print "mailing list for this prodebian : "."yes or no TBD<br />";
print "language of users<br />";


print '
<form action="generateprodebian.php" method="GET">
	<button name="id" value="'.$prodebian['id_prodebian'].'" type="submit">generate</button>
</form>
<form action="deleteprodebian.php" method="POST">
	<button name="delete" type="submit">delete</button>
</form>
';

//-------------------
my_endpage();
?>
