<?php

function beginpage() {
print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>prodebian</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
';
}
function print_menu() {
print '
<a href="index.php">accueil</a><br />
<br />
';
}
function endpage() {
print '</body></html>
';
}
function error_page() {
beginpage();
print 'Error inserting data to the database.';
endpage();
}
function goto_page($page) {
header("Location: http://".$_SERVER['HTTP_HOST']
                      .dirname($_SERVER['PHP_SELF'])
                      .$page);
}
?>
