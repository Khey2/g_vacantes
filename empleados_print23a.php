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
  set_time_limit(0);

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
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

//variables recuperadas
$IDempleado = $_POST['IDempleado'];
$periodo = $_POST['periodo'];
$diasd = $_POST['diasd'];
$diasr = $_POST['diasr'];
$fecha1 = $_POST['fecha1'];
$fecha2 = $_POST['fecha2'];
$diasp = $_POST['diasp'];

mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT prod_activos.*, vac_matriz.matriz, vac_matriz.matriz_cv, vac_matriz.direccion, vac_puestos.IDarea FROM prod_activos LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$elarea = $row_contratos ['IDarea'];

$el_empleado = $row_contratos['emp_paterno']." ".$row_contratos['emp_materno']." ".$row_contratos['emp_nombre'];
$logotipo = 'CONTS/logo.jpg';
$ubicacionfirma =  $row_contratos['matriz_cv'];


// convertir fecha en letras
$eldiafecha = date('d', strtotime($fecha2));
$elmesfecha = date('m', strtotime($fecha2));
$elaniofecha = date('Y', strtotime($fecha2));


switch ($elmesfecha) {
case '01':  $elmes_fecha = "enero";      break;     
case '02':  $elmes_fecha = "febrero";    break;    
case '03':  $elmes_fecha = "marzo";      break;    
case '04':  $elmes_fecha = "abril";      break;    
case '05':  $elmes_fecha = "mayo";       break;    
case '06':  $elmes_fecha = "junio";      break;    
case '07':  $elmes_fecha = "julio";      break;    
case '08':  $elmes_fecha = "agosto";     break;    
case '09':  $elmes_fecha = "septiembre"; break;    
case '10': $elmes_fecha = "octubre";    break;    
case '11': $elmes_fecha = "noviembre";  break;    
case '12': $elmes_fecha = "diciembre";  break;   
}

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
            margin: 0px 60px 0px 60px !important;
            padding: 0px 0px 0px 0px !important;
}
</style>
<body>
<br clear="ALL"/>

<p class="justificar"><img width="191" height="65" src="'.$logotipo.'" /></p>
<p align="center"><strong>Impulsora Sahuayo S.A. de C.V.</strong></p>
<p align="center"><strong>Carta de vacaciones pendientes</strong></p>
&nbsp; <br>


<p align="right">'.$ubicacionfirma.', a '.$eldiafecha.' de '.$elmes_fecha.' de '.$elaniofecha.'.</p>

&nbsp; <br>

<p>De: Recursos Humanos.</p>
<p>Para: C. ' .$el_empleado.'.</p>
<p>Puesto: ' .$row_contratos['denominacion'].'.</p>
<p>Número de Empleado: ' .$row_contratos['IDempleado'].'.</p>
&nbsp; <br>

<p  class="justificar">Por medio del presente le informamos que tiene número de días días de vacaciones pendientes de disfrutar correspondientes al periodo '.$periodo.' mismos que expiran el próximo '.$fecha1.'.</p>

<p  class="justificar">Por tal motivo deberá programar con su Jefe Inmediato la fecha en que disfrutará estos días antes de su vencimiento, de lo contrario, prescribirán conforme los artículos 79 y 81 de la Ley Federal del Trabajo y la política de vacaciones expedida por la empresa.</p>


<table border="1" cellspacing="0" cellpadding="0" width="80%" align="center">
	<tr>
	<td width="25%"><strong><p align="center">Periodo</p></strong></td>
	<td width="25%"><strong><p align="center">Dias de Periodo</p></strong></td>
	<td width="25%"><strong><p align="center">Dias Disfrutados</p></strong></td>
	<td width="25%"><strong><p align="center">Saldo</p></strong></td>
	</tr>
	<tr>
	<td><p align="center">'.$periodo.'</p></td>
	<td><p align="center">'.$diasp.'</p></td>
	<td><p align="center">'.$diasd.'</p></td>
	<td><p align="center">'.$diasr.'</p></td>
	</tr>
</table>

&nbsp; <br>
<p>Sin más por el momento, quedamos a sus órdenes para cualquier aclaración al respecto.</p>
&nbsp; <br>
&nbsp; <br>
&nbsp; <br>

<table border="0" cellspacing="0" cellpadding="0" width="80%" align="center">
    <tr>
      <td width="25%" valign="top"><p align="center">
	  <strong>Atentamente</strong> <br>
		&nbsp; <br>
		&nbsp; <br>
		&nbsp; <br>
		&nbsp; <br>
 	    <strong>___________________________</strong><br>
 	    Recursos Humanos<br>
 	    Nombre y Firma
		</td>
      <td width="13%" valign="top"><p align="center"><p align="center" class="blanco"> _______</td>
      <td width="25%" valign="top"><p align="center">
        <strong>Enterado</strong> <br>
        &nbsp; <br>
        &nbsp; <br>
        &nbsp; <br>
        &nbsp; <br>
        <strong>___________________________</strong><br>
        C. ' .$el_empleado.'<br>
	    &nbsp;
      </td>
      <td width="12%" valign="top"><p align="center"><p align="center" class="blanco"> _______</td>
      <td width="25%" valign="top"><p align="center">
	  <strong>Enterado</strong> <br>
		&nbsp; <br>
		&nbsp; <br>
		&nbsp; <br>
		&nbsp; <br>
 	    <strong>___________________________</strong><br>
 	    Jefe Inmediato<br>
 	    Nombre y Firma
		</td>
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
$nombreDelDocumento = "ANEXO 4. CARTA VACACIONES PENDIENTES ".date('dmY')." ".$IDempleado.".pdf";
$bytes = file_put_contents("CONTS/".$nombreDelDocumento, $contenido);
$dompdf->stream($nombreDelDocumento);
unlink("CONTS/".$nombreDelDocumento);
?>