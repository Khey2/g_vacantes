<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
$restrict->Execute();
//End Restrict Access To Page

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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 

if (isset($_GET['IDperiodo'])) {$IDperiodo = $_GET['IDperiodo'];} 
elseif (isset($_SESSION['IDperiodo'])) {$IDperiodo = $_SESSION['IDperiodo'];} 
else {$IDperiodo = $row_variables['IDperiodo'];}

$_SESSION['IDperiodo'] = $IDperiodo;

$id  = $_GET['id'];
$IDempleado  = $_GET['IDempleado'];
$el_usuario  = $_GET['IDempleado'];
$tipo_importacion = $_GET['importar'];
$IDperiodo_anterior = $IDperiodo - 1;

echo $IDperiodo_anterior;


if ($tipo_importacion == 1){

//importar solo pendientes
$query_mis_metas_anteriores_pendientes = "SELECT * FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo_anterior' AND sed_individuales.mi_resultado = 4"; 
mysql_query("SET NAMES 'utf8'");
$mis_metas_anteriores_pendientes = mysql_query($query_mis_metas_anteriores_pendientes, $vacantes) or die(mysql_error());
$row_mis_metas_anteriores_pendientes = mysql_fetch_assoc($mis_metas_anteriores_pendientes);

do {
	
	$IDempleado = $row_mis_metas_anteriores_pendientes['IDempleado'];
	$mi_mi = $row_mis_metas_anteriores_pendientes['mi_mi'];
	$mi_IDunidad = $row_mis_metas_anteriores_pendientes['mi_IDunidad'];
	$mi_ponderacion = $row_mis_metas_anteriores_pendientes['mi_ponderacion'];
	$mi_IDindicador = $row_mis_metas_anteriores_pendientes['mi_IDindicador'];
	$mi_3 = $row_mis_metas_anteriores_pendientes['mi_3'];
	$mi_2 = $row_mis_metas_anteriores_pendientes['mi_2'];
	$mi_1 = $row_mis_metas_anteriores_pendientes['mi_1'];
	$estatus = 1;
	$fecha_captura = date("Y-m-d"); 
	$fecha_termino = $fecha;
	echo $mi_mi;

    $query1 = "INSERT into sed_individuales (IDempleado, mi_mi, mi_IDunidad, mi_ponderacion, mi_IDindicador, mi_3, mi_2, mi_1, estatus, fecha_captura, fecha_termino, IDperiodo) values 
	('$IDempleado', '$mi_mi', '$mi_IDunidad', '$mi_ponderacion', '$mi_IDindicador', '$mi_3', '$mi_2', '$mi_1', '$estatus','$fecha_captura', '$fecha_termino', '$IDperiodo')"; 
    $result1 = mysql_query($query1) or die(mysql_error());  

 } while ($row_mis_metas_anteriores_pendientes = mysql_fetch_assoc($mis_metas_anteriores_pendientes));

 header("Location: f_desemp_captura.php?info=8&IDperiodo=$IDperiodo&IDempleado=$IDempleado&id=$id");

} else {
	
// importar todo
$query_mis_metas_anteriores = "SELECT * FROM sed_individuales WHERE sed_individuales.IDempleado = '$el_usuario' AND sed_individuales.IDperiodo = '$IDperiodo_anterior'"; 
mysql_query("SET NAMES 'utf8'");
$mis_metas_anteriores = mysql_query($query_mis_metas_anteriores, $vacantes) or die(mysql_error());
$row_mis_metas_anteriores = mysql_fetch_assoc($mis_metas_anteriores);

do {
	$IDempleado = $row_mis_metas_anteriores['IDempleado'];
	$mi_mi = $row_mis_metas_anteriores['mi_mi'];
	$mi_IDunidad = $row_mis_metas_anteriores['mi_IDunidad'];
	$mi_ponderacion = $row_mis_metas_anteriores['mi_ponderacion'];
	$mi_IDindicador = $row_mis_metas_anteriores['mi_IDindicador'];
	$mi_3 = $row_mis_metas_anteriores['mi_3'];
	$mi_2 = $row_mis_metas_anteriores['mi_2'];
	$mi_1 = $row_mis_metas_anteriores['mi_1'];
	$estatus = 1;
	$fecha_captura = date("Y-m-d"); 
	$fecha_termino = $fecha;

    $query1 = "INSERT into sed_individuales (IDempleado, mi_mi, mi_IDunidad, mi_ponderacion, mi_IDindicador, mi_3, mi_2, mi_1, estatus, fecha_captura, fecha_termino, IDperiodo) values 
	('$IDempleado', '$mi_mi', '$mi_IDunidad', '$mi_ponderacion', '$mi_IDindicador', '$mi_3', '$mi_2', '$mi_1', '$estatus','$fecha_captura', '$fecha_termino', '$IDperiodo')"; 
    $result1 = mysql_query($query1) or die(mysql_error());  

 } while ($row_mis_metas_anteriores = mysql_fetch_assoc($mis_metas_anteriores));

 header("Location: f_desemp_captura.php?info=9&IDperiodo=$IDperiodo&IDempleado=$IDempleado&id=$id");
}
?>
