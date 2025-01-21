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
$mis_areas = $row_usuario['IDmatrizes'];$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$el_mes = $_SESSION['el_mes'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 
$el_usuario = $row_usuario['IDusuario'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));
$tipo = $_GET['tipo'];
$IDcaptura =  $_GET['IDcaptura'];

mysql_select_db($database_vacantes, $vacantes);
$query_captura = "SELECT * FROM inc_captura WHERE IDcaptura = $IDcaptura";
$captura = mysql_query($query_captura, $vacantes) or die(mysql_error());
$row_captura = mysql_fetch_assoc($captura);
$totalRows_captura = mysql_num_rows($captura);
$el_empleado = $row_captura['IDempleado'];
$el_puesto = $row_captura['IDpuesto'];
$la_matriz = $row_captura['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_activo = "SELECT * FROM prod_activos WHERE IDempleado = $el_empleado";
$activo = mysql_query($query_activo, $vacantes) or die(mysql_error());
$row_activo = mysql_fetch_assoc($activo);
$totalRows_activo = mysql_num_rows($activo);
$sueldo_diario = $row_activo['sueldo_diario'];

mysql_select_db($database_vacantes, $vacantes);
$query_pxv_loc = "SELECT * FROM inc_pxv WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz AND tipo = 1";
$pxv_loc = mysql_query($query_pxv_loc, $vacantes) or die(mysql_error());
$row_pxv_loc = mysql_fetch_assoc($pxv_loc);
$totalRows_pxv_loc = mysql_num_rows($pxv_loc);

mysql_select_db($database_vacantes, $vacantes);
$query_pxv_for = "SELECT * FROM inc_pxv WHERE IDpuesto = $el_puesto AND IDmatriz = $la_matriz AND tipo = 2";
$pxv_for = mysql_query($query_pxv_for, $vacantes) or die(mysql_error());
$row_pxv_for = mysql_fetch_assoc($pxv_for);
$totalRows_pxv_for = mysql_num_rows($pxv_for);


//Ampliamos la session
if (!isset($_SESSION)) {
ini_set("session.cookie_lifetime", 10800);
ini_set("session.gc_maxlifetime", 10800); 
  session_start();
}

if ($tipo == 1) {
echo "horas extras";	
	
    $query1 = "UPDATE inc_captura SET inc1 = 0 WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");
	
} 

elseif ($tipo == 2) {
echo "Suplencia";	

    $query1 = "UPDATE inc_captura SET inc2 = 0 WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");

} 

elseif ($tipo == 3) {
echo "Incentivos";	

    $query1 = "UPDATE inc_captura SET inc3 = 0 WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");

} 

elseif ($tipo == 4) {
echo "PXV";	

    $query1 = "UPDATE inc_captura SET inc4 = 0 WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");

} 

elseif ($tipo == 6) {
echo "Festivos";	

    $query1 = "UPDATE inc_captura SET inc6 = 0, diasf = 0 WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");

} 

elseif ($tipo == 5) {
echo "Domingos </br>";	

    $query1 = "UPDATE inc_captura SET inc5 = 0 WHERE IDcaptura = '$IDcaptura'"; 		
    $result1 = mysql_query($query1) or die(mysql_error());  
		
	header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");

} else { 

header("Location: inc_cap_puesto.php?IDpuesto=$el_puesto");

}
?>