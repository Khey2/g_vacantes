<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works


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

if (isset($_GET['info']) AND $_GET['info'] == 3) { 

mysql_select_db($database_vacantes, $vacantes);
$query1 = "truncate TABLE pc_semaforo_temp"; 
$result1 = mysql_query($query1) or die(mysql_error());  
header("Location: admin_plan_carrera_importar.php?info=1"); 	
}


if (isset($_GET['info']) AND $_GET['info'] == 1) { 

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT * FROM pc_semaforo_temp";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

 do { 

	$IDempleado = $row_activos['IDempleado'];
	$IDpuesto = $row_activos['IDpuesto'];
	$reqa = $row_activos['reqa'];
	$reqb = $row_activos['reqb'];
	$reqc = $row_activos['reqc'];
	$reqd = $row_activos['reqd'];
	$reqe = $row_activos['reqe'];
	$reqf = $row_activos['reqf'];
	$observaciones = $row_activos['observaciones'];

if ($row_activos['estatus'] == 1) {
	
$query1 = "INSERT INTO pc_semaforo (IDempleado, IDpuesto, reqa, reqb, reqc, reqd, reqe, reqf, observaciones, estatus) VALUES ('$IDempleado', '$IDpuesto', '$reqa', '$reqb', '$reqc', '$reqd', '$reqe', '$reqf', '$observaciones', 1)"; 
$result1 = mysql_query($query1) or die(mysql_error());  
echo $IDempleado." insertado";

} else {
	
$query1 = "UPDATE pc_semaforo SET IDempleado = '$IDempleado', IDpuesto = '$IDpuesto', reqa = '$reqa', reqb = '$reqb', reqc = '$reqc', reqd = '$reqd', reqe = '$reqe', reqf = '$reqf', observaciones = '$observaciones' WHERE IDempleado = '$IDempleado'"; 
$result1 = mysql_query($query1) or die(mysql_error());  
echo $IDempleado." actualizado";

} 

} while ($row_activos = mysql_fetch_assoc($activos));

//borramos
$query3 = "truncate TABLE pc_semaforo_temp"; 
$result3 = mysql_query($query3) or die(mysql_error());  

header("Location: admin_plan_carrera_importar.php?info=1"); 	

}
?>