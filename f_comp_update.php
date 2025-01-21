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
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$IDllave = $row_usuario['IDllave'];
$IDpuesto = $row_usuario['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];

// autoevaluacion
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo1 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Avg(sed_competencias_resultados.comp8) AS Rcomp8, Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 3 GROUP BY sed_competencias_resultados.IDempleado"; 
$Total_tipo1 = mysql_query($query_Total_tipo1, $vacantes) or die(mysql_error());
$row_Total_tipo1 = mysql_fetch_assoc($Total_tipo1);
$totalRows_Total_tipo1 = mysql_num_rows($Total_tipo1);

// autoevaluacion
mysql_select_db($database_vacantes, $vacantes);
$query_tipo1 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 3";
$tipo1 = mysql_query($query_tipo1, $vacantes) or die(mysql_error());
$row_tipo1 = mysql_fetch_assoc($tipo1);
$totalRows_tipo1 = mysql_num_rows($tipo1);

// ver que tenga todo Tipo 1
if ($row_Total_tipo1['Resultados'] == $totalRows_tipo1) { $Completo_tipo1 = 1;} else {$Completo_tipo1 = 0;}

?>