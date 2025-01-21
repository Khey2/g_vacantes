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
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

//$el_mes = $_SESSION['el_mes'];
$el_mes = 11;

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//select
$IDvacante = $_GET['IDvacante'];
mysql_select_db($database_vacantes, $vacantes);
$query_vacante = "SELECT * FROM vac_vacante WHERE IDvacante = $IDvacante";
$vacante = mysql_query($query_vacante, $vacantes) or die(mysql_error());
$row_vacante = mysql_fetch_assoc($vacante);
$totalRows_vacante = mysql_num_rows($vacante);

$IDvacante = $row_vacante['IDvacante'];
$IDarea = $row_vacante['IDarea'];
$IDpuesto = $row_vacante['IDpuesto'];
$IDusuario = $row_vacante['IDusuario'];
$IDmotivo_v = $row_vacante['IDmotivo_v'];
$IDtipo_vacante = $row_vacante['IDtipo_vacante'];
$IDsucursal = $row_vacante['IDsucursal'];
$IDmatriz = $row_vacante['IDmatriz'];
$IDturno = $row_vacante['IDturno'];
$sueldo = $row_vacante['sueldo'];
$reemplazo_de = $row_vacante['reemplazo_de'];
$IDmotivo_baja = $row_vacante['IDmotivo_baja'];
$fecha_baja = $row_vacante['fecha_baja'];
$fecha_requi = $row_vacante['fecha_requi'];
$fecha_ocupacion = $row_vacante['fecha_ocupacion'];
$IDestatus = $row_vacante['IDestatus'];
$ajuste_dias = $row_vacante['ajuste_dias'];
$candidato_electo = $row_vacante['candidato_electo'];
$IDfuente = $row_vacante['IDfuente'];
$candidatos_reclutados = $row_vacante['candidatos_reclutados'];
$observaciones = $row_vacante['observaciones'];
$apoyo = $row_vacante['apoyo'];
$corpo = $row_vacante['corpo'];


//insert
   $sql = "INSERT INTO vac_vacante_h (IDvacante, IDarea, IDpuesto, IDusuario, IDmotivo_v, IDtipo_vacante, IDsucursal, IDmatriz, IDturno, sueldo, reemplazo_de, IDmotivo_baja, fecha_baja, fecha_requi, fecha_ocupacion, IDestatus, ajuste_dias, candidato_electo, IDfuente, candidatos_reclutados, observaciones, apoyo, corpo) VALUES ('$IDvacante', '$IDarea', '$IDpuesto', '$IDusuario', '$IDmotivo_v', '$IDtipo_vacante', '$IDsucursal', '$IDmatriz', '$IDturno', '$sueldo', '$reemplazo_de', '$IDmotivo_baja', '$fecha_baja', '$fecha_requi', '$fecha_ocupacion', '$IDestatus', '$ajuste_dias', '$candidato_electo', '$IDfuente', '$candidatos_reclutados', '$observaciones', '$apoyo', '$corpo')";
  $sql = mysql_query($sql) or die(mysql_error());  


//redirect
header('Location:vacantes_activas.php?info=2');

?>