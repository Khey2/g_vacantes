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



//$fecha_corte = $_POST['fecha_consulta']; 
//$y1 = substr( $fecha_corte_, 6, 4 );
//$m1 = substr( $fecha_corte_, 3, 2 );
//$d1 = substr( $fecha_corte_, 0, 2 );
//$fecha_corte = $y1."-".$m1."-".$d1; echo $fecha_corte_; echo $fecha_corte;

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
$mis_areas = $row_usuario['IDmatrizes'];$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];


// cambiar periodo cada semestre?
$IDperiodo = $_POST['IDperiodo'];

if (isset($_POST['IDmatriz'])) {	foreach ($_POST['IDmatriz'] as $matris) 	{	$_SESSION['IDmatrizX'] = implode(", ", $_POST['IDmatriz']);}	} 
$IDmatrizX = $_SESSION['IDmatrizX'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizX)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_uniformes_periodos WHERE IDperiodo = $IDperiodo";
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos);

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM sed_uniformes_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);


$cantidad_camisa_ventas = $row_variables['cantidad_camisa_ventas'];
$cantidad_pantalon_ventas = $row_variables['cantidad_pantalon_ventas'];
$cantidad_playera_polo = $row_variables['cantidad_playera_polo'];
$cantidad_playera_roja = $row_variables['cantidad_playera_roja'];
$cantidad_pantalon_mezclilla = $row_variables['cantidad_pantalon_mezclilla'];
$cantidad_botas = $row_variables['cantidad_botas'];
$cantidad_faja = $row_variables['cantidad_faja'];
$extra = (100 + $row_variables['extra'] ) / 100;

// PHPExcel_IOFactory
include('assets/PHPExcel/IOFactory.php');

// Creamos un objeto PHPExcel
$objPHPExcel = new PHPExcel();

// Leemos un archivo Excel 2007
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("CI/Uniformes.xlsx");


do {
 $IDmatrizU = $row_lmatriz['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_consulta = "SELECT
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 26 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_26,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 28 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_28,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 30 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_30,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 32 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_32,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 34 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_34,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 36 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_36,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 38 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_38,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 40 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_40,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 42 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_42,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 44 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_44,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 46 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_46,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 48 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_48,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 50 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_H_50,

COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 26 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_26,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 28 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_28,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 30 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_30,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 32 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_32,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 34 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_34,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 36 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_36,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 38 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_38,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 40 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_40,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 42 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_42,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 44 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_44,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 46 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_46,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 48 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_48,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_camisa_ventas = 50 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_camisa_ventas END )), 0 ) AS Talla_camisa_ventas_M_50,


COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 26 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_26,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 28 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_28,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 30 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_30,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 32 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_32,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 34 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_34,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 36 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_36,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 38 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_38,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 40 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_40,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 42 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_42,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 44 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_44,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 46 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_46,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 48 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_48,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 50 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_H_50,

COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 26 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_26,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 28 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_28,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 30 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_30,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 32 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_32,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 34 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_34,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 36 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_36,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 38 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_38,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 40 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_40,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 42 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_42,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 44 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_44,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 46 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_46,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 48 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_48,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_ventas = 50 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_ventas END )), 0 ) AS Talla_pantalon_ventas_M_50,


COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'CH'      AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_H_CH,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'M'       AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_H_M,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'G'       AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_H_G,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'XG'      AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_H_XG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'XXG'     AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_H_XXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'XXXG'    AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_H_XXXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'XXXXG'   AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_H_XXXXG,

COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'CH'      AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_M_CH,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'M'       AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_M_M,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'G'       AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_M_G,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'XG'      AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_M_XG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'XXG'     AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_M_XXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'XXXG'    AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_M_XXXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_polo_distribucion = 'XXXXG'   AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_polo_distribucion END )), 0 ) AS Talla_playera_polo_distribucion_M_XXXXG,


COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'CH'      AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_H_CH,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'M'       AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_H_M,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'G'       AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_H_G,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'XG'      AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_H_XG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'XXG'     AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_H_XXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'XXXG'    AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_H_XXXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'XXXXG'   AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_H_XXXXG,

COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'CH'      AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_M_CH,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'M'       AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_M_M,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'G'       AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_M_G,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'XG'      AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_M_XG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'XXG'     AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_M_XXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'XXXG'    AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_M_XXXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_playera_roja_almacen = 'XXXXG'   AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_playera_roja_almacen END )), 0 ) AS Talla_playera_roja_M_XXXXG,


COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 26 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_26,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 28 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_28,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 30 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_30,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 32 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_32,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 34 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_34,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 36 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_36,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 38 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_38,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 40 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_40,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 42 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_42,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 44 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_44,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 46 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_46,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 48 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_48,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 50 AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_H_50,

COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 26 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_26,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 28 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_28,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 30 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_30,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 32 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_32,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 34 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_34,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 36 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_36,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 38 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_38,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 40 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_40,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 42 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_42,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 44 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_44,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 46 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_46,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 48 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_48,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_pantalon_operaciones = 50 AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_pantalon_operaciones END )), 0 ) AS Talla_pantalon_operaciones_M_50,


COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 21 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_21,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 22 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_22,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 23 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_23,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 24 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_24,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 25 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_25,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 26 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_26,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 27 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_27,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 28 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_28,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 29 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_29,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 30 AND sed_uniformes.Sexo = 'H') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_H_30,

COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 21 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_21,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 22 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_22,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 23 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_23,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 24 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_24,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 25 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_25,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 26 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_26,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 27 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_27,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 28 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_28,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 29 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_29,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_botas = 30 AND sed_uniformes.Sexo = 'M') THEN sed_uniformes.T_botas END )), 0 ) AS Talla_botas_M_30,


COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'CH'      AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_H_CH,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'M'       AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_H_M,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'G'       AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_H_G,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'XG'      AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_H_XG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'XXG'     AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_H_XXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'XXXG'    AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_H_XXXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'XXXXG'   AND sed_uniformes.Sexo = 'H' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_H_XXXXG,

COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'CH'      AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_M_CH,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'M'       AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_M_M,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'G'       AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_M_G,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'XG'      AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_M_XG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'XXG'     AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_M_XXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'XXXG'    AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_M_XXXG,
COALESCE ( COUNT(( CASE WHEN ( sed_uniformes.T_faja = 'XXXXG'   AND sed_uniformes.Sexo = 'M' ) THEN sed_uniformes.T_faja END )), 0 ) AS Talla_faja_M_XXXXG

FROM prod_activos	LEFT JOIN sed_uniformes ON prod_activos.IDempleado = sed_uniformes.IDempleado WHERE sed_uniformes.IDmatriz = $IDmatrizU AND IDperiodo = $IDperiodo"; 
mysql_query("SET NAMES 'utf8'");
$consulta = mysql_query($query_consulta, $vacantes) or die(mysql_error());

    $rowCount = $IDmatrizU+2; //new

    while($row_consulta = mysql_fetch_array($consulta)){ 

        $Talla_camisa_ventas_H_26 = round((( $row_consulta['Talla_camisa_ventas_H_26'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_28 = round((( $row_consulta['Talla_camisa_ventas_H_28'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_30 = round((( $row_consulta['Talla_camisa_ventas_H_30'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_32 = round((( $row_consulta['Talla_camisa_ventas_H_32'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_34 = round((( $row_consulta['Talla_camisa_ventas_H_34'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_36 = round((( $row_consulta['Talla_camisa_ventas_H_36'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_38 = round((( $row_consulta['Talla_camisa_ventas_H_38'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_40 = round((( $row_consulta['Talla_camisa_ventas_H_40'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_42 = round((( $row_consulta['Talla_camisa_ventas_H_42'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_44 = round((( $row_consulta['Talla_camisa_ventas_H_44'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_46 = round((( $row_consulta['Talla_camisa_ventas_H_46'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_48 = round((( $row_consulta['Talla_camisa_ventas_H_48'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_H_50 = round((( $row_consulta['Talla_camisa_ventas_H_50'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_26 = round((( $row_consulta['Talla_camisa_ventas_M_26'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_28 = round((( $row_consulta['Talla_camisa_ventas_M_28'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_30 = round((( $row_consulta['Talla_camisa_ventas_M_30'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_32 = round((( $row_consulta['Talla_camisa_ventas_M_32'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_34 = round((( $row_consulta['Talla_camisa_ventas_M_34'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_36 = round((( $row_consulta['Talla_camisa_ventas_M_36'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_38 = round((( $row_consulta['Talla_camisa_ventas_M_38'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_40 = round((( $row_consulta['Talla_camisa_ventas_M_40'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_42 = round((( $row_consulta['Talla_camisa_ventas_M_42'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_44 = round((( $row_consulta['Talla_camisa_ventas_M_44'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_46 = round((( $row_consulta['Talla_camisa_ventas_M_46'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_48 = round((( $row_consulta['Talla_camisa_ventas_M_48'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_camisa_ventas_M_50 = round((( $row_consulta['Talla_camisa_ventas_M_50'] * $cantidad_camisa_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_26 = round((( $row_consulta['Talla_pantalon_ventas_H_26'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_28 = round((( $row_consulta['Talla_pantalon_ventas_H_28'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_30 = round((( $row_consulta['Talla_pantalon_ventas_H_30'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_32 = round((( $row_consulta['Talla_pantalon_ventas_H_32'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_34 = round((( $row_consulta['Talla_pantalon_ventas_H_34'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_36 = round((( $row_consulta['Talla_pantalon_ventas_H_36'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_38 = round((( $row_consulta['Talla_pantalon_ventas_H_38'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_40 = round((( $row_consulta['Talla_pantalon_ventas_H_40'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_42 = round((( $row_consulta['Talla_pantalon_ventas_H_42'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_44 = round((( $row_consulta['Talla_pantalon_ventas_H_44'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_46 = round((( $row_consulta['Talla_pantalon_ventas_H_46'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_48 = round((( $row_consulta['Talla_pantalon_ventas_H_48'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_H_50 = round((( $row_consulta['Talla_pantalon_ventas_H_50'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_26 = round((( $row_consulta['Talla_pantalon_ventas_M_26'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_28 = round((( $row_consulta['Talla_pantalon_ventas_M_28'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_30 = round((( $row_consulta['Talla_pantalon_ventas_M_30'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_32 = round((( $row_consulta['Talla_pantalon_ventas_M_32'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_34 = round((( $row_consulta['Talla_pantalon_ventas_M_34'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_36 = round((( $row_consulta['Talla_pantalon_ventas_M_36'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_38 = round((( $row_consulta['Talla_pantalon_ventas_M_38'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_40 = round((( $row_consulta['Talla_pantalon_ventas_M_40'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_42 = round((( $row_consulta['Talla_pantalon_ventas_M_42'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_44 = round((( $row_consulta['Talla_pantalon_ventas_M_44'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_46 = round((( $row_consulta['Talla_pantalon_ventas_M_46'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_48 = round((( $row_consulta['Talla_pantalon_ventas_M_48'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_ventas_M_50 = round((( $row_consulta['Talla_pantalon_ventas_M_50'] * $cantidad_pantalon_ventas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_H_CH = round((( $row_consulta['Talla_playera_polo_distribucion_H_CH'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_H_M = round((( $row_consulta['Talla_playera_polo_distribucion_H_M'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_H_G = round((( $row_consulta['Talla_playera_polo_distribucion_H_G'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_H_XG = round((( $row_consulta['Talla_playera_polo_distribucion_H_XG'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_H_XXG = round((( $row_consulta['Talla_playera_polo_distribucion_H_XXG'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_H_XXXG = round((( $row_consulta['Talla_playera_polo_distribucion_H_XXXG'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_H_XXXXG = round((( $row_consulta['Talla_playera_polo_distribucion_H_XXXXG'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_M_CH = round((( $row_consulta['Talla_playera_polo_distribucion_M_CH'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_M_M = round((( $row_consulta['Talla_playera_polo_distribucion_M_M'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_M_G = round((( $row_consulta['Talla_playera_polo_distribucion_M_G'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_M_XG = round((( $row_consulta['Talla_playera_polo_distribucion_M_XG'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_M_XXG = round((( $row_consulta['Talla_playera_polo_distribucion_M_XXG'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_M_XXXG = round((( $row_consulta['Talla_playera_polo_distribucion_M_XXXG'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_polo_distribucion_M_XXXXG = round((( $row_consulta['Talla_playera_polo_distribucion_M_XXXXG'] * $cantidad_playera_polo ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_H_CH = round((( $row_consulta['Talla_playera_roja_H_CH'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_H_M = round((( $row_consulta['Talla_playera_roja_H_M'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_H_G = round((( $row_consulta['Talla_playera_roja_H_G'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_H_XG = round((( $row_consulta['Talla_playera_roja_H_XG'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_H_XXG = round((( $row_consulta['Talla_playera_roja_H_XXG'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_H_XXXG = round((( $row_consulta['Talla_playera_roja_H_XXXG'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_H_XXXXG = round((( $row_consulta['Talla_playera_roja_H_XXXXG'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_M_CH = round((( $row_consulta['Talla_playera_roja_M_CH'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_M_M = round((( $row_consulta['Talla_playera_roja_M_M'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_M_G = round((( $row_consulta['Talla_playera_roja_M_G'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_M_XG = round((( $row_consulta['Talla_playera_roja_M_XG'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_M_XXG = round((( $row_consulta['Talla_playera_roja_M_XXG'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_M_XXXG = round((( $row_consulta['Talla_playera_roja_M_XXXG'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_playera_roja_M_XXXXG = round((( $row_consulta['Talla_playera_roja_M_XXXXG'] * $cantidad_playera_roja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_26 = round((( $row_consulta['Talla_pantalon_operaciones_H_26'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_28 = round((( $row_consulta['Talla_pantalon_operaciones_H_28'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_30 = round((( $row_consulta['Talla_pantalon_operaciones_H_30'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_32 = round((( $row_consulta['Talla_pantalon_operaciones_H_32'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_34 = round((( $row_consulta['Talla_pantalon_operaciones_H_34'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_36 = round((( $row_consulta['Talla_pantalon_operaciones_H_36'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_38 = round((( $row_consulta['Talla_pantalon_operaciones_H_38'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_40 = round((( $row_consulta['Talla_pantalon_operaciones_H_40'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_42 = round((( $row_consulta['Talla_pantalon_operaciones_H_42'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_44 = round((( $row_consulta['Talla_pantalon_operaciones_H_44'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_46 = round((( $row_consulta['Talla_pantalon_operaciones_H_46'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_48 = round((( $row_consulta['Talla_pantalon_operaciones_H_48'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_H_50 = round((( $row_consulta['Talla_pantalon_operaciones_H_50'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_26 = round((( $row_consulta['Talla_pantalon_operaciones_M_26'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_28 = round((( $row_consulta['Talla_pantalon_operaciones_M_28'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_30 = round((( $row_consulta['Talla_pantalon_operaciones_M_30'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_32 = round((( $row_consulta['Talla_pantalon_operaciones_M_32'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_34 = round((( $row_consulta['Talla_pantalon_operaciones_M_34'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_36 = round((( $row_consulta['Talla_pantalon_operaciones_M_36'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_38 = round((( $row_consulta['Talla_pantalon_operaciones_M_38'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_40 = round((( $row_consulta['Talla_pantalon_operaciones_M_40'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_42 = round((( $row_consulta['Talla_pantalon_operaciones_M_42'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_44 = round((( $row_consulta['Talla_pantalon_operaciones_M_44'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_46 = round((( $row_consulta['Talla_pantalon_operaciones_M_46'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_48 = round((( $row_consulta['Talla_pantalon_operaciones_M_48'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_pantalon_operaciones_M_50 = round((( $row_consulta['Talla_pantalon_operaciones_M_50'] * $cantidad_pantalon_mezclilla ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_21 = round((( $row_consulta['Talla_botas_H_21'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_22 = round((( $row_consulta['Talla_botas_H_22'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_23 = round((( $row_consulta['Talla_botas_H_23'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_24 = round((( $row_consulta['Talla_botas_H_24'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_25 = round((( $row_consulta['Talla_botas_H_25'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_26 = round((( $row_consulta['Talla_botas_H_26'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_27 = round((( $row_consulta['Talla_botas_H_27'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_28 = round((( $row_consulta['Talla_botas_H_28'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_29 = round((( $row_consulta['Talla_botas_H_29'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_H_30 = round((( $row_consulta['Talla_botas_H_30'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_21 = round((( $row_consulta['Talla_botas_M_21'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_22 = round((( $row_consulta['Talla_botas_M_22'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_23 = round((( $row_consulta['Talla_botas_M_23'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_24 = round((( $row_consulta['Talla_botas_M_24'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_25 = round((( $row_consulta['Talla_botas_M_25'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_26 = round((( $row_consulta['Talla_botas_M_26'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_27 = round((( $row_consulta['Talla_botas_M_27'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_28 = round((( $row_consulta['Talla_botas_M_28'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_29 = round((( $row_consulta['Talla_botas_M_29'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_botas_M_30 = round((( $row_consulta['Talla_botas_M_30'] * $cantidad_botas ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_H_CH = round((( $row_consulta['Talla_faja_H_CH'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_H_M = round((( $row_consulta['Talla_faja_H_M'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_H_G = round((( $row_consulta['Talla_faja_H_G'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_H_XG = round((( $row_consulta['Talla_faja_H_XG'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_H_XXG = round((( $row_consulta['Talla_faja_H_XXG'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_H_XXXG = round((( $row_consulta['Talla_faja_H_XXXG'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_H_XXXXG = round((( $row_consulta['Talla_faja_H_XXXXG'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_M_CH = round((( $row_consulta['Talla_faja_M_CH'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_M_M = round((( $row_consulta['Talla_faja_M_M'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_M_G = round((( $row_consulta['Talla_faja_M_G'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_M_XG = round((( $row_consulta['Talla_faja_M_XG'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_M_XXG = round((( $row_consulta['Talla_faja_M_XXG'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_M_XXXG = round((( $row_consulta['Talla_faja_M_XXXG'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);
        $Talla_faja_M_XXXXG = round((( $row_consulta['Talla_faja_M_XXXXG'] * $cantidad_faja ) * $extra),0, PHP_ROUND_HALF_UP);


        // camisas ventas
        $objPHPExcel->setActiveSheetIndex(0);

		    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Talla_camisa_ventas_H_26);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $Talla_camisa_ventas_H_28);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $Talla_camisa_ventas_H_30);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $Talla_camisa_ventas_H_32);   
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $Talla_camisa_ventas_H_34);   
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $Talla_camisa_ventas_H_36);   
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $Talla_camisa_ventas_H_38);   
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $Talla_camisa_ventas_H_40);   
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $Talla_camisa_ventas_H_42);   
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $Talla_camisa_ventas_H_44);   
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $Talla_camisa_ventas_H_46);   
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $Talla_camisa_ventas_H_48);   
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $Talla_camisa_ventas_H_50);   

		    $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $Talla_camisa_ventas_M_26);
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $Talla_camisa_ventas_M_28);
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $Talla_camisa_ventas_M_30);
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $Talla_camisa_ventas_M_32);   
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $Talla_camisa_ventas_M_34);   
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $Talla_camisa_ventas_M_36);   
        $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $Talla_camisa_ventas_M_38);   
        $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $Talla_camisa_ventas_M_40);   
        $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $Talla_camisa_ventas_M_42);   
        $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, $Talla_camisa_ventas_M_44);   
        $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $Talla_camisa_ventas_M_46);   
        $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $Talla_camisa_ventas_M_48);   
        $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $Talla_camisa_ventas_M_50);   
      

        // pantalon ventas
        $objPHPExcel->setActiveSheetIndex(1);

        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Talla_pantalon_ventas_H_26);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $Talla_pantalon_ventas_H_28);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $Talla_pantalon_ventas_H_30);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $Talla_pantalon_ventas_H_32);   
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $Talla_pantalon_ventas_H_34);   
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $Talla_pantalon_ventas_H_36);   
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $Talla_pantalon_ventas_H_38);   
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $Talla_pantalon_ventas_H_40);   
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $Talla_pantalon_ventas_H_42);   
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $Talla_pantalon_ventas_H_44);   
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $Talla_pantalon_ventas_H_46);   
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $Talla_pantalon_ventas_H_48);   
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $Talla_pantalon_ventas_H_50);   
  
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $Talla_pantalon_ventas_M_26);
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $Talla_pantalon_ventas_M_28);
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $Talla_pantalon_ventas_M_30);
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $Talla_pantalon_ventas_M_32);   
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $Talla_pantalon_ventas_M_34);   
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $Talla_pantalon_ventas_M_36);   
        $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $Talla_pantalon_ventas_M_38);   
        $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $Talla_pantalon_ventas_M_40);   
        $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $Talla_pantalon_ventas_M_42);   
        $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, $Talla_pantalon_ventas_M_44);   
        $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $Talla_pantalon_ventas_M_46);   
        $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $Talla_pantalon_ventas_M_48);   
        $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $Talla_pantalon_ventas_M_50);   
       
         // playeras polo distribucion
        $objPHPExcel->setActiveSheetIndex(2);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Talla_playera_polo_distribucion_H_CH);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $Talla_playera_polo_distribucion_H_M);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $Talla_playera_polo_distribucion_H_G);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $Talla_playera_polo_distribucion_H_XG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $Talla_playera_polo_distribucion_H_XXG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $Talla_playera_polo_distribucion_H_XXXG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $Talla_playera_polo_distribucion_H_XXXXG);   
   
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $Talla_playera_polo_distribucion_M_CH);
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $Talla_playera_polo_distribucion_M_M);
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $Talla_playera_polo_distribucion_M_G);
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $Talla_playera_polo_distribucion_M_XG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $Talla_playera_polo_distribucion_M_XXG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $Talla_playera_polo_distribucion_M_XXXG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $Talla_playera_polo_distribucion_M_XXXXG);   
   
          // playeras rojas polo
        $objPHPExcel->setActiveSheetIndex(3);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Talla_playera_roja_H_CH);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $Talla_playera_roja_H_M);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $Talla_playera_roja_H_G);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $Talla_playera_roja_H_XG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $Talla_playera_roja_H_XXG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $Talla_playera_roja_H_XXXG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $Talla_playera_roja_H_XXXXG);   
   
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $Talla_playera_roja_M_CH);
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $Talla_playera_roja_M_M);
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $Talla_playera_roja_M_G);
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $Talla_playera_roja_M_XG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $Talla_playera_roja_M_XXG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $Talla_playera_roja_M_XXXG);   
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $Talla_playera_roja_M_XXXXG);   
          
         // Pantalos Mezclilla
        $objPHPExcel->setActiveSheetIndex(4);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Talla_pantalon_operaciones_H_26);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $Talla_pantalon_operaciones_H_28);
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $Talla_pantalon_operaciones_H_30);
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $Talla_pantalon_operaciones_H_32);   
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $Talla_pantalon_operaciones_H_34);   
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $Talla_pantalon_operaciones_H_36);   
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $Talla_pantalon_operaciones_H_38);   
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $Talla_pantalon_operaciones_H_40);   
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $Talla_pantalon_operaciones_H_42);   
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $Talla_pantalon_operaciones_H_44);   
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $Talla_pantalon_operaciones_H_46);   
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $Talla_pantalon_operaciones_H_48);   
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $Talla_pantalon_operaciones_H_50);   
 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $Talla_pantalon_operaciones_M_26);
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $Talla_pantalon_operaciones_M_28);
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $Talla_pantalon_operaciones_M_30);
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $Talla_pantalon_operaciones_M_32);   
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $Talla_pantalon_operaciones_M_34);   
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $Talla_pantalon_operaciones_M_36);   
        $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $Talla_pantalon_operaciones_M_38);   
        $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $Talla_pantalon_operaciones_M_40);   
        $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $Talla_pantalon_operaciones_M_42);   
        $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, $Talla_pantalon_operaciones_M_44);   
        $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, $Talla_pantalon_operaciones_M_46);   
        $objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, $Talla_pantalon_operaciones_M_48);   
        $objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, $Talla_pantalon_operaciones_M_50);   
  
  
        // Botas
        $objPHPExcel->setActiveSheetIndex(5);
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Talla_botas_H_21); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $Talla_botas_H_22); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $Talla_botas_H_23); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $Talla_botas_H_24);    
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $Talla_botas_H_25);    
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $Talla_botas_H_26);    
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $Talla_botas_H_27);    
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $Talla_botas_H_28);    
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $Talla_botas_H_29);    
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $Talla_botas_H_30);    
   
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $Talla_botas_M_21); 
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $Talla_botas_M_22); 
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $Talla_botas_M_23); 
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $Talla_botas_M_24);    
        $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $Talla_botas_M_25);    
        $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $Talla_botas_M_26);    
        $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $Talla_botas_M_27);    
        $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $Talla_botas_M_28);    
        $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $Talla_botas_M_29);    
        $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $Talla_botas_M_30);    

          // Fajas
          $objPHPExcel->setActiveSheetIndex(6);

        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $Talla_faja_H_CH); 
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $Talla_faja_H_M); 
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $Talla_faja_H_G); 
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $Talla_faja_H_XG);    
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $Talla_faja_H_XXG);    
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $Talla_faja_H_XXXG);    
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $Talla_faja_H_XXXXG);    
  
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $Talla_faja_M_CH); 
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $Talla_faja_M_M); 
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $Talla_faja_M_G); 
        $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $Talla_faja_M_XG);    
        $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $Talla_faja_M_XXG);    
        $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $Talla_faja_M_XXXG);    
        $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $Talla_faja_M_XXXXG);    

         // Increment the Excel row counter
        //$rowCount++; 
    }
	

  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));


    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);


    // Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="Reporte Uniformes.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
	
    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>