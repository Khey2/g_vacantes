<?php require_once('Connections/vacantes.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$usr_ = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM prod_activos WHERE IDempleado = '$usr_'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$el_usr = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_usuario2 = "SELECT * FROM prod_activosj WHERE IDempleado = '$usr_'";
$usuario2 = mysql_query($query_usuario2, $vacantes) or die(mysql_error());
$row_usuario2 = mysql_fetch_assoc($usuario2);
$totalRows_usuario2 = mysql_num_rows($usuario2);


$el_usr2 = md5($row_usuario['IDempleado']);
echo $el_usr;

   $sql_ = "UPDATE prod_activos SET password = '$el_usr2' WHERE IDempleado = '$el_usr'";
   $sql_ = mysql_query($sql_) or die(mysql_error());  
   
 if ($totalRows_usuario2 > 0) {  

   $sql_2 = "UPDATE prod_activosj SET password = '$el_usr2' WHERE IDempleado = '$el_usr'";
   $sql_2 = mysql_query($sql_2) or die(mysql_error());  

 } else {
	 
  $updateSQL = "INSERT INTO prod_activosj (IDempleado, password) VALUES ('$el_usr', '$el_usr2')"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

 }
   
   header('Location:admin_plantilla_activos.php?info=2');
?>