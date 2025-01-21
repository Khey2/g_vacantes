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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$IDusuario = $row_usuario['IDusuario'];
$mis_areas = $row_usuario['IDareas'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];

  
	$fecha_filtro = 1;
	
	for ($i = 1; $i <= 31; $i++) {

	if ($fecha_filtro == 31) {$fecha_filtro = '2022-10-31';} else {$fecha_filtro = '2022-11-'.$fecha_filtro;}

	$query_activostotales = "SELECT * FROM prod_activosfaltas WHERE prod_activosfaltas.IDmatriz = '$IDmatriz' AND prod_activosfaltas.IDarea IN (1, 2, 3, 4) AND (DATE(prod_activosfaltas.fecha_baja) BETWEEN '2022-10-31' AND '2022-11-30' OR DATE(prod_activosfaltas.fecha_baja) = '0000-00-00' OR DATE(prod_activosfaltas.fecha_antiguedad) = '$fecha_filtro')";
	$activostotales = mysql_query($query_activostotales, $vacantes) or die(mysql_error());
	$row_activostotales = mysql_fetch_assoc($activostotales);
	$totalRows_activostotales = mysql_num_rows($activostotales);

	$query_activoscapturados = "SELECT prod_activosfaltas.IDempleado, prod_activosfaltas.fecha_baja, prod_activosfaltas.fecha_alta, prod_activosfaltas.IDmatriz, prod_activosfaltas.IDpuesto, prod_activosfaltas.IDarea, ind_asistencia.IDasistencia, ind_asistencia.IDestatus FROM prod_activosfaltas LEFT JOIN ind_asistencia ON prod_activosfaltas.IDempleado = ind_asistencia.IDempleado AND ind_asistencia.IDfecha = $fecha_filtro WHERE ind_asistencia.IDestatus != '' AND ind_asistencia.IDvalidador != '' AND prod_activosfaltas.IDmatriz = '$IDmatriz' AND prod_activosfaltas.IDarea IN (1, 2, 3, 4) AND (DATE(prod_activosfaltas.fecha_baja) BETWEEN '2022-10-31' AND '2022-11-30' OR DATE(prod_activosfaltas.fecha_baja) = '0000-00-00' OR DATE(prod_activosfaltas.fecha_antiguedad) = '$fecha_filtro')";
	$activoscapturados = mysql_query($query_activoscapturados, $vacantes) or die(mysql_error());
	$row_activoscapturados = mysql_fetch_assoc($activoscapturados);
	$totalRows_activoscapturados = mysql_num_rows($activoscapturados);


	if($totalRows_activostotales == $totalRows_activoscapturados) { $fecha.$fecha_filtro = 1; } else { $fecha.$fecha_filtro = 0; }

    echo $fecha.$fecha_filtro;
}
