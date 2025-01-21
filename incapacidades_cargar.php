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
$fecha = date("Y-m-d");

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


//migramos los datos
$query = "INSERT INTO incapacidades_certificados (IDincapacidad, nss, IDempleado, IDtipo_certificado, IDtipo_incapacidad, folio_certificado, fecha_inicio, fecha_fin, dias, IDestatus, 
IDusuario, fecha_carga, IDmatriz)  (SELECT DISTINCT IDincapacidad, nss, IDempleado, IDtipo_certificado, IDtipo_incapacidad, folio_certificado, fecha_inicio, fecha_fin, dias, IDestatus, 
'$el_usuario', '$fecha', IDmatriz FROM incapacidades_certificados_temp WHERE duplicado = 0)"; 
$result = mysql_query($query) or die(mysql_error());  


//Ubicamos los repetidos
mysql_select_db($database_vacantes, $vacantes);
$query_duplicados = "SELECT * FROM incapacidades_certificados_temp WHERE duplicado = 1";
$duplicados = mysql_query($query_duplicados, $vacantes) or die(mysql_error());
$row_duplicados = mysql_fetch_assoc($duplicados);
$totalRows_duplicados = mysql_num_rows($duplicados);

if ($totalRows_duplicados > 0) {
do {
$IDcertificado = $row_duplicados['IDcertificado'];
$query4 = "UPDATE incapacidades_certificados SET IDestatus = 2 WHERE IDcertificado = $IDcertificado"; 
$result4 = mysql_query($query4) or die(mysql_error());  
} while ($row_duplicados = mysql_fetch_assoc($duplicados));
}

//borramos temporales
$query3 = "TRUNCATE TABLE incapacidades_certificados_temp"; 
$result3 = mysql_query($query3) or die(mysql_error());  


//redirecto
header("Location: incapacidades_carga.php?info=6"); 	

?>