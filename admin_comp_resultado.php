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


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];
$el_usuario = $_GET['IDempleado'];

// autoevaluacion
mysql_select_db($database_vacantes, $vacantes);
$query_evaluacion = "SELECT
sed_competencias_resultados.IDevaluacion,
sed_competencias_resultados.IDempleado,
sed_competencias_resultados.IDempleado_evaluador,
sed_competencias_resultados.anio,
sed_competencias_resultados.IDtipo,
sed_competencias_resultados.IDestatus,
sed_competencias_resultados.comp1,
sed_competencias_resultados.comp2,
sed_competencias_resultados.comp3,
sed_competencias_resultados.comp4,
sed_competencias_resultados.comp5,
sed_competencias_resultados.comp6,
sed_competencias_resultados.comp7,
Avg(sed_competencias_resultados.comp8) AS Rcomp8,
prod_activos.emp_paterno,
prod_activos.emp_materno,
prod_activos.emp_nombre,
prod_activos.denominacion,
prod_activos.IDpuesto,
prod_activos.IDmatriz,
prod_activos.IDsucursal,
prod_activos.IDarea,
vac_puestos.IDnivel_puestoC,
vac_puestos.denominacion,
vac_areas.area,
vac_areas.area,
vac_matriz.matriz 
FROM
sed_competencias_resultados
LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado
LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto
LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea
LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz 
WHERE
sed_competencias_resultados.IDempleado = $el_usuario";
$evaluacion = mysql_query($query_evaluacion, $vacantes) or die(mysql_error());
$row_evaluacion = mysql_fetch_assoc($evaluacion);
$totalRows_evaluacion = mysql_num_rows($evaluacion);
$elnivel = $row_evaluacion['IDnivel_puestoC'];

mysql_select_db($database_vacantes, $vacantes);
$query_competencias1 = "SELECT sed_competencias.IDcompetencia, sed_competencias.competencia, sed_competencias.descripcion, sed_competencias.IDtipo_competencia, sed_competencias_preguntas.IDnivel, sed_competencias_preguntas.comportamiento FROM sed_competencias LEFT JOIN sed_competencias_preguntas ON sed_competencias_preguntas.IDcompetencia = sed_competencias.IDcompetencia WHERE IDnivel = '$elnivel' AND sed_competencias.IDcompetencia= 1";
mysql_query("SET NAMES 'utf8'");
$competencias1 = mysql_query($query_competencias1, $vacantes) or die(mysql_error());
$row_competencias1 = mysql_fetch_assoc($competencias1);
$totalRows_competencias1 = mysql_num_rows($competencias1);

mysql_select_db($database_vacantes, $vacantes);
$query_competencias2 = "SELECT sed_competencias.IDcompetencia, sed_competencias.competencia, sed_competencias.descripcion, sed_competencias.IDtipo_competencia, sed_competencias_preguntas.IDnivel, sed_competencias_preguntas.comportamiento FROM sed_competencias LEFT JOIN sed_competencias_preguntas ON sed_competencias_preguntas.IDcompetencia = sed_competencias.IDcompetencia WHERE IDnivel = '$elnivel' AND sed_competencias.IDcompetencia= 2";
mysql_query("SET NAMES 'utf8'");
$competencias2 = mysql_query($query_competencias2, $vacantes) or die(mysql_error());
$row_competencias2 = mysql_fetch_assoc($competencias2);
$totalRows_competencias2 = mysql_num_rows($competencias2);

mysql_select_db($database_vacantes, $vacantes);
$query_competencias3 = "SELECT sed_competencias.IDcompetencia, sed_competencias.competencia, sed_competencias.descripcion, sed_competencias.IDtipo_competencia, sed_competencias_preguntas.IDnivel, sed_competencias_preguntas.comportamiento FROM sed_competencias LEFT JOIN sed_competencias_preguntas ON sed_competencias_preguntas.IDcompetencia = sed_competencias.IDcompetencia WHERE IDnivel = '$elnivel' AND sed_competencias.IDcompetencia= 3";
mysql_query("SET NAMES 'utf8'");
$competencias3 = mysql_query($query_competencias3, $vacantes) or die(mysql_error());
$row_competencias3 = mysql_fetch_assoc($competencias3);
$totalRows_competencias3 = mysql_num_rows($competencias3);

mysql_select_db($database_vacantes, $vacantes);
$query_competencias4 = "SELECT sed_competencias.IDcompetencia, sed_competencias.competencia, sed_competencias.descripcion, sed_competencias.IDtipo_competencia, sed_competencias_preguntas.IDnivel, sed_competencias_preguntas.comportamiento FROM sed_competencias LEFT JOIN sed_competencias_preguntas ON sed_competencias_preguntas.IDcompetencia = sed_competencias.IDcompetencia WHERE IDnivel = '$elnivel' AND sed_competencias.IDcompetencia= 4";
mysql_query("SET NAMES 'utf8'");
$competencias4 = mysql_query($query_competencias4, $vacantes) or die(mysql_error());
$row_competencias4 = mysql_fetch_assoc($competencias4);
$totalRows_competencias4 = mysql_num_rows($competencias4);

if( $elnivel == 1 ){$enlace = 9; } else {$enlace = 5;}
mysql_select_db($database_vacantes, $vacantes);
$query_competencias5 = "SELECT sed_competencias.IDcompetencia, sed_competencias.competencia, sed_competencias.descripcion, sed_competencias.IDtipo_competencia, sed_competencias_preguntas.IDnivel, sed_competencias_preguntas.comportamiento FROM sed_competencias LEFT JOIN sed_competencias_preguntas ON sed_competencias_preguntas.IDcompetencia = sed_competencias.IDcompetencia WHERE IDnivel = '$elnivel' AND sed_competencias.IDcompetencia = $enlace";
mysql_query("SET NAMES 'utf8'");
$competencias5 = mysql_query($query_competencias5, $vacantes) or die(mysql_error());
$row_competencias5 = mysql_fetch_assoc($competencias5);
$totalRows_competencias5 = mysql_num_rows($competencias5);

mysql_select_db($database_vacantes, $vacantes);
$query_competencias6 = "SELECT sed_competencias.IDcompetencia, sed_competencias.competencia, sed_competencias.descripcion, sed_competencias.IDtipo_competencia, sed_competencias_preguntas.IDnivel, sed_competencias_preguntas.comportamiento FROM sed_competencias LEFT JOIN sed_competencias_preguntas ON sed_competencias_preguntas.IDcompetencia = sed_competencias.IDcompetencia WHERE IDnivel = '$elnivel' AND sed_competencias.IDcompetencia= 6";
mysql_query("SET NAMES 'utf8'");
$competencias6 = mysql_query($query_competencias6, $vacantes) or die(mysql_error());
$row_competencias6 = mysql_fetch_assoc($competencias6);
$totalRows_competencias6 = mysql_num_rows($competencias6);

mysql_select_db($database_vacantes, $vacantes);
$query_competencias7 = "SELECT sed_competencias.IDcompetencia, sed_competencias.competencia, sed_competencias.descripcion, sed_competencias.IDtipo_competencia, sed_competencias_preguntas.IDnivel, sed_competencias_preguntas.comportamiento FROM sed_competencias LEFT JOIN sed_competencias_preguntas ON sed_competencias_preguntas.IDcompetencia = sed_competencias.IDcompetencia WHERE IDnivel = '$elnivel' AND sed_competencias.IDcompetencia= 7";
mysql_query("SET NAMES 'utf8'");
$competencias7 = mysql_query($query_competencias7, $vacantes) or die(mysql_error());
$row_competencias7 = mysql_fetch_assoc($competencias7);
$totalRows_competencias7 = mysql_num_rows($competencias7);

mysql_select_db($database_vacantes, $vacantes);
$query_competencias8 = "SELECT sed_competencias.IDcompetencia, sed_competencias.competencia, sed_competencias.descripcion, sed_competencias.IDtipo_competencia, sed_competencias_preguntas.IDnivel, sed_competencias_preguntas.comportamiento FROM sed_competencias LEFT JOIN sed_competencias_preguntas ON sed_competencias_preguntas.IDcompetencia = sed_competencias.IDcompetencia WHERE IDnivel = '$elnivel' AND sed_competencias.IDcompetencia= 8";
mysql_query("SET NAMES 'utf8'");
$competencias8 = mysql_query($query_competencias8, $vacantes) or die(mysql_error());
$row_competencias8 = mysql_fetch_assoc($competencias8);
$totalRows_competencias8 = mysql_num_rows($competencias8);


// autoevaluacion
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo1 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, Avg(sed_competencias_resultados.comp8) AS Rcomp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 1 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo1 = mysql_query($query_Total_tipo1, $vacantes) or die(mysql_error());
$row_Total_tipo1 = mysql_fetch_assoc($Total_tipo1);
$totalRows_Total_tipo1 = mysql_num_rows($Total_tipo1);

// pares
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo2 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, Avg(sed_competencias_resultados.comp8) AS Rcomp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 2 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo2 = mysql_query($query_Total_tipo2, $vacantes) or die(mysql_error());
$row_Total_tipo2 = mysql_fetch_assoc($Total_tipo2);
$totalRows_Total_tipo2 = mysql_num_rows($Total_tipo2);

// jefe
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo3 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, Avg(sed_competencias_resultados.comp8) AS Rcomp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 3 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo3 = mysql_query($query_Total_tipo3, $vacantes) or die(mysql_error());
$row_Total_tipo3 = mysql_fetch_assoc($Total_tipo3);
$totalRows_Total_tipo3 = mysql_num_rows($Total_tipo3);

// colaboradores
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo4 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, Avg(sed_competencias_resultados.comp8) AS Rcomp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 4 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo4 = mysql_query($query_Total_tipo4, $vacantes) or die(mysql_error());
$row_Total_tipo4 = mysql_fetch_assoc($Total_tipo4);
$totalRows_Total_tipo4 = mysql_num_rows($Total_tipo4);

// clientes
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo5 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, Avg(sed_competencias_resultados.comp8) AS Rcomp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 5 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo5 = mysql_query($query_Total_tipo5, $vacantes) or die(mysql_error());
$row_Total_tipo5 = mysql_fetch_assoc($Total_tipo5);
$totalRows_Total_tipo5 = mysql_num_rows($Total_tipo5);

// autoevaluacion
mysql_select_db($database_vacantes, $vacantes);
$query_tipo1 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 1 AND sed_competencias_resultados.anio = $anio";
$tipo1 = mysql_query($query_tipo1, $vacantes) or die(mysql_error());
$row_tipo1 = mysql_fetch_assoc($tipo1);
$totalRows_tipo1 = mysql_num_rows($tipo1);

// jefe
mysql_select_db($database_vacantes, $vacantes);
$query_tipo2 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 2 AND sed_competencias_resultados.anio = $anio";
$tipo2 = mysql_query($query_tipo2, $vacantes) or die(mysql_error());
$row_tipo2 = mysql_fetch_assoc($tipo2);
$totalRows_tipo2 = mysql_num_rows($tipo2);

// pares
mysql_select_db($database_vacantes, $vacantes);
$query_tipo3 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 3 AND sed_competencias_resultados.anio = $anio";
$tipo3 = mysql_query($query_tipo3, $vacantes) or die(mysql_error());
$row_tipo3 = mysql_fetch_assoc($tipo3);
$totalRows_tipo3 = mysql_num_rows($tipo3);

// cols
mysql_select_db($database_vacantes, $vacantes);
$query_tipo4 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 4 AND sed_competencias_resultados.anio = $anio";
$tipo4 = mysql_query($query_tipo4, $vacantes) or die(mysql_error());
$row_tipo4 = mysql_fetch_assoc($tipo4);
$totalRows_tipo4 = mysql_num_rows($tipo4);

// clientes
mysql_select_db($database_vacantes, $vacantes);
$query_tipo5 = "SELECT sed_competencias_resultados.IDevaluacion FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 5 AND sed_competencias_resultados.anio = $anio";
$tipo5 = mysql_query($query_tipo5, $vacantes) or die(mysql_error());
$row_tipo5 = mysql_fetch_assoc($tipo5);
$totalRows_tipo5 = mysql_num_rows($tipo5);

if ($row_Total_tipo1['Resultados'] == $totalRows_tipo1) { $Completo_tipo1 = 1;} else {$Completo_tipo1 = 0;}
if ($row_Total_tipo2['Resultados'] == $totalRows_tipo2) { $Completo_tipo2 = 1;} else {$Completo_tipo2 = 0;}
if ($row_Total_tipo3['Resultados'] == $totalRows_tipo3) { $Completo_tipo3 = 1;} else {$Completo_tipo3 = 0;}
if ($row_Total_tipo4['Resultados'] == $totalRows_tipo4) { $Completo_tipo4 = 1;} else {$Completo_tipo4 = 0;}
if ($row_Total_tipo5['Resultados'] == $totalRows_tipo5) { $Completo_tipo5 = 1;} else {$Completo_tipo5 = 0;}

$completo = 0;
if ($row_Total_tipo1['Resultados'] == $totalRows_tipo1) { $completo = $completo + 1; }
if ($row_Total_tipo2['Resultados'] == $totalRows_tipo2) { $completo = $completo + 1; }
if ($row_Total_tipo3['Resultados'] == $totalRows_tipo3 or $row_Total_tipo3['Resultados'] > 2) { $completo = $completo + 1; }
if ($row_Total_tipo4['Resultados'] == $totalRows_tipo4 or $row_Total_tipo4['Resultados'] > 2) { $completo = $completo + 1; }
if ($row_Total_tipo5['Resultados'] == $totalRows_tipo5 or $row_Total_tipo5['Resultados'] > 2) { $completo = $completo + 1; }

if ($totalRows_Total_tipo1 == 0) { $Aplica_tipo1 = 0;} else {$Aplica_tipo1 = 1;}
if ($totalRows_Total_tipo2 == 0) { $Aplica_tipo2 = 0;} else {$Aplica_tipo2 = 1;}
if ($totalRows_Total_tipo3 == 0) { $Aplica_tipo3 = 0;} else {$Aplica_tipo3 = 1;}
if ($totalRows_Total_tipo4 == 0) { $Aplica_tipo4 = 0;} else {$Aplica_tipo4 = 1;}
if ($totalRows_Total_tipo5 == 0) { $Aplica_tipo5 = 0;} else {$Aplica_tipo5 = 1;}

//calculamos la ponderacion de cada parte
//ponderaciones
$pond_tipo1 = 10; 
$pond_tipo2 = 40; 
$pond_tipo3 = 20; 
$pond_tipo4 = 20; 
$pond_tipo5 = 10; 

if ($Aplica_tipo2 == 0 && $Aplica_tipo3 == 1 && $Aplica_tipo4 == 1 && $Aplica_tipo5 == 1) { $pond_tipo1 = 20; $pond_tipo2 = 0; $pond_tipo3 = 30; $pond_tipo4 = 30; $pond_tipo5 = 20; }
if ($Aplica_tipo2 == 0 && $Aplica_tipo3 == 1 && $Aplica_tipo4 == 1 && $Aplica_tipo5 == 0) { $pond_tipo1 = 20; $pond_tipo2 = 0; $pond_tipo3 = 40; $pond_tipo4 = 40; $pond_tipo5 = 0; }
if ($Aplica_tipo2 == 0 && $Aplica_tipo3 == 1 && $Aplica_tipo4 == 0 && $Aplica_tipo5 == 1) { $pond_tipo1 = 20; $pond_tipo2 = 0; $pond_tipo3 = 40; $pond_tipo4 = 0; $pond_tipo5 = 40; }
if ($Aplica_tipo2 == 0 && $Aplica_tipo3 == 1 && $Aplica_tipo4 == 0 && $Aplica_tipo5 == 0) { $pond_tipo1 = 20; $pond_tipo2 = 0; $pond_tipo3 = 80; $pond_tipo4 = 0; $pond_tipo5 = 0; }
if ($Aplica_tipo2 == 0 && $Aplica_tipo3 == 0 && $Aplica_tipo4 == 1 && $Aplica_tipo5 == 1) { $pond_tipo1 = 20; $pond_tipo2 = 0; $pond_tipo3 = 0; $pond_tipo4 = 40; $pond_tipo5 = 40; }
if ($Aplica_tipo2 == 0 && $Aplica_tipo3 == 0 && $Aplica_tipo4 == 1 && $Aplica_tipo5 == 0) { $pond_tipo1 = 20; $pond_tipo2 = 0; $pond_tipo3 = 0; $pond_tipo4 = 80; $pond_tipo5 = 0; }
if ($Aplica_tipo2 == 0 && $Aplica_tipo3 == 0 && $Aplica_tipo4 == 0 && $Aplica_tipo5 == 1) { $pond_tipo1 = 20; $pond_tipo2 = 0; $pond_tipo3 = 0; $pond_tipo4 = 0; $pond_tipo5 = 80; }
if ($Aplica_tipo2 == 1 && $Aplica_tipo3 == 1 && $Aplica_tipo4 == 1 && $Aplica_tipo5 == 1) { $pond_tipo1 = 10; $pond_tipo2 = 40; $pond_tipo3 = 20; $pond_tipo4 = 20; $pond_tipo5 = 10; }
if ($Aplica_tipo2 == 1 && $Aplica_tipo3 == 1 && $Aplica_tipo4 == 1 && $Aplica_tipo5 == 0) { $pond_tipo1 = 10; $pond_tipo2 = 40; $pond_tipo3 = 25; $pond_tipo4 = 25; $pond_tipo5 = 0; }
if ($Aplica_tipo2 == 1 && $Aplica_tipo3 == 1 && $Aplica_tipo4 == 0 && $Aplica_tipo5 == 1) { $pond_tipo1 = 10; $pond_tipo2 = 40; $pond_tipo3 = 25; $pond_tipo4 = 0; $pond_tipo5 = 25; }
if ($Aplica_tipo2 == 1 && $Aplica_tipo3 == 1 && $Aplica_tipo4 == 0 && $Aplica_tipo5 == 0) { $pond_tipo1 = 20; $pond_tipo2 = 40; $pond_tipo3 = 40; $pond_tipo4 = 0; $pond_tipo5 = 0; }
if ($Aplica_tipo2 == 1 && $Aplica_tipo3 == 0 && $Aplica_tipo4 == 1 && $Aplica_tipo5 == 1) { $pond_tipo1 = 10; $pond_tipo2 = 40; $pond_tipo3 = 0; $pond_tipo4 = 25; $pond_tipo5 = 25; }
if ($Aplica_tipo2 == 1 && $Aplica_tipo3 == 0 && $Aplica_tipo4 == 1 && $Aplica_tipo5 == 0) { $pond_tipo1 = 20; $pond_tipo2 = 40; $pond_tipo3 = 0; $pond_tipo4 = 40; $pond_tipo5 = 0; }
if ($Aplica_tipo2 == 1 && $Aplica_tipo3 == 0 && $Aplica_tipo4 == 0 && $Aplica_tipo5 == 1) { $pond_tipo1 = 20; $pond_tipo2 = 40; $pond_tipo3 = 0; $pond_tipo4 = 0; $pond_tipo5 = 4; }
if ($Aplica_tipo2 == 1 && $Aplica_tipo3 == 0 && $Aplica_tipo4 == 0 && $Aplica_tipo5 == 0) { $pond_tipo1 = 20; $pond_tipo2 = 80; $pond_tipo3 = 0; $pond_tipo4 = 0; $pond_tipo5 = 0; }

//echo $pond_tipo1."</br>"; 
//echo $pond_tipo2."</br>"; 
//echo $pond_tipo3."</br>"; 
//echo $pond_tipo4."</br>"; 
//echo $pond_tipo5."</br>"; 


$Rcomp1=(($row_Total_tipo1['Rcomp1']*$pond_tipo1)+($row_Total_tipo2['Rcomp1']*$pond_tipo2)+($row_Total_tipo3['Rcomp1']*$pond_tipo3)+($row_Total_tipo4['Rcomp1']*$pond_tipo4)+($row_Total_tipo5['Rcomp1']*$pond_tipo5))/4;
$Rcomp2=(($row_Total_tipo1['Rcomp2']*$pond_tipo1)+($row_Total_tipo2['Rcomp2']*$pond_tipo2)+($row_Total_tipo3['Rcomp2']*$pond_tipo3)+($row_Total_tipo4['Rcomp2']*$pond_tipo4)+($row_Total_tipo5['Rcomp2']*$pond_tipo5))/4;
$Rcomp3=(($row_Total_tipo1['Rcomp3']*$pond_tipo1)+($row_Total_tipo2['Rcomp3']*$pond_tipo2)+($row_Total_tipo3['Rcomp3']*$pond_tipo3)+($row_Total_tipo4['Rcomp3']*$pond_tipo4)+($row_Total_tipo5['Rcomp3']*$pond_tipo5))/4;
$Rcomp4=(($row_Total_tipo1['Rcomp4']*$pond_tipo1)+($row_Total_tipo2['Rcomp4']*$pond_tipo2)+($row_Total_tipo3['Rcomp4']*$pond_tipo3)+($row_Total_tipo4['Rcomp4']*$pond_tipo4)+($row_Total_tipo5['Rcomp4']*$pond_tipo5))/4;
$Rcomp5=(($row_Total_tipo1['Rcomp5']*$pond_tipo1)+($row_Total_tipo2['Rcomp5']*$pond_tipo2)+($row_Total_tipo3['Rcomp5']*$pond_tipo3)+($row_Total_tipo4['Rcomp5']*$pond_tipo4)+($row_Total_tipo5['Rcomp5']*$pond_tipo5))/4;
$Rcomp6=(($row_Total_tipo1['Rcomp6']*$pond_tipo1)+($row_Total_tipo2['Rcomp6']*$pond_tipo2)+($row_Total_tipo3['Rcomp6']*$pond_tipo3)+($row_Total_tipo4['Rcomp6']*$pond_tipo4)+($row_Total_tipo5['Rcomp6']*$pond_tipo5))/4;
$Rcomp7=(($row_Total_tipo1['Rcomp7']*$pond_tipo1)+($row_Total_tipo2['Rcomp7']*$pond_tipo2)+($row_Total_tipo3['Rcomp7']*$pond_tipo3)+($row_Total_tipo4['Rcomp7']*$pond_tipo4)+($row_Total_tipo5['Rcomp7']*$pond_tipo5))/4;
$Rcomp8=(($row_Total_tipo1['Rcomp8']*$pond_tipo1)+($row_Total_tipo2['Rcomp8']*$pond_tipo2)+($row_Total_tipo3['Rcomp8']*$pond_tipo3)+($row_Total_tipo4['Rcomp8']*$pond_tipo4)+($row_Total_tipo5['Rcomp8']*$pond_tipo5))/4;

//echo "Comp1: ". $Rcomp1."</br>";
//echo "Comp2: ". $Rcomp2."</br>";
//echo "Comp3: ". $Rcomp3."</br>";
//echo "Comp4: ". $Rcomp4."</br>";
//echo "Comp5: ". $Rcomp5."</br>";
//echo "comp6: ". $Rcomp6."</br>";
//echo "comp7: ". $Rcomp7."</br>";

if ($Rcomp1 >= 90) { $Tcomp1 = 4;} else if ($Rcomp1 < 90 && $Rcomp1 >= 75 ) {$Tcomp1 = 3; } else if ($Rcomp1 < 75 && $Rcomp1 >= 60 ) {$Tcomp1 = 2; } else if ($Rcomp1 < 60) { $Tcomp1 = 1;} else { $Tcomp1 = 3; }
if ($Rcomp2 >= 90) { $Tcomp2 = 4;} else if ($Rcomp2 < 90 && $Rcomp2 >= 75 ) {$Tcomp2 = 3; } else if ($Rcomp2 < 75 && $Rcomp2 >= 60 ) {$Tcomp2 = 2; } else if ($Rcomp2 < 60) { $Tcomp2 = 1;} else { $Tcomp2 = 3; }
if ($Rcomp3 >= 90) { $Tcomp3 = 4;} else if ($Rcomp3 < 90 && $Rcomp3 >= 75 ) {$Tcomp3 = 3; } else if ($Rcomp3 < 75 && $Rcomp3 >= 60 ) {$Tcomp3 = 2; } else if ($Rcomp3 < 60) { $Tcomp3 = 1;} else { $Tcomp3 = 3; }
if ($Rcomp4 >= 90) { $Tcomp4 = 4;} else if ($Rcomp4 < 90 && $Rcomp4 >= 75 ) {$Tcomp4 = 3; } else if ($Rcomp4 < 75 && $Rcomp4 >= 60 ) {$Tcomp4 = 2; } else if ($Rcomp4 < 60) { $Tcomp4 = 1;} else { $Tcomp4 = 3; }
if ($Rcomp5 >= 90) { $Tcomp5 = 4;} else if ($Rcomp5 < 90 && $Rcomp5 >= 75 ) {$Tcomp5 = 3; } else if ($Rcomp5 < 75 && $Rcomp5 >= 60 ) {$Tcomp5 = 2; } else if ($Rcomp5 < 60) { $Tcomp5 = 1;} else { $Tcomp5 = 3; }
if ($Rcomp6 >= 90) { $Tcomp6 = 4;} else if ($Rcomp6 < 90 && $Rcomp6 >= 75 ) {$Tcomp6 = 3; } else if ($Rcomp6 < 75 && $Rcomp6 >= 60 ) {$Tcomp6 = 2; } else if ($Rcomp6 < 60) { $Tcomp6 = 1;} else { $Tcomp6 = 3; }
if ($Rcomp7 >= 90) { $Tcomp7 = 4;} else if ($Rcomp7 < 90 && $Rcomp7 >= 75 ) {$Tcomp7 = 3; } else if ($Rcomp7 < 75 && $Rcomp7 >= 60 ) {$Tcomp7 = 2; } else if ($Rcomp7 < 60) { $Tcomp7 = 1;} else { $Tcomp7 = 3; }
if ($Rcomp8 >= 90) { $Tcomp8 = 4;} else if ($Rcomp8 < 90 && $Rcomp8 >= 75 ) {$Tcomp8 = 3; } else if ($Rcomp8 < 75 && $Rcomp8 >= 60 ) {$Tcomp8 = 2; } else if ($Rcomp8 < 60) { $Tcomp8 = 1;} else { $Tcomp8 = 3; }
							 
							 
mysql_select_db($database_vacantes, $vacantes);
$query_Txtcomp1 = "SELECT * FROM sed_competencias_textos WHERE  IDcompetencia = 1 AND IDnivel = $Tcomp1";
$Txtcomp1 = mysql_query($query_Txtcomp1, $vacantes) or die(mysql_error());
$row_Txtcomp1 = mysql_fetch_assoc($Txtcomp1);
$totalRows_Txtcomp1 = mysql_num_rows($Txtcomp1);

mysql_select_db($database_vacantes, $vacantes);
$query_Txtcomp2 = "SELECT * FROM sed_competencias_textos WHERE  IDcompetencia = 2 AND IDnivel = $Tcomp2";
$Txtcomp2 = mysql_query($query_Txtcomp2, $vacantes) or die(mysql_error());
$row_Txtcomp2 = mysql_fetch_assoc($Txtcomp2);
$totalRows_Txtcomp2 = mysql_num_rows($Txtcomp2);

mysql_select_db($database_vacantes, $vacantes);
$query_Txtcomp3 = "SELECT * FROM sed_competencias_textos WHERE  IDcompetencia = 3 AND IDnivel = $Tcomp3";
$Txtcomp3 = mysql_query($query_Txtcomp3, $vacantes) or die(mysql_error());
$row_Txtcomp3 = mysql_fetch_assoc($Txtcomp3);
$totalRows_Txtcomp3 = mysql_num_rows($Txtcomp3);

mysql_select_db($database_vacantes, $vacantes);
$query_Txtcomp4 = "SELECT * FROM sed_competencias_textos WHERE  IDcompetencia = 4 AND IDnivel = $Tcomp4";
$Txtcomp4 = mysql_query($query_Txtcomp4, $vacantes) or die(mysql_error());
$row_Txtcomp4 = mysql_fetch_assoc($Txtcomp4);
$totalRows_Txtcomp4 = mysql_num_rows($Txtcomp4);

mysql_select_db($database_vacantes, $vacantes);
$query_Txtcomp5 = "SELECT * FROM sed_competencias_textos WHERE  IDcompetencia = 5 AND IDnivel = $Tcomp5";
$Txtcomp5 = mysql_query($query_Txtcomp5, $vacantes) or die(mysql_error());
$row_Txtcomp5 = mysql_fetch_assoc($Txtcomp5);
$totalRows_Txtcomp5 = mysql_num_rows($Txtcomp5);

mysql_select_db($database_vacantes, $vacantes);
$query_Txtcomp6 = "SELECT * FROM sed_competencias_textos WHERE  IDcompetencia = 6 AND IDnivel = $Tcomp6";
$Txtcomp6 = mysql_query($query_Txtcomp6, $vacantes) or die(mysql_error());
$row_Txtcomp6 = mysql_fetch_assoc($Txtcomp6);
$totalRows_Txtcomp6 = mysql_num_rows($Txtcomp6);

mysql_select_db($database_vacantes, $vacantes);
$query_Txtcomp7 = "SELECT * FROM sed_competencias_textos WHERE  IDcompetencia = 7 AND IDnivel = $Tcomp7";
$Txtcomp7 = mysql_query($query_Txtcomp7, $vacantes) or die(mysql_error());
$row_Txtcomp7 = mysql_fetch_assoc($Txtcomp7);
$totalRows_Txtcomp7 = mysql_num_rows($Txtcomp7);

mysql_select_db($database_vacantes, $vacantes);
$query_Txtcomp8 = "SELECT * FROM sed_competencias_textos WHERE  IDcompetencia = 8 AND IDnivel = $Tcomp8";
$Txtcomp8 = mysql_query($query_Txtcomp8, $vacantes) or die(mysql_error());
$row_Txtcomp8 = mysql_fetch_assoc($Txtcomp8);
$totalRows_Txtcomp8 = mysql_num_rows($Txtcomp8);


$evaluaciones = $totalRows_tipo1 + $totalRows_tipo2 + $totalRows_tipo3 + $totalRows_tipo4 + $totalRows_tipo5;
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

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

    <script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/1datatables_extension_buttons_html52.js"></script>
	<!-- /Theme JS files -->
 </head>
<body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

	<?php require_once('assets/mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/pheader.php'); ?>

			<!-- Content area -->
			  <div class="content">
              
					<h1 class="text-center content-group text-danger">
						Evaluación de 360° por Competencias
                    </h1>

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha evaliado correctamente al Empleado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

				<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">
                        
							<!-- About author -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Mis resultados</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
                                    
                                    <p>A continuación se muestra el resultado de tu evaluación de 306°. <br/>
                                    Para cada competencia laboral, se muestra el resultado obtenido, así como la calificación y 
                                    algunas recomendaciones.<br/>
                                    Te recomendamos poner especial atención a los resultados con calificación de Suficiente y Deficiente.<br/>
                                    Para consultar los comentarios abiertos de tu evaluación, da clic en el botón "Ver comentarios abiertos".</p>
                                    <p>Recuerda que se buscó que los comentarios fueran constructivos y bien fundamentados y que no se deben tomar de forma literal.</p>
									<p><?php if($completo == 5 and $evaluaciones > 0) { ?><strong>El reporte está visible para el usuario.</strong><?php } ?></p>
                    <table class="table datatable-button-html5-image">
                    <thead> 
                    <tr class="bg-primary-600"> 
                      <th class="text-center" width="30%">Rubro / Competencia</th>
                      <th class="text-center" width="10%">Resultado</th>
                      <th class="text-center" width="10%">Calificación</th>
                      <th class="text-center" width="50%">Interpretación y Recomendaciones</th>
               		 </tr>
                     </thead>
                    <tbody>
                          <tr>
                            <td><strong><?php echo $row_competencias1['competencia']; ?></strong>: <?php echo $row_competencias1['descripcion']; ?></td>
                            <td class="text-center"><?php echo round($Rcomp1,0); ?></td>
                            <td>
							<?php if ($Rcomp1 >= 90) { echo "<span class='label label-primary'>Sobresaliente</span>";} 
						     else if ($Rcomp1 < 90 && $Rcomp1 >= 75 ) { echo "<span class='label label-success'>Satisfactorio</span>";} 
						     else if ($Rcomp1 < 75 && $Rcomp1 >= 60 ) { echo "<span class='label label-warning'>Suficiente</span>";} 
						     else if ($Rcomp1 < 60) { echo "<span class='label label-danger'>Deficiente</span>";} 
							?>
                             </td>
                            <td>
                            <p><strong>Interpretación:</strong></p>
								<p><?php echo $row_Txtcomp1['texto']; ?></p>
                            <p><strong>Recomendaciones</strong>:</p>
                            <?php echo $row_Txtcomp1['recomendacion']; ?>
                            </td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias2['competencia']; ?></strong>: <?php echo $row_competencias2['descripcion']; ?></td>
                            <td class="text-center"><?php echo round($Rcomp2,0); ?></td>
                            <td>
							<?php if ($Rcomp2 >= 90) { echo "<span class='label label-primary'>Sobresaliente</span>";} 
						     else if ($Rcomp2 < 90 && $Rcomp2 >= 75 ) { echo "<span class='label label-success'>Satisfactorio</span>";} 
						     else if ($Rcomp2 < 75 && $Rcomp2 >= 60 ) { echo "<span class='label label-warning'>Suficiente</span>";} 
						     else if ($Rcomp2 < 60) { echo "<span class='label label-danger'>Deficiente</span>";} 
							 ?>
                             </td>
                            <td>
                            <p><strong>Interpretación:</strong></p>
								<p><?php echo $row_Txtcomp2['texto']; ?></p>
                            <p><strong>Recomendaciones</strong>:</p>
                            <?php echo $row_Txtcomp2['recomendacion']; ?>
                            </td>
                          </tr>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias3['competencia']; ?></strong>: <?php echo $row_competencias3['descripcion']; ?></td>
                            <td class="text-center"><?php echo round($Rcomp3,0); ?></td>
                            <td>
							<?php if ($Rcomp3 >= 90) { echo "<span class='label label-primary'>Sobresaliente</span>";} 
						     else if ($Rcomp3 < 90 && $Rcomp3 >= 75 ) { echo "<span class='label label-success'>Satisfactorio</span>";} 
						     else if ($Rcomp3 < 75 && $Rcomp3 >= 60 ) { echo "<span class='label label-warning'>Suficiente</span>";} 
						     else if ($Rcomp3 < 60) { echo "<span class='label label-danger'>Deficiente</span>";} 
							 ?>
                             </td>
                            <td>
                            <p><strong>Interpretación:</strong></p>
								<p><?php echo $row_Txtcomp3['texto']; ?></p>
                            <p><strong>Recomendaciones</strong>:</p>
                            <?php echo $row_Txtcomp3['recomendacion']; ?>
                            </td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias4['competencia']; ?></strong>: <?php echo $row_competencias4['descripcion']; ?></td>
                            <td class="text-center"><?php echo round($Rcomp4,0); ?></td>
                            <td>
							<?php if ($Rcomp4 >= 90) { echo "<span class='label label-primary'>Sobresaliente</span>";} 
						     else if ($Rcomp4 < 90 && $Rcomp4 >= 75 ) { echo "<span class='label label-success'>Satisfactorio</span>";} 
						     else if ($Rcomp4 < 75 && $Rcomp4 >= 60 ) { echo "<span class='label label-warning'>Suficiente</span>";} 
						     else if ($Rcomp4 < 60) { echo "<span class='label label-danger'>Deficiente</span>";} 
							 ?>
                             </td>
                            <td>
                            <p><strong>Interpretación:</strong></p>
								<p><?php echo $row_Txtcomp4['texto']; ?></p>
                            <p><strong>Recomendaciones</strong>:</p>
                            <?php echo $row_Txtcomp4['recomendacion']; ?>
                            </td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias5['competencia']; ?></strong>: <?php echo $row_competencias5['descripcion']; ?></td>
                            <td class="text-center"><?php echo  round($Rcomp5,0); ?></td>
                            <td>
							<?php if ($Rcomp5 >= 90) { echo "<span class='label label-primary'>Sobresaliente</span>";} 
						     else if ($Rcomp5 < 90 && $Rcomp5 >= 75 ) { echo "<span class='label label-success'>Satisfactorio</span>";} 
						     else if ($Rcomp5 < 75 && $Rcomp5 >= 60 ) { echo "<span class='label label-warning'>Suficiente</span>";} 
						     else if ($Rcomp5 < 60) { echo "<span class='label label-danger'>Deficiente</span>";} 
							 ?>
                             </td>
                            <td>
                            <p><strong>Interpretación:</strong></p>
								<p><?php echo $row_Txtcomp5['texto']; ?></p>
                            <p><strong>Recomendaciones</strong>:</p>
                            <?php echo $row_Txtcomp5['recomendacion']; ?>
                            </td>
							</tr>
                          <tr>
                            <td><strong><?php echo $row_competencias6['competencia']; ?></strong>: <?php echo $row_competencias6['comportamiento']; ?></td>
                            <td class="text-center"><?php echo round($Rcomp6,0); ?></td>
                            <td>
							<?php if ($Rcomp6 >= 90) { echo "<span class='label label-primary'>Sobresaliente</span>";} 
						     else if ($Rcomp6 < 90 && $Rcomp6 >= 75 ) { echo "<span class='label label-success'>Satisfactorio</span>";} 
						     else if ($Rcomp6 < 75 && $Rcomp6 >= 60 ) { echo "<span class='label label-warning'>Suficiente</span>";} 
						     else if ($Rcomp6 < 60) { echo "<span class='label label-danger'>Deficiente</span>";} 
							 ?>
                             </td>
                            <td>
                            <p><strong>Interpretación:</strong></p>
								<p><?php echo $row_Txtcomp6['texto']; ?></p>
                            <p><strong>Recomendaciones</strong>:</p>
                            <?php echo $row_Txtcomp6['recomendacion']; ?>
                            </td>
							</tr>
							<tr>
                            <td><strong><?php echo $row_competencias7['competencia']; ?></strong>: <?php echo $row_competencias7['comportamiento']; ?></td>
                            <td class="text-center"><?php echo round($Rcomp7,0); ?></td>
                            <td>
							<?php if ($Rcomp7 >= 90) { echo "<span class='label label-primary'>Sobresaliente</span>";} 
						     else if ($Rcomp7 < 90 && $Rcomp7 >= 75 ) { echo "<span class='label label-success'>Satisfactorio</span>";} 
						     else if ($Rcomp7 < 75 && $Rcomp7 >= 60 ) { echo "<span class='label label-warning'>Suficiente</span>";} 
						     else if ($Rcomp7 < 60) { echo "<span class='label label-danger'>Deficiente</span>";} 
							 ?>
                             </td>
                            <td>
                            <p><strong>Interpretación:</strong></p>
								<p><?php echo $row_Txtcomp8['texto']; ?></p>
                            <p><strong>Recomendaciones</strong>:</p>
                            <?php echo $row_Txtcomp8['recomendacion']; ?>
                            </td>
							</tr>
							<tr>
                            <td><strong><?php echo $row_competencias8['competencia']; ?></strong>: <?php echo $row_competencias8['comportamiento']; ?></td>
                            <td class="text-center"><?php echo round($Rcomp8,0); ?></td>
                            <td>
							<?php if ($Rcomp8 >= 90) { echo "<span class='label label-primary'>Sobresaliente</span>";} 
						     else if ($Rcomp8 < 90 && $Rcomp8 >= 75 ) { echo "<span class='label label-success'>Satisfactorio</span>";} 
						     else if ($Rcomp8 < 75 && $Rcomp8 >= 60 ) { echo "<span class='label label-warning'>Suficiente</span>";} 
						     else if ($Rcomp8 < 60) { echo "<span class='label label-danger'>Deficiente</span>";} 
							 ?>
                             </td>
                            <td>
                            <p><strong>Interpretación:</strong></p>
								<p><?php echo $row_Txtcomp8['texto']; ?></p>
                            <p><strong>Recomendaciones</strong>:</p>
                            <?php echo $row_Txtcomp8['recomendacion']; ?>
                            </td>
							</tr>                     </tbody>
					</table>

                                    
                    <!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Comentarios abiertos</h6>
								</div>

								<div class="modal-body">
									<div class="category-content">
										<ul>
								<?php do { ?>
										 <li>&nbsp; <?php echo $row_Txtcomp8['comp8'] ?></li>
								<?php } while ($row_Txtcomp8 = mysql_fetch_assoc($Txtcomp8)); ?>
                                        </ul>
									</div>

								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


                                    </div>
								</div>
							</div>
							<!-- /about author -->
                        

				</div>
					</div>
					<!-- /detached content -->


					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">
							
							
								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Accesos</span>
									</div>

									<div class="category-content">
										<ul class="media-list">
										<a href="admin_comp.php" class="btn btn-success">Regresar</a>
										</ul>
									</div>
								</div>
								<!-- /upcoming courses -->


								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Datos del Evaluado</span>
									</div>

									<div class="category-content">

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Nombre:</label>
											<div><?php echo $row_evaluacion['emp_nombre']." ".$row_evaluacion['emp_paterno']." ".$row_evaluacion['emp_materno']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Puesto:</label>
											<div><?php echo $row_evaluacion['denominacion']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Sucursal:</label>
											<div><?php echo $row_evaluacion['matriz']; ?></div>
										</div>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Área:</label>
											<div><?php echo $row_evaluacion['area']; ?></div>
										</div>


										</div>								
                                    </div>
								<!-- /upcoming courses -->

								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Ponderación</span>
									</div>

									<div class="category-content">
										<ul class="media-list">
										 <li>&nbsp; <?php echo $pond_tipo1; ?>% Autoevaluación <strong><?php  echo "(".$row_Total_tipo1['Resultados'] ." de ". $totalRows_tipo1.")"; ?></strong></li>
										 <li>&nbsp; <?php echo $pond_tipo2; ?>% Jefe <strong><?php  echo "(".$row_Total_tipo2['Resultados'] ." de ". $totalRows_tipo2.")"; ?></strong></li>
										 <li>&nbsp; <?php echo $pond_tipo4; ?>% Pares <strong><?php  echo "(".$row_Total_tipo4['Resultados'] ." de ". $totalRows_tipo4.")"; ?></strong></li>
										 <li>&nbsp; <?php echo $pond_tipo3; ?>% Colaboradores <strong><?php  echo "(".$row_Total_tipo3['Resultados'] ." de ". $totalRows_tipo3.")"; ?></strong></li>
										 <li>&nbsp; <?php echo $pond_tipo5; ?>% Clientes <strong><?php  echo "(".$row_Total_tipo5['Resultados'] ." de ". $totalRows_tipo5.")"; ?></strong></li>
										 <li><strong> 100% Total</strong></li>
										 <li>&nbsp;</li>
										 <li>Total de participantes: <strong><?php echo $evaluaciones; ?></strong></li>

                                        </ul>
									</div>
								</div>
								<!-- /upcoming courses -->


							</div>
						</div>
					</div>
		            <!-- /detached sidebar -->


					<!-- /Contenido -->

				  <!-- Footer -->
				  <div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
			    </div>
				    <!-- /footer -->
                </div>
				<!-- /content area -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>