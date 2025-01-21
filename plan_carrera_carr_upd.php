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
$query_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.IDmatriz, prod_activos.IDarea,  prod_activos.IDpuesto, prod_activos.IDaplica_SED, pc_semaforo.IDplan, pc_semaforo.c_capa1, pc_semaforo.c_capa2, pc_semaforo.a_discprog_c, pc_semaforo.a_puntyasist_c, pc_semaforo.a_desemp_c, pc_semaforo.a_antig_c, pc_semaforo.b_puesto_c, pc_semaforo.c_capa1_c, pc_semaforo.c_capa2_c, pc_semaforo.a_discprog, pc_semaforo.a_puntyasist, pc_semaforo.a_desemp, pc_semaforo.a_antig, pc_semaforo.b_puesto, pc_semaforo.estatus_pc FROM prod_activos LEFT JOIN pc_semaforo ON pc_semaforo.IDempleado = prod_activos.IDempleado WHERE prod_activos.IDempleado = '$IDempleado'"; 
$detalle = mysql_query($query_detalle, $vacantes) or die(mysql_error());
$row_detalle = mysql_fetch_assoc($detalle);
$totalRows_detalle = mysql_num_rows($detalle);
$el_puest = $row_detalle['IDpuesto'];

if ($row_detalle['a_discprog'] == 1) { $a_discprog = 10;} else { $a_discprog = 0; }
if ($row_detalle['a_puntyasist'] == 1) { $a_puntyasist = 5;} else { $a_puntyasist = 0;}
if ($row_detalle['a_desemp'] == 1) { $a_desemp = 10;} else { $a_desemp = 0;}
if ($row_detalle['a_antig'] == 1) { $a_antig = 5;} else { $a_antig = 0;}
if ($row_detalle['b_puesto'] == 1) { $b_puesto = 20;} else { $b_puesto = 0;}
if ($row_detalle['c_capa1'] == 1) { $c_capa1 = 25;} else { $b_puesto = 0;}
if ($row_detalle['c_capa2'] == 1) { $c_capa2 = 25;} else { $c_capa2 = 0;}

$avance = $a_discprog + $a_puntyasist + $a_desemp + $a_antig + $b_puesto + $c_capa1 + $c_capa2;

echo $avance;

$updateSQL = "UPDATE pc_semaforo SET avance_pc = $avance WHERE IDempleado = '$IDempleado'";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "plan_carrera_carr.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));


?>