<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

$restrict->addLevel("3");
$restrict->addLevel("4");
$restrict->addLevel("5");
$restrict->Execute();
//End Restrict Access To Page


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
header('Content-Type: text/html; charset=iso-8859-1');

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
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
mysql_query("SET NAMES 'utf8'"); 
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);  

$query_sth = "SELECT
prod_activos.IDempleado AS numero_empleado,
prod_activos.emp_nombre AS 'nombre',
prod_activos.emp_paterno AS 'apellido_p',
prod_activos.emp_materno AS 'apellido_m',
vac_usuarios.usuario AS 'email',
prod_activos.rfc AS 'rfc',
SUBSTRING( prod_activos.rfc13, 11, 3 ) AS 'rfc_homoclave',
prod_activos.curp AS 'curp',
prod_activos.IDempleado AS 'password',
DATE_FORMAT( prod_activos.fecha_alta, '%Y-%m-%d' ) AS 'fecha_ingreso',
DATE_FORMAT( prod_activos.fecha_antiguedad, '%Y-%m-%d' ) AS 'fecha_antiguedad',
DATE_FORMAT( prod_activos.fecha_baja, '%Y-%m-%d' ) AS 'fecha_baja',
prod_activos.sueldo_mensual AS 'salario',
prod_activos.IDpuesto AS 'puesto_id',
prod_activos.IDsucursal AS 'sucursal_id',
prod_activos.IDempleadoJ AS 'jefe_numero_empleado',
prod_activos.estatus AS 'status_id',
IF( prod_activos.descripcion_nomina = 'Honorarios', 'Nomina Quincenal CORVI', prod_activos.descripcion_nomina ) AS 'identificador_nomina',
vac_sucursal.sucursal AS 'sucursal',
vac_puestos.denominacion AS 'puesto',
TRIM(	TRAILING '\r\n' 	FROM	TRIM( TRAILING '\n' FROM vac_areas.area )) AS 'area' 
FROM
prod_activos
LEFT JOIN vac_usuarios ON prod_activos.IDempleado = vac_usuarios.IDusuario
LEFT JOIN vac_sucursal ON prod_activos.IDsucursal = vac_sucursal.IDsucursal
LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto
LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea"; 
$sth = mysql_query($query_sth, $vacantes) or die(mysql_error());

for($row_sth = array(); 
$row_sth = mysql_fetch_assoc($sth); 
$rows[] = $row_sth);

//$generate_json = json_encode($rows);
$generate_json = json_encode($rows, JSON_UNESCAPED_UNICODE);


//print_r($rows);
//print $generate_json;

$filename = 'Activos_' . date( 'Y-m-d' );

header("Content-type: application/vnd.ms-excel");
header("Content-Type: application/force-download");
header("Content-Type: application/download");
header("Content-disposition: " . $filename . ".json");
header("Content-disposition: filename=" . $filename . ".json");

print $generate_json;
exit;


?>