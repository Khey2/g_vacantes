<?php require_once('Connections/vacantes.php'); ?>
<?php

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
$anio = $row_variables['anio'];
$fecha = date("Y-m-d");
$el_mes = date("m") - 1;
if ($el_mes == 0){$el_mes = 12;}



$meses = array("01" => "enero", "02" => "febrero", "03" => "marzo", "04" => "abril", "05" => "mayo", "06" => "junio", "07" => "julio", "08" => "agosto", "09" => "septiembre", "10" => "octubre", "11" => "noviembre", "12" => "diciembre");

$date_a = new DateTime($fecha);
$date_a->modify('-20 day');
$DateA = $date_a->format('Y-m-d');
$DateB = date('Y-m-d');
$DateA1 = date( 'd' , strtotime($DateA)); 
$DateA2 = date( 'm' , strtotime($DateA));
$DateB1 = date( 'd' , strtotime($DateB));
$DateB2 = date( 'm' , strtotime($DateB));

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN (1,2,3,4,5,6,8,9,10,11,12,13,14,15,16)"; 
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);

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
      }

//se envia correo
require 'assets/PHPMailer/PHPMailerAutoload.php';
require 'assets/dias.php';

do {
	
$la_matriz = $row_matriz['IDmatriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT prod_activosfaltas.IDempleado, prod_activosfaltas.emp_paterno, prod_activosfaltas.emp_materno, prod_activosfaltas.estado, prod_activosfaltas.emp_nombre, prod_activosfaltas.denominacion, prod_activosfaltas.IDmatriz, prod_activosfaltas.IDsucursal, prod_activosfaltas.fecha_alta, prod_activosfaltas.IDarea, prod_activosfaltas.fecha_baja, prod_activosfaltas.IDpuesto, prod_activosfaltas.descripcion_nomina FROM prod_activosfaltas WHERE prod_activosfaltas.IDpuesto NOT IN ( 216 ) AND prod_activosfaltas.IDmatriz = $la_matriz AND prod_activosfaltas.IDarea IN ( 5, 6 ) AND ( prod_activosfaltas.fecha_baja = '0000-00-00' OR ( DATE ( prod_activosfaltas.fecha_baja )  BETWEEN '$DateA' AND '$DateB' )) ORDER BY prod_activosfaltas.denominacion ASC";
mysql_query("SET NAMES 'utf8'");
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);

$query_gerente = "SELECT * FROM vac_usuarios WHERE IDmatriz = $la_matriz AND correo_semanal = 1"; 
$gerente = mysql_query($query_gerente, $vacantes) or die(mysql_error());
$row_gerente = mysql_fetch_assoc($gerente);
$totalRows_gerente = mysql_num_rows($gerente);
$elGerente = $row_gerente['usuario_correo'];

$query_jefeventas = "SELECT * FROM vac_usuarios WHERE IDmatriz = $la_matriz AND correo_semanal = 6"; 
$jefeventas = mysql_query($query_jefeventas, $vacantes) or die(mysql_error());
$row_jefeventas = mysql_fetch_assoc($jefeventas);
$totalRows_jefeventas = mysql_num_rows($jefeventas);
$elJefeVentas = $row_jefeventas['usuario_correo'];

$query_jeferh = "SELECT * FROM vac_usuarios WHERE IDmatriz = $la_matriz AND correo_semanal = 5"; 
$jeferh = mysql_query($query_jeferh, $vacantes) or die(mysql_error());
$row_jeferh = mysql_fetch_assoc($jeferh);
$totalRows_jeferh = mysql_num_rows($jeferh);
$elJefeRH = $row_jeferh['usuario_correo'];

$mail = new PHPMailer;
//$mail->isSMTP();
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';
$mail->Host = "smtp.office365.com";
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->SMTPAutoTLS = false;
$mail->SMTPSecure = '';
$mail->Username = "reporte_diario@gestionvacantes.com";
$mail->Password = "parazoom2020!";
$mail->setFrom('reporte_diario@gestionvacantes.com', 'Sistema de Gestion de Recursos Humanos');
$mail->addReplyTo('reporte_diario@gestionvacantes.com', 'Admin');
$mail->AddAddress('jacardenas@sahuayo.mx');
$mail->AddAddress('cgaona@sahuayo.mx');
$mail->AddAddress('rtejeda@sahuayo.mx');
$mail->AddAddress('gemendiola@sahuayo.mx');
$mail->AddAddress('dmmartinez@sahuayo.mx');
$mail->AddAddress('jlgomez@sahuayo.mx');
$mail->AddAddress($elGerente);
$mail->AddAddress($elJefeRH);
if($totalRows_jefeventas > 1) {$mail->AddAddress($elJefeVentas);}
$mail->Subject = 'Validación Quincenal - Fuerza de Ventas';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Validación Quincenal - Fuerza de Ventas';

$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
<!--[if !mso]-->
<!-- -->
<link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet">
<!--<![endif]-->
<title>
Sistema de Gestion de Recursos Humanos</title>
<style type="text/css">
body{width: 100%; background-color: #ffffff; margin: 0; padding: 0; -webkit-font-smoothing: antialiased;
mso-margin-top-alt: 0px; mso-margin-bottom-alt: 0px; mso-padding-alt: 0px 0px 0px 0px;}
p, h1, h2, h3, h4{margin-top: 0; margin-bottom: 0; padding-top: 0; padding-bottom: 0;}
span.preheader{display: none; font-size: 1px;}
html{width: 100%;}
table{font-size: 12px; border: 0; } } @media only screen and (max-width: 640px) { 
.main-header {font-size: 20px !important; } 
.main-section-header {font-size: 28px !important; } 
.show {display: block !important; } 
.hide {display: none !important; } 
.align-center {text-align: center !important; } 
.no-bg {background: none !important; } 
.main-image img {width: 440px !important;height: auto !important; } 
.divider img {width: 440px !important; } 
.container590 {width: 440px !important;} 
.container580 {width: 400px !important; } 
.main-button {width: 220px !important; } 
.section-img img {width: 320px !important;height: auto !important; } 
.team-img img {width: 100% !important;height: auto !important; }    } @media only screen and (max-width: 479px) { 
.main-header {font-size: 18px !important; } 
.main-section-header {font-size: 26px !important; } 
.divider img {width: 280px !important; } 
.container590 {width: 280px !important;} 
.container590 {width: 280px !important;} 
.container580 {width: 260px !important;} 
.section-img img {width: 280px !important;height: auto !important; } } 
a:link {  color: #888888;  background-color: transparent;  text-decoration: none;}
a:visited {  color: #888888;  background-color: transparent;  text-decoration: none;}
a:hover {  color: #888888;  background-color: transparent;  text-decoration: underline;}
a:active {  color: #888888;  background-color: transparent;  text-decoration: underline;}
</style>
<!--[if gte mso 9]><style type=text/css> body { font-family: arial, sans-serif!important; } </style><![endif]-->
</head>
<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<!-- pre-header -->
<table style="display:none!important;">
    <tr>
        <td>
<div style="overflow:hidden;display:none;font-size:1px;color:#ffffff;line-height:1px;font-family:Arial;maxheight:0px;max-width:0px;opacity:0;">Sistema de Gestion de Recursos Humanos</div>
        </td>
    </tr>
</table>
<!-- pre-header end -->
<!-- header -->
<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">
    <tr>
        <td align="center">
<table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">
<tr>
    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;
</td>
</tr>
<tr>
    <td align="center">
        <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">
<tr>
<td align="center" height="70" style="height:70px;">
    <a href="" style="display: block; border-style: none !important; border: 0 !important;">
<img width="100" border="0" style="display: block; width: 100px;" src="https://gestionvacantes.com/global_assets/images/logo_dark.png" alt="" />
</a>
</td>
</tr>
<tr>
<td align="center">&nbsp;
</td>
</tr>
        </table>
    </td>
</tr>
<tr>
    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;
</td>
</tr>
</table>
        </td>
    </tr>
</table>
<!-- end header -->
<!-- big image section -->
<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">
    <tr>
        <td align="center">
<table border="0" align="center" width="80%" cellpadding="0" cellspacing="0" class="container590">
<tr>
  <td align="center" style="color: #343434; font-size: 24px; font-family: Quicksand, Arial, Calibri, sans-serif; font-weight:700;letter-spacing: 3px; line-height: 35px;" class="main-header">
        <!-- section text ======-->
        <div style="line-height: 35px">Empleados <span style="color: #d8020a;">Fuerza de Ventas</span></div>
    </td>
</tr>
<tr>
    <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;
</td>
</tr>
<tr>
    <td align="center">
        <table border="0" width="40" align="center" cellpadding="0" cellspacing="0" bgcolor="eeeeee">
<tr>
<td height="2" style="font-size: 2px; line-height: 2px;">&nbsp;
</td>
</tr>
        </table>
    </td>
</tr>
<tr>
    <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;
</td>
</tr>
<tr>
    <td align="left">
        <table width="80%" align="center" cellpadding="0" cellspacing="0">
<tr>
<td align="left" style="color: #888888; font-size: 16px; font-family: "Work Sans", Arial, Calibri, sans-serif; line-height: 24px;">
    <!-- section text ======-->
    <p style="line-height: 24px; margin-bottom:15px;">
	A continuación se muestran los empleados del área de Ventas en '.ucfirst(strtolower($row_matriz['matriz'])).' con corte al <strong>'.$DateB1.' de '.$meses[$DateB2].'</strong> del '.$anio.'.<br />
	No olvides validar la plantilla y notificar cualquier cambio al área de Recursos Humanos de la Sucursal.<br />
	El reporte se genera de forma automática y muestra el estado (activos, suspendidos y bajas) con corte quicenal.<br /><br />
	
<table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">
 <thead>
 <tr>
 <th>No.</th>
 <th>No. Emp.</th>
 <th>Nombre Completo</th>
 <th>Puesto</th>
 <th>Estatus</th>
 <th>Fecha Alta</th>
 <th>Fecha Baja</th>
 </tr>
 </thead>';
$body .= '<tbody>';	

	$numero = 1;
	do {
	if($row_activos['fecha_baja'] != '0000-00-00') {$fecha_baja = date( 'd/m/Y', strtotime($row_activos['fecha_baja'])); }  else {$fecha_baja = "No aplica";}
    if($row_activos['estado'] == 4) {$estado = "Suspendido";} else if($row_activos['estado'] == 3) {$estado = "Activo";} else if($row_activos['estado'] == 2) {$estado = "Baja";} else if($row_activos['estado'] == 1) {$estado = "Activo";} else { $estado = "n/a";} 
    if($row_activos['estado'] == 4 or $row_activos['estado'] == 2) {$color = "#d8020a;";} else {$color = "#888888;";} 


	$fecha_alta = date( 'd/m/Y', strtotime($row_activos['fecha_alta'])); 
	$body .= '<tr>';
	$body .= '<td align="center">';
	$body .=  $numero;
	$body .= '</td>';
	$body .= '<td align="center">';
	$body .=  $row_activos['IDempleado'];
	$body .= '</td>';
	$body .= '<td>';
	$body .=  $row_activos['emp_paterno'].' '.$row_activos['emp_materno'].' '.$row_activos['emp_nombre'];
	$body .= '</td>';
	$body .= '<td>';
	$body .=  $row_activos['denominacion'];
	$body .= '</td>';
	$body .= '<td align="center"><span style="color: '.$color.'">';
	$body .=  $estado;
	$body .= '</span></td>';
	$body .= '<td align="center">';
	$body .=  $fecha_alta;
	$body .= '</td>';
	$body .= '<td align="center">';
	$body .=  $fecha_baja;
	$body .= '</td>';
	$body .= '</tr>';
	$numero = $numero+1;
	} while($row_activos = mysql_fetch_array($activos));

$body .= '
</tbody>
</table>
<br/>
</p>

    Puedes consultar en detalle en el SGRH <strong><a href="https://www.gestionvacantes.com/plantilla_activos.php" >dando clic aqui.</a></strong><br /><br />

    	Para cualquier duda al respecto contactanos por correo a <a href="mailtio:jacardenas@sahuayo.mx" class="color: #000000;">jacardenas@sahuayo.mx</a><br /><br />
		<p style="line-height: 24px">Saludos cordiales,</br>Recursos Humanos Sahuayo</p>
    </td>
    </tr>
</table>
    </td>
</tr>
    </table>
</td>
    </tr>
    <tr>
<td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>
    </tr>
</table>
<!-- end section -->
<!-- main section -->
<!-- end section -->
<!-- contact section -->
<!-- end section -->
<!-- footer ====== -->
  <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="f4f4f4">
    <tr>
<td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
    </tr>
    <tr>
<td align="center">
    <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">
<tr>
    <td>
<table border="0" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="container590">
    <tr>
<td align="left" style="color: #aaaaaa; font-size: 14px; font-family: "Work Sans", Arial, Calibri, sans-serif; line-height: 24px;">
    <div style="line-height: 24px;"><span style="color: #333333;">Sistema de Gestion de Recursos Humanos 2022</span></div>
</td>
    </tr>
</table>
<table border="0" align="left" width="5" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="container590">
    <tr>
<td height="20" width="5" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
    </tr>
</table>
<table border="0" align="right" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;" class="container590">
<tr>
<td align="center">
    <table align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td align="center"></td>
</tr>
    </table>
</td>
    </tr>
</table>
    </td>
</tr>
    </table>
</td>
    </tr>
    <tr>
<td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>
    </tr>
</table>
<!-- end footer ====== -->
</body>
</html>';
$mail->Body = $body;
echo $body;
if (!$mail->send()) {    echo "Mailer Error: " . $mail->ErrorInfo; } 

} while($row_matriz = mysql_fetch_array($matriz))
?>