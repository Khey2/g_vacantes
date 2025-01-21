<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);


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
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$IDperiodovar = $row_variables['IDperiodo'];


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDmatrizes'];

$IDmatriz = $row_usuario['IDmatriz'];


$query_periodos = "SELECT * FROM sed_periodos_sed"; 
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);

$query_periodos2 = "SELECT * FROM sed_periodos_sed"; 
$periodos2 = mysql_query($query_periodos2, $vacantes) or die(mysql_error());
$row_periodos2 = mysql_fetch_assoc($periodos2);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT * FROM vac_puestos WHERE vac_puestos.IDaplica_SED = 1 ORDER BY vac_puestos.denominacion ASC";  
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatriz)";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// Poner Periodo Original como "En proceso"
if (isset($_POST['IDaccion']) AND $_POST['IDaccion'] == 1) {
	$estatus = 3;
	$mi_resultado = 3;
	$IDperiodo1 = $_POST['IDperiodo1'];

	foreach ($_POST['IDempleado'] as $puests) { 

$updateSQL = "UPDATE sed_individuales SET estatus=$estatus, mi_resultado=$mi_resultado WHERE IDempleado = $puests AND IDperiodo = $IDperiodo1";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

mysql_select_db($database_vacantes, $vacantes);
$query_activo = "SELECT * FROM prod_activos WHERE IDempleado=$puests";  
$activo = mysql_query($query_activo, $vacantes) or die(mysql_error());
$row_activo = mysql_fetch_assoc($activo);
$totalRows_activo = mysql_num_rows($activo);
$IDllave = $row_activo['IDllave'];

mysql_select_db($database_vacantes, $vacantes);
$query_resultadosA = "SELECT * FROM sed_individuales_resultados WHERE IDempleado=$puests AND IDperiodo = $IDperiodo1";  
$resultadosA = mysql_query($query_resultadosA, $vacantes) or die(mysql_error());
$row_resultadosA = mysql_fetch_assoc($resultadosA);
$totalRows_resultadosA = mysql_num_rows($resultadosA);

if ($totalRows_resultadosA == 1) { 

$updateSQLb = "UPDATE sed_individuales_resultados SET estatus=$estatus, resultado = 100 WHERE IDempleado=$puests AND IDperiodo = $IDperiodo1";
mysql_select_db($database_vacantes, $vacantes);
$Result1b = mysql_query($updateSQLb, $vacantes) or die(mysql_error());

} else { 

$updateSQLb = "INSERT INTO sed_individuales_resultados(IDempleado, IDperiodo, resultado, estatus, especial, IDllave) VALUES ($puests, $IDperiodo1, 100, 4, 1, $IDllave)";
mysql_select_db($database_vacantes, $vacantes);
$Result1b = mysql_query($updateSQLb, $vacantes) or die(mysql_error());

}

}

  $updateGoTo = "admin_periodos_replica.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
  $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
  
  
} else if (isset($_POST['IDaccion']) AND $_POST['IDaccion'] == 2) {
	$estatus = 1;
	$mi_resultado = 3;
	$IDperiodo1 = $_POST['IDperiodo1']; 
	$IDperiodo2 = $_POST['IDperiodo2']; 
	$fecha = date("Y-m-d");
	
	foreach ($_POST['IDempleado'] as $puests) { 

mysql_select_db($database_vacantes, $vacantes);
$query_activo = "SELECT * FROM prod_activos WHERE IDempleado=$puests";  
$activo = mysql_query($query_activo, $vacantes) or die(mysql_error());
$row_activo = mysql_fetch_assoc($activo);
$totalRows_activo = mysql_num_rows($activo);
$IDllave = $row_activo['IDllave'];

mysql_select_db($database_vacantes, $vacantes);
$query_resultadosA = "SELECT * FROM sed_individuales WHERE IDempleado=$puests AND IDperiodo = $IDperiodo1";  
$resultadosA = mysql_query($query_resultadosA, $vacantes) or die(mysql_error());
$row_resultadosA = mysql_fetch_assoc($resultadosA);
$totalRows_resultadosA = mysql_num_rows($resultadosA);
	
if ($totalRows_resultadosA > 0) { 

$deleteSQL = "DELETE FROM sed_individuales WHERE IDempleado=$puests AND IDperiodo = $IDperiodo2";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());

$updateSQLb = "INSERT INTO sed_individuales_resultados (IDempleado, IDperiodo, resultado, estatus, especial, IDllave) VALUES ($puests, $IDperiodo2, 0, $estatus, 1, $IDllave)";
mysql_select_db($database_vacantes, $vacantes);
$Result1b = mysql_query($updateSQLb, $vacantes) or die(mysql_error());

do {
	echo " ".$puests;
	$mi_mi = addslashes($row_resultadosA['mi_mi']);
	$mi_IDunidad = $row_resultadosA['mi_IDunidad'];
	$mi_ponderacion = $row_resultadosA['mi_ponderacion'];
	$mi_IDindicador = $row_resultadosA['mi_IDindicador'];
	$mi_3 = addslashes($row_resultadosA['mi_3']);
	$mi_2 = addslashes($row_resultadosA['mi_2']);
	$mi_1 = addslashes($row_resultadosA['mi_1']);
	
	
	
$updateSQLb = "INSERT INTO sed_individuales (IDempleado, mi_mi, mi_IDunidad, mi_ponderacion, mi_IDindicador, mi_3, mi_2, mi_1, mi_resultado, estatus, IDperiodo, fecha_captura, fecha_termino) VALUES($puests, '$mi_mi', '$mi_IDunidad', '$mi_ponderacion', '$mi_IDindicador', '$mi_3', '$mi_2', '$mi_1', 0, '$estatus', '$IDperiodo2', '$fecha', '$fecha')";
mysql_select_db($database_vacantes, $vacantes);
$Result1b = mysql_query($updateSQLb, $vacantes) or die(mysql_error());
	
} while ($row_resultadosA = mysql_fetch_assoc($resultadosA));

}

}
	
  $updateGoTo = "admin_periodos_replica.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
  $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
		
}

?>