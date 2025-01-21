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

$currentPage = $_SERVER["PHP_SELF"];

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

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];

$query = "INSERT INTO con_empleados (IDempleado, a_paterno, a_materno, a_nombre, a_correo, `password`, a_rfc, a_curp, a_sexo, a_imss, IDnacionalidad, a_estado_civil, a_banco, a_cuenta_bancaria_clabe, a_cuenta_bancaria, c_fecha_nacimiento, fecha_alta, d_calle, d_numero_calle, d_colonia, d_delegacion_municipio, IDestado, d_estado, d_codigo_postal, fecha_cambio, b_sueldo_diario_int, b_sueldo_diario, b_sueldo_mensual, IDcuenta, IDsubcuenta, estatus, IDmatriz, IDpuesto, tipo_contrato, tipo_de_contrato, local_foraneo, importado)  (SELECT DISTINCT IDempleado, a_paterno, a_materno, a_nombre, a_correo, `password`, a_rfc, a_curp, a_sexo, a_imss, IDnacionalidad, a_estado_civil, a_banco, a_cuenta_bancaria_clabe, a_cuenta_bancaria, c_fecha_nacimiento, fecha_alta, d_calle, d_numero_calle, d_colonia, d_delegacion_municipio, IDestado, d_estado, d_codigo_postal, fecha_cambio, b_sueldo_diario_int, b_sueldo_diario, b_sueldo_mensual, IDcuenta, IDsubcuenta, estatus, IDmatriz, IDpuesto, tipo_contrato, tipo_de_contrato, local_foraneo, beneficiario_si FROM con_empleados_temp)"; 
$result = mysql_query($query) or die(mysql_error());  

$query3 = "INSERT INTO con_dependientes (nombre, IDempleado, IDtipo, beneficiario, telefono, direccion)  (SELECT DISTINCT beneficiario_nombre, IDempleado, beneficiario_parentesco, beneficiario_si, beneficiario_telefono, beneficiario_direccion FROM con_empleados_temp)"; 
$result3 = mysql_query($query3) or die(mysql_error());  

$query2 = "TRUNCATE TABLE con_empleados_temp"; 
$result2 = mysql_query($query2) or die(mysql_error());  

// redirect
header("Location: empleados_consulta.php?info=6");

					
?>