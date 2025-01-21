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
$fecha = date("Y-m-d"); // la fecha actual
$date2 = new DateTime($fecha);
$cajas_puestos = array(2, 18, 281, 313, 371);

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT prod_activosj.IDturno, prod_activos.IDempleado, prod_activos.fecha_antiguedad, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.descripcion_nomina, prod_activos.descripcion_nivel, prod_activos.denominacion, prod_activos.rfc, prod_activos.sueldo_total, prod_activos.password as NUEVO, prod_activosj.password as GUARDADO, prod_activos.IDempleadoJ as JEFE_ANTERIOR, prod_activosj.IDempleadoJ as JEFE_NUEVO, villosas.sueldo FROM prod_activos left JOIN prod_activosj ON prod_activosj.IDempleado = prod_activos.IDempleado LEFT JOIN villosas ON prod_activos.IDempleado = villosas.IDvillosa";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

set_time_limit(0);
 do { 

				$la_llave = $row_activos['descripcion_nomina'] . $row_activos['descripcion_nivel'] . $row_activos['denominacion'];
				$IDempleado = $row_activos['IDempleado'];

				//campos adicionales
				mysql_select_db($database_vacantes, $vacantes);
				$query_llave = "SELECT * FROM prod_llave WHERE llave = '$la_llave'";
				$llave = mysql_query($query_llave, $vacantes) or die(mysql_error());
				$row_llave = mysql_fetch_assoc($llave);
				$totalRows_llave = mysql_num_rows($llave);
				$IDmatriz = $row_llave['IDmatriz'];

				$date1 = new DateTime($row_activos['fecha_antiguedad']);
				$diff = $date1->diff($date2);
				$antiguedad = (($diff->format('%y') * 12) + $diff->format('%m'));

				//PASSWORD
				if (empty($row_activos['GUARDADO'])) {$pass = md5($row_activos['IDempleado']); } else {$pass = $row_activos['GUARDADO']; }							
				$querypass = "UPDATE prod_activos SET password = '$pass' WHERE IDempleado = '$IDempleado'"; 
				$resultpass = mysql_query($querypass) or die(mysql_error());  
				
				//TURNO
				if ($row_activos['IDturno'] == 0 OR $row_activos['IDturno'] == '') {$IDturno = 0; } else {$IDturno = $row_activos['IDturno']; }							
				$queryturno = "UPDATE prod_activos SET IDturno = '$IDturno' WHERE IDempleado = '$IDempleado'"; 
				$resultturno = mysql_query($queryturno) or die(mysql_error());  

				$IDsucursal = $row_llave['IDsucursal'];
				$IDpuesto = $row_llave['IDpuesto'];
				$IDarea = $row_llave['IDarea'];
				$IDVsueldo = $row_llave['IDVsueldo'];
				$IDaplica_PROD = $row_llave['IDaplica_PROD'];
				$IDaplica_SED = $row_llave['IDaplica_SED'];
				$IDaplica_INC = $row_llave['IDaplica_INC'];
				$IDllave = $row_llave['IDllave'];
				$IDllaveJ = $row_llave['IDllaveJ'];
				$boss_original = $row_activos['JEFE_ANTERIOR'];
				$boss_nuevo = $row_activos['JEFE_NUEVO']; 
				if ($boss_original == $boss_nuevo){$boss = $boss_original;} else {$boss = $boss_nuevo;}
				//if ($boss_original == 0 or $boss_original == ''){$boss = 0;}

				mysql_select_db($database_vacantes, $vacantes);
				$query_tabulador = "SELECT * FROM vac_tabulador WHERE IDpuesto = '$IDpuesto' AND IDmatriz = '$IDmatriz'";
				$tabulador = mysql_query($query_tabulador, $vacantes) or die(mysql_error());
				$row_tabulador = mysql_fetch_assoc($tabulador);
				$totalRows_tabulador = mysql_num_rows($tabulador);
								
				mysql_select_db($database_vacantes, $vacantes);
				$query_villose = "SELECT * FROM villosas WHERE IDvillosa = '$IDempleado'";
				$villose = mysql_query($query_villose, $vacantes) or die(mysql_error());
				$row_villose = mysql_fetch_assoc($villose);
				$totalRows_villose = mysql_num_rows($villose);

				if ($totalRows_villose > 0) { $sueldo_total_productividad = $row_tabulador['sueldo_mensual']; } 
									   else { $sueldo_total_productividad = $row_activos['sueldo_total'];} 

				if ($IDaplica_PROD == 1 AND in_array($IDpuesto, $cajas_puestos)) {
				$query_montos_cajas = "SELECT * FROM prod_valor_cajas WHERE IDmatriz = $IDmatriz AND '$antiguedad' >= meses_inicio AND '$antiguedad' <= meses_final";
				$montos_cajas = mysql_query($query_montos_cajas, $vacantes) or die(mysql_error());
				$row_montos_cajas = mysql_fetch_assoc($montos_cajas);
				$IDnivel_caja = $row_montos_cajas['IDnivel'];
				} else {
				$IDnivel_caja = 0;	
				}

				if ($IDaplica_PROD == 1) {
				$query_montos_antiguedad = "SELECT * FROM prod_valor_antiguedad WHERE IDmatriz = '$IDmatriz' AND IDpuesto = $IDpuesto AND '$antiguedad' >= meses_inicio AND '$antiguedad' <= meses_final";
				$montos_antiguedad = mysql_query($query_montos_antiguedad, $vacantes) or die(mysql_error());
				$row_montos_antiguedad = mysql_fetch_assoc($montos_antiguedad);
				$IDnivel_antiguedad = $row_montos_antiguedad['IDnivel'];
				} else {
				$IDnivel_antiguedad = 0;
				}
			
				
if ($totalRows_llave > 0) {
$query1 = "UPDATE prod_activos SET password = '$pass', IDmatriz = '$IDmatriz', IDsucursal = '$IDsucursal', IDVsueldo = '$IDVsueldo', IDpuesto = '$IDpuesto', IDarea = '$IDarea', IDaplica_PROD = '$IDaplica_PROD', IDaplica_INC = '$IDaplica_INC', IDllave = '$IDllave', IDaplica_SED = '$IDaplica_SED', IDempleadoJ = '$boss', activo = 1, sueldo_total_productividad = '$sueldo_total_productividad', nivel_acceso = '2', IDnivel_caja = '$IDnivel_caja', IDnivel_antiguedad = '$IDnivel_antiguedad' WHERE IDempleado = '$IDempleado'"; 
$result1 = mysql_query($query1) or die(mysql_error());  
 } 
 
 } while ($row_activos = mysql_fetch_assoc($activos));
?>