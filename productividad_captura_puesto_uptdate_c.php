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
$el_usuario = $row_usuario['IDusuario'];

//globales
$mi_fecha =  date('Y/m/d');
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$el_puesto = $_GET['IDpuesto'];

mysql_select_db($database_vacantes, $vacantes);
$query_resultado = "SELECT  * FROM prod_captura WHERE IDpuesto = '$el_puesto' AND semana = '$semana' AND anio = '$anio'"; 
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);


do {

$IDcaptura = $row_resultado['IDcaptura'];
$su_matriz = $row_resultado['IDmatriz'];

// KPIs
$a1 = $row_resultado['a1'];
$a2 = $row_resultado['a2'];
$a3 = $row_resultado['a3'];
$a4 = $row_resultado['a4'];
$a5 = $row_resultado['a5'];

// sueldo
$dias_laborados = 7;
$sueldo_diario = $row_resultado['sueldo_total'] / 30;
$sueldo_semanal = $sueldo_diario * 7;
$bono_asistencia = 0;

$camion_tulti = array(3476, 60953, 60792, 60780, 34484, 23087, 3047, 62418, 15542, 61551, 30397);
$el_empleado = $row_resultado['IDempleado'];

if (in_array($el_empleado, $camion_tulti)) {
$query_camionT = "SELECT * FROM inc_camion_tulti WHERE IDempleado = $el_empleado";
$camionT = mysql_query($query_camionT, $vacantes) or die(mysql_error());
$row_camionT = mysql_fetch_assoc($camionT);
$bono_asistencia = $row_camionT['monto']; 
}

//tabulador 
mysql_select_db($database_vacantes, $vacantes);
$query_tabulador = "SELECT * FROM vac_tabulador WHERE IDpuesto = $el_puesto AND IDmatriz = $su_matriz AND IDnivel = 'A'";
$tabulador = mysql_query($query_tabulador, $vacantes) or die(mysql_error());
$row_tabulador = mysql_fetch_assoc($tabulador);
$totalRows_tabulador = mysql_num_rows($tabulador);
$monto_tabulador = ($row_tabulador['variable_mensual']/30)*7;

// productividad real
$suma = $a1 + $a2 + $a3 + $a4 + $a5; 
$monto_previo = ($monto_tabulador * $suma ) / 100; 
$porcentaje_carga = ($monto_previo * $parap); 
$monto =  round($monto_previo, 2);  
if ( $monto < 0) { $monto = 0; } 

$pago_total = $monto;  
$pago = ($pago_total / $sueldo_semanal) * 100;  

$query2 = "UPDATE prod_captura SET bono_asistencia = '$bono_asistencia', adicional = '$monto_adicional', pago_total = '$pago_total', pago = '$pago'  WHERE IDcaptura = '$IDcaptura'"; 
$result2 = mysql_query($query2) or die(mysql_error());  
	
} while ($row_resultado = mysql_fetch_assoc($resultado));	
	
	//redirecto
	header("Location: productividad_captura_c.php?info=1&el_puesto=".$el_puesto); 	
		
?>