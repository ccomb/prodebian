<?php
session_start();

include 'my_functions.php';
//my_purge_data();

//my_debug();

//check arguments
if(!isset($_GET['id_action'])) my_gotopage("findprodebian.php");

$database = my_connectdatabase();
if(!isset($_SESSION['id_prodebian'])) my_gotopage("findprodebian.php");

// check that the action exists in the prodebian (for my_authenticate)
// to avoid modify any other action when authenticated for the current prodebian
$res = pg_query($database, "SELECT id_owner,actionlist FROM prodebians WHERE id_prodebian='".$_SESSION['id_prodebian']."';") or die();
$prodebians = pg_fetch_array($res);
$found = array_search($_GET['id_action'], my_array_psql2php($prodebians['actionlist']));
if(is_bool($found) AND $found==FALSE) my_gotopage("error.php?why=badaction");

// SAVE THE TITLE AND THE SCRIPT
if(isset($_POST['runscript']) AND isset($_POST['title'])) {
	my_authenticate($prodebians['id_owner']);
	pg_query($database, "UPDATE actions SET actionvalues='{\"".my_string_php2psql($_POST['runscript'])."\"}',title='".my_string_php2psql($_POST['title'])."' WHERE id_action='".$_GET['id_action']."';") or die();
	my_gotopage("runscript.php?id_action=".$_GET['id_action']);
}
//-------------------
// GET THE SCRIPT
$res = pg_query($database, "SELECT title,actiontype,actionvalues FROM actions WHERE id_action='".$_GET['id_action']."';") or die();
$actions = pg_fetch_array($res);
if($actions['actiontype']!=4) my_gotopage("error.php?why=badtype");
$script = my_script_psql2php($actions['actionvalues']);
//$script=substr($script['0'],1,strlen($script['0'])-2);
//-------------------
// EDIT THE TITLE AND SCRIPT IF REQUESTED
if(isset($_GET['edit'])) { 
	my_beginpage();
	my_printmenu();
	if($actions['title']=='') {
	  $actions['title']="(enter a short descriptive title for this action)";
	  $onfocustitle='onFocus="this.value=&quot;&quot;"';
	}
	if($script=='') {
	  $script="(enter a small script to run)";
	  $onfocuscript='onFocus="this.value=&quot;&quot;"';
	}
	print '
	<b>Title:</b><br />
	<form action="runscript.php?id_action='.$_GET['id_action'].'" method="POST">
		<input type="text" name="title" value="'.my_string_psql2php($actions['title']).'" size="64" maxlength="64" '.$onfocustitle.' />
		<br /><br />
		<b>Small script to run (with interpretor as first line, example: #!/bin/sh):</b><br />
		<textarea name="runscript" rows="15" cols="60" '.$onfocuscript.'>'.my_string_psql2php($script).'</textarea><br />
		<a href=runscript.php?id_action='.$_GET['id_action'].'>cancel</a> <button type="submit">save</button>
	</form>
	<br />
	';
	my_endpage();
//-------------------
// DISPLAY THE TITLE AND SCRIPT
} else {
	my_beginpage();
	my_printmenu();
	if($actions['title']=='') $actions['title']="(no title, clic on \"edit\" to add one)";
	if($script=='') $script="(no script, clic on \"edit\" to create one)";
	print '<b>'.my_string_psql2php($actions['title']).'</b><br />
	<hr align="left" size="1" width="100%" />
	<pre><code class="bash">'.my_string_psql2php($script).'</code></pre>
	<a href="runscript.php?id_action='.$_GET['id_action'].'&amp;edit">edit</a>
	<br />
	';
	//-------------------
	my_endpage();
}
?>
