<?php 
if (!isset($_COOKIE['lmenu'])) {setcookie('lmenu', 1, time() + 600); } else {setcookie('lmenu', '', time() + 600); }
$liga = $_GET['liga'];

echo $_COOKIE["lmenu"];

header("Location: " . $liga . "?IDresultado=271");

?>
