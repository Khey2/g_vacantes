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
$query_activos = "SELECT DISTINCT prod_activos.IDempleado, prod_activos.IDllave, prod_activos.IDempleadoJ FROM prod_activos WHERE prod_activos.IDempleadoJ = 0 AND IDaplica_SED = 0";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

set_time_limit(0);

 do { 
				$IDempleado = $row_activos['IDempleado'];
				$IDllave = $row_activos['IDllave'];
				//campos adicionales
				mysql_select_db($database_vacantes, $vacantes);
				$query_llave = "SELECT * FROM prod_llave WHERE IDllave = '$IDllave'";
				$llave = mysql_query($query_llave, $vacantes) or die(mysql_error());
				$row_llave = mysql_fetch_assoc($llave);
				$totalRows_llave = mysql_num_rows($llave);
				$IDllaveJ = $row_llave['IDllaveJ'];
				//campos adicionales
				mysql_select_db($database_vacantes, $vacantes);
				$query_boss = "SELECT * FROM prod_activos WHERE IDllave = '$IDllaveJ'";
				$boss = mysql_query($query_boss, $vacantes) or die(mysql_error());
				$row_boss = mysql_fetch_assoc($boss);
				$totalRows_boss = mysql_num_rows($boss);
				$IDempleadoJ = $row_boss['IDempleado'];

if ($totalRows_llave > 0 ) {

$query1 = "UPDATE prod_activos SET IDempleadoJ = '$IDempleadoJ' WHERE IDempleado = '$IDempleado'"; 
$result1 = mysql_query($query1) or die(mysql_error());  

if ($IDempleadoJ != '' or $IDempleadoJ != 0 ) {
$query2 = "INSERT INTO prod_activosj (IDempleado, IDempleadoJ) VALUES ('$IDempleado', '$IDempleadoJ')"; 
$result2 = mysql_query($query2) or die(mysql_error());  
} 
} 

} while ($row_activos = mysql_fetch_assoc($activos));

// quita sueldos que no se deben ver
$query2 = "UPDATE prod_activos SET sueldo_total = '0', sueldo_diario = '0', sueldo_mensual = '0', sobre_sueldo = 0, sueldo_total_productividad = 0 WHERE IDaplica_PROD = 0 AND IDaplica_INC = 0 "; 
$result2 = mysql_query($query2) or die(mysql_error());  


// quita sueldos que no se deben ver
//$query3 = "DELETE FROM prod_activos WHERE descripcion_nomina <> 'Nomina Semanal Sahuayo' AND  descripcion_nomina <> 'Nomina Quincenal ISA' AND descripcion_nomina <> 'Nomina Quincenal CORVI' AND descripcion_nomina <> 'Nomina Quincenal Fronteriza' AND descripcion_nomina <> 'Nomina Quincenal Promotora' AND descripcion_nomina <> 'Nomina Quincenal Luvi' AND  descripcion_nomina <> 'Nomina Quincenal Bajio' AND descripcion_nomina <> 'Nomina Quincenal Tepezala' AND descripcion_nomina <> 'Nomina Quincenal Puente'";  
//$result3 = mysql_query($query3) or die(mysql_error());  

header("Location: productividad_importar.php?info=1"); 	

?>