<?php
session_start();
include 'my_functions.php';
my_purge_data();


my_beginpage();
my_printmenu();

// get the latest version and increment it
// id_base = id de la prodebian de devel au moment de la release
print 'Do you want to release this prodebian as version ...';


my_endpage();
?>
	