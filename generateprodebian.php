<?php
session_start();
include 'html.php';
purge_data();

//===============
// CHECK ARGUMENTS
if(!isset($_GET['id'])) goto_page("findprodebian.php");
if((int)$_GET['id']==0) goto_page("findprodebian.php");
$_SESSION['id_prodebian'] = $_GET['id'];

//-------------------
// GENERATE THE PRODEBIAN
beginpage();
print_menu();
print '
1) Download the Debian netinst ISO image here : <a href=http://debian.org/>netinst.iso</a><br />
<form action="prodebian.php" method="POST">
2) Download your Prodebian installation guide :<button name="dldoc" type="submit">download guide</button>
</form>
<form action="prodebian.php" method="POST">
3) Download your Prodebian installation script :<button name="dlscript" type="submit">download script</button>
</form>
';
endpage();
exit();


?>