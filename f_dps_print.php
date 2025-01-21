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

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $_GET['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$user = $_SESSION['kt_login_user'];
mysql_select_db($database_vacantes, $vacantes);
$query_puesto_1 = "SELECT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo, prod_llave.IDllaveJ, prod_llave.IDllave FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_llave.IDpuesto = vac_puestos.IDpuesto WHERE vac_puestos.IDpuesto = '$IDpuesto'";
mysql_query("SET NAMES 'utf8'");
$puesto_1 = mysql_query($query_puesto_1, $vacantes) or die(mysql_error());
$row_puesto_1 = mysql_fetch_assoc($puesto_1);
$totalRows_puesto_1 = mysql_num_rows($puesto_1);
$llave = $row_puesto_1['IDllave'];

mysql_select_db($database_vacantes, $vacantes);
$query_puesto_2 = "SELECT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.descrito, vac_puestos.IDdp_tipo, vac_puestos.IDarea, vac_areas.area, vac_puestos.tipo, prod_llave.IDllaveJ, prod_llave.IDllave FROM vac_puestos LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_llave.IDpuesto = vac_puestos.IDpuesto WHERE prod_llave.IDllaveJ = '$llave'"; 	
$puesto_2 = mysql_query($query_puesto_2, $vacantes) or die(mysql_error());
$row_puesto_2 = mysql_fetch_assoc($puesto_2);
$totalRows_puesto_2 = mysql_num_rows($puesto_2);

// activos
$query_puesto_catalogos1 = "SELECT * FROM sed_dps WHERE sed_dps.IDpuesto = '$IDpuesto'";
$puesto_catalogos1 = mysql_query($query_puesto_catalogos1, $vacantes) or die(mysql_error());
$row_puesto_catalogos1 = mysql_fetch_assoc($puesto_catalogos1);
$totalRows_puesto_catalogos1 = mysql_num_rows($puesto_catalogos1);

// activos
$query_puesto_catalogos2 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'c' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos2 = mysql_query($query_puesto_catalogos2, $vacantes) or die(mysql_error());
$row_puesto_catalogos2 = mysql_fetch_assoc($puesto_catalogos2);
$totalRows_puesto_catalogos2 = mysql_num_rows($puesto_catalogos2);

// activos
$query_puesto_catalogos3 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'd' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos3 = mysql_query($query_puesto_catalogos3, $vacantes) or die(mysql_error());
$row_puesto_catalogos3 = mysql_fetch_assoc($puesto_catalogos3);
$totalRows_puesto_catalogos3 = mysql_num_rows($puesto_catalogos3);

// activos
$query_puesto_catalogos4 = "SELECT * FROM sed_dps WHERE sed_dps.IDpuesto = '$IDpuesto'";
$puesto_catalogos4 = mysql_query($query_puesto_catalogos4, $vacantes) or die(mysql_error());
$row_puesto_catalogos4 = mysql_fetch_assoc($puesto_catalogos4);
$totalRows_puesto_catalogos4 = mysql_num_rows($puesto_catalogos4);

// activos
$query_puesto_catalogos5 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'h' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos5 = mysql_query($query_puesto_catalogos5, $vacantes) or die(mysql_error());
$row_puesto_catalogos5 = mysql_fetch_assoc($puesto_catalogos5);
$totalRows_puesto_catalogos5 = mysql_num_rows($puesto_catalogos5);

// activos
$query_puesto_catalogos6 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'm' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos6 = mysql_query($query_puesto_catalogos6, $vacantes) or die(mysql_error());
$row_puesto_catalogos6 = mysql_fetch_assoc($puesto_catalogos6);
$totalRows_puesto_catalogos6 = mysql_num_rows($puesto_catalogos6);

// activos
$query_puesto_catalogos7 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'h' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos7 = mysql_query($query_puesto_catalogos7, $vacantes) or die(mysql_error());
$row_puesto_catalogos7 = mysql_fetch_assoc($puesto_catalogos7);
$totalRows_puesto_catalogos7 = mysql_num_rows($puesto_catalogos7);

// activos
$query_puesto_catalogos8 = "SELECT * FROM sed_dps_catalogos WHERE sed_dps_catalogos.criterio = 'z' AND sed_dps_catalogos.IDpuesto = '$IDpuesto'";
$puesto_catalogos8 = mysql_query($query_puesto_catalogos8, $vacantes) or die(mysql_error());
$row_puesto_catalogos8 = mysql_fetch_assoc($puesto_catalogos8);
$totalRows_puesto_catalogos8 = mysql_num_rows($puesto_catalogos8);

?>