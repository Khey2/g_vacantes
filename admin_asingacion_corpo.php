<?php require_once('Connections/vacantes.php'); ?>
<?php

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
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");

mysql_select_db($database_vacantes, $vacantes);
$query_corpo = "SELECT * FROM vac_usuarios WHERE vac_usuarios.corpo = 1";
$corpo = mysql_query($query_corpo, $vacantes) or die(mysql_error());
$row_corpo = mysql_fetch_assoc($corpo);
$totalRows_corpo = mysql_num_rows($corpo);
$el_corpo = $row_corpo['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_asignar = "SELECT vac_vacante.IDvacante, vac_vacante.IDarea, vac_areas.area, vac_puestos.dias, vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.IDusuario4, vac_vacante.ajuste_dias, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.IDestatus, vac_vacante.fecha_usr4 FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto  WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDusuario4 IS NULL";
$asignar = mysql_query($query_asignar, $vacantes) or die(mysql_error());
$row_asignar = mysql_fetch_assoc($asignar);
$totalRows_asignar = mysql_num_rows($asignar);

echo "corpo: " . $el_corpo . "</br>";

require 'assets/dias.php';

do { 

 $la_vac = $row_asignar['IDvacante'];
 $startdate = date('Y/m/d', strtotime($row_asignar['fecha_requi']));
 $end_date =  date('Y/m/d');
 
 // dias
 $previo = getWorkingDays($startdate, $end_date, $holidays);
 
 $ajuste_dias = $row_asignar['ajuste_dias'];
 
 if ($ajuste_dias != 0) { $previo = $previo - $ajuste_dias; } 
 
 // mientras
 if ($previo > 20) {  
     $query2 = "UPDATE vac_vacante SET IDusuario4 = 38,  IDusuario5 = 29, fecha_usr4 = '$end_date' WHERE IDvacante = '$la_vac'"; 
     $result2 = mysql_query($query2) or die(mysql_error());  

echo "IDvacante: " . $la_vac . "</br>";
}

} while ($row_asignar = mysql_fetch_assoc($asignar));
?>