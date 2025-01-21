<?php require_once('Connections/vacantes.php'); ?>
<?php
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
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
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
$semana_inicio = $semana - 8;
if ($semana_inicio < 1){$semana_inicio = 1;}
$semana_fin = $semana;

$IDempleado = $_GET['IDempleado'];	
mysql_select_db($database_vacantes, $vacantes);
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDarea,  prod_activos.IDpuesto, prod_activos.IDaplica_SED, pc_semaforo.IDplan, pc_semaforo.estatus, pc_semaforo.reqa, pc_semaforo.reqb, pc_semaforo.reqc, pc_semaforo.reqd, pc_semaforo.reqe, pc_semaforo.reqf FROM prod_activos LEFT JOIN pc_semaforo ON pc_semaforo.IDempleado = prod_activos.IDempleado WHERE prod_activos.IDempleado = '$IDempleado'"; 
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
$el_puest = $row_detalle['IDpuesto'];

$reqa = $row_detalle['reqa'];
$query_req_A = "SELECT * FROM pc_kpis WHERE pc_kpis.IDpuesto = '$el_puest' AND pc_kpis.IDreq = 'reqa' AND pc_kpis.valor = '$reqa'"; 
$req_A = mysql_query($query_req_A, $vacantes) or die(mysql_error());
$row_req_A = mysql_fetch_assoc($req_A);

$reqb = $row_detalle['reqb'];
$query_req_B = "SELECT * FROM pc_kpis WHERE pc_kpis.IDpuesto = '$el_puest' AND pc_kpis.IDreq = 'reqb' AND pc_kpis.valor = '$reqb'"; 
$req_B = mysql_query($query_req_B, $vacantes) or die(mysql_error());
$row_req_B = mysql_fetch_assoc($req_B);

$reqc = $row_detalle['reqc'];
$query_req_C = "SELECT * FROM pc_kpis WHERE pc_kpis.IDpuesto = '$el_puest' AND pc_kpis.IDreq = 'reqc' AND pc_kpis.valor = '$reqc'"; 
$req_C = mysql_query($query_req_C, $vacantes) or die(mysql_error());
$row_req_C = mysql_fetch_assoc($req_C);

$reqd = $row_detalle['reqd'];
$query_req_D = "SELECT * FROM pc_kpis WHERE pc_kpis.IDpuesto = '$el_puest' AND pc_kpis.IDreq = 'reqd' AND pc_kpis.valor = '$reqd'"; 
$req_D = mysql_query($query_req_D, $vacantes) or die(mysql_error());
$row_req_D = mysql_fetch_assoc($req_D);

$reqe = $row_detalle['reqe'];
if ($reqe == 1) {$reqe_E = 10;} else {$reqe_E = 0;}

$reqf = $row_detalle['reqf'];
if ($reqf == 1) {$reqe_F = 10;} else {$reqe_F = 0;}


$avance = $row_req_A['peso'] + $row_req_B['peso'] + $row_req_C['peso'] + $row_req_D['peso'] + $reqe_E + + $reqe_F;

echo $avance;

$updateSQL = "UPDATE pc_semaforo SET estatus = $avance WHERE IDempleado = '$IDempleado'";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "plan_carrera_inv.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
?>