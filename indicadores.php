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
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
if(isset($_POST['el_anio'])) { $anio = $_POST['el_anio'];} else {$anio = $row_variables['anio'];}
$desfase = $row_variables['dias_desfase'];


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



//CENTRO
//$la_matriz = '25, 20, 15, 19, 4, 30, 1, 10';
// SUR
//$la_matriz = '2, 13, 16, 17, 18, 22, 26, 28, 29';
// NORTE
//$la_matriz = '23, 24, 11, 21, 8, 9, 12, 6, 23';
// NORTE
//$la_matriz = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31';

$el_usuario = $row_usuario['IDusuario'];

//las variables de sesion para el filtrado
$llave = "";
if(isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {
$llave = " AND month(fecha_requi) = " . $_POST['el_mes'] . ""; 
$llave2 = " AND month(fecha_ocupacion) = " . $_POST['el_mes'] . ""; 
$el_mes = $_POST['el_mes']; 
} else {
$llave = ""; 
$llave2 = ""; 
$el_mes = "";
}

//las variables de sesion para el filtrado
if(isset($_POST['la_matriz']) && ($_POST['la_matriz']  > 0)) {
$_SESSION['la_matriz'] = $_POST['la_matriz'];
} else {
$_SESSION['la_matriz'] = $IDmatriz;
}
$la_matriz = $_SESSION['la_matriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizz = "SELECT * FROM vac_matriz";
$matrizz = mysql_query($query_matrizz, $vacantes) or die(mysql_error());
$row_matrizz = mysql_fetch_assoc($matrizz);
$totalRows_matrizz = mysql_num_rows($matrizz);

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

//Mes Actual Motivos Involuntarios
mysql_select_db($database_vacantes, $vacantes);
$query_mot1 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz, vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 1 AND vac_vacante.anio = '$anio' AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot1 = mysql_query($query_mot1, $vacantes) or die(mysql_error());
$m1 = mysql_num_rows($mot1);

mysql_select_db($database_vacantes, $vacantes);
$query_mot2 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 2 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz)))" . $llave;
$mot2 = mysql_query($query_mot2, $vacantes) or die(mysql_error());
$m2 = mysql_num_rows($mot2);

mysql_select_db($database_vacantes, $vacantes);
$query_mot3 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 3 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot3 = mysql_query($query_mot3, $vacantes) or die(mysql_error());
$m3 = mysql_num_rows($mot3);

mysql_select_db($database_vacantes, $vacantes);
$query_mot4 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 4 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot4 = mysql_query($query_mot4, $vacantes) or die(mysql_error());
$m4 = mysql_num_rows($mot4);

mysql_select_db($database_vacantes, $vacantes);
$query_mot5 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 5 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot5 = mysql_query($query_mot5, $vacantes) or die(mysql_error());
$m5 = mysql_num_rows($mot5);

mysql_select_db($database_vacantes, $vacantes);
$query_mot6 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 6 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot6 = mysql_query($query_mot6, $vacantes) or die(mysql_error());
$m6 = mysql_num_rows($mot6);

mysql_select_db($database_vacantes, $vacantes);
$query_mot7 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 7 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot7 = mysql_query($query_mot7, $vacantes) or die(mysql_error());
$m7 = mysql_num_rows($mot7);

mysql_select_db($database_vacantes, $vacantes);
$query_mot8 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 8 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot8 = mysql_query($query_mot8, $vacantes) or die(mysql_error());
$m8 = mysql_num_rows($mot8);

mysql_select_db($database_vacantes, $vacantes);
$query_mot9 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 9 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot9 = mysql_query($query_mot9, $vacantes) or die(mysql_error());
$m9 = mysql_num_rows($mot9);

mysql_select_db($database_vacantes, $vacantes);
$query_mot10 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 10 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot10 = mysql_query($query_mot10, $vacantes) or die(mysql_error());
$m10 = mysql_num_rows($mot10);

mysql_select_db($database_vacantes, $vacantes);
$query_mot11 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 11 AND vac_vacante.anio = '$anio' AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot11 = mysql_query($query_mot11, $vacantes) or die(mysql_error());
$m11 = mysql_num_rows($mot11);

mysql_select_db($database_vacantes, $vacantes);
$query_mot12 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 12 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot12 = mysql_query($query_mot12, $vacantes) or die(mysql_error());
$m12 = mysql_num_rows($mot12);

mysql_select_db($database_vacantes, $vacantes);
$query_mot13 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 13 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot13 = mysql_query($query_mot13, $vacantes) or die(mysql_error());
$m13 = mysql_num_rows($mot13);

mysql_select_db($database_vacantes, $vacantes);
$query_mot14 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 14 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot14 = mysql_query($query_mot14, $vacantes) or die(mysql_error());
$m14 = mysql_num_rows($mot14);

mysql_select_db($database_vacantes, $vacantes);
$query_mot15 = "SELECT vac_vacante.IDmotivo_baja AS M1, vac_vacante.IDmatriz, vac_vacante.IDusuario, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante LEFT JOIN vac_motivo_baja ON vac_vacante.IDmotivo_baja = vac_motivo_baja.IDmotivo LEFT JOIN vac_motivo_baja_tipo ON vac_motivo_baja.IDmotivo_baja_tipo = vac_motivo_baja_tipo.IDmotivo_baja_tipo WHERE vac_vacante.IDmotivo_baja = 15 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$mot15 = mysql_query($query_mot15, $vacantes) or die(mysql_error());
$m15 = mysql_num_rows($mot15);














mysql_select_db($database_vacantes, $vacantes);
$query_are1 = "SELECT vac_vacante.IDarea AS A1, vac_vacante.IDmatriz,  vac_vacante.IDusuario, vac_vacante.IDmatriz, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante WHERE vac_vacante.IDarea = 1 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$are1 = mysql_query($query_are1, $vacantes) or die(mysql_error());
$a1 = mysql_num_rows($are1);

mysql_select_db($database_vacantes, $vacantes);
$query_are2 = "SELECT vac_vacante.IDarea AS A1, vac_vacante.IDmatriz,  vac_vacante.IDusuario,  vac_vacante.IDmatriz, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante WHERE vac_vacante.IDarea = 3 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$are2 = mysql_query($query_are2, $vacantes) or die(mysql_error());
$a2 = mysql_num_rows($are2);

mysql_select_db($database_vacantes, $vacantes);
$query_are3 = "SELECT vac_vacante.IDarea AS A1, vac_vacante.IDmatriz,  vac_vacante.IDusuario,  vac_vacante.IDmatriz, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante WHERE vac_vacante.IDarea = 5 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$are3 = mysql_query($query_are3, $vacantes) or die(mysql_error());
$a3 = mysql_num_rows($are3);

mysql_select_db($database_vacantes, $vacantes);
$query_are4 = "SELECT vac_vacante.IDarea AS A1, vac_vacante.IDmatriz,  vac_vacante.IDusuario,  vac_vacante.IDmatriz, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante WHERE vac_vacante.IDarea = 7 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$are4 = mysql_query($query_are4, $vacantes) or die(mysql_error());
$a4 = mysql_num_rows($are4);

mysql_select_db($database_vacantes, $vacantes);
$query_are5 = "SELECT vac_vacante.IDarea AS A1, vac_vacante.IDmatriz,  vac_vacante.IDusuario,  vac_vacante.IDmatriz, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante WHERE vac_vacante.IDarea = 8 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$are5 = mysql_query($query_are5, $vacantes) or die(mysql_error());
$a5 = mysql_num_rows($are5);

mysql_select_db($database_vacantes, $vacantes);
$query_are6 = "SELECT vac_vacante.IDarea AS A1, vac_vacante.IDmatriz,  vac_vacante.IDusuario,  vac_vacante.IDmatriz, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante WHERE vac_vacante.IDarea = 9 AND vac_vacante.anio = '$anio' AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$are6 = mysql_query($query_are6, $vacantes) or die(mysql_error());
$a6 = mysql_num_rows($are6);

mysql_select_db($database_vacantes, $vacantes);
$query_are7 = "SELECT vac_vacante.IDarea AS A1, vac_vacante.IDmatriz, vac_vacante.IDusuario,  vac_vacante.IDmatriz, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante WHERE vac_vacante.IDarea = 10 AND vac_vacante.anio = '$anio'  AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$are7 = mysql_query($query_are7, $vacantes) or die(mysql_error());
$a7 = mysql_num_rows($are7);

mysql_select_db($database_vacantes, $vacantes);
$query_are8 = "SELECT vac_vacante.IDarea AS A1, vac_vacante.IDmatriz,  vac_vacante.IDusuario,  vac_vacante.IDmatriz, vac_vacante.IDusuario2, vac_vacante.IDusuario3, vac_vacante.fecha_requi FROM vac_vacante WHERE vac_vacante.IDarea IN (2,4,6) AND vac_vacante.anio = '$anio' AND ((IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz))) " . $llave;
$are8 = mysql_query($query_are8, $vacantes) or die(mysql_error());
$a8 = mysql_num_rows($are8);


//fechas
require_once('assets/dias.php');

//mes diferente para grafica
mysql_select_db($database_vacantes, $vacantes);
$query_otromes = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias,  vac_vacante.IDmatriz,  vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 " . $llave2;
$otromes = mysql_query($query_otromes, $vacantes) or die(mysql_error());
$row_otromes = mysql_fetch_assoc($otromes);
$totalRows_otromes = mysql_num_rows($otromes);


//variables en 0
$antes_tiempoy = 0;
$a_tiempoy = 0;
$fuera_tiempoy = 0;
$muy_fuera_tiempoy = 0;

// recorremos cada vacante
do { 

 $startdatey = date('Y/m/d', strtotime($row_otromes['fecha_requi']));
 $end_datey =  date('Y/m/d', strtotime($row_otromes['fecha_ocupacion']));

 $previoy = getWorkingDays($startdatey, $end_datey, $holidays);
                             
  // aplicamos ajuste de dias;
  $ajuste_diasy = $row_otromes['ajuste_dias'];
  if ($ajuste_diasy != 0) { $previoy = $previoy - $ajuste_diasy; } 
  
  // resultado grafica
     if (($previoy < 4) && ($totalRows_otromes != 0)) {  
	 $antes_tiempoy = $antes_tiempoy + 1; 
	} else if (($previoy <  $row_otromes['dias']) && ($totalRows_otromes != 0)) {   
	 $a_tiempoy = $a_tiempoy + 1;
	} else if (($previoy < $row_otromes['dias'] + 4) && ($totalRows_otromes != 0)) {  
	 $fuera_tiempoy = $fuera_tiempoy + 1;
	} else if (($previoy >= $row_otromes['dias']) && ($totalRows_otromes != 0)) {
	 $muy_fuera_tiempoy = $muy_fuera_tiempoy + 1; 
	}
	
	
} while ($row_otromes = mysql_fetch_assoc($otromes)); 

if ($totalRows_otromes != 0) {
$a = $antes_tiempoy * 1.5;
$b = $a_tiempoy * 1;
$c = $fuera_tiempoy * 0.5;
$d = 0;
$e = $a + $b + $c + $d;
$f = ($e / $totalRows_otromes) * 100;

if ( $f > 90) {$calificacion = "Sobresaliente";} elseif ( $f > 75) {$calificacion = "Satisfactorio"; } elseif ( $f > 65) {$calificacion = "Suficiente"; } else {$calificacion = "Deficiente"; }
if ( $f > 90) {$color = "info";} elseif ( $f > 75) {$color = "success"; } elseif ( $f > 65) {$color = "warning"; } else {$color  = "danger"; }
if ( $f > 90) {$icono = "icon-checkmark3";} elseif ( $f > 75) {$icono = "icon-checkmark3"; } elseif ( $f > 65) {$icono = "icon-cross2"; } else {$icono  = "icon-cross2"; }
} else { $calificacion = "Sin vacantes";}


//historico Enero
mysql_select_db($database_vacantes, $vacantes);
$query_k1 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 1";
$k1 = mysql_query($query_k1, $vacantes) or die(mysql_error());
$row_k1 = mysql_fetch_assoc($k1);
$totalRows_k1 = mysql_num_rows($k1);

$k11 = 0;
$k12 = 0;
$k13 = 0;
$k14 = 0;

do { 
 $startdatek1 = date('Y/m/d', strtotime($row_k1['fecha_requi']));
 $end_datek1 =  date('Y/m/d', strtotime($row_k1['fecha_ocupacion']));

 $previok1 = getWorkingDays($startdatek1, $end_datek1, $holidays);
                             
  $ajuste_diask1 = $row_k1['ajuste_dias'];
  if ($ajuste_diask1 != 0) { $previok1 = $previok1- $ajuste_diask1; } 
  
     if ($previok1 < 4 && $totalRows_k1 != 0) {  
	 $k11 = $k11 + 1; 
	} else if ($previok1 <  $row_k1['dias']     && $totalRows_k1 != 0) {   
	 $k12 = $k12 + 1;
	} else if ($previok1 <  $row_k1['dias'] + 4 && $totalRows_k1 != 0) {  
	 $k13 = $k13 + 1;
	} else if ($previok1 >= $row_k1['dias']     && $totalRows_k1 != 0) {
	 $k14 = $k14 + 1; 
	}
} while ($row_k1 = mysql_fetch_assoc($k1)); 

//if ($totalRows_k1 != 0) { $k11 = round(($k11 / $totalRows_k1) * 100); } else { $k11 = 0;}
//if ($totalRows_k1 != 0) { $k12 = round(($k12 / $totalRows_k1) * 100); } else { $k12 = 0;}
//if ($totalRows_k1 != 0) { $k13 = round(($k13 / $totalRows_k1) * 100); } else { $k13 = 0;}
//if ($totalRows_k1 != 0) { $k14 = round(($k14 / $totalRows_k1) * 100); } else { $k14 = 0;}

//historico Febrero
mysql_select_db($database_vacantes, $vacantes);
$query_k2 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2  AND month(fecha_ocupacion) = 2";
$k2 = mysql_query($query_k2, $vacantes) or die(mysql_error());
$row_k2 = mysql_fetch_assoc($k2);
$totalRows_k2 = mysql_num_rows($k2);

$k21 = 0;
$k22 = 0;
$k23 = 0;
$k24 = 0;

do { 
 $startdatek2 = date('Y/m/d', strtotime($row_k2['fecha_requi']));
 $end_datek2 =  date('Y/m/d', strtotime($row_k2['fecha_ocupacion']));

 $previok2 = getWorkingDays($startdatek2, $end_datek2, $holidays);
                             
  $ajuste_diask2 = $row_k2['ajuste_dias'];
  if ($ajuste_diask2 != 0) { $previok2 = $previok2- $ajuste_diask2; } 
  
     if ($previok2 < 4 && $totalRows_k2 != 0) {  
	 $k21 = $k21 + 1; 
	} else if ($previok2 <  $row_k2['dias']     && $totalRows_k2 != 0) {   
	 $k22 = $k22 + 1;
	} else if ($previok2 <  $row_k2['dias'] + 4 && $totalRows_k2 != 0) {  
	 $k23 = $k23 + 1;
	} else if ($previok2 >= $row_k2['dias']     && $totalRows_k2 != 0) {
	 $k24 = $k24 + 1; 
	}
} while ($row_k2 = mysql_fetch_assoc($k2)); 

//if ($totalRows_k2 != 0) { $k21 = round(($k21 / $totalRows_k2) * 100); } else { $k21 = 0;}
//if ($totalRows_k2 != 0) { $k22 = round(($k22 / $totalRows_k2) * 100); } else { $k22 = 0;}
//if ($totalRows_k2 != 0) { $k23 = round(($k23 / $totalRows_k2) * 100); } else { $k23 = 0;}
//if ($totalRows_k2 != 0) { $k24 = round(($k24 / $totalRows_k2) * 100); } else { $k24 = 0;}

//historico Marzo
mysql_select_db($database_vacantes, $vacantes);
$query_k3 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2  AND month(fecha_ocupacion) = 3";
$k3 = mysql_query($query_k3, $vacantes) or die(mysql_error());
$row_k3 = mysql_fetch_assoc($k3);
$totalRows_k3 = mysql_num_rows($k3);

$k31 = 0;
$k32 = 0;
$k33 = 0;
$k34 = 0;

do { 
 $startdatek3 = date('Y/m/d', strtotime($row_k3['fecha_requi']));
 $end_datek3 =  date('Y/m/d', strtotime($row_k3['fecha_ocupacion']));

 $previok3 = getWorkingDays($startdatek3, $end_datek3, $holidays);
                             
  $ajuste_diask3 = $row_k3['ajuste_dias'];
  if ($ajuste_diask3 != 0) { $previok3 = $previok3- $ajuste_diask3; } 
  
     if ($previok3 < 4 && $totalRows_k3 != 0) {  
	 $k31 = $k31 + 1; 
	} else if ($previok3 <  $row_k3['dias']     && $totalRows_k3 != 0) {   
	 $k32 = $k32 + 1;
	} else if ($previok3 <  $row_k3['dias'] + 4 && $totalRows_k3 != 0) {  
	 $k33 = $k33 + 1;
	} else if ($previok3 >= $row_k3['dias']     && $totalRows_k3 != 0) {
	 $k34 = $k34 + 1; 
	}
} while ($row_k3 = mysql_fetch_assoc($k3)); 

//if ($totalRows_k3 != 0) { $k31 = round(($k31 / $totalRows_k3) * 100); } else { $k31 = 0;}
//if ($totalRows_k3 != 0) { $k32 = round(($k32 / $totalRows_k3) * 100); } else { $k32 = 0;}
//if ($totalRows_k3 != 0) { $k33 = round(($k33 / $totalRows_k3) * 100); } else { $k33 = 0;}
//if ($totalRows_k3 != 0) { $k34 = round(($k34 / $totalRows_k3) * 100); } else { $k34 = 0;}

//historico Abril
mysql_select_db($database_vacantes, $vacantes);
$query_k4 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 4";
$k4 = mysql_query($query_k4, $vacantes) or die(mysql_error());
$row_k4 = mysql_fetch_assoc($k4);
$totalRows_k4 = mysql_num_rows($k4);

$k41 = 0;
$k42 = 0;
$k43 = 0;
$k44 = 0;

do { 
 $startdatek4 = date('Y/m/d', strtotime($row_k4['fecha_requi']));
 $end_datek4 =  date('Y/m/d', strtotime($row_k4['fecha_ocupacion']));

 $previok4 = getWorkingDays($startdatek4, $end_datek4, $holidays);
                             
  $ajuste_diask4 = $row_k4['ajuste_dias'];
  if ($ajuste_diask4 != 0) { $previok4 = $previok4- $ajuste_diask4; } 
  
     if ($previok4 < 4 && $totalRows_k4 != 0) {  
	 $k41 = $k41 + 1; 
	} else if ($previok4 <  $row_k4['dias']     && $totalRows_k4 != 0) {   
	 $k42 = $k42 + 1;
	} else if ($previok4 <  $row_k4['dias'] + 4 && $totalRows_k4 != 0) {  
	 $k43 = $k43 + 1;
	} else if ($previok4 >= $row_k4['dias']     && $totalRows_k4 != 0) {
	 $k44 = $k44 + 1; 
	}
} while ($row_k4 = mysql_fetch_assoc($k4)); 

//if ($totalRows_k4 != 0) { $k41 = round(($k41 / $totalRows_k4) * 100); } else { $k41 = 0;}
//if ($totalRows_k4 != 0) { $k42 = round(($k42 / $totalRows_k4) * 100); } else { $k42 = 0;}
//if ($totalRows_k4 != 0) { $k43 = round(($k43 / $totalRows_k4) * 100); } else { $k43 = 0;}
//if ($totalRows_k4 != 0) { $k44 = round(($k44 / $totalRows_k4) * 100); } else { $k44 = 0;}

//historico Mayo
mysql_select_db($database_vacantes, $vacantes);
$query_k5 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 5";
$k5 = mysql_query($query_k5, $vacantes) or die(mysql_error());
$row_k5 = mysql_fetch_assoc($k5);
$totalRows_k5 = mysql_num_rows($k5);

$k51 = 0;
$k52 = 0;
$k53 = 0;
$k54 = 0;

do { 
 $startdatek5 = date('Y/m/d', strtotime($row_k5['fecha_requi']));
 $end_datek5 =  date('Y/m/d', strtotime($row_k5['fecha_ocupacion']));

 $previok5 = getWorkingDays($startdatek5, $end_datek5, $holidays);
                             
  $ajuste_diask5 = $row_k5['ajuste_dias'];
  if ($ajuste_diask5 != 0) { $previok5 = $previok5- $ajuste_diask5; } 
  
     if ($previok5 < 4 && $totalRows_k5 != 0) {  
	 $k51 = $k51 + 1; 
	} else if ($previok5 <  $row_k5['dias']     && $totalRows_k5 != 0) {   
	 $k52 = $k52 + 1;
	} else if ($previok5 <  $row_k5['dias'] + 4 && $totalRows_k5 != 0) {  
	 $k53 = $k53 + 1;
	} else if ($previok5 >= $row_k5['dias']     && $totalRows_k5 != 0) {
	 $k54 = $k54 + 1; 
	}
} while ($row_k5 = mysql_fetch_assoc($k5)); 

//if ($totalRows_k5 != 0) { $k51 = round(($k51 / $totalRows_k5) * 100); } else { $k51 = 0;}
//if ($totalRows_k5 != 0) { $k52 = round(($k52 / $totalRows_k5) * 100); } else { $k52 = 0;}
//if ($totalRows_k5 != 0) { $k53 = round(($k53 / $totalRows_k5) * 100); } else { $k53 = 0;}
//if ($totalRows_k5 != 0) { $k54 = round(($k54 / $totalRows_k5) * 100); } else { $k54 = 0;}

//historico Junio
mysql_select_db($database_vacantes, $vacantes);
$query_k6 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 6";
$k6 = mysql_query($query_k6, $vacantes) or die(mysql_error());
$row_k6 = mysql_fetch_assoc($k6);
$totalRows_k6 = mysql_num_rows($k6);

$k61 = 0;
$k62 = 0;
$k63 = 0;
$k64 = 0;

do { 
 $startdatek6 = date('Y/m/d', strtotime($row_k6['fecha_requi']));
 $end_datek6 =  date('Y/m/d', strtotime($row_k6['fecha_ocupacion']));

 $previok6 = getWorkingDays($startdatek6, $end_datek6, $holidays);
                             
  $ajuste_diask6 = $row_k6['ajuste_dias'];
  if ($ajuste_diask6 != 0) { $previok6 = $previok6- $ajuste_diask6; } 
  
     if ($previok6 < 4 && $totalRows_k6 != 0) {  
	 $k61 = $k61 + 1; 
	} else if ($previok6 <  $row_k6['dias']     && $totalRows_k6 != 0) {   
	 $k62 = $k62 + 1;
	} else if ($previok6 <  $row_k6['dias'] + 4 && $totalRows_k6 != 0) {  
	 $k63 = $k63 + 1;
	} else if ($previok6 >= $row_k6['dias']     && $totalRows_k6 != 0) {
	 $k64 = $k64 + 1; 
	}
} while ($row_k6 = mysql_fetch_assoc($k6)); 

//if ($totalRows_k6 != 0) { $k61 = round(($k61 / $totalRows_k6) * 100); } else { $k61 = 0;}
//if ($totalRows_k6 != 0) { $k62 = round(($k62 / $totalRows_k6) * 100); } else { $k62 = 0;}
//if ($totalRows_k6 != 0) { $k63 = round(($k63 / $totalRows_k6) * 100); } else { $k63 = 0;}
//if ($totalRows_k6 != 0) { $k64 = round(($k64 / $totalRows_k6) * 100); } else { $k64 = 0;}

//historico Julio
mysql_select_db($database_vacantes, $vacantes);
$query_k7 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 7";
$k7 = mysql_query($query_k7, $vacantes) or die(mysql_error());
$row_k7 = mysql_fetch_assoc($k7);
$totalRows_k7 = mysql_num_rows($k7);

$k71 = 0;
$k72 = 0;
$k73 = 0;
$k74 = 0;

do { 
 $startdatek7 = date('Y/m/d', strtotime($row_k7['fecha_requi']));
 $end_datek7 =  date('Y/m/d', strtotime($row_k7['fecha_ocupacion']));

 $previok7 = getWorkingDays($startdatek7, $end_datek7, $holidays);
                             
  $ajuste_diask7 = $row_k7['ajuste_dias'];
  if ($ajuste_diask7 != 0) { $previok7 = $previok7- $ajuste_diask7; } 
  
     if ($previok7 < 4 && $totalRows_k7 != 0) {  
	 $k71 = $k71 + 1; 
	} else if ($previok7 <  $row_k7['dias']     && $totalRows_k7 != 0) {   
	 $k72 = $k72 + 1;
	} else if ($previok7 <  $row_k7['dias'] + 4 && $totalRows_k7 != 0) {  
	 $k73 = $k73 + 1;
	} else if ($previok7 >= $row_k7['dias']     && $totalRows_k7 != 0) {
	 $k74 = $k74 + 1; 
	}
} while ($row_k7 = mysql_fetch_assoc($k7)); 

//if ($totalRows_k7 != 0) { $k71 = round(($k71 / $totalRows_k7) * 100); } else { $k71 = 0;}
//if ($totalRows_k7 != 0) { $k72 = round(($k72 / $totalRows_k7) * 100); } else { $k72 = 0;}
//if ($totalRows_k7 != 0) { $k73 = round(($k73 / $totalRows_k7) * 100); } else { $k73 = 0;}
//if ($totalRows_k7 != 0) { $k74 = round(($k74 / $totalRows_k7) * 100); } else { $k74 = 0;}

//historico Agosto
mysql_select_db($database_vacantes, $vacantes);
$query_k8 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 8";
$k8 = mysql_query($query_k8, $vacantes) or die(mysql_error());
$row_k8 = mysql_fetch_assoc($k8);
$totalRows_k8 = mysql_num_rows($k8);

$k81 = 0;
$k82 = 0;
$k83 = 0;
$k84 = 0;

do { 
 $startdatek8 = date('Y/m/d', strtotime($row_k8['fecha_requi']));
 $end_datek8 =  date('Y/m/d', strtotime($row_k8['fecha_ocupacion']));

 $previok8 = getWorkingDays($startdatek8, $end_datek8, $holidays);
                             
  $ajuste_diask8 = $row_k8['ajuste_dias'];
  if ($ajuste_diask8 != 0) { $previok8 = $previok8- $ajuste_diask8; } 
  
     if ($previok8 < 4 && $totalRows_k8 != 0) {  
	 $k81 = $k81 + 1; 
	} else if ($previok8 <  $row_k8['dias']     && $totalRows_k8 != 0) {   
	 $k82 = $k82 + 1;
	} else if ($previok8 <  $row_k8['dias'] + 4 && $totalRows_k8 != 0) {  
	 $k83 = $k83 + 1;
	} else if ($previok8 >= $row_k8['dias']     && $totalRows_k8 != 0) {
	 $k84 = $k84 + 1; 
	}
} while ($row_k8 = mysql_fetch_assoc($k8)); 

//if ($totalRows_k8 != 0) { $k81 = round(($k81 / $totalRows_k8) * 100); } else { $k81 = 0;}
//if ($totalRows_k8 != 0) { $k82 = round(($k82 / $totalRows_k8) * 100); } else { $k82 = 0;}
//if ($totalRows_k8 != 0) { $k83 = round(($k83 / $totalRows_k8) * 100); } else { $k83 = 0;}
//if ($totalRows_k8 != 0) { $k84 = round(($k84 / $totalRows_k8) * 100); } else { $k84 = 0;}

//historico Sept
mysql_select_db($database_vacantes, $vacantes);
$query_k9 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 9";
$k9 = mysql_query($query_k9, $vacantes) or die(mysql_error());
$row_k9 = mysql_fetch_assoc($k9);
$totalRows_k9 = mysql_num_rows($k9);

$k91 = 0;
$k92 = 0;
$k93 = 0;
$k94 = 0;

do { 
 $startdatek9 = date('Y/m/d', strtotime($row_k9['fecha_requi']));
 $end_datek9 =  date('Y/m/d', strtotime($row_k9['fecha_ocupacion']));

 $previok9 = getWorkingDays($startdatek9, $end_datek9, $holidays);
                             
  $ajuste_diask9 = $row_k9['ajuste_dias'];
  if ($ajuste_diask9 != 0) { $previok9 = $previok9- $ajuste_diask9; } 
  
     if ($previok9 < 4 && $totalRows_k9 != 0) {  
	 $k91 = $k91 + 1; 
	} else if ($previok9 <  $row_k9['dias']     && $totalRows_k9 != 0) {   
	 $k92 = $k92 + 1;
	} else if ($previok9 <  $row_k9['dias'] + 4 && $totalRows_k9 != 0) {  
	 $k93 = $k93 + 1;
	} else if ($previok9 >= $row_k9['dias']     && $totalRows_k9 != 0) {
	 $k94 = $k94 + 1; 
	}
} while ($row_k9 = mysql_fetch_assoc($k9)); 

//if ($totalRows_k9 != 0) { $k91 = round(($k91 / $totalRows_k9) * 100); } else { $k91 = 0;}
//if ($totalRows_k9 != 0) { $k92 = round(($k92 / $totalRows_k9) * 100); } else { $k92 = 0;}
//if ($totalRows_k9 != 0) { $k93 = round(($k93 / $totalRows_k9) * 100); } else { $k93 = 0;}
//if ($totalRows_k9 != 0) { $k94 = round(($k94 / $totalRows_k9) * 100); } else { $k94 = 0;}

//historico Oct
mysql_select_db($database_vacantes, $vacantes);
$query_k10 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 10";
$k10 = mysql_query($query_k10, $vacantes) or die(mysql_error());
$row_k10 = mysql_fetch_assoc($k10);
$totalRows_k10 = mysql_num_rows($k10);

$k101 = 0;
$k102 = 0;
$k103 = 0;
$k104 = 0;

do { 
 $startdatek10 = date('Y/m/d', strtotime($row_k10['fecha_requi']));
 $end_datek10 =  date('Y/m/d', strtotime($row_k10['fecha_ocupacion']));

 $previok10 = getWorkingDays($startdatek10, $end_datek10, $holidays);
                             
  $ajuste_diask10 = $row_k10['ajuste_dias'];
  if ($ajuste_diask10 != 0) { $previok10 = $previok10- $ajuste_diask10; } 
  
     if ($previok10 < 4 && $totalRows_k10 != 0) {  
	 $k101 = $k101 + 1; 
	} else if ($previok10 <  $row_k10['dias']     && $totalRows_k10 != 0) {   
	 $k102 = $k102 + 1;
	} else if ($previok10 <  $row_k10['dias'] + 4 && $totalRows_k10 != 0) {  
	 $k103 = $k103 + 1;
	} else if ($previok10 >= $row_k10['dias']     && $totalRows_k10 != 0) {
	 $k104 = $k104 + 1; 
	}
} while ($row_k10 = mysql_fetch_assoc($k10)); 

//if ($totalRows_k10 != 0) { $k101 = round(($k101 / $totalRows_k10) * 100); } else { $k101 = 0;}
//if ($totalRows_k10 != 0) { $k102 = round(($k102 / $totalRows_k10) * 100); } else { $k102 = 0;}
//if ($totalRows_k10 != 0) { $k103 = round(($k103 / $totalRows_k10) * 100); } else { $k103 = 0;}
//if ($totalRows_k10 != 0) { $k104 = round(($k104 / $totalRows_k10) * 100); } else { $k104 = 0;}

//historico Nov
mysql_select_db($database_vacantes, $vacantes);
$query_xk11 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 11";
$xk11 = mysql_query($query_xk11, $vacantes) or die(mysql_error());
$row_xk11 = mysql_fetch_assoc($xk11);
$totalRows_xk11 = mysql_num_rows($xk11);

$xk111 = 0;
$xk112 = 0;
$xk113 = 0;
$xk114 = 0;

do { 
 $startdatexk11 = date('Y/m/d', strtotime($row_xk11['fecha_requi']));
 $end_datexk11 =  date('Y/m/d', strtotime($row_xk11['fecha_ocupacion']));

 $previoxk11 = getWorkingDays($startdatexk11, $end_datexk11, $holidays);
                             
  $ajuste_diasxk11 = $row_xk11['ajuste_dias'];
  if ($ajuste_diasxk11 != 0) { $previoxk11 = $previoxk11- $ajuste_diasxk11; } 
  
     if ($previoxk11 < 4 && $totalRows_xk11 != 0) {  
	 $xk111 = $xk111 + 1; 
	} else if ($previoxk11 <  $row_xk11['dias']     && $totalRows_xk11 != 0) {   
	 $xk112 = $xk112 + 1;
	} else if ($previoxk11 <  $row_xk11['dias'] + 4 && $totalRows_xk11 != 0) {  
	 $xk113 = $xk113 + 1;
	} else if ($previoxk11 >= $row_xk11['dias']     && $totalRows_xk11 != 0) {
	 $xk114 = $xk114 + 1; 
	}
} while ($row_xk11 = mysql_fetch_assoc($xk11)); 

//if ($totalRows_xk11 != 0) { $xk111 = round(($xk111 / $totalRows_xk11) * 100); } else { $xk111 = 0;}
//if ($totalRows_xk11 != 0) { $xk112 = round(($xk112 / $totalRows_xk11) * 100); } else { $xk112 = 0;}
//if ($totalRows_xk11 != 0) { $xk113 = round(($xk113 / $totalRows_xk11) * 100); } else { $xk113 = 0;}
//if ($totalRows_xk11 != 0) { $xk114 = round(($xk114 / $totalRows_xk11) * 100); } else { $xk114 = 0;}

//historico Dic
mysql_select_db($database_vacantes, $vacantes);
$query_xk12 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario') OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio' AND vac_vacante.IDestatus = 2 AND month(fecha_ocupacion) = 12";
$xk12 = mysql_query($query_xk12, $vacantes) or die(mysql_error());
$row_xk12 = mysql_fetch_assoc($xk12);
$totalRows_xk12 = mysql_num_rows($xk12);

$xk121 = 0;
$xk122 = 0;
$xk123 = 0;
$xk124 = 0;

do { 
 $startdatexk12 = date('Y/m/d', strtotime($row_xk12['fecha_requi']));
 $end_datexk12 =  date('Y/m/d', strtotime($row_xk12['fecha_ocupacion']));

 $previoxk12 = getWorkingDays($startdatexk12, $end_datexk12, $holidays);
                             
  $ajuste_diasxk12 = $row_xk12['ajuste_dias'];
  if ($ajuste_diasxk12 != 0) { $previoxk12 = $previoxk12- $ajuste_diasxk12; } 
  
     if ($previoxk12 < 4 && $totalRows_xk12 != 0) {  
	 $xk121 = $xk121 + 1; 
	} else if ($previoxk12 <  $row_xk12['dias']     && $totalRows_xk12 != 0) {   
	 $xk122 = $xk122 + 1;
	} else if ($previoxk12 <  $row_xk12['dias'] + 4 && $totalRows_xk12 != 0) {  
	 $xk123 = $xk123 + 1;
	} else if ($previoxk12 >= $row_xk12['dias']     && $totalRows_xk12 != 0) {
	 $xk124 = $xk124 + 1; 
	}
} while ($row_xk12 = mysql_fetch_assoc($xk12)); 

//if ($totalRows_xk12 != 0) { $xk121 = round(($xk121 / $totalRows_xk12) * 100); } else { $xk121 = 0;}
//if ($totalRows_xk12 != 0) { $xk122 = round(($xk122 / $totalRows_xk12) * 100); } else { $xk122 = 0;}
//if ($totalRows_xk12 != 0) { $xk123 = round(($xk123 / $totalRows_xk12) * 100); } else { $xk123 = 0;}
//if ($totalRows_xk12 != 0) { $xk124 = round(($xk124 / $totalRows_xk12) * 100); } else { $xk124 = 0;}


// el mes
  switch ($el_mes) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
    case '': $elmes = "Todos";  break;   
      }

mysql_select_db($database_vacantes, $vacantes);
$query_z1 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 1";
$z1 = mysql_query($query_z1, $vacantes) or die(mysql_error());
$row_z1 = mysql_fetch_assoc($z1);
$totalRows_z1 = mysql_num_rows($z1);

mysql_select_db($database_vacantes, $vacantes);
$query_z2 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 2";
$z2 = mysql_query($query_z2, $vacantes) or die(mysql_error());
$row_z2 = mysql_fetch_assoc($z2);
$totalRows_z2 = mysql_num_rows($z2);

mysql_select_db($database_vacantes, $vacantes);
$query_z3 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 3";
$z3 = mysql_query($query_z3, $vacantes) or die(mysql_error());
$row_z3 = mysql_fetch_assoc($z3);
$totalRows_z3 = mysql_num_rows($z3);

mysql_select_db($database_vacantes, $vacantes);
$query_z4 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 4";
$z4 = mysql_query($query_z4, $vacantes) or die(mysql_error());
$row_z4 = mysql_fetch_assoc($z4);
$totalRows_z4 = mysql_num_rows($z4);

mysql_select_db($database_vacantes, $vacantes);
$query_z5 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 5";
$z5 = mysql_query($query_z5, $vacantes) or die(mysql_error());
$row_z5 = mysql_fetch_assoc($z5);
$totalRows_z5 = mysql_num_rows($z5);

mysql_select_db($database_vacantes, $vacantes);
$query_z6 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 6";
$z6 = mysql_query($query_z6, $vacantes) or die(mysql_error());
$row_z6 = mysql_fetch_assoc($z6);
$totalRows_z6 = mysql_num_rows($z6);

mysql_select_db($database_vacantes, $vacantes);
$query_z7 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 7";
$z7 = mysql_query($query_z7, $vacantes) or die(mysql_error());
$row_z7 = mysql_fetch_assoc($z7);
$totalRows_z7 = mysql_num_rows($z7);

mysql_select_db($database_vacantes, $vacantes);
$query_z8 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 8";
$z8 = mysql_query($query_z8, $vacantes) or die(mysql_error());
$row_z8 = mysql_fetch_assoc($z8);
$totalRows_z8 = mysql_num_rows($z8);

mysql_select_db($database_vacantes, $vacantes);
$query_z9 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 9";
$z9 = mysql_query($query_z9, $vacantes) or die(mysql_error());
$row_z9 = mysql_fetch_assoc($z9);
$totalRows_z9 = mysql_num_rows($z9);

mysql_select_db($database_vacantes, $vacantes);
$query_z10 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 10";
$z10 = mysql_query($query_z10, $vacantes) or die(mysql_error());
$row_z10 = mysql_fetch_assoc($z10);
$totalRows_z10 = mysql_num_rows($z10);

mysql_select_db($database_vacantes, $vacantes);
$query_z11 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 11";
$z11 = mysql_query($query_z11, $vacantes) or die(mysql_error());
$row_z11 = mysql_fetch_assoc($z11);
$totalRows_z11 = mysql_num_rows($z11);

mysql_select_db($database_vacantes, $vacantes);
$query_z12 = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.dias, vac_vacante.IDestatus, vac_vacante.anio, vac_vacante.fecha_requi, vac_vacante.fecha_ocupacion, vac_vacante.ajuste_dias FROM vac_vacante left JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea  LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE ((vac_vacante.IDusuario = '$el_usuario' OR IDusuario2 = '$el_usuario' OR IDusuario3 = '$el_usuario')  OR (vac_vacante.IDmatriz IN ($la_matriz)))  AND vac_vacante.anio = '$anio'  AND vac_vacante.IDestatus = 3 AND month(fecha_ocupacion) = 12";
$z12 = mysql_query($query_z12, $vacantes) or die(mysql_error());
$row_z12 = mysql_fetch_assoc($z12);
$totalRows_z12 = mysql_num_rows($z12);

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
	<meta name="robots" content="noindex" />

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="https://www.gstatic.com/charts/loader.js"></script>
	<script src="global_assets/js/plugins/visualization/d3/d3.min.js"></script>
	<script src="global_assets/js/plugins/visualization/d3/d3_tooltip.js"></script>

	<script src="global_assets/js/demo_pages/general_widgets_stats.js"></script>
	<!-- /theme JS files -->
	<script src="assets/js/app.js"></script>
	<script src="assets/motivos.js"></script>
	<script src="assets/areas.js"></script>
	<script src="assets/historico.js"></script>
	<!-- /theme JS files -->

	
    <!-- /theme JS files -->
    <script type="text/javascript">
    var  m1 = <?php echo  $m1; ?>;
    var  m2 = <?php echo  $m2; ?>;
    var  m3 = <?php echo  $m3; ?>;
    var  m4 = <?php echo  $m4; ?>;
    var  m5 = <?php echo  $m5; ?>;
    var  m6 = <?php echo  $m6; ?>;
    var  m7 = <?php echo  $m7; ?>;
    var  m8 = <?php echo  $m8; ?>;
    var  m9 = <?php echo  $m9; ?>;
    var m10 = <?php echo $m10; ?>;
    var m11 = <?php echo $m11; ?>;
    var m12 = <?php echo $m12; ?>;
    var m13 = <?php echo $m13; ?>;
    var m14 = <?php echo $m14; ?>;
    var m15 = <?php echo $m15; ?>;	
	
	
    var  a1 = <?php echo  $a1; ?>;
    var  a2 = <?php echo  $a2; ?>;
    var  a3 = <?php echo  $a3; ?>;
    var  a4 = <?php echo  $a4; ?>;
    var  a5 = <?php echo  $a5; ?>;
    var  a6 = <?php echo  $a6; ?>;
    var  a7 = <?php echo  $a7; ?>;
    var  a8 = <?php echo  $a8; ?>;
	
	
    var k11 = <?php echo  $k11; ?>;
    var k12 = <?php echo  $k12; ?>;
    var k13 = <?php echo  $k13; ?>;
    var k14 = <?php echo  $k14; ?>;
    var k21 = <?php echo  $k21; ?>;
    var k22 = <?php echo  $k22; ?>;
    var k23 = <?php echo  $k23; ?>;
    var k24 = <?php echo  $k24; ?>;
    var k31 = <?php echo  $k31; ?>;
    var k32 = <?php echo  $k32; ?>;
    var k33 = <?php echo  $k33; ?>;
    var k34 = <?php echo  $k34; ?>;
    var k41 = <?php echo  $k41; ?>;
    var k42 = <?php echo  $k42; ?>;
    var k43 = <?php echo  $k43; ?>;
    var k44 = <?php echo  $k44; ?>;
    var k51 = <?php echo  $k51; ?>;
    var k52 = <?php echo  $k52; ?>;
    var k53 = <?php echo  $k53; ?>;
    var k54 = <?php echo  $k54; ?>;
    var k61 = <?php echo  $k61; ?>;
    var k62 = <?php echo  $k62; ?>;
    var k63 = <?php echo  $k63; ?>;
    var k64 = <?php echo  $k64; ?>;
    var k71 = <?php echo  $k71; ?>;
    var k72 = <?php echo  $k72; ?>;
    var k73 = <?php echo  $k73; ?>;
    var k74 = <?php echo  $k74; ?>;
    var k81 = <?php echo  $k81; ?>;
    var k82 = <?php echo  $k82; ?>;
    var k83 = <?php echo  $k83; ?>;
    var k84 = <?php echo  $k84; ?>;
    var k91 = <?php echo  $k91; ?>;
    var k92 = <?php echo  $k92; ?>;
    var k93 = <?php echo  $k93; ?>;
    var k94 = <?php echo  $k94; ?>;
    var k101 = <?php echo  $k101; ?>;
    var k102 = <?php echo  $k102; ?>;
    var k103 = <?php echo  $k103; ?>;
    var k104 = <?php echo  $k104; ?>;
    var k111 = <?php echo  $xk111; ?>;
    var k112 = <?php echo  $xk112; ?>;
    var k113 = <?php echo  $xk113; ?>;
    var k114 = <?php echo  $xk114; ?>;
    var k121 = <?php echo  $xk121; ?>;
    var k122 = <?php echo  $xk122; ?>;
    var k123 = <?php echo  $xk123; ?>;
    var k124 = <?php echo  $xk124; ?>;

    var z1 = <?php echo  $totalRows_z1; ?>;
    var z2 = <?php echo  $totalRows_z2; ?>;
    var z3 = <?php echo  $totalRows_z3; ?>;
    var z4 = <?php echo  $totalRows_z4; ?>;
    var z5 = <?php echo  $totalRows_z5; ?>;
    var z6 = <?php echo  $totalRows_z6; ?>;
    var z7 = <?php echo  $totalRows_z7; ?>;
    var z8 = <?php echo  $totalRows_z8; ?>;
    var z9 = <?php echo  $totalRows_z9; ?>;
    var z10 = <?php echo  $totalRows_z10; ?>;
    var z11 = <?php echo  $totalRows_z11; ?>;
    var z12 = <?php echo  $totalRows_z12; ?>;
	</script>

</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>		
	<?php require_once('assets/mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<?php require_once('assets/menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		
			<?php require_once('assets/pheader.php'); ?>

<!-- Content area -->
<div class="content">
                
				  <!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Reporte de Resultado</h5>	
						</div>

					<div class="panel-body">
                            <p>A continuación podrás consultar el reporte de indicadores de la sucursal, mismo que está basado en las vacantes reportadas en el Sistema.</p>
                            <p>Selecciona el mes de filtrado.</p>

                   <p>Los días programados por tipo de vacante son:<br>
                    <ul>
                      <li>Almacén = 8 días.</li>
                      <li>Distribución = 11 días.</li>
                      <li>Ventas = 16 días.</li>
                      <li>Administrativos = 21 días.</li>
                    </ul>
                    Los días de cálculo son laborales y no se consideran días festivos.</p>
                   <p> La calificación se calcula de acuerdo a la siguiente regla, de acuerdo a los días programados de cobertura de cada vacante:<br>
                    <ul>
                      <li>Vacantes cubiertas <span class="text text-primary">antes de tiempo</span>: x 1.5 de su valor.</li>
                      <li>Vacantes cubiertas <span class="text text-success">en tiempo</span>:		  x 1.0 de su valor.</li>
                      <li>Vacantes cubiertas <span class="text text-warning">fuera de tiempo</span>: x 0.5 de su valor.</li>
                      <li>Vacantes cubiertas <span class="text text-danger">muy fuera de tiempo</span>: x 0.</li>
                    </ul>
                    </p>
                   <p>La calificación considera los siguientes rangos:<br>
                    <ul>
                      <li>Mayor al 90% = Sobresaliente.</li>
                      <li>Entre 75 y 89%= Satisfactorio.</li>
                      <li>Entre 65 y 74% = Suficiente.</li>
                      <li>Menor a 65% = Deficiente.</li>
                    </ul>
					
                            <p><strong>Antes de tiempo: </strong><?php echo $antes_tiempoy ." x 1.5 = ". $a; ?></p>
                            <p><strong>A tiempo: </strong><?php echo $a_tiempoy ." x 1.0 = ". $b; ?></p>
                            <p><strong>Fuera de tiempo: </strong><?php echo $fuera_tiempoy ." x 0.5 = ". $c; ?></p>
                            <p><strong>Muy fuera de tiempo: </strong><?php echo $muy_fuera_tiempoy ." x 0 = ". $d; ?></p>
                            <p><strong>Calificación Total: </strong><?php echo round($f,0)."%"; ?></p>
					
					
                    </p>
                            
                            <p>&nbsp;</p>
                            
                             <form method="POST" action="indicadores.php">

					<table class="table">
							<tr>
                            <td>
                                             <select name="el_mes" class="form-control">
                                               <option value="" <?php if (!(strcmp("", $el_mes))) {echo "selected=\"selected\"";} ?>>Mes: Todos</option>
                                               <?php do {  ?>
                                               <option value="<?php echo $row_mes['IDmes']?>"<?php if (!(strcmp($row_mes['IDmes'], $el_mes))) {echo "selected=\"selected\"";} ?>><?php echo $row_mes['mes']?></option>
                                               <?php
											  } while ($row_mes = mysql_fetch_assoc($mes));
											  $rows = mysql_num_rows($mes);
											  if($rows > 0) {
												  mysql_data_seek($mes, 0);
												  $row_mes = mysql_fetch_assoc($mes);
											  } ?></select>
                            </td>
                            <td>
                                             <select name="la_matriz" class="form-control">
                                               <?php do {  ?>
                                               <option value="<?php echo $row_matrizz['IDmatriz']?>"<?php if (!(strcmp($row_matrizz['IDmatriz'], $la_matriz))) {echo "selected=\"selected\"";} ?>><?php echo $row_matrizz['matriz']?></option>
                                               <?php
											  } while ($row_matrizz = mysql_fetch_assoc($matrizz));
											  $rows = mysql_num_rows($matrizz);
											  if($rows > 0) {
												  mysql_data_seek($matrizz, 0);
												  $row_matrizz = mysql_fetch_assoc($matrizz);
											  } ?></select>
                            </td>
                            <td>
                                             <select name="el_anio" class="form-control">
                                               <option value="2020"<?php if (!(strcmp($anio, 2020))) {echo "selected=\"selected\"";} ?>>2020</option>
                                               <option value="2021"<?php if (!(strcmp($anio, 2021))) {echo "selected=\"selected\"";} ?>>2021</option>
                                               <option value="2022"<?php if (!(strcmp($anio, 2022))) {echo "selected=\"selected\"";} ?>>2022</option>
											   <option value="2023"<?php if (!(strcmp($anio, 2023))) {echo "selected=\"selected\"";} ?>>2023</option>
											   <option value="2024"<?php if (!(strcmp($anio, 2024))) {echo "selected=\"selected\"";} ?>>2024</option>
											   <option value="2025"<?php if (!(strcmp($anio, 2025))) {echo "selected=\"selected\"";} ?>>2025</option>
                                               </select>
                            </td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
                             </td>
					      </tr>
				    </table>
                    </form>
					
										<!-- Simple statistics -->


					<div class="row">
						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body bg-blue-400 has-bg-image">
								<div class="media no-margin">
									<div class="media-body">
										<h3 class="no-margin"><?php echo $totalRows_otromes?></h3>
										<span class="text-uppercase text-size-mini">Total Cubiertas</span>
									</div>

									<div class="media-right media-middle">
										<i class="icon-notification2 icon-3x opacity-75"></i>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body bg-danger-400 has-bg-image">
								<div class="media no-margin">
									<div class="media-body">
										<h3 class="no-margin"><?php echo $muy_fuera_tiempoy +  $fuera_tiempoy?></h3>
										<span class="text-uppercase text-size-mini">Total fuera de tiempo</span>
									</div>

									<div class="media-right media-middle">
										<i class=" icon-thumbs-down2 icon-3x opacity-75"></i>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body bg-success-400 has-bg-image">
								<div class="media no-margin">
									<div class="media-left media-middle">
										<i class="icon-thumbs-up2 icon-3x opacity-75"></i>
									</div>

									<div class="media-body text-right">
										<h3 class="no-margin"><?php echo $a_tiempoy +  $antes_tiempoy?></h3>
										<span class="text-uppercase text-size-mini">Total en tiempo</span>
									</div>
								</div>
							</div>
						</div>

						<div class="col-sm-6 col-md-3">
							<div class="panel panel-body bg-<?php echo $color ?>-400 has-bg-image">
								<div class="media no-margin">
									<div class="media-left media-middle">
										<i class="<?php echo $icono ?> icon-3x opacity-75"></i>
									</div>

									<div class="media-body text-right">
										<h4 class="no-margin"><?php if ($f <= 0){echo $calificacion . "(0)";} else { echo $calificacion . " (" . round($f,0) . ")";} ?></h4>
										<span class="text-uppercase text-size-mini">Calificación</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /simple statistics -->

					<!-- Trendlines -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Histórico de Cobertura</h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                	</ul>
		                	</div>
						</div>

						<div class="panel-body">
							<p class="content-group">A continuación se muesta el histórico de cobertura por mes. No incluye vacantes suspendidas o canceladas.</p>

							<div class="chart-container">
								<div class="chart" id="google-column-stacked"></div>
							</div>
						</div>
					</div>
					<!-- /trendlines -->

                       </div>
                  </div>
                  <!-- Statistics with progress bar -->
                  <!-- /statistics with progress bar -->
                  <!-- /panel heading options -->

					<!-- Footer -->
					<div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
				  </div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>