<?php require_once('Connections/vacantes.php'); ?>
<?php

mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM prod_activos WHERE IDempleado = '65'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);

do { 

$pass1 = $row_usuario['password'];
$pass2 = $row_usuario['IDempleado'];
$pass3 = md5($row_usuario['IDempleado']);

echo  "1: ".$pass1." ";
echo  "2: ".$pass1." ";
echo  "3: ".$pass1." ";
echo "<br>";

if ($pass1 == $pass2) {
  $updateSQL = "UPDATE prod_activos SET password = '$pass3' WHERE IDempleado = '$pass2' ";
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error()); 
} 

} while ($row_usuario = mysql_fetch_assoc($usuario));
?>