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

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

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
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


require_once 'assets/PHPExcel.php';
set_time_limit(0);

mysql_select_db($database_vacantes, $vacantes);
$query_reporteA = "SELECT
SUM(case WHEN ('2024-01-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene1,
SUM(case WHEN ('2024-01-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene2,
SUM(case WHEN ('2024-01-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene3,
SUM(case WHEN ('2024-01-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene4,
SUM(case WHEN ('2024-01-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene5,
SUM(case WHEN ('2024-01-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene6,
SUM(case WHEN ('2024-01-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene7,
SUM(case WHEN ('2024-01-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene8,
SUM(case WHEN ('2024-01-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene9,
SUM(case WHEN ('2024-01-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene10,
SUM(case WHEN ('2024-01-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene11,
SUM(case WHEN ('2024-01-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene12,
SUM(case WHEN ('2024-01-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene13,
SUM(case WHEN ('2024-01-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene14,
SUM(case WHEN ('2024-01-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene15,
SUM(case WHEN ('2024-01-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene16,
SUM(case WHEN ('2024-01-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene17,
SUM(case WHEN ('2024-01-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene18,
SUM(case WHEN ('2024-01-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene19,
SUM(case WHEN ('2024-01-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene20,
SUM(case WHEN ('2024-01-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene21,
SUM(case WHEN ('2024-01-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene22,
SUM(case WHEN ('2024-01-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene23,
SUM(case WHEN ('2024-01-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene24,
SUM(case WHEN ('2024-01-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene25,
SUM(case WHEN ('2024-01-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene26,
SUM(case WHEN ('2024-01-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene27,
SUM(case WHEN ('2024-01-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene28,
SUM(case WHEN ('2024-01-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene29,
SUM(case WHEN ('2024-01-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene30,
SUM(case WHEN ('2024-01-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aene31,
SUM(case WHEN ('2024-02-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb1,
SUM(case WHEN ('2024-02-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb2,
SUM(case WHEN ('2024-02-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb3,
SUM(case WHEN ('2024-02-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb4,
SUM(case WHEN ('2024-02-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb5,
SUM(case WHEN ('2024-02-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb6,
SUM(case WHEN ('2024-02-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb7,
SUM(case WHEN ('2024-02-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb8,
SUM(case WHEN ('2024-02-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb9,
SUM(case WHEN ('2024-02-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb10,
SUM(case WHEN ('2024-02-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb11,
SUM(case WHEN ('2024-02-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb12,
SUM(case WHEN ('2024-02-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb13,
SUM(case WHEN ('2024-02-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb14,
SUM(case WHEN ('2024-02-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb15,
SUM(case WHEN ('2024-02-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb16,
SUM(case WHEN ('2024-02-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb17,
SUM(case WHEN ('2024-02-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb18,
SUM(case WHEN ('2024-02-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb19,
SUM(case WHEN ('2024-02-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb20,
SUM(case WHEN ('2024-02-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb21,
SUM(case WHEN ('2024-02-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb22,
SUM(case WHEN ('2024-02-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb23,
SUM(case WHEN ('2024-02-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb24,
SUM(case WHEN ('2024-02-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb25,
SUM(case WHEN ('2024-02-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb26,
SUM(case WHEN ('2024-02-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb27,
SUM(case WHEN ('2024-02-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Afeb28,
SUM(case WHEN ('2024-03-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar1,
SUM(case WHEN ('2024-03-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar2,
SUM(case WHEN ('2024-03-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar3,
SUM(case WHEN ('2024-03-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar4,
SUM(case WHEN ('2024-03-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar5,
SUM(case WHEN ('2024-03-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar6,
SUM(case WHEN ('2024-03-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar7,
SUM(case WHEN ('2024-03-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar8,
SUM(case WHEN ('2024-03-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar9,
SUM(case WHEN ('2024-03-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar10,
SUM(case WHEN ('2024-03-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar11,
SUM(case WHEN ('2024-03-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar12,
SUM(case WHEN ('2024-03-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar13,
SUM(case WHEN ('2024-03-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar14,
SUM(case WHEN ('2024-03-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar15,
SUM(case WHEN ('2024-03-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar16,
SUM(case WHEN ('2024-03-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar17,
SUM(case WHEN ('2024-03-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar18,
SUM(case WHEN ('2024-03-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar19,
SUM(case WHEN ('2024-03-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar20,
SUM(case WHEN ('2024-03-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar21,
SUM(case WHEN ('2024-03-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar22,
SUM(case WHEN ('2024-03-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar23,
SUM(case WHEN ('2024-03-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar24,
SUM(case WHEN ('2024-03-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar25,
SUM(case WHEN ('2024-03-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar26,
SUM(case WHEN ('2024-03-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar27,
SUM(case WHEN ('2024-03-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar28,
SUM(case WHEN ('2024-03-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar29,
SUM(case WHEN ('2024-03-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar30,
SUM(case WHEN ('2024-03-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amar31,
SUM(case WHEN ('2024-04-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr1,
SUM(case WHEN ('2024-04-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr2,
SUM(case WHEN ('2024-04-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr3,
SUM(case WHEN ('2024-04-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr4,
SUM(case WHEN ('2024-04-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr5,
SUM(case WHEN ('2024-04-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr6,
SUM(case WHEN ('2024-04-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr7,
SUM(case WHEN ('2024-04-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr8,
SUM(case WHEN ('2024-04-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr9,
SUM(case WHEN ('2024-04-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr10,
SUM(case WHEN ('2024-04-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr11,
SUM(case WHEN ('2024-04-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr12,
SUM(case WHEN ('2024-04-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr13,
SUM(case WHEN ('2024-04-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr14,
SUM(case WHEN ('2024-04-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr15,
SUM(case WHEN ('2024-04-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr16,
SUM(case WHEN ('2024-04-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr17,
SUM(case WHEN ('2024-04-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr18,
SUM(case WHEN ('2024-04-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr19,
SUM(case WHEN ('2024-04-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr20,
SUM(case WHEN ('2024-04-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr21,
SUM(case WHEN ('2024-04-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr22,
SUM(case WHEN ('2024-04-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr23,
SUM(case WHEN ('2024-04-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr24,
SUM(case WHEN ('2024-04-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr25,
SUM(case WHEN ('2024-04-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr26,
SUM(case WHEN ('2024-04-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr27,
SUM(case WHEN ('2024-04-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr28,
SUM(case WHEN ('2024-04-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr29,
SUM(case WHEN ('2024-04-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aabr30,
SUM(case WHEN ('2024-05-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay1,
SUM(case WHEN ('2024-05-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay2,
SUM(case WHEN ('2024-05-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay3,
SUM(case WHEN ('2024-05-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay4,
SUM(case WHEN ('2024-05-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay5,
SUM(case WHEN ('2024-05-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay6,
SUM(case WHEN ('2024-05-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay7,
SUM(case WHEN ('2024-05-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay8,
SUM(case WHEN ('2024-05-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay9,
SUM(case WHEN ('2024-05-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay10,
SUM(case WHEN ('2024-05-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay11,
SUM(case WHEN ('2024-05-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay12,
SUM(case WHEN ('2024-05-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay13,
SUM(case WHEN ('2024-05-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay14,
SUM(case WHEN ('2024-05-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay15,
SUM(case WHEN ('2024-05-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay16,
SUM(case WHEN ('2024-05-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay17,
SUM(case WHEN ('2024-05-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay18,
SUM(case WHEN ('2024-05-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay19,
SUM(case WHEN ('2024-05-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay20,
SUM(case WHEN ('2024-05-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay21,
SUM(case WHEN ('2024-05-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay22,
SUM(case WHEN ('2024-05-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay23,
SUM(case WHEN ('2024-05-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay24,
SUM(case WHEN ('2024-05-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay25,
SUM(case WHEN ('2024-05-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay26,
SUM(case WHEN ('2024-05-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay27,
SUM(case WHEN ('2024-05-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay28,
SUM(case WHEN ('2024-05-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay29,
SUM(case WHEN ('2024-05-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay30,
SUM(case WHEN ('2024-05-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Amay31,
SUM(case WHEN ('2024-06-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun1,
SUM(case WHEN ('2024-06-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun2,
SUM(case WHEN ('2024-06-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun3,
SUM(case WHEN ('2024-06-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun4,
SUM(case WHEN ('2024-06-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun5,
SUM(case WHEN ('2024-06-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun6,
SUM(case WHEN ('2024-06-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun7,
SUM(case WHEN ('2024-06-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun8,
SUM(case WHEN ('2024-06-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun9,
SUM(case WHEN ('2024-06-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun10,
SUM(case WHEN ('2024-06-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun11,
SUM(case WHEN ('2024-06-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun12,
SUM(case WHEN ('2024-06-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun13,
SUM(case WHEN ('2024-06-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun14,
SUM(case WHEN ('2024-06-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun15,
SUM(case WHEN ('2024-06-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun16,
SUM(case WHEN ('2024-06-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun17,
SUM(case WHEN ('2024-06-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun18,
SUM(case WHEN ('2024-06-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun19,
SUM(case WHEN ('2024-06-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun20,
SUM(case WHEN ('2024-06-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun21,
SUM(case WHEN ('2024-06-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun22,
SUM(case WHEN ('2024-06-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun23,
SUM(case WHEN ('2024-06-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun24,
SUM(case WHEN ('2024-06-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun25,
SUM(case WHEN ('2024-06-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun26,
SUM(case WHEN ('2024-06-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun27,
SUM(case WHEN ('2024-06-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun28,
SUM(case WHEN ('2024-06-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun29,
SUM(case WHEN ('2024-06-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajun30,
SUM(case WHEN ('2024-07-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul1,
SUM(case WHEN ('2024-07-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul2,
SUM(case WHEN ('2024-07-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul3,
SUM(case WHEN ('2024-07-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul4,
SUM(case WHEN ('2024-07-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul5,
SUM(case WHEN ('2024-07-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul6,
SUM(case WHEN ('2024-07-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul7,
SUM(case WHEN ('2024-07-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul8,
SUM(case WHEN ('2024-07-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul9,
SUM(case WHEN ('2024-07-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul10,
SUM(case WHEN ('2024-07-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul11,
SUM(case WHEN ('2024-07-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul12,
SUM(case WHEN ('2024-07-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul13,
SUM(case WHEN ('2024-07-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul14,
SUM(case WHEN ('2024-07-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul15,
SUM(case WHEN ('2024-07-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul16,
SUM(case WHEN ('2024-07-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul17,
SUM(case WHEN ('2024-07-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul18,
SUM(case WHEN ('2024-07-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul19,
SUM(case WHEN ('2024-07-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul20,
SUM(case WHEN ('2024-07-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul21,
SUM(case WHEN ('2024-07-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul22,
SUM(case WHEN ('2024-07-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul23,
SUM(case WHEN ('2024-07-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul24,
SUM(case WHEN ('2024-07-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul25,
SUM(case WHEN ('2024-07-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul26,
SUM(case WHEN ('2024-07-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul27,
SUM(case WHEN ('2024-07-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul28,
SUM(case WHEN ('2024-07-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul29,
SUM(case WHEN ('2024-07-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul30,
SUM(case WHEN ('2024-07-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Ajul31,
SUM(case WHEN ('2024-08-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago1,
SUM(case WHEN ('2024-08-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago2,
SUM(case WHEN ('2024-08-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago3,
SUM(case WHEN ('2024-08-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago4,
SUM(case WHEN ('2024-08-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago5,
SUM(case WHEN ('2024-08-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago6,
SUM(case WHEN ('2024-08-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago7,
SUM(case WHEN ('2024-08-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago8,
SUM(case WHEN ('2024-08-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago9,
SUM(case WHEN ('2024-08-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago10,
SUM(case WHEN ('2024-08-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago11,
SUM(case WHEN ('2024-08-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago12,
SUM(case WHEN ('2024-08-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago13,
SUM(case WHEN ('2024-08-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago14,
SUM(case WHEN ('2024-08-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago15,
SUM(case WHEN ('2024-08-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago16,
SUM(case WHEN ('2024-08-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago17,
SUM(case WHEN ('2024-08-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago18,
SUM(case WHEN ('2024-08-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago19,
SUM(case WHEN ('2024-08-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago20,
SUM(case WHEN ('2024-08-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago21,
SUM(case WHEN ('2024-08-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago22,
SUM(case WHEN ('2024-08-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago23,
SUM(case WHEN ('2024-08-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago24,
SUM(case WHEN ('2024-08-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago25,
SUM(case WHEN ('2024-08-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago26,
SUM(case WHEN ('2024-08-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago27,
SUM(case WHEN ('2024-08-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago28,
SUM(case WHEN ('2024-08-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago29,
SUM(case WHEN ('2024-08-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago30,
SUM(case WHEN ('2024-08-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aago31,
SUM(case WHEN ('2024-09-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep1,
SUM(case WHEN ('2024-09-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep2,
SUM(case WHEN ('2024-09-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep3,
SUM(case WHEN ('2024-09-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep4,
SUM(case WHEN ('2024-09-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep5,
SUM(case WHEN ('2024-09-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep6,
SUM(case WHEN ('2024-09-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep7,
SUM(case WHEN ('2024-09-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep8,
SUM(case WHEN ('2024-09-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep9,
SUM(case WHEN ('2024-09-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep10,
SUM(case WHEN ('2024-09-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep11,
SUM(case WHEN ('2024-09-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep12,
SUM(case WHEN ('2024-09-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep13,
SUM(case WHEN ('2024-09-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep14,
SUM(case WHEN ('2024-09-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep15,
SUM(case WHEN ('2024-09-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep16,
SUM(case WHEN ('2024-09-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep17,
SUM(case WHEN ('2024-09-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep18,
SUM(case WHEN ('2024-09-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep19,
SUM(case WHEN ('2024-09-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep20,
SUM(case WHEN ('2024-09-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep21,
SUM(case WHEN ('2024-09-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep22,
SUM(case WHEN ('2024-09-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep23,
SUM(case WHEN ('2024-09-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep24,
SUM(case WHEN ('2024-09-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep25,
SUM(case WHEN ('2024-09-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep26,
SUM(case WHEN ('2024-09-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep27,
SUM(case WHEN ('2024-09-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep28,
SUM(case WHEN ('2024-09-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep29,
SUM(case WHEN ('2024-09-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Asep30,
SUM(case WHEN ('2024-10-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct1,
SUM(case WHEN ('2024-10-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct2,
SUM(case WHEN ('2024-10-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct3,
SUM(case WHEN ('2024-10-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct4,
SUM(case WHEN ('2024-10-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct5,
SUM(case WHEN ('2024-10-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct6,
SUM(case WHEN ('2024-10-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct7,
SUM(case WHEN ('2024-10-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct8,
SUM(case WHEN ('2024-10-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct9,
SUM(case WHEN ('2024-10-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct10,
SUM(case WHEN ('2024-10-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct11,
SUM(case WHEN ('2024-10-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct12,
SUM(case WHEN ('2024-10-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct13,
SUM(case WHEN ('2024-10-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct14,
SUM(case WHEN ('2024-10-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct15,
SUM(case WHEN ('2024-10-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct16,
SUM(case WHEN ('2024-10-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct17,
SUM(case WHEN ('2024-10-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct18,
SUM(case WHEN ('2024-10-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct19,
SUM(case WHEN ('2024-10-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct20,
SUM(case WHEN ('2024-10-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct21,
SUM(case WHEN ('2024-10-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct22,
SUM(case WHEN ('2024-10-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct23,
SUM(case WHEN ('2024-10-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct24,
SUM(case WHEN ('2024-10-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct25,
SUM(case WHEN ('2024-10-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct26,
SUM(case WHEN ('2024-10-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct27,
SUM(case WHEN ('2024-10-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct28,
SUM(case WHEN ('2024-10-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct29,
SUM(case WHEN ('2024-10-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct30,
SUM(case WHEN ('2024-10-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Aoct31,
SUM(case WHEN ('2024-11-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov1,
SUM(case WHEN ('2024-11-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov2,
SUM(case WHEN ('2024-11-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov3,
SUM(case WHEN ('2024-11-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov4,
SUM(case WHEN ('2024-11-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov5,
SUM(case WHEN ('2024-11-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov6,
SUM(case WHEN ('2024-11-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov7,
SUM(case WHEN ('2024-11-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov8,
SUM(case WHEN ('2024-11-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov9,
SUM(case WHEN ('2024-11-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov10,
SUM(case WHEN ('2024-11-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov11,
SUM(case WHEN ('2024-11-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov12,
SUM(case WHEN ('2024-11-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov13,
SUM(case WHEN ('2024-11-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov14,
SUM(case WHEN ('2024-11-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov15,
SUM(case WHEN ('2024-11-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov16,
SUM(case WHEN ('2024-11-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov17,
SUM(case WHEN ('2024-11-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov18,
SUM(case WHEN ('2024-11-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov19,
SUM(case WHEN ('2024-11-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov20,
SUM(case WHEN ('2024-11-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov21,
SUM(case WHEN ('2024-11-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov22,
SUM(case WHEN ('2024-11-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov23,
SUM(case WHEN ('2024-11-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov24,
SUM(case WHEN ('2024-11-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov25,
SUM(case WHEN ('2024-11-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov26,
SUM(case WHEN ('2024-11-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov27,
SUM(case WHEN ('2024-11-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov28,
SUM(case WHEN ('2024-11-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov29,
SUM(case WHEN ('2024-11-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Anov30,
SUM(case WHEN ('2024-12-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic1,
SUM(case WHEN ('2024-12-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic2,
SUM(case WHEN ('2024-12-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic3,
SUM(case WHEN ('2024-12-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic4,
SUM(case WHEN ('2024-12-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic5,
SUM(case WHEN ('2024-12-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic6,
SUM(case WHEN ('2024-12-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic7,
SUM(case WHEN ('2024-12-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic8,
SUM(case WHEN ('2024-12-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic9,
SUM(case WHEN ('2024-12-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic10,
SUM(case WHEN ('2024-12-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic11,
SUM(case WHEN ('2024-12-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic12,
SUM(case WHEN ('2024-12-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic13,
SUM(case WHEN ('2024-12-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic14,
SUM(case WHEN ('2024-12-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic15,
SUM(case WHEN ('2024-12-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic16,
SUM(case WHEN ('2024-12-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic17,
SUM(case WHEN ('2024-12-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic18,
SUM(case WHEN ('2024-12-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic19,
SUM(case WHEN ('2024-12-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic20,
SUM(case WHEN ('2024-12-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic21,
SUM(case WHEN ('2024-12-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic22,
SUM(case WHEN ('2024-12-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic23,
SUM(case WHEN ('2024-12-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic24,
SUM(case WHEN ('2024-12-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic25,
SUM(case WHEN ('2024-12-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic26,
SUM(case WHEN ('2024-12-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic27,
SUM(case WHEN ('2024-12-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic28,
SUM(case WHEN ('2024-12-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic29,
SUM(case WHEN ('2024-12-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic30,
SUM(case WHEN ('2024-12-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (1,2)) then 1 else 0 end) AS Adic31
FROM inc_vacaciones WHERE inc_vacaciones.IDmatriz = $IDmatriz"; 
$reporteA = mysql_query($query_reporteA, $vacantes) or die(mysql_error());
$row_reporteA = mysql_fetch_assoc($reporteA);

mysql_select_db($database_vacantes, $vacantes);
$query_reporteB = "SELECT
SUM(case WHEN ('2024-01-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene1,
SUM(case WHEN ('2024-01-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene2,
SUM(case WHEN ('2024-01-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene3,
SUM(case WHEN ('2024-01-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene4,
SUM(case WHEN ('2024-01-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene5,
SUM(case WHEN ('2024-01-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene6,
SUM(case WHEN ('2024-01-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene7,
SUM(case WHEN ('2024-01-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene8,
SUM(case WHEN ('2024-01-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene9,
SUM(case WHEN ('2024-01-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene10,
SUM(case WHEN ('2024-01-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene11,
SUM(case WHEN ('2024-01-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene12,
SUM(case WHEN ('2024-01-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene13,
SUM(case WHEN ('2024-01-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene14,
SUM(case WHEN ('2024-01-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene15,
SUM(case WHEN ('2024-01-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene16,
SUM(case WHEN ('2024-01-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene17,
SUM(case WHEN ('2024-01-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene18,
SUM(case WHEN ('2024-01-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene19,
SUM(case WHEN ('2024-01-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene20,
SUM(case WHEN ('2024-01-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene21,
SUM(case WHEN ('2024-01-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene22,
SUM(case WHEN ('2024-01-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene23,
SUM(case WHEN ('2024-01-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene24,
SUM(case WHEN ('2024-01-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene25,
SUM(case WHEN ('2024-01-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene26,
SUM(case WHEN ('2024-01-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene27,
SUM(case WHEN ('2024-01-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene28,
SUM(case WHEN ('2024-01-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene29,
SUM(case WHEN ('2024-01-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene30,
SUM(case WHEN ('2024-01-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bene31,
SUM(case WHEN ('2024-02-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb1,
SUM(case WHEN ('2024-02-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb2,
SUM(case WHEN ('2024-02-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb3,
SUM(case WHEN ('2024-02-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb4,
SUM(case WHEN ('2024-02-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb5,
SUM(case WHEN ('2024-02-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb6,
SUM(case WHEN ('2024-02-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb7,
SUM(case WHEN ('2024-02-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb8,
SUM(case WHEN ('2024-02-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb9,
SUM(case WHEN ('2024-02-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb10,
SUM(case WHEN ('2024-02-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb11,
SUM(case WHEN ('2024-02-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb12,
SUM(case WHEN ('2024-02-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb13,
SUM(case WHEN ('2024-02-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb14,
SUM(case WHEN ('2024-02-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb15,
SUM(case WHEN ('2024-02-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb16,
SUM(case WHEN ('2024-02-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb17,
SUM(case WHEN ('2024-02-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb18,
SUM(case WHEN ('2024-02-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb19,
SUM(case WHEN ('2024-02-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb20,
SUM(case WHEN ('2024-02-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb21,
SUM(case WHEN ('2024-02-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb22,
SUM(case WHEN ('2024-02-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb23,
SUM(case WHEN ('2024-02-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb24,
SUM(case WHEN ('2024-02-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb25,
SUM(case WHEN ('2024-02-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb26,
SUM(case WHEN ('2024-02-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb27,
SUM(case WHEN ('2024-02-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bfeb28,
SUM(case WHEN ('2024-03-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar1,
SUM(case WHEN ('2024-03-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar2,
SUM(case WHEN ('2024-03-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar3,
SUM(case WHEN ('2024-03-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar4,
SUM(case WHEN ('2024-03-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar5,
SUM(case WHEN ('2024-03-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar6,
SUM(case WHEN ('2024-03-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar7,
SUM(case WHEN ('2024-03-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar8,
SUM(case WHEN ('2024-03-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar9,
SUM(case WHEN ('2024-03-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar10,
SUM(case WHEN ('2024-03-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar11,
SUM(case WHEN ('2024-03-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar12,
SUM(case WHEN ('2024-03-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar13,
SUM(case WHEN ('2024-03-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar14,
SUM(case WHEN ('2024-03-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar15,
SUM(case WHEN ('2024-03-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar16,
SUM(case WHEN ('2024-03-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar17,
SUM(case WHEN ('2024-03-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar18,
SUM(case WHEN ('2024-03-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar19,
SUM(case WHEN ('2024-03-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar20,
SUM(case WHEN ('2024-03-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar21,
SUM(case WHEN ('2024-03-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar22,
SUM(case WHEN ('2024-03-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar23,
SUM(case WHEN ('2024-03-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar24,
SUM(case WHEN ('2024-03-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar25,
SUM(case WHEN ('2024-03-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar26,
SUM(case WHEN ('2024-03-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar27,
SUM(case WHEN ('2024-03-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar28,
SUM(case WHEN ('2024-03-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar29,
SUM(case WHEN ('2024-03-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar30,
SUM(case WHEN ('2024-03-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmar31,
SUM(case WHEN ('2024-04-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr1,
SUM(case WHEN ('2024-04-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr2,
SUM(case WHEN ('2024-04-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr3,
SUM(case WHEN ('2024-04-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr4,
SUM(case WHEN ('2024-04-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr5,
SUM(case WHEN ('2024-04-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr6,
SUM(case WHEN ('2024-04-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr7,
SUM(case WHEN ('2024-04-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr8,
SUM(case WHEN ('2024-04-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr9,
SUM(case WHEN ('2024-04-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr10,
SUM(case WHEN ('2024-04-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr11,
SUM(case WHEN ('2024-04-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr12,
SUM(case WHEN ('2024-04-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr13,
SUM(case WHEN ('2024-04-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr14,
SUM(case WHEN ('2024-04-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr15,
SUM(case WHEN ('2024-04-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr16,
SUM(case WHEN ('2024-04-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr17,
SUM(case WHEN ('2024-04-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr18,
SUM(case WHEN ('2024-04-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr19,
SUM(case WHEN ('2024-04-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr20,
SUM(case WHEN ('2024-04-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr21,
SUM(case WHEN ('2024-04-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr22,
SUM(case WHEN ('2024-04-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr23,
SUM(case WHEN ('2024-04-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr24,
SUM(case WHEN ('2024-04-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr25,
SUM(case WHEN ('2024-04-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr26,
SUM(case WHEN ('2024-04-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr27,
SUM(case WHEN ('2024-04-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr28,
SUM(case WHEN ('2024-04-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr29,
SUM(case WHEN ('2024-04-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Babr30,
SUM(case WHEN ('2024-05-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay1,
SUM(case WHEN ('2024-05-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay2,
SUM(case WHEN ('2024-05-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay3,
SUM(case WHEN ('2024-05-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay4,
SUM(case WHEN ('2024-05-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay5,
SUM(case WHEN ('2024-05-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay6,
SUM(case WHEN ('2024-05-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay7,
SUM(case WHEN ('2024-05-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay8,
SUM(case WHEN ('2024-05-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay9,
SUM(case WHEN ('2024-05-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay10,
SUM(case WHEN ('2024-05-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay11,
SUM(case WHEN ('2024-05-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay12,
SUM(case WHEN ('2024-05-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay13,
SUM(case WHEN ('2024-05-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay14,
SUM(case WHEN ('2024-05-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay15,
SUM(case WHEN ('2024-05-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay16,
SUM(case WHEN ('2024-05-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay17,
SUM(case WHEN ('2024-05-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay18,
SUM(case WHEN ('2024-05-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay19,
SUM(case WHEN ('2024-05-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay20,
SUM(case WHEN ('2024-05-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay21,
SUM(case WHEN ('2024-05-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay22,
SUM(case WHEN ('2024-05-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay23,
SUM(case WHEN ('2024-05-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay24,
SUM(case WHEN ('2024-05-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay25,
SUM(case WHEN ('2024-05-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay26,
SUM(case WHEN ('2024-05-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay27,
SUM(case WHEN ('2024-05-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay28,
SUM(case WHEN ('2024-05-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay29,
SUM(case WHEN ('2024-05-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay30,
SUM(case WHEN ('2024-05-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bmay31,
SUM(case WHEN ('2024-06-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun1,
SUM(case WHEN ('2024-06-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun2,
SUM(case WHEN ('2024-06-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun3,
SUM(case WHEN ('2024-06-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun4,
SUM(case WHEN ('2024-06-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun5,
SUM(case WHEN ('2024-06-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun6,
SUM(case WHEN ('2024-06-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun7,
SUM(case WHEN ('2024-06-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun8,
SUM(case WHEN ('2024-06-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun9,
SUM(case WHEN ('2024-06-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun10,
SUM(case WHEN ('2024-06-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun11,
SUM(case WHEN ('2024-06-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun12,
SUM(case WHEN ('2024-06-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun13,
SUM(case WHEN ('2024-06-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun14,
SUM(case WHEN ('2024-06-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun15,
SUM(case WHEN ('2024-06-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun16,
SUM(case WHEN ('2024-06-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun17,
SUM(case WHEN ('2024-06-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun18,
SUM(case WHEN ('2024-06-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun19,
SUM(case WHEN ('2024-06-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun20,
SUM(case WHEN ('2024-06-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun21,
SUM(case WHEN ('2024-06-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun22,
SUM(case WHEN ('2024-06-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun23,
SUM(case WHEN ('2024-06-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun24,
SUM(case WHEN ('2024-06-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun25,
SUM(case WHEN ('2024-06-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun26,
SUM(case WHEN ('2024-06-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun27,
SUM(case WHEN ('2024-06-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun28,
SUM(case WHEN ('2024-06-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun29,
SUM(case WHEN ('2024-06-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjun30,
SUM(case WHEN ('2024-07-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul1,
SUM(case WHEN ('2024-07-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul2,
SUM(case WHEN ('2024-07-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul3,
SUM(case WHEN ('2024-07-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul4,
SUM(case WHEN ('2024-07-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul5,
SUM(case WHEN ('2024-07-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul6,
SUM(case WHEN ('2024-07-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul7,
SUM(case WHEN ('2024-07-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul8,
SUM(case WHEN ('2024-07-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul9,
SUM(case WHEN ('2024-07-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul10,
SUM(case WHEN ('2024-07-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul11,
SUM(case WHEN ('2024-07-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul12,
SUM(case WHEN ('2024-07-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul13,
SUM(case WHEN ('2024-07-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul14,
SUM(case WHEN ('2024-07-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul15,
SUM(case WHEN ('2024-07-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul16,
SUM(case WHEN ('2024-07-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul17,
SUM(case WHEN ('2024-07-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul18,
SUM(case WHEN ('2024-07-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul19,
SUM(case WHEN ('2024-07-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul20,
SUM(case WHEN ('2024-07-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul21,
SUM(case WHEN ('2024-07-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul22,
SUM(case WHEN ('2024-07-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul23,
SUM(case WHEN ('2024-07-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul24,
SUM(case WHEN ('2024-07-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul25,
SUM(case WHEN ('2024-07-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul26,
SUM(case WHEN ('2024-07-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul27,
SUM(case WHEN ('2024-07-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul28,
SUM(case WHEN ('2024-07-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul29,
SUM(case WHEN ('2024-07-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul30,
SUM(case WHEN ('2024-07-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bjul31,
SUM(case WHEN ('2024-08-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago1,
SUM(case WHEN ('2024-08-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago2,
SUM(case WHEN ('2024-08-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago3,
SUM(case WHEN ('2024-08-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago4,
SUM(case WHEN ('2024-08-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago5,
SUM(case WHEN ('2024-08-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago6,
SUM(case WHEN ('2024-08-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago7,
SUM(case WHEN ('2024-08-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago8,
SUM(case WHEN ('2024-08-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago9,
SUM(case WHEN ('2024-08-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago10,
SUM(case WHEN ('2024-08-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago11,
SUM(case WHEN ('2024-08-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago12,
SUM(case WHEN ('2024-08-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago13,
SUM(case WHEN ('2024-08-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago14,
SUM(case WHEN ('2024-08-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago15,
SUM(case WHEN ('2024-08-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago16,
SUM(case WHEN ('2024-08-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago17,
SUM(case WHEN ('2024-08-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago18,
SUM(case WHEN ('2024-08-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago19,
SUM(case WHEN ('2024-08-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago20,
SUM(case WHEN ('2024-08-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago21,
SUM(case WHEN ('2024-08-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago22,
SUM(case WHEN ('2024-08-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago23,
SUM(case WHEN ('2024-08-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago24,
SUM(case WHEN ('2024-08-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago25,
SUM(case WHEN ('2024-08-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago26,
SUM(case WHEN ('2024-08-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago27,
SUM(case WHEN ('2024-08-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago28,
SUM(case WHEN ('2024-08-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago29,
SUM(case WHEN ('2024-08-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago30,
SUM(case WHEN ('2024-08-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bago31,
SUM(case WHEN ('2024-09-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep1,
SUM(case WHEN ('2024-09-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep2,
SUM(case WHEN ('2024-09-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep3,
SUM(case WHEN ('2024-09-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep4,
SUM(case WHEN ('2024-09-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep5,
SUM(case WHEN ('2024-09-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep6,
SUM(case WHEN ('2024-09-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep7,
SUM(case WHEN ('2024-09-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep8,
SUM(case WHEN ('2024-09-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep9,
SUM(case WHEN ('2024-09-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep10,
SUM(case WHEN ('2024-09-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep11,
SUM(case WHEN ('2024-09-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep12,
SUM(case WHEN ('2024-09-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep13,
SUM(case WHEN ('2024-09-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep14,
SUM(case WHEN ('2024-09-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep15,
SUM(case WHEN ('2024-09-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep16,
SUM(case WHEN ('2024-09-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep17,
SUM(case WHEN ('2024-09-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep18,
SUM(case WHEN ('2024-09-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep19,
SUM(case WHEN ('2024-09-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep20,
SUM(case WHEN ('2024-09-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep21,
SUM(case WHEN ('2024-09-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep22,
SUM(case WHEN ('2024-09-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep23,
SUM(case WHEN ('2024-09-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep24,
SUM(case WHEN ('2024-09-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep25,
SUM(case WHEN ('2024-09-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep26,
SUM(case WHEN ('2024-09-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep27,
SUM(case WHEN ('2024-09-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep28,
SUM(case WHEN ('2024-09-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep29,
SUM(case WHEN ('2024-09-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bsep30,
SUM(case WHEN ('2024-10-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct1,
SUM(case WHEN ('2024-10-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct2,
SUM(case WHEN ('2024-10-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct3,
SUM(case WHEN ('2024-10-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct4,
SUM(case WHEN ('2024-10-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct5,
SUM(case WHEN ('2024-10-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct6,
SUM(case WHEN ('2024-10-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct7,
SUM(case WHEN ('2024-10-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct8,
SUM(case WHEN ('2024-10-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct9,
SUM(case WHEN ('2024-10-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct10,
SUM(case WHEN ('2024-10-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct11,
SUM(case WHEN ('2024-10-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct12,
SUM(case WHEN ('2024-10-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct13,
SUM(case WHEN ('2024-10-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct14,
SUM(case WHEN ('2024-10-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct15,
SUM(case WHEN ('2024-10-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct16,
SUM(case WHEN ('2024-10-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct17,
SUM(case WHEN ('2024-10-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct18,
SUM(case WHEN ('2024-10-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct19,
SUM(case WHEN ('2024-10-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct20,
SUM(case WHEN ('2024-10-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct21,
SUM(case WHEN ('2024-10-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct22,
SUM(case WHEN ('2024-10-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct23,
SUM(case WHEN ('2024-10-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct24,
SUM(case WHEN ('2024-10-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct25,
SUM(case WHEN ('2024-10-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct26,
SUM(case WHEN ('2024-10-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct27,
SUM(case WHEN ('2024-10-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct28,
SUM(case WHEN ('2024-10-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct29,
SUM(case WHEN ('2024-10-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct30,
SUM(case WHEN ('2024-10-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Boct31,
SUM(case WHEN ('2024-11-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov1,
SUM(case WHEN ('2024-11-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov2,
SUM(case WHEN ('2024-11-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov3,
SUM(case WHEN ('2024-11-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov4,
SUM(case WHEN ('2024-11-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov5,
SUM(case WHEN ('2024-11-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov6,
SUM(case WHEN ('2024-11-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov7,
SUM(case WHEN ('2024-11-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov8,
SUM(case WHEN ('2024-11-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov9,
SUM(case WHEN ('2024-11-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov10,
SUM(case WHEN ('2024-11-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov11,
SUM(case WHEN ('2024-11-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov12,
SUM(case WHEN ('2024-11-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov13,
SUM(case WHEN ('2024-11-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov14,
SUM(case WHEN ('2024-11-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov15,
SUM(case WHEN ('2024-11-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov16,
SUM(case WHEN ('2024-11-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov17,
SUM(case WHEN ('2024-11-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov18,
SUM(case WHEN ('2024-11-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov19,
SUM(case WHEN ('2024-11-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov20,
SUM(case WHEN ('2024-11-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov21,
SUM(case WHEN ('2024-11-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov22,
SUM(case WHEN ('2024-11-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov23,
SUM(case WHEN ('2024-11-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov24,
SUM(case WHEN ('2024-11-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov25,
SUM(case WHEN ('2024-11-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov26,
SUM(case WHEN ('2024-11-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov27,
SUM(case WHEN ('2024-11-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov28,
SUM(case WHEN ('2024-11-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov29,
SUM(case WHEN ('2024-11-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bnov30,
SUM(case WHEN ('2024-12-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic1,
SUM(case WHEN ('2024-12-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic2,
SUM(case WHEN ('2024-12-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic3,
SUM(case WHEN ('2024-12-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic4,
SUM(case WHEN ('2024-12-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic5,
SUM(case WHEN ('2024-12-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic6,
SUM(case WHEN ('2024-12-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic7,
SUM(case WHEN ('2024-12-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic8,
SUM(case WHEN ('2024-12-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic9,
SUM(case WHEN ('2024-12-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic10,
SUM(case WHEN ('2024-12-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic11,
SUM(case WHEN ('2024-12-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic12,
SUM(case WHEN ('2024-12-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic13,
SUM(case WHEN ('2024-12-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic14,
SUM(case WHEN ('2024-12-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic15,
SUM(case WHEN ('2024-12-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic16,
SUM(case WHEN ('2024-12-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic17,
SUM(case WHEN ('2024-12-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic18,
SUM(case WHEN ('2024-12-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic19,
SUM(case WHEN ('2024-12-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic20,
SUM(case WHEN ('2024-12-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic21,
SUM(case WHEN ('2024-12-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic22,
SUM(case WHEN ('2024-12-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic23,
SUM(case WHEN ('2024-12-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic24,
SUM(case WHEN ('2024-12-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic25,
SUM(case WHEN ('2024-12-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic26,
SUM(case WHEN ('2024-12-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic27,
SUM(case WHEN ('2024-12-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic28,
SUM(case WHEN ('2024-12-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic29,
SUM(case WHEN ('2024-12-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic30,
SUM(case WHEN ('2024-12-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (3,4)) then 1 else 0 end) AS Bdic31
FROM inc_vacaciones WHERE inc_vacaciones.IDmatriz = $IDmatriz"; 
$reporteB = mysql_query($query_reporteB, $vacantes) or die(mysql_error());
$row_reporteB = mysql_fetch_assoc($reporteB);


mysql_select_db($database_vacantes, $vacantes);
$query_reporteC = "SELECT
SUM(case WHEN ('2024-01-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene1,
SUM(case WHEN ('2024-01-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene2,
SUM(case WHEN ('2024-01-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene3,
SUM(case WHEN ('2024-01-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene4,
SUM(case WHEN ('2024-01-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene5,
SUM(case WHEN ('2024-01-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene6,
SUM(case WHEN ('2024-01-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene7,
SUM(case WHEN ('2024-01-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene8,
SUM(case WHEN ('2024-01-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene9,
SUM(case WHEN ('2024-01-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene10,
SUM(case WHEN ('2024-01-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene11,
SUM(case WHEN ('2024-01-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene12,
SUM(case WHEN ('2024-01-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene13,
SUM(case WHEN ('2024-01-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene14,
SUM(case WHEN ('2024-01-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene15,
SUM(case WHEN ('2024-01-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene16,
SUM(case WHEN ('2024-01-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene17,
SUM(case WHEN ('2024-01-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene18,
SUM(case WHEN ('2024-01-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene19,
SUM(case WHEN ('2024-01-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene20,
SUM(case WHEN ('2024-01-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene21,
SUM(case WHEN ('2024-01-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene22,
SUM(case WHEN ('2024-01-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene23,
SUM(case WHEN ('2024-01-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene24,
SUM(case WHEN ('2024-01-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene25,
SUM(case WHEN ('2024-01-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene26,
SUM(case WHEN ('2024-01-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene27,
SUM(case WHEN ('2024-01-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene28,
SUM(case WHEN ('2024-01-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene29,
SUM(case WHEN ('2024-01-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene30,
SUM(case WHEN ('2024-01-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cene31,
SUM(case WHEN ('2024-02-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb1,
SUM(case WHEN ('2024-02-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb2,
SUM(case WHEN ('2024-02-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb3,
SUM(case WHEN ('2024-02-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb4,
SUM(case WHEN ('2024-02-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb5,
SUM(case WHEN ('2024-02-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb6,
SUM(case WHEN ('2024-02-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb7,
SUM(case WHEN ('2024-02-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb8,
SUM(case WHEN ('2024-02-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb9,
SUM(case WHEN ('2024-02-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb10,
SUM(case WHEN ('2024-02-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb11,
SUM(case WHEN ('2024-02-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb12,
SUM(case WHEN ('2024-02-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb13,
SUM(case WHEN ('2024-02-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb14,
SUM(case WHEN ('2024-02-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb15,
SUM(case WHEN ('2024-02-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb16,
SUM(case WHEN ('2024-02-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb17,
SUM(case WHEN ('2024-02-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb18,
SUM(case WHEN ('2024-02-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb19,
SUM(case WHEN ('2024-02-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb20,
SUM(case WHEN ('2024-02-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb21,
SUM(case WHEN ('2024-02-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb22,
SUM(case WHEN ('2024-02-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb23,
SUM(case WHEN ('2024-02-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb24,
SUM(case WHEN ('2024-02-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb25,
SUM(case WHEN ('2024-02-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb26,
SUM(case WHEN ('2024-02-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb27,
SUM(case WHEN ('2024-02-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cfeb28,
SUM(case WHEN ('2024-03-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar1,
SUM(case WHEN ('2024-03-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar2,
SUM(case WHEN ('2024-03-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar3,
SUM(case WHEN ('2024-03-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar4,
SUM(case WHEN ('2024-03-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar5,
SUM(case WHEN ('2024-03-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar6,
SUM(case WHEN ('2024-03-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar7,
SUM(case WHEN ('2024-03-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar8,
SUM(case WHEN ('2024-03-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar9,
SUM(case WHEN ('2024-03-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar10,
SUM(case WHEN ('2024-03-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar11,
SUM(case WHEN ('2024-03-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar12,
SUM(case WHEN ('2024-03-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar13,
SUM(case WHEN ('2024-03-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar14,
SUM(case WHEN ('2024-03-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar15,
SUM(case WHEN ('2024-03-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar16,
SUM(case WHEN ('2024-03-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar17,
SUM(case WHEN ('2024-03-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar18,
SUM(case WHEN ('2024-03-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar19,
SUM(case WHEN ('2024-03-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar20,
SUM(case WHEN ('2024-03-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar21,
SUM(case WHEN ('2024-03-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar22,
SUM(case WHEN ('2024-03-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar23,
SUM(case WHEN ('2024-03-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar24,
SUM(case WHEN ('2024-03-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar25,
SUM(case WHEN ('2024-03-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar26,
SUM(case WHEN ('2024-03-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar27,
SUM(case WHEN ('2024-03-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar28,
SUM(case WHEN ('2024-03-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar29,
SUM(case WHEN ('2024-03-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar30,
SUM(case WHEN ('2024-03-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmar31,
SUM(case WHEN ('2024-04-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr1,
SUM(case WHEN ('2024-04-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr2,
SUM(case WHEN ('2024-04-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr3,
SUM(case WHEN ('2024-04-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr4,
SUM(case WHEN ('2024-04-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr5,
SUM(case WHEN ('2024-04-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr6,
SUM(case WHEN ('2024-04-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr7,
SUM(case WHEN ('2024-04-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr8,
SUM(case WHEN ('2024-04-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr9,
SUM(case WHEN ('2024-04-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr10,
SUM(case WHEN ('2024-04-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr11,
SUM(case WHEN ('2024-04-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr12,
SUM(case WHEN ('2024-04-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr13,
SUM(case WHEN ('2024-04-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr14,
SUM(case WHEN ('2024-04-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr15,
SUM(case WHEN ('2024-04-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr16,
SUM(case WHEN ('2024-04-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr17,
SUM(case WHEN ('2024-04-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr18,
SUM(case WHEN ('2024-04-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr19,
SUM(case WHEN ('2024-04-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr20,
SUM(case WHEN ('2024-04-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr21,
SUM(case WHEN ('2024-04-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr22,
SUM(case WHEN ('2024-04-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr23,
SUM(case WHEN ('2024-04-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr24,
SUM(case WHEN ('2024-04-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr25,
SUM(case WHEN ('2024-04-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr26,
SUM(case WHEN ('2024-04-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr27,
SUM(case WHEN ('2024-04-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr28,
SUM(case WHEN ('2024-04-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr29,
SUM(case WHEN ('2024-04-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cabr30,
SUM(case WHEN ('2024-05-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay1,
SUM(case WHEN ('2024-05-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay2,
SUM(case WHEN ('2024-05-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay3,
SUM(case WHEN ('2024-05-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay4,
SUM(case WHEN ('2024-05-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay5,
SUM(case WHEN ('2024-05-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay6,
SUM(case WHEN ('2024-05-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay7,
SUM(case WHEN ('2024-05-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay8,
SUM(case WHEN ('2024-05-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay9,
SUM(case WHEN ('2024-05-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay10,
SUM(case WHEN ('2024-05-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay11,
SUM(case WHEN ('2024-05-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay12,
SUM(case WHEN ('2024-05-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay13,
SUM(case WHEN ('2024-05-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay14,
SUM(case WHEN ('2024-05-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay15,
SUM(case WHEN ('2024-05-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay16,
SUM(case WHEN ('2024-05-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay17,
SUM(case WHEN ('2024-05-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay18,
SUM(case WHEN ('2024-05-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay19,
SUM(case WHEN ('2024-05-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay20,
SUM(case WHEN ('2024-05-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay21,
SUM(case WHEN ('2024-05-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay22,
SUM(case WHEN ('2024-05-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay23,
SUM(case WHEN ('2024-05-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay24,
SUM(case WHEN ('2024-05-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay25,
SUM(case WHEN ('2024-05-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay26,
SUM(case WHEN ('2024-05-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay27,
SUM(case WHEN ('2024-05-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay28,
SUM(case WHEN ('2024-05-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay29,
SUM(case WHEN ('2024-05-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay30,
SUM(case WHEN ('2024-05-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cmay31,
SUM(case WHEN ('2024-06-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun1,
SUM(case WHEN ('2024-06-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun2,
SUM(case WHEN ('2024-06-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun3,
SUM(case WHEN ('2024-06-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun4,
SUM(case WHEN ('2024-06-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun5,
SUM(case WHEN ('2024-06-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun6,
SUM(case WHEN ('2024-06-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun7,
SUM(case WHEN ('2024-06-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun8,
SUM(case WHEN ('2024-06-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun9,
SUM(case WHEN ('2024-06-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun10,
SUM(case WHEN ('2024-06-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun11,
SUM(case WHEN ('2024-06-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun12,
SUM(case WHEN ('2024-06-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun13,
SUM(case WHEN ('2024-06-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun14,
SUM(case WHEN ('2024-06-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun15,
SUM(case WHEN ('2024-06-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun16,
SUM(case WHEN ('2024-06-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun17,
SUM(case WHEN ('2024-06-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun18,
SUM(case WHEN ('2024-06-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun19,
SUM(case WHEN ('2024-06-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun20,
SUM(case WHEN ('2024-06-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun21,
SUM(case WHEN ('2024-06-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun22,
SUM(case WHEN ('2024-06-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun23,
SUM(case WHEN ('2024-06-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun24,
SUM(case WHEN ('2024-06-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun25,
SUM(case WHEN ('2024-06-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun26,
SUM(case WHEN ('2024-06-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun27,
SUM(case WHEN ('2024-06-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun28,
SUM(case WHEN ('2024-06-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun29,
SUM(case WHEN ('2024-06-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjun30,
SUM(case WHEN ('2024-07-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul1,
SUM(case WHEN ('2024-07-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul2,
SUM(case WHEN ('2024-07-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul3,
SUM(case WHEN ('2024-07-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul4,
SUM(case WHEN ('2024-07-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul5,
SUM(case WHEN ('2024-07-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul6,
SUM(case WHEN ('2024-07-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul7,
SUM(case WHEN ('2024-07-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul8,
SUM(case WHEN ('2024-07-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul9,
SUM(case WHEN ('2024-07-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul10,
SUM(case WHEN ('2024-07-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul11,
SUM(case WHEN ('2024-07-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul12,
SUM(case WHEN ('2024-07-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul13,
SUM(case WHEN ('2024-07-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul14,
SUM(case WHEN ('2024-07-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul15,
SUM(case WHEN ('2024-07-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul16,
SUM(case WHEN ('2024-07-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul17,
SUM(case WHEN ('2024-07-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul18,
SUM(case WHEN ('2024-07-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul19,
SUM(case WHEN ('2024-07-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul20,
SUM(case WHEN ('2024-07-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul21,
SUM(case WHEN ('2024-07-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul22,
SUM(case WHEN ('2024-07-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul23,
SUM(case WHEN ('2024-07-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul24,
SUM(case WHEN ('2024-07-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul25,
SUM(case WHEN ('2024-07-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul26,
SUM(case WHEN ('2024-07-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul27,
SUM(case WHEN ('2024-07-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul28,
SUM(case WHEN ('2024-07-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul29,
SUM(case WHEN ('2024-07-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul30,
SUM(case WHEN ('2024-07-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cjul31,
SUM(case WHEN ('2024-08-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago1,
SUM(case WHEN ('2024-08-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago2,
SUM(case WHEN ('2024-08-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago3,
SUM(case WHEN ('2024-08-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago4,
SUM(case WHEN ('2024-08-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago5,
SUM(case WHEN ('2024-08-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago6,
SUM(case WHEN ('2024-08-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago7,
SUM(case WHEN ('2024-08-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago8,
SUM(case WHEN ('2024-08-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago9,
SUM(case WHEN ('2024-08-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago10,
SUM(case WHEN ('2024-08-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago11,
SUM(case WHEN ('2024-08-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago12,
SUM(case WHEN ('2024-08-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago13,
SUM(case WHEN ('2024-08-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago14,
SUM(case WHEN ('2024-08-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago15,
SUM(case WHEN ('2024-08-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago16,
SUM(case WHEN ('2024-08-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago17,
SUM(case WHEN ('2024-08-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago18,
SUM(case WHEN ('2024-08-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago19,
SUM(case WHEN ('2024-08-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago20,
SUM(case WHEN ('2024-08-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago21,
SUM(case WHEN ('2024-08-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago22,
SUM(case WHEN ('2024-08-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago23,
SUM(case WHEN ('2024-08-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago24,
SUM(case WHEN ('2024-08-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago25,
SUM(case WHEN ('2024-08-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago26,
SUM(case WHEN ('2024-08-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago27,
SUM(case WHEN ('2024-08-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago28,
SUM(case WHEN ('2024-08-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago29,
SUM(case WHEN ('2024-08-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago30,
SUM(case WHEN ('2024-08-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cago31,
SUM(case WHEN ('2024-09-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep1,
SUM(case WHEN ('2024-09-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep2,
SUM(case WHEN ('2024-09-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep3,
SUM(case WHEN ('2024-09-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep4,
SUM(case WHEN ('2024-09-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep5,
SUM(case WHEN ('2024-09-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep6,
SUM(case WHEN ('2024-09-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep7,
SUM(case WHEN ('2024-09-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep8,
SUM(case WHEN ('2024-09-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep9,
SUM(case WHEN ('2024-09-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep10,
SUM(case WHEN ('2024-09-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep11,
SUM(case WHEN ('2024-09-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep12,
SUM(case WHEN ('2024-09-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep13,
SUM(case WHEN ('2024-09-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep14,
SUM(case WHEN ('2024-09-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep15,
SUM(case WHEN ('2024-09-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep16,
SUM(case WHEN ('2024-09-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep17,
SUM(case WHEN ('2024-09-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep18,
SUM(case WHEN ('2024-09-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep19,
SUM(case WHEN ('2024-09-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep20,
SUM(case WHEN ('2024-09-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep21,
SUM(case WHEN ('2024-09-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep22,
SUM(case WHEN ('2024-09-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep23,
SUM(case WHEN ('2024-09-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep24,
SUM(case WHEN ('2024-09-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep25,
SUM(case WHEN ('2024-09-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep26,
SUM(case WHEN ('2024-09-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep27,
SUM(case WHEN ('2024-09-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep28,
SUM(case WHEN ('2024-09-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep29,
SUM(case WHEN ('2024-09-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Csep30,
SUM(case WHEN ('2024-10-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct1,
SUM(case WHEN ('2024-10-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct2,
SUM(case WHEN ('2024-10-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct3,
SUM(case WHEN ('2024-10-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct4,
SUM(case WHEN ('2024-10-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct5,
SUM(case WHEN ('2024-10-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct6,
SUM(case WHEN ('2024-10-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct7,
SUM(case WHEN ('2024-10-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct8,
SUM(case WHEN ('2024-10-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct9,
SUM(case WHEN ('2024-10-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct10,
SUM(case WHEN ('2024-10-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct11,
SUM(case WHEN ('2024-10-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct12,
SUM(case WHEN ('2024-10-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct13,
SUM(case WHEN ('2024-10-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct14,
SUM(case WHEN ('2024-10-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct15,
SUM(case WHEN ('2024-10-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct16,
SUM(case WHEN ('2024-10-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct17,
SUM(case WHEN ('2024-10-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct18,
SUM(case WHEN ('2024-10-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct19,
SUM(case WHEN ('2024-10-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct20,
SUM(case WHEN ('2024-10-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct21,
SUM(case WHEN ('2024-10-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct22,
SUM(case WHEN ('2024-10-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct23,
SUM(case WHEN ('2024-10-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct24,
SUM(case WHEN ('2024-10-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct25,
SUM(case WHEN ('2024-10-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct26,
SUM(case WHEN ('2024-10-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct27,
SUM(case WHEN ('2024-10-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct28,
SUM(case WHEN ('2024-10-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct29,
SUM(case WHEN ('2024-10-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct30,
SUM(case WHEN ('2024-10-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Coct31,
SUM(case WHEN ('2024-11-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov1,
SUM(case WHEN ('2024-11-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov2,
SUM(case WHEN ('2024-11-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov3,
SUM(case WHEN ('2024-11-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov4,
SUM(case WHEN ('2024-11-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov5,
SUM(case WHEN ('2024-11-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov6,
SUM(case WHEN ('2024-11-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov7,
SUM(case WHEN ('2024-11-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov8,
SUM(case WHEN ('2024-11-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov9,
SUM(case WHEN ('2024-11-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov10,
SUM(case WHEN ('2024-11-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov11,
SUM(case WHEN ('2024-11-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov12,
SUM(case WHEN ('2024-11-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov13,
SUM(case WHEN ('2024-11-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov14,
SUM(case WHEN ('2024-11-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov15,
SUM(case WHEN ('2024-11-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov16,
SUM(case WHEN ('2024-11-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov17,
SUM(case WHEN ('2024-11-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov18,
SUM(case WHEN ('2024-11-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov19,
SUM(case WHEN ('2024-11-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov20,
SUM(case WHEN ('2024-11-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov21,
SUM(case WHEN ('2024-11-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov22,
SUM(case WHEN ('2024-11-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov23,
SUM(case WHEN ('2024-11-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov24,
SUM(case WHEN ('2024-11-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov25,
SUM(case WHEN ('2024-11-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov26,
SUM(case WHEN ('2024-11-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov27,
SUM(case WHEN ('2024-11-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov28,
SUM(case WHEN ('2024-11-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov29,
SUM(case WHEN ('2024-11-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cnov30,
SUM(case WHEN ('2024-12-01' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic1,
SUM(case WHEN ('2024-12-02' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic2,
SUM(case WHEN ('2024-12-03' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic3,
SUM(case WHEN ('2024-12-04' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic4,
SUM(case WHEN ('2024-12-05' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic5,
SUM(case WHEN ('2024-12-06' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic6,
SUM(case WHEN ('2024-12-07' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic7,
SUM(case WHEN ('2024-12-08' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic8,
SUM(case WHEN ('2024-12-09' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic9,
SUM(case WHEN ('2024-12-10' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic10,
SUM(case WHEN ('2024-12-11' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic11,
SUM(case WHEN ('2024-12-12' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic12,
SUM(case WHEN ('2024-12-13' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic13,
SUM(case WHEN ('2024-12-14' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic14,
SUM(case WHEN ('2024-12-15' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic15,
SUM(case WHEN ('2024-12-16' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic16,
SUM(case WHEN ('2024-12-17' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic17,
SUM(case WHEN ('2024-12-18' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic18,
SUM(case WHEN ('2024-12-19' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic19,
SUM(case WHEN ('2024-12-20' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic20,
SUM(case WHEN ('2024-12-21' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic21,
SUM(case WHEN ('2024-12-22' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic22,
SUM(case WHEN ('2024-12-23' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic23,
SUM(case WHEN ('2024-12-24' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic24,
SUM(case WHEN ('2024-12-25' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic25,
SUM(case WHEN ('2024-12-26' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic26,
SUM(case WHEN ('2024-12-27' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic27,
SUM(case WHEN ('2024-12-28' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic28,
SUM(case WHEN ('2024-12-29' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic29,
SUM(case WHEN ('2024-12-30' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic30,
SUM(case WHEN ('2024-12-31' BETWEEN fecha_inicio AND fecha_fin AND IDarea IN (5,6)) then 1 else 0 end) AS Cdic31
FROM inc_vacaciones WHERE inc_vacaciones.IDmatriz = $IDmatriz"; 
$reporteC = mysql_query($query_reporteC, $vacantes) or die(mysql_error());
$row_reporteC = mysql_fetch_assoc($reporteC);
$totalRows_reporteC = mysql_num_rows($reporteC); 

// SUMAR POR SEMANA
$semana1A = $row_reporteA['Aene1'] +  $row_reporteA['Aene2'] +  $row_reporteA['Aene3'] +  $row_reporteA['Aene4'] +  $row_reporteA['Aene5'] +  $row_reporteA['Aene6'] +  $row_reporteA['Aene7'];
$semana2A = $row_reporteA['Aene8'] +  $row_reporteA['Aene9'] +  $row_reporteA['Aene10'] + $row_reporteA['Aene11'] + $row_reporteA['Aene12'] + $row_reporteA['Aene13'] + $row_reporteA['Aene14'];
$semana3A = $row_reporteA['Aene15'] + $row_reporteA['Aene16'] + $row_reporteA['Aene17'] + $row_reporteA['Aene18'] + $row_reporteA['Aene19'] + $row_reporteA['Aene20'] + $row_reporteA['Aene21'];
$semana4A = $row_reporteA['Aene22'] + $row_reporteA['Aene23'] + $row_reporteA['Aene24'] + $row_reporteA['Aene25'] + $row_reporteA['Aene26'] + $row_reporteA['Aene27'] + $row_reporteA['Aene28'];
$semana5A = $row_reporteA['Aene29'] + $row_reporteA['Aene30'] + $row_reporteA['Aene31'] + $row_reporteA['Afeb1'] +  $row_reporteA['Afeb2'] +  $row_reporteA['Afeb3'] +  $row_reporteA['Afeb4'];
$semana6A = $row_reporteA['Afeb5'] +  $row_reporteA['Afeb6'] +  $row_reporteA['Afeb7'] + $row_reporteA['Afeb8'] +  $row_reporteA['Afeb9'] +  $row_reporteA['Afeb10'] + $row_reporteA['Afeb11'];
$semana7A = $row_reporteA['Afeb12'] + $row_reporteA['Afeb13'] + $row_reporteA['Afeb14'] + $row_reporteA['Afeb15'] + $row_reporteA['Afeb16'] + $row_reporteA['Afeb17'] + $row_reporteA['Afeb18'];
$semana8A = $row_reporteA['Afeb19'] + $row_reporteA['Afeb20'] + $row_reporteA['Afeb21'] + $row_reporteA['Afeb22'] + $row_reporteA['Afeb23'] + $row_reporteA['Afeb24'] + $row_reporteA['Afeb25'];
$semana9A = $row_reporteA['Afeb26'] + $row_reporteA['Afeb27'] + $row_reporteA['Afeb28'] + $row_reporteA['Amar1'] +  $row_reporteA['Amar2'] +  $row_reporteA['Amar3'] +  $row_reporteA['Amar4'];
$semana10A = $row_reporteA['Amar5'] +  $row_reporteA['Amar6'] +  $row_reporteA['Amar7'] + $row_reporteA['Amar8'] +  $row_reporteA['Amar9'] +  $row_reporteA['Amar10'] + $row_reporteA['Amar11'];
$semana11A = $row_reporteA['Amar12'] + $row_reporteA['Amar13'] + $row_reporteA['Amar14'] + $row_reporteA['Amar15'] + $row_reporteA['Amar16'] + $row_reporteA['Amar17'] + $row_reporteA['Amar18'];
$semana12A = $row_reporteA['Amar19'] + $row_reporteA['Amar20'] + $row_reporteA['Amar21'] + $row_reporteA['Amar22'] + $row_reporteA['Amar23'] + $row_reporteA['Amar24'] + $row_reporteA['Amar25'];
$semana13A = $row_reporteA['Amar26'] + $row_reporteA['Amar27'] + $row_reporteA['Amar28'] + $row_reporteA['Aabr1'] +  $row_reporteA['Aabr2'] +  $row_reporteA['Aabr3'] +  $row_reporteA['Aabr4'];
$semana14A = $row_reporteA['Aabr5'] +  $row_reporteA['Aabr6'] +  $row_reporteA['Aabr7'] + $row_reporteA['Aabr8'] +  $row_reporteA['Aabr9'] +  $row_reporteA['Aabr10'] + $row_reporteA['Aabr11'];
$semana15A = $row_reporteA['Aabr12'] + $row_reporteA['Aabr13'] + $row_reporteA['Aabr14'] + $row_reporteA['Aabr15'] + $row_reporteA['Aabr16'] + $row_reporteA['Aabr17'] + $row_reporteA['Aabr18'];
$semana16A = $row_reporteA['Aabr19'] + $row_reporteA['Aabr20'] + $row_reporteA['Aabr21'] + $row_reporteA['Aabr22'] + $row_reporteA['Aabr23'] + $row_reporteA['Aabr24'] + $row_reporteA['Aabr25'];
$semana17A = $row_reporteA['Aabr26'] + $row_reporteA['Aabr27'] + $row_reporteA['Aabr28'] + $row_reporteA['Amay1'] +  $row_reporteA['Amay2'] +  $row_reporteA['Amay3'] +  $row_reporteA['Amay4'];
$semana18A = $row_reporteA['Amay5'] +  $row_reporteA['Amay6'] +  $row_reporteA['Amay7'] + $row_reporteA['Amay8'] +  $row_reporteA['Amay9'] +  $row_reporteA['Amay10'] + $row_reporteA['Amay11'];
$semana19A = $row_reporteA['Amay12'] + $row_reporteA['Amay13'] + $row_reporteA['Amay14'] + $row_reporteA['Amay15'] + $row_reporteA['Amay16'] + $row_reporteA['Amay17'] + $row_reporteA['Amay18'];
$semana20A = $row_reporteA['Amay19'] + $row_reporteA['Amay20'] + $row_reporteA['Amay21'] + $row_reporteA['Amay22'] + $row_reporteA['Amay23'] + $row_reporteA['Amay24'] + $row_reporteA['Amay25'];
$semana21A = $row_reporteA['Amay26'] + $row_reporteA['Amay27'] + $row_reporteA['Amay28'] + $row_reporteA['Ajun1'] +  $row_reporteA['Ajun2'] +  $row_reporteA['Ajun3'] +  $row_reporteA['Ajun4'];
$semana22A = $row_reporteA['Ajun5'] +  $row_reporteA['Ajun6'] +  $row_reporteA['Ajun7'] + $row_reporteA['Ajun8'] +  $row_reporteA['Ajun9'] +  $row_reporteA['Ajun10'] + $row_reporteA['Ajun11']; 
$semana23A = $row_reporteA['Ajun12'] + $row_reporteA['Ajun13'] + $row_reporteA['Ajun14'] + $row_reporteA['Ajun15'] + $row_reporteA['Ajun16'] + $row_reporteA['Ajun17'] + $row_reporteA['Ajun18'];
$semana24A = $row_reporteA['Ajun19'] + $row_reporteA['Ajun20'] + $row_reporteA['Ajun21'] + $row_reporteA['Ajun22'] + $row_reporteA['Ajun23'] + $row_reporteA['Ajun24'] + $row_reporteA['Ajun25'];
$semana25A = $row_reporteA['Ajun26'] + $row_reporteA['Ajun27'] + $row_reporteA['Ajun28'] + $row_reporteA['Ajul1'] +  $row_reporteA['Ajul2'] +  $row_reporteA['Ajul3'] +  $row_reporteA['Ajul4'];
$semana26A = $row_reporteA['Ajul5'] +  $row_reporteA['Ajul6'] +  $row_reporteA['Ajul7'] + $row_reporteA['Ajul8'] +  $row_reporteA['Ajul9'] +  $row_reporteA['Ajul10'] + $row_reporteA['Ajul11'];
$semana27A = $row_reporteA['Ajul12'] + $row_reporteA['Ajul13'] + $row_reporteA['Ajul14'] + $row_reporteA['Ajul15'] + $row_reporteA['Ajul16'] + $row_reporteA['Ajul17'] + $row_reporteA['Ajul18'];
$semana28A = $row_reporteA['Ajul19'] + $row_reporteA['Ajul20'] + $row_reporteA['Ajul21'] + $row_reporteA['Ajul22'] + $row_reporteA['Ajul23'] + $row_reporteA['Ajul24'] + $row_reporteA['Ajul25'];
$semana29A = $row_reporteA['Ajul26'] + $row_reporteA['Ajul27'] + $row_reporteA['Ajul28'] + $row_reporteA['Aago1'] +  $row_reporteA['Aago2'] +  $row_reporteA['Aago3'] +  $row_reporteA['Aago4'];
$semana30A = $row_reporteA['Aago5'] +  $row_reporteA['Aago6'] +  $row_reporteA['Aago7'] + $row_reporteA['Aago8'] +  $row_reporteA['Aago9'] +  $row_reporteA['Aago10'] + $row_reporteA['Aago11'];
$semana31A = $row_reporteA['Aago12'] + $row_reporteA['Aago13'] + $row_reporteA['Aago14'] + $row_reporteA['Aago15'] + $row_reporteA['Aago16'] + $row_reporteA['Aago17'] + $row_reporteA['Aago18'];
$semana32A = $row_reporteA['Aago19'] + $row_reporteA['Aago20'] + $row_reporteA['Aago21'] + $row_reporteA['Aago22'] + $row_reporteA['Aago23'] + $row_reporteA['Aago24'] + $row_reporteA['Aago25'];
$semana33A = $row_reporteA['Aago26'] + $row_reporteA['Aago27'] + $row_reporteA['Aago28'] + $row_reporteA['Asep1'] +  $row_reporteA['Asep2'] +  $row_reporteA['Asep3'] +  $row_reporteA['Asep4'];
$semana34A = $row_reporteA['Asep5'] +  $row_reporteA['Asep6'] +  $row_reporteA['Asep7'] + $row_reporteA['Asep8'] +  $row_reporteA['Asep9'] +  $row_reporteA['Asep10'] + $row_reporteA['Asep11'];
$semana35A = $row_reporteA['Asep12'] + $row_reporteA['Asep13'] + $row_reporteA['Asep14'] + $row_reporteA['Asep15'] + $row_reporteA['Asep16'] + $row_reporteA['Asep17'] + $row_reporteA['Asep18'];
$semana36A = $row_reporteA['Asep19'] + $row_reporteA['Asep20'] + $row_reporteA['Asep21'] + $row_reporteA['Asep22'] + $row_reporteA['Asep23'] + $row_reporteA['Asep24'] + $row_reporteA['Asep25'];
$semana37A = $row_reporteA['Asep26'] + $row_reporteA['Asep27'] + $row_reporteA['Asep28'] + $row_reporteA['Asep1'] +  $row_reporteA['Asep2'] +  $row_reporteA['Asep3'] +  $row_reporteA['Asep4'];
$semana38A = $row_reporteA['Asep5'] +  $row_reporteA['Asep6'] +  $row_reporteA['Asep7'] + $row_reporteA['Asep8'] +  $row_reporteA['Asep9'] +  $row_reporteA['Asep10'] + $row_reporteA['Asep11'];
$semana39A = $row_reporteA['Asep12'] + $row_reporteA['Asep13'] + $row_reporteA['Asep14'] + $row_reporteA['Asep15'] + $row_reporteA['Asep16'] + $row_reporteA['Asep17'] + $row_reporteA['Asep18'];
$semana40A = $row_reporteA['Asep19'] + $row_reporteA['Asep20'] + $row_reporteA['Asep21'] + $row_reporteA['Asep22'] + $row_reporteA['Asep23'] + $row_reporteA['Asep24'] + $row_reporteA['Asep25'];
$semana41A = $row_reporteA['Asep26'] + $row_reporteA['Asep27'] + $row_reporteA['Asep28'] + $row_reporteA['Anov1'] +  $row_reporteA['Anov2'] +  $row_reporteA['Anov3'] +  $row_reporteA['Anov4'];
$semana42A = $row_reporteA['Anov5'] +  $row_reporteA['Anov6'] +  $row_reporteA['Anov7'] + $row_reporteA['Anov8'] +  $row_reporteA['Anov9'] +  $row_reporteA['Anov10'] + $row_reporteA['Anov11'];
$semana43A = $row_reporteA['Anov12'] + $row_reporteA['Anov13'] + $row_reporteA['Anov14'] + $row_reporteA['Anov15'] + $row_reporteA['Anov16'] + $row_reporteA['Anov17'] + $row_reporteA['Anov18'];
$semana44A = $row_reporteA['Anov19'] + $row_reporteA['Anov20'] + $row_reporteA['Anov21'] + $row_reporteA['Anov22'] + $row_reporteA['Anov23'] + $row_reporteA['Anov24'] + $row_reporteA['Anov25'];
$semana45A = $row_reporteA['Anov26'] + $row_reporteA['Anov27'] + $row_reporteA['Anov28'] + $row_reporteA['Adic1'] +  $row_reporteA['Adic2'] +  $row_reporteA['Adic3'] +  $row_reporteA['Adic4'];
$semana46A = $row_reporteA['Adic5'] +  $row_reporteA['Adic6'] +  $row_reporteA['Adic7'] + $row_reporteA['Adic8'] +  $row_reporteA['Adic9'] +  $row_reporteA['Adic10'] + $row_reporteA['Adic11'];
$semana47A = $row_reporteA['Adic12'] + $row_reporteA['Adic13'] + $row_reporteA['Adic14'] + $row_reporteA['Adic15'] + $row_reporteA['Adic16'] + $row_reporteA['Adic17'] + $row_reporteA['Adic18'];
$semana48A = $row_reporteA['Adic19'] + $row_reporteA['Adic20'] + $row_reporteA['Adic21'] + $row_reporteA['Adic22'] + $row_reporteA['Adic23'] + $row_reporteA['Adic24'] + $row_reporteA['Adic25'];
$semana49A = $row_reporteA['Adic26'] + $row_reporteA['Adic27'] + $row_reporteA['Adic28'];

// SUMAR POR SEMANA
$semana1B =$row_reporteB['Bene1'] +  $row_reporteB['Bene2'] +  $row_reporteB['Bene3'] +  $row_reporteB['Bene4'] +  $row_reporteB['Bene5'] +  $row_reporteB['Bene6'] +  $row_reporteB['Bene7'];
$semana2B =$row_reporteB['Bene8'] +  $row_reporteB['Bene9'] +  $row_reporteB['Bene10'] + $row_reporteB['Bene11'] + $row_reporteB['Bene12'] + $row_reporteB['Bene13'] + $row_reporteB['Bene14'];
$semana3B =$row_reporteB['Bene15'] + $row_reporteB['Bene16'] + $row_reporteB['Bene17'] + $row_reporteB['Bene18'] + $row_reporteB['Bene19'] + $row_reporteB['Bene20'] + $row_reporteB['Bene21'];
$semana4B =$row_reporteB['Bene22'] + $row_reporteB['Bene23'] + $row_reporteB['Bene24'] + $row_reporteB['Bene25'] + $row_reporteB['Bene26'] + $row_reporteB['Bene27'] + $row_reporteB['Bene28'];
$semana5B =$row_reporteB['Bene29'] + $row_reporteB['Bene30'] + $row_reporteB['Bene31'] + $row_reporteB['Bfeb1'] +  $row_reporteB['Bfeb2'] +  $row_reporteB['Bfeb3'] +  $row_reporteB['Bfeb4'];
$semana6B =$row_reporteB['Bfeb5'] +  $row_reporteB['Bfeb6'] +  $row_reporteB['Bfeb7'] + $row_reporteB['Bfeb8'] +  $row_reporteB['Bfeb9'] +  $row_reporteB['Bfeb10'] + $row_reporteB['Bfeb11'];
$semana7B =$row_reporteB['Bfeb12'] + $row_reporteB['Bfeb13'] + $row_reporteB['Bfeb14'] + $row_reporteB['Bfeb15'] + $row_reporteB['Bfeb16'] + $row_reporteB['Bfeb17'] + $row_reporteB['Bfeb18'];
$semana8B =$row_reporteB['Bfeb19'] + $row_reporteB['Bfeb20'] + $row_reporteB['Bfeb21'] + $row_reporteB['Bfeb22'] + $row_reporteB['Bfeb23'] + $row_reporteB['Bfeb24'] + $row_reporteB['Bfeb25'];
$semana9B =$row_reporteB['Bfeb26'] + $row_reporteB['Bfeb27'] + $row_reporteB['Bfeb28'] + $row_reporteB['Bmar1'] +  $row_reporteB['Bmar2'] +  $row_reporteB['Bmar3'] +  $row_reporteB['Bmar4'];
$semana10B =$row_reporteB['Bmar5'] +  $row_reporteB['Bmar6'] +  $row_reporteB['Bmar7'] + $row_reporteB['Bmar8'] +  $row_reporteB['Bmar9'] +  $row_reporteB['Bmar10'] + $row_reporteB['Bmar11'];
$semana11B =$row_reporteB['Bmar12'] + $row_reporteB['Bmar13'] + $row_reporteB['Bmar14'] + $row_reporteB['Bmar15'] + $row_reporteB['Bmar16'] + $row_reporteB['Bmar17'] + $row_reporteB['Bmar18'];
$semana12B =$row_reporteB['Bmar19'] + $row_reporteB['Bmar20'] + $row_reporteB['Bmar21'] + $row_reporteB['Bmar22'] + $row_reporteB['Bmar23'] + $row_reporteB['Bmar24'] + $row_reporteB['Bmar25'];
$semana13B =$row_reporteB['Bmar26'] + $row_reporteB['Bmar27'] + $row_reporteB['Bmar28'] + $row_reporteB['Babr1'] +  $row_reporteB['Babr2'] +  $row_reporteB['Babr3'] +  $row_reporteB['Babr4'];
$semana14B =$row_reporteB['Babr5'] +  $row_reporteB['Babr6'] +  $row_reporteB['Babr7'] + $row_reporteB['Babr8'] +  $row_reporteB['Babr9'] +  $row_reporteB['Babr10'] + $row_reporteB['Babr11'];
$semana15B =$row_reporteB['Babr12'] + $row_reporteB['Babr13'] + $row_reporteB['Babr14'] + $row_reporteB['Babr15'] + $row_reporteB['Babr16'] + $row_reporteB['Babr17'] + $row_reporteB['Babr18'];
$semana16B =$row_reporteB['Babr19'] + $row_reporteB['Babr20'] + $row_reporteB['Babr21'] + $row_reporteB['Babr22'] + $row_reporteB['Babr23'] + $row_reporteB['Babr24'] + $row_reporteB['Babr25'];
$semana17B =$row_reporteB['Babr26'] + $row_reporteB['Babr27'] + $row_reporteB['Babr28'] + $row_reporteB['Bmay1'] +  $row_reporteB['Bmay2'] +  $row_reporteB['Bmay3'] +  $row_reporteB['Bmay4'];
$semana18B =$row_reporteB['Bmay5'] +  $row_reporteB['Bmay6'] +  $row_reporteB['Bmay7'] + $row_reporteB['Bmay8'] +  $row_reporteB['Bmay9'] +  $row_reporteB['Bmay10'] + $row_reporteB['Bmay11'];
$semana19B =$row_reporteB['Bmay12'] + $row_reporteB['Bmay13'] + $row_reporteB['Bmay14'] + $row_reporteB['Bmay15'] + $row_reporteB['Bmay16'] + $row_reporteB['Bmay17'] + $row_reporteB['Bmay18'];
$semana20B =$row_reporteB['Bmay19'] + $row_reporteB['Bmay20'] + $row_reporteB['Bmay21'] + $row_reporteB['Bmay22'] + $row_reporteB['Bmay23'] + $row_reporteB['Bmay24'] + $row_reporteB['Bmay25'];
$semana21B =$row_reporteB['Bmay26'] + $row_reporteB['Bmay27'] + $row_reporteB['Bmay28'] + $row_reporteB['Bjun1'] +  $row_reporteB['Bjun2'] +  $row_reporteB['Bjun3'] +  $row_reporteB['Bjun4'];
$semana22B =$row_reporteB['Bjun5'] +  $row_reporteB['Bjun6'] +  $row_reporteB['Bjun7'] + $row_reporteB['Bjun8'] +  $row_reporteB['Bjun9'] +  $row_reporteB['Bjun10'] + $row_reporteB['Bjun11']; 
$semana23B =$row_reporteB['Bjun12'] + $row_reporteB['Bjun13'] + $row_reporteB['Bjun14'] + $row_reporteB['Bjun15'] + $row_reporteB['Bjun16'] + $row_reporteB['Bjun17'] + $row_reporteB['Bjun18'];
$semana24B =$row_reporteB['Bjun19'] + $row_reporteB['Bjun20'] + $row_reporteB['Bjun21'] + $row_reporteB['Bjun22'] + $row_reporteB['Bjun23'] + $row_reporteB['Bjun24'] + $row_reporteB['Bjun25'];
$semana25B =$row_reporteB['Bjun26'] + $row_reporteB['Bjun27'] + $row_reporteB['Bjun28'] + $row_reporteB['Bjul1'] +  $row_reporteB['Bjul2'] +  $row_reporteB['Bjul3'] +  $row_reporteB['Bjul4'];
$semana26B =$row_reporteB['Bjul5'] +  $row_reporteB['Bjul6'] +  $row_reporteB['Bjul7'] + $row_reporteB['Bjul8'] +  $row_reporteB['Bjul9'] +  $row_reporteB['Bjul10'] + $row_reporteB['Bjul11'];
$semana27B =$row_reporteB['Bjul12'] + $row_reporteB['Bjul13'] + $row_reporteB['Bjul14'] + $row_reporteB['Bjul15'] + $row_reporteB['Bjul16'] + $row_reporteB['Bjul17'] + $row_reporteB['Bjul18'];
$semana28B =$row_reporteB['Bjul19'] + $row_reporteB['Bjul20'] + $row_reporteB['Bjul21'] + $row_reporteB['Bjul22'] + $row_reporteB['Bjul23'] + $row_reporteB['Bjul24'] + $row_reporteB['Bjul25'];
$semana29B =$row_reporteB['Bjul26'] + $row_reporteB['Bjul27'] + $row_reporteB['Bjul28'] + $row_reporteB['Bago1'] +  $row_reporteB['Bago2'] +  $row_reporteB['Bago3'] +  $row_reporteB['Bago4'];
$semana30B =$row_reporteB['Bago5'] +  $row_reporteB['Bago6'] +  $row_reporteB['Bago7'] + $row_reporteB['Bago8'] +  $row_reporteB['Bago9'] +  $row_reporteB['Bago10'] + $row_reporteB['Bago11'];
$semana31B =$row_reporteB['Bago12'] + $row_reporteB['Bago13'] + $row_reporteB['Bago14'] + $row_reporteB['Bago15'] + $row_reporteB['Bago16'] + $row_reporteB['Bago17'] + $row_reporteB['Bago18'];
$semana32B =$row_reporteB['Bago19'] + $row_reporteB['Bago20'] + $row_reporteB['Bago21'] + $row_reporteB['Bago22'] + $row_reporteB['Bago23'] + $row_reporteB['Bago24'] + $row_reporteB['Bago25'];
$semana33B =$row_reporteB['Bago26'] + $row_reporteB['Bago27'] + $row_reporteB['Bago28'] + $row_reporteB['Bsep1'] +  $row_reporteB['Bsep2'] +  $row_reporteB['Bsep3'] +  $row_reporteB['Bsep4'];
$semana34B =$row_reporteB['Bsep5'] +  $row_reporteB['Bsep6'] +  $row_reporteB['Bsep7'] + $row_reporteB['Bsep8'] +  $row_reporteB['Bsep9'] +  $row_reporteB['Bsep10'] + $row_reporteB['Bsep11'];
$semana35B =$row_reporteB['Bsep12'] + $row_reporteB['Bsep13'] + $row_reporteB['Bsep14'] + $row_reporteB['Bsep15'] + $row_reporteB['Bsep16'] + $row_reporteB['Bsep17'] + $row_reporteB['Bsep18'];
$semana36B =$row_reporteB['Bsep19'] + $row_reporteB['Bsep20'] + $row_reporteB['Bsep21'] + $row_reporteB['Bsep22'] + $row_reporteB['Bsep23'] + $row_reporteB['Bsep24'] + $row_reporteB['Bsep25'];
$semana37B =$row_reporteB['Bsep26'] + $row_reporteB['Bsep27'] + $row_reporteB['Bsep28'] + $row_reporteB['Bsep1'] +  $row_reporteB['Bsep2'] +  $row_reporteB['Bsep3'] +  $row_reporteB['Bsep4'];
$semana38B =$row_reporteB['Bsep5'] +  $row_reporteB['Bsep6'] +  $row_reporteB['Bsep7'] + $row_reporteB['Bsep8'] +  $row_reporteB['Bsep9'] +  $row_reporteB['Bsep10'] + $row_reporteB['Bsep11'];
$semana39B =$row_reporteB['Bsep12'] + $row_reporteB['Bsep13'] + $row_reporteB['Bsep14'] + $row_reporteB['Bsep15'] + $row_reporteB['Bsep16'] + $row_reporteB['Bsep17'] + $row_reporteB['Bsep18'];
$semana40B =$row_reporteB['Bsep19'] + $row_reporteB['Bsep20'] + $row_reporteB['Bsep21'] + $row_reporteB['Bsep22'] + $row_reporteB['Bsep23'] + $row_reporteB['Bsep24'] + $row_reporteB['Bsep25'];
$semana41B =$row_reporteB['Bsep26'] + $row_reporteB['Bsep27'] + $row_reporteB['Bsep28'] + $row_reporteB['Bnov1'] +  $row_reporteB['Bnov2'] +  $row_reporteB['Bnov3'] +  $row_reporteB['Bnov4'];
$semana42B =$row_reporteB['Bnov5'] +  $row_reporteB['Bnov6'] +  $row_reporteB['Bnov7'] + $row_reporteB['Bnov8'] +  $row_reporteB['Bnov9'] +  $row_reporteB['Bnov10'] + $row_reporteB['Bnov11'];
$semana43B =$row_reporteB['Bnov12'] + $row_reporteB['Bnov13'] + $row_reporteB['Bnov14'] + $row_reporteB['Bnov15'] + $row_reporteB['Bnov16'] + $row_reporteB['Bnov17'] + $row_reporteB['Bnov18'];
$semana44B =$row_reporteB['Bnov19'] + $row_reporteB['Bnov20'] + $row_reporteB['Bnov21'] + $row_reporteB['Bnov22'] + $row_reporteB['Bnov23'] + $row_reporteB['Bnov24'] + $row_reporteB['Bnov25'];
$semana45B =$row_reporteB['Bnov26'] + $row_reporteB['Bnov27'] + $row_reporteB['Bnov28'] + $row_reporteB['Bdic1'] +  $row_reporteB['Bdic2'] +  $row_reporteB['Bdic3'] +  $row_reporteB['Bdic4'];
$semana46B =$row_reporteB['Bdic5'] +  $row_reporteB['Bdic6'] +  $row_reporteB['Bdic7'] + $row_reporteB['Bdic8'] +  $row_reporteB['Bdic9'] +  $row_reporteB['Bdic10'] + $row_reporteB['Bdic11'];
$semana47B =$row_reporteB['Bdic12'] + $row_reporteB['Bdic13'] + $row_reporteB['Bdic14'] + $row_reporteB['Bdic15'] + $row_reporteB['Bdic16'] + $row_reporteB['Bdic17'] + $row_reporteB['Bdic18'];
$semana48B =$row_reporteB['Bdic19'] + $row_reporteB['Bdic20'] + $row_reporteB['Bdic21'] + $row_reporteB['Bdic22'] + $row_reporteB['Bdic23'] + $row_reporteB['Bdic24'] + $row_reporteB['Bdic25'];
$semana49B =$row_reporteB['Bdic26'] + $row_reporteB['Bdic27'] + $row_reporteB['Bdic28'];


// SUMAR POR SEMANA
$semana1C =$row_reporteC['Cene1'] +  $row_reporteC['Cene2'] +  $row_reporteC['Cene3'] +  $row_reporteC['Cene4'] +  $row_reporteC['Cene5'] +  $row_reporteC['Cene6'] +  $row_reporteC['Cene7'];
$semana2C =$row_reporteC['Cene8'] +  $row_reporteC['Cene9'] +  $row_reporteC['Cene10'] + $row_reporteC['Cene11'] + $row_reporteC['Cene12'] + $row_reporteC['Cene13'] + $row_reporteC['Cene14'];
$semana3C =$row_reporteC['Cene15'] + $row_reporteC['Cene16'] + $row_reporteC['Cene17'] + $row_reporteC['Cene18'] + $row_reporteC['Cene19'] + $row_reporteC['Cene20'] + $row_reporteC['Cene21'];
$semana4C =$row_reporteC['Cene22'] + $row_reporteC['Cene23'] + $row_reporteC['Cene24'] + $row_reporteC['Cene25'] + $row_reporteC['Cene26'] + $row_reporteC['Cene27'] + $row_reporteC['Cene28'];
$semana5C =$row_reporteC['Cene29'] + $row_reporteC['Cene30'] + $row_reporteC['Cene31'] + $row_reporteC['Cfeb1'] +  $row_reporteC['Cfeb2'] +  $row_reporteC['Cfeb3'] +  $row_reporteC['Cfeb4'];
$semana6C =$row_reporteC['Cfeb5'] +  $row_reporteC['Cfeb6'] +  $row_reporteC['Cfeb7'] + $row_reporteC['Cfeb8'] +  $row_reporteC['Cfeb9'] +  $row_reporteC['Cfeb10'] + $row_reporteC['Cfeb11'];
$semana7C =$row_reporteC['Cfeb12'] + $row_reporteC['Cfeb13'] + $row_reporteC['Cfeb14'] + $row_reporteC['Cfeb15'] + $row_reporteC['Cfeb16'] + $row_reporteC['Cfeb17'] + $row_reporteC['Cfeb18'];
$semana8C =$row_reporteC['Cfeb19'] + $row_reporteC['Cfeb20'] + $row_reporteC['Cfeb21'] + $row_reporteC['Cfeb22'] + $row_reporteC['Cfeb23'] + $row_reporteC['Cfeb24'] + $row_reporteC['Cfeb25'];
$semana9C =$row_reporteC['Cfeb26'] + $row_reporteC['Cfeb27'] + $row_reporteC['Cfeb28'] + $row_reporteC['Cmar1'] +  $row_reporteC['Cmar2'] +  $row_reporteC['Cmar3'] +  $row_reporteC['Cmar4'];
$semana10C =$row_reporteC['Cmar5'] +  $row_reporteC['Cmar6'] +  $row_reporteC['Cmar7'] + $row_reporteC['Cmar8'] +  $row_reporteC['Cmar9'] +  $row_reporteC['Cmar10'] + $row_reporteC['Cmar11'];
$semana11C =$row_reporteC['Cmar12'] + $row_reporteC['Cmar13'] + $row_reporteC['Cmar14'] + $row_reporteC['Cmar15'] + $row_reporteC['Cmar16'] + $row_reporteC['Cmar17'] + $row_reporteC['Cmar18'];
$semana12C =$row_reporteC['Cmar19'] + $row_reporteC['Cmar20'] + $row_reporteC['Cmar21'] + $row_reporteC['Cmar22'] + $row_reporteC['Cmar23'] + $row_reporteC['Cmar24'] + $row_reporteC['Cmar25'];
$semana13C =$row_reporteC['Cmar26'] + $row_reporteC['Cmar27'] + $row_reporteC['Cmar28'] + $row_reporteC['Cabr1'] +  $row_reporteC['Cabr2'] +  $row_reporteC['Cabr3'] +  $row_reporteC['Cabr4'];
$semana14C =$row_reporteC['Cabr5'] +  $row_reporteC['Cabr6'] +  $row_reporteC['Cabr7'] + $row_reporteC['Cabr8'] +  $row_reporteC['Cabr9'] +  $row_reporteC['Cabr10'] + $row_reporteC['Cabr11'];
$semana15C =$row_reporteC['Cabr12'] + $row_reporteC['Cabr13'] + $row_reporteC['Cabr14'] + $row_reporteC['Cabr15'] + $row_reporteC['Cabr16'] + $row_reporteC['Cabr17'] + $row_reporteC['Cabr18'];
$semana16C =$row_reporteC['Cabr19'] + $row_reporteC['Cabr20'] + $row_reporteC['Cabr21'] + $row_reporteC['Cabr22'] + $row_reporteC['Cabr23'] + $row_reporteC['Cabr24'] + $row_reporteC['Cabr25'];
$semana17C =$row_reporteC['Cabr26'] + $row_reporteC['Cabr27'] + $row_reporteC['Cabr28'] + $row_reporteC['Cmay1'] +  $row_reporteC['Cmay2'] +  $row_reporteC['Cmay3'] +  $row_reporteC['Cmay4'];
$semana18C =$row_reporteC['Cmay5'] +  $row_reporteC['Cmay6'] +  $row_reporteC['Cmay7'] + $row_reporteC['Cmay8'] +  $row_reporteC['Cmay9'] +  $row_reporteC['Cmay10'] + $row_reporteC['Cmay11'];
$semana19C =$row_reporteC['Cmay12'] + $row_reporteC['Cmay13'] + $row_reporteC['Cmay14'] + $row_reporteC['Cmay15'] + $row_reporteC['Cmay16'] + $row_reporteC['Cmay17'] + $row_reporteC['Cmay18'];
$semana20C =$row_reporteC['Cmay19'] + $row_reporteC['Cmay20'] + $row_reporteC['Cmay21'] + $row_reporteC['Cmay22'] + $row_reporteC['Cmay23'] + $row_reporteC['Cmay24'] + $row_reporteC['Cmay25'];
$semana21C =$row_reporteC['Cmay26'] + $row_reporteC['Cmay27'] + $row_reporteC['Cmay28'] + $row_reporteC['Cjun1'] +  $row_reporteC['Cjun2'] +  $row_reporteC['Cjun3'] +  $row_reporteC['Cjun4'];
$semana22C =$row_reporteC['Cjun5'] +  $row_reporteC['Cjun6'] +  $row_reporteC['Cjun7'] + $row_reporteC['Cjun8'] +  $row_reporteC['Cjun9'] +  $row_reporteC['Cjun10'] + $row_reporteC['Cjun11']; 
$semana23C =$row_reporteC['Cjun12'] + $row_reporteC['Cjun13'] + $row_reporteC['Cjun14'] + $row_reporteC['Cjun15'] + $row_reporteC['Cjun16'] + $row_reporteC['Cjun17'] + $row_reporteC['Cjun18'];
$semana24C =$row_reporteC['Cjun19'] + $row_reporteC['Cjun20'] + $row_reporteC['Cjun21'] + $row_reporteC['Cjun22'] + $row_reporteC['Cjun23'] + $row_reporteC['Cjun24'] + $row_reporteC['Cjun25'];
$semana25C =$row_reporteC['Cjun26'] + $row_reporteC['Cjun27'] + $row_reporteC['Cjun28'] + $row_reporteC['Cjul1'] +  $row_reporteC['Cjul2'] +  $row_reporteC['Cjul3'] +  $row_reporteC['Cjul4'];
$semana26C =$row_reporteC['Cjul5'] +  $row_reporteC['Cjul6'] +  $row_reporteC['Cjul7'] + $row_reporteC['Cjul8'] +  $row_reporteC['Cjul9'] +  $row_reporteC['Cjul10'] + $row_reporteC['Cjul11'];
$semana27C =$row_reporteC['Cjul12'] + $row_reporteC['Cjul13'] + $row_reporteC['Cjul14'] + $row_reporteC['Cjul15'] + $row_reporteC['Cjul16'] + $row_reporteC['Cjul17'] + $row_reporteC['Cjul18'];
$semana28C =$row_reporteC['Cjul19'] + $row_reporteC['Cjul20'] + $row_reporteC['Cjul21'] + $row_reporteC['Cjul22'] + $row_reporteC['Cjul23'] + $row_reporteC['Cjul24'] + $row_reporteC['Cjul25'];
$semana29C =$row_reporteC['Cjul26'] + $row_reporteC['Cjul27'] + $row_reporteC['Cjul28'] + $row_reporteC['Cago1'] +  $row_reporteC['Cago2'] +  $row_reporteC['Cago3'] +  $row_reporteC['Cago4'];
$semana30C =$row_reporteC['Cago5'] +  $row_reporteC['Cago6'] +  $row_reporteC['Cago7'] + $row_reporteC['Cago8'] +  $row_reporteC['Cago9'] +  $row_reporteC['Cago10'] + $row_reporteC['Cago11'];
$semana31C =$row_reporteC['Cago12'] + $row_reporteC['Cago13'] + $row_reporteC['Cago14'] + $row_reporteC['Cago15'] + $row_reporteC['Cago16'] + $row_reporteC['Cago17'] + $row_reporteC['Cago18'];
$semana32C =$row_reporteC['Cago19'] + $row_reporteC['Cago20'] + $row_reporteC['Cago21'] + $row_reporteC['Cago22'] + $row_reporteC['Cago23'] + $row_reporteC['Cago24'] + $row_reporteC['Cago25'];
$semana33C =$row_reporteC['Cago26'] + $row_reporteC['Cago27'] + $row_reporteC['Cago28'] + $row_reporteC['Csep1'] +  $row_reporteC['Csep2'] +  $row_reporteC['Csep3'] +  $row_reporteC['Csep4'];
$semana34C =$row_reporteC['Csep5'] +  $row_reporteC['Csep6'] +  $row_reporteC['Csep7'] + $row_reporteC['Csep8'] +  $row_reporteC['Csep9'] +  $row_reporteC['Csep10'] + $row_reporteC['Csep11'];
$semana35C =$row_reporteC['Csep12'] + $row_reporteC['Csep13'] + $row_reporteC['Csep14'] + $row_reporteC['Csep15'] + $row_reporteC['Csep16'] + $row_reporteC['Csep17'] + $row_reporteC['Csep18'];
$semana36C =$row_reporteC['Csep19'] + $row_reporteC['Csep20'] + $row_reporteC['Csep21'] + $row_reporteC['Csep22'] + $row_reporteC['Csep23'] + $row_reporteC['Csep24'] + $row_reporteC['Csep25'];
$semana37C =$row_reporteC['Csep26'] + $row_reporteC['Csep27'] + $row_reporteC['Csep28'] + $row_reporteC['Csep1'] +  $row_reporteC['Csep2'] +  $row_reporteC['Csep3'] +  $row_reporteC['Csep4'];
$semana38C =$row_reporteC['Csep5'] +  $row_reporteC['Csep6'] +  $row_reporteC['Csep7'] + $row_reporteC['Csep8'] +  $row_reporteC['Csep9'] +  $row_reporteC['Csep10'] + $row_reporteC['Csep11'];
$semana39C =$row_reporteC['Csep12'] + $row_reporteC['Csep13'] + $row_reporteC['Csep14'] + $row_reporteC['Csep15'] + $row_reporteC['Csep16'] + $row_reporteC['Csep17'] + $row_reporteC['Csep18'];
$semana40C =$row_reporteC['Csep19'] + $row_reporteC['Csep20'] + $row_reporteC['Csep21'] + $row_reporteC['Csep22'] + $row_reporteC['Csep23'] + $row_reporteC['Csep24'] + $row_reporteC['Csep25'];
$semana41C =$row_reporteC['Csep26'] + $row_reporteC['Csep27'] + $row_reporteC['Csep28'] + $row_reporteC['Cnov1'] +  $row_reporteC['Cnov2'] +  $row_reporteC['Cnov3'] +  $row_reporteC['Cnov4'];
$semana42C =$row_reporteC['Cnov5'] +  $row_reporteC['Cnov6'] +  $row_reporteC['Cnov7'] + $row_reporteC['Cnov8'] +  $row_reporteC['Cnov9'] +  $row_reporteC['Cnov10'] + $row_reporteC['Cnov11'];
$semana43C =$row_reporteC['Cnov12'] + $row_reporteC['Cnov13'] + $row_reporteC['Cnov14'] + $row_reporteC['Cnov15'] + $row_reporteC['Cnov16'] + $row_reporteC['Cnov17'] + $row_reporteC['Cnov18'];
$semana44C =$row_reporteC['Cnov19'] + $row_reporteC['Cnov20'] + $row_reporteC['Cnov21'] + $row_reporteC['Cnov22'] + $row_reporteC['Cnov23'] + $row_reporteC['Cnov24'] + $row_reporteC['Cnov25'];
$semana45C =$row_reporteC['Cnov26'] + $row_reporteC['Cnov27'] + $row_reporteC['Cnov28'] + $row_reporteC['Cdic1'] +  $row_reporteC['Cdic2'] +  $row_reporteC['Cdic3'] +  $row_reporteC['Cdic4'];
$semana46C =$row_reporteC['Cdic5'] +  $row_reporteC['Cdic6'] +  $row_reporteC['Cdic7'] + $row_reporteC['Cdic8'] +  $row_reporteC['Cdic9'] +  $row_reporteC['Cdic10'] + $row_reporteC['Cdic11'];
$semana47C =$row_reporteC['Cdic12'] + $row_reporteC['Cdic13'] + $row_reporteC['Cdic14'] + $row_reporteC['Cdic15'] + $row_reporteC['Cdic16'] + $row_reporteC['Cdic17'] + $row_reporteC['Cdic18'];
$semana48C =$row_reporteC['Cdic19'] + $row_reporteC['Cdic20'] + $row_reporteC['Cdic21'] + $row_reporteC['Cdic22'] + $row_reporteC['Cdic23'] + $row_reporteC['Cdic24'] + $row_reporteC['Cdic25'];
$semana49C =$row_reporteC['Cdic26'] + $row_reporteC['Cdic27'] + $row_reporteC['Cdic28'];



$plazas_semana1A = round($semana1A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana2A = round($semana2A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana3A = round($semana3A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana4A = round($semana4A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana5A = round($semana5A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana6A = round($semana6A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana7A = round($semana7A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana8A = round($semana8A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana9A = round($semana9A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana10A = round($semana10A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana11A = round($semana11A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana12A = round($semana12A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana13A = round($semana13A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana14A = round($semana14A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana15A = round($semana15A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana16A = round($semana16A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana17A = round($semana17A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana18A = round($semana18A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana19A = round($semana19A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana20A = round($semana20A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana21A = round($semana21A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana22A = round($semana22A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana23A = round($semana23A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana24A = round($semana24A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana25A = round($semana25A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana26A = round($semana26A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana27A = round($semana27A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana28A = round($semana28A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana29A = round($semana29A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana30A = round($semana30A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana31A = round($semana31A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana32A = round($semana32A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana33A = round($semana33A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana34A = round($semana34A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana35A = round($semana35A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana36A = round($semana36A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana37A = round($semana37A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana38A = round($semana38A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana39A = round($semana39A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana40A = round($semana40A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana41A = round($semana41A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana42A = round($semana42A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana43A = round($semana43A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana44A = round($semana44A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana45A = round($semana45A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana46A = round($semana46A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana47A = round($semana47A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana48A = round($semana48A / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana49A = round($semana49A / 6 , 0, PHP_ROUND_HALF_UP);

$plazas_semana1B = round($semana1B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana2B = round($semana2B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana3B = round($semana3B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana4B = round($semana4B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana5B = round($semana5B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana6B = round($semana6B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana7B = round($semana7B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana8B = round($semana8B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana9B = round($semana9B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana10B = round($semana10B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana11B = round($semana11B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana12B = round($semana12B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana13B = round($semana13B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana14B = round($semana14B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana15B = round($semana15B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana16B = round($semana16B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana17B = round($semana17B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana18B = round($semana18B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana19B = round($semana19B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana20B = round($semana20B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana21B = round($semana21B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana22B = round($semana22B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana23B = round($semana23B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana24B = round($semana24B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana25B = round($semana25B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana26B = round($semana26B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana27B = round($semana27B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana28B = round($semana28B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana29B = round($semana29B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana30B = round($semana30B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana31B = round($semana31B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana32B = round($semana32B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana33B = round($semana33B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana34B = round($semana34B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana35B = round($semana35B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana36B = round($semana36B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana37B = round($semana37B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana38B = round($semana38B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana39B = round($semana39B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana40B = round($semana40B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana41B = round($semana41B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana42B = round($semana42B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana43B = round($semana43B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana44B = round($semana44B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana45B = round($semana45B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana46B = round($semana46B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana47B = round($semana47B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana48B = round($semana48B / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana49B = round($semana49B / 6 , 0, PHP_ROUND_HALF_UP);

$plazas_semana1C = round($semana1C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana2C = round($semana2C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana3C = round($semana3C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana4C = round($semana4C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana5C = round($semana5C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana6C = round($semana6C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana7C = round($semana7C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana8C = round($semana8C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana9C = round($semana9C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana10C = round($semana10C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana11C = round($semana11C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana12C = round($semana12C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana13C = round($semana13C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana14C = round($semana14C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana15C = round($semana15C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana16C = round($semana16C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana17C = round($semana17C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana18C = round($semana18C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana19C = round($semana19C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana20C = round($semana20C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana21C = round($semana21C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana22C = round($semana22C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana23C = round($semana23C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana24C = round($semana24C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana25C = round($semana25C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana26C = round($semana26C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana27C = round($semana27C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana28C = round($semana28C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana29C = round($semana29C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana30C = round($semana30C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana31C = round($semana31C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana32C = round($semana32C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana33C = round($semana33C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana34C = round($semana34C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana35C = round($semana35C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana36C = round($semana36C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana37C = round($semana37C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana38C = round($semana38C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana39C = round($semana39C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana40C = round($semana40C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana41C = round($semana41C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana42C = round($semana42C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana43C = round($semana43C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana44C = round($semana44C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana45C = round($semana45C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana46C = round($semana46C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana47C = round($semana47C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana48C = round($semana48C / 6 , 0, PHP_ROUND_HALF_UP);
$plazas_semana49C = round($semana49C / 6 , 0, PHP_ROUND_HALF_UP);


$plazasa_mes1A =  max($plazas_semana1A,  $plazas_semana2A,  $plazas_semana3A,  $plazas_semana4A);
$plazasa_mes2A =  max($plazas_semana5A,  $plazas_semana6A,  $plazas_semana7A,  $plazas_semana8A);
$plazasa_mes3A =  max($plazas_semana9A,  $plazas_semana10A, $plazas_semana11A, $plazas_semana12A);
$plazasa_mes4A =  max($plazas_semana13A, $plazas_semana14A, $plazas_semana15A, $plazas_semana16A);
$plazasa_mes5A =  max($plazas_semana17A, $plazas_semana18A, $plazas_semana19A, $plazas_semana20A);
$plazasa_mes6A =  max($plazas_semana21A, $plazas_semana22A, $plazas_semana23A, $plazas_semana24A);
$plazasa_mes7A =  max($plazas_semana25A, $plazas_semana26A, $plazas_semana27A, $plazas_semana28A);
$plazasa_mes8A =  max($plazas_semana29A, $plazas_semana30A, $plazas_semana31A, $plazas_semana32A);
$plazasa_mes9A =  max($plazas_semana33A, $plazas_semana34A, $plazas_semana35A, $plazas_semana36A);
$plazasa_mes10A = max($plazas_semana37A, $plazas_semana38A, $plazas_semana39A, $plazas_semana40A);
$plazasa_mes11A = max($plazas_semana41A, $plazas_semana42A, $plazas_semana43A, $plazas_semana44A);
$plazasa_mes12A = max($plazas_semana45A, $plazas_semana46A, $plazas_semana47A, $plazas_semana48A);

$plazasa_mes1B =  max($plazas_semana1B,  $plazas_semana2B,  $plazas_semana3B,  $plazas_semana4B);
$plazasa_mes2B =  max($plazas_semana5B,  $plazas_semana6B,  $plazas_semana7B,  $plazas_semana8B);
$plazasa_mes3B =  max($plazas_semana9B,  $plazas_semana10B, $plazas_semana11B, $plazas_semana12B);
$plazasa_mes4B =  max($plazas_semana13B, $plazas_semana14B, $plazas_semana15B, $plazas_semana16B);
$plazasa_mes5B =  max($plazas_semana17B, $plazas_semana18B, $plazas_semana19B, $plazas_semana20B);
$plazasa_mes6B =  max($plazas_semana21B, $plazas_semana22B, $plazas_semana23B, $plazas_semana24B);
$plazasa_mes7B =  max($plazas_semana25B, $plazas_semana26B, $plazas_semana27B, $plazas_semana28B);
$plazasa_mes8B =  max($plazas_semana29B, $plazas_semana30B, $plazas_semana31B, $plazas_semana32B);
$plazasa_mes9B =  max($plazas_semana33B, $plazas_semana34B, $plazas_semana35B, $plazas_semana36B);
$plazasa_mes10B = max($plazas_semana37B, $plazas_semana38B, $plazas_semana39B, $plazas_semana40B);
$plazasa_mes11B = max($plazas_semana41B, $plazas_semana42B, $plazas_semana43B, $plazas_semana44B);
$plazasa_mes12B = max($plazas_semana45B, $plazas_semana46B, $plazas_semana47B, $plazas_semana48B);

$plazasa_mes1C =  max($plazas_semana1C,  $plazas_semana2C,  $plazas_semana3C,  $plazas_semana4C);
$plazasa_mes2C =  max($plazas_semana5C,  $plazas_semana6C,  $plazas_semana7C,  $plazas_semana8C);
$plazasa_mes3C =  max($plazas_semana9C,  $plazas_semana10C, $plazas_semana11C, $plazas_semana12C);
$plazasa_mes4C =  max($plazas_semana13C, $plazas_semana14C, $plazas_semana15C, $plazas_semana16C);
$plazasa_mes5C =  max($plazas_semana17C, $plazas_semana18C, $plazas_semana19C, $plazas_semana20C);
$plazasa_mes6C =  max($plazas_semana21C, $plazas_semana22C, $plazas_semana23C, $plazas_semana24C);
$plazasa_mes7C =  max($plazas_semana25C, $plazas_semana26C, $plazas_semana27C, $plazas_semana28C);
$plazasa_mes8C =  max($plazas_semana29C, $plazas_semana30C, $plazas_semana31C, $plazas_semana32C);
$plazasa_mes9C =  max($plazas_semana33C, $plazas_semana34C, $plazas_semana35C, $plazas_semana36C);
$plazasa_mes10C = max($plazas_semana37C, $plazas_semana38C, $plazas_semana39C, $plazas_semana40C);
$plazasa_mes11C = max($plazas_semana41C, $plazas_semana42C, $plazas_semana43C, $plazas_semana44C);
$plazasa_mes12C = max($plazas_semana45C, $plazas_semana46C, $plazas_semana47C, $plazas_semana48C);

$totalventas = $plazasa_mes1C + $plazasa_mes2C + $plazasa_mes3C + $plazasa_mes4C + $plazasa_mes5C + $plazasa_mes6C + $plazasa_mes7C + $plazasa_mes8C + $plazasa_mes9C + $plazasa_mes10C + $plazasa_mes11C + $plazasa_mes12C;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
	<meta name="robots" content="noindex" />

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<!-- /theme JS files -->
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>
<?php require_once('assets/mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">		
			<?php require_once('assets/pheader.php'); ?>

			<!-- Content area -->
				<div class="content">
				
					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Reporte Plazas Pull</h6>
								</div>

								<div class="panel-body">
								<p>A continuación se muestra la cantidad de Plazas Pull requeridas por la Sucursal <b><?php echo $row_matriz['matriz']; ?></b>.</p>

							<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-info"> 
                                    <th>Matriz</th>
                                    <th>Área</th>
                                    <th>Mes</th>
                                    <th colspan="4" class="text text-center">Semanas</th>
                                    <th>Plazas Pull</th>
                                    <th>Puesto</th>
                                  </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>ENERO</td>
                                      <td><b>1:</b> <?php echo $plazas_semana1A; ?></td>
                                      <td><b>2:</b> <?php echo $plazas_semana2A; ?></td>
                                      <td><b>3:</b> <?php echo $plazas_semana3A; ?></td>
                                      <td><b>4:</b> <?php echo $plazas_semana4A; ?></td>
									  <td><?php echo $plazasa_mes1A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>FEBRERO</td>
                                      <td><b>5:</b> <?php echo $plazas_semana5A; ?></td>
                                      <td><b>6:</b> <?php echo $plazas_semana6A; ?></td>
                                      <td><b>7:</b> <?php echo $plazas_semana7A; ?></td>
                                      <td><b>8:</b> <?php echo $plazas_semana8A; ?></td>
									  <td><?php echo $plazasa_mes2A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>MARZO</td>
                                      <td><b>9:</b> <?php echo $plazas_semana9A; ?></td>
                                      <td><b>10:</b> <?php echo $plazas_semana10A; ?></td>
                                      <td><b>11:</b> <?php echo $plazas_semana11A; ?></td>
                                      <td><b>12:</b> <?php echo $plazas_semana12A; ?></td>
									  <td><?php echo $plazasa_mes3A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>ABRIL</td>
                                      <td><b>13:</b> <?php echo $plazas_semana13A; ?></td>
                                      <td><b>14:</b> <?php echo $plazas_semana14A; ?></td>
                                      <td><b>15:</b> <?php echo $plazas_semana15A; ?></td>
                                      <td><b>16:</b> <?php echo $plazas_semana16A; ?></td>
									  <td><?php echo $plazasa_mes4A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>MAYO</td>
                                      <td><b>17:</b> <?php echo $plazas_semana17A; ?></td>
                                      <td><b>18:</b> <?php echo $plazas_semana18A; ?></td>
                                      <td><b>19:</b> <?php echo $plazas_semana19A; ?></td>
                                      <td><b>20:</b> <?php echo $plazas_semana20A; ?></td>
									  <td><?php echo $plazasa_mes5A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>JUNIO</td>
                                      <td><b>21:</b> <?php echo $plazas_semana21A; ?></td>
                                      <td><b>22:</b> <?php echo $plazas_semana22A; ?></td>
                                      <td><b>23:</b> <?php echo $plazas_semana23A; ?></td>
                                      <td><b>24:</b> <?php echo $plazas_semana24A; ?></td>
									  <td><?php echo $plazasa_mes6A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>JULIO</td>
                                      <td><b>25:</b> <?php echo $plazas_semana25A; ?></td>
                                      <td><b>26:</b> <?php echo $plazas_semana26A; ?></td>
                                      <td><b>27:</b> <?php echo $plazas_semana27A; ?></td>
                                      <td><b>28:</b> <?php echo $plazas_semana28A; ?></td>
									  <td><?php echo $plazasa_mes7A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>AGOSTO</td>
                                      <td><b>29:</b> <?php echo $plazas_semana29A; ?></td>
                                      <td><b>30:</b> <?php echo $plazas_semana30A; ?></td>
                                      <td><b>31:</b> <?php echo $plazas_semana31A; ?></td>
                                      <td><b>32:</b> <?php echo $plazas_semana32A; ?></td>
									  <td><?php echo $plazasa_mes8A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>SEPTIEMBRE</td>
                                      <td><b>33:</b> <?php echo $plazas_semana33A; ?></td>
                                      <td><b>24:</b> <?php echo $plazas_semana34A; ?></td>
                                      <td><b>35:</b> <?php echo $plazas_semana35A; ?></td>
                                      <td><b>36:</b> <?php echo $plazas_semana36A; ?></td>
									  <td><?php echo $plazasa_mes9A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>OCTUBRE</td>
                                      <td><b>37:</b> <?php echo $plazas_semana37A; ?></td>
                                      <td><b>38:</b> <?php echo $plazas_semana38A; ?></td>
                                      <td><b>39:</b> <?php echo $plazas_semana39A; ?></td>
                                      <td><b>40:</b> <?php echo $plazas_semana40A; ?></td>
									  <td><?php echo $plazasa_mes10A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>NOVIEMBRE</td>
                                      <td><b>41:</b> <?php echo $plazas_semana41A; ?></td>
                                      <td><b>42:</b> <?php echo $plazas_semana42A; ?></td>
                                      <td><b>43:</b> <?php echo $plazas_semana43A; ?></td>
                                      <td><b>44:</b> <?php echo $plazas_semana44A; ?></td>
									  <td><?php echo $plazasa_mes11A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>ALMACEN</td>
                                      <td>DICIEMBRE</td>
                                      <td><b>45:</b> <?php echo $plazas_semana45A; ?></td>
                                      <td><b>46:</b> <?php echo $plazas_semana46A; ?></td>
                                      <td><b>47:</b> <?php echo $plazas_semana47A; ?></td>
                                      <td><b>48:</b> <?php echo $plazas_semana48A; ?></td>
									  <td><?php echo $plazasa_mes12A; ?></td>
                                      <td>Despachador</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>ENERO</td>
                                      <td><b>1:</b> <?php echo $plazas_semana1B; ?></td>
                                      <td><b>2:</b> <?php echo $plazas_semana2B; ?></td>
                                      <td><b>3:</b> <?php echo $plazas_semana3B; ?></td>
                                      <td><b>4:</b> <?php echo $plazas_semana4B; ?></td>
									  <td><?php echo $plazasa_mes1B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>FEBRERO</td>
                                      <td><b>5:</b> <?php echo $plazas_semana5B; ?></td>
                                      <td><b>6:</b> <?php echo $plazas_semana6B; ?></td>
                                      <td><b>7:</b> <?php echo $plazas_semana7B; ?></td>
                                      <td><b>8:</b> <?php echo $plazas_semana8B; ?></td>
									  <td><?php echo $plazasa_mes2B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>MARZO</td>
                                      <td><b>9:</b> <?php echo $plazas_semana9B; ?></td>
                                      <td><b>10:</b> <?php echo $plazas_semana10B; ?></td>
                                      <td><b>11:</b> <?php echo $plazas_semana11B; ?></td>
                                      <td><b>12:</b> <?php echo $plazas_semana12B; ?></td>
									  <td><?php echo $plazasa_mes3B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>ABRIL</td>
                                      <td><b>13:</b> <?php echo $plazas_semana13B; ?></td>
                                      <td><b>14:</b> <?php echo $plazas_semana14B; ?></td>
                                      <td><b>15:</b> <?php echo $plazas_semana15B; ?></td>
                                      <td><b>16:</b> <?php echo $plazas_semana16B; ?></td>
									  <td><?php echo $plazasa_mes4B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>MAYO</td>
                                      <td><b>17:</b> <?php echo $plazas_semana17B; ?></td>
                                      <td><b>18:</b> <?php echo $plazas_semana18B; ?></td>
                                      <td><b>19:</b> <?php echo $plazas_semana19B; ?></td>
                                      <td><b>20:</b> <?php echo $plazas_semana20B; ?></td>
									  <td><?php echo $plazasa_mes5B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>JUNIO</td>
                                      <td><b>21:</b> <?php echo $plazas_semana21B; ?></td>
                                      <td><b>22:</b> <?php echo $plazas_semana22B; ?></td>
                                      <td><b>23:</b> <?php echo $plazas_semana23B; ?></td>
                                      <td><b>24:</b> <?php echo $plazas_semana24B; ?></td>
									  <td><?php echo $plazasa_mes6B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>JULIO</td>
                                      <td><b>25:</b> <?php echo $plazas_semana25B; ?></td>
                                      <td><b>26:</b> <?php echo $plazas_semana26B; ?></td>
                                      <td><b>27:</b> <?php echo $plazas_semana27B; ?></td>
                                      <td><b>28:</b> <?php echo $plazas_semana28B; ?></td>
									  <td><?php echo $plazasa_mes7B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>AGOSTO</td>
                                      <td><b>29:</b> <?php echo $plazas_semana29B; ?></td>
                                      <td><b>30:</b> <?php echo $plazas_semana30B; ?></td>
                                      <td><b>31:</b> <?php echo $plazas_semana31B; ?></td>
                                      <td><b>32:</b> <?php echo $plazas_semana32B; ?></td>
									  <td><?php echo $plazasa_mes8B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>SEPTIEMBRE</td>
                                      <td><b>33:</b> <?php echo $plazas_semana33B; ?></td>
                                      <td><b>24:</b> <?php echo $plazas_semana34B; ?></td>
                                      <td><b>35:</b> <?php echo $plazas_semana35B; ?></td>
                                      <td><b>36:</b> <?php echo $plazas_semana36B; ?></td>
									  <td><?php echo $plazasa_mes9B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>OCTUBRE</td>
                                      <td><b>37:</b> <?php echo $plazas_semana37B; ?></td>
                                      <td><b>38:</b> <?php echo $plazas_semana38B; ?></td>
                                      <td><b>39:</b> <?php echo $plazas_semana39B; ?></td>
                                      <td><b>40:</b> <?php echo $plazas_semana40B; ?></td>
									  <td><?php echo $plazasa_mes10B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>NOVIEMBRE</td>
                                      <td><b>41:</b> <?php echo $plazas_semana41B; ?></td>
                                      <td><b>42:</b> <?php echo $plazas_semana42B; ?></td>
                                      <td><b>43:</b> <?php echo $plazas_semana43B; ?></td>
                                      <td><b>44:</b> <?php echo $plazas_semana44B; ?></td>
									  <td><?php echo $plazasa_mes11B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>DISTRIBUCION</td>
                                      <td>DICIEMBRE</td>
                                      <td><b>45:</b> <?php echo $plazas_semana45B; ?></td>
                                      <td><b>46:</b> <?php echo $plazas_semana46B; ?></td>
                                      <td><b>47:</b> <?php echo $plazas_semana47B; ?></td>
                                      <td><b>48:</b> <?php echo $plazas_semana48B; ?></td>
									  <td><?php echo $plazasa_mes12B; ?></td>
                                      <td>Chofer Torton</td>
                                    </tr>

<?php if ($totalventas > 0) { ?>	

                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>ENERO</td>
                                      <td><b>1:</b> <?php echo $plazas_semana1C; ?></td>
                                      <td><b>2:</b> <?php echo $plazas_semana2C; ?></td>
                                      <td><b>3:</b> <?php echo $plazas_semana3C; ?></td>
                                      <td><b>4:</b> <?php echo $plazas_semana4C; ?></td>
									  <td><?php echo $plazasa_mes1C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>FEBRERO</td>
                                      <td><b>5:</b> <?php echo $plazas_semana5C; ?></td>
                                      <td><b>6:</b> <?php echo $plazas_semana6C; ?></td>
                                      <td><b>7:</b> <?php echo $plazas_semana7C; ?></td>
                                      <td><b>8:</b> <?php echo $plazas_semana8C; ?></td>
									  <td><?php echo $plazasa_mes2C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>MARZO</td>
                                      <td><b>9:</b> <?php echo $plazas_semana9C; ?></td>
                                      <td><b>10:</b> <?php echo $plazas_semana10C; ?></td>
                                      <td><b>11:</b> <?php echo $plazas_semana11C; ?></td>
                                      <td><b>12:</b> <?php echo $plazas_semana12C; ?></td>
									  <td><?php echo $plazasa_mes3C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>ABRIL</td>
                                      <td><b>13:</b> <?php echo $plazas_semana13C; ?></td>
                                      <td><b>14:</b> <?php echo $plazas_semana14C; ?></td>
                                      <td><b>15:</b> <?php echo $plazas_semana15C; ?></td>
                                      <td><b>16:</b> <?php echo $plazas_semana16C; ?></td>
									  <td><?php echo $plazasa_mes4C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>MAYO</td>
                                      <td><b>17:</b> <?php echo $plazas_semana17C; ?></td>
                                      <td><b>18:</b> <?php echo $plazas_semana18C; ?></td>
                                      <td><b>19:</b> <?php echo $plazas_semana19C; ?></td>
                                      <td><b>20:</b> <?php echo $plazas_semana20C; ?></td>
									  <td><?php echo $plazasa_mes5C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>JUNIO</td>
                                      <td><b>21:</b> <?php echo $plazas_semana21C; ?></td>
                                      <td><b>22:</b> <?php echo $plazas_semana22C; ?></td>
                                      <td><b>23:</b> <?php echo $plazas_semana23C; ?></td>
                                      <td><b>24:</b> <?php echo $plazas_semana24C; ?></td>
									  <td><?php echo $plazasa_mes6C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>JULIO</td>
                                      <td><b>25:</b> <?php echo $plazas_semana25C; ?></td>
                                      <td><b>26:</b> <?php echo $plazas_semana26C; ?></td>
                                      <td><b>27:</b> <?php echo $plazas_semana27C; ?></td>
                                      <td><b>28:</b> <?php echo $plazas_semana28C; ?></td>
									  <td><?php echo $plazasa_mes7C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>AGOSTO</td>
                                      <td><b>29:</b> <?php echo $plazas_semana29C; ?></td>
                                      <td><b>30:</b> <?php echo $plazas_semana30C; ?></td>
                                      <td><b>31:</b> <?php echo $plazas_semana31C; ?></td>
                                      <td><b>32:</b> <?php echo $plazas_semana32C; ?></td>
									  <td><?php echo $plazasa_mes8C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>SEPTIEMBRE</td>
                                      <td><b>33:</b> <?php echo $plazas_semana33C; ?></td>
                                      <td><b>24:</b> <?php echo $plazas_semana34C; ?></td>
                                      <td><b>35:</b> <?php echo $plazas_semana35C; ?></td>
                                      <td><b>36:</b> <?php echo $plazas_semana36C; ?></td>
									  <td><?php echo $plazasa_mes9C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>OCTUBRE</td>
                                      <td><b>37:</b> <?php echo $plazas_semana37C; ?></td>
                                      <td><b>38:</b> <?php echo $plazas_semana38C; ?></td>
                                      <td><b>39:</b> <?php echo $plazas_semana39C; ?></td>
                                      <td><b>40:</b> <?php echo $plazas_semana40C; ?></td>
									  <td><?php echo $plazasa_mes10C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>NOVIEMBRE</td>
                                      <td><b>41:</b> <?php echo $plazas_semana41C; ?></td>
                                      <td><b>42:</b> <?php echo $plazas_semana42C; ?></td>
                                      <td><b>43:</b> <?php echo $plazas_semana43C; ?></td>
                                      <td><b>44:</b> <?php echo $plazas_semana44C; ?></td>
									  <td><?php echo $plazasa_mes11C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                    <tr>
                                      <td><?php echo $row_matriz['matriz']; ?></td>
                                      <td>VENTAS</td>
                                      <td>DICIEMBRE</td>
                                      <td><b>45:</b> <?php echo $plazas_semana45C; ?></td>
                                      <td><b>46:</b> <?php echo $plazas_semana46C; ?></td>
                                      <td><b>47:</b> <?php echo $plazas_semana47C; ?></td>
                                      <td><b>48:</b> <?php echo $plazas_semana48C; ?></td>
									  <td><?php echo $plazasa_mes12C; ?></td>
                                      <td>Rep. de Ventas C</td>
                                    </tr>
                                  </tbody>
<?php } ?>	

                    			<tfoot>
                                	<tr> 
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                  </tr>
                                </tfoot>
                                </table>
								

								
								</div>
							</div>
						</div>
                                    
					<!-- /Contenido -->

					<!-- Footer -->
					<div class="footer text-muted">
						&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
</div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->
		

</div>
	<!-- /page container -->
</body>
</html>