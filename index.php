<?php
include 'my_functions.php';
my_beginpage();
print '<img src="prodebian.png" /><br />
Prodebian<br />
<br />
<div style="text-align: center">One can understand Prodebian with just one schema:<br />
<img src="schema.png" alt="prodebian schema" /></div><br />


Imagine you want to install <img src="debian-mini.png" /><a href="http://debian.org">Debian</a> for a customer, for a friend, for your mother or just for a show.
So you want a particular set of packages, with a particular configuration,
a customized desktop with predefined icons and predefined shortcuts in mozilla.
Maybe you want to set-up a small ready-to-use proxy server.
Or you want a PC dedicated to CD cloning with a minimal user interface.
Or you just want to store a configuration for installing at your friends\' 
or that you would like to be a standard for professional use.<br />
<br />
Find the configuration you need in the Prodebian database, modify an existing one or build your own from scratch!<br />
<br />
A <img src="prodebian-mini.png" />Prodebian is a <img src="debian-mini.png" />Debian system dedicated to a particular task, job and/or hardware.<br />
<br />
<a href="createprodebian.php">Create your Prodebian</a><br />
<a href="findprodebian.php">Search a Prodebian</a><br />
';
//<a href="chooseprodebian.php">Chose a Prodebian</a><br />
//<a href="languages.php">Ajouter une nouvelle langue</a>


my_endpage();

?>