<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$currentPage = $_SERVER["PHP_SELF"];
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

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
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);
$IDmatriz = $row_usuario['IDmatriz'];


$IDempleado = $_GET['IDempleado'];
$IDperiodo = $_GET['IDperiodo'];
$IDexamen = $_GET['IDexamen'];

$query_empleado_dob = "SELECT * FROM nom35_respuestas WHERE IDempleado = $IDempleado AND IDperiodo = $IDperiodo AND IDexamen = $IDexamen";
$empleado_dob = mysql_query($query_empleado_dob, $vacantes) or die(mysql_error());
$row_empleado_dob = mysql_fetch_assoc($empleado_dob);
$totalRows_empleado_dob = mysql_num_rows($empleado_dob);

mysql_select_db($database_vacantes, $vacantes);
$query_usuarios = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado";
$usuarios = mysql_query($query_usuarios, $vacantes) or die(mysql_error());
$row_usuarios = mysql_fetch_assoc($usuarios);
$totalRows_usuarios = mysql_num_rows($usuarios);

//update
do{

$Tipo_pregunta = $row_empleado_dob['pregunta_tipo'];
$Respuesta =  $row_empleado_dob['respuesta'];
$IDpregunta = $row_empleado_dob['IDpregunta'];

if($Tipo_pregunta == 2){

     if($Respuesta == 4){ $Respuesta_ = 0;}
else if($Respuesta == 3){ $Respuesta_ = 1;}
else if($Respuesta == 2){ $Respuesta_ = 2;}
else if($Respuesta == 1){ $Respuesta_ = 3;}
else if($Respuesta == 0){ $Respuesta_ = 4;}

$updateSQL = "UPDATE nom35_respuestas SET respuesta = $Respuesta_ WHERE IDempleado = $IDempleado AND IDperiodo = $IDperiodo AND IDexamen = $IDexamen AND IDpregunta = $IDpregunta"; 
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

}

} while ($row_empleado_dob = mysql_fetch_assoc($empleado_dob)); 

$emp_paterno = $row_usuarios['emp_paterno'];
$emp_materno = $row_usuarios['emp_materno'];
$emp_nombre = $row_usuarios['emp_nombre'];
$rfc = $row_usuarios['rfc'];
$fecha_alta = $row_usuarios['fecha_alta'];
$denominacion = $row_usuarios['denominacion'];
$IDsucursal = $row_usuarios['IDsucursal']; 
$IDarea = $row_usuarios['IDarea'];
$IDmatriz = $row_usuarios['IDmatriz'];
$IDpuesto = $row_usuarios['IDpuesto'];
$total_respuestas = $totalRows_empleado_dob;
$examen_terminado = 1;
$fecha_aplicacion = $fecha;

$updateSQL = "INSERT INTO nom35_resultados (IDexamen, IDempleado, emp_paterno, emp_materno, emp_nombre, rfc, fecha_alta, denominacion, IDsucursal, IDarea, IDmatriz, IDpuesto, IDperiodo, total_respuestas, examen_terminado, fecha_aplicacion, manual) VALUES ('$IDexamen', '$IDempleado', '$emp_paterno', '$emp_materno', '$emp_nombre', '$rfc', '$fecha_alta', '$denominacion', '$IDsucursal', '$IDarea', '$IDmatriz', '$IDpuesto', '$IDperiodo', '$total_respuestas', '$examen_terminado', '$fecha_aplicacion', 1)"; 
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());


header("Location: admin_n35e.php?info=9");

?>