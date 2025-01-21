<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
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
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 
$fecha_evaluacion = date("Y-m-d"); 

if (date("m") == 1) {$mes_actual = 1;} else {$mes_actual = date("m")-1;}

if (isset($_GET['IDmes'])) {$el_mes = $_GET['IDmes'];} else {$el_mes = $mes_actual;}
if (isset($_GET['anio'])) {$anio = $_GET['anio'];}
$IDempleado = $_GET["IDempleado"];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$IDllave = $row_usuario['IDllave'];
$IDpuesto = $row_usuario['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];

$query_becario = "SELECT capa_becarios.*,  capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo, capa_becarios_evaluacion.IDevaluacion, capa_becarios_evaluacion.IDcalificacion, capa_becarios_evaluacion.anio, capa_becarios_evaluacion.IDmes, vac_meses.mes FROM capa_becarios LEFT JOIN capa_becarios_evaluacion ON capa_becarios.IDempleado = capa_becarios_evaluacion.IDempleado LEFT JOIN vac_meses ON capa_becarios_evaluacion.IDmes = vac_meses.IDmes LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo WHERE capa_becarios.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$becario = mysql_query($query_becario, $vacantes) or die(mysql_error());
$row_becario = mysql_fetch_assoc($becario);
$totalRows_becario = mysql_num_rows($becario);
$IDtipo = $row_becario['IDtipo'];

$query_respuesta = "SELECT SUM(IDrespuesta) AS Total FROM capa_becarios_respuestas WHERE IDempleado = $IDempleado AND IDmes = $el_mes AND anio = $anio AND IDpregunta != 9";
$respuesta = mysql_query($query_respuesta, $vacantes) or die(mysql_error());
$row_respuesta = mysql_fetch_assoc($respuesta);
$totalRows_respuesta = mysql_num_rows($respuesta);
$IDcalificacion = $row_respuesta['Total'];

$query_resultado = "SELECT * FROM capa_becarios_evaluacion WHERE IDempleado = $IDempleado AND IDmes = $el_mes AND anio = $anio";
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);

$query_preguntas = "SELECT * FROM capa_becarios_preguntas WHERE capa_becarios_preguntas.IDtipo = $IDtipo AND IDtipo_opciones != 3";
$preguntas = mysql_query($query_preguntas, $vacantes) or die(mysql_error());
$row_preguntas = mysql_fetch_assoc($preguntas);
$totalRows_preguntas = mysql_num_rows($preguntas);

$IDcalificacion = floor(round($IDcalificacion / $totalRows_preguntas, 1));
echo $IDcalificacion;


if ($totalRows_resultado > 0) {

$query = "UPDATE capa_becarios_evaluacion SET IDcalificacion = '$IDcalificacion', fecha_evaluacion = '$fecha_evaluacion' WHERE IDempleado = $IDempleado AND IDmes = $el_mes AND anio = $anio";
$result = mysql_query($query) or die(mysql_error());  	
header("Location: f_capa_becarios_evaluar_new.php?IDempleado=$IDempleado&IDmes=$el_mes&anio=$anio&info=22"); 

} else {

$query = "INSERT INTO capa_becarios_evaluacion (IDempleado, IDmes, anio, IDcalificacion, fecha_evaluacion, IDempleadoJ) VALUES ($IDempleado, $el_mes, $anio, $IDcalificacion, '$fecha_evaluacion', $el_usuario)"; 
$result = mysql_query($query) or die(mysql_error());  	
header("Location: f_capa_becarios_evaluar_new.php?IDempleado=$IDempleado&IDmes=$el_mes&anio=$anio&info=11"); 

}
?>