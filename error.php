<?php
//session_start();
include 'html.php';

if(!isset($_GET['why'])) goto_page("error.php?why=error");
$error = $_GET['why'];

beginpage();
print_menu();
//-----------------------
if($error=="error") {
	print "error";
}

if($error=="inserterror") {
	print 'Error inserting data to the database.';
}

if($error=="invalidprodebian") {
	print 'This Prodebian does not exist.';
}

if($error=="deleteerror") {
	print 'Error deleting data from the database.';
}

//-----------------------
endpage();