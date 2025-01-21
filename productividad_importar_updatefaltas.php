<?php require_once('Connections/vacantes.php'); 

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

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


mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT prod_activosfaltas.* FROM prod_activosfaltas";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

set_time_limit(0);

 do { 

  $llave = $row_activos['descripcion_nomina'] . $row_activos['descripcion_nivel'] . $row_activos['denominacion'];
  $IDempleado = $row_activos['IDempleado'];
  
  //campos adicionales
  mysql_select_db($database_vacantes, $vacantes);
  $query_llave = "SELECT * FROM prod_llave WHERE llave = '$llave'";
  $llave = mysql_query($query_llave, $vacantes) or die(mysql_error());
  $row_llave = mysql_fetch_assoc($llave);
  $totalRows_llave = mysql_num_rows($llave);
          
  $IDmatriz = $row_llave['IDmatriz'];
  $IDsucursal = $row_llave['IDsucursal'];
  $IDpuesto = $row_llave['IDpuesto'];
  $IDarea = $row_llave['IDarea'];
  $IDllave = $row_llave['IDllave'];
				
if ($totalRows_llave > 0) {
$query1 = "UPDATE prod_activosfaltas SET IDllave = $IDllave, IDmatriz = '$IDmatriz', IDsucursal = '$IDsucursal', IDpuesto = '$IDpuesto', IDarea = '$IDarea', activo = 1 WHERE IDempleado = '$IDempleado'"; 
$result1 = mysql_query($query1) or die(mysql_error());  
 } 
 
 } while ($row_activos = mysql_fetch_assoc($activos));

$query3 = "DELETE FROM prod_activosfaltas WHERE descripcion_nomina = 'Nomina Semanal Yana' OR descripcion_nomina = 'Nomina Quincenal Yana'"; 
$result3 = mysql_query($query3) or die(mysql_error());  

//$query4 = "UPDATE nombre_tabla SET campo = REPLACE(campo, '_', '/');"; 
//$result4 = mysql_query($query4) or die(mysql_error());  
?>