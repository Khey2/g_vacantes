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

$semana = $_GET['semana'];

mysql_select_db($database_vacantes, $vacantes);
$query_corregir = "SELECT * FROM prod_captura WHERE semana = '$semana'";
$corregir = mysql_query($query_corregir, $vacantes) or die(mysql_error());
$row_corregir = mysql_fetch_assoc($corregir);

set_time_limit(0);

do {
	
	$IDempleado = $row_corregir['IDempleado'];
	mysql_select_db($database_vacantes, $vacantes);
	$query_datos = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado";
	$datos = mysql_query($query_datos, $vacantes) or die(mysql_error());
	$row_datos = mysql_fetch_assoc($datos);
	
	$emp_paterno = $row_datos['emp_paterno'];
	$emp_materno = $row_datos['emp_materno'];
	$emp_nombre = $row_datos['emp_nombre'];
	$denominacion = $row_datos['denominacion'];
	$sueldo_total = $row_datos['sueldo_total'];
	
	if ($emp_paterno != NULL ) 	{$emp_paterno = $row_datos['emp_paterno'];	} else {$emp_paterno = "null";	}
	if ($emp_materno != NULL ) 	{$emp_materno = $row_datos['emp_materno'];	} else {$emp_materno = "null";	}
	if ($emp_nombre != NULL )	{$emp_nombre = $row_datos['emp_nombre'];	} else {$emp_nombre = "null";	}
	if ($denominacion != NULL ) {$denominacion = $row_datos['denominacion'];} else {$denominacion = "null";	}
	if ($sueldo_total != NULL ) {$sueldo_total = $row_datos['sueldo_total'];} else {$sueldo_total = "null";	}

    $query1 = "UPDATE prod_captura SET 
	emp_paterno = '$emp_paterno', emp_materno = '$emp_materno', emp_nombre = '$emp_nombre', denominacion = '$denominacion',  sueldo_total = '$sueldo_total' WHERE IDempleado = '$IDempleado'"; 
    $result1 = mysql_query($query1) or die(mysql_error());  


} while ($row_corregir = mysql_fetch_assoc($corregir));

echo "final";		


?>