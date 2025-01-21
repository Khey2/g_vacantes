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

// autoevaluacion
$IDevaluacion = $_GET['IDevaluacion'];
mysql_select_db($database_vacantes, $vacantes);
$query_evaluacion = "SELECT sed_competencias_resultados.IDevaluacion,  sed_competencias_resultados.IDempleado, sed_competencias_resultados.IDempleado_evaluador, sed_competencias_resultados.anio,  sed_competencias_resultados.IDtipo, sed_competencias_resultados.IDestatus, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, vac_puestos.IDnivel_puestoC,  sed_competencias_resultados.comp1, sed_competencias_resultados.comp2, sed_competencias_resultados.comp3, sed_competencias_resultados.comp4, sed_competencias_resultados.comp5, sed_competencias_resultados.comp6, sed_competencias_resultados.comp7, sed_competencias_resultados.comp8, vac_puestos.denominacion,
vac_areas.area, vac_areas.area, vac_matriz.matriz FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = prod_activos.IDpuesto LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz WHERE sed_competencias_resultados.IDevaluacion = $IDevaluacion";
mysql_query("SET NAMES 'utf8'");
$evaluacion = mysql_query($query_evaluacion, $vacantes) or die(mysql_error());
$row_evaluacion = mysql_fetch_assoc($evaluacion);
$totalRows_evaluacion = mysql_num_rows($evaluacion);
$elnivel = $row_evaluacion['IDnivel_puestoC'];
$eltipo = $row_evaluacion['IDtipo'];
$el_usuario = $row_evaluacion['IDempleado'];


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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar 1
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$updateSQL = sprintf("UPDATE sed_competencias_resultados SET comp1=%s, comp2=%s, comp3=%s, comp4=%s, comp5=%s, comp6=%s, comp7=%s, comp8=%s, IDestatus=%s  WHERE IDevaluacion='$IDevaluacion'",
                       GetSQLValueString($_POST['comp1'], "int"),
                       GetSQLValueString($_POST['comp2'], "int"),
                       GetSQLValueString($_POST['comp3'], "int"),
                       GetSQLValueString($_POST['comp4'], "int"),
                       GetSQLValueString($_POST['comp5'], "int"),
                       GetSQLValueString($_POST['comp6'], "int"),
                       GetSQLValueString($_POST['comp7'], "int"),
                       GetSQLValueString($_POST['comp8'], "text"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($IDevaluacion, "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header('Location: admin_comp_ver.php?info=1&IDevaluacion='.$IDevaluacion.'');
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDevaluacion'];
  $deleteSQL = "UPDATE sed_competencias_resultados SET comp1='', comp2='', comp3='', comp4='', comp5='', comp6='', comp7='', comp8='', IDestatus=0  WHERE IDevaluacion='$IDevaluacion'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: prev_admin_comp.php?IDempleado=".$el_usuario."&info=3");
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 2)) {
  
  $borrado = $_GET['IDevaluacion'];
  $deleteSQL = "DELETE FROM sed_competencias_resultados WHERE IDevaluacion='$IDevaluacion'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: prev_admin_comp.php?IDempleado=".$el_usuario."&info=4");
}

// autoevaluacion
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo1 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 1 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo1 = mysql_query($query_Total_tipo1, $vacantes) or die(mysql_error());
$row_Total_tipo1 = mysql_fetch_assoc($Total_tipo1);
$totalRows_Total_tipo1 = mysql_num_rows($Total_tipo1);

// jefe
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo2 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 2 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo2 = mysql_query($query_Total_tipo2, $vacantes) or die(mysql_error());
$row_Total_tipo2 = mysql_fetch_assoc($Total_tipo2);
$totalRows_Total_tipo2 = mysql_num_rows($Total_tipo2);

// pares
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo3 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 3 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo3 = mysql_query($query_Total_tipo3, $vacantes) or die(mysql_error());
$row_Total_tipo3 = mysql_fetch_assoc($Total_tipo3);
$totalRows_Total_tipo3 = mysql_num_rows($Total_tipo3);

// colaboradores
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo4 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 4 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo4 = mysql_query($query_Total_tipo4, $vacantes) or die(mysql_error());
$row_Total_tipo4 = mysql_fetch_assoc($Total_tipo4);
$totalRows_Total_tipo4 = mysql_num_rows($Total_tipo4);

// clientes
mysql_select_db($database_vacantes, $vacantes);
$query_Total_tipo5 = "SELECT sed_competencias_resultados.IDevaluacion, sed_competencias_resultados.IDempleado, Avg(sed_competencias_resultados.comp1) AS Rcomp1, Avg(sed_competencias_resultados.comp2) AS Rcomp2, Avg(sed_competencias_resultados.comp3) AS Rcomp3, Avg(sed_competencias_resultados.comp4) AS Rcomp4, Avg(sed_competencias_resultados.comp5) AS Rcomp5, Avg(sed_competencias_resultados.comp6) AS Rcomp6, Avg(sed_competencias_resultados.comp7) AS Rcomp7, Sum(sed_competencias_resultados.IDestatus) AS Resultados, sed_competencias_resultados.comp8 FROM sed_competencias_resultados LEFT JOIN prod_activos ON sed_competencias_resultados.IDempleado = prod_activos.IDempleado WHERE sed_competencias_resultados.IDempleado = $el_usuario AND sed_competencias_resultados.IDtipo = 5 AND sed_competencias_resultados.anio = $anio GROUP BY sed_competencias_resultados.IDempleado";
$Total_tipo5 = mysql_query($query_Total_tipo5, $vacantes) or die(mysql_error());
$row_Total_tipo5 = mysql_fetch_assoc($Total_tipo5);
$totalRows_Total_tipo5 = mysql_num_rows($Total_tipo5);

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

$completo = 0;
if ($row_Total_tipo1['Resultados'] == $totalRows_tipo1) { $completo = $completo + 1; }
if ($row_Total_tipo2['Resultados'] == $totalRows_tipo2) { $completo = $completo + 1; }
if ($row_Total_tipo3['Resultados'] == $totalRows_tipo3 or $row_Total_tipo3['Resultados'] > 4) { $completo = $completo + 1; }
if ($row_Total_tipo4['Resultados'] == $totalRows_tipo4 or $row_Total_tipo4['Resultados'] > 4) { $completo = $completo + 1; }
if ($row_Total_tipo5['Resultados'] == $totalRows_tipo5 or $row_Total_tipo5['Resultados'] > 4) { $completo = $completo + 1; }
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    
    <script src="assets/js/app.js"></script>
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
                        <?php if($row_evaluacion['IDestatus'] == 1) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Esta evaluación ya está completa.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


				<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">
                        
							<!-- About author -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Evaluar</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
								<p>Seleccione el nivel de dominio de cada una de las competencias determinadas para el evaluado.</br> A exepción de la pregunta abierta, todos los campos son obligatorios.</p>
                                <p>Asegurate de que en la pregunta abierta tu comentarios sean constructivos y bien fundamentados ya que esto es crucial para una correcta retroalimentación al empleado.</p>
                                    
                                    <div class="row show-grid">
								</div>
                                    
									
            		<form method="post" class="form-horizontal form-validate-jquery" name="form1"  action="<?php echo $editFormAction; ?>" >
                    <table class="table table datatable-condensed table-bordered">
                    <thead> 
                    <tr class="bg-primary-600"> 
                      <th width="30%" class="text-center">Competencia / Rubro</th>
                      <th width="30%" class="text-center">Comportamiento</th>
                      <th class="text-center">Sobresaliente</th>
                      <th class="text-center">Satisfactorio</th>
                      <th class="text-center">Requiere Mejorar</th>
                      <th class="text-center">No Satisfactorio</th>
               		 </tr>
                    </thead>
                    <tbody>
                          <tr>
                            <td><strong><?php echo $row_competencias1['competencia']; ?></strong>: <?php echo $row_competencias1['descripcion']; ?></td>
                            <td> <?php echo $row_competencias1['comportamiento']; ?></td>
                            <td><p class="text-center"> <input required type="radio" name="comp1" class="styled" value="4" <?php if (!(strcmp(htmlentities($row_evaluacion['comp1'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp1" class="styled" value="3" <?php if (!(strcmp(htmlentities($row_evaluacion['comp1'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp1" class="styled" value="2" <?php if (!(strcmp(htmlentities($row_evaluacion['comp1'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp1" class="styled" value="1" <?php if (!(strcmp(htmlentities($row_evaluacion['comp1'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></p></td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias2['competencia']; ?></strong>:<?php echo $row_competencias2['descripcion']; ?></td>
                            <td> <?php echo $row_competencias2['comportamiento']; ?></td>
                            <td><p class="text-center"><input required type="radio" name="comp2" class="styled" value="4" <?php if (!(strcmp(htmlentities($row_evaluacion['comp2'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp2" class="styled" value="3" <?php if (!(strcmp(htmlentities($row_evaluacion['comp2'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp2" class="styled" value="2" <?php if (!(strcmp(htmlentities($row_evaluacion['comp2'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp2" class="styled" value="1" <?php if (!(strcmp(htmlentities($row_evaluacion['comp2'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></p></td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias3['competencia']; ?></strong>: <?php echo $row_competencias3['descripcion']; ?></td>
                            <td> <?php echo $row_competencias3['comportamiento']; ?></td>
                            <td><p class="text-center"><input required type="radio" name="comp3" class="styled" value="4" <?php if (!(strcmp(htmlentities($row_evaluacion['comp3'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp3" class="styled" value="3" <?php if (!(strcmp(htmlentities($row_evaluacion['comp3'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp3" class="styled" value="2" <?php if (!(strcmp(htmlentities($row_evaluacion['comp3'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp3" class="styled" value="1" <?php if (!(strcmp(htmlentities($row_evaluacion['comp3'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></p></td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias4['competencia']; ?></strong>: <?php echo $row_competencias4['descripcion']; ?></td>
                            <td> <?php echo $row_competencias4['comportamiento']; ?></td>
                            <td><p class="text-center"><input required type="radio" name="comp4" class="styled" value="4" <?php if (!(strcmp(htmlentities($row_evaluacion['comp4'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp4" class="styled" value="3" <?php if (!(strcmp(htmlentities($row_evaluacion['comp4'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp4" class="styled" value="2" <?php if (!(strcmp(htmlentities($row_evaluacion['comp4'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp4" class="styled" value="1" <?php if (!(strcmp(htmlentities($row_evaluacion['comp4'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></p></td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias5['competencia']; ?></strong>: <?php echo $row_competencias5['descripcion']; ?></td>
                            <td> <?php echo $row_competencias5['comportamiento']; ?></td>
                            <td><p class="text-center"><input required type="radio" name="comp5" class="styled" value="4" <?php if (!(strcmp(htmlentities($row_evaluacion['comp5'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp5" class="styled" value="3" <?php if (!(strcmp(htmlentities($row_evaluacion['comp5'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp5" class="styled" value="2" <?php if (!(strcmp(htmlentities($row_evaluacion['comp5'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp5" class="styled" value="1" <?php if (!(strcmp(htmlentities($row_evaluacion['comp5'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></p></td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias6['competencia']; ?></strong>: <?php echo $row_competencias6['descripcion']; ?> </td>
                            <td> <?php echo $row_competencias6['comportamiento']; ?></td>
                            <td><p class="text-center"><input required type="radio" name="comp6" class="styled" value="4" <?php if (!(strcmp(htmlentities($row_evaluacion['comp6'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp6" class="styled" value="3" <?php if (!(strcmp(htmlentities($row_evaluacion['comp6'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp6" class="styled" value="2" <?php if (!(strcmp(htmlentities($row_evaluacion['comp6'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp6" class="styled" value="1" <?php if (!(strcmp(htmlentities($row_evaluacion['comp6'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></p></td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias7['competencia']; ?></strong>: <?php echo $row_competencias7['descripcion']; ?></td>
                            <td> <?php echo $row_competencias7['comportamiento']; ?></td>
                            <td><p class="text-center"><input required type="radio" name="comp7" class="styled" value="4" <?php if (!(strcmp(htmlentities($row_evaluacion['comp7'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp7" class="styled" value="3" <?php if (!(strcmp(htmlentities($row_evaluacion['comp7'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp7" class="styled" value="2" <?php if (!(strcmp(htmlentities($row_evaluacion['comp7'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp7" class="styled" value="1" <?php if (!(strcmp(htmlentities($row_evaluacion['comp7'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></p></td>
                          </tr>
                          <tr>
                            <td><strong><?php echo $row_competencias8['competencia']; ?></strong>: <?php echo $row_competencias8['descripcion']; ?></td>
                            <td> <?php echo $row_competencias8['comportamiento']; ?></td>
                            <td><p class="text-center"><input required type="radio" name="comp8" class="styled" value="4" <?php if (!(strcmp(htmlentities($row_evaluacion['comp8'], ENT_COMPAT, 'utf-8'),4))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp8" class="styled" value="3" <?php if (!(strcmp(htmlentities($row_evaluacion['comp8'], ENT_COMPAT, 'utf-8'),3))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp8" class="styled" value="2" <?php if (!(strcmp(htmlentities($row_evaluacion['comp8'], ENT_COMPAT, 'utf-8'),2))) {echo "checked=\"checked\"";} ?> /></p></td>
                            <td><p class="text-center"><input type="radio" name="comp8" class="styled" value="1" <?php if (!(strcmp(htmlentities($row_evaluacion['comp8'], ENT_COMPAT, 'utf-8'),1))) {echo "checked=\"checked\"";} ?> /></p></td>
                          </tr>
                          <tr>
                            <td colspan="6">
                            	<div class="modal-footer">
                                    <input type="hidden" name="MM_update" value="form1">
                                    <input type="hidden" name="IDplantilla" value="<?php echo $row_evaluacion['IDevaluacion']; ?>">
                                    <input type="hidden" name="IDestatus" value="1">
  									<a class="btn btn-default" href="admin_comp.php">Regresar</a>
                                   <?php  if( $completo != 5) { ?>
                                    <input type="submit" class="btn btn-primary" value="Actualizar">
                                   <?php  } ?>
			                    </div>
							 </td>
                          </tr>
                     </tbody>
					</table>
                                </form>
                                
                                    
                                    </div>
								</div>
							</div>
							<!-- /about author --><strong></strong>
                        

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
										<a href="prev_admin_comp.php?IDempleado=<?php echo $el_usuario ?>" class="btn btn-success">Regresar</a>
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
											<label class="control-label no-margin text-semibold">Tipo de Relación:</label>
											<div><?php   if($row_evaluacion['IDtipo'] == 1) {echo 'Autoevaluacion';}
													else if($row_evaluacion['IDtipo'] == 2) {echo 'Jefe';}
													else if($row_evaluacion['IDtipo'] == 3) {echo 'Colaborador';}
													else if($row_evaluacion['IDtipo'] == 4) {echo 'Par';}
													else if($row_evaluacion['IDtipo'] == 5) {echo 'Cliente';}
											?></div>
										</div>

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
										 <li>&nbsp; <?php echo $pond_tipo1; ?>% Autoevaluación</li>
										 <li>&nbsp; <?php echo $pond_tipo2; ?>% Jefe</li>
										 <li>&nbsp; <?php echo $pond_tipo3; ?>% Pares</li>
										 <li>&nbsp; <?php echo $pond_tipo4; ?>% Colaboradores</li>
										 <li>&nbsp; <?php echo $pond_tipo5; ?>% Clientes</li>
										 <li><strong> 100% Total</strong></li>
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