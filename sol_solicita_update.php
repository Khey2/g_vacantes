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

$IDvacante = $_GET['IDvacante'];
mysql_select_db($database_vacantes, $vacantes);
$query_vacante = "SELECT * FROM vac_vacante WHERE IDvacante = '$IDvacante'";
$vacante = mysql_query($query_vacante, $vacantes) or die(mysql_error());
$row_vacante = mysql_fetch_assoc($vacante);
$totalRows_vacante = mysql_num_rows($vacante);
$la_matriz = $row_vacante['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_jefecorpo = "SELECT * FROM vac_usuarios WHERE IDusuario_puesto = '127'";
$jefecorpo = mysql_query($query_jefecorpo, $vacantes) or die(mysql_error());
$row_jefecorpo = mysql_fetch_assoc($jefecorpo);
$totalRows_jefecorpo = mysql_num_rows($jefecorpo);
$jefecorpo = $row_jefecorpo['IDusuario'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_usuarios WHERE IDmatriz = '$la_matriz' AND (IDusuario_puesto = '128' OR IDusuario_puesto = '114')";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$jeferh = $row_matriz['IDusuario'];


echo $totalRows_matriz;


if ($totalRows_matriz == 0) {$usuario_asignado = $jefecorpo;} else {$usuario_asignado = $jeferh;}

//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

if (!isset($_SESSION['el_mesg'])){  $otro_mes = date("m"); } else { $otro_mes = $_SESSION['el_mesg'];} 
$_SESSION['el_mes'] = date("m");

		
    $query1 = "UPDATE vac_vacante SET IDusuario = '$usuario_asignado' WHERE IDvacante = '$IDvacante'"; 
    $result1 = mysql_query($query1) or die(mysql_error());  
	
	//redirecto
	header("Location: sol_total.php?info=1"); 	
				
?>