<?php
session_start();

include 'my_functions.php';
my_purge_data();

//my_debug();

//check arguments
if(!isset($_GET['id_action'])) my_gotopage("findprodebian.php");

$database = my_connectdatabase();
if(!isset($_SESSION['id_prodebian'])) my_gotopage("findprodebian.php");

// check that the action exists in the prodebian (for my_authenticate)
// to avoid modify any external action when authenticated for the current prodebian
$res = pg_query($database, "SELECT id_owner,actionlist FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
$prodebians = pg_fetch_array($res);
$found = array_search($_GET['id_action'], my_array_psql2php($prodebians['actionlist']));
if(is_bool($found) AND $found==FALSE) my_gotopage("error.php?why=badaction");

// SAVE THE SCRIPT
if(isset($_POST['runscript'])) {
	my_authenticate($prodebians['id_owner']);
	pg_query($database, "UPDATE actions SET actionvalues='".my_string_php2psql($_POST['runscript'])."' WHERE id_action='".$_GET['id_action']."';") or die();
	my_gotopage("runscript.php?id_action=".$_GET['id_action']);
}
//-------------------
// SAVE THE TITLE
if(isset($_POST['title'])) {
	my_authenticate($prodebians['id_owner']);
	pg_query($database, "UPDATE actions SET title='".addslashes($_POST['title'])."' WHERE id_action='".$_GET['id_action']."';") or die();
	my_gotopage("runscript.php?id_action=".$_GET['id_action']);
}
//-------------------
// GET THE SCRIPT
$res = pg_query($database, "SELECT title,actiontype,actionvalues FROM actions WHERE id_action='".$_GET['id_action']."';") or die();
$actions = pg_fetch_array($res);
if($actions['actiontype']!=4) my_gotopage("error.php?why=badtype");
$script = my_string_psql2php($actions['actionvalues']);
//$script=substr($script['0'],1,strlen($script['0'])-2);
//-------------------


my_beginpage();
my_printmenu();

if($actions['title']=='') {
  $help="(enter a short descriptive title for this action)";
  $onfocustitle='onFocus="this.value=&quot;&quot;"';
} else $onfocustitle='onFocus="this.value=&quot;'.$actions['title'].'&quot;"';
if($script=='') {
  $script="(enter a small script to run)";
  $onfocuscript='onFocus="this.content=&quot;&quot;"';
 }
print '<b>Title: '.stripslashes($actions['title']).'</b><br />
<form action="runscript.php?id_action='.$_GET['id_action'].'" method="POST">
<button type="submit">save</button>
<input type="text" name="title" value="'.$help.'" size="64" maxlength="64" '.$onfocustitle.' />
</form>

<hr align="left" size="1" width="100%" />

<b>Small script to run:</b> <pre><code class=bash>'.$script.'</code></pre>
<form action="runscript.php?id_action='.$_GET['id_action'].'" method="POST">
<textarea name="runscript" rows="15" cols="60">'.$script.'</textarea><br />
<button type="submit">save</button>
</form>
<br />
';


//-------------------
my_endpage();
?>
