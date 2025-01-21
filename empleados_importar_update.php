<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
$restrict->addLevel("3");
$restrict->addLevel("4");
$restrict->addLevel("5");
$restrict->Execute();
//End Restrict Access To Page


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
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT * FROM con_empleados";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

set_time_limit(0);

 do { 

$IDempleado = $row_activos['IDempleado'];
$llave = $row_activos['descripcion_nomina'] . $row_activos['descripcion_nivel'] . $row_activos['denominacion'];

mysql_select_db($database_vacantes, $vacantes);
$query_llave = "SELECT * FROM prod_llave WHERE llave = '$llave'";
$llave = mysql_query($query_llave, $vacantes) or die(mysql_error());
$row_llave = mysql_fetch_assoc($llave);
$totalRows_llave = mysql_num_rows($llave);
$IDmatriz = $row_llave['IDmatriz'];
$IDsucursal = $row_llave['IDsucursal'];
$IDpuesto = $row_llave['IDpuesto'];
$IDarea = $row_llave['IDarea'];

if ($totalRows_llave > 0 ) {

$query1 = "UPDATE con_empleados SET IDmatriz = '$IDmatriz', IDsucursal = '$IDsucursal', IDpuesto = '$IDpuesto', IDarea = '$IDarea' WHERE IDempleado = '$IDempleado'"; 
$result1 = mysql_query($query1) or die(mysql_error());  

} else {

$query2 = "DELETE FROM con_empleados WHERE IDempleado = '$IDempleado'"; 
$result2 = mysql_query($query2) or die(mysql_error());  

}


} while ($row_activos = mysql_fetch_assoc($activos));

header("Location: productividad_importar.php?info=1"); 	
		
?>