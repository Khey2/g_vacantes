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

if(isset($_GET['borrar']) AND $_GET['borrar'] == 1) {
//borramos para cargar de nuevo
$query_borrar = "DELETE FROM com_vd_temp WHERE IDvd > 0";
$borrar = mysql_query($query_borrar, $vacantes) or die(mysql_error());
//redirecto
header("Location: vd_importar.php?info=6"); 	
}


//globales
$mi_fecha =  date('Y/m/d');
$el_mes = 10;
if (strlen($el_mes) == 1) {$el_mes_compara = "0".$el_mes;} else {$el_mes_compara = $el_mes;}
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 
$anio = $row_variables['anio'];
$el_mes_bono = date("m");

// seleccionados los datos para importar
mysql_select_db($database_vacantes, $vacantes);
$query_resultado1 = "SELECT * FROM com_vd WHERE IDmes = 10 AND anio = $anio";
$resultado1 = mysql_query($query_resultado1, $vacantes) or die(mysql_error());
$row_resultado1 = mysql_fetch_assoc($resultado1);
$totalRows_resultado1 = mysql_num_rows($resultado1);


do {
	
				$clave = $row_resultado1['Clave'];


//a3 Premios
	$Premio_1 = 0;
	$Premio_2 = 0;

	 if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 250000 AND $row_resultado1['VentaNeta'] <= 300000 ) { $Premio_1 = 1500; }
else if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 300000 AND $row_resultado1['VentaNeta'] <= 350000 ) { $Premio_1 = 2000; }
else if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 350000 AND $row_resultado1['VentaNeta'] <= 400000 ) { $Premio_1 = 2500; }
else if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 400000 AND $row_resultado1['VentaNeta'] <= 500000 ) { $Premio_1 = 3000; }
else if ($row_resultado1['VentaNeta'] > 0  AND $row_resultado1['VentaNeta'] >= 500000 ) { $Premio_1 = 4000; }


echo $row_resultado1['IDempleado']." | ";
echo $row_resultado1['VentaNeta']." | ";
echo $Premio_1."<br/>";



	$Premios = $Premio_1 + $Premio_2;


$query_bproductividad = "UPDATE com_vd_temp SET Premios = ".$Premios.", Premio_1 = ".$Premio_1.", Premio_2 = ".$Premio_2." WHERE com_vd_temp.Clave = '".$clave."'";
$bproductividad = mysql_query($query_bproductividad, $vacantes) or die(mysql_error());



 } while ($row_resultado1 = mysql_fetch_assoc($resultado1));
 
?>

