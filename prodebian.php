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
$database = my_connectdatabase();

// UPDATE TITLE AND DESCRIPTION
if(isset($_POST['title']) AND isset($_POST['desc']) AND isset($_POST['debversion'])) { // get the description ID
	$res = pg_query($database, "SELECT id_owner FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
	$prodebian = pg_fetch_array($res);
	my_authenticate($prodebian['id_owner']);
	$res = pg_query($database, "UPDATE prodebians SET description='".my_string_php2psql($_POST['desc'])."', title='".my_string_php2psql($_POST['title'])."', debversion=".$_POST['debversion']." WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
	my_gotopage("prodebian.php?id=".$_SESSION['id_prodebian']);
}

//-------------------
// GET PRODEBIAN DATA
$res = pg_query($database, "SELECT * FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
$prodebians = pg_fetch_array($res);
if($prodebians==0) my_gotopage("error.php?why=invalidprodebian");
// package list
$actionlist = $prodebians['actionlist'];
if($actionlist=='{}') $action_number=0;
else $action_number = count(my_array_psql2php($actionlist));

// owner
if(isset($prodebians['id_owner'])) {
	$res = pg_query($database, "SELECT id_owner,username FROM owners WHERE id_owner='".$prodebians['id_owner']."';") or die();
	$owners = pg_fetch_array($res);
} else { 
	$owners['username']="(click to own)";
	$owners['id_owner']=0;
}
//-------------------
// EDIT THE PRODEBIAN PARAMETERS
if(isset($_GET['edit'])) {
	my_beginpage();
	my_printmenu();
	if($prodebians['description']=='') {
	  $prodebians['description']="(enter a detailed description of this prodebian)";
	  $onfocusdesc='onFocus="this.value=&quot;&quot;"';
	}
	if($prodebians['title']=='') {
	  $prodebians['title']="(enter a short descriptive title for this prodebian)";
	  $onfocustitle='onFocus="this.value=&quot;&quot;"';
	}	print '
	<h2>Prodebian #'.$prodebians['id_prodebian'].'</h2>
	<form action="prodebian.php?id='.$_SESSION['id_prodebian'].'" method="POST">
	based on Debian version :
	<select name="debversion" size="1">
		<option value="2.2">'.my_debianversion(2.2).'</option>
		<option value="3.0">'.my_debianversion(3.0).'</option>
		<option value="3.1" selected>'.my_debianversion(3.1).'</option>
		<option value="99">'.my_debianversion(99).'</option>
	</select><br />
	Short descriptive title :
	<input type="text" name="title" value="'.my_string_psql2php($prodebians['title']).'"size="64" maxlength="64" '.$onfocustitle.' /><br /><br />
	Detailed description: (Limited to 900 chars)<br /><textarea name="desc" rows="15" cols="60" '.$onfocusdesc.' >'.my_string_psql2php($prodebians['description']).'</textarea><br />
	<a href=prodebian.php?id='.$prodebians['id_prodebian'].'>cancel</a> <button name="save" type="submit">save</button>
	</form>';
	my_endpage();
} else {
//-------------------
// DISPLAY THE PRODEBIAN SUMMARY
my_beginpage();
my_printmenu();
if($prodebians['description']=='') $prodebians['description']='(no description. Please add one!)';
print "<b>Prodebian #".$prodebians['id_prodebian'].": ".my_string_psql2php($prodebians['title'])."</b>";
print "<pre>".my_string_psql2php($prodebians['description'])."</pre>";
print '<hr align="left" size="2" width="100%" />';
print "Based on Debian version : ".my_debianversion($prodebians['debversion'])."<br />";
print "list of actions: <a href=actionlist.php>".$action_number." action(s)</a><br />";
print 'owner: <a href="owner.php?id='.$owners['id_owner'].'">'.$owners['username'].'</a><br />';
//print "dedicated to a particular hardware : "."yes or no TBD<br />";
//print "dedicated to a particular job : TBD<br />";
//print "user experience : "."TBD<br />";
//print "architecture : "."TBD<br />";
//print "mailing list for this prodebian : "."yes or no TBD<br />";
//print "language of users<br />";


print '
<a href=prodebian.php?id='.$prodebians['id_prodebian'].'&edit>edit</a>
 | <a href=deleteprodebian.php?edit>delete</a>
 | <a href=generateprodebian.php?id='.$prodebians['id_prodebian'].'>download</a>
';

//-------------------
my_endpage();
}
?>
