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
$IDperiodo = $row_variables['IDperiodoN35'];
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
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$IDresultado = $_GET['IDresultado'];
$IDmatriz = $row_usuario['IDmatriz'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);




$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE nom35_resultados SET plan_accion=%s WHERE IDresultado=%s",
                       GetSQLValueString($_POST['plan_accion'], "text"),
                       GetSQLValueString($_POST['IDresultado'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "admin_n35e_resultado.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}




mysql_select_db($database_vacantes, $vacantes);
$query_resultado = "SELECT * FROM nom35_resultados WHERE IDresultado = $IDresultado";
$resultado = mysql_query($query_resultado, $vacantes) or die(mysql_error());
$row_resultado = mysql_fetch_assoc($resultado);
$totalRows_resultado = mysql_num_rows($resultado);
$el_evaluado = $row_resultado['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//el examen contestado
if($row_matriz['nom35_g2'] == 2){$IDexmen = 3;} else {$IDexmen = 2;}

mysql_select_db($database_vacantes, $vacantes);
$query_amatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_periodo = "SELECT * FROM nom35_periodos WHERE IDperiodo = $IDperiodo";
$periodo = mysql_query($query_periodo, $vacantes) or die(mysql_error());
$row_periodo = mysql_fetch_assoc($periodo);
$totalRows_periodo = mysql_num_rows($periodo);

//RESULTADOS TOTALES
mysql_select_db($database_vacantes, $vacantes);
$query_total_c = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo AND pregunta_tipo <> 3"; 
$total_c = mysql_query($query_total_c, $vacantes) or die(mysql_error());
$row_total_c = mysql_fetch_assoc($total_c);
$totalRows_total_c = mysql_num_rows($total_c);


//CATEGORIA 1 Ambiente de trabajo	
mysql_select_db($database_vacantes, $vacantes);
$query_categoria1 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 1  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$categoria1 = mysql_query($query_categoria1, $vacantes) or die(mysql_error());
$row_categoria1 = mysql_fetch_assoc($categoria1);
$totalRows_categoria1 = mysql_num_rows($categoria1);

//CATEGORIA 2 Factores propios de la actividad
mysql_select_db($database_vacantes, $vacantes);
$query_categoria2 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 2  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$categoria2 = mysql_query($query_categoria2, $vacantes) or die(mysql_error());
$row_categoria2 = mysql_fetch_assoc($categoria2);
$totalRows_categoria2 = mysql_num_rows($categoria2);

//CATEGORIA 3 Organización del tiempo de trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_categoria3 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 3  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$categoria3 = mysql_query($query_categoria3, $vacantes) or die(mysql_error());
$row_categoria3 = mysql_fetch_assoc($categoria3);
$totalRows_categoria3 = mysql_num_rows($categoria3);

//CATEGORIA 4 Liderazgo y relaciones en el trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_categoria4 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 4  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$categoria4 = mysql_query($query_categoria4, $vacantes) or die(mysql_error());
$row_categoria4 = mysql_fetch_assoc($categoria4);
$totalRows_categoria4 = mysql_num_rows($categoria4);

//CATEGORIA 5 Liderazgo y relaciones en el trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_categoria5 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 5  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$categoria5 = mysql_query($query_categoria5, $vacantes) or die(mysql_error());
$row_categoria5 = mysql_fetch_assoc($categoria5);
$totalRows_categoria5 = mysql_num_rows($categoria5);


//DOMINIO  1 Condiciones en el ambiente de trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio1 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 1  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$dominio1 = mysql_query($query_dominio1, $vacantes) or die(mysql_error());
$row_dominio1 = mysql_fetch_assoc($dominio1);
$totalRows_dominio1 = mysql_num_rows($dominio1);

//DOMINIO 2 Carga de trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio2 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 2  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$dominio2 = mysql_query($query_dominio2, $vacantes) or die(mysql_error());
$row_dominio2 = mysql_fetch_assoc($dominio2);
$totalRows_dominio2 = mysql_num_rows($dominio2);

//DOMINIO 3 Falta de control sobre el trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio3 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 3  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$dominio3 = mysql_query($query_dominio3, $vacantes) or die(mysql_error());
$row_dominio3 = mysql_fetch_assoc($dominio3);
$totalRows_dominio3 = mysql_num_rows($dominio3);

//DOMINIO 4 Jornada de trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio4 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 4  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$dominio4 = mysql_query($query_dominio4, $vacantes) or die(mysql_error());
$row_dominio4 = mysql_fetch_assoc($dominio4);
$totalRows_dominio4 = mysql_num_rows($dominio4);

//DOMINIO 5 Interferencia en la relación trabajo-familia
mysql_select_db($database_vacantes, $vacantes);
$query_dominio5 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 5  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$dominio5 = mysql_query($query_dominio5, $vacantes) or die(mysql_error());
$row_dominio5 = mysql_fetch_assoc($dominio5);
$totalRows_dominio5 = mysql_num_rows($dominio5);

//DOMINIO 6 Liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio6 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 6  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$dominio6 = mysql_query($query_dominio6, $vacantes) or die(mysql_error());
$row_dominio6 = mysql_fetch_assoc($dominio6);
$totalRows_dominio6 = mysql_num_rows($dominio6);

//DOMINIO 7 Relaciones en el trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio7 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 7  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$dominio7 = mysql_query($query_dominio7, $vacantes) or die(mysql_error());
$row_dominio7 = mysql_fetch_assoc($dominio7);
$totalRows_dominio7 = mysql_num_rows($dominio7);

//DOMINIO 8 Violencia
mysql_select_db($database_vacantes, $vacantes);
$query_dominio8 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension FROM nom35_respuestas WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 8  AND IDempleado = $el_evaluado AND IDperiodo = $IDperiodo  AND pregunta_tipo <> 3"; 
$dominio8 = mysql_query($query_dominio8, $vacantes) or die(mysql_error());
$row_dominio8 = mysql_fetch_assoc($dominio8);
$totalRows_dominio8 = mysql_num_rows($dominio8);


if ($IDexamen = 3){

$ct_MA = 90;
$ct_A  = 70;
$ct_M  = 45;
$ct_B  = 20;
$ct_N  = 0;

$c1_MA = 14;
$c1_A  = 11;
$c1_M  = 9;
$c1_B  = 5;
$c1_N  = 0; 

$c2_MA = 60;
$c2_A  = 45;
$c2_M  = 30;
$c2_B  = 15;
$c2_N  = 0;

$c3_MA = 13;
$c3_A  = 10;
$c3_M  = 7;
$c3_B  = 5;
$c3_N  = 0;

$c4_MA = 58;
$c4_A  = 42;
$c4_M  = 29;
$c4_B  = 14;
$c4_N  = 0;

$c5_MA = 23;
$c5_A  = 18;
$c5_M  = 14;
$c5_B  = 10;
$c5_N  = 0;



$d1_MA = 14;
$d1_A  = 11;
$d1_M  = 9;
$d1_B  = 5;
$d1_N  = 0;

$d2_MA = 37;
$d2_A  = 27;
$d2_M  = 21;
$d2_B  = 15;
$d2_N  = 0;

$d3_MA = 25;
$d3_A  = 21;
$d3_M  = 16;
$d3_B  = 11;
$d3_N  = 0;

$d4_MA = 6;
$d4_A  = 4;
$d4_M  = 2;
$d4_B  = 1;
$d4_N  = 0;

$d5_MA = 10;
$d5_A  = 8;
$d5_M  = 6;
$d5_B  = 4;
$d5_N  = 0;

$d6_MA = 20;
$d6_A  = 16;
$d6_M  = 12;
$d6_B  = 9;
$d6_N  = 0;

$d7_MA = 21;
$d7_A  = 17;
$d7_M  = 13;
$d7_B  = 10;
$d7_N  = 0;

$d8_MA = 16;
$d8_A  = 13;
$d8_M  = 10;
$d8_B  = 7;
$d8_N  = 0;


     if($row_total_c['Respuesta'] >= $ct_MA)                                       {$ct_ = "Muy Alto";}
else if($row_total_c['Respuesta'] >= $ct_A AND $row_total_c['Respuesta'] < $ct_MA) {$ct_ = "Alto";}
else if($row_total_c['Respuesta'] >= $ct_M AND $row_total_c['Respuesta'] < $ct_A)  {$ct_ = "Medio";}
else if($row_total_c['Respuesta'] >= $ct_B AND $row_total_c['Respuesta'] < $ct_M)  {$ct_ = "Bajo";}
else if($row_total_c['Respuesta'] >= $ct_N AND $row_total_c['Respuesta'] < $ct_B)  {$ct_ = "Nulo";}

if($row_categoria1['Respuesta'] >= $c1_MA)                                         {$c1_ = "Muy Alto";}
else if($row_categoria1['Respuesta'] >= $c1_A AND $row_categoria1['Respuesta'] < $c1_MA){$c1_ = "Alto";}
else if($row_categoria1['Respuesta'] >= $c1_M AND $row_categoria1['Respuesta'] < $c1_A) {$c1_ = "Medio";}
else if($row_categoria1['Respuesta'] >= $c1_B AND $row_categoria1['Respuesta'] < $c1_M) {$c1_ = "Bajo";}
else if($row_categoria1['Respuesta'] >= $c1_N AND $row_categoria1['Respuesta'] < $c1_B) {$c1_ = "Nulo";}


if($row_categoria2['Respuesta'] >= $c2_MA)                                         {$c2_ = "Muy Alto";}
else if($row_categoria2['Respuesta'] >= $c2_A AND $row_categoria2['Respuesta'] < $c2_MA){$c2_ = "Alto";}
else if($row_categoria2['Respuesta'] >= $c2_M AND $row_categoria2['Respuesta'] < $c2_A) {$c2_ = "Medio";}
else if($row_categoria2['Respuesta'] >= $c2_B AND $row_categoria2['Respuesta'] < $c2_M) {$c2_ = "Bajo";}
else if($row_categoria2['Respuesta'] >= $c2_N AND $row_categoria2['Respuesta'] < $c2_B) {$c2_ = "Nulo";}

if($row_categoria3['Respuesta'] >= $c3_MA)                                         {$c3_ = "Muy Alto";}
else if($row_categoria3['Respuesta'] >= $c3_A AND $row_categoria3['Respuesta'] < $c3_MA){$c3_ = "Alto";}
else if($row_categoria3['Respuesta'] >= $c3_M AND $row_categoria3['Respuesta'] < $c3_A) {$c3_ = "Medio";}
else if($row_categoria3['Respuesta'] >= $c3_B AND $row_categoria3['Respuesta'] < $c3_M) {$c3_ = "Bajo";}
else if($row_categoria3['Respuesta'] >= $c3_N AND $row_categoria3['Respuesta'] < $c3_B) {$c3_ = "Nulo";}

if($row_categoria4['Respuesta'] >= $c4_MA)                                         {$c4_ = "Muy Alto";}
else if($row_categoria4['Respuesta'] >= $c4_A AND $row_categoria4['Respuesta'] < $c4_MA){$c4_ = "Alto";}
else if($row_categoria4['Respuesta'] >= $c4_M AND $row_categoria4['Respuesta'] < $c4_A) {$c4_ = "Medio";}
else if($row_categoria4['Respuesta'] >= $c4_B AND $row_categoria4['Respuesta'] < $c4_M) {$c4_ = "Bajo";}
else if($row_categoria4['Respuesta'] >= $c4_N AND $row_categoria4['Respuesta'] < $c4_B) {$c4_ = "Nulo";}

if($row_categoria5['Respuesta'] >= $c4_MA)                                         {$c4_ = "Muy Alto";}
else if($row_categoria5['Respuesta'] >= $c5_A AND $row_categoria5['Respuesta'] < $c5_MA){$c5_ = "Alto";}
else if($row_categoria5['Respuesta'] >= $c5_M AND $row_categoria5['Respuesta'] < $c5_A) {$c5_ = "Medio";}
else if($row_categoria5['Respuesta'] >= $c5_B AND $row_categoria5['Respuesta'] < $c5_M) {$c5_ = "Bajo";}
else if($row_categoria5['Respuesta'] >= $c5_N AND $row_categoria5['Respuesta'] < $c5_B) {$c5_ = "Nulo";}



if($row_dominio1['Respuesta'] >= $d1_MA)                                         {$d1_ = "Muy Alto";}
else if($row_dominio1['Respuesta'] >= $d1_A AND $row_dominio1['Respuesta'] < $d1_MA){$d1_ = "Alto";}
else if($row_dominio1['Respuesta'] >= $d1_M AND $row_dominio1['Respuesta'] < $d1_A) {$d1_ = "Medio";}
else if($row_dominio1['Respuesta'] >= $d1_B AND $row_dominio1['Respuesta'] < $d1_M) {$d1_ = "Bajo";}
else if($row_dominio1['Respuesta'] >= $d1_N AND $row_dominio1['Respuesta'] < $d1_B) {$d1_ = "Nulo";}


if($row_dominio2['Respuesta'] >= $d2_MA)                                         {$d2_ = "Muy Alto";}
else if($row_dominio2['Respuesta'] >= $d2_A AND $row_dominio2['Respuesta'] < $d2_MA){$d2_ = "Alto";}
else if($row_dominio2['Respuesta'] >= $d2_M AND $row_dominio2['Respuesta'] < $d2_A) {$d2_ = "Medio";}
else if($row_dominio2['Respuesta'] >= $d2_B AND $row_dominio2['Respuesta'] < $d2_M) {$d2_ = "Bajo";}
else if($row_dominio2['Respuesta'] >= $d2_N AND $row_dominio2['Respuesta'] < $d2_B) {$d2_ = "Nulo";}


if($row_dominio3['Respuesta'] >= $d3_MA)                                         {$d3_ = "Muy Alto";}
else if($row_dominio3['Respuesta'] >= $d3_A AND $row_dominio3['Respuesta'] < $d3_MA){$d3_ = "Alto";}
else if($row_dominio3['Respuesta'] >= $d3_M AND $row_dominio3['Respuesta'] < $d3_A) {$d3_ = "Medio";}
else if($row_dominio3['Respuesta'] >= $d3_B AND $row_dominio3['Respuesta'] < $d3_M) {$d3_ = "Bajo";}
else if($row_dominio3['Respuesta'] >= $d3_N AND $row_dominio3['Respuesta'] < $d3_B) {$d3_ = "Nulo";}


if($row_dominio4['Respuesta'] >= $d4_MA)                                         {$d4_ = "Muy Alto";}
else if($row_dominio4['Respuesta'] >= $d4_A AND $row_dominio4['Respuesta'] < $d4_MA){$d4_ = "Alto";}
else if($row_dominio4['Respuesta'] >= $d4_M AND $row_dominio4['Respuesta'] < $d4_A) {$d4_ = "Medio";}
else if($row_dominio4['Respuesta'] >= $d4_B AND $row_dominio4['Respuesta'] < $d4_M) {$d4_ = "Bajo";}
else if($row_dominio4['Respuesta'] >= $d4_N AND $row_dominio4['Respuesta'] < $d4_B) {$d4_ = "Nulo";}


if($row_dominio5['Respuesta'] >= $d5_MA)                                         {$d5_ = "Muy Alto";}
else if($row_dominio5['Respuesta'] >= $d5_A AND $row_dominio5['Respuesta'] < $d5_MA){$d5_ = "Alto";}
else if($row_dominio5['Respuesta'] >= $d5_M AND $row_dominio5['Respuesta'] < $d5_A) {$d5_ = "Medio";}
else if($row_dominio5['Respuesta'] >= $d5_B AND $row_dominio5['Respuesta'] < $d5_M) {$d5_ = "Bajo";}
else if($row_dominio5['Respuesta'] >= $d5_N AND $row_dominio5['Respuesta'] < $d5_B) {$d5_ = "Nulo";}


if($row_dominio1['Respuesta'] >= $d6_MA)                                         {$d6_ = "Muy Alto";}
else if($row_dominio1['Respuesta'] >= $d6_A AND $row_dominio1['Respuesta'] < $d6_MA){$d6_ = "Alto";}
else if($row_dominio1['Respuesta'] >= $d6_M AND $row_dominio1['Respuesta'] < $d6_A) {$d6_ = "Medio";}
else if($row_dominio1['Respuesta'] >= $d6_B AND $row_dominio1['Respuesta'] < $d6_M) {$d6_ = "Bajo";}
else if($row_dominio1['Respuesta'] >= $d6_N AND $row_dominio1['Respuesta'] < $d6_B) {$d6_ = "Nulo";}


if($row_dominio2['Respuesta'] >= $d7_MA)                                         {$d7_ = "Muy Alto";}
else if($row_dominio2['Respuesta'] >= $d7_A AND $row_dominio2['Respuesta'] < $d7_MA){$d7_ = "Alto";}
else if($row_dominio2['Respuesta'] >= $d7_M AND $row_dominio2['Respuesta'] < $d7_A) {$d7_ = "Medio";}
else if($row_dominio2['Respuesta'] >= $d7_B AND $row_dominio2['Respuesta'] < $d7_M) {$d7_ = "Bajo";}
else if($row_dominio2['Respuesta'] >= $d7_N AND $row_dominio2['Respuesta'] < $d7_B) {$d7_ = "Nulo";}


if($row_dominio3['Respuesta'] >= $d8_MA)                                         {$d8_ = "Muy Alto";}
else if($row_dominio3['Respuesta'] >= $d8_A AND $row_dominio3['Respuesta'] < $d8_MA){$d8_ = "Alto";}
else if($row_dominio3['Respuesta'] >= $d8_M AND $row_dominio3['Respuesta'] < $d8_A) {$d8_ = "Medio";}
else if($row_dominio3['Respuesta'] >= $d8_B AND $row_dominio3['Respuesta'] < $d8_M) {$d8_ = "Bajo";}
else if($row_dominio3['Respuesta'] >= $d8_N AND $row_dominio3['Respuesta'] < $d8_B) {$d8_ = "Nulo";}



} else {

  $ct_MA = 90;
  $ct_A  = 70;
  $ct_M  = 45;
  $ct_B  = 20;
  $ct_N  = 0;

  $c1_MA = 9;
  $c1_A  = 7;
  $c1_M  = 5;
  $c1_B  = 3;
  $c1_N  = 0; 

  $c2_MA = 40;
  $c2_A  = 30;
  $c2_M  = 20;
  $c2_B  = 10;
  $c2_N  = 0;
  
  $c3_MA = 12 ;
  $c3_A  = 9;
  $c3_M  = 6;
  $c3_B  = 4;
  $c3_N  = 0;

  $c4_MA = 38;
  $c4_A  = 28;
  $c4_M  = 18;
  $c4_B  = 10;
  $c4_N  = 0;

  $d1_MA = 9;
  $d1_A  = 7;
  $d1_M  = 5;
  $d1_B  = 3;
  $d1_N  = 0;
  
  $d2_MA = 24;
  $d2_A  = 20;
  $d2_M  = 16;
  $d2_B  = 12;
  $d2_N  = 0;
  
  $d3_MA = 14;
  $d3_A  = 11;
  $d3_M  = 8;
  $d3_B  = 5;
  $d3_N  = 0;
  
  $d4_MA = 6;
  $d4_A  = 4;
  $d4_M  = 2;
  $d4_B  = 1;
  $d4_N  = 0;
  
  $d5_MA = 6;
  $d5_A  = 4;
  $d5_M  = 2;
  $d5_B  = 1;
  $d5_N  = 0;
  
  $d6_MA = 11;
  $d6_A  = 8;
  $d6_M  = 5;
  $d6_B  = 3;
  $d6_N  = 0;
  
  $d7_MA = 14;
  $d7_A  = 11;
  $d7_M  = 8;
  $d7_B  = 5;
  $d7_N  = 0;
  
  $d8_MA = 16;
  $d8_A  = 13;
  $d8_M  = 10;
  $d8_B  = 7;
  $d8_N  = 0;
  

  if($row_total_c['Respuesta'] >= $ct_MA)                                       {$ct_ = "Muy Alto";}
  else if($row_total_c['Respuesta'] >= $ct_A AND $row_total_c['Respuesta'] < $ct_MA) {$ct_ = "Alto";}
  else if($row_total_c['Respuesta'] >= $ct_M AND $row_total_c['Respuesta'] < $ct_A)  {$ct_ = "Medio";}
  else if($row_total_c['Respuesta'] >= $ct_B AND $row_total_c['Respuesta'] < $ct_M)  {$ct_ = "Bajo";}
  else if($row_total_c['Respuesta'] >= $ct_N AND $row_total_c['Respuesta'] < $ct_B)  {$ct_ = "Nulo";}
  
  if($row_categoria1['Respuesta'] >= $c1_MA)                                         {$c1_ = "Muy Alto";}
  else if($row_categoria1['Respuesta'] >= $c1_A AND $row_categoria1['Respuesta'] < $c1_MA){$c1_ = "Alto";}
  else if($row_categoria1['Respuesta'] >= $c1_M AND $row_categoria1['Respuesta'] < $c1_A) {$c1_ = "Medio";}
  else if($row_categoria1['Respuesta'] >= $c1_B AND $row_categoria1['Respuesta'] < $c1_M) {$c1_ = "Bajo";}
  else if($row_categoria1['Respuesta'] >= $c1_N AND $row_categoria1['Respuesta'] < $c1_B) {$c1_ = "Nulo";}
  
  
  if($row_categoria2['Respuesta'] >= $c2_MA)                                         {$c2_ = "Muy Alto";}
  else if($row_categoria2['Respuesta'] >= $c2_A AND $row_categoria2['Respuesta'] < $c2_MA){$c2_ = "Alto";}
  else if($row_categoria2['Respuesta'] >= $c2_M AND $row_categoria2['Respuesta'] < $c2_A) {$c2_ = "Medio";}
  else if($row_categoria2['Respuesta'] >= $c2_B AND $row_categoria2['Respuesta'] < $c2_M) {$c2_ = "Bajo";}
  else if($row_categoria2['Respuesta'] >= $c2_N AND $row_categoria2['Respuesta'] < $c2_B) {$c2_ = "Nulo";}
  
  if($row_categoria3['Respuesta'] >= $c3_MA)                                         {$c3_ = "Muy Alto";}
  else if($row_categoria3['Respuesta'] >= $c3_A AND $row_categoria3['Respuesta'] < $c3_MA){$c3_ = "Alto";}
  else if($row_categoria3['Respuesta'] >= $c3_M AND $row_categoria3['Respuesta'] < $c3_A) {$c3_ = "Medio";}
  else if($row_categoria3['Respuesta'] >= $c3_B AND $row_categoria3['Respuesta'] < $c3_M) {$c3_ = "Bajo";}
  else if($row_categoria3['Respuesta'] >= $c3_N AND $row_categoria3['Respuesta'] < $c3_B) {$c3_ = "Nulo";}
  
  if($row_categoria4['Respuesta'] >= $c4_MA)                                         {$c4_ = "Muy Alto";}
  else if($row_categoria4['Respuesta'] >= $c4_A AND $row_categoria4['Respuesta'] < $c4_MA){$c4_ = "Alto";}
  else if($row_categoria4['Respuesta'] >= $c4_M AND $row_categoria4['Respuesta'] < $c4_A) {$c4_ = "Medio";}
  else if($row_categoria4['Respuesta'] >= $c4_B AND $row_categoria4['Respuesta'] < $c4_M) {$c4_ = "Bajo";}
  else if($row_categoria4['Respuesta'] >= $c4_N AND $row_categoria4['Respuesta'] < $c4_B) {$c4_ = "Nulo";}
  
  if($row_categoria5['Respuesta'] >= $c4_MA)                                         {$c4_ = "Muy Alto";}
  else if($row_categoria5['Respuesta'] >= $c5_A AND $row_categoria5['Respuesta'] < $c5_MA){$c5_ = "Alto";}
  else if($row_categoria5['Respuesta'] >= $c5_M AND $row_categoria5['Respuesta'] < $c5_A) {$c5_ = "Medio";}
  else if($row_categoria5['Respuesta'] >= $c5_B AND $row_categoria5['Respuesta'] < $c5_M) {$c5_ = "Bajo";}
  else if($row_categoria5['Respuesta'] >= $c5_N AND $row_categoria5['Respuesta'] < $c5_B) {$c5_ = "Nulo";}
  
  
  
  if($row_dominio1['Respuesta'] >= $d1_MA)                                         {$d1_ = "Muy Alto";}
  else if($row_dominio1['Respuesta'] >= $d1_A AND $row_dominio1['Respuesta'] < $d1_MA){$d1_ = "Alto";}
  else if($row_dominio1['Respuesta'] >= $d1_M AND $row_dominio1['Respuesta'] < $d1_A) {$d1_ = "Medio";}
  else if($row_dominio1['Respuesta'] >= $d1_B AND $row_dominio1['Respuesta'] < $d1_M) {$d1_ = "Bajo";}
  else if($row_dominio1['Respuesta'] >= $d1_N AND $row_dominio1['Respuesta'] < $d1_B) {$d1_ = "Nulo";}
  
  
  if($row_dominio2['Respuesta'] >= $d2_MA)                                         {$d2_ = "Muy Alto";}
  else if($row_dominio2['Respuesta'] >= $d2_A AND $row_dominio2['Respuesta'] < $d2_MA){$d2_ = "Alto";}
  else if($row_dominio2['Respuesta'] >= $d2_M AND $row_dominio2['Respuesta'] < $d2_A) {$d2_ = "Medio";}
  else if($row_dominio2['Respuesta'] >= $d2_B AND $row_dominio2['Respuesta'] < $d2_M) {$d2_ = "Bajo";}
  else if($row_dominio2['Respuesta'] >= $d2_N AND $row_dominio2['Respuesta'] < $d2_B) {$d2_ = "Nulo";}
  
  
  if($row_dominio3['Respuesta'] >= $d3_MA)                                         {$d3_ = "Muy Alto";}
  else if($row_dominio3['Respuesta'] >= $d3_A AND $row_dominio3['Respuesta'] < $d3_MA){$d3_ = "Alto";}
  else if($row_dominio3['Respuesta'] >= $d3_M AND $row_dominio3['Respuesta'] < $d3_A) {$d3_ = "Medio";}
  else if($row_dominio3['Respuesta'] >= $d3_B AND $row_dominio3['Respuesta'] < $d3_M) {$d3_ = "Bajo";}
  else if($row_dominio3['Respuesta'] >= $d3_N AND $row_dominio3['Respuesta'] < $d3_B) {$d3_ = "Nulo";}
  
  
  if($row_dominio4['Respuesta'] >= $d4_MA)                                         {$d4_ = "Muy Alto";}
  else if($row_dominio4['Respuesta'] >= $d4_A AND $row_dominio4['Respuesta'] < $d4_MA){$d4_ = "Alto";}
  else if($row_dominio4['Respuesta'] >= $d4_M AND $row_dominio4['Respuesta'] < $d4_A) {$d4_ = "Medio";}
  else if($row_dominio4['Respuesta'] >= $d4_B AND $row_dominio4['Respuesta'] < $d4_M) {$d4_ = "Bajo";}
  else if($row_dominio4['Respuesta'] >= $d4_N AND $row_dominio4['Respuesta'] < $d4_B) {$d4_ = "Nulo";}
  
  
  if($row_dominio5['Respuesta'] >= $d5_MA)                                         {$d5_ = "Muy Alto";}
  else if($row_dominio5['Respuesta'] >= $d5_A AND $row_dominio5['Respuesta'] < $d5_MA){$d5_ = "Alto";}
  else if($row_dominio5['Respuesta'] >= $d5_M AND $row_dominio5['Respuesta'] < $d5_A) {$d5_ = "Medio";}
  else if($row_dominio5['Respuesta'] >= $d5_B AND $row_dominio5['Respuesta'] < $d5_M) {$d5_ = "Bajo";}
  else if($row_dominio5['Respuesta'] >= $d5_N AND $row_dominio5['Respuesta'] < $d5_B) {$d5_ = "Nulo";}
  
  
  if($row_dominio1['Respuesta'] >= $d6_MA)                                         {$d6_ = "Muy Alto";}
  else if($row_dominio1['Respuesta'] >= $d6_A AND $row_dominio1['Respuesta'] < $d6_MA){$d6_ = "Alto";}
  else if($row_dominio1['Respuesta'] >= $d6_M AND $row_dominio1['Respuesta'] < $d6_A) {$d6_ = "Medio";}
  else if($row_dominio1['Respuesta'] >= $d6_B AND $row_dominio1['Respuesta'] < $d6_M) {$d6_ = "Bajo";}
  else if($row_dominio1['Respuesta'] >= $d6_N AND $row_dominio1['Respuesta'] < $d6_B) {$d6_ = "Nulo";}
  
  
  if($row_dominio2['Respuesta'] >= $d7_MA)                                         {$d7_ = "Muy Alto";}
  else if($row_dominio2['Respuesta'] >= $d7_A AND $row_dominio2['Respuesta'] < $d7_MA){$d7_ = "Alto";}
  else if($row_dominio2['Respuesta'] >= $d7_M AND $row_dominio2['Respuesta'] < $d7_A) {$d7_ = "Medio";}
  else if($row_dominio2['Respuesta'] >= $d7_B AND $row_dominio2['Respuesta'] < $d7_M) {$d7_ = "Bajo";}
  else if($row_dominio2['Respuesta'] >= $d7_N AND $row_dominio2['Respuesta'] < $d7_B) {$d7_ = "Nulo";}
  
  
  if($row_dominio3['Respuesta'] >= $d8_MA)                                         {$d8_ = "Muy Alto";}
  else if($row_dominio3['Respuesta'] >= $d8_A AND $row_dominio3['Respuesta'] < $d8_MA){$d8_ = "Alto";}
  else if($row_dominio3['Respuesta'] >= $d8_M AND $row_dominio3['Respuesta'] < $d8_A) {$d8_ = "Medio";}
  else if($row_dominio3['Respuesta'] >= $d8_B AND $row_dominio3['Respuesta'] < $d8_M) {$d8_ = "Bajo";}
  else if($row_dominio3['Respuesta'] >= $d8_N AND $row_dominio3['Respuesta'] < $d8_B) {$d8_ = "Nulo";}
  
  
  if($row_dominio4['Respuesta'] >= $d9_MA)                                         {$d9_ = "Muy Alto";}
  else if($row_dominio4['Respuesta'] >= $d9_A AND $row_dominio4['Respuesta'] < $d9_MA){$d9_ = "Alto";}
  else if($row_dominio4['Respuesta'] >= $d9_M AND $row_dominio4['Respuesta'] < $d9_A) {$d9_ = "Medio";}
  else if($row_dominio4['Respuesta'] >= $d9_B AND $row_dominio4['Respuesta'] < $d9_M) {$d9_ = "Bajo";}
  else if($row_dominio4['Respuesta'] >= $d9_N AND $row_dominio4['Respuesta'] < $d9_B) {$d9_ = "Nulo";}
  
  
  if($row_dominio5['Respuesta'] >= $d10_MA)                                         {$d10_ = "Muy Alto";}
  else if($row_dominio5['Respuesta'] >= $d10_A AND $row_dominio5['Respuesta'] < $d10_MA){$d10_ = "Alto";}
  else if($row_dominio5['Respuesta'] >= $d10_M AND $row_dominio5['Respuesta'] < $d10_A) {$d10_ = "Medio";}
  else if($row_dominio5['Respuesta'] >= $d10_B AND $row_dominio5['Respuesta'] < $d10_M) {$d10_ = "Bajo";}
  else if($row_dominio5['Respuesta'] >= $d10_N AND $row_dominio5['Respuesta'] < $d10_B) {$d10_ = "Nulo";}
  
}

if ($ct_ == "Muy Alto"){ $IDnivel = 5;}
else if ($ct_ == "Alto")    { $IDnivel = 4;}
else if ($ct_ == "Medio")   { $IDnivel = 3;}
else if ($ct_ == "Bajo")    { $IDnivel = 2;}
else if ($ct_ == "Nulo")    { $IDnivel = 1;}

mysql_select_db($database_vacantes, $vacantes);
$query_nivelesr = "SELECT * FROM nom35_niveles_riesgo WHERE IDnivel = $IDnivel";
$nivelesr = mysql_query($query_nivelesr, $vacantes) or die(mysql_error());
$row_nivelesr = mysql_fetch_assoc($nivelesr);
$totalRows_nivelesr = mysql_num_rows($nivelesr);
$interpretacion = $row_nivelesr['texto'];


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="assets/print.css" media="print" />
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
  <link rel="stylesheet" type="text/css" href="assets/print.css" media="print" />

	<script src="assets/js/app.js"></script>
	<!-- /theme JS files -->

  <style> 
    table td:first-child {  width: 40%; } 
    div.a { text-align: center; }
    th { text-align: center; }
    td { text-align: center; }
    </style>

</head>
<body class="has-detached-left" onLoad="window.print()">

<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">



			<!-- Main content -->
			<div class="content-wrapper">		

            <!-- Content area -->
            <div class="content">
                            
					<!-- Contenido -->
                  <div class="panel panel-flat">
					<div class="panel-body">
          <h5>Política de Prevención de Riesgos Psicosociales</h5>
        <p><b>Impulsora Sahuayo S.A. de C.V. </b>promueve la prevención de los factores de riesgo psicosocial; la prevención de la violencia laboral, y la promoción de un entorno organizacional favorable, la diversidad e inclusión, facilitando a los colaboradores acciones de sensibilización, programas de comunicación, capacitación y espacios de participación y consulta, quedando estrictamente prohibidos los actos de violencia laboral, represalias, abusos, discriminación por creencias, raza, sexo, religión, etnia o edad, preferencia sexual o cualquier otra condición que derive en riesgo psicosocial o acciones en contra del favorable entorno organizacional.</p>

					</div>
                    
					<!-- /Contenido -->
					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">

								<!-- User details -->
								<div class="content-group">
									<div class="panel-body bg-indigo-400 border-radius-top text-center" style="background-image:
                                     url(http://demo.interface.club/limitless/assets/images/bg.png); background-size: contain;">
										<div class="content-group-sm">
											<h6 class="text-semibold no-margin-bottom">
												<?php echo $row_resultado['emp_paterno'] . " " .  $row_resultado['emp_materno'] . "<br/> " .  $row_resultado['emp_nombre']; ?>
											</h6>
											<span class="display-block">No. Emp. <?php echo $row_resultado['IDempleado']; ?></span>
										</div>
										<a href="#" class="display-inline-block content-group-sm">
											<img src="global_assets/images/placeholders/placeholder.jpg" class="img-circle img-responsive" alt="" style="width: 110px; height: 110px;">
										</a>
									</div>

									<div class="panel no-border-top no-border-radius-top">
										<ul class="navigation">
											<li class="navigation-header">Datos</li>
											<li><a href="#" data-toggle="tab">No. Emp.: <?php echo $row_resultado['IDempleado']; ?></a></li>
											<li><a href="#" data-toggle="tab">Paterno: <?php echo $row_resultado['emp_paterno']; ?></a></li>
											<li><a href="#" data-toggle="tab">Materno: <?php echo $row_resultado['emp_materno']; ?></a></li>
											<li><a href="#" data-toggle="tab">Nombres: <?php echo $row_resultado['emp_nombre']; ?></a></li>
											<li><a href="#" data-toggle="tab">Sucursal: <?php echo $row_matriz['matriz']; ?></a></li>
											<li><a href="#" data-toggle="tab">Puesto: <?php echo $row_resultado['denominacion']; ?></a></li>
											<li><a href="#" data-toggle="tab">Ingreso: <?php 
											 $afecha = date('d/m/Y', strtotime($row_resultado['fecha_alta'])); echo $afecha; ?></a></li>
											<li><a href="#" data-toggle="tab">Fecha: <?php 
											 $afecha = date('d/m/Y', strtotime($row_resultado['fecha_aplicacion'])); echo $afecha; ?></a></li>
										</ul>
									</div>
								</div>
								<!-- /user details -->


							</div>
						</div>
					</div>
					</div>
		            <!-- /detached sidebar -->


					<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">

							<!-- Tab content -->
							<div class="tab-content">
							  <div class="tab-pane fade in active" id="profile">

									<div class="panel panel-flat">                                        
                      <div class="panel-body">
        <legend class="text-bold">Resultado General</legend>


        <p><b>Puntos Totales: </b><?php echo $row_total_c['Respuesta']; ?> </p>
        <p><b>Riesgo <?php echo $ct_; ?>:</b> <?php echo $interpretacion; ?></p>

            <p>&nbsp;</p>
            <legend class="text-bold">Resultados por categoria</legend>
            <table class="table table-bordered table-condensed">
                <thead>
                <tr style="text-align: center;">
                        <th>Categoría</th>
                        <th>Calificacion</th>
                        <th>Nivel Riesgo</th>
                    </tr>
                </thead>
                <tbody class="border border-neutral">
                    <tr>
                    <td class="font-semibold"><span class="text text-info">Ambiente de trabajo</span></td>
                    <td><?php echo $row_categoria1['Respuesta']; ?></td>
                    <td><?php echo $c1_; ?></td>
                    </tr>
                    <tr>
                    <td class="font-semibold"><span class="text text-info">Factores Propios de la actividad</span></td>
                    <td><?php echo $row_categoria2['Respuesta']; ?></td>
                    <td><?php echo $c2_; ?></td>
                    </tr>
                    <tr>
                    <td class="font-semibold"><span class="text text-info">Organización del tiempo de trabajo</span></td>
                    <td><?php echo $row_categoria3['Respuesta']; ?></td>
                    <td><?php echo $c3_; ?></td>
                    </tr>
                    <tr>
                    <td class="font-semibold"><span class="text text-info">Liderazgo y relaciones del trabajo</span></td>
                    <td><?php echo $row_categoria4['Respuesta']; ?></td>
                    <td><?php echo $c4_; ?></td>
                    </tr>
                </tbody>
            </table>

 
<p>&nbsp;</p>
<legend class="text-bold">Resultados por Dominio</legend>
<table class="table table-bordered table-condensed">
    <thead>
    <tr style="text-align: center;">
            <th>Dominio</th>
            <th>Calificacion</th>
            <th>Nivel Riesgo</th>
        </tr>
    </thead>
    <tbody class="border border-neutral">
        <tr>
        <td class="font-semibold"><span class="text text-primary">Condiciones en el ambiente de trabajo</span></td>
        <td><?php echo $row_dominio1['Respuesta']; ?></td>
        <td><?php echo $d1_; ?></td>
        </tr>
        <tr>
        <td class="font-semibold"><span class="text text-primary">Carga de trabajo</span></td>
        <td><?php echo $row_dominio2['Respuesta']; ?></td>
        <td><?php echo $d2_; ?></td>
        </tr>
        <tr>
        <td class="font-semibold"><span class="text text-primary">Falta de control sobre el trabajo</span></td>
        <td><?php echo $row_dominio3['Respuesta']; ?></td>
        <td><?php echo $d3_; ?></td>
        </tr>
        <tr>
        <td class="font-semibold"><span class="text text-primary">Jornada de trabajo</span></td>
        <td><?php echo $row_dominio4['Respuesta']; ?></td>
        <td><?php echo $d4_; ?></td>
        </tr>
        <tr>
        <td class="font-semibold"><span class="text text-primary">Interferencia en la relación trabajo-familia</span></td>
        <td><?php echo $row_dominio5['Respuesta']; ?></td>
        <td><?php echo $d5_; ?></td>
        </tr>
        <tr>
        <td class="font-semibold"><span class="text text-primary">Liderazgo</span></td>
        <td><?php echo $row_dominio6['Respuesta']; ?></td>
        <td><?php echo $d6_; ?></td>
        </tr>
        <tr>
        <td class="font-semibold"><span class="text text-primary">Relaciones en el trabajo</span></td>
        <td><?php echo $row_dominio7['Respuesta']; ?></td>
        <td><?php echo $d7_; ?></td>
        </tr>
        <tr>
        <td class="font-semibold"><span class="text text-primary">Violencia</span></td>
        <td><?php echo $row_dominio8['Respuesta']; ?></td>
        <td><?php echo $d8_; ?></td>
        </tr>
    </tbody>
</table>



                      </div>
                  </div>
                                    
                  

									<!-- Share your thoughts -->
									<div class="panel panel-flat">
										<div class="panel-heading">
                    <legend class="text-bold">Plan de Acción</legend>
										</div>

										<div class="panel-body">
											<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
												<div class="form-group">
                            <textarea name="plan_accion" class="form-control mb-15" rows="3" cols="1"><?php echo htmlentities($row_resultado['plan_accion'], ENT_COMPAT, 'utf-8'); ?></textarea>
                            <input type="hidden" name="MM_update" value="form1" />
                            <input type="hidden" name="IDresultado" value="<?php echo $row_resultado['IDresultado']; ?>" />
												</div>

												      <div class="row">
						                    <div class="col-xs-6">
						                    </div>

                            <div class="col-xs-6 text-right">
                            </div>
						              </div>
					            </form>
				                    	</div>
									</div>
									<!-- /share your thoughts -->

								</div>
								</div>
							</div>
							<!-- /tab content -->

						</div>
					<!-- /detached content -->                    
                    
                    

					<!-- Footer -->
					<div class="footer text-muted">
						 Recursos Humanos | <?php echo $row_variables['empresa']; ?> | <?php echo $anio; ?>
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