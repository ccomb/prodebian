<?php
//session_start();
include 'my_functions.php';

if(!isset($_GET['why'])) my_gotopage("error.php?why=error");
$error = $_GET['why'];

my_beginpage();
my_printmenu();
//-----------------------
if($error=="error") {
	print "error";
}

elseif($error=="inserterror") {
	print 'Error inserting data to the database.';
}

elseif($error=="updateerror") {
	print 'Error updating data in the database.';
}

elseif($error=="invalidprodebian") {
	print 'This Prodebian does not exist.';
}

elseif($error=="deleteerror") {
	print 'Error deleting data from the database.';
}

elseif($error=="selecterror") {
	print 'Error retrieving data from the database.';
}
elseif($error=="autherror") {
	print 'Permission denied';
}

else { print "error"; }
//-----------------------
my_endpage();