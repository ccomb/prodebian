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

if($error=="inserterror") {
	print 'Error inserting data to the database.';
}

if($error=="updateerror") {
	print 'Error updating data in the database.';
}

if($error=="invalidprodebian") {
	print 'This Prodebian does not exist.';
}

if($error=="deleteerror") {
	print 'Error deleting data from the database.';
}

if($error=="autherror") {
	print 'Permission denied';
}
//-----------------------
my_endpage();