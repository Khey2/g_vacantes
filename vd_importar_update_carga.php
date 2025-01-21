<?php require_once('Connections/vacantes.php'); ?>
<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
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
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//globales
$mi_fecha =  date('Y/m/d');
//$el_mes = date("m") - 1;
$el_mes = 11;
if (strlen($el_mes) == 1) {$el_mes_compara = "0".$el_mes;} else {$el_mes_compara = $el_mes;}
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$anio = $row_variables['anio'];

// seleccionados los datos para importar
mysql_select_db($database_vacantes, $vacantes);
$query_resultado1 = "SELECT com_vd_temp.IDmes, com_vd_temp.anio FROM com_vd_temp GROUP BY com_vd_temp.IDmes";
$resultado1 = mysql_query($query_resultado1, $vacantes) or die(mysql_error());
$row_resultado1 = mysql_fetch_assoc($resultado1);
$totalRows_resultado1 = mysql_num_rows($resultado1);
$IDmes = $row_resultado1['IDmes'];
$anio = $row_resultado1['anio'];

echo "MES: ".$IDmes;
echo "AÑO: ".$anio;

$query = "INSERT INTO com_vd (IDmatriz,  IDempleadoS,  IDempleado,  emp_paterno,  emp_materno,  emp_nombre, fecha_antiguedad, denominacion,  IDpuesto,  Clave,  ObjetivoVentaNeta,  VentaNetaPremio,  VentaNetaCajasPremio,  ClientesVentaPremio,  VentaNeta,  VentaNetaCajas,  VentaNetaPieza,  ClientesVenta,  NoPedidos,  Visitas,  DevImporte,  DevPorc,  Presupuesto,  Cubrimiento,  MargenBruto,  IDmes,  IDsemana,  anio,  bt_01,  bt_01_fecha,  bt_02,  bt_02_fecha,  bt_03,  bt_03_fecha,  bt_04,  bt_04_fecha,  bt_05,  bt_05_fecha,  bt_garantizado,  bt_adicional,  bt_observaciones,  bt_capturador,  bt_validador,  bt_autorizador,  bt_fecha_captura,  BonoProductividad,  BonoVentaNeta,  BonoClientesVenta,  BonoDevPorc,  Premios,  Premio_1,  Premio_2,  Comisiones,  Comisiones_pieza,  Comisiones_caja,  garantizado,  fecha_importacion,  calculado, IDgarantizado)  (SELECT DISTINCT IDmatriz,  IDempleadoS,  IDempleado,  emp_paterno,  emp_materno,  emp_nombre,  fecha_antiguedad, denominacion,  IDpuesto,  Clave,  ObjetivoVentaNeta,  VentaNetaPremio,  VentaNetaCajasPremio,  ClientesVentaPremio,  VentaNeta,  VentaNetaCajas,  VentaNetaPieza,  ClientesVenta,  NoPedidos,  Visitas,  DevImporte,  DevPorc,  Presupuesto,  Cubrimiento,  MargenBruto,  IDmes,  IDsemana,  anio,  bt_01,  bt_01_fecha,  bt_02,  bt_02_fecha,  bt_03,  bt_03_fecha,  bt_04,  bt_04_fecha,  bt_05,  bt_05_fecha,  bt_garantizado,  bt_adicional,  bt_observaciones,  bt_capturador,  bt_validador,  bt_autorizador,  bt_fecha_captura,  BonoProductividad,  BonoVentaNeta,  BonoClientesVenta,  BonoDevPorc,  Premios,  Premio_1,  Premio_2,  Comisiones,  Comisiones_pieza,  Comisiones_caja,  garantizado,  fecha_importacion,  calculado, IDgarantizado FROM com_vd_temp)"; 
$result = mysql_query($query) or die(mysql_error());  

$query2 = "TRUNCATE TABLE com_vd_temp"; 
$result2 = mysql_query($query2) or die(mysql_error());  

//redirecto
header("Location: vd_vendedores.php?info=6"); 	

?>