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
$query_puestos = "SELECT * FROM rel_lab_tipos";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$IDasesoria_etapa = $_GET['IDasesoria_etapa'];
mysql_select_db($database_vacantes, $vacantes);
$query_asesoria = "SELECT rel_lab_etapas.IDasesoria_etapa, rel_lab_etapas.IDasesoria, rel_lab_etapas.IDetapa, rel_lab_etapas.IDestatus, rel_lab_etapas.IDempleado_jefe, rel_lab_etapas.IDempleado_rh, rel_lab_etapas.IDempleado_testigo, rel_lab_etapas.file, rel_lab_etapas.fecha_captura, rel_lab_etapas.fecha_inicio, rel_lab_etapas.fecha_fin, rel_lab_etapas.texto_esperado, rel_lab_etapas.texto_observado, rel_lab_etapas.texto_acuerdos, rel_lab_etapas.texto_resultados, rel_lab_etapas.observaciones, rel_lab_asesorias.IDempleado, rel_lab_asesorias.emp_paterno, rel_lab_asesorias.emp_materno, rel_lab_asesorias.fecha_antiguedad, rel_lab_asesorias.emp_nombre, rel_lab_asesorias.denominacion, rel_lab_asesorias.rfc, rel_lab_asesorias.IDpuesto, rel_lab_asesorias.IDsucursal, rel_lab_asesorias.IDarea, rel_lab_asesorias.anio, rel_lab_asesorias.IDmatriz, rel_lab_asesorias.IDestatus, rel_lab_etapas.politica1, rel_lab_etapas.politica2, rel_lab_etapas.politica3, rel_lab_etapas.politica4, rel_lab_asesorias.IDmotivo, rel_lab_tipos.motivo, rel_lab_tipos.instrucciones, rel_lab_tipos.tiempo, vac_areas.area, vac_matriz.matriz FROM rel_lab_etapas LEFT JOIN rel_lab_asesorias ON rel_lab_etapas.IDasesoria = rel_lab_asesorias.IDasesoria LEFT JOIN rel_lab_tipos ON rel_lab_asesorias.IDmotivo = rel_lab_tipos.IDmotivo LEFT JOIN vac_matriz ON rel_lab_asesorias.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON rel_lab_asesorias.IDarea = vac_areas.IDarea WHERE rel_lab_etapas.IDasesoria_etapa = $IDasesoria_etapa";
mysql_query("SET NAMES 'utf8'");
$asesoria = mysql_query($query_asesoria, $vacantes) or die(mysql_error());
$row_asesoria = mysql_fetch_assoc($asesoria);
$totalRows_asesoria = mysql_num_rows($asesoria);

$empleado = $row_asesoria['emp_paterno']." ".$row_asesoria['emp_materno']." ".$row_asesoria['emp_nombre'];
$noempleado = $row_asesoria['IDempleado'];
$sucursal = $row_asesoria['matriz'];
$area = $row_asesoria['area'];
$inicio = date('d/m/Y', strtotime($row_asesoria['fecha_inicio']));
$fin = date('d/m/Y', strtotime($row_asesoria['fecha_fin']));
$texto_esperado = $row_asesoria['texto_esperado'];
$texto_observado = $row_asesoria['texto_observado'];
$texto_acuerdos = $row_asesoria['texto_acuerdos'];
$texto_resultados = $row_asesoria['texto_resultados'];
$observaciones = $row_asesoria['observaciones'];
$IDempleado_rh =  $row_asesoria['IDempleado_rh'];
$IDempleado_testigo = $row_asesoria['IDempleado_testigo'];
$IDempleado_jefe = $row_asesoria['IDempleado_jefe'];


$timestp = date("dmYHm"); // la fecha actual


include_once "global_assets/dompdf/autoload.inc.php";
use Dompdf\Dompdf;
$dompdf = new Dompdf();
header('Content-Type: text/html; charset=UTF-8');
$dompdf->loadHtml('<!DOCTYPE html>
<meta charset="UTF-8"/>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
body {
font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
font-size: 12px;  
}
.justificar {
	text-align: justify;
}
.centrado {
	text-align: center;
}
.blanco {
	color: #FFF;
}
.saltopagina {
	page-break-after:always;
}
</style>
<body>
<p align="center"><strong> ASESORIA PARA MEJORAR </strong></p>
<p><strong>SUCURSAL:</strong> '.$sucursal.'<br/>
<strong>AREA:</strong> '.$area.'<br/>
<strong>No. EMPLEADO:</strong> '.$noempleado.'<br/>
<strong>EMPLEADO</strong>: '.$empleado.' <br/>
<strong>JEFE INMEDIATO</strong>: '.$IDempleado_jefe.'<br/>
<strong>FECHA</strong>: '.$inicio.' <br/>
<table border="1" cellspacing="0" cellpadding="0" width="100%">
    <tbody>
        <tr>
            <td width="100%" colspan="2" valign="top"><p align="center"><strong>EVIDENCIAS DE DESEMPEÑO / COMPORTAMIENTO</strong></p></td>
        </tr>
        <tr>
            <td width="50%" valign="top">
			<p align="center"><strong>ESPERADO</strong></p>
			<p>'.$texto_esperado.'</p>
			</td>
            <td width="50%" valign="top">
			<p align="center"><strong>OBSERVADO</strong></p>
			<p>'.$texto_observado.'</p>
			</td>
        </tr>
        <tr>
            <td width="50%" valign="top"></td>
            <td width="50%" valign="top"></td>
        </tr>
    </tbody>
</table>
<table border="1" cellspacing="0" cellpadding="0" width="100%">
    <tbody>
        <tr>
            <td width="80%" valign="top"><p align="center"><strong>ACCIONES</strong></p></td>
            <td width="20%" valign="top"><p align="center"><strong>FECHA COMPROMISO</strong></p></td>
        </tr>
        <tr>
            <td width="80%"><p>'.$texto_acuerdos.'</p></td>
            <td width="20%"><p align="center">'.$fin.'</p></td>
        </tr>
    </tbody>
</table>
<table border="1" cellspacing="0" cellpadding="0" width="100%">
    <tbody>
        <tr>
            <td width="100%" valign="top"><p align="center"><strong>OBSERVACIONES</strong></p></td>
        </tr>
        <tr>
            <td width="100%"><p>'.$observaciones.'</p></td>
        </tr>
    </tbody>
</table>
<table border="1" cellspacing="0" cellpadding="0" width="100%">
    <tbody>
        <tr>
            <td width="100%" colspan="2" valign="top"><p align="center"><strong>SEGUIMIENTO</strong></p></td>
        </tr>
        <tr>
            <td width="80%" valign="top"><p align="center"><strong>EVIDENCIAS / COMENTARIOS</strong></p></td>
            <td width="20%" valign="top"><p align="center"><strong>FECHA</strong></p></td>
        </tr>
        <tr>
            <td width="80%"><p>'.$texto_resultados.'</p></td>
            <td width="20%"><p>'.$fin.'</p></td>
        </tr>
    </tbody>
</table>
<p>&nbsp; </p>
<table border="1" cellspacing="0" cellpadding="0" width="100%">
    <tbody>
        <tr>
            <td width="100%" colspan="2" valign="top"><p align="center"><strong>PARTICIPANTES</strong></p></td>
        </tr>
        <tr>
            <td width="50%" valign="top">
				<p align="center">Estoy de acuerdo en los términos de la presente asesoría por lo que, en caso de no cumplir mis compromisos, se podrá terminar la relación laboral sin perjuicio para la empresa.</p>
				<p>&nbsp; </p>
				<p align="center">'.$empleado.'<br/>
				<strong>EMPLEADO</strong></p>
			</td>
            <td width="50%" valign="bottom">
				<p align="center">'.$IDempleado_jefe.'<br/>
				<strong>JEFE INMEDIATO SUPERIOR</strong></p>
			</td>
        </tr>
        <tr>
            <td width="50%" valign="bottom">
				<p>&nbsp; </p>
				<p>&nbsp; </p>
				<p align="center">'.$IDempleado_rh.'<br/>
				<strong>Recursos Humanos</strong></p>
			</td>
            <td width="50%" valign="bottom">
				<p align="center">'.$IDempleado_testigo.'<br/>
				<strong>TESTIGO</strong></p>
			</td>
        </tr>
    </tbody>
</table>');
$dompdf->set_option('enable_html5_parser', TRUE);
$dompdf->setPaper('letter');
$dompdf->render();
$contenido = $dompdf->output();
$nombreDelDocumento = $timestp." ".$noempleado." ".$sucursal." ASESORIA.pdf";
$bytes = file_put_contents("RELAB/".$nombreDelDocumento, $contenido);
$dompdf->stream($nombreDelDocumento);
?>