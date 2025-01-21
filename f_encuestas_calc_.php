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
$IDperiodo = $row_variables['IDperiodoN35'];
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
// la matriz y el usuario
$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDempleado'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
// matriz en nombre
$mi_matriz = $row_matriz['matriz'];


if (isset($_GET['IDrespuesta'])) {$la_respuesta = $_GET['IDrespuesta']; }
if (!isset($_GET['IDexamen'])) { header('Location: panel.php?info=5'); }

$el_examen =  $_GET['IDexamen'];

if (isset($_GET['IDpregunta'])) { $la_pregunta = $_GET['IDpregunta']; } else {
mysql_select_db($database_vacantes, $vacantes);
$query_mayor = "SELECT DISTINCT Max(nom35_respuestas.IDpregunta) AS mayor FROM nom35_respuestas left JOIN nom35_preguntas ON nom35_respuestas.IDpregunta WHERE nom35_respuestas.IDempleado = '$el_usuario' AND nom35_preguntas.IDexamen = '$el_examen' AND IDperiodo = $IDperiodo";
$mayor = mysql_query($query_mayor, $vacantes) or die(mysql_error());
$row_mayor = mysql_fetch_assoc($mayor);
$totalRows_mayor = mysql_num_rows($mayor);
$la_pregunta =  $row_mayor['mayor'];}

$la_pregunta_sig = $la_pregunta + 1;
$la_pregunta_ant = $la_pregunta - 1;

mysql_select_db($database_vacantes, $vacantes);
$query_examen = "SELECT * FROM nom35_examenes WHERE IDexamen = '$el_examen'";
$examen = mysql_query($query_examen, $vacantes) or die(mysql_error());
$row_examen = mysql_fetch_assoc($examen);
$totalRows_examen = mysql_num_rows($examen);
// total de preguntas del examen
$preguntas_totales = $row_examen['examen_total_preguntas'];

//respuestas
mysql_select_db($database_vacantes, $vacantes);
$query_respuesta = "SELECT * FROM nom35_respuestas WHERE IDexamen = '$el_examen' AND IDpregunta = '$la_pregunta' AND IDempleado = '$el_usuario' AND IDperiodo = $IDperiodo";
$respuesta = mysql_query($query_respuesta, $vacantes) or die(mysql_error());
$row_respuesta = mysql_fetch_assoc($respuesta);
$totalRows_respuesta = mysql_num_rows($respuesta);
$la_respuesta = $row_respuesta['respuesta'];

mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_previa = "SELECT * FROM nom35_respuestas WHERE IDexamen = '$el_examen' AND IDpregunta = '$la_pregunta_ant' AND IDempleado = '$el_usuario' AND IDperiodo = $IDperiodo";
$respuesta_previa = mysql_query($query_respuesta_previa, $vacantes) or die(mysql_error());
$row_respuesta_previa = mysql_fetch_assoc($respuesta_previa);
$totalRows_respuesta_previa = mysql_num_rows($respuesta_previa);
// respuesta previa
$la_respuesta_previa = $row_respuesta_previa['IDrespuesta'];

mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_siguiente = "SELECT * FROM nom35_respuestas WHERE IDexamen = '$el_examen' AND IDpregunta = '$la_pregunta_sig' AND IDempleado = '$el_usuario' AND IDperiodo = $IDperiodo";
$respuesta_siguiente = mysql_query($query_respuesta_siguiente, $vacantes) or die(mysql_error());
$row_respuesta_siguiente = mysql_fetch_assoc($respuesta_siguiente);
$totalRows_respuesta_siguiente = mysql_num_rows($respuesta_siguiente);
// respuesta siguiente
$la_respuesta_siguiente = $row_respuesta_siguiente['IDrespuesta'];

mysql_select_db($database_vacantes, $vacantes);
$query_respuesta_salto = "SELECT * FROM nom35_respuestas WHERE IDexamen = '$el_examen' AND IDpregunta = '45' AND IDempleado = '$el_usuario' AND IDperiodo = $IDperiodo";
$respuesta_salto = mysql_query($query_respuesta_salto, $vacantes) or die(mysql_error());
$row_respuesta_salto = mysql_fetch_assoc($respuesta_salto);
$totalRows_respuesta_salto = mysql_num_rows($respuesta_salto);
// respuesta salto
$la_pregunta_salto = 45;
$la_respuesta_salto = $row_respuesta_salto['IDrespuesta'];

mysql_select_db($database_vacantes, $vacantes);
$query_pregunta = "SELECT * FROM nom35_preguntas WHERE IDexamen = '$el_examen' AND IDpregunta = '$la_pregunta'";
mysql_query("SET NAMES 'utf8'");
$pregunta = mysql_query($query_pregunta, $vacantes) or die(mysql_error());
$row_pregunta = mysql_fetch_assoc($pregunta);
$totalRows_pregunta = mysql_num_rows($pregunta);
// texto de la pregunta
$texto_pregunta = $row_pregunta['pregunta_texto'];
$pregunta_llave = $row_pregunta['pregunta_llave'];
$pregunta_concepto = $row_pregunta['pregunta_concepto'];

$reenvio = 'f_encuestas.php';
if($pregunta_llave == 1 && $la_respuesta == 5) {$reenvio = 'f_encuestas.php?IDexamen=' . $el_examen . '&IDpregunta=' . $la_pregunta_salto . '&IDrespuesta=' . $la_respuesta_salto;	}
else if($pregunta_llave == 2 && $la_respuesta == 5) {$reenvio = 'f_encuestas_result.php?IDexamen=' . $el_examen . '&IDempleado=' . $el_usuario;	}
else if ( $totalRows_respuesta_siguiente == NULL ){$reenvio = 'f_encuestas.php?IDexamen=' . $el_examen . '&IDpregunta=' . $la_pregunta_sig;} 
else {$reenvio = 'f_encuestas.php?IDexamen=' . $el_examen . '&IDpregunta=' . $la_pregunta_sig . '&IDrespuesta=' . $la_respuesta_siguiente;}

// si la prengunta es la X, el examen el X y dijo que nom... actualizar las respuestas con un 9

// si la prengunta es la X, el examen el X y dijo que nom... actualizar las respuestas con un 9

echo "Examen: " . $el_examen . "<br>";
echo "Pregunta: " . $la_pregunta . "<br>";
echo "Usuario: " . $el_usuario . "<br>";
echo "Respuesta: " . $la_respuesta . "<br>";
echo "Respuesta Prev: " . $la_respuesta_previa . "<br>";
echo "Respuesta Sig: " . $la_respuesta_siguiente . "<br>";
echo "Preg Totl: " . $preguntas_totales . "<br>";

if ($la_pregunta == $preguntas_totales) {
header('Location: f_encuestas_result.php?IDexamen=" . $el_examen . "&IDempleado=" . $el_usuario"');
} else {
header('Location: '.$reenvio.'');
}
?>
