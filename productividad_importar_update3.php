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
$query_activos = "SELECT * FROM prod_activos WHERE IDaplica_PROD = 1";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);
$anio = $row_variables['anio'];

set_time_limit(0);

 do { 
				$IDempleado = $row_activos['IDempleado'];
				$fecha = date("Y-m-d"); 
				$semana = date("W", strtotime($fecha));

// busca si ya esta en productividad
$query_reps = "SELECT * FROM prod_captura WHERE IDempleado = '$IDempleado' AND semana = '$semana'";
$reps = mysql_query($query_reps, $vacantes) or die(mysql_error());
$row_reps = mysql_fetch_assoc($reps);
$totalRows_reps = mysql_num_rows($reps);

				$emp_paterno = $row_activos['emp_paterno'];
				$emp_materno = $row_activos['emp_materno'];
				$emp_nombre = $row_activos['emp_nombre'];
				$denominacion = $row_activos['denominacion'];
				$sueldo_total = $row_activos['sueldo_total'];
				$IDmatriz = $row_activos['IDmatriz'];
				$IDsucursal = $row_activos['IDsucursal'];
				$IDpuesto = $row_activos['IDpuesto'];
				$IDarea = $row_activos['IDarea'];
				$IDaplica_SED = $row_activos['IDaplica_SED'];

// si no esta en esa semana lo agrega	
if ($totalRows_reps == 0) { 
$query2 = "INSERT INTO prod_captura (IDempleado, emp_paterno, emp_materno, emp_nombre,  denominacion, sueldo_total, semana, anio, IDpuesto, IDmatriz, IDsucursal, IDarea, lun, mar, mie, jue, vie, sab, dom) VALUES ('$IDempleado', '$emp_paterno', '$emp_materno', '$emp_nombre',  '$denominacion', '$sueldo_total', '$semana', '$anio', '$IDpuesto',  '$IDmatriz', '$IDsucursal', '$IDarea', 1, 1, 1, 1, 1, 1, 1)"; 
$result2 = mysql_query($query2) or die(mysql_error());  

}

} while ($row_activos = mysql_fetch_assoc($activos));

header("Location: productividad_importar.php?info=1"); 	

?>