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
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$fecha = date("Y-m-d"); // la fecha actual
$mes_actual = date("m"); // la fecha actual
if ($mes_actual == 01) {$mes_actual = 12;} else {$mes_actual = $mes_actual - 1;}

//echo $fecha_inicio_mes_ok; 

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
$IDmatriz = '2,13,16,17,18,22,26,28,29';

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

if(isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {
$_SESSION['el_mes'] = $_POST['el_mes']; } else { $_SESSION['el_mes'] = $mes_actual;}

if(isset($_POST['el_anio']) && ($_POST['el_anio']  > 0)) {
$_SESSION['el_anio'] = $_POST['el_anio']; } else { $_SESSION['el_anio'] = $anio;}

$el_mes = $_SESSION['el_mes'];
$el_anio = $_SESSION['el_anio'];
$anio_actual = $el_anio;
$anio_anterior = $el_anio - 1; // la fecha actual

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_anio = "SELECT * FROM vac_anios ORDER BY vac_anios.IDanio DESC";
$anio = mysql_query($query_anio, $vacantes) or die(mysql_error());
$row_anio = mysql_fetch_assoc($anio);
$totalRows_anio = mysql_num_rows($anio);

//objetivo y total año anterior
mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT * FROM ind_objetivo WHERE IDmatriz in ($IDmatriz) AND anio = $el_anio";
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_resultados_anterior = "SELECT * FROM ind_objetivo WHERE IDmatriz in ($IDmatriz) AND anio = $anio_anterior";
$resultados_anterior = mysql_query($query_resultados_anterior, $vacantes) or die(mysql_error());
$row_resultados_anterior = mysql_fetch_assoc($resultados_anterior);
$totalRows_resultados_anterior = mysql_num_rows($resultados_anterior);



// Resultado Mes 1 año actual
$fini_ms1 = new DateTime($anio_actual . '-01-01');
$fini_ms1->modify('first day of this month');
$fini_ms1k = $fini_ms1->format('Y/m/d'); 

$fter_ms1 = new DateTime($anio_actual . '-01-01');
$fter_ms1->modify('last day of this month');
$fter_ms1k = $fter_ms1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms1 = "SELECT 
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms1k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms1k')  AND ind_bajas.IDmotivo < 15";
$res_ms1 = mysql_query($query_res_ms1, $vacantes) or die(mysql_error());
$row_res_ms1 = mysql_fetch_assoc($res_ms1);
$totalRows_res_ms1 = mysql_num_rows($res_ms1);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 1 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms1 = mysql_query($query_bja_ms1, $vacantes) or die(mysql_error());
$row_bja_ms1 = mysql_fetch_assoc($bja_ms1);
$totalRows_bja_ms1 = mysql_num_rows($bja_ms1);

$RotTotalM1 =  $row_bja_ms1['TOTAL'] / $row_res_ms1['TOTAL'];

// Resultado Mes 2 año actual
$fini_ms2 = new DateTime($anio_actual . '-02-01');
$fini_ms2->modify('first day of this month');
$fini_ms2k = $fini_ms2->format('Y/m/d'); 

$fter_ms2 = new DateTime($anio_actual . '-02-01');
$fter_ms2->modify('last day of this month');
$fter_ms2k = $fter_ms2->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms2 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms2k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms2k')  AND ind_bajas.IDmotivo < 15";
$res_ms2 = mysql_query($query_res_ms2, $vacantes) or die(mysql_error());
$row_res_ms2 = mysql_fetch_assoc($res_ms2);
$totalRows_res_ms2 = mysql_num_rows($res_ms2);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 2 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms2 = mysql_query($query_bja_ms2, $vacantes) or die(mysql_error());
$row_bja_ms2 = mysql_fetch_assoc($bja_ms2);
$totalRows_bja_ms2 = mysql_num_rows($bja_ms2);

$RotTotalM2 =  $row_bja_ms2['TOTAL'] / $row_res_ms2['TOTAL'];

// Resultado Mes 3 año actual
$fini_ms3 = new DateTime($anio_actual . '-03-01');
$fini_ms3->modify('first day of this month');
$fini_ms3k = $fini_ms3->format('Y/m/d'); 

$fter_ms3 = new DateTime($anio_actual . '-03-01');
$fter_ms3->modify('last day of this month');
$fter_ms3k = $fter_ms3->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms3 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms3k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms3k')  AND ind_bajas.IDmotivo < 15";
$res_ms3 = mysql_query($query_res_ms3, $vacantes) or die(mysql_error());
$row_res_ms3 = mysql_fetch_assoc($res_ms3);
$totalRows_res_ms3 = mysql_num_rows($res_ms3);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms3 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 3 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms3 = mysql_query($query_bja_ms3, $vacantes) or die(mysql_error());
$row_bja_ms3 = mysql_fetch_assoc($bja_ms3);
$totalRows_bja_ms3 = mysql_num_rows($bja_ms3);

$RotTotalM3 =  $row_bja_ms3['TOTAL'] / $row_res_ms3['TOTAL'];

// Resultado Mes 4 año actual
$fini_ms4 = new DateTime($anio_actual . '-04-01');
$fini_ms4->modify('first day of this month');
$fini_ms4k = $fini_ms4->format('Y/m/d'); 

$fter_ms4 = new DateTime($anio_actual . '-04-01');
$fter_ms4->modify('last day of this month');
$fter_ms4k = $fter_ms4->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms4 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms4k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms4k')  AND ind_bajas.IDmotivo < 15";
$res_ms4 = mysql_query($query_res_ms4, $vacantes) or die(mysql_error());
$row_res_ms4 = mysql_fetch_assoc($res_ms4);
$totalRows_res_ms4 = mysql_num_rows($res_ms4);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms4 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 4 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms4 = mysql_query($query_bja_ms4, $vacantes) or die(mysql_error());
$row_bja_ms4 = mysql_fetch_assoc($bja_ms4);
$totalRows_bja_ms4 = mysql_num_rows($bja_ms4);

$RotTotalM4 =  $row_bja_ms4['TOTAL'] / $row_res_ms4['TOTAL'];

// Resultado Mes 5 año actual
$fini_ms5 = new DateTime($anio_actual . '-05-01');
$fini_ms5->modify('first day of this month');
$fini_ms5k = $fini_ms5->format('Y/m/d'); 

$fter_ms5 = new DateTime($anio_actual . '-05-01');
$fter_ms5->modify('last day of this month');
$fter_ms5k = $fter_ms5->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms5 = "SELECT 
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms5k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms5k')  AND ind_bajas.IDmotivo < 15";
$res_ms5 = mysql_query($query_res_ms5, $vacantes) or die(mysql_error());
$row_res_ms5 = mysql_fetch_assoc($res_ms5);
$totalRows_res_ms5 = mysql_num_rows($res_ms5);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms5 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 5 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms5 = mysql_query($query_bja_ms5, $vacantes) or die(mysql_error());
$row_bja_ms5 = mysql_fetch_assoc($bja_ms5);
$totalRows_bja_ms5 = mysql_num_rows($bja_ms5);

$RotTotalM5 =  $row_bja_ms5['TOTAL'] / $row_res_ms5['TOTAL'];

// Resultado Mes 6 año actual
$fini_ms6 = new DateTime($anio_actual . '-06-01');
$fini_ms6->modify('first day of this month');
$fini_ms6k = $fini_ms6->format('Y/m/d'); 

$fter_ms6 = new DateTime($anio_actual . '-06-01');
$fter_ms6->modify('last day of this month');
$fter_ms6k = $fter_ms6->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms6 = "SELECT 
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms6k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms6k')  AND ind_bajas.IDmotivo < 15";
$res_ms6 = mysql_query($query_res_ms6, $vacantes) or die(mysql_error());
$row_res_ms6 = mysql_fetch_assoc($res_ms6);
$totalRows_res_ms6 = mysql_num_rows($res_ms6);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms6 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 6 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms6 = mysql_query($query_bja_ms6, $vacantes) or die(mysql_error());
$row_bja_ms6 = mysql_fetch_assoc($bja_ms6);
$totalRows_bja_ms6 = mysql_num_rows($bja_ms6);

$RotTotalM6 =  $row_bja_ms6['TOTAL'] / $row_res_ms6['TOTAL'];

// Resultado Mes 7 año actual
$fini_ms7 = new DateTime($anio_actual . '-07-01');
$fini_ms7->modify('first day of this month');
$fini_ms7k = $fini_ms7->format('Y/m/d'); 

$fter_ms7 = new DateTime($anio_actual . '-07-01');
$fter_ms7->modify('last day of this month');
$fter_ms7k = $fter_ms7->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms7 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms7k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms7k')  AND ind_bajas.IDmotivo < 15";
$res_ms7 = mysql_query($query_res_ms7, $vacantes) or die(mysql_error());
$row_res_ms7 = mysql_fetch_assoc($res_ms7);
$totalRows_res_ms7 = mysql_num_rows($res_ms7);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms7 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 7 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms7 = mysql_query($query_bja_ms7, $vacantes) or die(mysql_error());
$row_bja_ms7 = mysql_fetch_assoc($bja_ms7);
$totalRows_bja_ms7 = mysql_num_rows($bja_ms7);

$row_res_ms7['TOTAL'] = $row_res_ms7['TOTAL'];

$RotTotalM7 =  $row_bja_ms7['TOTAL'] / $row_res_ms7['TOTAL'];


// Resultado Mes 8 año actual
$fini_ms8 = new DateTime($anio_actual . '-08-01');
$fini_ms8->modify('first day of this month');
$fini_ms8k = $fini_ms8->format('Y/m/d'); 

$fter_ms8 = new DateTime($anio_actual . '-08-01');
$fter_ms8->modify('last day of this month');
$fter_ms8k = $fter_ms8->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms8 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms8k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms8k')  AND ind_bajas.IDmotivo < 15";
$res_ms8 = mysql_query($query_res_ms8, $vacantes) or die(mysql_error());
$row_res_ms8 = mysql_fetch_assoc($res_ms8);
$totalRows_res_ms8 = mysql_num_rows($res_ms8);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms8 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 8 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms8 = mysql_query($query_bja_ms8, $vacantes) or die(mysql_error());
$row_bja_ms8 = mysql_fetch_assoc($bja_ms8);
$totalRows_bja_ms8 = mysql_num_rows($bja_ms8);

$RotTotalM8 =  $row_bja_ms8['TOTAL'] / $row_res_ms8['TOTAL'];

// Resultado Mes 9 año actual
$fini_ms9 = new DateTime($anio_actual . '-09-01');
$fini_ms9->modify('first day of this month');
$fini_ms9k = $fini_ms9->format('Y/m/d'); 

$fter_ms9 = new DateTime($anio_actual . '-09-01');
$fter_ms9->modify('last day of this month');
$fter_ms9k = $fter_ms9->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms9 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms9k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms9k')  AND ind_bajas.IDmotivo < 15";
$res_ms9 = mysql_query($query_res_ms9, $vacantes) or die(mysql_error());
$row_res_ms9 = mysql_fetch_assoc($res_ms9);
$totalRows_res_ms9 = mysql_num_rows($res_ms9);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms9 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 9 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms9 = mysql_query($query_bja_ms9, $vacantes) or die(mysql_error());
$row_bja_ms9 = mysql_fetch_assoc($bja_ms9);
$totalRows_bja_ms9 = mysql_num_rows($bja_ms9);

$RotTotalM9 =  $row_bja_ms9['TOTAL'] / $row_res_ms9['TOTAL'];

// Resultado Mes 10 año actual
$fini_ms10 = new DateTime($anio_actual . '-10-01');
$fini_ms10->modify('first day of this month');
$fini_ms10k = $fini_ms10->format('Y/m/d'); 

$fter_ms10 = new DateTime($anio_actual . '-10-01');
$fter_ms10->modify('last day of this month');
$fter_ms10k = $fter_ms10->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms10 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms10k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms10k')  AND ind_bajas.IDmotivo < 15";
$res_ms10 = mysql_query($query_res_ms10, $vacantes) or die(mysql_error());
$row_res_ms10 = mysql_fetch_assoc($res_ms10);
$totalRows_res_ms10 = mysql_num_rows($res_ms10);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms10 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 10 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms10 = mysql_query($query_bja_ms10, $vacantes) or die(mysql_error());
$row_bja_ms10 = mysql_fetch_assoc($bja_ms10);
$totalRows_bja_ms10 = mysql_num_rows($bja_ms10);

$RotTotalM10 =  $row_bja_ms10['TOTAL'] / $row_res_ms10['TOTAL'];

// Resultado Mes 11 año actual
$fini_ms11 = new DateTime($anio_actual . '-11-01');
$fini_ms11->modify('first day of this month');
$fini_ms11k = $fini_ms11->format('Y/m/d'); 

$fter_ms11 = new DateTime($anio_actual . '-11-01');
$fter_ms11->modify('last day of this month');
$fter_ms11k = $fter_ms11->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms11 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms11k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms11k')  AND ind_bajas.IDmotivo < 15";
$res_ms11 = mysql_query($query_res_ms11, $vacantes) or die(mysql_error());
$row_res_ms11 = mysql_fetch_assoc($res_ms11);
$totalRows_res_ms11 = mysql_num_rows($res_ms11);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms11 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 11 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms11 = mysql_query($query_bja_ms11, $vacantes) or die(mysql_error());
$row_bja_ms11 = mysql_fetch_assoc($bja_ms11);
$totalRows_bja_ms11 = mysql_num_rows($bja_ms11);

$RotTotalM11 =  $row_bja_ms11['TOTAL'] / $row_res_ms11['TOTAL'];

// Resultado Mes 12 año actual
$fini_ms12 = new DateTime($anio_actual . '-12-01');
$fini_ms12->modify('first day of this month');
$fini_ms12k = $fini_ms12->format('Y/m/d'); 

$fter_ms12 = new DateTime($anio_actual . '-12-01');
$fter_ms12->modify('last day of this month');
$fter_ms12k = $fter_ms12->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_ms12 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_ms12k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_ms12k')  AND ind_bajas.IDmotivo < 15";
$res_ms12 = mysql_query($query_res_ms12, $vacantes) or die(mysql_error());
$row_res_ms12 = mysql_fetch_assoc($res_ms12);
$totalRows_res_ms12 = mysql_num_rows($res_ms12);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_ms12 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 12 AND YEAR(fecha_baja) = $anio_actual)";
$bja_ms12 = mysql_query($query_bja_ms12, $vacantes) or die(mysql_error());
$row_bja_ms12 = mysql_fetch_assoc($bja_ms12);
$totalRows_bja_ms12 = mysql_num_rows($bja_ms12);

$RotTotalM12 =  $row_bja_ms12['TOTAL'] / $row_res_ms12['TOTAL'];
// acumualdo total año
$Acumulado = $RotTotalM1 + $RotTotalM2 + $RotTotalM3 + $RotTotalM4 + $RotTotalM5 + $RotTotalM6 + $RotTotalM7 + $RotTotalM8 + $RotTotalM9 + $RotTotalM10 + $RotTotalM11 + $RotTotalM12;

	switch ($el_mes) {
    case 1:  
	$Area1xmes = $row_res_ms1['Area1'];
	break;     
    case 2:  
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] ) / 2, 0);
	break;    
    case 3:  
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] ) / 3, 0);
	break;    
    case 4:  
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] + $row_res_ms4['Area1'] ) / 4, 0);
	break;    
    case 5:  
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] + $row_res_ms4['Area1'] + $row_res_ms5['Area1']  ) / 5, 0);
	break;    
    case 6:  
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] + $row_res_ms4['Area1'] + $row_res_ms5['Area1'] + $row_res_ms6['Area1']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] + $row_res_ms4['Area1'] + $row_res_ms5['Area1'] + $row_res_ms6['Area1']
	+ $row_res_ms7['Area1'] ) / 7, 0);
	break;    
    case 8:  
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] + $row_res_ms4['Area1'] + $row_res_ms5['Area1'] + $row_res_ms6['Area1']
	+ $row_res_ms7['Area1'] + $row_res_ms8['Area1'] ) / 8, 0);
	break;    
    case 9:  
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] + $row_res_ms4['Area1'] + $row_res_ms5['Area1'] + $row_res_ms6['Area1']
	+ $row_res_ms7['Area1'] + $row_res_ms8['Area1'] + $row_res_ms9['Area1'] ) / 9, 0);
	break;    
    case 10: 
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] + $row_res_ms4['Area1'] + $row_res_ms5['Area1'] + $row_res_ms6['Area1']
	+ $row_res_ms7['Area1'] + $row_res_ms8['Area1'] + $row_res_ms9['Area1'] + $row_res_ms10['Area1'] ) / 10, 0);
	break;    
    case 11: 
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] + $row_res_ms4['Area1'] + $row_res_ms5['Area1'] + $row_res_ms6['Area1']
	+ $row_res_ms7['Area1'] + $row_res_ms8['Area1'] + $row_res_ms9['Area1'] + $row_res_ms10['Area1'] + $row_res_ms11['Area1'] ) / 11, 0);
	break;    
    case 12: 
	$Area1xmes = round(($row_res_ms1['Area1'] + $row_res_ms2['Area1'] + $row_res_ms3['Area1'] + $row_res_ms4['Area1'] + $row_res_ms5['Area1'] + $row_res_ms6['Area1']
	+ $row_res_ms7['Area1'] + $row_res_ms8['Area1'] + $row_res_ms9['Area1'] + $row_res_ms10['Area1'] + $row_res_ms11['Area1'] + $row_res_ms12['Area1'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area2xmes = $row_res_ms1['Area2'];
	break;     
    case 2:  
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] ) / 2, 0);
	break;    
    case 3:  
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] ) / 3, 0);
	break;    
    case 4:  
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] + $row_res_ms4['Area2'] ) / 4, 0);
	break;    
    case 5:  
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] + $row_res_ms4['Area2'] + $row_res_ms5['Area2']  ) / 5, 0);
	break;    
    case 6:  
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] + $row_res_ms4['Area2'] + $row_res_ms5['Area2'] + $row_res_ms6['Area2']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] + $row_res_ms4['Area2'] + $row_res_ms5['Area2'] + $row_res_ms6['Area2']
	+ $row_res_ms7['Area2'] ) / 7, 0);
	break;    
    case 8:  
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] + $row_res_ms4['Area2'] + $row_res_ms5['Area2'] + $row_res_ms6['Area2']
	+ $row_res_ms7['Area2'] + $row_res_ms8['Area2'] ) / 8, 0);
	break;    
    case 9:  
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] + $row_res_ms4['Area2'] + $row_res_ms5['Area2'] + $row_res_ms6['Area2']
	+ $row_res_ms7['Area2'] + $row_res_ms8['Area2'] + $row_res_ms9['Area2'] ) / 9, 0);
	break;    
    case 10: 
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] + $row_res_ms4['Area2'] + $row_res_ms5['Area2'] + $row_res_ms6['Area2']
	+ $row_res_ms7['Area2'] + $row_res_ms8['Area2'] + $row_res_ms9['Area2'] + $row_res_ms10['Area2'] ) / 10, 0);
	break;    
    case 11: 
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] + $row_res_ms4['Area2'] + $row_res_ms5['Area2'] + $row_res_ms6['Area2']
	+ $row_res_ms7['Area2'] + $row_res_ms8['Area2'] + $row_res_ms9['Area2'] + $row_res_ms10['Area2'] + $row_res_ms11['Area2'] ) / 11, 0);
	break;    
    case 12: 
	$Area2xmes = round(($row_res_ms1['Area2'] + $row_res_ms2['Area2'] + $row_res_ms3['Area2'] + $row_res_ms4['Area2'] + $row_res_ms5['Area2'] + $row_res_ms6['Area2']
	+ $row_res_ms7['Area2'] + $row_res_ms8['Area2'] + $row_res_ms9['Area2'] + $row_res_ms10['Area2'] + $row_res_ms11['Area2'] + $row_res_ms12['Area2'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area3xmes = $row_res_ms1['Area3'];
	break;     
    case 2:  
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] ) / 2, 0);
	break;    
    case 3:  
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] ) / 3, 0);
	break;    
    case 4:  
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] + $row_res_ms4['Area3'] ) / 4, 0);
	break;    
    case 5:  
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] + $row_res_ms4['Area3'] + $row_res_ms5['Area3']  ) / 5, 0);
	break;    
    case 6:  
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] + $row_res_ms4['Area3'] + $row_res_ms5['Area3'] + $row_res_ms6['Area3']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] + $row_res_ms4['Area3'] + $row_res_ms5['Area3'] + $row_res_ms6['Area3']
	+ $row_res_ms7['Area3'] ) / 7, 0);
	break;    
    case 8:  
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] + $row_res_ms4['Area3'] + $row_res_ms5['Area3'] + $row_res_ms6['Area3']
	+ $row_res_ms7['Area3'] + $row_res_ms8['Area3'] ) / 8, 0);
	break;    
    case 9:  
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] + $row_res_ms4['Area3'] + $row_res_ms5['Area3'] + $row_res_ms6['Area3']
	+ $row_res_ms7['Area3'] + $row_res_ms8['Area3'] + $row_res_ms9['Area3'] ) / 9, 0);
	break;    
    case 10: 
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] + $row_res_ms4['Area3'] + $row_res_ms5['Area3'] + $row_res_ms6['Area3']
	+ $row_res_ms7['Area3'] + $row_res_ms8['Area3'] + $row_res_ms9['Area3'] + $row_res_ms10['Area3'] ) / 10, 0);
	break;    
    case 11: 
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] + $row_res_ms4['Area3'] + $row_res_ms5['Area3'] + $row_res_ms6['Area3']
	+ $row_res_ms7['Area3'] + $row_res_ms8['Area3'] + $row_res_ms9['Area3'] + $row_res_ms10['Area3'] + $row_res_ms11['Area3'] ) / 11, 0);
	break;    
    case 12: 
	$Area3xmes = round(($row_res_ms1['Area3'] + $row_res_ms2['Area3'] + $row_res_ms3['Area3'] + $row_res_ms4['Area3'] + $row_res_ms5['Area3'] + $row_res_ms6['Area3']
	+ $row_res_ms7['Area3'] + $row_res_ms8['Area3'] + $row_res_ms9['Area3'] + $row_res_ms10['Area3'] + $row_res_ms11['Area3'] + $row_res_ms12['Area3'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area4xmes = $row_res_ms1['Area4'];
	break;     
    case 2:  
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] ) / 2, 0);
	break;    
    case 3:  
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] ) / 3, 0);
	break;    
    case 4:  
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] + $row_res_ms4['Area4'] ) / 4, 0);
	break;    
    case 5:  
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] + $row_res_ms4['Area4'] + $row_res_ms5['Area4']  ) / 5, 0);
	break;    
    case 6:  
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] + $row_res_ms4['Area4'] + $row_res_ms5['Area4'] + $row_res_ms6['Area4']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] + $row_res_ms4['Area4'] + $row_res_ms5['Area4'] + $row_res_ms6['Area4']
	+ $row_res_ms7['Area4'] ) / 7, 0);
	break;    
    case 8:  
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] + $row_res_ms4['Area4'] + $row_res_ms5['Area4'] + $row_res_ms6['Area4']
	+ $row_res_ms7['Area4'] + $row_res_ms8['Area4'] ) / 8, 0);
	break;    
    case 9:  
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] + $row_res_ms4['Area4'] + $row_res_ms5['Area4'] + $row_res_ms6['Area4']
	+ $row_res_ms7['Area4'] + $row_res_ms8['Area4'] + $row_res_ms9['Area4'] ) / 9, 0);
	break;    
    case 10: 
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] + $row_res_ms4['Area4'] + $row_res_ms5['Area4'] + $row_res_ms6['Area4']
	+ $row_res_ms7['Area4'] + $row_res_ms8['Area4'] + $row_res_ms9['Area4'] + $row_res_ms10['Area4'] ) / 10, 0);
	break;    
    case 11: 
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] + $row_res_ms4['Area4'] + $row_res_ms5['Area4'] + $row_res_ms6['Area4']
	+ $row_res_ms7['Area4'] + $row_res_ms8['Area4'] + $row_res_ms9['Area4'] + $row_res_ms10['Area4'] + $row_res_ms11['Area4'] ) / 11, 0);
	break;    
    case 12: 
	$Area4xmes = round(($row_res_ms1['Area4'] + $row_res_ms2['Area4'] + $row_res_ms3['Area4'] + $row_res_ms4['Area4'] + $row_res_ms5['Area4'] + $row_res_ms6['Area4']
	+ $row_res_ms7['Area4'] + $row_res_ms8['Area4'] + $row_res_ms9['Area4'] + $row_res_ms10['Area4'] + $row_res_ms11['Area4'] + $row_res_ms12['Area4'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area5xmes = $row_res_ms1['Area5'];
	break;     
    case 2:  
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] ) / 2, 0);
	break;    
    case 3:  
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] ) / 3, 0);
	break;    
    case 4:  
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] + $row_res_ms4['Area5'] ) / 4, 0);
	break;    
    case 5:  
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] + $row_res_ms4['Area5'] + $row_res_ms5['Area5']  ) / 5, 0);
	break;    
    case 6:  
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] + $row_res_ms4['Area5'] + $row_res_ms5['Area5'] + $row_res_ms6['Area5']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] + $row_res_ms4['Area5'] + $row_res_ms5['Area5'] + $row_res_ms6['Area5']
	+ $row_res_ms7['Area5'] ) / 7, 0);
	break;    
    case 8:  
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] + $row_res_ms4['Area5'] + $row_res_ms5['Area5'] + $row_res_ms6['Area5']
	+ $row_res_ms7['Area5'] + $row_res_ms8['Area5'] ) / 8, 0);
	break;    
    case 9:  
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] + $row_res_ms4['Area5'] + $row_res_ms5['Area5'] + $row_res_ms6['Area5']
	+ $row_res_ms7['Area5'] + $row_res_ms8['Area5'] + $row_res_ms9['Area5'] ) / 9, 0);
	break;    
    case 10: 
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] + $row_res_ms4['Area5'] + $row_res_ms5['Area5'] + $row_res_ms6['Area5']
	+ $row_res_ms7['Area5'] + $row_res_ms8['Area5'] + $row_res_ms9['Area5'] + $row_res_ms10['Area5'] ) / 10, 0);
	break;    
    case 11: 
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] + $row_res_ms4['Area5'] + $row_res_ms5['Area5'] + $row_res_ms6['Area5']
	+ $row_res_ms7['Area5'] + $row_res_ms8['Area5'] + $row_res_ms9['Area5'] + $row_res_ms10['Area5'] + $row_res_ms11['Area5'] ) / 11, 0);
	break;    
    case 12: 
	$Area5xmes = round(($row_res_ms1['Area5'] + $row_res_ms2['Area5'] + $row_res_ms3['Area5'] + $row_res_ms4['Area5'] + $row_res_ms5['Area5'] + $row_res_ms6['Area5']
	+ $row_res_ms7['Area5'] + $row_res_ms8['Area5'] + $row_res_ms9['Area5'] + $row_res_ms10['Area5'] + $row_res_ms11['Area5'] + $row_res_ms12['Area5'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area6xmes = $row_res_ms1['Area6'];
	break;     
    case 2:  
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] ) / 2, 0);
	break;    
    case 3:  
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] ) / 3, 0);
	break;    
    case 4:  
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] + $row_res_ms4['Area6'] ) / 4, 0);
	break;    
    case 5:  
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] + $row_res_ms4['Area6'] + $row_res_ms5['Area6']  ) / 5, 0);
	break;    
    case 6:  
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] + $row_res_ms4['Area6'] + $row_res_ms5['Area6'] + $row_res_ms6['Area6']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] + $row_res_ms4['Area6'] + $row_res_ms5['Area6'] + $row_res_ms6['Area6']
	+ $row_res_ms7['Area6'] ) / 7, 0);
	break;    
    case 8:  
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] + $row_res_ms4['Area6'] + $row_res_ms5['Area6'] + $row_res_ms6['Area6']
	+ $row_res_ms7['Area6'] + $row_res_ms8['Area6'] ) / 8, 0);
	break;    
    case 9:  
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] + $row_res_ms4['Area6'] + $row_res_ms5['Area6'] + $row_res_ms6['Area6']
	+ $row_res_ms7['Area6'] + $row_res_ms8['Area6'] + $row_res_ms9['Area6'] ) / 9, 0);
	break;    
    case 10: 
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] + $row_res_ms4['Area6'] + $row_res_ms5['Area6'] + $row_res_ms6['Area6']
	+ $row_res_ms7['Area6'] + $row_res_ms8['Area6'] + $row_res_ms9['Area6'] + $row_res_ms10['Area6'] ) / 10, 0);
	break;    
    case 11: 
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] + $row_res_ms4['Area6'] + $row_res_ms5['Area6'] + $row_res_ms6['Area6']
	+ $row_res_ms7['Area6'] + $row_res_ms8['Area6'] + $row_res_ms9['Area6'] + $row_res_ms10['Area6'] + $row_res_ms11['Area6'] ) / 11, 0);
	break;    
    case 12: 
	$Area6xmes = round(($row_res_ms1['Area6'] + $row_res_ms2['Area6'] + $row_res_ms3['Area6'] + $row_res_ms4['Area6'] + $row_res_ms5['Area6'] + $row_res_ms6['Area6']
	+ $row_res_ms7['Area6'] + $row_res_ms8['Area6'] + $row_res_ms9['Area6'] + $row_res_ms10['Area6'] + $row_res_ms11['Area6'] + $row_res_ms12['Area6'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area7xmes = $row_res_ms1['Area7'];
	break;     
    case 2:  
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] ) / 2, 0);
	break;    
    case 3:  
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] ) / 3, 0);
	break;    
    case 4:  
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] + $row_res_ms4['Area7'] ) / 4, 0);
	break;    
    case 5:  
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] + $row_res_ms4['Area7'] + $row_res_ms5['Area7']  ) / 5, 0);
	break;    
    case 6:  
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] + $row_res_ms4['Area7'] + $row_res_ms5['Area7'] + $row_res_ms6['Area7']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] + $row_res_ms4['Area7'] + $row_res_ms5['Area7'] + $row_res_ms6['Area7']
	+ $row_res_ms7['Area7'] ) / 7, 0);
	break;    
    case 8:  
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] + $row_res_ms4['Area7'] + $row_res_ms5['Area7'] + $row_res_ms6['Area7']
	+ $row_res_ms7['Area7'] + $row_res_ms8['Area7'] ) / 8, 0);
	break;    
    case 9:  
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] + $row_res_ms4['Area7'] + $row_res_ms5['Area7'] + $row_res_ms6['Area7']
	+ $row_res_ms7['Area7'] + $row_res_ms8['Area7'] + $row_res_ms9['Area7'] ) / 9, 0);
	break;    
    case 10: 
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] + $row_res_ms4['Area7'] + $row_res_ms5['Area7'] + $row_res_ms6['Area7']
	+ $row_res_ms7['Area7'] + $row_res_ms8['Area7'] + $row_res_ms9['Area7'] + $row_res_ms10['Area7'] ) / 10, 0);
	break;    
    case 11: 
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] + $row_res_ms4['Area7'] + $row_res_ms5['Area7'] + $row_res_ms6['Area7']
	+ $row_res_ms7['Area7'] + $row_res_ms8['Area7'] + $row_res_ms9['Area7'] + $row_res_ms10['Area7'] + $row_res_ms11['Area7'] ) / 11, 0);
	break;    
    case 12: 
	$Area7xmes = round(($row_res_ms1['Area7'] + $row_res_ms2['Area7'] + $row_res_ms3['Area7'] + $row_res_ms4['Area7'] + $row_res_ms5['Area7'] + $row_res_ms6['Area7']
	+ $row_res_ms7['Area7'] + $row_res_ms8['Area7'] + $row_res_ms9['Area7'] + $row_res_ms10['Area7'] + $row_res_ms11['Area7'] + $row_res_ms12['Area7'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area8xmes = $row_res_ms1['Area8'];
	break;     
    case 2:  
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] ) / 2, 0);
	break;    
    case 3:  
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] ) / 3, 0);
	break;    
    case 4:  
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] + $row_res_ms4['Area8'] ) / 4, 0);
	break;    
    case 5:  
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] + $row_res_ms4['Area8'] + $row_res_ms5['Area8']  ) / 5, 0);
	break;    
    case 6:  
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] + $row_res_ms4['Area8'] + $row_res_ms5['Area8'] + $row_res_ms6['Area8']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] + $row_res_ms4['Area8'] + $row_res_ms5['Area8'] + $row_res_ms6['Area8']
	+ $row_res_ms7['Area8'] ) / 7, 0);
	break;    
    case 8:  
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] + $row_res_ms4['Area8'] + $row_res_ms5['Area8'] + $row_res_ms6['Area8']
	+ $row_res_ms7['Area8'] + $row_res_ms8['Area8'] ) / 8, 0);
	break;    
    case 9:  
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] + $row_res_ms4['Area8'] + $row_res_ms5['Area8'] + $row_res_ms6['Area8']
	+ $row_res_ms7['Area8'] + $row_res_ms8['Area8'] + $row_res_ms9['Area8'] ) / 9, 0);
	break;    
    case 10: 
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] + $row_res_ms4['Area8'] + $row_res_ms5['Area8'] + $row_res_ms6['Area8']
	+ $row_res_ms7['Area8'] + $row_res_ms8['Area8'] + $row_res_ms9['Area8'] + $row_res_ms10['Area8'] ) / 10, 0);
	break;    
    case 11: 
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] + $row_res_ms4['Area8'] + $row_res_ms5['Area8'] + $row_res_ms6['Area8']
	+ $row_res_ms7['Area8'] + $row_res_ms8['Area8'] + $row_res_ms9['Area8'] + $row_res_ms10['Area8'] + $row_res_ms11['Area8'] ) / 11, 0);
	break;    
    case 12: 
	$Area8xmes = round(($row_res_ms1['Area8'] + $row_res_ms2['Area8'] + $row_res_ms3['Area8'] + $row_res_ms4['Area8'] + $row_res_ms5['Area8'] + $row_res_ms6['Area8']
	+ $row_res_ms7['Area8'] + $row_res_ms8['Area8'] + $row_res_ms9['Area8'] + $row_res_ms10['Area8'] + $row_res_ms11['Area8'] + $row_res_ms12['Area8'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area9xmes = $row_res_ms1['Area9'];
	break;     
    case 2:  
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] ) / 2, 0);
	break;    
    case 3:  
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] ) / 3, 0);
	break;    
    case 4:  
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] + $row_res_ms4['Area9'] ) / 4, 0);
	break;    
    case 5:  
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] + $row_res_ms4['Area9'] + $row_res_ms5['Area9']  ) / 5, 0);
	break;    
    case 6:  
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] + $row_res_ms4['Area9'] + $row_res_ms5['Area9'] + $row_res_ms6['Area9']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] + $row_res_ms4['Area9'] + $row_res_ms5['Area9'] + $row_res_ms6['Area9']
	+ $row_res_ms7['Area9'] ) / 7, 0);
	break;    
    case 8:  
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] + $row_res_ms4['Area9'] + $row_res_ms5['Area9'] + $row_res_ms6['Area9']
	+ $row_res_ms7['Area9'] + $row_res_ms8['Area9'] ) / 8, 0);
	break;    
    case 9:  
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] + $row_res_ms4['Area9'] + $row_res_ms5['Area9'] + $row_res_ms6['Area9']
	+ $row_res_ms7['Area9'] + $row_res_ms8['Area9'] + $row_res_ms9['Area9'] ) / 9, 0);
	break;    
    case 10: 
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] + $row_res_ms4['Area9'] + $row_res_ms5['Area9'] + $row_res_ms6['Area9']
	+ $row_res_ms7['Area9'] + $row_res_ms8['Area9'] + $row_res_ms9['Area9'] + $row_res_ms10['Area9'] ) / 10, 0);
	break;    
    case 11: 
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] + $row_res_ms4['Area9'] + $row_res_ms5['Area9'] + $row_res_ms6['Area9']
	+ $row_res_ms7['Area9'] + $row_res_ms8['Area9'] + $row_res_ms9['Area9'] + $row_res_ms10['Area9'] + $row_res_ms11['Area9'] ) / 11, 0);
	break;    
    case 12: 
	$Area9xmes = round(($row_res_ms1['Area9'] + $row_res_ms2['Area9'] + $row_res_ms3['Area9'] + $row_res_ms4['Area9'] + $row_res_ms5['Area9'] + $row_res_ms6['Area9']
	+ $row_res_ms7['Area9'] + $row_res_ms8['Area9'] + $row_res_ms9['Area9'] + $row_res_ms10['Area9'] + $row_res_ms11['Area9'] + $row_res_ms12['Area9'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area10xmes = $row_res_ms1['Area10'];
	break;     
    case 2:  
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] ) / 2, 0);
	break;    
    case 3:  
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] ) / 3, 0);
	break;    
    case 4:  
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] + $row_res_ms4['Area10'] ) / 4, 0);
	break;    
    case 5:  
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] + $row_res_ms4['Area10'] + $row_res_ms5['Area10']  ) / 5, 0);
	break;    
    case 6:  
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] + $row_res_ms4['Area10'] + $row_res_ms5['Area10'] + $row_res_ms6['Area10']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] + $row_res_ms4['Area10'] + $row_res_ms5['Area10'] + $row_res_ms6['Area10']
	+ $row_res_ms7['Area10'] ) / 7, 0);
	break;    
    case 8:  
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] + $row_res_ms4['Area10'] + $row_res_ms5['Area10'] + $row_res_ms6['Area10']
	+ $row_res_ms7['Area10'] + $row_res_ms8['Area10'] ) / 8, 0);
	break;    
    case 9:  
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] + $row_res_ms4['Area10'] + $row_res_ms5['Area10'] + $row_res_ms6['Area10']
	+ $row_res_ms7['Area10'] + $row_res_ms8['Area10'] + $row_res_ms9['Area10'] ) / 9, 0);
	break;    
    case 10: 
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] + $row_res_ms4['Area10'] + $row_res_ms5['Area10'] + $row_res_ms6['Area10']
	+ $row_res_ms7['Area10'] + $row_res_ms8['Area10'] + $row_res_ms9['Area10'] + $row_res_ms10['Area10'] ) / 10, 0);
	break;    
    case 11: 
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] + $row_res_ms4['Area10'] + $row_res_ms5['Area10'] + $row_res_ms6['Area10']
	+ $row_res_ms7['Area10'] + $row_res_ms8['Area10'] + $row_res_ms9['Area10'] + $row_res_ms10['Area10'] + $row_res_ms11['Area10'] ) / 11, 0);
	break;    
    case 12: 
	$Area10xmes = round(($row_res_ms1['Area10'] + $row_res_ms2['Area10'] + $row_res_ms3['Area10'] + $row_res_ms4['Area10'] + $row_res_ms5['Area10'] + $row_res_ms6['Area10']
	+ $row_res_ms7['Area10'] + $row_res_ms8['Area10'] + $row_res_ms9['Area10'] + $row_res_ms10['Area10'] + $row_res_ms11['Area10'] + $row_res_ms12['Area10'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area11xmes = $row_res_ms1['Area11'];
	break;     
    case 2:  
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] ) / 2, 0);
	break;    
    case 3:  
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] ) / 3, 0);
	break;    
    case 4:  
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] + $row_res_ms4['Area11'] ) / 4, 0);
	break;    
    case 5:  
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] + $row_res_ms4['Area11'] + $row_res_ms5['Area11']  ) / 5, 0);
	break;    
    case 6:  
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] + $row_res_ms4['Area11'] + $row_res_ms5['Area11'] + $row_res_ms6['Area11']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] + $row_res_ms4['Area11'] + $row_res_ms5['Area11'] + $row_res_ms6['Area11']
	+ $row_res_ms7['Area11'] ) / 7, 0);
	break;    
    case 8:  
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] + $row_res_ms4['Area11'] + $row_res_ms5['Area11'] + $row_res_ms6['Area11']
	+ $row_res_ms7['Area11'] + $row_res_ms8['Area11'] ) / 8, 0);
	break;    
    case 9:  
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] + $row_res_ms4['Area11'] + $row_res_ms5['Area11'] + $row_res_ms6['Area11']
	+ $row_res_ms7['Area11'] + $row_res_ms8['Area11'] + $row_res_ms9['Area11'] ) / 9, 0);
	break;    
    case 10: 
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] + $row_res_ms4['Area11'] + $row_res_ms5['Area11'] + $row_res_ms6['Area11']
	+ $row_res_ms7['Area11'] + $row_res_ms8['Area11'] + $row_res_ms9['Area11'] + $row_res_ms10['Area11'] ) / 10, 0);
	break;    
    case 11: 
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] + $row_res_ms4['Area11'] + $row_res_ms5['Area11'] + $row_res_ms6['Area11']
	+ $row_res_ms7['Area11'] + $row_res_ms8['Area11'] + $row_res_ms9['Area11'] + $row_res_ms10['Area11'] + $row_res_ms11['Area11'] ) / 11, 0);
	break;    
    case 12: 
	$Area11xmes = round(($row_res_ms1['Area11'] + $row_res_ms2['Area11'] + $row_res_ms3['Area11'] + $row_res_ms4['Area11'] + $row_res_ms5['Area11'] + $row_res_ms6['Area11']
	+ $row_res_ms7['Area11'] + $row_res_ms8['Area11'] + $row_res_ms9['Area11'] + $row_res_ms10['Area11'] + $row_res_ms11['Area11'] + $row_res_ms12['Area11'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area12xmes = $row_res_ms1['Area12'];
	break;     
    case 2:  
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] ) / 2, 0);
	break;    
    case 3:  
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] ) / 3, 0);
	break;    
    case 4:  
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] + $row_res_ms4['Area12'] ) / 4, 0);
	break;    
    case 5:  
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] + $row_res_ms4['Area12'] + $row_res_ms5['Area12']  ) / 5, 0);
	break;    
    case 6:  
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] + $row_res_ms4['Area12'] + $row_res_ms5['Area12'] + $row_res_ms6['Area12']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] + $row_res_ms4['Area12'] + $row_res_ms5['Area12'] + $row_res_ms6['Area12']
	+ $row_res_ms7['Area12'] ) / 7, 0);
	break;    
    case 8:  
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] + $row_res_ms4['Area12'] + $row_res_ms5['Area12'] + $row_res_ms6['Area12']
	+ $row_res_ms7['Area12'] + $row_res_ms8['Area12'] ) / 8, 0);
	break;    
    case 9:  
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] + $row_res_ms4['Area12'] + $row_res_ms5['Area12'] + $row_res_ms6['Area12']
	+ $row_res_ms7['Area12'] + $row_res_ms8['Area12'] + $row_res_ms9['Area12'] ) / 9, 0);
	break;    
    case 10: 
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] + $row_res_ms4['Area12'] + $row_res_ms5['Area12'] + $row_res_ms6['Area12']
	+ $row_res_ms7['Area12'] + $row_res_ms8['Area12'] + $row_res_ms9['Area12'] + $row_res_ms10['Area12'] ) / 10, 0);
	break;    
    case 11: 
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] + $row_res_ms4['Area12'] + $row_res_ms5['Area12'] + $row_res_ms6['Area12']
	+ $row_res_ms7['Area12'] + $row_res_ms8['Area12'] + $row_res_ms9['Area12'] + $row_res_ms10['Area12'] + $row_res_ms11['Area12'] ) / 11, 0);
	break;    
    case 12: 
	$Area12xmes = round(($row_res_ms1['Area12'] + $row_res_ms2['Area12'] + $row_res_ms3['Area12'] + $row_res_ms4['Area12'] + $row_res_ms5['Area12'] + $row_res_ms6['Area12']
	+ $row_res_ms7['Area12'] + $row_res_ms8['Area12'] + $row_res_ms9['Area12'] + $row_res_ms10['Area12'] + $row_res_ms11['Area12'] + $row_res_ms12['Area12'] ) / 12, 0);
	break;   
      }

$Area7a12xmes = $Area7xmes + $Area8xmes+ $Area9xmes+ $Area10xmes+ $Area11xmes+ $Area12xmes;







// anio anterior
// Resultado Mes 1 año anterior
$fini_msa1 = new DateTime($anio_anterior . '-01-01');
$fini_msa1->modify('first day of this month');
$fini_msa1k = $fini_msa1->format('Y/m/d'); 

$fter_msa1 = new DateTime($anio_anterior . '-01-01');
$fter_msa1->modify('last day of this month');
$fter_msa1k = $fter_msa1->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa1 = "SELECT 
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa1k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa1k')  AND ind_bajas.IDmotivo < 15";
$res_msa1 = mysql_query($query_res_msa1, $vacantes) or die(mysql_error());
$row_res_msa1 = mysql_fetch_assoc($res_msa1);
$totalRows_res_msa1 = mysql_num_rows($res_msa1);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 1 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa1 = mysql_query($query_bja_msa1, $vacantes) or die(mysql_error());
$row_bja_msa1 = mysql_fetch_assoc($bja_msa1);
$totalRows_bja_msa1 = mysql_num_rows($bja_msa1);

$RotaTotalM1 =  $row_bja_msa1['TOTAL'] / $row_res_msa1['TOTAL'];

// Resultado Mes 2 año anterior
$fini_msa2 = new DateTime($anio_anterior . '-02-01');
$fini_msa2->modify('first day of this month');
$fini_msa2k = $fini_msa2->format('Y/m/d'); 

$fter_msa2 = new DateTime($anio_anterior . '-02-01');
$fter_msa2->modify('last day of this month');
$fter_msa2k = $fter_msa2->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa2 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa2k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa2k')  AND ind_bajas.IDmotivo < 15";
$res_msa2 = mysql_query($query_res_msa2, $vacantes) or die(mysql_error());
$row_res_msa2 = mysql_fetch_assoc($res_msa2);
$totalRows_res_msa2 = mysql_num_rows($res_msa2);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 2 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa2 = mysql_query($query_bja_msa2, $vacantes) or die(mysql_error());
$row_bja_msa2 = mysql_fetch_assoc($bja_msa2);
$totalRows_bja_msa2 = mysql_num_rows($bja_msa2);

$RotaTotalM2 =  $row_bja_msa2['TOTAL'] / $row_res_msa2['TOTAL'];

// Resultado Mes 3 año anterior
$fini_msa3 = new DateTime($anio_anterior . '-03-01');
$fini_msa3->modify('first day of this month');
$fini_msa3k = $fini_msa3->format('Y/m/d'); 

$fter_msa3 = new DateTime($anio_anterior . '-03-01');
$fter_msa3->modify('last day of this month');
$fter_msa3k = $fter_msa3->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa3 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa3k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa3k')  AND ind_bajas.IDmotivo < 15";
$res_msa3 = mysql_query($query_res_msa3, $vacantes) or die(mysql_error());
$row_res_msa3 = mysql_fetch_assoc($res_msa3);
$totalRows_res_msa3 = mysql_num_rows($res_msa3);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa3 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 3 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa3 = mysql_query($query_bja_msa3, $vacantes) or die(mysql_error());
$row_bja_msa3 = mysql_fetch_assoc($bja_msa3);
$totalRows_bja_msa3 = mysql_num_rows($bja_msa3);

$RotaTotalM3 =  $row_bja_msa3['TOTAL'] / $row_res_msa3['TOTAL'];

// Resultado Mes 4 año anterior
$fini_msa4 = new DateTime($anio_anterior . '-04-01');
$fini_msa4->modify('first day of this month');
$fini_msa4k = $fini_msa4->format('Y/m/d'); 

$fter_msa4 = new DateTime($anio_anterior . '-04-01');
$fter_msa4->modify('last day of this month');
$fter_msa4k = $fter_msa4->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa4 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa4k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa4k')  AND ind_bajas.IDmotivo < 15";
$res_msa4 = mysql_query($query_res_msa4, $vacantes) or die(mysql_error());
$row_res_msa4 = mysql_fetch_assoc($res_msa4);
$totalRows_res_msa4 = mysql_num_rows($res_msa4);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa4 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 4 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa4 = mysql_query($query_bja_msa4, $vacantes) or die(mysql_error());
$row_bja_msa4 = mysql_fetch_assoc($bja_msa4);
$totalRows_bja_msa4 = mysql_num_rows($bja_msa4);

$RotaTotalM4 =  $row_bja_msa4['TOTAL'] / $row_res_msa4['TOTAL'];

// Resultado Mes 5 año anterior
$fini_msa5 = new DateTime($anio_anterior . '-05-01');
$fini_msa5->modify('first day of this month');
$fini_msa5k = $fini_msa5->format('Y/m/d'); 

$fter_msa5 = new DateTime($anio_anterior . '-05-01');
$fter_msa5->modify('last day of this month');
$fter_msa5k = $fter_msa5->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa5 = "SELECT 
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa5k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa5k')  AND ind_bajas.IDmotivo < 15";
$res_msa5 = mysql_query($query_res_msa5, $vacantes) or die(mysql_error());
$row_res_msa5 = mysql_fetch_assoc($res_msa5);
$totalRows_res_msa5 = mysql_num_rows($res_msa5);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa5 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 5 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa5 = mysql_query($query_bja_msa5, $vacantes) or die(mysql_error());
$row_bja_msa5 = mysql_fetch_assoc($bja_msa5);
$totalRows_bja_msa5 = mysql_num_rows($bja_msa5);

$RotaTotalM5 =  $row_bja_msa5['TOTAL'] / $row_res_msa5['TOTAL'];

// Resultado Mes 6 año anterior
$fini_msa6 = new DateTime($anio_anterior . '-06-01');
$fini_msa6->modify('first day of this month');
$fini_msa6k = $fini_msa6->format('Y/m/d'); 

$fter_msa6 = new DateTime($anio_anterior . '-06-01');
$fter_msa6->modify('last day of this month');
$fter_msa6k = $fter_msa6->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa6 = "SELECT 
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa6k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa6k')  AND ind_bajas.IDmotivo < 15";
$res_msa6 = mysql_query($query_res_msa6, $vacantes) or die(mysql_error());
$row_res_msa6 = mysql_fetch_assoc($res_msa6);
$totalRows_res_msa6 = mysql_num_rows($res_msa6);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa6 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 6 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa6 = mysql_query($query_bja_msa6, $vacantes) or die(mysql_error());
$row_bja_msa6 = mysql_fetch_assoc($bja_msa6);
$totalRows_bja_msa6 = mysql_num_rows($bja_msa6);

$RotaTotalM6 =  $row_bja_msa6['TOTAL'] / $row_res_msa6['TOTAL'];

// Resultado Mes 7 año anterior
$fini_msa7 = new DateTime($anio_anterior . '-07-01');
$fini_msa7->modify('first day of this month');
$fini_msa7k = $fini_msa7->format('Y/m/d'); 

$fter_msa7 = new DateTime($anio_anterior . '-07-01');
$fter_msa7->modify('last day of this month');
$fter_msa7k = $fter_msa7->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa7 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,

sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa7k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa7k')  AND ind_bajas.IDmotivo < 15";
$res_msa7 = mysql_query($query_res_msa7, $vacantes) or die(mysql_error());
$row_res_msa7 = mysql_fetch_assoc($res_msa7);
$totalRows_res_msa7 = mysql_num_rows($res_msa7);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa7 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 7 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa7 = mysql_query($query_bja_msa7, $vacantes) or die(mysql_error());
$row_bja_msa7 = mysql_fetch_assoc($bja_msa7);
$totalRows_bja_msa7 = mysql_num_rows($bja_msa7);

$RotaTotalM7 =  $row_bja_msa7['TOTAL'] / $row_res_msa7['TOTAL'];

// Resultado Mes 8 año anterior
$fini_msa8 = new DateTime($anio_anterior . '-08-01');
$fini_msa8->modify('first day of this month');
$fini_msa8k = $fini_msa8->format('Y/m/d'); 

$fter_msa8 = new DateTime($anio_anterior . '-08-01');
$fter_msa8->modify('last day of this month');
$fter_msa8k = $fter_msa8->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa8 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa8k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa8k')  AND ind_bajas.IDmotivo < 15";
$res_msa8 = mysql_query($query_res_msa8, $vacantes) or die(mysql_error());
$row_res_msa8 = mysql_fetch_assoc($res_msa8);
$totalRows_res_msa8 = mysql_num_rows($res_msa8);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa8 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 8 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa8 = mysql_query($query_bja_msa8, $vacantes) or die(mysql_error());
$row_bja_msa8 = mysql_fetch_assoc($bja_msa8);
$totalRows_bja_msa8 = mysql_num_rows($bja_msa8);

$RotaTotalM8 =  $row_bja_msa8['TOTAL'] / $row_res_msa8['TOTAL'];

// Resultado Mes 9 año anterior
$fini_msa9 = new DateTime($anio_anterior . '-09-01');
$fini_msa9->modify('first day of this month');
$fini_msa9k = $fini_msa9->format('Y/m/d'); 

$fter_msa9 = new DateTime($anio_anterior . '-09-01');
$fter_msa9->modify('last day of this month');
$fter_msa9k = $fter_msa9->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa9 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa9k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa9k')  AND ind_bajas.IDmotivo < 15";
$res_msa9 = mysql_query($query_res_msa9, $vacantes) or die(mysql_error());
$row_res_msa9 = mysql_fetch_assoc($res_msa9);
$totalRows_res_msa9 = mysql_num_rows($res_msa9);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa9 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 9 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa9 = mysql_query($query_bja_msa9, $vacantes) or die(mysql_error());
$row_bja_msa9 = mysql_fetch_assoc($bja_msa9);
$totalRows_bja_msa9 = mysql_num_rows($bja_msa9);

$RotaTotalM9 =  $row_bja_msa9['TOTAL'] / $row_res_msa9['TOTAL'];

// Resultado Mes 10 año anterior
$fini_msa10 = new DateTime($anio_anterior . '-10-01');
$fini_msa10->modify('first day of this month');
$fini_msa10k = $fini_msa10->format('Y/m/d'); 

$fter_msa10 = new DateTime($anio_anterior . '-10-01');
$fter_msa10->modify('last day of this month');
$fter_msa10k = $fter_msa10->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa10 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa10k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa10k')  AND ind_bajas.IDmotivo < 15";
$res_msa10 = mysql_query($query_res_msa10, $vacantes) or die(mysql_error());
$row_res_msa10 = mysql_fetch_assoc($res_msa10);
$totalRows_res_msa10 = mysql_num_rows($res_msa10);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa10 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 10 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa10 = mysql_query($query_bja_msa10, $vacantes) or die(mysql_error());
$row_bja_msa10 = mysql_fetch_assoc($bja_msa10);
$totalRows_bja_msa10 = mysql_num_rows($bja_msa10);

$RotaTotalM10 =  $row_bja_msa10['TOTAL'] / $row_res_msa10['TOTAL'];

// Resultado Mes 11 año anterior
$fini_msa11 = new DateTime($anio_anterior . '-11-01');
$fini_msa11->modify('first day of this month');
$fini_msa11k = $fini_msa11->format('Y/m/d'); 

$fter_msa11 = new DateTime($anio_anterior . '-11-01');
$fter_msa11->modify('last day of this month');
$fter_msa11k = $fter_msa11->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa11 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa11k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa11k')  AND ind_bajas.IDmotivo < 15";
$res_msa11 = mysql_query($query_res_msa11, $vacantes) or die(mysql_error());
$row_res_msa11 = mysql_fetch_assoc($res_msa11);
$totalRows_res_msa11 = mysql_num_rows($res_msa11);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa11 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 11 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa11 = mysql_query($query_bja_msa11, $vacantes) or die(mysql_error());
$row_bja_msa11 = mysql_fetch_assoc($bja_msa11);
$totalRows_bja_msa11 = mysql_num_rows($bja_msa11);

$RotaTotalM11 =  $row_bja_msa11['TOTAL'] / $row_res_msa11['TOTAL'];

// Resultado Mes 12 año anterior
$fini_msa12 = new DateTime($anio_anterior . '-12-01');
$fini_msa12->modify('first day of this month');
$fini_msa12k = $fini_msa12->format('Y/m/d'); 

$fter_msa12 = new DateTime($anio_anterior . '-12-01');
$fter_msa12->modify('last day of this month');
$fter_msa12k = $fter_msa12->format('Y/m/d'); 

mysql_select_db($database_vacantes, $vacantes);
$query_res_msa12 = "SELECT
sum(case when IDarea = 1 then 1 else 0 end) as Area1,
sum(case when IDarea = 2 then 1 else 0 end) as Area2,
sum(case when IDarea = 3 then 1 else 0 end) as Area3,
sum(case when IDarea = 4 then 1 else 0 end) as Area4,
sum(case when IDarea = 5 then 1 else 0 end) as Area5,
sum(case when IDarea = 6 then 1 else 0 end) as Area6,
sum(case when IDarea = 7 then 1 else 0 end) as Area7,
sum(case when IDarea = 8 then 1 else 0 end) as Area8,
sum(case when IDarea = 9 then 1 else 0 end) as Area9,
sum(case when IDarea = 10 then 1 else 0 end) as Area10,
sum(case when IDarea = 11 then 1 else 0 end) as Area11,
sum(case when IDarea = 12 then 1 else 0 end) as Area12,
Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE fecha_antiguedad < '$fter_msa12k' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja > '$fini_msa12k')  AND ind_bajas.IDmotivo < 15";
$res_msa12 = mysql_query($query_res_msa12, $vacantes) or die(mysql_error());
$row_res_msa12 = mysql_fetch_assoc($res_msa12);
$totalRows_res_msa12 = mysql_num_rows($res_msa12);

mysql_select_db($database_vacantes, $vacantes);
$query_bja_msa12 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) = 12 AND YEAR(fecha_baja) = $anio_anterior)";
$bja_msa12 = mysql_query($query_bja_msa12, $vacantes) or die(mysql_error());
$row_bja_msa12 = mysql_fetch_assoc($bja_msa12);
$totalRows_bja_msa12 = mysql_num_rows($bja_msa12);

$RotaTotalM12 =  $row_bja_ms12['TOTAL'] / $row_res_ms12['TOTAL'];

// acumualdo total año
$Acumuladoa = $RotaTotalM1 + $RotaTotalM2 + $RotaTotalM3 + $RotaTotalM4 + $RotaTotalM5 + $RotaTotalM6 + $RotaTotalM7 + $RotaTotalM8 + $RotaTotalM9 + $RotaTotalM10 + $RotaTotalM11 + $RotaTotalM12;

	switch ($el_mes) {
    case 1:  
	$Area1xmesa = $row_res_msa1['Area1'];
	break;     
    case 2:  
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] ) / 2, 0);
	break;    
    case 3:  
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] ) / 3, 0);
	break;    
    case 4:  
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] + $row_res_msa4['Area1'] ) / 4, 0);
	break;    
    case 5:  
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] + $row_res_msa4['Area1'] + $row_res_msa5['Area1']  ) / 5, 0);
	break;    
    case 6:  
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] + $row_res_msa4['Area1'] + $row_res_msa5['Area1'] + $row_res_msa6['Area1']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] + $row_res_msa4['Area1'] + $row_res_msa5['Area1'] + $row_res_msa6['Area1']
	+ $row_res_msa7['Area1'] ) / 7, 0);
	break;    
    case 8:  
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] + $row_res_msa4['Area1'] + $row_res_msa5['Area1'] + $row_res_msa6['Area1']
	+ $row_res_msa7['Area1'] + $row_res_msa8['Area1'] ) / 8, 0);
	break;    
    case 9:  
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] + $row_res_msa4['Area1'] + $row_res_msa5['Area1'] + $row_res_msa6['Area1']
	+ $row_res_msa7['Area1'] + $row_res_msa8['Area1'] + $row_res_msa9['Area1'] ) / 9, 0);
	break;    
    case 10: 
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] + $row_res_msa4['Area1'] + $row_res_msa5['Area1'] + $row_res_msa6['Area1']
	+ $row_res_msa7['Area1'] + $row_res_msa8['Area1'] + $row_res_msa9['Area1'] + $row_res_msa10['Area1'] ) / 10, 0);
	break;    
    case 11: 
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] + $row_res_msa4['Area1'] + $row_res_msa5['Area1'] + $row_res_msa6['Area1']
	+ $row_res_msa7['Area1'] + $row_res_msa8['Area1'] + $row_res_msa9['Area1'] + $row_res_msa10['Area1'] + $row_res_msa11['Area1'] ) / 11, 0);
	break;    
    case 12: 
	$Area1xmesa = round(($row_res_msa1['Area1'] + $row_res_msa2['Area1'] + $row_res_msa3['Area1'] + $row_res_msa4['Area1'] + $row_res_msa5['Area1'] + $row_res_msa6['Area1']
	+ $row_res_msa7['Area1'] + $row_res_msa8['Area1'] + $row_res_msa9['Area1'] + $row_res_msa10['Area1'] + $row_res_msa11['Area1'] + $row_res_msa12['Area1'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area2xmesa = $row_res_msa1['Area2'];
	break;     
    case 2:  
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] ) / 2, 0);
	break;    
    case 3:  
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] ) / 3, 0);
	break;    
    case 4:  
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] + $row_res_msa4['Area2'] ) / 4, 0);
	break;    
    case 5:  
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] + $row_res_msa4['Area2'] + $row_res_msa5['Area2']  ) / 5, 0);
	break;    
    case 6:  
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] + $row_res_msa4['Area2'] + $row_res_msa5['Area2'] + $row_res_msa6['Area2']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] + $row_res_msa4['Area2'] + $row_res_msa5['Area2'] + $row_res_msa6['Area2']
	+ $row_res_msa7['Area2'] ) / 7, 0);
	break;    
    case 8:  
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] + $row_res_msa4['Area2'] + $row_res_msa5['Area2'] + $row_res_msa6['Area2']
	+ $row_res_msa7['Area2'] + $row_res_msa8['Area2'] ) / 8, 0);
	break;    
    case 9:  
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] + $row_res_msa4['Area2'] + $row_res_msa5['Area2'] + $row_res_msa6['Area2']
	+ $row_res_msa7['Area2'] + $row_res_msa8['Area2'] + $row_res_msa9['Area2'] ) / 9, 0);
	break;    
    case 10: 
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] + $row_res_msa4['Area2'] + $row_res_msa5['Area2'] + $row_res_msa6['Area2']
	+ $row_res_msa7['Area2'] + $row_res_msa8['Area2'] + $row_res_msa9['Area2'] + $row_res_msa10['Area2'] ) / 10, 0);
	break;    
    case 11: 
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] + $row_res_msa4['Area2'] + $row_res_msa5['Area2'] + $row_res_msa6['Area2']
	+ $row_res_msa7['Area2'] + $row_res_msa8['Area2'] + $row_res_msa9['Area2'] + $row_res_msa10['Area2'] + $row_res_msa11['Area2'] ) / 11, 0);
	break;    
    case 12: 
	$Area2xmesa = round(($row_res_msa1['Area2'] + $row_res_msa2['Area2'] + $row_res_msa3['Area2'] + $row_res_msa4['Area2'] + $row_res_msa5['Area2'] + $row_res_msa6['Area2']
	+ $row_res_msa7['Area2'] + $row_res_msa8['Area2'] + $row_res_msa9['Area2'] + $row_res_msa10['Area2'] + $row_res_msa11['Area2'] + $row_res_msa12['Area2'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area3xmesa = $row_res_msa1['Area3'];
	break;     
    case 2:  
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] ) / 2, 0);
	break;    
    case 3:  
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] ) / 3, 0);
	break;    
    case 4:  
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] + $row_res_msa4['Area3'] ) / 4, 0);
	break;    
    case 5:  
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] + $row_res_msa4['Area3'] + $row_res_msa5['Area3']  ) / 5, 0);
	break;    
    case 6:  
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] + $row_res_msa4['Area3'] + $row_res_msa5['Area3'] + $row_res_msa6['Area3']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] + $row_res_msa4['Area3'] + $row_res_msa5['Area3'] + $row_res_msa6['Area3']
	+ $row_res_msa7['Area3'] ) / 7, 0);
	break;    
    case 8:  
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] + $row_res_msa4['Area3'] + $row_res_msa5['Area3'] + $row_res_msa6['Area3']
	+ $row_res_msa7['Area3'] + $row_res_msa8['Area3'] ) / 8, 0);
	break;    
    case 9:  
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] + $row_res_msa4['Area3'] + $row_res_msa5['Area3'] + $row_res_msa6['Area3']
	+ $row_res_msa7['Area3'] + $row_res_msa8['Area3'] + $row_res_msa9['Area3'] ) / 9, 0);
	break;    
    case 10: 
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] + $row_res_msa4['Area3'] + $row_res_msa5['Area3'] + $row_res_msa6['Area3']
	+ $row_res_msa7['Area3'] + $row_res_msa8['Area3'] + $row_res_msa9['Area3'] + $row_res_msa10['Area3'] ) / 10, 0);
	break;    
    case 11: 
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] + $row_res_msa4['Area3'] + $row_res_msa5['Area3'] + $row_res_msa6['Area3']
	+ $row_res_msa7['Area3'] + $row_res_msa8['Area3'] + $row_res_msa9['Area3'] + $row_res_msa10['Area3'] + $row_res_msa11['Area3'] ) / 11, 0);
	break;    
    case 12: 
	$Area3xmesa = round(($row_res_msa1['Area3'] + $row_res_msa2['Area3'] + $row_res_msa3['Area3'] + $row_res_msa4['Area3'] + $row_res_msa5['Area3'] + $row_res_msa6['Area3']
	+ $row_res_msa7['Area3'] + $row_res_msa8['Area3'] + $row_res_msa9['Area3'] + $row_res_msa10['Area3'] + $row_res_msa11['Area3'] + $row_res_msa12['Area3'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area4xmesa = $row_res_msa1['Area4'];
	break;     
    case 2:  
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] ) / 2, 0);
	break;    
    case 3:  
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] ) / 3, 0);
	break;    
    case 4:  
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] + $row_res_msa4['Area4'] ) / 4, 0);
	break;    
    case 5:  
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] + $row_res_msa4['Area4'] + $row_res_msa5['Area4']  ) / 5, 0);
	break;    
    case 6:  
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] + $row_res_msa4['Area4'] + $row_res_msa5['Area4'] + $row_res_msa6['Area4']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] + $row_res_msa4['Area4'] + $row_res_msa5['Area4'] + $row_res_msa6['Area4']
	+ $row_res_msa7['Area4'] ) / 7, 0);
	break;    
    case 8:  
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] + $row_res_msa4['Area4'] + $row_res_msa5['Area4'] + $row_res_msa6['Area4']
	+ $row_res_msa7['Area4'] + $row_res_msa8['Area4'] ) / 8, 0);
	break;    
    case 9:  
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] + $row_res_msa4['Area4'] + $row_res_msa5['Area4'] + $row_res_msa6['Area4']
	+ $row_res_msa7['Area4'] + $row_res_msa8['Area4'] + $row_res_msa9['Area4'] ) / 9, 0);
	break;    
    case 10: 
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] + $row_res_msa4['Area4'] + $row_res_msa5['Area4'] + $row_res_msa6['Area4']
	+ $row_res_msa7['Area4'] + $row_res_msa8['Area4'] + $row_res_msa9['Area4'] + $row_res_msa10['Area4'] ) / 10, 0);
	break;    
    case 11: 
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] + $row_res_msa4['Area4'] + $row_res_msa5['Area4'] + $row_res_msa6['Area4']
	+ $row_res_msa7['Area4'] + $row_res_msa8['Area4'] + $row_res_msa9['Area4'] + $row_res_msa10['Area4'] + $row_res_msa11['Area4'] ) / 11, 0);
	break;    
    case 12: 
	$Area4xmesa = round(($row_res_msa1['Area4'] + $row_res_msa2['Area4'] + $row_res_msa3['Area4'] + $row_res_msa4['Area4'] + $row_res_msa5['Area4'] + $row_res_msa6['Area4']
	+ $row_res_msa7['Area4'] + $row_res_msa8['Area4'] + $row_res_msa9['Area4'] + $row_res_msa10['Area4'] + $row_res_msa11['Area4'] + $row_res_msa12['Area4'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area5xmesa = $row_res_msa1['Area5'];
	break;     
    case 2:  
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] ) / 2, 0);
	break;    
    case 3:  
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] ) / 3, 0);
	break;    
    case 4:  
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] + $row_res_msa4['Area5'] ) / 4, 0);
	break;    
    case 5:  
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] + $row_res_msa4['Area5'] + $row_res_msa5['Area5']  ) / 5, 0);
	break;    
    case 6:  
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] + $row_res_msa4['Area5'] + $row_res_msa5['Area5'] + $row_res_msa6['Area5']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] + $row_res_msa4['Area5'] + $row_res_msa5['Area5'] + $row_res_msa6['Area5']
	+ $row_res_msa7['Area5'] ) / 7, 0);
	break;    
    case 8:  
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] + $row_res_msa4['Area5'] + $row_res_msa5['Area5'] + $row_res_msa6['Area5']
	+ $row_res_msa7['Area5'] + $row_res_msa8['Area5'] ) / 8, 0);
	break;    
    case 9:  
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] + $row_res_msa4['Area5'] + $row_res_msa5['Area5'] + $row_res_msa6['Area5']
	+ $row_res_msa7['Area5'] + $row_res_msa8['Area5'] + $row_res_msa9['Area5'] ) / 9, 0);
	break;    
    case 10: 
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] + $row_res_msa4['Area5'] + $row_res_msa5['Area5'] + $row_res_msa6['Area5']
	+ $row_res_msa7['Area5'] + $row_res_msa8['Area5'] + $row_res_msa9['Area5'] + $row_res_msa10['Area5'] ) / 10, 0);
	break;    
    case 11: 
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] + $row_res_msa4['Area5'] + $row_res_msa5['Area5'] + $row_res_msa6['Area5']
	+ $row_res_msa7['Area5'] + $row_res_msa8['Area5'] + $row_res_msa9['Area5'] + $row_res_msa10['Area5'] + $row_res_msa11['Area5'] ) / 11, 0);
	break;    
    case 12: 
	$Area5xmesa = round(($row_res_msa1['Area5'] + $row_res_msa2['Area5'] + $row_res_msa3['Area5'] + $row_res_msa4['Area5'] + $row_res_msa5['Area5'] + $row_res_msa6['Area5']
	+ $row_res_msa7['Area5'] + $row_res_msa8['Area5'] + $row_res_msa9['Area5'] + $row_res_msa10['Area5'] + $row_res_msa11['Area5'] + $row_res_msa12['Area5'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area6xmesa = $row_res_msa1['Area6'];
	break;     
    case 2:  
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] ) / 2, 0);
	break;    
    case 3:  
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] ) / 3, 0);
	break;    
    case 4:  
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] + $row_res_msa4['Area6'] ) / 4, 0);
	break;    
    case 5:  
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] + $row_res_msa4['Area6'] + $row_res_msa5['Area6']  ) / 5, 0);
	break;    
    case 6:  
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] + $row_res_msa4['Area6'] + $row_res_msa5['Area6'] + $row_res_msa6['Area6']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] + $row_res_msa4['Area6'] + $row_res_msa5['Area6'] + $row_res_msa6['Area6']
	+ $row_res_msa7['Area6'] ) / 7, 0);
	break;    
    case 8:  
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] + $row_res_msa4['Area6'] + $row_res_msa5['Area6'] + $row_res_msa6['Area6']
	+ $row_res_msa7['Area6'] + $row_res_msa8['Area6'] ) / 8, 0);
	break;    
    case 9:  
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] + $row_res_msa4['Area6'] + $row_res_msa5['Area6'] + $row_res_msa6['Area6']
	+ $row_res_msa7['Area6'] + $row_res_msa8['Area6'] + $row_res_msa9['Area6'] ) / 9, 0);
	break;    
    case 10: 
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] + $row_res_msa4['Area6'] + $row_res_msa5['Area6'] + $row_res_msa6['Area6']
	+ $row_res_msa7['Area6'] + $row_res_msa8['Area6'] + $row_res_msa9['Area6'] + $row_res_msa10['Area6'] ) / 10, 0);
	break;    
    case 11: 
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] + $row_res_msa4['Area6'] + $row_res_msa5['Area6'] + $row_res_msa6['Area6']
	+ $row_res_msa7['Area6'] + $row_res_msa8['Area6'] + $row_res_msa9['Area6'] + $row_res_msa10['Area6'] + $row_res_msa11['Area6'] ) / 11, 0);
	break;    
    case 12: 
	$Area6xmesa = round(($row_res_msa1['Area6'] + $row_res_msa2['Area6'] + $row_res_msa3['Area6'] + $row_res_msa4['Area6'] + $row_res_msa5['Area6'] + $row_res_msa6['Area6']
	+ $row_res_msa7['Area6'] + $row_res_msa8['Area6'] + $row_res_msa9['Area6'] + $row_res_msa10['Area6'] + $row_res_msa11['Area6'] + $row_res_msa12['Area6'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area7xmesa = $row_res_msa1['Area7'];
	break;     
    case 2:  
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] ) / 2, 0);
	break;    
    case 3:  
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] ) / 3, 0);
	break;    
    case 4:  
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] + $row_res_msa4['Area7'] ) / 4, 0);
	break;    
    case 5:  
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] + $row_res_msa4['Area7'] + $row_res_msa5['Area7']  ) / 5, 0);
	break;    
    case 6:  
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] + $row_res_msa4['Area7'] + $row_res_msa5['Area7'] + $row_res_msa6['Area7']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] + $row_res_msa4['Area7'] + $row_res_msa5['Area7'] + $row_res_msa6['Area7']
	+ $row_res_msa7['Area7'] ) / 7, 0);
	break;    
    case 8:  
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] + $row_res_msa4['Area7'] + $row_res_msa5['Area7'] + $row_res_msa6['Area7']
	+ $row_res_msa7['Area7'] + $row_res_msa8['Area7'] ) / 8, 0);
	break;    
    case 9:  
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] + $row_res_msa4['Area7'] + $row_res_msa5['Area7'] + $row_res_msa6['Area7']
	+ $row_res_msa7['Area7'] + $row_res_msa8['Area7'] + $row_res_msa9['Area7'] ) / 9, 0);
	break;    
    case 10: 
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] + $row_res_msa4['Area7'] + $row_res_msa5['Area7'] + $row_res_msa6['Area7']
	+ $row_res_msa7['Area7'] + $row_res_msa8['Area7'] + $row_res_msa9['Area7'] + $row_res_msa10['Area7'] ) / 10, 0);
	break;    
    case 11: 
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] + $row_res_msa4['Area7'] + $row_res_msa5['Area7'] + $row_res_msa6['Area7']
	+ $row_res_msa7['Area7'] + $row_res_msa8['Area7'] + $row_res_msa9['Area7'] + $row_res_msa10['Area7'] + $row_res_msa11['Area7'] ) / 11, 0);
	break;    
    case 12: 
	$Area7xmesa = round(($row_res_msa1['Area7'] + $row_res_msa2['Area7'] + $row_res_msa3['Area7'] + $row_res_msa4['Area7'] + $row_res_msa5['Area7'] + $row_res_msa6['Area7']
	+ $row_res_msa7['Area7'] + $row_res_msa8['Area7'] + $row_res_msa9['Area7'] + $row_res_msa10['Area7'] + $row_res_msa11['Area7'] + $row_res_msa12['Area7'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area8xmesa = $row_res_msa1['Area8'];
	break;     
    case 2:  
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] ) / 2, 0);
	break;    
    case 3:  
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] ) / 3, 0);
	break;    
    case 4:  
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] + $row_res_msa4['Area8'] ) / 4, 0);
	break;    
    case 5:  
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] + $row_res_msa4['Area8'] + $row_res_msa5['Area8']  ) / 5, 0);
	break;    
    case 6:  
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] + $row_res_msa4['Area8'] + $row_res_msa5['Area8'] + $row_res_msa6['Area8']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] + $row_res_msa4['Area8'] + $row_res_msa5['Area8'] + $row_res_msa6['Area8']
	+ $row_res_msa7['Area8'] ) / 7, 0);
	break;    
    case 8:  
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] + $row_res_msa4['Area8'] + $row_res_msa5['Area8'] + $row_res_msa6['Area8']
	+ $row_res_msa7['Area8'] + $row_res_msa8['Area8'] ) / 8, 0);
	break;    
    case 9:  
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] + $row_res_msa4['Area8'] + $row_res_msa5['Area8'] + $row_res_msa6['Area8']
	+ $row_res_msa7['Area8'] + $row_res_msa8['Area8'] + $row_res_msa9['Area8'] ) / 9, 0);
	break;    
    case 10: 
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] + $row_res_msa4['Area8'] + $row_res_msa5['Area8'] + $row_res_msa6['Area8']
	+ $row_res_msa7['Area8'] + $row_res_msa8['Area8'] + $row_res_msa9['Area8'] + $row_res_msa10['Area8'] ) / 10, 0);
	break;    
    case 11: 
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] + $row_res_msa4['Area8'] + $row_res_msa5['Area8'] + $row_res_msa6['Area8']
	+ $row_res_msa7['Area8'] + $row_res_msa8['Area8'] + $row_res_msa9['Area8'] + $row_res_msa10['Area8'] + $row_res_msa11['Area8'] ) / 11, 0);
	break;    
    case 12: 
	$Area8xmesa = round(($row_res_msa1['Area8'] + $row_res_msa2['Area8'] + $row_res_msa3['Area8'] + $row_res_msa4['Area8'] + $row_res_msa5['Area8'] + $row_res_msa6['Area8']
	+ $row_res_msa7['Area8'] + $row_res_msa8['Area8'] + $row_res_msa9['Area8'] + $row_res_msa10['Area8'] + $row_res_msa11['Area8'] + $row_res_msa12['Area8'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area9xmesa = $row_res_msa1['Area9'];
	break;     
    case 2:  
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] ) / 2, 0);
	break;    
    case 3:  
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] ) / 3, 0);
	break;    
    case 4:  
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] + $row_res_msa4['Area9'] ) / 4, 0);
	break;    
    case 5:  
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] + $row_res_msa4['Area9'] + $row_res_msa5['Area9']  ) / 5, 0);
	break;    
    case 6:  
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] + $row_res_msa4['Area9'] + $row_res_msa5['Area9'] + $row_res_msa6['Area9']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] + $row_res_msa4['Area9'] + $row_res_msa5['Area9'] + $row_res_msa6['Area9']
	+ $row_res_msa7['Area9'] ) / 7, 0);
	break;    
    case 8:  
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] + $row_res_msa4['Area9'] + $row_res_msa5['Area9'] + $row_res_msa6['Area9']
	+ $row_res_msa7['Area9'] + $row_res_msa8['Area9'] ) / 8, 0);
	break;    
    case 9:  
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] + $row_res_msa4['Area9'] + $row_res_msa5['Area9'] + $row_res_msa6['Area9']
	+ $row_res_msa7['Area9'] + $row_res_msa8['Area9'] + $row_res_msa9['Area9'] ) / 9, 0);
	break;    
    case 10: 
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] + $row_res_msa4['Area9'] + $row_res_msa5['Area9'] + $row_res_msa6['Area9']
	+ $row_res_msa7['Area9'] + $row_res_msa8['Area9'] + $row_res_msa9['Area9'] + $row_res_msa10['Area9'] ) / 10, 0);
	break;    
    case 11: 
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] + $row_res_msa4['Area9'] + $row_res_msa5['Area9'] + $row_res_msa6['Area9']
	+ $row_res_msa7['Area9'] + $row_res_msa8['Area9'] + $row_res_msa9['Area9'] + $row_res_msa10['Area9'] + $row_res_msa11['Area9'] ) / 11, 0);
	break;    
    case 12: 
	$Area9xmesa = round(($row_res_msa1['Area9'] + $row_res_msa2['Area9'] + $row_res_msa3['Area9'] + $row_res_msa4['Area9'] + $row_res_msa5['Area9'] + $row_res_msa6['Area9']
	+ $row_res_msa7['Area9'] + $row_res_msa8['Area9'] + $row_res_msa9['Area9'] + $row_res_msa10['Area9'] + $row_res_msa11['Area9'] + $row_res_msa12['Area9'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area10xmesa = $row_res_msa1['Area10'];
	break;     
    case 2:  
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] ) / 2, 0);
	break;    
    case 3:  
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] ) / 3, 0);
	break;    
    case 4:  
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] + $row_res_msa4['Area10'] ) / 4, 0);
	break;    
    case 5:  
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] + $row_res_msa4['Area10'] + $row_res_msa5['Area10']  ) / 5, 0);
	break;    
    case 6:  
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] + $row_res_msa4['Area10'] + $row_res_msa5['Area10'] + $row_res_msa6['Area10']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] + $row_res_msa4['Area10'] + $row_res_msa5['Area10'] + $row_res_msa6['Area10']
	+ $row_res_msa7['Area10'] ) / 7, 0);
	break;    
    case 8:  
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] + $row_res_msa4['Area10'] + $row_res_msa5['Area10'] + $row_res_msa6['Area10']
	+ $row_res_msa7['Area10'] + $row_res_msa8['Area10'] ) / 8, 0);
	break;    
    case 9:  
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] + $row_res_msa4['Area10'] + $row_res_msa5['Area10'] + $row_res_msa6['Area10']
	+ $row_res_msa7['Area10'] + $row_res_msa8['Area10'] + $row_res_msa9['Area10'] ) / 9, 0);
	break;    
    case 10: 
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] + $row_res_msa4['Area10'] + $row_res_msa5['Area10'] + $row_res_msa6['Area10']
	+ $row_res_msa7['Area10'] + $row_res_msa8['Area10'] + $row_res_msa9['Area10'] + $row_res_msa10['Area10'] ) / 10, 0);
	break;    
    case 11: 
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] + $row_res_msa4['Area10'] + $row_res_msa5['Area10'] + $row_res_msa6['Area10']
	+ $row_res_msa7['Area10'] + $row_res_msa8['Area10'] + $row_res_msa9['Area10'] + $row_res_msa10['Area10'] + $row_res_msa11['Area10'] ) / 11, 0);
	break;    
    case 12: 
	$Area10xmesa = round(($row_res_msa1['Area10'] + $row_res_msa2['Area10'] + $row_res_msa3['Area10'] + $row_res_msa4['Area10'] + $row_res_msa5['Area10'] + $row_res_msa6['Area10']
	+ $row_res_msa7['Area10'] + $row_res_msa8['Area10'] + $row_res_msa9['Area10'] + $row_res_msa10['Area10'] + $row_res_msa11['Area10'] + $row_res_msa12['Area10'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area11xmesa = $row_res_msa1['Area11'];
	break;     
    case 2:  
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] ) / 2, 0);
	break;    
    case 3:  
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] ) / 3, 0);
	break;    
    case 4:  
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] + $row_res_msa4['Area11'] ) / 4, 0);
	break;    
    case 5:  
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] + $row_res_msa4['Area11'] + $row_res_msa5['Area11']  ) / 5, 0);
	break;    
    case 6:  
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] + $row_res_msa4['Area11'] + $row_res_msa5['Area11'] + $row_res_msa6['Area11']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] + $row_res_msa4['Area11'] + $row_res_msa5['Area11'] + $row_res_msa6['Area11']
	+ $row_res_msa7['Area11'] ) / 7, 0);
	break;    
    case 8:  
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] + $row_res_msa4['Area11'] + $row_res_msa5['Area11'] + $row_res_msa6['Area11']
	+ $row_res_msa7['Area11'] + $row_res_msa8['Area11'] ) / 8, 0);
	break;    
    case 9:  
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] + $row_res_msa4['Area11'] + $row_res_msa5['Area11'] + $row_res_msa6['Area11']
	+ $row_res_msa7['Area11'] + $row_res_msa8['Area11'] + $row_res_msa9['Area11'] ) / 9, 0);
	break;    
    case 10: 
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] + $row_res_msa4['Area11'] + $row_res_msa5['Area11'] + $row_res_msa6['Area11']
	+ $row_res_msa7['Area11'] + $row_res_msa8['Area11'] + $row_res_msa9['Area11'] + $row_res_msa10['Area11'] ) / 10, 0);
	break;    
    case 11: 
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] + $row_res_msa4['Area11'] + $row_res_msa5['Area11'] + $row_res_msa6['Area11']
	+ $row_res_msa7['Area11'] + $row_res_msa8['Area11'] + $row_res_msa9['Area11'] + $row_res_msa10['Area11'] + $row_res_msa11['Area11'] ) / 11, 0);
	break;    
    case 12: 
	$Area11xmesa = round(($row_res_msa1['Area11'] + $row_res_msa2['Area11'] + $row_res_msa3['Area11'] + $row_res_msa4['Area11'] + $row_res_msa5['Area11'] + $row_res_msa6['Area11']
	+ $row_res_msa7['Area11'] + $row_res_msa8['Area11'] + $row_res_msa9['Area11'] + $row_res_msa10['Area11'] + $row_res_msa11['Area11'] + $row_res_msa12['Area11'] ) / 12, 0);
	break;   
      }

	switch ($el_mes) {
    case 1:  
	$Area12xmesa = $row_res_msa1['Area12'];
	break;     
    case 2:  
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] ) / 2, 0);
	break;    
    case 3:  
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] ) / 3, 0);
	break;    
    case 4:  
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] + $row_res_msa4['Area12'] ) / 4, 0);
	break;    
    case 5:  
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] + $row_res_msa4['Area12'] + $row_res_msa5['Area12']  ) / 5, 0);
	break;    
    case 6:  
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] + $row_res_msa4['Area12'] + $row_res_msa5['Area12'] + $row_res_msa6['Area12']
	 ) / 6, 0);
	break;    
    case 7: 
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] + $row_res_msa4['Area12'] + $row_res_msa5['Area12'] + $row_res_msa6['Area12']
	+ $row_res_msa7['Area12'] ) / 7, 0);
	break;    
    case 8:  
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] + $row_res_msa4['Area12'] + $row_res_msa5['Area12'] + $row_res_msa6['Area12']
	+ $row_res_msa7['Area12'] + $row_res_msa8['Area12'] ) / 8, 0);
	break;    
    case 9:  
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] + $row_res_msa4['Area12'] + $row_res_msa5['Area12'] + $row_res_msa6['Area12']
	+ $row_res_msa7['Area12'] + $row_res_msa8['Area12'] + $row_res_msa9['Area12'] ) / 9, 0);
	break;    
    case 10: 
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] + $row_res_msa4['Area12'] + $row_res_msa5['Area12'] + $row_res_msa6['Area12']
	+ $row_res_msa7['Area12'] + $row_res_msa8['Area12'] + $row_res_msa9['Area12'] + $row_res_msa10['Area12'] ) / 10, 0);
	break;    
    case 11: 
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] + $row_res_msa4['Area12'] + $row_res_msa5['Area12'] + $row_res_msa6['Area12']
	+ $row_res_msa7['Area12'] + $row_res_msa8['Area12'] + $row_res_msa9['Area12'] + $row_res_msa10['Area12'] + $row_res_msa11['Area12'] ) / 11, 0);
	break;    
    case 12: 
	$Area12xmesa = round(($row_res_msa1['Area12'] + $row_res_msa2['Area12'] + $row_res_msa3['Area12'] + $row_res_msa4['Area12'] + $row_res_msa5['Area12'] + $row_res_msa6['Area12']
	+ $row_res_msa7['Area12'] + $row_res_msa8['Area12'] + $row_res_msa9['Area12'] + $row_res_msa10['Area12'] + $row_res_msa11['Area12'] + $row_res_msa12['Area12'] ) / 12, 0);
	break;   
      }
	  
	  
	  
$Area7a12xmesa = $Area7xmesa + $Area8xmesa + $Area9xmesa + $Area10xmesa + $Area11xmesa + $Area12xmesa;

//resultados por area
mysql_select_db($database_vacantes, $vacantes);
$query_ar1an1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 1 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_actual)";
$ar1an1 = mysql_query($query_ar1an1, $vacantes) or die(mysql_error());
$row_ar1an1 = mysql_fetch_assoc($ar1an1);
$totalRows_ar1an1 = mysql_num_rows($ar1an1);
$ar1an1r = $row_ar1an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar1an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 1 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_anterior)";
$ar1an2 = mysql_query($query_ar1an2, $vacantes) or die(mysql_error());
$row_ar1an2 = mysql_fetch_assoc($ar1an2);
$totalRows_ar1an2 = mysql_num_rows($ar1an2);
$ar1an2r = $row_ar1an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar2an1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 2 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_actual)";
$ar2an1 = mysql_query($query_ar2an1, $vacantes) or die(mysql_error());
$row_ar2an1 = mysql_fetch_assoc($ar2an1);
$totalRows_ar2an1 = mysql_num_rows($ar2an1);
$ar2an1r = $row_ar2an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar2an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 2 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_anterior)";
$ar2an2 = mysql_query($query_ar2an2, $vacantes) or die(mysql_error());
$row_ar2an2 = mysql_fetch_assoc($ar2an2);
$totalRows_ar2an2 = mysql_num_rows($ar2an2);
$ar2an2r = $row_ar2an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar3an1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 3 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_actual)";
$ar3an1 = mysql_query($query_ar3an1, $vacantes) or die(mysql_error());
$row_ar3an1 = mysql_fetch_assoc($ar3an1);
$totalRows_ar3an1 = mysql_num_rows($ar3an1);
$ar3an1r = $row_ar3an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar3an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 3 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_anterior)";
$ar3an2 = mysql_query($query_ar3an2, $vacantes) or die(mysql_error());
$row_ar3an2 = mysql_fetch_assoc($ar3an2);
$totalRows_ar3an2 = mysql_num_rows($ar3an2);
$ar3an2r = $row_ar3an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar4an1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 4 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_actual)";
$ar4an1 = mysql_query($query_ar4an1, $vacantes) or die(mysql_error());
$row_ar4an1 = mysql_fetch_assoc($ar4an1);
$totalRows_ar4an1 = mysql_num_rows($ar4an1);
$ar4an1r = $row_ar4an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar4an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 4 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_anterior)";
$ar4an2 = mysql_query($query_ar4an2, $vacantes) or die(mysql_error());
$row_ar4an2 = mysql_fetch_assoc($ar4an2);
$totalRows_ar4an2 = mysql_num_rows($ar4an2);
$ar4an2r = $row_ar4an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar5an1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 5 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_actual)";
$ar5an1 = mysql_query($query_ar5an1, $vacantes) or die(mysql_error());
$row_ar5an1 = mysql_fetch_assoc($ar5an1);
$totalRows_ar5an1 = mysql_num_rows($ar5an1);
$ar5an1r = $row_ar5an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar5an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 5 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_anterior)";
$ar5an2 = mysql_query($query_ar5an2, $vacantes) or die(mysql_error());
$row_ar5an2 = mysql_fetch_assoc($ar5an2);
$totalRows_ar5an2 = mysql_num_rows($ar5an2);
$ar5an2r = $row_ar5an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar6an1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 6 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_actual)";
$ar6an1 = mysql_query($query_ar6an1, $vacantes) or die(mysql_error());
$row_ar6an1 = mysql_fetch_assoc($ar6an1);
$totalRows_ar6an1 = mysql_num_rows($ar6an1);
$ar6an1r = $row_ar6an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar6an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 6 AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_anterior)";
$ar6an2 = mysql_query($query_ar6an2, $vacantes) or die(mysql_error());
$row_ar6an2 = mysql_fetch_assoc($ar6an2);
$totalRows_ar6an2 = mysql_num_rows($ar6an2);
$ar6an2r = $row_ar6an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar7an1 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea IN (7,8,9,10,11,12) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_actual)";
$ar7an1 = mysql_query($query_ar7an1, $vacantes) or die(mysql_error());
$row_ar7an1 = mysql_fetch_assoc($ar7an1);
$totalRows_ar7an1 = mysql_num_rows($ar7an1);
$ar7an1r = $row_ar7an1['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_ar7an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea IN (7,8,9,10,11,12) AND ind_bajas.IDmotivo < 15 AND (MONTH(fecha_baja) <= $el_mes AND YEAR(fecha_baja) = $anio_anterior)";
$ar7an2 = mysql_query($query_ar7an2, $vacantes) or die(mysql_error());
$row_ar7an2 = mysql_fetch_assoc($ar7an2);
$totalRows_ar7an2 = mysql_num_rows($ar7an2);
$ar7an2r = $row_ar7an2['TOTAL'];

// Resultado actual total  año actual
$Tfini_ms1 = new DateTime($anio_actual . '-01-01');
$Tfini_ms1->modify('first day of this month');
$Tfini_msk1 = $Tfini_ms1->format('Y/m/d'); 

$Tfter_ms1 = new DateTime($anio_actual . '-' . $el_mes . '-01');
$Tfter_ms1->modify('last day of this month');
$Tfter_msk1 = $Tfter_ms1->format('Y/m/d'); 

$Tfini_ms2 = new DateTime($anio_anterior . '-01-01');
$Tfini_ms2->modify('first day of this month');
$Tfini_msk2 = $Tfini_ms2->format('Y/m/d'); 

$Tfter_ms2 = new DateTime($anio_anterior . '-' . $el_mes . '-01');
$Tfter_ms2->modify('last day of this month');
$Tfter_msk2 = $Tfter_ms2->format('Y/m/d'); 


mysql_select_db($database_vacantes, $vacantes);
$query_Toar1an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 1 AND ind_bajas.IDmotivo < 15 AND fecha_antiguedad <= '$Tfter_msk2' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk2')";
$Toar1an2 = mysql_query($query_Toar1an2, $vacantes) or die(mysql_error());
$row_Toar1an2 = mysql_fetch_assoc($Toar1an2);
$totalRows_Toar1an2 = mysql_num_rows($Toar1an2);
$Toar1an2r = $row_Toar1an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar2an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 2 AND ind_bajas.IDmotivo < 15 AND fecha_antiguedad <= '$Tfter_msk2' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk2')";
$Toar2an2 = mysql_query($query_Toar2an2, $vacantes) or die(mysql_error());
$row_Toar2an2 = mysql_fetch_assoc($Toar2an2);
$totalRows_Toar2an2 = mysql_num_rows($Toar2an2);
$Toar2an2r = $row_Toar2an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar3an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 3 AND ind_bajas.IDmotivo < 15 AND fecha_antiguedad <= '$Tfter_msk2' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk2')";
$Toar3an2 = mysql_query($query_Toar3an2, $vacantes) or die(mysql_error());
$row_Toar3an2 = mysql_fetch_assoc($Toar3an2);
$totalRows_Toar3an2 = mysql_num_rows($Toar3an2);
$Toar3an2r = $row_Toar3an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar4an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 4 AND ind_bajas.IDmotivo < 15 AND fecha_antiguedad <= '$Tfter_msk2' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk2')";
$Toar4an2 = mysql_query($query_Toar4an2, $vacantes) or die(mysql_error());
$row_Toar4an2 = mysql_fetch_assoc($Toar4an2);
$totalRows_Toar4an2 = mysql_num_rows($Toar4an2);
$Toar4an2r = $row_Toar4an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar5an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 5 AND ind_bajas.IDmotivo < 15 AND fecha_antiguedad <= '$Tfter_msk2' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk2')";
$Toar5an2 = mysql_query($query_Toar5an2, $vacantes) or die(mysql_error());
$row_Toar5an2 = mysql_fetch_assoc($Toar5an2);
$totalRows_Toar5an2 = mysql_num_rows($Toar5an2);
$Toar5an2r = $row_Toar5an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar6an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea = 6 AND ind_bajas.IDmotivo < 15 AND fecha_antiguedad <= '$Tfter_msk2' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk2')";
$Toar6an2 = mysql_query($query_Toar6an2, $vacantes) or die(mysql_error());
$row_Toar6an2 = mysql_fetch_assoc($Toar6an2);
$totalRows_Toar6an2 = mysql_num_rows($Toar6an2);
$Toar6an2r = $row_Toar6an2['TOTAL'];

mysql_select_db($database_vacantes, $vacantes);
$query_Toar7an2 = "SELECT Count(ind_bajas.AREA_COSTOS) AS TOTAL FROM ind_bajas WHERE IDarea IN (7,8,9,10,11,12) AND ind_bajas.IDmotivo < 15 AND fecha_antiguedad <= '$Tfini_msk2' AND (ind_bajas.fecha_baja IS NULL OR ind_bajas.fecha_baja >= '$Tfini_msk2')";
$Toar7an2 = mysql_query($query_Toar7an2, $vacantes) or die(mysql_error());
$row_Toar7an2 = mysql_fetch_assoc($Toar7an2);
$totalRows_Toar7an2 = mysql_num_rows($Toar7an2);
$Toar7an2r = $row_Toar7an2['TOTAL'];

// por antiguedad año acutal
mysql_select_db($database_vacantes, $vacantes);
$query_por_antig1 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad, ind_bajas.SUCURSAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$el_anio')  AND IDantig = 1 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC";
$por_antig1 = mysql_query($query_por_antig1, $vacantes) or die(mysql_error());
$row_por_antig1 = mysql_fetch_assoc($por_antig1);
$totalRows_por_antig1 = mysql_num_rows($por_antig1);
if($totalRows_por_antig1 == 0){$ceroatres = 0;} else {$ceroatres = $row_por_antig1['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antig2 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad, ind_bajas.SUCURSAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$el_anio')  AND IDantig = 2 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC ";
$por_antig2 = mysql_query($query_por_antig2, $vacantes) or die(mysql_error());
$row_por_antig2 = mysql_fetch_assoc($por_antig2);
$totalRows_por_antig2 = mysql_num_rows($por_antig2);
if($totalRows_por_antig2 == 0){$tresaseis = 0;} else {$tresaseis = $row_por_antig2['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antig3 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad, ind_bajas.SUCURSAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$el_anio')  AND IDantig = 3 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC ";
$por_antig3 = mysql_query($query_por_antig3, $vacantes) or die(mysql_error());
$row_por_antig3 = mysql_fetch_assoc($por_antig3);
$totalRows_por_antig3 = mysql_num_rows($por_antig3);
if($totalRows_por_antig3 == 0){$seisadoce = 0;} else {$seisadoce = $row_por_antig3['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antig4= "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad, ind_bajas.SUCURSAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$el_anio')  AND IDantig = 4 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC ";
$por_antig4= mysql_query($query_por_antig4, $vacantes) or die(mysql_error());
$row_por_antig4= mysql_fetch_assoc($por_antig4);
$totalRows_por_antig4= mysql_num_rows($por_antig4);
if($totalRows_por_antig4 == 0){$masdedoce = 0;} else {$masdedoce = $row_por_antig4['antiguedad'];}

// por antiguedad año anterior
mysql_select_db($database_vacantes, $vacantes);
$query_por_antigb1 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad, ind_bajas.SUCURSAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior')  AND IDantig = 1 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC";
$por_antigb1 = mysql_query($query_por_antigb1, $vacantes) or die(mysql_error());
$row_por_antigb1 = mysql_fetch_assoc($por_antigb1);
$totalRows_por_antigb1 = mysql_num_rows($por_antigb1);
if($totalRows_por_antigb1 == 0){$ceroatres2 = 0;} else {$ceroatres2 = $row_por_antigb1['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antigb2 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad, ind_bajas.SUCURSAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior')  AND IDantig = 2 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC ";
$por_antigb2 = mysql_query($query_por_antigb2, $vacantes) or die(mysql_error());
$row_por_antigb2 = mysql_fetch_assoc($por_antigb2);
$totalRows_por_antigb2 = mysql_num_rows($por_antigb2);
if($totalRows_por_antig2 == 0){$tresaseis2 = 0;} else {$tresaseis2 = $row_por_antig2['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antigb3 = "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad, ind_bajas.SUCURSAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior')  AND IDantig = 3 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC ";
$por_antigb3 = mysql_query($query_por_antigb3, $vacantes) or die(mysql_error());
$row_por_antigb3 = mysql_fetch_assoc($por_antigb3);
$totalRows_por_antigb3 = mysql_num_rows($por_antigb3);
if($totalRows_por_antigb3 == 0){$seisadoce2 = 0;} else {$seisadoce2 = $row_por_antigb3['antiguedad'];}

mysql_select_db($database_vacantes, $vacantes);
$query_por_antigb4= "SELECT ind_bajas.IDantig, Count(ind_bajas.IDantig) AS antiguedad, ind_bajas.SUCURSAL FROM ind_bajas WHERE ind_bajas.IDmotivo < 15 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior')  AND IDantig = 4 GROUP BY ind_bajas.IDantig ORDER BY ind_bajas.IDantig ASC ";
$por_antigb4= mysql_query($query_por_antigb4, $vacantes) or die(mysql_error());
$row_por_antigb4= mysql_fetch_assoc($por_antigb4);
$totalRows_por_antigb4= mysql_num_rows($por_antigb4);
if($totalRows_por_antigb4 == 0){$masdedoce2 = 0;} else {$masdedoce2 = $row_por_antigb4['antiguedad'];}


//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot11 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 1 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot11 = mysql_query($query_mot11, $vacantes) or die(mysql_error());
$row_mot11 = mysql_fetch_assoc($mot11);
$m11 = $row_mot11['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot12 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 1  AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot12 = mysql_query($query_mot12, $vacantes) or die(mysql_error());
$row_mot12 = mysql_fetch_assoc($mot12);
$m12 = $row_mot12['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot21 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 2 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot21 = mysql_query($query_mot21, $vacantes) or die(mysql_error());
$row_mot21 = mysql_fetch_assoc($mot21);
$m21 = $row_mot21['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot22 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 2 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot22 = mysql_query($query_mot22, $vacantes) or die(mysql_error());
$row_mot22 = mysql_fetch_assoc($mot22);
$m22 = $row_mot22['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot31 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 3 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot31 = mysql_query($query_mot31, $vacantes) or die(mysql_error());
$row_mot31 = mysql_fetch_assoc($mot31);
$m31 = $row_mot31['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot32 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 3 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot32 = mysql_query($query_mot32, $vacantes) or die(mysql_error());
$row_mot32 = mysql_fetch_assoc($mot32);
$m32 = $row_mot32['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot41 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 4 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot41 = mysql_query($query_mot41, $vacantes) or die(mysql_error());
$row_mot41 = mysql_fetch_assoc($mot41);
$m41 = $row_mot41['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot42 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 4 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot42 = mysql_query($query_mot42, $vacantes) or die(mysql_error());
$row_mot42 = mysql_fetch_assoc($mot42);
$m42 = $row_mot42['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot51 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 5 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot51 = mysql_query($query_mot51, $vacantes) or die(mysql_error());
$row_mot51 = mysql_fetch_assoc($mot51);
$m51 = $row_mot51['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot52 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 5 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot52 = mysql_query($query_mot52, $vacantes) or die(mysql_error());
$row_mot52 = mysql_fetch_assoc($mot52);
$m52 = $row_mot52['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot61 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 6 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot61 = mysql_query($query_mot61, $vacantes) or die(mysql_error());
$row_mot61 = mysql_fetch_assoc($mot61);
$m61 = $row_mot61['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot62 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 6 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot62 = mysql_query($query_mot62, $vacantes) or die(mysql_error());
$row_mot62 = mysql_fetch_assoc($mot62);
$m62 = $row_mot62['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot71 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 7 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot71 = mysql_query($query_mot71, $vacantes) or die(mysql_error());
$row_mot71 = mysql_fetch_assoc($mot71);
$m71 = $row_mot71['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot72 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 7 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot72 = mysql_query($query_mot72, $vacantes) or die(mysql_error());
$row_mot72 = mysql_fetch_assoc($mot72);
$m72 = $row_mot72['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot81 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 8 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot81 = mysql_query($query_mot81, $vacantes) or die(mysql_error());
$row_mot81 = mysql_fetch_assoc($mot81);
$m81 = $row_mot81['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot82 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 8 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot82 = mysql_query($query_mot82, $vacantes) or die(mysql_error());
$row_mot82 = mysql_fetch_assoc($mot82);
$m82 = $row_mot82['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot91 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 9 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot91 = mysql_query($query_mot91, $vacantes) or die(mysql_error());
$row_mot91 = mysql_fetch_assoc($mot91);
$m91 = $row_mot91['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot92 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 9 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot92 = mysql_query($query_mot92, $vacantes) or die(mysql_error());
$row_mot92 = mysql_fetch_assoc($mot92);
$m92 = $row_mot92['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot101 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 10 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot101 = mysql_query($query_mot101, $vacantes) or die(mysql_error());
$row_mot101 = mysql_fetch_assoc($mot101);
$m101 = $row_mot101['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot102 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 10 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot102 = mysql_query($query_mot102, $vacantes) or die(mysql_error());
$row_mot102 = mysql_fetch_assoc($mot102);
$m102 = $row_mot102['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot111 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 11 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot111 = mysql_query($query_mot111, $vacantes) or die(mysql_error());
$row_mot111 = mysql_fetch_assoc($mot111);
$m111 = $row_mot111['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot112 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 11 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot112 = mysql_query($query_mot112, $vacantes) or die(mysql_error());
$row_mot112 = mysql_fetch_assoc($mot112);
$m112 = $row_mot112['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot121 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 12 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot121 = mysql_query($query_mot121, $vacantes) or die(mysql_error());
$row_mot121 = mysql_fetch_assoc($mot121);
$m121 = $row_mot121['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot122 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 12 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot122 = mysql_query($query_mot122, $vacantes) or die(mysql_error());
$row_mot122 = mysql_fetch_assoc($mot122);
$m122 = $row_mot122['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot131 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 13 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot131 = mysql_query($query_mot131, $vacantes) or die(mysql_error());
$row_mot131 = mysql_fetch_assoc($mot131);
$m131 = $row_mot131['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot132 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 13 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot132 = mysql_query($query_mot132, $vacantes) or die(mysql_error());
$row_mot132 = mysql_fetch_assoc($mot132);
$m132 = $row_mot132['motivos'];

//Mes Actual Motivos
mysql_select_db($database_vacantes, $vacantes);
$query_mot141 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 14 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_actual') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot141 = mysql_query($query_mot141, $vacantes) or die(mysql_error());
$row_mot141 = mysql_fetch_assoc($mot141);
$m141 = $row_mot141['motivos'];

mysql_select_db($database_vacantes, $vacantes);
$query_mot142 = "SELECT Count(ind_bajas.IDmotivo) AS motivos FROM ind_bajas LEFT JOIN vac_motivo_baja ON vac_motivo_baja.IDmotivo = ind_bajas.IDmotivo WHERE ind_bajas.IDmotivo = 14 AND (ind_bajas.baja_mes <= '$el_mes' AND ind_bajas.baja_anio = '$anio_anterior') GROUP BY ind_bajas.IDmotivo ORDER BY vac_motivo_baja.IDmotivo_baja_tipo ASC";
$mot142 = mysql_query($query_mot142, $vacantes) or die(mysql_error());
$row_mot142 = mysql_fetch_assoc($mot142);
$m142 = $row_mot142['motivos'];

// el mes
  switch ($el_mes) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
      }
	  
	if ($ar1an1r == 0) {$area11 = 0;} else {$area11 = $ar1an1r;}
    if ($ar2an1r == 0) {$area21 = 0;} else {$area21 = $ar2an1r;}
    if ($ar3an1r == 0) {$area31 = 0;} else {$area31 = $ar3an1r;}
    if ($ar4an1r == 0) {$area41 = 0;} else {$area41 = $ar4an1r;}
    if ($ar5an1r == 0) {$area51 = 0;} else {$area51 = $ar5an1r;}
    if ($ar6an1r == 0) {$area61 = 0;} else {$area61 = $ar6an1r;}
    if ($ar7an1r == 0) {$area71 = 0;} else {$area71 = $ar7an1r;}
    if ($ar1an2r == 0) {$area12 = 0;} else {$area12 = $ar1an2r;}
    if ($ar2an2r == 0) {$area22 = 0;} else {$area22 = $ar2an2r;}
    if ($ar3an2r == 0) {$area32 = 0;} else {$area32 = $ar3an2r;}
    if ($ar4an2r == 0) {$area42 = 0;} else {$area42 = $ar4an2r;}
    if ($ar5an2r == 0) {$area52 = 0;} else {$area52 = $ar5an2r;}
    if ($ar6an2r == 0) {$area62 = 0;} else {$area62 = $ar6an2r;}
    if ($ar7an2r == 0) {$area72 = 0;} else {$area72 = $ar7an2r;}

    if ($m11 == 0) {$m11r = 0;} else {$m11r = $m11;}
    if ($m21 == 0) {$m21r = 0;} else {$m21r = $m21;}
    if ($m31 == 0) {$m31r = 0;} else {$m31r = $m31;}
    if ($m41 == 0) {$m41r = 0;} else {$m41r = $m41;}
    if ($m51 == 0) {$m51r = 0;} else {$m51r = $m51;}
    if ($m61 == 0) {$m61r = 0;} else {$m61r = $m61;}
    if ($m71 == 0) {$m71r = 0;} else {$m71r = $m71;}
    if ($m81 == 0) {$m81r = 0;} else {$m81r = $m81;}
    if ($m91 == 0) {$m91r = 0;} else {$m91r = $m91;}
    if ($m101 == 0) {$m101r = 0;} else {$m101r = $m101;}
    if ($m111 == 0) {$m111r = 0;} else {$m111r = $m111;}
    if ($m121 == 0) {$m121r = 0;} else {$m121r = $m121;}
    if ($m131 == 0) {$m131r = 0;} else {$m131r = $m131;}
    if ($m141 == 0) {$m141r = 0;} else {$m141r = $m141;}
    if ($m12 == 0) {$m12r = 0;} else {$m12r = $m12;}
    if ($m22 == 0) {$m22r = 0;} else {$m22r = $m22;}
    if ($m32 == 0) {$m32r = 0;} else {$m32r = $m32;}
    if ($m42 == 0) {$m42r = 0;} else {$m42r = $m42;}
    if ($m52 == 0) {$m52r = 0;} else {$m52r = $m52;}
    if ($m62 == 0) {$m62r = 0;} else {$m62r = $m62;}
    if ($m72 == 0) {$m72r = 0;} else {$m72r = $m72;}
    if ($m82 == 0) {$m82r = 0;} else {$m82r = $m82;}
    if ($m92 == 0) {$m92r = 0;} else {$m92r = $m92;}
    if ($m102 == 0) {$m102r = 0;} else {$m102r = $m102;}
    if ($m112 == 0) {$m112r = 0;} else {$m112r = $m112;}
    if ($m122 == 0) {$m122r = 0;} else {$m122r = $m122;}
    if ($m132 == 0) {$m132r = 0;} else {$m132r = $m132;}
    if ($m142 == 0) {$m142r = 0;} else {$m142r = $m142;}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
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

	<script src="https://www.gstatic.com/charts/loader.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/sucursal.js"></script>
	<script src="global_assets/js/sucursal2.js"></script>
	<script src="global_assets/js/area.js"></script>
    <script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
    
	<script src="assets/rot_antig.js"></script>
	<script src="assets/rot_area.js"></script>
	<script src="assets/rot_motivo.js"></script>

	
    <!-- /theme JS files -->
    <script type="text/javascript">
    var ceroatres = <?php echo $ceroatres; ?>;
    var tresaseis = <?php echo $tresaseis; ?>;
    var seisadoce = <?php echo $seisadoce; ?>;
    var masdedoce = <?php echo $masdedoce; ?>;
    var ceroatres2 = <?php echo $ceroatres2; ?>;
    var tresaseis2 = <?php echo $tresaseis2; ?>;
    var seisadoce2 = <?php echo $seisadoce2; ?>;
    var masdedoce2 = <?php echo $masdedoce2; ?>;
    var anio_actual = <?php echo $anio_actual; ?>;
    var anio_anterior = <?php echo $anio_anterior; ?>;
	
    var area11 = <?php echo $area11; ?>;
    var area21 = <?php echo $area21; ?>;
    var area31 = <?php echo $area31; ?>;
    var area41 = <?php echo $area41; ?>;
    var area51 = <?php echo $area51; ?>;
    var area61 = <?php echo $area61; ?>;
    var area71 = <?php echo $area71; ?>;
    var area12 = <?php echo $area12; ?>;
    var area22 = <?php echo $area22; ?>;
    var area32 = <?php echo $area32; ?>;
    var area42 = <?php echo $area42; ?>;
    var area52 = <?php echo $area52; ?>;
    var area62 = <?php echo $area62; ?>;
    var area72 = <?php echo $area72; ?>;
	
	var  m11 = <?php echo  $m11r; ?>;
    var  m21 = <?php echo  $m21r; ?>;
    var  m31 = <?php echo  $m31r; ?>;
    var  m41 = <?php echo  $m41r; ?>;
    var  m51 = <?php echo  $m51r; ?>;
    var  m61 = <?php echo  $m61r; ?>;
    var  m71 = <?php echo  $m71r; ?>;
    var  m81 = <?php echo  $m81r; ?>;
    var  m91 = <?php echo  $m91r; ?>;
    var m101 = <?php echo $m101r; ?>;
    var m111 = <?php echo $m111r; ?>;
    var m121 = <?php echo $m121r; ?>;
    var m131 = <?php echo $m131r; ?>;
    var m141 = <?php echo $m141r; ?>;
    var  m12 = <?php echo  $m12r; ?>;
    var  m22 = <?php echo  $m22r; ?>;
    var  m32 = <?php echo  $m32r; ?>;
    var  m42 = <?php echo  $m42r; ?>;
    var  m52 = <?php echo  $m52r; ?>;
    var  m62 = <?php echo  $m62r; ?>;
    var  m72 = <?php echo  $m72r; ?>;
    var  m82 = <?php echo  $m82r; ?>;
    var  m92 = <?php echo  $m92r; ?>;
    var m102 = <?php echo $m102r; ?>;
    var m112 = <?php echo $m112r; ?>;
    var m122 = <?php echo $m122r; ?>;
    var m132 = <?php echo $m132r; ?>;
    var m142 = <?php echo $m142r; ?>;

</script>
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
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Rotación Acumulado Total Compañía.</h5>
						</div>
						
                            <p>&nbsp;</p>
												<form method="POST" action="admin_indicadors_ac.php">

					<table class="table">
							<tr>
                            <td>
							 <select name="el_anio" class="form-control">
							   <option value="2020"<?php if (!(strcmp($el_anio, 2020))) {echo "selected=\"selected\"";} ?>>2020</option>
							   <option value="2021"<?php if (!(strcmp($el_anio, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
							   <option value="2022"<?php if (!(strcmp($el_anio, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
							   <option value="2023"<?php if (!(strcmp($el_anio, 2023))) {echo "selected=\"selected\"";} ?>>2023</option>
							   <option value="2024"<?php if (!(strcmp($el_anio, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
							   <option value="2025"<?php if (!(strcmp($el_anio, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
							   </select>
                            </td>
							<td>
                             <select name="el_mes" class="form-control">
                               <option value="" <?php if (!(strcmp("", $el_mes))) {echo "selected=\"selected\"";} ?>>Mes: Actual</option>
                               <?php do {  ?>
                               <option value="<?php echo $row_mes['IDmes']?>"<?php if (!(strcmp($row_mes['IDmes'], $el_mes)))
							   {echo "selected=\"selected\"";} ?>><?php echo $row_mes['mes']?></option>
                               <?php
                              } while ($row_mes = mysql_fetch_assoc($mes));
                              $rows = mysql_num_rows($mes);
                              if($rows > 0) {
                                  mysql_data_seek($mes, 0);
                                  $row_mes = mysql_fetch_assoc($mes);
                              } ?></select>
                            </td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                             </td>
					      </tr>
				    </table>
                    </form>
                            


					<div class="panel-body">
					<p>El índice de rotación de personal es indicador que permite medir cuál es el flujo de salidas y entradas de empleados en nuestra empresa y que 
					sirve para determinar estrategias de retención del Capital Humano.</br>
					En la presente sección podrás conocer cuál es el comportamiento de dicho indicador para cada sucursal de la Organización.</br>
					Cuando el índice de rotación es alto, es responsabilidad de Recursos Humanos entender las causas y buscar estrategias 
					de solución que involucren a todos los líderes y responsables para solucionarlo de forma permanente.
					</p>
                    </div>
               	  </div>


					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Resultado acumulado</h5>
						</div>

						<div class="panel-body">
							<p class="content-group">En la siguiente tabla, se muestra el resultado mensual y la tendencia anual. El semáforo muestra el nivel de cumplimiento de forma anualizada.</p>
							<h5>Resultado <?php echo $anio_anterior; ?>:<strong> <?php echo round($row_resultados_anterior['resultado'],0); ?>%</strong> - 
							Objetivo de Reducción <?php echo $anio_actual; ?>:<strong> <?php echo round($row_resultados['reduccion'],0); ?>%</strong> = 
							Objetivo <?php echo $anio_actual; ?>:  <strong><?php echo $row_resultados_anterior['resultado'] - $row_resultados['reduccion']; ?>%</strong></h5>
							<h5>Objetivo mensual:<strong> <?php echo round(($row_resultados_anterior['resultado'] - $row_resultados['reduccion'])/12,0); ?>%</strong> </h5>
								<div class="content-group">
                                
				<div class="table-responsive">
                    <table class="table">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>Ene </th>
                      <th>Feb</th>
                      <th>Mar</th>
                      <th>Abr</th>
                      <th>May</th>
                      <th>Jun</th>
                      <th>Jul</th>
                      <th>Ags</th>
                      <th>Sep</th>
                      <th>Oct</th>
                      <th>Nov</th>
                      <th>Dic</th>
                      <th class="bg-danger">ACUMULADO</th>
                      <th class="bg-danger">OBJETIVO</th>
                      <th class="bg-danger">SEMAFORO</th>
               		 </tr>
                    </thead>
                    <tbody>
                 	<tr>
                    <td>
					<?php 
					if($RotTotalM1 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM1 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM1 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM1 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM2 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM2 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM2 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM2 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM3 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM3 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM3 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM3 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM4 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM4 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM4 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM4 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM5 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM5 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM5 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM5 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM6 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM6 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM6 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM6 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM7 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM7 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM7 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM7 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM8 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM8 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM8 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM8 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM9 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM9 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM9 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM9 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM10 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM10 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM10 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM10 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM11 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM11 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM11 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM11 * 100, 1) . "%";}
					?>
                    </td>
                    <td>
					<?php 
					if($RotTotalM12 == 0)
					{ echo "-"; }
					else if(( round($RotTotalM12 * 100, 1) > ($row_resultados['objetivo'] / 12))) 
					{echo "<i class='icon-checkmark-circle text-danger position-left'></i>". round($RotTotalM12 * 100, 1) . "%";}
					else
					{echo "<i class='icon-checkmark-circle text-success position-left'></i>". round($RotTotalM12 * 100, 1) . "%";}
					?>
                    </td>
                    <td><?php echo round($Acumulado * 100, 0); ?>%</td>
                    <td><?php echo round((($row_resultados['objetivo'] / 12) * $el_mes), 0); ?>%</td>
                    <td><?php 
					$a_ = round((($row_resultados['objetivo'] / 12) * $el_mes), 0);
					$b_ = round($Acumulado * 100, 1);
					if ($a_ < $b_) {echo "<i class='icon-checkmark-circle text-danger position-left'></i>ROJO";} else {echo "<i class='icon-checkmark-circle text-success position-left'></i>VERDE";}
					 ?></td>
                    </tr>
                    </tbody>
                   </table> 
                       </div>
                       </div>
				</div>
			</div>


					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Resultado acumulado por Área</h5>
						</div>

						<div class="panel-body">
							<p class="content-group">A continuación, se muestra el resultado de rotación por área con corte al mes de <strong><?php echo $elmes; ?></strong>.</p>
								<div class="content-group">



				<div class="table-responsive">
                    <table class="table table-bordered">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>ÁREA </th>
                      <th class="col-lg-1 bg-danger">Bajas <?php echo $anio_anterior; ?></th>
                      <th class="col-lg-1 bg-danger">Activos <?php echo $anio_anterior; ?></th>
                      <th class="col-lg-2 bg-danger">Rotación <?php echo $anio_anterior; ?></th>
                      <th class="col-lg-1 bg-blue">Bajas <?php echo $anio_actual; ?></th>
                      <th class="col-lg-1 bg-blue">Activos <?php echo $anio_actual; ?></th>
                      <th class="col-lg-2 bg-blue">Rotación <?php echo $anio_actual; ?></th>
               		 </tr>
                    </thead>
                    <tfoot> 
                    <tr> 
                      <th>Total </th>
                      <th><?php $TTBan2 = $ar1an2r + $ar2an2r + $ar3an2r + $ar4an2r + $ar5an2r + $ar6an2r + $ar7an2r;  echo $TTBan2; ?></th>
                      <th><?php $TTAan2 = $Area1xmesa + $Area2xmesa + $Area3xmesa + $Area4xmesa + $Area5xmesa + $Area6xmesa + $Area7a12xmesa;  echo $TTAan2; ?></th>
                      <th><?php $Prev2 = ($TTBan2 / $TTAan2) * 100;  echo round($Prev2 , 0); ?>%</th>
                      <th><?php $TTBan1 = $ar1an1r + $ar2an1r + $ar3an1r + $ar4an1r + $ar5an1r + $ar6an1r + $ar7an1r;  echo $TTBan1; ?></th>
                      <th><?php $TTAan1 = $Area1xmes + $Area2xmes + $Area3xmes + $Area4xmes + $Area5xmes + $Area6xmes + $Area7a12xmes;  echo $TTAan1; ?></th>
                      <th><?php $Prev1 = ($TTBan1 / $TTAan1) * 100;  echo round($Prev1 , 0); ?>%</th>
               		 </tr>
                    </tfoot>
                    <tbody>
                 	<tr>
                    <td>Almacén</td>
                    <td><?php echo $ar1an2r; ?></td>
                    <td><?php echo $Area1xmesa; ?></td>
                    <td><?php if ($ar1an2r > 0) {echo (round($ar1an2r / $Area1xmesa, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar1an1r; ?></td>
                    <td><?php echo $Area1xmes; ?></td>
                    <td><?php if ($ar1an1r > 0) {echo (round($ar1an1r / $Area1xmes, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Almacén Detalle</td>
                    <td><?php echo $ar2an2r; ?></td>
                    <td><?php echo $Area2xmesa; ?></td>
                    <td><?php if ($ar2an2r > 0) {echo (round($ar2an2r / $Area2xmesa, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar2an1r; ?></td>
                    <td><?php echo $Area2xmes; ?></td>
                    <td><?php if ($ar2an1r > 0) {echo (round($ar2an1r / $Area2xmes, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Distribución</td>
                    <td><?php echo $ar3an2r; ?></td>
                    <td><?php echo $Area3xmesa; ?></td>
                    <td><?php if ($ar3an2r > 0) {echo (round($ar3an2r / $Area3xmesa, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar3an1r; ?></td>
                    <td><?php echo $Area3xmes; ?></td>
                    <td><?php if ($ar3an1r > 0) {echo (round($ar3an1r / $Area3xmes, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Distribución Detalle</td>
                    <td><?php echo $ar4an2r; ?></td>
                    <td><?php echo $Area4xmesa; ?></td>
                    <td><?php if ($ar4an2r > 0) {echo (round($ar4an2r / $Area4xmesa, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar4an1r; ?></td>
                    <td><?php echo $Area4xmes; ?></td>
                    <td><?php if ($ar4an1r > 0) {echo (round($ar4an1r / $Area4xmes, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Ventas</td>
                    <td><?php echo $ar5an2r; ?></td>
                    <td><?php echo $Area5xmesa; ?></td>
                    <td><?php if ($ar5an2r > 0) {echo (round($ar5an2r / $Area5xmesa, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar5an1r; ?></td>
                    <td><?php echo $Area5xmes; ?></td>
                    <td><?php if ($ar5an1r > 0) {echo (round($ar5an1r / $Area5xmes, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Ventas Detalle</td>
                    <td><?php echo $ar6an2r; ?></td>
                    <td><?php echo $Area6xmesa; ?></td>
                    <td><?php if ($ar6an2r > 0) {echo (round($ar6an2r / $Area6xmesa, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar6an1r; ?></td>
                    <td><?php echo $Area6xmes; ?></td>
                    <td><?php if ($ar6an1r > 0) {echo (round($ar6an1r / $Area6xmes, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                 	<tr>
                    <td>Administración</td>
                    <td><?php echo $ar7an2r; ?></td>
                    <td><?php echo $Area7a12xmesa; ?></td>
                    <td><?php if ($ar7an2r > 0) {echo (round($ar7an2r / $Area7a12xmesa, 4)) * 100;} else {echo "0";} ?>%</td>
                    <td><?php echo $ar7an1r; ?></td>
                    <td><?php echo $Area7a12xmes; ?></td>
                    <td><?php if ($ar7an1r > 0) {echo (round($ar7an1r / $Area7a12xmes, 4)) * 100;} else {echo "0";} ?>%</td>
                    </tr>
                    </tbody>
                   </table> 
					</div>                                
                   </div>
				</div>
			</div>


				<div class="row">
					<div class="col-md-6">
					<!-- Exploded pie charts -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Rotación por Antiguedad</h5>
						</div>

						<div class="panel-body">

							<div class="row">
								<div class="col-md">
									<div class="chart-container text-center content-group">
										<div class="chart" id="rot_antig"></div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- /exploded pie charts -->
					</div>
                    
                    
					<div class="col-md-6">
					<!-- Exploded pie charts -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Rotación por Área</h5>
						</div>

						<div class="panel-body">

							<div class="row">
								<div class="col-md">
									<div class="chart-container text-center content-group">
										<div class="chart" id="rot_areas"></div>
									</div>
								</div>
								
							</div>
						</div>
					</div>
					<!-- /exploded pie charts -->
					</div>
				</div>


					<!-- Exploded pie charts -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Rotación por Motivo</h5>
						</div>

						<div class="panel-body">
							<p class="content-group">Los motivos señalados en color azul, corresponden a motivos no deseados, mientras que los rojos, corresponden a motivos deseados. </p>
							<p class="content-group">Actual: <?php echo $anio_actual; ?>. Anterior: <?php echo $anio_anterior; ?> </p>

									<div class="chart-container text-center content-group">
										<div class="chart" id="rot_motivo"></div>
							</div>
						</div>
					</div>
					<!-- /exploded pie charts -->
					</div>



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