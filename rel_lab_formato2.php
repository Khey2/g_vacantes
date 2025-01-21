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
$query_asesoria = "SELECT rel_lab_etapas.IDasesoria_etapa, rel_lab_etapas.IDasesoria, rel_lab_etapas.IDetapa, rel_lab_etapas.IDestatus, rel_lab_etapas.IDempleado_jefe, rel_lab_etapas.IDempleado_rh, rel_lab_etapas.IDempleado_testigo, rel_lab_etapas.file, rel_lab_etapas.fecha_captura, rel_lab_etapas.fecha_inicio, rel_lab_etapas.hora_inicio, rel_lab_etapas.fecha_fin, rel_lab_etapas.texto_esperado, rel_lab_etapas.texto_observado, rel_lab_etapas.texto_acuerdos, rel_lab_etapas.texto_resultados, rel_lab_etapas.observaciones, rel_lab_asesorias.IDempleado, rel_lab_asesorias.emp_paterno, rel_lab_asesorias.emp_materno, rel_lab_asesorias.fecha_antiguedad, rel_lab_asesorias.emp_nombre, rel_lab_asesorias.denominacion, rel_lab_asesorias.rfc, rel_lab_asesorias.IDpuesto, rel_lab_asesorias.IDsucursal, rel_lab_asesorias.IDarea, rel_lab_asesorias.anio, rel_lab_asesorias.IDmatriz, rel_lab_asesorias.IDestatus, rel_lab_etapas.politica1, rel_lab_etapas.politica2, rel_lab_etapas.politica3, rel_lab_etapas.politica4, rel_lab_asesorias.IDmotivo, rel_lab_tipos.motivo, rel_lab_tipos.instrucciones, rel_lab_tipos.tiempo, vac_areas.area, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion FROM rel_lab_etapas LEFT JOIN rel_lab_asesorias ON rel_lab_etapas.IDasesoria = rel_lab_asesorias.IDasesoria LEFT JOIN rel_lab_tipos ON rel_lab_asesorias.IDmotivo = rel_lab_tipos.IDmotivo LEFT JOIN vac_matriz ON rel_lab_asesorias.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON rel_lab_asesorias.IDarea = vac_areas.IDarea WHERE rel_lab_etapas.IDasesoria_etapa = $IDasesoria_etapa";
mysql_query("SET NAMES 'utf8'");
$asesoria = mysql_query($query_asesoria, $vacantes) or die(mysql_error());
$row_asesoria = mysql_fetch_assoc($asesoria);
$totalRows_asesoria = mysql_num_rows($asesoria);

$empleado = $row_asesoria['emp_paterno']." ".$row_asesoria['emp_materno']." ".$row_asesoria['emp_nombre'];
$noempleado = $row_asesoria['IDempleado'];
$denominacion = $row_asesoria['denominacion'];
$sucursal = $row_asesoria['matriz'];
$sucursal_cv = strtoupper($row_asesoria['matriz_cv']);
$sucursal_direccion = strtoupper($row_asesoria['direccion']);
$area = $row_asesoria['area'];
$hora_inicio = $row_asesoria['hora_inicio'];
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
$ini_cont_d = date('d', strtotime($row_asesoria['fecha_inicio']));
$ini_cont_m_ = date('m', strtotime($row_asesoria['fecha_inicio']));
$ini_cont_y = date('Y', strtotime($row_asesoria['fecha_inicio']));

  switch ($ini_cont_m_) {
	case 01:  $ini_cont_m = "ENERO";      break;     
    case 02:  $ini_cont_m = "FEBRERO";    break;    
    case 03:  $ini_cont_m = "MARZO";      break;    
    case 04:  $ini_cont_m = "ABRIL";      break;    
    case 05:  $ini_cont_m = "MAYO";       break;    
    case 06:  $ini_cont_m = "JUNIO";      break;    
    case 07:  $ini_cont_m = "JULIO";      break;    
    case 08:  $ini_cont_m = "AGOSTO";     break;    
    case 09:  $ini_cont_m = "SEPTIEMBRE"; break;    
    case 10: $ini_cont_m = "OCTUBRE";    break;    
    case 11: $ini_cont_m = "NOVIEMBRE";  break;    
    case 12: $ini_cont_m = "DICIEMBRE";  break;   
      }


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
<p align="center"><strong>ACTA ADMINISTRATIVA</strong></p>
<p>&nbsp; </p>
<p align="justify">EN <strong>'.$sucursal_cv.'</strong>, SIENDO LAS <strong>'.$hora_inicio.'</strong> DEL <strong>'.$ini_cont_d.' DEL MES DE '.$ini_cont_m.' DE '.$ini_cont_y.'</strong>, SE ENCUENTRAN REUNIDOS EN EL LUGAR QUE OCUPA EL DEPARTAMENTO DE RECURSOS HUMANOS DE SUCURSAL '.$sucursal.' DE LA EMPRESA <strong>IMPULSORA SAHUAYO S.A. DE C.V.</strong>, UBICADA EN EL DOMICLIO UBICADO EN <strong>'.$sucursal_direccion.'</strong>;EN LA QUE COMPARECEN: POR LA EMPRESA <strong>'.$IDempleado_jefe.'</strong> EN SU CARÁCTER DE REPRESENTANTE DE LA EMPRESA Y POR LA OTRA EL TRABAJADOR <strong>'.$empleado.'</strong>, QUIEN DESEMPEÑA EL PUESTO DE <strong>'.$denominacion.'</strong>; ASI MISMO, EN CARÁCTER DE TESTIGOS DE LA REDACCIÓN Y CONSTANCIA DE LA PRESENTE ACTA, COMPARECEN C.<strong>'.$IDempleado_rh.'</strong> Y C. <strong>'.$IDempleado_testigo.'</strong>, EN SU CARÁCTER DE: <strong>REPRESENTANTE DE RECURSOS HUMANOS Y TESTIGO.</strong></p>

<p align="justify">MANIFESTANDO LOS REUNIDOS EN LA PRESENTE ACTA QUE EL MOTIVO DE LA PRESENTE, ES CON EL FIN DE DAR CONSTACIA DE MANERA ESCRITA DEL SIGUIENTE HECHO: <strong>'.$texto_observado.'</strong></p>

<p align="justify">A CONTINUACIÓN EL JEFE DE RECURSOS HUMANOS DE LA EMPRESA DIJO: <strong>'.$texto_acuerdos.'</strong>, QUE DICHAS ACTIVIDAES LAS REALICEN CON DILIGENCIA Y ACORDE A LAS POLITICAS ESTABLECIDAS POR LA EMPRESA.</p>

<p align="justify">REDACTADA Y PRESENTADA EL ACTA ANTE LA VISTA DEL TRABAJADOR DANIEL MARROQUIN VAZQUEZ, EN SU CARÁCTER DE '.$denominacion.', SE PROCEDE A LEERLA UNA VEZ MÁS, EN VOZ ALTA MANIFESTANDOLE AL PRESENTE QUE SE LE CONCEDE EL USO DE LA PALABRA PARA QUE MANIFIESTE LO QUE A SU DERECHO CORRESPONDA: POR LO QUE EN ESTE ACTO MANIFIESTA QUE: <strong>'.$observaciones.'</strong>.</p>

<p align="justify">VISTO LO ASENTADO Y LEÍDO EN VOZ ALTA, POR TODOS LOS QUE INTERVIENEN EN LA REDACCIÓN DE LA PRESENTE ACTA, CON FUNDAMENTO EN EL ARTÍCULO 134 FRACCIÓN I, III, IV, DE LA LEY FEDERAL DEL TRABAJO, SE AMONESTA AL C. <strong>'.$empleado.'</strong> Y SE LE INVITA A DESEMPEÑAR CORRECTA Y DILIGENTEMENTE SU TRABAJO SO PENA DE QUE SE APLICEN LAS MEDIDAS DISCIPLINARIAS CORRESPONDIENTES, SE RATIFICA EN TODAS Y CADA UNA DE SUS PARTES EL CONTENIDO DE LA PRESENTE Y SE FIRMA POR LOS COMPARECIENTES AL CALCE Y AL MARGEN, PARA DAR DEBIDA CONSTANCIA DE LA LEGALIDAD Y VALIDEZ DE ESTE DOCUMENTO. SE DA POR CONCLUIDA LA PRESENTE ACTA ADMINISTRATIVA DE HECHOS A LAS <strong>'.$hora_inicio.'</strong> DEL <strong>'.$ini_cont_d.' DE '.$ini_cont_m.' DE '.$ini_cont_y.'</strong>.</p>

<table border="1" cellspacing="0" cellpadding="0" width="100%">
    <tbody>
        <tr>
            <td width="100%" colspan="2" valign="top"><p align="center"><strong>PARTICIPANTES</strong></p></td>
        </tr>
        <tr>
            <td width="50%" valign="top">
				<p>&nbsp; </p>
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
$nombreDelDocumento = $timestp." ".$noempleado." ".$sucursal." ACTA.pdf";
$bytes = file_put_contents("RELAB/".$nombreDelDocumento, $contenido);
$dompdf->stream($nombreDelDocumento);
?>