<?php
include 'html.php';
purge_data();
session_start();
//-------------------
// PROMPT FOR USERNAME
if(!isset($_SESSION['username']) || !isset($_SESSION['password'])) {
	


// demande le username et password
// si existe, approprie
// si n'existe pas, crée l'user
// demande le mot de passe (et le mail facultatif)
// approprie



beginpage();
print_menu();

print '<b>Prodebian #'.$_SESSION['id_prodebian'].'</b>
<form action="description.php" method="POST">
Username:
<input type="text" name="name" value="'.$prodebian['name'].'"size="32" maxlength="32" /><br />
Password:
<input type="text" name="name" value="'.$prodebian['name'].'"size="32" maxlength="32" /><br />
E-mail:
<input type="text" name="name" value="'.$prodebian['name'].'"size="32" maxlength="32" /><br />
<button name="save" type="submit">save</button>
</form>';


//-------------------
endpage();
?>