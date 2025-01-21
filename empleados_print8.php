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
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT con_empleados.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_puestos.denominacion FROM con_empleados LEFT JOIN vac_matriz ON con_empleados.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON con_empleados.IDpuesto = vac_puestos.IDpuesto WHERE con_empleados.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);

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
$estado =  $row_contratos['matriz'];
$puesto =  $row_contratos['denominacion'];
$direccion_empresa = $row_contratos['direccion'];
$fecha_alta = date('d/m/Y', strtotime($row_contratos['fecha_alta']));
$timestp = date("dmYHm"); // la fecha actual
$enletras = str_replace(',','',$row_contratos['b_sueldo_mensual']); 
$ubicacionfirma =  $row_contratos['matriz_cv'];

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
font-size: 14px;  
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
            margin: 60px 60px 80px 60px !important;
            padding: 0px 0px 0px 0px !important;
}
</style>
<body>
<br clear="ALL"/>

<p class="justificar"><img width="191" height="65" src="'.$logotipo.'" /></p>

<p><strong>'.$empresa.'</strong></p>
<p><strong>P R E S E N T E.</strong></p>
&nbsp; <br>

<p class="justificar"><strong>'.$row_contratos['a_nombre'].' '.$row_contratos['a_paterno'].' '.$row_contratos['a_materno'].'</strong>, por propio derecho, deseo manifestar que por así convenir a mis intereses y de manera voluntaria, con esta fecha doy por terminada la relación de trabajo que me unía con '.$empresa.', empresa en la cual me desempeñe con el puesto de '.$puesto.', relación que inicio desde el día '.$fecha_alta.' y que de manera formal hoy doy por terminada.</p>
&nbsp; <br>

<p class="justificar">Aprovecho la ocasión para agradecer muy cumplidamente las atenciones que me dispensaron y manifiesto que durante el tiempo que presté mis servicios a la empresa siempre me fueron cubiertas oportunamente todas y cada una de las prestaciones a que tuve derecho conforme a mi contrato individual de trabajo y a la ley, tales como vacaciones, prima vacacional, aguinaldo, utilidades, etc. Expreso de igual manera que durante dicho tiempo no sufrí enfermedad o accidente de trabajo alguno.</p>
&nbsp; <br>

<p class="justificar">Asimismo, dejo constancia para todos los efectos legales a que haya lugar, que nunca laboré tiempo extraordinario alguno, ni séptimos días, ni días festivos de descanso obligatorio, por lo que extiendo el más amplio finiquito que en derecho proceda a favor de mi único patrón '.$empresa.', no me reservo acción ni derecho que ejercitar en su contra.</p>
&nbsp; <br>

<p align="center"><strong>A T E N T A M E N T E.</strong></p>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
<p align="center">________________________________ </p>
<p align="center"><strong>'.$row_contratos['a_nombre'].' '.$row_contratos['a_paterno'].' '.$row_contratos['a_materno'].'</strong></p>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>
<p>RATIFICO DE CONFORMIDAD</p>
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
$nombreDelDocumento = "RENUNCIA ANTICIPADA ".date('dmY')." ".$IDempleado.".pdf";
$bytes = file_put_contents("CONTS/".$nombreDelDocumento, $contenido);
$dompdf->stream($nombreDelDocumento);
unlink("CONTS/".$nombreDelDocumento);
?>