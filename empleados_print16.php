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
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT con_empleados.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_puestos.denominacion, vac_puestos.IDarea FROM con_empleados LEFT JOIN vac_matriz ON con_empleados.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto WHERE con_empleados.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$elarea = $row_contratos ['IDarea'];

mysql_select_db($database_vacantes, $vacantes);
$query_documentos = "SELECT * FROM con_documentos WHERE FIND_IN_SET('$elarea',IDarea)";
mysql_query("SET NAMES 'utf8'");
$documentos = mysql_query($query_documentos, $vacantes) or die(mysql_error());
$row_documentos = mysql_fetch_assoc($documentos);
$totalRows_documentos = mysql_num_rows($documentos);

// convertir fecha en letras
$ini_cont_d = date('d', strtotime($row_contratos['fecha_alta']));
$ini_cont_m_ = date('m', strtotime($row_contratos['fecha_alta']));
$ini_cont_y = date('Y', strtotime($row_contratos['fecha_alta']));

// convertir fecha en letras
$nac_cont_d = date('d', strtotime($row_contratos['c_fecha_nacimiento']));
$nac_cont_m_ = date('m', strtotime($row_contratos['c_fecha_nacimiento']));
$nac_cont_y = date('Y', strtotime($row_contratos['c_fecha_nacimiento']));


switch ($ini_cont_m_) {
case '01':  $ini_cont_m = "enero";      break;     
case '02':  $ini_cont_m = "febrero";    break;    
case '03':  $ini_cont_m = "marzo";      break;    
case '04':  $ini_cont_m = "abril";      break;    
case '05':  $ini_cont_m = "mayo";       break;    
case '06':  $ini_cont_m = "junio";      break;    
case '07':  $ini_cont_m = "julio";      break;    
case '08':  $ini_cont_m = "agosto";     break;    
case '09':  $ini_cont_m = "septiembre"; break;    
case '10': $ini_cont_m = "octubre";    break;    
case '11': $ini_cont_m = "noviembre";  break;    
case '12': $ini_cont_m = "diciembre";  break;   
}

switch ($nac_cont_m_) {
case '01':  $nac_cont_m = "enero";      break;     
case '02':  $nac_cont_m = "febrero";    break;    
case '03':  $nac_cont_m = "marzo";      break;    
case '04':  $nac_cont_m = "abril";      break;    
case '05':  $nac_cont_m = "mayo";       break;    
case '06':  $nac_cont_m = "junio";      break;    
case '07':  $nac_cont_m = "julio";      break;    
case '08':  $nac_cont_m = "agosto";     break;    
case '09':  $nac_cont_m = "septiembre"; break;    
case '10': $nac_cont_m = "octubre";    break;    
case '11': $nac_cont_m = "noviembre";  break;    
case '12': $nac_cont_m = "diciembre";  break;   
}

$date_a = new DateTime($row_contratos['fecha_alta']);
$date_b = new DateTime($row_contratos['c_fecha_nacimiento']);
$diff_c = $date_a->diff($date_b);
$periodo_d =  $diff_c->y;

$el_empleado = $row_contratos['a_paterno']." ".$row_contratos['a_materno']." ".$row_contratos['a_nombre'];
$ubicacionfirma =  $row_contratos['matriz_cv'];
$estado =  $row_contratos['matriz'];
$direccion_empresa =  $row_contratos['direccion'];
$fecha_alta = date('d/m/Y', strtotime($row_contratos['fecha_alta']));
$timestp = date("dmYHm"); // la fecha actual
$enletras = str_replace(',','',$row_contratos['b_sueldo_mensual']); 

if ($row_contratos['IDempresa'] == 1) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';}
elseif ($row_contratos['IDempresa'] == 2) {$empresa = 'IMPULSORA SAHUAYO S.A. DE C.V.';} 
elseif ($row_contratos['IDempresa'] == 3) {$empresa = 'PERINTO S.A. DE C.V.';}
else{ $empresa = 'SIN DETERMINAR';}
 
if ($row_contratos['IDempresa'] == 1 OR $row_contratos['IDempresa'] == 2) {$rep_legal = $row_variables['rep_legal'];} else {$rep_legal = 'Alejandro Barrios Uribe';} 

if($row_contratos['IDnacionalidad'] == 1) {$nacionalidad = "Mexicana";} else {$nacionalidad = "Extranjera";}
if($row_contratos['a_sexo'] == 1) {$sexo = "Masculino";} else {$sexo = "Femenino";}
if($row_contratos['a_estado_civil'] == 1) {$edo_civil = "Soltero";} else {$edo_civil = "Casado";}$IDestado = $row_contratos['IDestado'];

if ($row_contratos['IDempresa'] == 1 OR $row_contratos['IDempresa'] == 2) {$logotipo = 'CONTS/logo.jpg';} else {$logotipo = 'https://www.gestionvacantes.com/CONTS/logo2.jpg';} 

$body = '<!DOCTYPE html>
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
 @page {
            margin: 0px 30px 0px 30px !important;
            padding: 0px 0px 0px 0px !important;
}
</style>
<body>
<br clear="ALL"/>

<p class="justificar"><img width="191" height="65" src="'.$logotipo.'" /></p>
<p align="center"><strong>ACUSE DE CONOCIMIENTO DE NORMATIVIDAD INTERNA DE IMPULSORA SAHUAYO S.A. DE C.V</strong></p>

<p align="right">'.$ubicacionfirma.', a '.$ini_cont_d.' de '.$ini_cont_m.' de '.$ini_cont_y.'.</p>

<p>
Por medio del presente declaro haber le&#237;do y entendido los documentos normativos señalados a continuaci&#243;n:</p>

<table width="100%" border="1">
  <tr>
    <td width="80%">Nombre del Documento</td>
    <td width="20%">Tipo</td>
</tr>';
$body .= '<tbody>';
do {
$body .= '<tr>';
$body .= '<td>';
$body .=  $row_documentos['documento'];
$body .= '</td>';
$body .= '<td>';
$body .=  iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $row_documentos['area']);
$body .= '</td>';
} while ($row_documentos = mysql_fetch_assoc($documentos));
$body .= '</tr>
</tbody>
</table>


<p>As&#237; mismo, me comprometo a apegarme a los lineamientos establecidos con el fin de desempe&#241;ar correctamente mis labores en beneficio de la empresa.</p>

<p>Firmo de conformidad para que la empresa haga uso de este documento para los fines que considere pertinentes.</p>

<table border="0" cellspacing="0" cellpadding="0" width="60%" align="center">
    <tr>
      <td width="45%" valign="top"><p align="center">
        <strong>&quot;EL TRABAJADOR&quot;</strong> <br>
        &nbsp; <br>
        <strong>___________________________</strong><br>
        <strong>C. ' .$row_contratos['a_nombre'].' '.$row_contratos['a_paterno'].' '.$row_contratos['a_materno'].'</strong><br>
      </td>
</table>


</body>';
include_once "global_assets/dompdf/autoload.inc.php";
use Dompdf\Dompdf;
$dompdf = new Dompdf();
header('Content-Type: text/html; charset=UTF-8');
$dompdf->loadHtml($body);
$dompdf->set_option('enable_html5_parser', TRUE);
$dompdf->set_option('isRemoteEnabled', TRUE);
$dompdf->setPaper('letter');
$dompdf->render();
$contenido = $dompdf->output();
$nombreDelDocumento = "ACUSE POLITICAS Y PROC ".date('dmY')." ".$IDempleado.".pdf";
$bytes = file_put_contents("CONTS/".$nombreDelDocumento, $contenido);
$dompdf->stream($nombreDelDocumento);
unlink("CONTS/".$nombreDelDocumento);
?>