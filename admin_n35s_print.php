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
mysql_query("SET NAMES 'utf8'");
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
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz']; 


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$margen = $row_matriz['nom35_margen']; 
$margen = 100 + $row_matriz['nom35_margen']; 
$margen = $margen / 100; 

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

mysql_select_db($database_vacantes, $vacantes);
$query_resultados1 = "SELECT DISTINCT IDempleado FROM nom35_resultados WHERE IDmatriz = $IDmatriz AND IDexamen = 1";
$resultados1 = mysql_query($query_resultados1, $vacantes) or die(mysql_error());
$row_resultados1 = mysql_fetch_assoc($resultados1);
$totalRows_resultados1 = mysql_num_rows($resultados1);

mysql_select_db($database_vacantes, $vacantes);
$query_resultados3 = "SELECT DISTINCT IDempleado FROM nom35_resultados WHERE IDmatriz = $IDmatriz AND IDexamen = 3";
$resultados3 = mysql_query($query_resultados3, $vacantes) or die(mysql_error());
$row_resultados3 = mysql_fetch_assoc($resultados3);
$totalRows_resultados3 = mysql_num_rows($resultados3);

//activos
mysql_select_db($database_vacantes, $vacantes);
$query_resultadosa1 = "SELECT * FROM prod_activos WHERE IDmatriz = $IDmatriz";
$resultadosa1 = mysql_query($query_resultadosa1, $vacantes) or die(mysql_error());
$row_resultadosa1 = mysql_fetch_assoc($resultadosa1);
$totalRows_resultadosa1 = mysql_num_rows($resultadosa1);

mysql_select_db($database_vacantes, $vacantes);
$query_resultadosa3 = "SELECT * FROM prod_activos WHERE IDmatriz = $IDmatriz";
$resultadosa3 = mysql_query($query_resultadosa3, $vacantes) or die(mysql_error());
$row_resultadosa3 = mysql_fetch_assoc($resultadosa3);
$totalRows_resultadosa3 = mysql_num_rows($resultadosa3);

mysql_select_db($database_vacantes, $vacantes);
$query_respuestas1a = "SELECT DISTINCT prod_activos.IDmatriz, prod_activos.IDpuesto, prod_activos.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.respuesta, nom35_respuestas.IDperiodo, nom35_respuestas.IDpregunta  FROM prod_activos LEFT JOIN nom35_respuestas ON prod_activos.IDempleado = nom35_respuestas.IDempleado WHERE prod_activos.IDmatriz = $IDmatriz AND nom35_respuestas.IDperiodo = $IDperiodo AND nom35_respuestas.IDexamen = 1 GROUP BY prod_activos.IDempleado";
$respuestas1a = mysql_query($query_respuestas1a, $vacantes) or die(mysql_error());
$row_respuestas1a = mysql_fetch_assoc($respuestas1a);
$totalRows_respuestas1a = mysql_num_rows($respuestas1a);

mysql_select_db($database_vacantes, $vacantes);
$query_respuestas1b = "SELECT DISTINCT prod_activos.IDmatriz, prod_activos.IDpuesto, prod_activos.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.respuesta, nom35_respuestas.IDperiodo, nom35_respuestas.IDpregunta  FROM prod_activos LEFT JOIN nom35_respuestas ON prod_activos.IDempleado = nom35_respuestas.IDempleado WHERE prod_activos.IDmatriz = $IDmatriz AND nom35_respuestas.IDpregunta = 1 AND nom35_respuestas.respuesta != 5 AND  nom35_respuestas.IDperiodo = $IDperiodo AND nom35_respuestas.IDexamen = 1";
$respuestas1b = mysql_query($query_respuestas1b, $vacantes) or die(mysql_error());
$row_respuestas1b = mysql_fetch_assoc($respuestas1b);
$totalRows_respuestas1b = mysql_num_rows($respuestas1b);

mysql_select_db($database_vacantes, $vacantes);
$query_respuestas1c = "SELECT DISTINCT prod_activos.IDmatriz, prod_activos.IDpuesto, prod_activos.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.respuesta, nom35_respuestas.IDperiodo, nom35_respuestas.IDpregunta  FROM prod_activos LEFT JOIN nom35_respuestas ON prod_activos.IDempleado = nom35_respuestas.IDempleado WHERE prod_activos.IDmatriz = $IDmatriz AND nom35_respuestas.IDpregunta = 1 AND nom35_respuestas.respuesta = 5 AND  nom35_respuestas.IDperiodo = $IDperiodo AND nom35_respuestas.IDexamen = 1";
$respuestas1c = mysql_query($query_respuestas1c, $vacantes) or die(mysql_error());
$row_respuestas1c = mysql_fetch_assoc($respuestas1c);
$totalRows_respuestas1c = mysql_num_rows($respuestas1c);

$n1_porcentaje1 = round(($totalRows_respuestas1c / $totalRows_respuestas1a) * 100, 0);
$n1_porcentaje2 = round(($totalRows_respuestas1b / $totalRows_respuestas1a) * 100, 0);
$n1_porcentaje3 = '100';

//RESULTADOS TOTALES
mysql_select_db($database_vacantes, $vacantes);
$query_total_c = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$total_c = mysql_query($query_total_c, $vacantes) or die(mysql_error());
$row_total_c = mysql_fetch_assoc($total_c);
$totalRows_total_c = mysql_num_rows($total_c);


//CATEGORIA 1 Factores propios de la actividad
mysql_select_db($database_vacantes, $vacantes);
$query_categoria1 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 1 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$categoria1 = mysql_query($query_categoria1, $vacantes) or die(mysql_error());
$row_categoria1 = mysql_fetch_assoc($categoria1);
$totalRows_categoria1 = mysql_num_rows($categoria1);

//CATEGORIA 2 Factores propios de la actividad
mysql_select_db($database_vacantes, $vacantes);
$query_categoria2 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 2 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$categoria2 = mysql_query($query_categoria2, $vacantes) or die(mysql_error());
$row_categoria2 = mysql_fetch_assoc($categoria2);
$totalRows_categoria2 = mysql_num_rows($categoria2);

//CATEGORIA 3 Organización del tiempo de trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_categoria3 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 3 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$categoria3 = mysql_query($query_categoria3, $vacantes) or die(mysql_error());
$row_categoria3 = mysql_fetch_assoc($categoria3);
$totalRows_categoria3 = mysql_num_rows($categoria3);

//CATEGORIA 4 Liderazgo y relaciones en el trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_categoria4 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 4 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$categoria4 = mysql_query($query_categoria4, $vacantes) or die(mysql_error());
$row_categoria4 = mysql_fetch_assoc($categoria4);
$totalRows_categoria4 = mysql_num_rows($categoria4);

//CATEGORIA 5 Liderazgo y relaciones en el trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_categoria5 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDcategoria = 5 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$categoria5 = mysql_query($query_categoria5, $vacantes) or die(mysql_error());
$row_categoria5 = mysql_fetch_assoc($categoria5);
$totalRows_categoria5 = mysql_num_rows($categoria5);


//DOMINIO  1 Condiciones en el ambiente de trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio1 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 1 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio1 = mysql_query($query_dominio1, $vacantes) or die(mysql_error());
$row_dominio1 = mysql_fetch_assoc($dominio1);
$totalRows_dominio1 = mysql_num_rows($dominio1);

//DOMINIO 2 Carga de trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio2 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 2 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio2 = mysql_query($query_dominio2, $vacantes) or die(mysql_error());
$row_dominio2 = mysql_fetch_assoc($dominio2);
$totalRows_dominio2 = mysql_num_rows($dominio2);

//DOMINIO 3 Falta de control sobre el trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio3 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 3 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio3 = mysql_query($query_dominio3, $vacantes) or die(mysql_error());
$row_dominio3 = mysql_fetch_assoc($dominio3);
$totalRows_dominio3 = mysql_num_rows($dominio3);

//DOMINIO 4 Jornada de trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio4 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 4 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio4 = mysql_query($query_dominio4, $vacantes) or die(mysql_error());
$row_dominio4 = mysql_fetch_assoc($dominio4);
$totalRows_dominio4 = mysql_num_rows($dominio4);

//DOMINIO 5 Interferencia en la relación trabajo-familia
mysql_select_db($database_vacantes, $vacantes);
$query_dominio5 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 5 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio5 = mysql_query($query_dominio5, $vacantes) or die(mysql_error());
$row_dominio5 = mysql_fetch_assoc($dominio5);
$totalRows_dominio5 = mysql_num_rows($dominio5);

//DOMINIO 6 Liderazgo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio6 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 6 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio6 = mysql_query($query_dominio6, $vacantes) or die(mysql_error());
$row_dominio6 = mysql_fetch_assoc($dominio6);
$totalRows_dominio6 = mysql_num_rows($dominio6);

//DOMINIO 7 Relaciones en el trabajo
mysql_select_db($database_vacantes, $vacantes);
$query_dominio7 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 7 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio7 = mysql_query($query_dominio7, $vacantes) or die(mysql_error());
$row_dominio7 = mysql_fetch_assoc($dominio7);
$totalRows_dominio7 = mysql_num_rows($dominio7);

//DOMINIO 8 Violencia
mysql_select_db($database_vacantes, $vacantes);
$query_dominio8 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 8 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio8 = mysql_query($query_dominio8, $vacantes) or die(mysql_error());
$row_dominio8 = mysql_fetch_assoc($dominio8);
$totalRows_dominio8 = mysql_num_rows($dominio8);

//DOMINIO 9 Reconocimiento del desempeño
mysql_select_db($database_vacantes, $vacantes);
$query_dominio9 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 9 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio9 = mysql_query($query_dominio9, $vacantes) or die(mysql_error());
$row_dominio9 = mysql_fetch_assoc($dominio9);
$totalRows_dominio9 = mysql_num_rows($dominio9);

//DOMINIO 10 Insuficiente sentido de pertenencia e, inestabilidad
mysql_select_db($database_vacantes, $vacantes);
$query_dominio10 = "SELECT nom35_respuestas.IDrespuesta, nom35_respuestas.IDempleado, nom35_respuestas.IDexamen, nom35_respuestas.IDpregunta, nom35_respuestas.IDperiodo, SUM(nom35_respuestas.respuesta) AS Respuesta, nom35_respuestas.IDcategoria, nom35_respuestas.IDdominio, nom35_respuestas.IDdimension, prod_activos.IDmatriz FROM nom35_respuestas INNER JOIN prod_activos ON nom35_respuestas.IDempleado = prod_activos.IDempleado WHERE nom35_respuestas.IDexamen = $IDexmen AND nom35_respuestas.IDdominio = 10 AND prod_activos.IDmatriz = $IDmatriz  AND nom35_respuestas.pregunta_tipo <> 3  GROUP BY nom35_respuestas.IDempleado"; 
$dominio10 = mysql_query($query_dominio10, $vacantes) or die(mysql_error());
$row_dominio10 = mysql_fetch_assoc($dominio10);
$totalRows_dominio10 = mysql_num_rows($dominio10);

if ($IDexmen == 3){
    
// el examen es 3, se repite todo
$ct_MA = 140* $margen; 
$ct_A  = 99* $margen; 
$ct_M  = 75* $margen; 
$ct_B  = 50* $margen; 
$ct_N  = 0;
    
$ct_MA_r = 0;
$ct_A_r  = 0;
$ct_M_r  = 0;
$ct_B_r  = 0;
$ct_N_r  = 0;
$promedio = 0;

do { 
         if($row_total_c['Respuesta'] >= $ct_MA)                                       {$ct_MA_r++;}
    else if($row_total_c['Respuesta'] >= $ct_A AND $row_total_c['Respuesta'] < $ct_MA) {$ct_A_r++;}
    else if($row_total_c['Respuesta'] >= $ct_M AND $row_total_c['Respuesta'] < $ct_A)  {$ct_M_r++;}
    else if($row_total_c['Respuesta'] >= $ct_B AND $row_total_c['Respuesta'] < $ct_M)  {$ct_B_r++;}
    else if($row_total_c['Respuesta'] >= $ct_N AND $row_total_c['Respuesta'] < $ct_B)  {$ct_N_r++;}
    $promedio = $promedio + $row_total_c['Respuesta'];
} while ($row_total_c = mysql_fetch_assoc($total_c));

$ct_T_r = $ct_MA_r + $ct_A_r + $ct_M_r + $ct_B_r + $ct_N_r;
$promedio = $promedio / $totalRows_total_c;

$ct_porcentaje1 = round(($ct_MA_r / $ct_T_r) * 100, 0);
$ct_porcentaje2 = round(($ct_A_r / $ct_T_r) * 100, 0);
$ct_porcentaje3 = round(($ct_M_r / $ct_T_r) * 100, 0);
$ct_porcentaje4 = round(($ct_B_r / $ct_T_r) * 100, 0);
$ct_porcentaje5 = round(($ct_N_r / $ct_T_r) * 100, 0);
$ct_porcentajeT = 100;


//nivel de riesgo mas recurrente

if(($ct_MA_r >= $ct_A_r)  AND ($ct_MA_r >= $ct_M_r) AND ($ct_MA_r >= $ct_B_r) AND ($ct_MA_r >= $ct_N_r)){ $val_max = "Muy Alto"; $IDnivel = 5; }
if(($ct_A_r  >= $ct_MA_r) AND ($ct_A_r  >= $ct_M_r) AND ($ct_A_r  >= $ct_B_r) AND ($ct_A_r  >= $ct_N_r)){ $val_max = "Alto";     $IDnivel = 4; }
if(($ct_M_r  >= $ct_MA_r) AND ($ct_M_r  >= $ct_A_r) AND ($ct_M_r  >= $ct_B_r) AND ($ct_M_r  >= $ct_N_r)){ $val_max = "Medio";    $IDnivel = 3; }
if(($ct_B_r  >= $ct_MA_r) AND ($ct_B_r  >= $ct_A_r) AND ($ct_B_r  >= $ct_M_r) AND ($ct_B_r  >= $ct_N_r)){ $val_max = "Bajo";     $IDnivel = 2; }
if(($ct_N_r  >= $ct_MA_r) AND ($ct_N_r  >= $ct_A_r) AND ($ct_N_r  >= $ct_M_r) AND ($ct_N_r  >= $ct_B_r)){ $val_max = "Nulo";     $IDnivel = 1; }


mysql_select_db($database_vacantes, $vacantes);
$query_nivelesr = "SELECT * FROM nom35_niveles_riesgo WHERE IDnivel = $IDnivel";
$nivelesr = mysql_query($query_nivelesr, $vacantes) or die(mysql_error());
$row_nivelesr = mysql_fetch_assoc($nivelesr);
$totalRows_nivelesr = mysql_num_rows($nivelesr);
$interpretacion = $row_nivelesr['texto'];

$c1_MA = 14* $margen; 
$c1_A  = 11* $margen; 
$c1_M  = 9* $margen; 
$c1_B  = 5* $margen; 
$c1_N  = 0; 

$c1_MA_r = 0;
$c1_A_r  = 0;
$c1_M_r  = 0;
$c1_B_r  = 0;
$c1_N_r  = 0;

do { 
         if($row_categoria1['Respuesta'] >= $c1_MA)                                         {$c1_MA_r++;}
    else if($row_categoria1['Respuesta'] >= $c1_A AND $row_categoria1['Respuesta'] < $c1_MA){$c1_A_r++;}
    else if($row_categoria1['Respuesta'] >= $c1_M AND $row_categoria1['Respuesta'] < $c1_A) {$c1_M_r++;}
    else if($row_categoria1['Respuesta'] >= $c1_B AND $row_categoria1['Respuesta'] < $c1_M) {$c1_B_r++;}
    else if($row_categoria1['Respuesta'] >= $c1_N AND $row_categoria1['Respuesta'] < $c1_B) {$c1_N_r++;}
} while ($row_categoria1 = mysql_fetch_assoc($categoria1));

$c1_T_r = $c1_MA_r + $c1_A_r + $c1_M_r + $c1_B_r + $c1_N_r;



$c2_MA = 60* $margen; 
$c2_A  = 45* $margen; 
$c2_M  = 30* $margen; 
$c2_B  = 15* $margen; 
$c2_N  = 0;

$c2_MA_r = 0;
$c2_A_r  = 0;
$c2_M_r  = 0;
$c2_B_r  = 0;
$c2_N_r  = 0;

do { 
         if($row_categoria2['Respuesta'] >= $c2_MA)                                         {$c2_MA_r++;}
    else if($row_categoria2['Respuesta'] >= $c2_A AND $row_categoria2['Respuesta'] < $c2_MA){$c2_A_r++;}
    else if($row_categoria2['Respuesta'] >= $c2_M AND $row_categoria2['Respuesta'] < $c2_A) {$c2_M_r++;}
    else if($row_categoria2['Respuesta'] >= $c2_B AND $row_categoria2['Respuesta'] < $c2_M) {$c2_B_r++;}
    else if($row_categoria2['Respuesta'] >= $c2_N AND $row_categoria2['Respuesta'] < $c2_B) {$c2_N_r++;}
} while ($row_categoria2 = mysql_fetch_assoc($categoria2));

$c2_T_r = $c2_MA_r + $c2_A_r + $c2_M_r + $c2_B_r + $c2_N_r;


$c3_MA = 13* $margen; 
$c3_A  = 10* $margen; 
$c3_M  = 7* $margen; 
$c3_B  = 5* $margen; 
$c3_N  = 0;

$c3_MA_r = 0;
$c3_A_r  = 0;
$c3_M_r  = 0;
$c3_B_r  = 0;
$c3_N_r  = 0;

do { 
         if($row_categoria3['Respuesta'] >= $c3_MA)                                         {$c3_MA_r++;}
    else if($row_categoria3['Respuesta'] >= $c3_A AND $row_categoria3['Respuesta'] < $c3_MA){$c3_A_r++;}
    else if($row_categoria3['Respuesta'] >= $c3_M AND $row_categoria3['Respuesta'] < $c3_A) {$c3_M_r++;}
    else if($row_categoria3['Respuesta'] >= $c3_B AND $row_categoria3['Respuesta'] < $c3_M) {$c3_B_r++;}
    else if($row_categoria3['Respuesta'] >= $c3_N AND $row_categoria3['Respuesta'] < $c3_B) {$c3_N_r++;}
} while ($row_categoria3 = mysql_fetch_assoc($categoria3));

$c3_T_r = $c3_MA_r + $c3_A_r + $c3_M_r + $c3_B_r + $c3_N_r;


$c4_MA = 58* $margen; 
$c4_A  = 42* $margen; 
$c4_M  = 29* $margen; 
$c4_B  = 14* $margen; 
$c4_N  = 0;

$c4_MA_r = 0;
$c4_A_r  = 0;
$c4_M_r  = 0;
$c4_B_r  = 0;
$c4_N_r  = 0;

do { 
         if($row_categoria4['Respuesta'] >= $c4_MA)                                         {$c4_MA_r++;}
    else if($row_categoria4['Respuesta'] >= $c4_A AND $row_categoria4['Respuesta'] < $c4_MA){$c4_A_r++;}
    else if($row_categoria4['Respuesta'] >= $c4_M AND $row_categoria4['Respuesta'] < $c4_A) {$c4_M_r++;}
    else if($row_categoria4['Respuesta'] >= $c4_B AND $row_categoria4['Respuesta'] < $c4_M) {$c4_B_r++;}
    else if($row_categoria4['Respuesta'] >= $c4_N AND $row_categoria4['Respuesta'] < $c4_B) {$c4_N_r++;}
} while ($row_categoria4 = mysql_fetch_assoc($categoria4));

$c4_T_r = $c4_MA_r + $c4_A_r + $c4_M_r + $c4_B_r + $c4_N_r;


$c5_MA = 23* $margen; 
$c5_A  = 18* $margen; 
$c5_M  = 14* $margen; 
$c5_B  = 10* $margen; 
$c5_N  = 0;

$c5_MA_r = 0;
$c5_A_r  = 0;
$c5_M_r  = 0;
$c5_B_r  = 0;
$c5_N_r  = 0;

do { 
         if($row_categoria5['Respuesta'] >= $c5_MA)                                         {$c5_MA_r++;}
    else if($row_categoria5['Respuesta'] >= $c5_A AND $row_categoria5['Respuesta'] < $c5_MA){$c5_A_r++;}
    else if($row_categoria5['Respuesta'] >= $c5_M AND $row_categoria5['Respuesta'] < $c5_A) {$c5_M_r++;}
    else if($row_categoria5['Respuesta'] >= $c5_B AND $row_categoria5['Respuesta'] < $c5_M) {$c5_B_r++;}
    else if($row_categoria5['Respuesta'] >= $c5_N AND $row_categoria2['Respuesta'] < $c5_B) {$c5_N_r++;}
} while ($row_categoria5 = mysql_fetch_assoc($categoria5));

$c5_T_r = $c5_MA_r + $c5_A_r + $c5_M_r + $c5_B_r + $c5_N_r;


$d1_MA = 14* $margen; 
$d1_A  = 11* $margen; 
$d1_M  = 9* $margen; 
$d1_B  = 5* $margen; 
$d1_N  = 0;

$d2_MA = 37* $margen; 
$d2_A  = 27* $margen; 
$d2_M  = 21* $margen; 
$d2_B  = 15* $margen; 
$d2_N  = 0;

$d3_MA = 25* $margen; 
$d3_A  = 21* $margen; 
$d3_M  = 16* $margen; 
$d3_B  = 11* $margen; 
$d3_N  = 0;

$d4_MA = 6* $margen; 
$d4_A  = 4* $margen; 
$d4_M  = 2* $margen; 
$d4_B  = 1* $margen; 
$d4_N  = 0;

$d5_MA = 10* $margen; 
$d5_A  = 8* $margen; 
$d5_M  = 6* $margen; 
$d5_B  = 4* $margen; 
$d5_N  = 0;

$d6_MA = 20* $margen; 
$d6_A  = 16* $margen; 
$d6_M  = 12* $margen; 
$d6_B  = 9* $margen; 
$d6_N  = 0;

$d7_MA = 21* $margen; 
$d7_A  = 17* $margen; 
$d7_M  = 13* $margen; 
$d7_B  = 10* $margen; 
$d7_N  = 0;

$d8_MA = 16* $margen; 
$d8_A  = 13* $margen; 
$d8_M  = 10* $margen; 
$d8_B  = 7* $margen; 
$d8_N  = 0;

$d9_MA = 18* $margen; 
$d9_A  = 14* $margen; 
$d9_M  = 10* $margen; 
$d9_B  = 6* $margen; 
$d9_N  = 0;

$d10_MA = 10* $margen; 
$d10_A  = 8* $margen; 
$d10_M  = 6* $margen; 
$d10_B  = 4* $margen; 
$d10_N  = 0;





$d1_MA_r = 0;
$d1_A_r  = 0;
$d1_M_r  = 0;
$d1_B_r  = 0;
$d1_N_r  = 0;

$d2_MA_r = 0;
$d2_A_r  = 0;
$d2_M_r  = 0;
$d2_B_r  = 0;
$d2_N_r  = 0;

$d3_MA_r = 0;
$d3_A_r  = 0;
$d3_M_r  = 0;
$d3_B_r  = 0;
$d3_N_r  = 0;

$d4_MA_r = 0;
$d4_A_r  = 0;
$d4_M_r  = 0;
$d4_B_r  = 0;
$d4_N_r  = 0;

$d5_MA_r = 0;
$d5_A_r  = 0;
$d5_M_r  = 0;
$d5_B_r  = 0;
$d5_N_r  = 0;

$d6_MA_r = 0;
$d6_A_r  = 0;
$d6_M_r  = 0;
$d6_B_r  = 0;
$d6_N_r  = 0;

$d7_MA_r = 0;
$d7_A_r  = 0;
$d7_M_r  = 0;
$d7_B_r  = 0;
$d7_N_r  = 0;

$d8_MA_r = 0;
$d8_A_r  = 0;
$d8_M_r  = 0;
$d8_B_r  = 0;
$d8_N_r  = 0;

$d9_MA_r = 0;
$d9_A_r  = 0;
$d9_M_r  = 0;
$d9_B_r  = 0;
$d9_N_r  = 0;

$d10_MA_r = 0;
$d10_A_r  = 0;
$d10_M_r  = 0;
$d10_B_r  = 0;
$d10_N_r  = 0;



do { 
     if($row_dominio1['Respuesta'] >= $d1_MA)                                       {$d1_MA_r++;}
else if($row_dominio1['Respuesta'] >= $d1_A AND $row_dominio1['Respuesta'] < $d1_MA){$d1_A_r++;}
else if($row_dominio1['Respuesta'] >= $d1_M AND $row_dominio1['Respuesta'] < $d1_A) {$d1_M_r++;}
else if($row_dominio1['Respuesta'] >= $d1_B AND $row_dominio1['Respuesta'] < $d1_M) {$d1_B_r++;}
else if($row_dominio1['Respuesta'] >= $d1_N AND $row_dominio1['Respuesta'] < $d1_B) {$d1_N_r++;}
} while ($row_dominio1 = mysql_fetch_assoc($dominio1));

do { 
    if($row_dominio2['Respuesta'] >= $d2_MA)                                       {$d2_MA_r++;}
else if($row_dominio2['Respuesta'] >= $d2_A AND $row_dominio2['Respuesta'] < $d2_MA){$d2_A_r++;}
else if($row_dominio2['Respuesta'] >= $d2_M AND $row_dominio2['Respuesta'] < $d2_A) {$d2_M_r++;}
else if($row_dominio2['Respuesta'] >= $d2_B AND $row_dominio2['Respuesta'] < $d2_M) {$d2_B_r++;}
else if($row_dominio2['Respuesta'] >= $d2_N AND $row_dominio2['Respuesta'] < $d2_B) {$d2_N_r++;}
} while ($row_dominio2 = mysql_fetch_assoc($dominio2));

do { 
    if($row_dominio3['Respuesta'] >= $d3_MA)                                       {$d3_MA_r++;}
else if($row_dominio3['Respuesta'] >= $d3_A AND $row_dominio3['Respuesta'] < $d3_MA){$d3_A_r++;}
else if($row_dominio3['Respuesta'] >= $d3_M AND $row_dominio3['Respuesta'] < $d3_A) {$d3_M_r++;}
else if($row_dominio3['Respuesta'] >= $d3_B AND $row_dominio3['Respuesta'] < $d3_M) {$d3_B_r++;}
else if($row_dominio3['Respuesta'] >= $d3_N AND $row_dominio3['Respuesta'] < $d3_B) {$d3_N_r++;}
} while ($row_dominio3 = mysql_fetch_assoc($dominio3));

do { 
    if($row_dominio4['Respuesta'] >= $d4_MA)                                       {$d4_MA_r++;}
else if($row_dominio4['Respuesta'] >= $d4_A AND $row_dominio4['Respuesta'] < $d4_MA){$d4_A_r++;}
else if($row_dominio4['Respuesta'] >= $d4_M AND $row_dominio4['Respuesta'] < $d4_A) {$d4_M_r++;}
else if($row_dominio4['Respuesta'] >= $d4_B AND $row_dominio4['Respuesta'] < $d4_M) {$d4_B_r++;}
else if($row_dominio4['Respuesta'] >= $d4_N AND $row_dominio4['Respuesta'] < $d4_B) {$d4_N_r++;}
} while ($row_dominio4 = mysql_fetch_assoc($dominio4));

do { 
    if($row_dominio5['Respuesta'] >= $d5_MA)                                       {$d5_MA_r++;}
else if($row_dominio5['Respuesta'] >= $d5_A AND $row_dominio5['Respuesta'] < $d5_MA){$d5_A_r++;}
else if($row_dominio5['Respuesta'] >= $d5_M AND $row_dominio5['Respuesta'] < $d5_A) {$d5_M_r++;}
else if($row_dominio5['Respuesta'] >= $d5_B AND $row_dominio5['Respuesta'] < $d5_M) {$d5_B_r++;}
else if($row_dominio5['Respuesta'] >= $d5_N AND $row_dominio5['Respuesta'] < $d5_B) {$d5_N_r++;}
} while ($row_dominio5 = mysql_fetch_assoc($dominio5));

do { 
    if($row_dominio6['Respuesta'] >= $d6_MA)                                       {$d6_MA_r++;}
else if($row_dominio6['Respuesta'] >= $d6_A AND $row_dominio6['Respuesta'] < $d6_MA){$d6_A_r++;}
else if($row_dominio6['Respuesta'] >= $d6_M AND $row_dominio6['Respuesta'] < $d6_A) {$d6_M_r++;}
else if($row_dominio6['Respuesta'] >= $d6_B AND $row_dominio6['Respuesta'] < $d6_M) {$d6_B_r++;}
else if($row_dominio6['Respuesta'] >= $d6_N AND $row_dominio6['Respuesta'] < $d6_B) {$d6_N_r++;}
} while ($row_dominio6 = mysql_fetch_assoc($dominio6));

do { 
    if($row_dominio7['Respuesta'] >= $d7_MA)                                       {$d7_MA_r++;}
else if($row_dominio7['Respuesta'] >= $d7_A AND $row_dominio7['Respuesta'] < $d7_MA){$d7_A_r++;}
else if($row_dominio7['Respuesta'] >= $d7_M AND $row_dominio7['Respuesta'] < $d7_A) {$d7_M_r++;}
else if($row_dominio7['Respuesta'] >= $d7_B AND $row_dominio7['Respuesta'] < $d7_M) {$d7_B_r++;}
else if($row_dominio7['Respuesta'] >= $d7_N AND $row_dominio7['Respuesta'] < $d7_B) {$d7_N_r++;}
} while ($row_dominio7 = mysql_fetch_assoc($dominio7));

do { 
    if($row_dominio8['Respuesta'] >= $d8_MA)                                       {$d8_MA_r++;}
else if($row_dominio8['Respuesta'] >= $d8_A AND $row_dominio8['Respuesta'] < $d8_MA){$d8_A_r++;}
else if($row_dominio8['Respuesta'] >= $d8_M AND $row_dominio8['Respuesta'] < $d8_A) {$d8_M_r++;}
else if($row_dominio8['Respuesta'] >= $d8_B AND $row_dominio8['Respuesta'] < $d8_M) {$d8_B_r++;}
else if($row_dominio8['Respuesta'] >= $d8_N AND $row_dominio8['Respuesta'] < $d8_B) {$d8_N_r++;}
} while ($row_dominio8 = mysql_fetch_assoc($dominio8));

do { 
    if($row_dominio9['Respuesta'] >= $d9_MA)                                       {$d9_MA_r++;}
else if($row_dominio9['Respuesta'] >= $d9_A AND $row_dominio9['Respuesta'] < $d9_MA){$d9_A_r++;}
else if($row_dominio9['Respuesta'] >= $d9_M AND $row_dominio9['Respuesta'] < $d9_A) {$d9_M_r++;}
else if($row_dominio9['Respuesta'] >= $d9_B AND $row_dominio9['Respuesta'] < $d9_M) {$d9_B_r++;}
else if($row_dominio9['Respuesta'] >= $d9_N AND $row_dominio9['Respuesta'] < $d9_B) {$d9_N_r++;}
} while ($row_dominio9 = mysql_fetch_assoc($dominio9));

do { 
    if($row_dominio10['Respuesta'] >= $d10_MA)                                       {$d10_MA_r++;}
else if($row_dominio10['Respuesta'] >= $d10_A AND $row_dominio10['Respuesta'] < $d10_MA){$d10_A_r++;}
else if($row_dominio10['Respuesta'] >= $d10_M AND $row_dominio10['Respuesta'] < $d10_A) {$d10_M_r++;}
else if($row_dominio10['Respuesta'] >= $d10_B AND $row_dominio10['Respuesta'] < $d10_M) {$d10_B_r++;}
else if($row_dominio10['Respuesta'] >= $d10_N AND $row_dominio10['Respuesta'] < $d10_B) {$d10_N_r++;}
} while ($row_dominio10 = mysql_fetch_assoc($dominio10));




$d1_T_r = $d1_MA_r + $d1_A_r + $d1_M_r + $d1_B_r + $d1_N_r;
$d2_T_r = $d2_MA_r + $d2_A_r + $d2_M_r + $d2_B_r + $d2_N_r;
$d3_T_r = $d3_MA_r + $d3_A_r + $d3_M_r + $d3_B_r + $d3_N_r;
$d4_T_r = $d4_MA_r + $d4_A_r + $d4_M_r + $d4_B_r + $d4_N_r;
$d5_T_r = $d5_MA_r + $d5_A_r + $d5_M_r + $d5_B_r + $d5_N_r;
$d6_T_r = $d6_MA_r + $d6_A_r + $d6_M_r + $d6_B_r + $d6_N_r;
$d7_T_r = $d7_MA_r + $d7_A_r + $d7_M_r + $d7_B_r + $d7_N_r;
$d8_T_r = $d8_MA_r + $d8_A_r + $d8_M_r + $d8_B_r + $d8_N_r;
$d9_T_r = $d9_MA_r + $d9_A_r + $d9_M_r + $d9_B_r + $d9_N_r;
$d10_T_r = $d10_MA_r + $d10_A_r + $d10_M_r + $d10_B_r + $d10_N_r;


} else {

// el examen es 2

$ct_MA = 90* $margen; 
$ct_A  = 70* $margen; 
$ct_M  = 45* $margen; 
$ct_B  = 20* $margen; 
$ct_N  = 0;

$ct_MA_r = 0;
$ct_A_r  = 0;
$ct_M_r  = 0;
$ct_B_r  = 0;
$ct_N_r  = 0;
$promedio = 0;

do { 
         if($row_total_c['Respuesta'] >= $ct_MA)                                       {$ct_MA_r++;}
    else if($row_total_c['Respuesta'] >= $ct_A AND $row_total_c['Respuesta'] < $ct_MA) {$ct_A_r++;}
    else if($row_total_c['Respuesta'] >= $ct_M AND $row_total_c['Respuesta'] < $ct_A)  {$ct_M_r++;}
    else if($row_total_c['Respuesta'] >= $ct_B AND $row_total_c['Respuesta'] < $ct_M)  {$ct_B_r++;}
    else if($row_total_c['Respuesta'] >= $ct_N AND $row_total_c['Respuesta'] < $ct_B)  {$ct_N_r++;}
    $promedio = $promedio + $row_total_c['Respuesta'];
} while ($row_total_c = mysql_fetch_assoc($total_c));

$ct_T_r = $ct_MA_r + $ct_A_r + $ct_M_r + $ct_B_r + $ct_N_r;
$promedio = $promedio / $totalRows_total_c;

$ct_porcentaje1 = round(($ct_MA_r / $ct_T_r) * 100, 0);
$ct_porcentaje2 = round(($ct_A_r / $ct_T_r) * 100, 0);
$ct_porcentaje3 = round(($ct_M_r / $ct_T_r) * 100, 0);
$ct_porcentaje4 = round(($ct_B_r / $ct_T_r) * 100, 0);
$ct_porcentaje5 = round(($ct_N_r / $ct_T_r) * 100, 0);
$ct_porcentajeT = 100;


//nivel de riesgo mas recurrente

if(($ct_MA_r >= $ct_A_r)  AND ($ct_MA_r >= $ct_M_r) AND ($ct_MA_r >= $ct_B_r) AND ($ct_MA_r >= $ct_N_r)){ $val_max = "Muy Alto"; $IDnivel = 5; }
if(($ct_A_r  >= $ct_MA_r) AND ($ct_A_r  >= $ct_M_r) AND ($ct_A_r  >= $ct_B_r) AND ($ct_A_r  >= $ct_N_r)){ $val_max = "Alto";     $IDnivel = 4; }
if(($ct_M_r  >= $ct_MA_r) AND ($ct_M_r  >= $ct_A_r) AND ($ct_M_r  >= $ct_B_r) AND ($ct_M_r  >= $ct_N_r)){ $val_max = "Medio";    $IDnivel = 3; }
if(($ct_B_r  >= $ct_MA_r) AND ($ct_B_r  >= $ct_A_r) AND ($ct_B_r  >= $ct_M_r) AND ($ct_B_r  >= $ct_N_r)){ $val_max = "Bajo";     $IDnivel = 2; }
if(($ct_N_r  >= $ct_MA_r) AND ($ct_N_r  >= $ct_A_r) AND ($ct_N_r  >= $ct_M_r) AND ($ct_N_r  >= $ct_B_r)){ $val_max = "NUlo";     $IDnivel = 1; }


mysql_select_db($database_vacantes, $vacantes);
$query_nivelesr = "SELECT * FROM nom35_niveles_riesgo WHERE IDnivel = $IDnivel";
$nivelesr = mysql_query($query_nivelesr, $vacantes) or die(mysql_error());
$row_nivelesr = mysql_fetch_assoc($nivelesr);
$totalRows_nivelesr = mysql_num_rows($nivelesr);
$interpretacion = $row_nivelesr['texto'];

$c1_MA = 9* $margen; 
$c1_A  = 7* $margen; 
$c1_M  = 5* $margen; 
$c1_B  = 3* $margen; 
$c1_N  = 0; 

$c1_MA_r = 0;
$c1_A_r  = 0;
$c1_M_r  = 0;
$c1_B_r  = 0;
$c1_N_r  = 0;

do { 
         if($row_categoria1['Respuesta'] >= $c1_MA)                                         {$c1_MA_r++;}
    else if($row_categoria1['Respuesta'] >= $c1_A AND $row_categoria1['Respuesta'] < $c1_MA){$c1_A_r++;}
    else if($row_categoria1['Respuesta'] >= $c1_M AND $row_categoria1['Respuesta'] < $c1_A) {$c1_M_r++;}
    else if($row_categoria1['Respuesta'] >= $c1_B AND $row_categoria1['Respuesta'] < $c1_M) {$c1_B_r++;}
    else if($row_categoria1['Respuesta'] >= $c1_N AND $row_categoria1['Respuesta'] < $c1_B) {$c1_N_r++;}
} while ($row_categoria1 = mysql_fetch_assoc($categoria1));

$c1_T_r = $c1_MA_r + $c1_A_r + $c1_M_r + $c1_B_r + $c1_N_r;



$c2_MA = 40* $margen; 
$c2_A  = 30* $margen; 
$c2_M  = 20* $margen; 
$c2_B  = 10* $margen; 
$c2_N  = 0;

$c2_MA_r = 0;
$c2_A_r  = 0;
$c2_M_r  = 0;
$c2_B_r  = 0;
$c2_N_r  = 0;

do { 
         if($row_categoria2['Respuesta'] >= $c2_MA)                                         {$c2_MA_r++;}
    else if($row_categoria2['Respuesta'] >= $c2_A AND $row_categoria2['Respuesta'] < $c2_MA){$c2_A_r++;}
    else if($row_categoria2['Respuesta'] >= $c2_M AND $row_categoria2['Respuesta'] < $c2_A) {$c2_M_r++;}
    else if($row_categoria2['Respuesta'] >= $c2_B AND $row_categoria2['Respuesta'] < $c2_M) {$c2_B_r++;}
    else if($row_categoria2['Respuesta'] >= $c2_N AND $row_categoria2['Respuesta'] < $c2_B) {$c2_N_r++;}
} while ($row_categoria2 = mysql_fetch_assoc($categoria2));

$c2_T_r = $c2_MA_r + $c2_A_r + $c2_M_r + $c2_B_r + $c2_N_r;


$c3_MA = 12 * $margen; 
$c3_A  = 9* $margen; 
$c3_M  = 6* $margen; 
$c3_B  = 4* $margen; 
$c3_N  = 0;

$c3_MA_r = 0;
$c3_A_r  = 0;
$c3_M_r  = 0;
$c3_B_r  = 0;
$c3_N_r  = 0;

do { 
         if($row_categoria3['Respuesta'] >= $c3_MA)                                         {$c3_MA_r++;}
    else if($row_categoria3['Respuesta'] >= $c3_A AND $row_categoria3['Respuesta'] < $c3_MA){$c3_A_r++;}
    else if($row_categoria3['Respuesta'] >= $c3_M AND $row_categoria3['Respuesta'] < $c3_A) {$c3_M_r++;}
    else if($row_categoria3['Respuesta'] >= $c3_B AND $row_categoria3['Respuesta'] < $c3_M) {$c3_B_r++;}
    else if($row_categoria3['Respuesta'] >= $c3_N AND $row_categoria3['Respuesta'] < $c3_B) {$c3_N_r++;}
} while ($row_categoria3 = mysql_fetch_assoc($categoria3));

$c3_T_r = $c3_MA_r + $c3_A_r + $c3_M_r + $c3_B_r + $c3_N_r;


$c4_MA = 38* $margen; 
$c4_A  = 28* $margen; 
$c4_M  = 18* $margen; 
$c4_B  = 10* $margen; 
$c4_N  = 0;

$c4_MA_r = 0;
$c4_A_r  = 0;
$c4_M_r  = 0;
$c4_B_r  = 0;
$c4_N_r  = 0;

do { 
         if($row_categoria4['Respuesta'] >= $c4_MA)                                         {$c4_MA_r++;}
    else if($row_categoria4['Respuesta'] >= $c4_A AND $row_categoria4['Respuesta'] < $c4_MA){$c4_A_r++;}
    else if($row_categoria4['Respuesta'] >= $c4_M AND $row_categoria4['Respuesta'] < $c4_A) {$c4_M_r++;}
    else if($row_categoria4['Respuesta'] >= $c4_B AND $row_categoria4['Respuesta'] < $c4_M) {$c4_B_r++;}
    else if($row_categoria4['Respuesta'] >= $c4_N AND $row_categoria4['Respuesta'] < $c4_B) {$c4_N_r++;}
} while ($row_categoria4 = mysql_fetch_assoc($categoria4));

$c4_T_r = $c4_MA_r + $c4_A_r + $c4_M_r + $c4_B_r + $c4_N_r;


$d1_MA = 9* $margen; 
$d1_A  = 7* $margen; 
$d1_M  = 5* $margen; 
$d1_B  = 3* $margen; 
$d1_N  = 0;

$d2_MA = 24* $margen; 
$d2_A  = 20* $margen; 
$d2_M  = 16* $margen; 
$d2_B  = 12* $margen; 
$d2_N  = 0;

$d3_MA = 14* $margen; 
$d3_A  = 11* $margen; 
$d3_M  = 8* $margen; 
$d3_B  = 5* $margen; 
$d3_N  = 0;

$d4_MA = 6* $margen; 
$d4_A  = 4* $margen; 
$d4_M  = 2* $margen; 
$d4_B  = 1* $margen; 
$d4_N  = 0;

$d5_MA = 6* $margen; 
$d5_A  = 4* $margen; 
$d5_M  = 2* $margen; 
$d5_B  = 1* $margen; 
$d5_N  = 0;

$d6_MA = 11* $margen; 
$d6_A  = 8* $margen; 
$d6_M  = 5* $margen; 
$d6_B  = 3* $margen; 
$d6_N  = 0;

$d7_MA = 14* $margen; 
$d7_A  = 11* $margen; 
$d7_M  = 8* $margen; 
$d7_B  = 5* $margen; 
$d7_N  = 0;

$d8_MA = 16* $margen; 
$d8_A  = 13* $margen; 
$d8_M  = 10* $margen; 
$d8_B  = 7* $margen; 
$d8_N  = 0;



$d1_MA_r = 0;
$d1_A_r  = 0;
$d1_M_r  = 0;
$d1_B_r  = 0;
$d1_N_r  = 0;

$d2_MA_r = 0;
$d2_A_r  = 0;
$d2_M_r  = 0;
$d2_B_r  = 0;
$d2_N_r  = 0;

$d3_MA_r = 0;
$d3_A_r  = 0;
$d3_M_r  = 0;
$d3_B_r  = 0;
$d3_N_r  = 0;

$d4_MA_r = 0;
$d4_A_r  = 0;
$d4_M_r  = 0;
$d4_B_r  = 0;
$d4_N_r  = 0;

$d5_MA_r = 0;
$d5_A_r  = 0;
$d5_M_r  = 0;
$d5_B_r  = 0;
$d5_N_r  = 0;

$d6_MA_r = 0;
$d6_A_r  = 0;
$d6_M_r  = 0;
$d6_B_r  = 0;
$d6_N_r  = 0;

$d7_MA_r = 0;
$d7_A_r  = 0;
$d7_M_r  = 0;
$d7_B_r  = 0;
$d7_N_r  = 0;

$d8_MA_r = 0;
$d8_A_r  = 0;
$d8_M_r  = 0;
$d8_B_r  = 0;
$d8_N_r  = 0;


do { 
     if($row_dominio1['Respuesta'] >= $d1_MA)                                       {$d1_MA_r++;}
else if($row_dominio1['Respuesta'] >= $d1_A AND $row_dominio1['Respuesta'] < $d1_MA){$d1_A_r++;}
else if($row_dominio1['Respuesta'] >= $d1_M AND $row_dominio1['Respuesta'] < $d1_A) {$d1_M_r++;}
else if($row_dominio1['Respuesta'] >= $d1_B AND $row_dominio1['Respuesta'] < $d1_M) {$d1_B_r++;}
else if($row_dominio1['Respuesta'] >= $d1_N AND $row_dominio1['Respuesta'] < $d1_B) {$d1_N_r++;}
} while ($row_dominio1 = mysql_fetch_assoc($dominio1));

do { 
    if($row_dominio2['Respuesta'] >= $d2_MA)                                       {$d2_MA_r++;}
else if($row_dominio2['Respuesta'] >= $d2_A AND $row_dominio2['Respuesta'] < $d2_MA){$d2_A_r++;}
else if($row_dominio2['Respuesta'] >= $d2_M AND $row_dominio2['Respuesta'] < $d2_A) {$d2_M_r++;}
else if($row_dominio2['Respuesta'] >= $d2_B AND $row_dominio2['Respuesta'] < $d2_M) {$d2_B_r++;}
else if($row_dominio2['Respuesta'] >= $d2_N AND $row_dominio2['Respuesta'] < $d2_B) {$d2_N_r++;}
} while ($row_dominio2 = mysql_fetch_assoc($dominio2));

do { 
    if($row_dominio3['Respuesta'] >= $d3_MA)                                       {$d3_MA_r++;}
else if($row_dominio3['Respuesta'] >= $d3_A AND $row_dominio3['Respuesta'] < $d3_MA){$d3_A_r++;}
else if($row_dominio3['Respuesta'] >= $d3_M AND $row_dominio3['Respuesta'] < $d3_A) {$d3_M_r++;}
else if($row_dominio3['Respuesta'] >= $d3_B AND $row_dominio3['Respuesta'] < $d3_M) {$d3_B_r++;}
else if($row_dominio3['Respuesta'] >= $d3_N AND $row_dominio3['Respuesta'] < $d3_B) {$d3_N_r++;}
} while ($row_dominio3 = mysql_fetch_assoc($dominio3));

do { 
    if($row_dominio4['Respuesta'] >= $d4_MA)                                       {$d4_MA_r++;}
else if($row_dominio4['Respuesta'] >= $d4_A AND $row_dominio4['Respuesta'] < $d4_MA){$d4_A_r++;}
else if($row_dominio4['Respuesta'] >= $d4_M AND $row_dominio4['Respuesta'] < $d4_A) {$d4_M_r++;}
else if($row_dominio4['Respuesta'] >= $d4_B AND $row_dominio4['Respuesta'] < $d4_M) {$d4_B_r++;}
else if($row_dominio4['Respuesta'] >= $d4_N AND $row_dominio4['Respuesta'] < $d4_B) {$d4_N_r++;}
} while ($row_dominio4 = mysql_fetch_assoc($dominio4));

do { 
    if($row_dominio5['Respuesta'] >= $d5_MA)                                       {$d5_MA_r++;}
else if($row_dominio5['Respuesta'] >= $d5_A AND $row_dominio5['Respuesta'] < $d5_MA){$d5_A_r++;}
else if($row_dominio5['Respuesta'] >= $d5_M AND $row_dominio5['Respuesta'] < $d5_A) {$d5_M_r++;}
else if($row_dominio5['Respuesta'] >= $d5_B AND $row_dominio5['Respuesta'] < $d5_M) {$d5_B_r++;}
else if($row_dominio5['Respuesta'] >= $d5_N AND $row_dominio5['Respuesta'] < $d5_B) {$d5_N_r++;}
} while ($row_dominio5 = mysql_fetch_assoc($dominio5));

do { 
    if($row_dominio6['Respuesta'] >= $d6_MA)                                       {$d6_MA_r++;}
else if($row_dominio6['Respuesta'] >= $d6_A AND $row_dominio6['Respuesta'] < $d6_MA){$d6_A_r++;}
else if($row_dominio6['Respuesta'] >= $d6_M AND $row_dominio6['Respuesta'] < $d6_A) {$d6_M_r++;}
else if($row_dominio6['Respuesta'] >= $d6_B AND $row_dominio6['Respuesta'] < $d6_M) {$d6_B_r++;}
else if($row_dominio6['Respuesta'] >= $d6_N AND $row_dominio6['Respuesta'] < $d6_B) {$d6_N_r++;}
} while ($row_dominio6 = mysql_fetch_assoc($dominio6));

do { 
    if($row_dominio7['Respuesta'] >= $d7_MA)                                       {$d7_MA_r++;}
else if($row_dominio7['Respuesta'] >= $d7_A AND $row_dominio7['Respuesta'] < $d7_MA){$d7_A_r++;}
else if($row_dominio7['Respuesta'] >= $d7_M AND $row_dominio7['Respuesta'] < $d7_A) {$d7_M_r++;}
else if($row_dominio7['Respuesta'] >= $d7_B AND $row_dominio7['Respuesta'] < $d7_M) {$d7_B_r++;}
else if($row_dominio7['Respuesta'] >= $d7_N AND $row_dominio7['Respuesta'] < $d7_B) {$d7_N_r++;}
} while ($row_dominio7 = mysql_fetch_assoc($dominio7));

do { 
    if($row_dominio8['Respuesta'] >= $d8_MA)                                       {$d8_MA_r++;}
else if($row_dominio8['Respuesta'] >= $d8_A AND $row_dominio8['Respuesta'] < $d8_MA){$d8_A_r++;}
else if($row_dominio8['Respuesta'] >= $d8_M AND $row_dominio8['Respuesta'] < $d8_A) {$d8_M_r++;}
else if($row_dominio8['Respuesta'] >= $d8_B AND $row_dominio8['Respuesta'] < $d8_M) {$d8_B_r++;}
else if($row_dominio8['Respuesta'] >= $d8_N AND $row_dominio8['Respuesta'] < $d8_B) {$d8_N_r++;}
} while ($row_dominio8 = mysql_fetch_assoc($dominio8));



$d1_T_r = $d1_MA_r + $d1_A_r + $d1_M_r + $d1_B_r + $d1_N_r;
$d2_T_r = $d2_MA_r + $d2_A_r + $d2_M_r + $d2_B_r + $d2_N_r;
$d3_T_r = $d3_MA_r + $d3_A_r + $d3_M_r + $d3_B_r + $d3_N_r;
$d4_T_r = $d4_MA_r + $d4_A_r + $d4_M_r + $d4_B_r + $d4_N_r;
$d5_T_r = $d5_MA_r + $d5_A_r + $d5_M_r + $d5_B_r + $d5_N_r;
$d6_T_r = $d6_MA_r + $d6_A_r + $d6_M_r + $d6_B_r + $d6_N_r;
$d7_T_r = $d7_MA_r + $d7_A_r + $d7_M_r + $d7_B_r + $d7_N_r;
$d8_T_r = $d8_MA_r + $d8_A_r + $d8_M_r + $d8_B_r + $d8_N_r;

}

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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
    <link rel="stylesheet" type="text/css" href="assets/print.css" media="print" />
	<!-- /theme JS files -->
    <style> 
    table td:first-child {  width: 40%; } 
    div.a { text-align: center; }
    tr { text-align: center; }
    th { text-align: center; }
    </style>


</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';} ?> onLoad="window.print()">
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">



			<!-- Main content -->
			<div class="content-wrapper">		

<!-- Content area -->
<div class="content">



<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title">Resultados NOM035 Sucursal</h5>
    </div>
        <div class="panel-body">
        <h5>Política de Prevención de Riesgos Psicosociales</h5>
        <p><b>Impulsora Sahuayo S.A. de C.V. </b>promueve la prevención de los factores de riesgo psicosocial; la prevención de la violencia laboral, y la promoción de un entorno organizacional favorable, la diversidad e inclusión, facilitando a los colaboradores acciones de sensibilización, programas de comunicación, capacitación y espacios de participación y consulta, quedando estrictamente prohibidos los actos de violencia laboral, represalias, abusos, discriminación por creencias, raza, sexo, religión, etnia o edad, preferencia sexual o cualquier otra condición que derive en riesgo psicosocial o acciones en contra del favorable entorno organizacional.</p>

        </div>
</div>
            
                
    <div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title">Cuestionario de Acontecimientos Traumáticos Severos</h5>
    </div>

        <div class="panel-body">

        <legend class="text-bold">Datos para el cuestionario</legend>
        <ul>
            <li><b>Sucursal:</b> <?php echo $row_matriz['matriz'];?>.</li>
            <li><b>Periodo:</b> <?php echo $row_periodo['periodo'];?>.</li>
            <li><b>No. de Empleados activos:</b>  <?php echo $totalRows_resultadosa1;?></li>
        </ul>


        <legend class="text-bold">Resultados Generales</legend>

        <table class="table table-bordered table-condensed">
            <thead>
            <tr>
                    <th>Nivel de atención</th>
                    <th><span class="text text-success">No requiere valoración ni atención clínica</span></th>
                    <th><span class="text text-danger">Requiere valoración y atención clínica</span></th>
                    <th>Total de empleados evaluados</th>
                </tr>
            </thead>
            <tbody class="border border-neutral">
                <tr>
                    <td class="font-semibold">Número de trabajadores</td>
                    <td><?php echo $totalRows_respuestas1c; ?></td>
                    <td><?php echo $totalRows_respuestas1b; ?></td>
                    <td><?php echo $totalRows_respuestas1c + $totalRows_respuestas1b; ?></td>
                </tr>
                <tr >
                    <td class="font-semibold">Porcentaje</td>
                    <td><?php echo $n1_porcentaje1; ?>%</td>
                    <td><?php echo $n1_porcentaje2; ?>%</td>
                    <td><?php echo $n1_porcentaje3; ?>%</td>
                </tr>
            </tbody>
        </table>
        </div>
        </div>
            
            
<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title">Cuestionario de factores de Riesgo Psicosocial <?php if ($IDexmen == 3) { echo "y Entorno organizacional"; }?></h5>
    </div>

        <div class="panel-body">

        <legend class="text-bold">Datos para el cuestionario</legend>
        <ul>
            <li><b>Sucursal:</b> <?php echo $row_matriz['matriz'];?>.</li>
            <li><b>Periodo:</b> <?php echo $row_periodo['periodo'];?>.</li>
            <li><b>No. de Empleados activos:</b>  <?php echo $totalRows_resultadosa3;?></li>
            <li><b>Muestra según NOM35: </b><?php  If ($IDexmen == 2) {echo "Se debe aplicar a todos los empleados."; $muestra = 1;} else {  $muestra = round((0.9604 * $totalRows_resultadosa3) / ( 0.0025 * ($totalRows_resultadosa3 - 1) + 0.9604), 0 , PHP_ROUND_HALF_UP); echo $muestra;  } ?>
            </li>
            <li><b>Empleados evaluados:</b> <?php echo $totalRows_resultados3;?> (<?php echo round($totalRows_resultados3/$muestra*100,1);?>%)</li>
            <li><b>Nivel de riesgo: </b><?php echo $val_max; ?></li>
            <li><b>Calificación Promedio: </b><?php echo round($promedio,0); ?></li>
        </ul>


        <p>&nbsp;</p>
        <legend class="text-bold">Interpretación</legend>


        <p><b><?php echo $val_max; ?></b>: <?php echo $interpretacion;?></p>

        <p>&nbsp;</p>
        <legend class="text-bold">Resultados Generales</legend>

            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th></th>
                        <th><span class="text text-danger">Muy Alto</span></th>
                        <th><span class="text text-warning">Alto</span></th>
                        <th><span class="text text-warning">Medio</span></th>
                        <th><span class="text text-success">Bajo</span></th>
                        <th><span class="text text-success">Nulo</span></th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody class="border border-neutral">
                    <tr>
                        <td class="font-semibold">Número de trabajadores</td>
                        <td><?php echo $ct_MA_r; ?></td>
                        <td><?php echo $ct_A_r; ?></td>
                        <td><?php echo $ct_M_r; ?></td>
                        <td><?php echo $ct_B_r; ?></td>
                        <td><?php echo $ct_N_r; ?></td>
                        <td><?php echo $ct_T_r; ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold">Porcentaje</td>
                        <td><?php echo $ct_porcentaje1; ?>%</td>
                        <td><?php echo $ct_porcentaje2; ?>%</td>
                        <td><?php echo $ct_porcentaje3; ?>%</td>
                        <td><?php echo $ct_porcentaje4; ?>%</td>
                        <td><?php echo $ct_porcentaje5; ?>%</td>
                        <td><?php echo $ct_porcentajeT; ?>%</td>
                    </tr>
                </tbody>
            </table>



            <p>&nbsp;</p>
            <legend class="text-bold">Resultados por categoria y dominios</legend>

            <p>&nbsp;</p>
            <p class="text text-semibold text-info">Ambiente de trabajo</p>

            <table class="table table-bordered table-condensed">
                <thead>
                <tr style="text-align: center;">
                        <th></th>
                        <th><span class="text text-danger">Muy Alto</span></th>
                        <th><span class="text text-warning">Alto</span></th>
                        <th><span class="text text-warning">Medio</span></th>
                        <th><span class="text text-success">Bajo</span></th>
                        <th><span class="text text-success">Nulo</span></th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody class="border border-neutral">
                <tr style="text-align: center;">
                        <td class="font-semibold">Total Categoria</td>
                        <td><?php echo $c1_MA_r; ?></td>
                        <td><?php echo $c1_A_r; ?></td>
                        <td><?php echo $c1_M_r; ?></td>
                        <td><?php echo $c1_B_r; ?></td>
                        <td><?php echo $c1_N_r; ?></td>
                        <td><?php echo $c1_T_r; ?></td>
                    </tr>
                    <tr style="text-align: center;">
                        <td class="font-semibold">Condiciones en el ambiente de trabajo</td>
                        <td><?php echo $d1_MA_r; ?></td>
                        <td><?php echo $d1_A_r; ?></td>
                        <td><?php echo $d1_M_r; ?></td>
                        <td><?php echo $d1_B_r; ?></td>
                        <td><?php echo $d1_N_r; ?></td>
                        <td><?php echo $d1_T_r; ?></td>
                    </tr>
                </tbody>
            </table>

            <p>&nbsp;</p>
            <p class="text text-semibold text-info">Factores propios de la actividad</p>
        


            <table class="table table-bordered table-condensed">
                <thead>
                <tr width="50%">
                        <th></th>
                        <th><span class="text text-danger">Muy Alto</span></th>
                        <th><span class="text text-warning">Alto</span></th>
                        <th><span class="text text-warning">Medio</span></th>
                        <th><span class="text text-success">Bajo</span></th>
                        <th><span class="text text-success">Nulo</span></th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody class="border border-neutral">
                <tr width="50%">
                        <td class="font-semibold">Total Categoria</td>
                        <td><?php echo $c2_MA_r; ?></td>
                        <td><?php echo $c2_A_r; ?></td>
                        <td><?php echo $c2_M_r; ?></td>
                        <td><?php echo $c2_B_r; ?></td>
                        <td><?php echo $c2_N_r; ?></td>
                        <td><?php echo $c2_T_r; ?></td>
                    </tr>
                    <tr width="50%">
                        <td class="font-semibold">Carga de trabajo</td>
                        <td><?php echo $d2_MA_r; ?></td>
                        <td><?php echo $d2_A_r; ?></td>
                        <td><?php echo $d2_M_r; ?></td>
                        <td><?php echo $d2_B_r; ?></td>
                        <td><?php echo $d2_N_r; ?></td>
                        <td><?php echo $d2_T_r; ?></td>
                    </tr>
                    <tr width="50%">
                        <td class="font-semibold">Falta de control sobre el trabajo</td>
                        <td><?php echo $d3_MA_r; ?></td>
                        <td><?php echo $d3_A_r; ?></td>
                        <td><?php echo $d3_M_r; ?></td>
                        <td><?php echo $d3_B_r; ?></td>
                        <td><?php echo $d3_N_r; ?></td>
                        <td><?php echo $d3_T_r; ?></td>
                    </tr>
                </tbody>
            </table>

            <p>&nbsp;</p>
            <p class="text text-semibold text-info">Organización del tiempo de trabajo</p>


            <table class="table table-bordered table-condensed">
                <thead>
                <tr>
                        <th></th>
                        <th><span class="text text-danger">Muy Alto</span></th>
                        <th><span class="text text-warning">Alto</span></th>
                        <th><span class="text text-warning">Medio</span></th>
                        <th><span class="text text-success">Bajo</span></th>
                        <th><span class="text text-success">Nulo</span></th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody class="border border-neutral">
                <tr>
                        <td class="font-semibold">Total Categoria</td>
                        <td><?php echo $c3_MA_r; ?></td>
                        <td><?php echo $c3_A_r; ?></td>
                        <td><?php echo $c3_M_r; ?></td>
                        <td><?php echo $c3_B_r; ?></td>
                        <td><?php echo $c3_N_r; ?></td>
                        <td><?php echo $c3_T_r; ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold">Jornada de trabajo</td>
                        <td><?php echo $d4_MA_r; ?></td>
                        <td><?php echo $d4_A_r; ?></td>
                        <td><?php echo $d4_M_r; ?></td>
                        <td><?php echo $d4_B_r; ?></td>
                        <td><?php echo $d4_N_r; ?></td>
                        <td><?php echo $d4_T_r; ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold">Interferencia en la relación trabajo-familia</td>
                        <td><?php echo $d5_MA_r; ?></td>
                        <td><?php echo $d5_A_r; ?></td>
                        <td><?php echo $d5_M_r; ?></td>
                        <td><?php echo $d5_B_r; ?></td>
                        <td><?php echo $d5_N_r; ?></td>
                        <td><?php echo $d5_T_r; ?></td>
                    </tr>
                </tbody>
            </table>

            <p>&nbsp;</p>
            <p class="text text-semibold text-info">Liderazgo y relaciones en el trabajo</p>


            <table class="table table-bordered table-condensed">
                <thead>
                <tr>
                        <th></th>
                        <th><span class="text text-danger">Muy Alto</span></th>
                        <th><span class="text text-warning">Alto</span></th>
                        <th><span class="text text-warning">Medio</span></th>
                        <th><span class="text text-success">Bajo</span></th>
                        <th><span class="text text-success">Nulo</span></th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody class="border border-neutral">
                <tr>
                        <td class="font-semibold">Total Categoria</td>
                        <td><?php echo $c4_MA_r; ?></td>
                        <td><?php echo $c4_A_r; ?></td>
                        <td><?php echo $c4_M_r; ?></td>
                        <td><?php echo $c4_B_r; ?></td>
                        <td><?php echo $c4_N_r; ?></td>
                        <td><?php echo $c4_T_r; ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold">Liderazgo</td>
                        <td><?php echo $d6_MA_r; ?></td>
                        <td><?php echo $d6_A_r; ?></td>
                        <td><?php echo $d6_M_r; ?></td>
                        <td><?php echo $d6_B_r; ?></td>
                        <td><?php echo $d6_N_r; ?></td>
                        <td><?php echo $d6_T_r; ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold">Relaciones en el trabajo</td>
                        <td><?php echo $d7_MA_r; ?></td>
                        <td><?php echo $d7_A_r; ?></td>
                        <td><?php echo $d7_M_r; ?></td>
                        <td><?php echo $d7_B_r; ?></td>
                        <td><?php echo $d7_N_r; ?></td>
                        <td><?php echo $d7_T_r; ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold">Violencia</td>
                        <td><?php echo $d8_MA_r; ?></td>
                        <td><?php echo $d8_A_r; ?></td>
                        <td><?php echo $d8_M_r; ?></td>
                        <td><?php echo $d8_B_r; ?></td>
                        <td><?php echo $d8_N_r; ?></td>
                        <td><?php echo $d8_T_r; ?></td>
            </tr>
                </tbody>
            </table>

<?php if ($IDexmen == 3) { ?>
        <p>&nbsp;</p>
            <p class="text text-semibold text-info">Entorno organizacional</p>


            <table class="table table-bordered table-condensed">
                <thead>
                <tr>
                        <th></th>
                        <th><span class="text text-danger">Muy Alto</span></th>
                        <th><span class="text text-warning">Alto</span></th>
                        <th><span class="text text-warning">Medio</span></th>
                        <th><span class="text text-success">Bajo</span></th>
                        <th><span class="text text-success">Nulo</span></th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody class="border border-neutral">
                <tr>
                        <td class="font-semibold">Total Categoria</td>
                        <td><?php echo $c5_MA_r; ?></td>
                        <td><?php echo $c5_A_r; ?></td>
                        <td><?php echo $c5_M_r; ?></td>
                        <td><?php echo $c5_B_r; ?></td>
                        <td><?php echo $c5_N_r; ?></td>
                        <td><?php echo $c5_T_r; ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold">Reconocimiento del desempeño</td>
                        <td><?php echo $d9_MA_r; ?></td>
                        <td><?php echo $d9_A_r; ?></td>
                        <td><?php echo $d9_M_r; ?></td>
                        <td><?php echo $d9_B_r; ?></td>
                        <td><?php echo $d9_N_r; ?></td>
                        <td><?php echo $d9_T_r; ?></td>
                    </tr>
                    <tr>
                        <td class="font-semibold">Insuficiente sentido de pertenencia e, inestabilidad</td>
                        <td><?php echo $d10_MA_r; ?></td>
                        <td><?php echo $d10_A_r; ?></td>
                        <td><?php echo $d10_M_r; ?></td>
                        <td><?php echo $d10_B_r; ?></td>
                        <td><?php echo $d10_N_r; ?></td>
                        <td><?php echo $d10_T_r; ?></td>
                    </tr>
                </tbody>
            </table>

            <?php } ?>



            </div>
        </div>

                  

					<!-- /Contenido -->

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