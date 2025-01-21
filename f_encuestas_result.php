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
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$IDperiodo = $row_variables['IDperiodoN35'];
$fecha = date("Y-m-d"); 

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
// la matriz y el usuario
if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDempleado'];
$IDempleado = $row_usuario['IDempleado'];
$emp_paterno = $row_usuario['emp_paterno'];
$emp_materno = $row_usuario['emp_materno'];
$emp_nombre = $row_usuario['emp_nombre'];
$rfc = $row_usuario['rfc'];
$fecha_alta = $row_usuario['fecha_alta'];
$denominacion = $row_usuario['denominacion'];
$IDsucursal = $row_usuario['IDsucursal'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDpuesto = $row_usuario['IDpuesto'];
$IDarea = $row_usuario['IDarea'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
// matriz en nombre
$mi_matriz = $row_matriz['matriz'];

$el_examen =  $_GET['IDexamen'];
$IDexamen =  $_GET['IDexamen'];
mysql_select_db($database_vacantes, $vacantes);
$query_examen = "SELECT * FROM nom35_examenes WHERE IDexamen = '$el_examen'";
$examen = mysql_query($query_examen, $vacantes) or die(mysql_error());
$row_examen = mysql_fetch_assoc($examen);
$totalRows_examen = mysql_num_rows($examen);

mysql_select_db($database_vacantes, $vacantes);
$query_total_res = "SELECT COUNT( IDrespuesta ) AS Respuestas FROM nom35_respuestas WHERE nom35_respuestas.IDempleado = $IDempleado AND nom35_respuestas.IDperiodo = $IDperiodo AND nom35_respuestas.IDexamen = $IDexamen";
$total_res = mysql_query($query_total_res, $vacantes) or die(mysql_error());
$row_total_res = mysql_fetch_assoc($total_res);
$totalRows_total_res = mysql_num_rows($total_res);
$Respuestas = $row_total_res['Respuestas'];


$query = "INSERT into nom35_resultados (IDexamen, IDempleado, examen_terminado, emp_paterno, emp_materno, emp_nombre, rfc, fecha_alta, denominacion, IDsucursal, IDarea, IDmatriz, IDpuesto, fecha_aplicacion, IDperiodo, total_respuestas)	values ('$IDexamen', '$IDempleado', 1, '$emp_paterno', '$emp_materno', '$emp_nombre', '$rfc', '$fecha_alta', '$denominacion', '$IDsucursal', '$IDarea', '$IDmatriz', '$IDpuesto', '$fecha', '$IDperiodo', '$Respuestas')";
$result = mysql_query($query, $vacantes) or die(mysql_error());

$reenvio = 'f_encuestas_aplicar.php?info=6';
header('Location: '.$reenvio.'');
?>