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

$currentPage = $_SERVER["PHP_SELF"];

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

$colname_usuario = "-1";

if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $_GET['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

$el_anio = $_GET['anio'];
//$el_mes = $_GET['mes'];
//$IDfecha = $_GET['IDfecha'];


$query_reporte = "SELECT inc_vacaciones.IDempleado, inc_vacaciones.fecha_inicio, inc_vacaciones.fecha_fin, inc_vacaciones.IDperiodo, inc_vacaciones.IDdias_asignados, inc_vacaciones.IDmatriz, inc_vacaciones.IDpuesto, inc_vacaciones.IDarea, inc_vacaciones.IDsucursal, inc_vacaciones.denominacion, vac_areas.area, vac_matriz.matriz  FROM inc_vacaciones LEFT JOIN vac_areas ON inc_vacaciones.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON inc_vacaciones.IDmatriz = vac_matriz.IDmatriz WHERE inc_vacaciones.IDmatriz = $IDmatriz AND inc_vacaciones.anio = $el_anio";
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());
$row_reporte = mysql_fetch_assoc($reporte);



require_once 'assets/PHPExcel.php';
set_time_limit(0);

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("ANUAL/vacaciones.xlsx");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $rowCount = 4; //new

    while($row_reporte = mysql_fetch_array($reporte)){ 
	
$IDempleado = $row_reporte['IDempleado'];

$query_reporteA = "SELECT
SUM(case WHEN ('$el_anio-01-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene1,
SUM(case WHEN ('$el_anio-01-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene2,
SUM(case WHEN ('$el_anio-01-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene3,
SUM(case WHEN ('$el_anio-01-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene4,
SUM(case WHEN ('$el_anio-01-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene5,
SUM(case WHEN ('$el_anio-01-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene6,
SUM(case WHEN ('$el_anio-01-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene7,
SUM(case WHEN ('$el_anio-01-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene8,
SUM(case WHEN ('$el_anio-01-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene9,
SUM(case WHEN ('$el_anio-01-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene10,
SUM(case WHEN ('$el_anio-01-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene11,
SUM(case WHEN ('$el_anio-01-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene12,
SUM(case WHEN ('$el_anio-01-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene13,
SUM(case WHEN ('$el_anio-01-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene14,
SUM(case WHEN ('$el_anio-01-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene15,
SUM(case WHEN ('$el_anio-01-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene16,
SUM(case WHEN ('$el_anio-01-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene17,
SUM(case WHEN ('$el_anio-01-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene18,
SUM(case WHEN ('$el_anio-01-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene19,
SUM(case WHEN ('$el_anio-01-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene20,
SUM(case WHEN ('$el_anio-01-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene21,
SUM(case WHEN ('$el_anio-01-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene22,
SUM(case WHEN ('$el_anio-01-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene23,
SUM(case WHEN ('$el_anio-01-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene24,
SUM(case WHEN ('$el_anio-01-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene25,
SUM(case WHEN ('$el_anio-01-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene26,
SUM(case WHEN ('$el_anio-01-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene27,
SUM(case WHEN ('$el_anio-01-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene28,
SUM(case WHEN ('$el_anio-01-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene29,
SUM(case WHEN ('$el_anio-01-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene30,
SUM(case WHEN ('$el_anio-01-31' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aene31,
SUM(case WHEN ('$el_anio-02-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb1,
SUM(case WHEN ('$el_anio-02-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb2,
SUM(case WHEN ('$el_anio-02-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb3,
SUM(case WHEN ('$el_anio-02-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb4,
SUM(case WHEN ('$el_anio-02-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb5,
SUM(case WHEN ('$el_anio-02-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb6,
SUM(case WHEN ('$el_anio-02-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb7,
SUM(case WHEN ('$el_anio-02-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb8,
SUM(case WHEN ('$el_anio-02-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb9,
SUM(case WHEN ('$el_anio-02-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb10,
SUM(case WHEN ('$el_anio-02-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb11,
SUM(case WHEN ('$el_anio-02-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb12,
SUM(case WHEN ('$el_anio-02-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb13,
SUM(case WHEN ('$el_anio-02-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb14,
SUM(case WHEN ('$el_anio-02-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb15,
SUM(case WHEN ('$el_anio-02-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb16,
SUM(case WHEN ('$el_anio-02-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb17,
SUM(case WHEN ('$el_anio-02-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb18,
SUM(case WHEN ('$el_anio-02-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb19,
SUM(case WHEN ('$el_anio-02-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb20,
SUM(case WHEN ('$el_anio-02-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb21,
SUM(case WHEN ('$el_anio-02-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb22,
SUM(case WHEN ('$el_anio-02-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb23,
SUM(case WHEN ('$el_anio-02-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb24,
SUM(case WHEN ('$el_anio-02-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb25,
SUM(case WHEN ('$el_anio-02-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb26,
SUM(case WHEN ('$el_anio-02-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb27,
SUM(case WHEN ('$el_anio-02-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Afeb28,
SUM(case WHEN ('$el_anio-03-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar1,
SUM(case WHEN ('$el_anio-03-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar2,
SUM(case WHEN ('$el_anio-03-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar3,
SUM(case WHEN ('$el_anio-03-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar4,
SUM(case WHEN ('$el_anio-03-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar5,
SUM(case WHEN ('$el_anio-03-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar6,
SUM(case WHEN ('$el_anio-03-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar7,
SUM(case WHEN ('$el_anio-03-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar8,
SUM(case WHEN ('$el_anio-03-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar9,
SUM(case WHEN ('$el_anio-03-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar10,
SUM(case WHEN ('$el_anio-03-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar11,
SUM(case WHEN ('$el_anio-03-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar12,
SUM(case WHEN ('$el_anio-03-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar13,
SUM(case WHEN ('$el_anio-03-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar14,
SUM(case WHEN ('$el_anio-03-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar15,
SUM(case WHEN ('$el_anio-03-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar16,
SUM(case WHEN ('$el_anio-03-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar17,
SUM(case WHEN ('$el_anio-03-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar18,
SUM(case WHEN ('$el_anio-03-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar19,
SUM(case WHEN ('$el_anio-03-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar20,
SUM(case WHEN ('$el_anio-03-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar21,
SUM(case WHEN ('$el_anio-03-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar22,
SUM(case WHEN ('$el_anio-03-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar23,
SUM(case WHEN ('$el_anio-03-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar24,
SUM(case WHEN ('$el_anio-03-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar25,
SUM(case WHEN ('$el_anio-03-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar26,
SUM(case WHEN ('$el_anio-03-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar27,
SUM(case WHEN ('$el_anio-03-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar28,
SUM(case WHEN ('$el_anio-03-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar29,
SUM(case WHEN ('$el_anio-03-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar30,
SUM(case WHEN ('$el_anio-03-31' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amar31,
SUM(case WHEN ('$el_anio-04-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr1,
SUM(case WHEN ('$el_anio-04-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr2,
SUM(case WHEN ('$el_anio-04-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr3,
SUM(case WHEN ('$el_anio-04-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr4,
SUM(case WHEN ('$el_anio-04-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr5,
SUM(case WHEN ('$el_anio-04-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr6,
SUM(case WHEN ('$el_anio-04-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr7,
SUM(case WHEN ('$el_anio-04-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr8,
SUM(case WHEN ('$el_anio-04-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr9,
SUM(case WHEN ('$el_anio-04-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr10,
SUM(case WHEN ('$el_anio-04-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr11,
SUM(case WHEN ('$el_anio-04-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr12,
SUM(case WHEN ('$el_anio-04-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr13,
SUM(case WHEN ('$el_anio-04-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr14,
SUM(case WHEN ('$el_anio-04-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr15,
SUM(case WHEN ('$el_anio-04-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr16,
SUM(case WHEN ('$el_anio-04-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr17,
SUM(case WHEN ('$el_anio-04-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr18,
SUM(case WHEN ('$el_anio-04-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr19,
SUM(case WHEN ('$el_anio-04-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr20,
SUM(case WHEN ('$el_anio-04-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr21,
SUM(case WHEN ('$el_anio-04-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr22,
SUM(case WHEN ('$el_anio-04-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr23,
SUM(case WHEN ('$el_anio-04-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr24,
SUM(case WHEN ('$el_anio-04-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr25,
SUM(case WHEN ('$el_anio-04-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr26,
SUM(case WHEN ('$el_anio-04-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr27,
SUM(case WHEN ('$el_anio-04-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr28,
SUM(case WHEN ('$el_anio-04-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr29,
SUM(case WHEN ('$el_anio-04-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aabr30,
SUM(case WHEN ('$el_anio-05-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay1,
SUM(case WHEN ('$el_anio-05-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay2,
SUM(case WHEN ('$el_anio-05-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay3,
SUM(case WHEN ('$el_anio-05-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay4,
SUM(case WHEN ('$el_anio-05-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay5,
SUM(case WHEN ('$el_anio-05-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay6,
SUM(case WHEN ('$el_anio-05-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay7,
SUM(case WHEN ('$el_anio-05-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay8,
SUM(case WHEN ('$el_anio-05-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay9,
SUM(case WHEN ('$el_anio-05-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay10,
SUM(case WHEN ('$el_anio-05-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay11,
SUM(case WHEN ('$el_anio-05-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay12,
SUM(case WHEN ('$el_anio-05-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay13,
SUM(case WHEN ('$el_anio-05-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay14,
SUM(case WHEN ('$el_anio-05-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay15,
SUM(case WHEN ('$el_anio-05-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay16,
SUM(case WHEN ('$el_anio-05-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay17,
SUM(case WHEN ('$el_anio-05-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay18,
SUM(case WHEN ('$el_anio-05-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay19,
SUM(case WHEN ('$el_anio-05-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay20,
SUM(case WHEN ('$el_anio-05-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay21,
SUM(case WHEN ('$el_anio-05-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay22,
SUM(case WHEN ('$el_anio-05-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay23,
SUM(case WHEN ('$el_anio-05-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay24,
SUM(case WHEN ('$el_anio-05-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay25,
SUM(case WHEN ('$el_anio-05-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay26,
SUM(case WHEN ('$el_anio-05-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay27,
SUM(case WHEN ('$el_anio-05-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay28,
SUM(case WHEN ('$el_anio-05-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay29,
SUM(case WHEN ('$el_anio-05-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay30,
SUM(case WHEN ('$el_anio-05-31' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Amay31,
SUM(case WHEN ('$el_anio-06-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun1,
SUM(case WHEN ('$el_anio-06-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun2,
SUM(case WHEN ('$el_anio-06-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun3,
SUM(case WHEN ('$el_anio-06-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun4,
SUM(case WHEN ('$el_anio-06-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun5,
SUM(case WHEN ('$el_anio-06-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun6,
SUM(case WHEN ('$el_anio-06-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun7,
SUM(case WHEN ('$el_anio-06-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun8,
SUM(case WHEN ('$el_anio-06-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun9,
SUM(case WHEN ('$el_anio-06-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun10,
SUM(case WHEN ('$el_anio-06-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun11,
SUM(case WHEN ('$el_anio-06-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun12,
SUM(case WHEN ('$el_anio-06-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun13,
SUM(case WHEN ('$el_anio-06-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun14,
SUM(case WHEN ('$el_anio-06-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun15,
SUM(case WHEN ('$el_anio-06-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun16,
SUM(case WHEN ('$el_anio-06-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun17,
SUM(case WHEN ('$el_anio-06-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun18,
SUM(case WHEN ('$el_anio-06-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun19,
SUM(case WHEN ('$el_anio-06-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun20,
SUM(case WHEN ('$el_anio-06-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun21,
SUM(case WHEN ('$el_anio-06-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun22,
SUM(case WHEN ('$el_anio-06-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun23,
SUM(case WHEN ('$el_anio-06-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun24,
SUM(case WHEN ('$el_anio-06-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun25,
SUM(case WHEN ('$el_anio-06-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun26,
SUM(case WHEN ('$el_anio-06-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun27,
SUM(case WHEN ('$el_anio-06-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun28,
SUM(case WHEN ('$el_anio-06-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun29,
SUM(case WHEN ('$el_anio-06-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajun30,
SUM(case WHEN ('$el_anio-07-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul1,
SUM(case WHEN ('$el_anio-07-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul2,
SUM(case WHEN ('$el_anio-07-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul3,
SUM(case WHEN ('$el_anio-07-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul4,
SUM(case WHEN ('$el_anio-07-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul5,
SUM(case WHEN ('$el_anio-07-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul6,
SUM(case WHEN ('$el_anio-07-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul7,
SUM(case WHEN ('$el_anio-07-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul8,
SUM(case WHEN ('$el_anio-07-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul9,
SUM(case WHEN ('$el_anio-07-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul10,
SUM(case WHEN ('$el_anio-07-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul11,
SUM(case WHEN ('$el_anio-07-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul12,
SUM(case WHEN ('$el_anio-07-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul13,
SUM(case WHEN ('$el_anio-07-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul14,
SUM(case WHEN ('$el_anio-07-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul15,
SUM(case WHEN ('$el_anio-07-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul16,
SUM(case WHEN ('$el_anio-07-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul17,
SUM(case WHEN ('$el_anio-07-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul18,
SUM(case WHEN ('$el_anio-07-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul19,
SUM(case WHEN ('$el_anio-07-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul20,
SUM(case WHEN ('$el_anio-07-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul21,
SUM(case WHEN ('$el_anio-07-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul22,
SUM(case WHEN ('$el_anio-07-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul23,
SUM(case WHEN ('$el_anio-07-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul24,
SUM(case WHEN ('$el_anio-07-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul25,
SUM(case WHEN ('$el_anio-07-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul26,
SUM(case WHEN ('$el_anio-07-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul27,
SUM(case WHEN ('$el_anio-07-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul28,
SUM(case WHEN ('$el_anio-07-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul29,
SUM(case WHEN ('$el_anio-07-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul30,
SUM(case WHEN ('$el_anio-07-31' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Ajul31,
SUM(case WHEN ('$el_anio-08-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago1,
SUM(case WHEN ('$el_anio-08-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago2,
SUM(case WHEN ('$el_anio-08-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago3,
SUM(case WHEN ('$el_anio-08-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago4,
SUM(case WHEN ('$el_anio-08-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago5,
SUM(case WHEN ('$el_anio-08-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago6,
SUM(case WHEN ('$el_anio-08-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago7,
SUM(case WHEN ('$el_anio-08-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago8,
SUM(case WHEN ('$el_anio-08-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago9,
SUM(case WHEN ('$el_anio-08-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago10,
SUM(case WHEN ('$el_anio-08-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago11,
SUM(case WHEN ('$el_anio-08-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago12,
SUM(case WHEN ('$el_anio-08-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago13,
SUM(case WHEN ('$el_anio-08-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago14,
SUM(case WHEN ('$el_anio-08-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago15,
SUM(case WHEN ('$el_anio-08-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago16,
SUM(case WHEN ('$el_anio-08-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago17,
SUM(case WHEN ('$el_anio-08-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago18,
SUM(case WHEN ('$el_anio-08-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago19,
SUM(case WHEN ('$el_anio-08-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago20,
SUM(case WHEN ('$el_anio-08-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago21,
SUM(case WHEN ('$el_anio-08-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago22,
SUM(case WHEN ('$el_anio-08-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago23,
SUM(case WHEN ('$el_anio-08-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago24,
SUM(case WHEN ('$el_anio-08-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago25,
SUM(case WHEN ('$el_anio-08-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago26,
SUM(case WHEN ('$el_anio-08-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago27,
SUM(case WHEN ('$el_anio-08-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago28,
SUM(case WHEN ('$el_anio-08-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago29,
SUM(case WHEN ('$el_anio-08-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago30,
SUM(case WHEN ('$el_anio-08-31' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aago31,
SUM(case WHEN ('$el_anio-09-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep1,
SUM(case WHEN ('$el_anio-09-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep2,
SUM(case WHEN ('$el_anio-09-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep3,
SUM(case WHEN ('$el_anio-09-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep4,
SUM(case WHEN ('$el_anio-09-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep5,
SUM(case WHEN ('$el_anio-09-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep6,
SUM(case WHEN ('$el_anio-09-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep7,
SUM(case WHEN ('$el_anio-09-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep8,
SUM(case WHEN ('$el_anio-09-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep9,
SUM(case WHEN ('$el_anio-09-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep10,
SUM(case WHEN ('$el_anio-09-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep11,
SUM(case WHEN ('$el_anio-09-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep12,
SUM(case WHEN ('$el_anio-09-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep13,
SUM(case WHEN ('$el_anio-09-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep14,
SUM(case WHEN ('$el_anio-09-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep15,
SUM(case WHEN ('$el_anio-09-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep16,
SUM(case WHEN ('$el_anio-09-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep17,
SUM(case WHEN ('$el_anio-09-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep18,
SUM(case WHEN ('$el_anio-09-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep19,
SUM(case WHEN ('$el_anio-09-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep20,
SUM(case WHEN ('$el_anio-09-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep21,
SUM(case WHEN ('$el_anio-09-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep22,
SUM(case WHEN ('$el_anio-09-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep23,
SUM(case WHEN ('$el_anio-09-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep24,
SUM(case WHEN ('$el_anio-09-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep25,
SUM(case WHEN ('$el_anio-09-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep26,
SUM(case WHEN ('$el_anio-09-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep27,
SUM(case WHEN ('$el_anio-09-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep28,
SUM(case WHEN ('$el_anio-09-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep29,
SUM(case WHEN ('$el_anio-09-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Asep30,
SUM(case WHEN ('$el_anio-10-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct1,
SUM(case WHEN ('$el_anio-10-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct2,
SUM(case WHEN ('$el_anio-10-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct3,
SUM(case WHEN ('$el_anio-10-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct4,
SUM(case WHEN ('$el_anio-10-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct5,
SUM(case WHEN ('$el_anio-10-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct6,
SUM(case WHEN ('$el_anio-10-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct7,
SUM(case WHEN ('$el_anio-10-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct8,
SUM(case WHEN ('$el_anio-10-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct9,
SUM(case WHEN ('$el_anio-10-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct10,
SUM(case WHEN ('$el_anio-10-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct11,
SUM(case WHEN ('$el_anio-10-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct12,
SUM(case WHEN ('$el_anio-10-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct13,
SUM(case WHEN ('$el_anio-10-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct14,
SUM(case WHEN ('$el_anio-10-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct15,
SUM(case WHEN ('$el_anio-10-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct16,
SUM(case WHEN ('$el_anio-10-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct17,
SUM(case WHEN ('$el_anio-10-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct18,
SUM(case WHEN ('$el_anio-10-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct19,
SUM(case WHEN ('$el_anio-10-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct20,
SUM(case WHEN ('$el_anio-10-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct21,
SUM(case WHEN ('$el_anio-10-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct22,
SUM(case WHEN ('$el_anio-10-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct23,
SUM(case WHEN ('$el_anio-10-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct24,
SUM(case WHEN ('$el_anio-10-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct25,
SUM(case WHEN ('$el_anio-10-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct26,
SUM(case WHEN ('$el_anio-10-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct27,
SUM(case WHEN ('$el_anio-10-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct28,
SUM(case WHEN ('$el_anio-10-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct29,
SUM(case WHEN ('$el_anio-10-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct30,
SUM(case WHEN ('$el_anio-10-31' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Aoct31,
SUM(case WHEN ('$el_anio-11-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov1,
SUM(case WHEN ('$el_anio-11-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov2,
SUM(case WHEN ('$el_anio-11-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov3,
SUM(case WHEN ('$el_anio-11-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov4,
SUM(case WHEN ('$el_anio-11-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov5,
SUM(case WHEN ('$el_anio-11-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov6,
SUM(case WHEN ('$el_anio-11-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov7,
SUM(case WHEN ('$el_anio-11-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov8,
SUM(case WHEN ('$el_anio-11-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov9,
SUM(case WHEN ('$el_anio-11-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov10,
SUM(case WHEN ('$el_anio-11-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov11,
SUM(case WHEN ('$el_anio-11-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov12,
SUM(case WHEN ('$el_anio-11-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov13,
SUM(case WHEN ('$el_anio-11-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov14,
SUM(case WHEN ('$el_anio-11-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov15,
SUM(case WHEN ('$el_anio-11-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov16,
SUM(case WHEN ('$el_anio-11-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov17,
SUM(case WHEN ('$el_anio-11-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov18,
SUM(case WHEN ('$el_anio-11-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov19,
SUM(case WHEN ('$el_anio-11-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov20,
SUM(case WHEN ('$el_anio-11-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov21,
SUM(case WHEN ('$el_anio-11-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov22,
SUM(case WHEN ('$el_anio-11-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov23,
SUM(case WHEN ('$el_anio-11-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov24,
SUM(case WHEN ('$el_anio-11-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov25,
SUM(case WHEN ('$el_anio-11-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov26,
SUM(case WHEN ('$el_anio-11-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov27,
SUM(case WHEN ('$el_anio-11-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov28,
SUM(case WHEN ('$el_anio-11-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov29,
SUM(case WHEN ('$el_anio-11-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Anov30,
SUM(case WHEN ('$el_anio-12-01' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic1,
SUM(case WHEN ('$el_anio-12-02' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic2,
SUM(case WHEN ('$el_anio-12-03' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic3,
SUM(case WHEN ('$el_anio-12-04' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic4,
SUM(case WHEN ('$el_anio-12-05' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic5,
SUM(case WHEN ('$el_anio-12-06' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic6,
SUM(case WHEN ('$el_anio-12-07' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic7,
SUM(case WHEN ('$el_anio-12-08' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic8,
SUM(case WHEN ('$el_anio-12-09' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic9,
SUM(case WHEN ('$el_anio-12-10' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic10,
SUM(case WHEN ('$el_anio-12-11' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic11,
SUM(case WHEN ('$el_anio-12-12' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic12,
SUM(case WHEN ('$el_anio-12-13' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic13,
SUM(case WHEN ('$el_anio-12-14' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic14,
SUM(case WHEN ('$el_anio-12-15' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic15,
SUM(case WHEN ('$el_anio-12-16' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic16,
SUM(case WHEN ('$el_anio-12-17' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic17,
SUM(case WHEN ('$el_anio-12-18' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic18,
SUM(case WHEN ('$el_anio-12-19' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic19,
SUM(case WHEN ('$el_anio-12-20' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic20,
SUM(case WHEN ('$el_anio-12-21' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic21,
SUM(case WHEN ('$el_anio-12-22' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic22,
SUM(case WHEN ('$el_anio-12-23' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic23,
SUM(case WHEN ('$el_anio-12-24' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic24,
SUM(case WHEN ('$el_anio-12-25' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic25,
SUM(case WHEN ('$el_anio-12-26' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic26,
SUM(case WHEN ('$el_anio-12-27' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic27,
SUM(case WHEN ('$el_anio-12-28' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic28,
SUM(case WHEN ('$el_anio-12-29' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic29,
SUM(case WHEN ('$el_anio-12-30' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic30,
SUM(case WHEN ('$el_anio-12-31' BETWEEN fecha_inicio AND fecha_fin AND inc_vacaciones.IDarea IN (1,2,3,4,5,6,11)) then 1 else 0 end) AS Adic31
FROM inc_vacaciones WHERE inc_vacaciones.IDempleado  = $IDempleado"; 
$reporteA = mysql_query($query_reporteA, $vacantes) or die(mysql_error());
$row_reporteA = mysql_fetch_assoc($reporteA);

        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row_reporte['matriz']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row_reporte['area']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row_reporte['denominacion']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row_reporteA['Aene1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row_reporteA['Aene2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row_reporteA['Aene3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row_reporteA['Aene4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row_reporteA['Aene5']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row_reporteA['Aene6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row_reporteA['Aene7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row_reporteA['Aene9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row_reporteA['Aene10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row_reporteA['Aene11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $row_reporteA['Aene12']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $row_reporteA['Aene13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $row_reporteA['Aene14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $row_reporteA['Aene16']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $row_reporteA['Aene17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $row_reporteA['Aene18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $row_reporteA['Aene19']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $row_reporteA['Aene20']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $row_reporteA['Aene21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $row_reporteA['Aene23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $row_reporteA['Aene24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $row_reporteA['Aene25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowCount, $row_reporteA['Aene26']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowCount, $row_reporteA['Aene27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AF'.$rowCount, $row_reporteA['Aene28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, $row_reporteA['Aene30']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, $row_reporteA['Aene31']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, $row_reporteA['Afeb1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, $row_reporteA['Afeb2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, $row_reporteA['Afeb3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AM'.$rowCount, $row_reporteA['Afeb4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AN'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AO'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AP'.$rowCount, $row_reporteA['Afeb7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AQ'.$rowCount, $row_reporteA['Afeb8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AR'.$rowCount, $row_reporteA['Afeb9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AS'.$rowCount, $row_reporteA['Afeb10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AT'.$rowCount, $row_reporteA['Afeb11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AU'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AV'.$rowCount, $row_reporteA['Afeb13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AW'.$rowCount, $row_reporteA['Afeb14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AX'.$rowCount, $row_reporteA['Afeb15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AY'.$rowCount, $row_reporteA['Afeb16']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('AZ'.$rowCount, $row_reporteA['Afeb17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BA'.$rowCount, $row_reporteA['Afeb18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BB'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BC'.$rowCount, $row_reporteA['Afeb20']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BD'.$rowCount, $row_reporteA['Afeb21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BE'.$rowCount, $row_reporteA['Afeb22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BF'.$rowCount, $row_reporteA['Afeb23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BG'.$rowCount, $row_reporteA['Afeb24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BH'.$rowCount, $row_reporteA['Afeb25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BI'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BJ'.$rowCount, $row_reporteA['Afeb27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BK'.$rowCount, $row_reporteA['Afeb28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BL'.$rowCount, $row_reporteA['Amar1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BM'.$rowCount, $row_reporteA['Amar2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BN'.$rowCount, $row_reporteA['Amar3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BO'.$rowCount, $row_reporteA['Amar4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BP'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BQ'.$rowCount, $row_reporteA['Amar6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BR'.$rowCount, $row_reporteA['Amar7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BS'.$rowCount, $row_reporteA['Amar8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BT'.$rowCount, $row_reporteA['Amar9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BU'.$rowCount, $row_reporteA['Amar10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BV'.$rowCount, $row_reporteA['Amar11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BW'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BX'.$rowCount, $row_reporteA['Amar13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BY'.$rowCount, $row_reporteA['Amar14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('BZ'.$rowCount, $row_reporteA['Amar15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CA'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CB'.$rowCount, $row_reporteA['Amar17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CC'.$rowCount, $row_reporteA['Amar18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CD'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CE'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CF'.$rowCount, $row_reporteA['Amar21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CG'.$rowCount, $row_reporteA['Amar22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CH'.$rowCount, $row_reporteA['Amar23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CI'.$rowCount, $row_reporteA['Amar24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CJ'.$rowCount, $row_reporteA['Amar25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CK'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CL'.$rowCount, $row_reporteA['Amar27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CM'.$rowCount, $row_reporteA['Amar28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CN'.$rowCount, $row_reporteA['Amar29']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CO'.$rowCount, $row_reporteA['Amar30']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CP'.$rowCount, $row_reporteA['Amar31']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CQ'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CR'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CS'.$rowCount, $row_reporteA['Aabr3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CT'.$rowCount, $row_reporteA['Aabr4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CU'.$rowCount, $row_reporteA['Aabr5']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CV'.$rowCount, $row_reporteA['Aabr6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CW'.$rowCount, $row_reporteA['Aabr7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CX'.$rowCount, $row_reporteA['Aabr8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CY'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('CZ'.$rowCount, $row_reporteA['Aabr10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DA'.$rowCount, $row_reporteA['Aabr11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DB'.$rowCount, $row_reporteA['Aabr12']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DC'.$rowCount, $row_reporteA['Aabr13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DD'.$rowCount, $row_reporteA['Aabr14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DE'.$rowCount, $row_reporteA['Aabr15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DF'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DG'.$rowCount, $row_reporteA['Aabr17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DH'.$rowCount, $row_reporteA['Aabr18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DI'.$rowCount, $row_reporteA['Aabr19']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DJ'.$rowCount, $row_reporteA['Aabr20']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DK'.$rowCount, $row_reporteA['Aabr21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DL'.$rowCount, $row_reporteA['Aabr22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DM'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DN'.$rowCount, $row_reporteA['Aabr24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DO'.$rowCount, $row_reporteA['Aabr25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DP'.$rowCount, $row_reporteA['Aabr26']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DQ'.$rowCount, $row_reporteA['Aabr27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DR'.$rowCount, $row_reporteA['Aabr28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DS'.$rowCount, $row_reporteA['Aabr29']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DT'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DU'.$rowCount, $row_reporteA['Amay1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DV'.$rowCount, $row_reporteA['Amay2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DW'.$rowCount, $row_reporteA['Amay3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DX'.$rowCount, $row_reporteA['Amay4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DY'.$rowCount, $row_reporteA['Amay5']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('DZ'.$rowCount, $row_reporteA['Amay6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EA'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EB'.$rowCount, $row_reporteA['Amay8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EC'.$rowCount, $row_reporteA['Amay9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('ED'.$rowCount, $row_reporteA['Amay10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EE'.$rowCount, $row_reporteA['Amay11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EF'.$rowCount, $row_reporteA['Amay12']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EG'.$rowCount, $row_reporteA['Amay13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EH'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EI'.$rowCount, $row_reporteA['Amay15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EJ'.$rowCount, $row_reporteA['Amay16']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EK'.$rowCount, $row_reporteA['Amay17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EL'.$rowCount, $row_reporteA['Amay18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EM'.$rowCount, $row_reporteA['Amay19']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EN'.$rowCount, $row_reporteA['Amay20']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EO'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EP'.$rowCount, $row_reporteA['Amay22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EQ'.$rowCount, $row_reporteA['Amay23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('ER'.$rowCount, $row_reporteA['Amay24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('ES'.$rowCount, $row_reporteA['Amay25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('ET'.$rowCount, $row_reporteA['Amay26']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EU'.$rowCount, $row_reporteA['Amay27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EV'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EW'.$rowCount, $row_reporteA['Amay29']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EX'.$rowCount, $row_reporteA['Amay30']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EY'.$rowCount, $row_reporteA['Amay31']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('EZ'.$rowCount, $row_reporteA['Ajun1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FA'.$rowCount, $row_reporteA['Ajun2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FB'.$rowCount, $row_reporteA['Ajun3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FC'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FD'.$rowCount, $row_reporteA['Ajun5']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FE'.$rowCount, $row_reporteA['Ajun6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FF'.$rowCount, $row_reporteA['Ajun7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FG'.$rowCount, $row_reporteA['Ajun8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FH'.$rowCount, $row_reporteA['Ajun9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FI'.$rowCount, $row_reporteA['Ajun10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FJ'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FK'.$rowCount, $row_reporteA['Ajun12']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FL'.$rowCount, $row_reporteA['Ajun13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FM'.$rowCount, $row_reporteA['Ajun14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FN'.$rowCount, $row_reporteA['Ajun15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FO'.$rowCount, $row_reporteA['Ajun16']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FP'.$rowCount, $row_reporteA['Ajun17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FQ'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FR'.$rowCount, $row_reporteA['Ajun19']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FS'.$rowCount, $row_reporteA['Ajun20']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FT'.$rowCount, $row_reporteA['Ajun21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FU'.$rowCount, $row_reporteA['Ajun22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FV'.$rowCount, $row_reporteA['Ajun23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FW'.$rowCount, $row_reporteA['Ajun24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FX'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FY'.$rowCount, $row_reporteA['Ajun26']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('FZ'.$rowCount, $row_reporteA['Ajun27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GA'.$rowCount, $row_reporteA['Ajun28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GB'.$rowCount, $row_reporteA['Ajun29']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GC'.$rowCount, $row_reporteA['Ajun30']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GD'.$rowCount, $row_reporteA['Ajul1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GE'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GF'.$rowCount, $row_reporteA['Ajul3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GG'.$rowCount, $row_reporteA['Ajul4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GH'.$rowCount, $row_reporteA['Ajul5']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GI'.$rowCount, $row_reporteA['Ajul6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GJ'.$rowCount, $row_reporteA['Ajul7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GK'.$rowCount, $row_reporteA['Ajul8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GL'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GM'.$rowCount, $row_reporteA['Ajul10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GN'.$rowCount, $row_reporteA['Ajul11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GO'.$rowCount, $row_reporteA['Ajul12']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GP'.$rowCount, $row_reporteA['Ajul13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GQ'.$rowCount, $row_reporteA['Ajul14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GR'.$rowCount, $row_reporteA['Ajul15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GS'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GT'.$rowCount, $row_reporteA['Ajul17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GU'.$rowCount, $row_reporteA['Ajul18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GV'.$rowCount, $row_reporteA['Ajul19']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GW'.$rowCount, $row_reporteA['Ajul20']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GX'.$rowCount, $row_reporteA['Ajul21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GY'.$rowCount, $row_reporteA['Ajul22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('GZ'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HA'.$rowCount, $row_reporteA['Ajul24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HB'.$rowCount, $row_reporteA['Ajul25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HC'.$rowCount, $row_reporteA['Ajul26']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HD'.$rowCount, $row_reporteA['Ajul27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HE'.$rowCount, $row_reporteA['Ajul28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HF'.$rowCount, $row_reporteA['Ajul29']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HG'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HH'.$rowCount, $row_reporteA['Ajul31']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HI'.$rowCount, $row_reporteA['Aago1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HJ'.$rowCount, $row_reporteA['Aago2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HK'.$rowCount, $row_reporteA['Aago3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HL'.$rowCount, $row_reporteA['Aago4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HM'.$rowCount, $row_reporteA['Aago5']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HN'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HO'.$rowCount, $row_reporteA['Aago7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HP'.$rowCount, $row_reporteA['Aago8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HQ'.$rowCount, $row_reporteA['Aago9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HR'.$rowCount, $row_reporteA['Aago10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HS'.$rowCount, $row_reporteA['Aago11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HT'.$rowCount, $row_reporteA['Aago12']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HU'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HV'.$rowCount, $row_reporteA['Aago14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HW'.$rowCount, $row_reporteA['Aago15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HX'.$rowCount, $row_reporteA['Aago16']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HY'.$rowCount, $row_reporteA['Aago17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('HZ'.$rowCount, $row_reporteA['Aago18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IA'.$rowCount, $row_reporteA['Aago19']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IB'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IC'.$rowCount, $row_reporteA['Aago21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('ID'.$rowCount, $row_reporteA['Aago22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IE'.$rowCount, $row_reporteA['Aago23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IF'.$rowCount, $row_reporteA['Aago24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IG'.$rowCount, $row_reporteA['Aago25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IH'.$rowCount, $row_reporteA['Aago26']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('II'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IJ'.$rowCount, $row_reporteA['Aago28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IK'.$rowCount, $row_reporteA['Aago29']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IL'.$rowCount, $row_reporteA['Aago30']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IM'.$rowCount, $row_reporteA['Aago31']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IN'.$rowCount, $row_reporteA['Asep1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IO'.$rowCount, $row_reporteA['Asep2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IP'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IQ'.$rowCount, $row_reporteA['Asep4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IR'.$rowCount, $row_reporteA['Asep5']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IS'.$rowCount, $row_reporteA['Asep6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IT'.$rowCount, $row_reporteA['Asep7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IU'.$rowCount, $row_reporteA['Asep8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IV'.$rowCount, $row_reporteA['Asep9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IW'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IX'.$rowCount, $row_reporteA['Asep11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IY'.$rowCount, $row_reporteA['Asep12']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('IZ'.$rowCount, $row_reporteA['Asep13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JA'.$rowCount, $row_reporteA['Asep14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JB'.$rowCount, $row_reporteA['Asep15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JC'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JD'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JE'.$rowCount, $row_reporteA['Asep18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JF'.$rowCount, $row_reporteA['Asep19']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JG'.$rowCount, $row_reporteA['Asep20']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JH'.$rowCount, $row_reporteA['Asep21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JI'.$rowCount, $row_reporteA['Asep22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JJ'.$rowCount, $row_reporteA['Asep23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JK'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JL'.$rowCount, $row_reporteA['Asep25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JM'.$rowCount, $row_reporteA['Asep26']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JN'.$rowCount, $row_reporteA['Asep27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JO'.$rowCount, $row_reporteA['Asep28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JP'.$rowCount, $row_reporteA['Asep29']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JQ'.$rowCount, $row_reporteA['Asep30']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JR'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JS'.$rowCount, $row_reporteA['Aoct2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JT'.$rowCount, $row_reporteA['Aoct3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JU'.$rowCount, $row_reporteA['Aoct4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JV'.$rowCount, $row_reporteA['Aoct5']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JW'.$rowCount, $row_reporteA['Aoct6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JX'.$rowCount, $row_reporteA['Aoct7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JY'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('JZ'.$rowCount, $row_reporteA['Aoct9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KA'.$rowCount, $row_reporteA['Aoct10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KB'.$rowCount, $row_reporteA['Aoct11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KC'.$rowCount, $row_reporteA['Aoct12']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KD'.$rowCount, $row_reporteA['Aoct13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KE'.$rowCount, $row_reporteA['Aoct14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KF'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KG'.$rowCount, $row_reporteA['Aoct16']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KH'.$rowCount, $row_reporteA['Aoct17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KI'.$rowCount, $row_reporteA['Aoct18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KJ'.$rowCount, $row_reporteA['Aoct19']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KK'.$rowCount, $row_reporteA['Aoct20']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KL'.$rowCount, $row_reporteA['Aoct21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KM'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KN'.$rowCount, $row_reporteA['Aoct23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KO'.$rowCount, $row_reporteA['Aoct24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KP'.$rowCount, $row_reporteA['Aoct25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KQ'.$rowCount, $row_reporteA['Aoct26']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KR'.$rowCount, $row_reporteA['Aoct27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KS'.$rowCount, $row_reporteA['Aoct28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KT'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KU'.$rowCount, $row_reporteA['Aoct30']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KV'.$rowCount, $row_reporteA['Aoct31']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KW'.$rowCount, $row_reporteA['Anov1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KX'.$rowCount, $row_reporteA['Anov2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KY'.$rowCount, $row_reporteA['Anov3']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('KZ'.$rowCount, $row_reporteA['Anov4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LA'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LB'.$rowCount, $row_reporteA['Anov6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LC'.$rowCount, $row_reporteA['Anov7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LD'.$rowCount, $row_reporteA['Anov8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LE'.$rowCount, $row_reporteA['Anov9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LF'.$rowCount, $row_reporteA['Anov10']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LG'.$rowCount, $row_reporteA['Anov11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LH'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LI'.$rowCount, $row_reporteA['Anov13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LJ'.$rowCount, $row_reporteA['Anov14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LK'.$rowCount, $row_reporteA['Anov15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LL'.$rowCount, $row_reporteA['Anov16']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LM'.$rowCount, $row_reporteA['Anov17']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LN'.$rowCount, $row_reporteA['Anov18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LO'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LP'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LQ'.$rowCount, $row_reporteA['Anov21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LR'.$rowCount, $row_reporteA['Anov22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LS'.$rowCount, $row_reporteA['Anov23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LT'.$rowCount, $row_reporteA['Anov24']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LU'.$rowCount, $row_reporteA['Anov25']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LV'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LW'.$rowCount, $row_reporteA['Anov27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LX'.$rowCount, $row_reporteA['Anov28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LY'.$rowCount, $row_reporteA['Anov29']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('LZ'.$rowCount, $row_reporteA['Anov30']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MA'.$rowCount, $row_reporteA['Adic1']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MB'.$rowCount, $row_reporteA['Adic2']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MC'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MD'.$rowCount, $row_reporteA['Adic4']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('ME'.$rowCount, $row_reporteA['Adic5']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MF'.$rowCount, $row_reporteA['Adic6']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MG'.$rowCount, $row_reporteA['Adic7']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MH'.$rowCount, $row_reporteA['Adic8']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MI'.$rowCount, $row_reporteA['Adic9']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MJ'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MK'.$rowCount, $row_reporteA['Adic11']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('ML'.$rowCount, $row_reporteA['Adic12']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MM'.$rowCount, $row_reporteA['Adic13']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MN'.$rowCount, $row_reporteA['Adic14']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MO'.$rowCount, $row_reporteA['Adic15']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MP'.$rowCount, $row_reporteA['Adic16']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MQ'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MR'.$rowCount, $row_reporteA['Adic18']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MS'.$rowCount, $row_reporteA['Adic19']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MT'.$rowCount, $row_reporteA['Adic20']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MU'.$rowCount, $row_reporteA['Adic21']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MV'.$rowCount, $row_reporteA['Adic22']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MW'.$rowCount, $row_reporteA['Adic23']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MX'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MY'.$rowCount, 0); 
        $objPHPExcel->getActiveSheet()->SetCellValue('MZ'.$rowCount, $row_reporteA['Adic26']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('NA'.$rowCount, $row_reporteA['Adic27']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('NB'.$rowCount, $row_reporteA['Adic28']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('NC'.$rowCount, $row_reporteA['Adic29']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('ND'.$rowCount, $row_reporteA['Adic30']); 
        $objPHPExcel->getActiveSheet()->SetCellValue('NE'.$rowCount, 0); 



// Increment the Excel row counter
        $rowCount++; 
    }

$la_matriz = $row_reporte['matriz'];

    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="VACACIONES 2024 '.date('d-m-Y').$la_matriz.'.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
	
	
?>