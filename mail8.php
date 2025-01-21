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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT DISTINCT vac_matriz.matriz as LaMatriz, vac_vacante.IDvacante, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.IDmatriz, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 GROUP BY vac_matriz.matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());

//se envia correo
require 'assets/PHPMailer/PHPMailerAutoload.php';
require 'assets/dias.php';

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
$mail->AddAddress('rtejeda@sahuayo.mx');
$mail->AddAddress('jgarcia@sahuayo.mx');
$mail->AddAddress('caarzate@sahuayo.mx');
$mail->AddAddress('gemendiola@sahuayo.mx');
$mail->AddAddress('dmmartinez@sahuayo.mx');
$mail->AddAddress('gortiz@sahuayo.mx');
$mail->AddAddress('jmcamacho@sahuayo.mx');
$mail->AddAddress('rerivas@sahuayo.mx');
$mail->AddAddress('dadzul@sahuayo.mx');
$mail->AddAddress('acortes@sahuayo.mx');
$mail->AddAddress('cgaona@sahuayo.mx');
$mail->AddAddress('lafloress@sahuayo.mx');
$mail->AddAddress('rramirez@sahuayo.mx');
$mail->AddAddress('jacardenas@sahuayo.mx');
$mail->Subject = 'Reporte Semanal de Vacantes de Logística';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Reporte Semanal de Vacantes de Logística';

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
table{font-size: 14px; border: 0; } } @media only screen and (max-width: 640px) { 
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
.section-img img {width: 280px !important;height: auto !important; } } </style>
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
<table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">
<tr>
  <td align="center" style="color: #343434; font-size: 24px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 3px; line-height: 35px;" class="main-header">
        <!-- section text ======-->
        <div style="line-height: 35px">Reporte de Vacantes <span style="color: #d8020a;">Semanal</span> <br />Logística</div>
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
        <table border="0" width="900" align="center" cellpadding="0" cellspacing="0" class="container590">
<tr>
<td align="left" style="color: #888888; font-size: 16px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">
    <!-- section text ======-->
    <p style="line-height: 24px; margin-bottom:15px;">A continuación encontrarás el reporte semanal de vacantes de los puestos de Chofer de VD, Chofer de Camioneta, Chofer de Rabón, Chofer de Torton y Ayudante de Reparto reportado en el Sistema de Gestión de Recursos Humanos (SGRH).<br />
    El estatus de vacantes es validado por los Jefes de Operaciones y de RH los días martes.</p>
	
<table  border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">
 <thead>
 <tr>
 <th>Matriz</th>
 <th>Total</th>
 <th>Chofer <br/>Camioneta VD</th>
 <th>Chofer <br/>Camioneta</th>
 <th>Chofer <br/>Rabón</th>
 <th>Chofer <br/>Torton</th>
 <th>Ayu. <br/>Reparto</th>
 <th>Aux. <br/>Alm.</th>
 <th><span style="color: #DB2B2B;">Aux. <br/>Alm. <br/>Pull Vac.</span></th>
 <th><span style="color: #DB2B2B;">Rabón y <br/>Torton <br/>Pull Vac.</span></th>
 </tr>
 </thead>';
$body .= '<tbody>';

					$Xactivas = 0; 
					$Xactivas1 = 0; 
					$Xactivas2 = 0; 
					$Xactivas3 = 0; 
					$Xactivas4 = 0; 
					$Xactivas5 = 0; 
					$Xactivas6 = 0; 
					$Xactivas7 = 0; 
					$Xactivas8 = 0; 


while($row_matriz = mysql_fetch_array($matriz)){
	
$LaMatriz = $row_matriz['IDmatriz'];
$NLaMatriz = $row_matriz['LaMatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_Area1 = "SELECT vac_vacante.IDvacante, count(vac_vacante.IDvacante) AS Cuenta, vac_puestos.IDarea, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON  vac_areas.IDarea = vac_vacante.IDarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDmatriz = '$LaMatriz' AND vac_vacante.IDpuesto IN (57)";
$Area1 = mysql_query($query_Area1, $vacantes) or die(mysql_error());
$row_Area1 = mysql_fetch_assoc($Area1);

mysql_select_db($database_vacantes, $vacantes);
$query_Area2 = "SELECT vac_vacante.IDvacante, count(vac_vacante.IDvacante) AS Cuenta, vac_puestos.IDarea, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON  vac_areas.IDarea = vac_vacante.IDarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDmatriz = '$LaMatriz' AND vac_vacante.IDpuesto IN (42)";
$Area2 = mysql_query($query_Area2, $vacantes) or die(mysql_error());
$row_Area2 = mysql_fetch_assoc($Area2);

mysql_select_db($database_vacantes, $vacantes);
$query_Area3 = "SELECT vac_vacante.IDvacante, count(vac_vacante.IDvacante) AS Cuenta, vac_puestos.IDarea, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON  vac_areas.IDarea = vac_vacante.IDarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDmatriz = '$LaMatriz' AND vac_vacante.IDpuesto IN (43)  AND vac_vacante.IDmotivo_v != 5";
$Area3 = mysql_query($query_Area3, $vacantes) or die(mysql_error());
$row_Area3 = mysql_fetch_assoc($Area3);

mysql_select_db($database_vacantes, $vacantes);
$query_Area4 = "SELECT vac_vacante.IDvacante, count(vac_vacante.IDvacante) AS Cuenta, vac_puestos.IDarea, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON  vac_areas.IDarea = vac_vacante.IDarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDmatriz = '$LaMatriz' AND vac_vacante.IDpuesto IN (44)  AND vac_vacante.IDmotivo_v != 5";
$Area4 = mysql_query($query_Area4, $vacantes) or die(mysql_error());
$row_Area4 = mysql_fetch_assoc($Area4);

mysql_select_db($database_vacantes, $vacantes);
$query_Area5 = "SELECT vac_vacante.IDvacante, count(vac_vacante.IDvacante) AS Cuenta, vac_puestos.IDarea, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON  vac_areas.IDarea = vac_vacante.IDarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDmatriz = '$LaMatriz' AND vac_vacante.IDpuesto = 41";
$Area5 = mysql_query($query_Area5, $vacantes) or die(mysql_error());
$row_Area5 = mysql_fetch_assoc($Area5);

mysql_select_db($database_vacantes, $vacantes);
$query_Area6 = "SELECT vac_vacante.IDvacante, count(vac_vacante.IDvacante) AS Cuenta, vac_puestos.IDarea, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON  vac_areas.IDarea = vac_vacante.IDarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDmatriz = '$LaMatriz' AND vac_vacante.IDpuesto IN (2, 18, 371, 281, 282, 313)";
$Area6 = mysql_query($query_Area6, $vacantes) or die(mysql_error());
$row_Area6 = mysql_fetch_assoc($Area6);

mysql_select_db($database_vacantes, $vacantes);
$query_Area7 = "SELECT vac_vacante.IDvacante, count(vac_vacante.IDvacante) AS Cuenta, vac_puestos.IDarea, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON  vac_areas.IDarea = vac_vacante.IDarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDmatriz = '$LaMatriz' AND vac_vacante.IDpuesto IN (2, 18, 371, 281, 282, 313) AND vac_vacante.IDmotivo_v = 5";
$Area7 = mysql_query($query_Area7, $vacantes) or die(mysql_error());
$row_Area7 = mysql_fetch_assoc($Area7);

mysql_select_db($database_vacantes, $vacantes);
$query_Area8 = "SELECT vac_vacante.IDvacante, count(vac_vacante.IDvacante) AS Cuenta, vac_puestos.IDarea, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON  vac_areas.IDarea = vac_vacante.IDarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDmatriz = '$LaMatriz' AND vac_vacante.IDpuesto IN (43,44) AND vac_vacante.IDmotivo_v = 5";
$Area8 = mysql_query($query_Area8, $vacantes) or die(mysql_error());
$row_Area8 = mysql_fetch_assoc($Area8);


					$Area1_total = $row_Area1['Cuenta'];
					$Area2_total = $row_Area2['Cuenta'];
					$Area3_total = $row_Area3['Cuenta'];
					$Area4_total = $row_Area4['Cuenta'];
					$Area5_total = $row_Area5['Cuenta'];
					$Area6_total = $row_Area6['Cuenta'];
					$Area7_total = $row_Area7['Cuenta'];
					$Area8_total = $row_Area8['Cuenta'];

					$activas = $Area1_total + $Area2_total + $Area3_total + $Area4_total + $Area5_total + $Area6_total;

					$Xactivas = $Xactivas + $activas; 
					$Xactivas1 = $Xactivas1 + $Area1_total; 
					$Xactivas2 = $Xactivas2 + $Area2_total; 
					$Xactivas3 = $Xactivas3 + $Area3_total; 
					$Xactivas4 = $Xactivas4 + $Area4_total; 
					$Xactivas5 = $Xactivas5 + $Area5_total; 
					$Xactivas6 = $Xactivas6 + $Area6_total; 
					$Xactivas7 = $Xactivas7 + $Area7_total; 
					$Xactivas8 = $Xactivas8 + $Area8_total; 

$body .= '<tr>';
$body .= '<td>';
$body .=  $NLaMatriz;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $activas;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Area1_total;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Area2_total;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Area3_total;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Area4_total;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Area5_total;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $Area6_total;
$body .= '</td>';
$body .= '<td align="center">';
$body .= '<span style="color: #DB2B2B;">'.$Area7_total.'</span>';
$body .= '</td>';
$body .= '<td align="center">';
$body .= '<span style="color: #DB2B2B;">'.$Area8_total.'</span>';
$body .= '</td>';
$body .= '</tr>';
}
$body .= '<tr>';
$body .= '<td><strong>TOTAL</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xactivas;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xactivas1;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xactivas2;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xactivas3;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xactivas4;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xactivas5;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xactivas6;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  '<span style="color: #DB2B2B;">'.$Xactivas7.'</span>';
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  '<span style="color: #DB2B2B;">'.$Xactivas8.'</span>';
$body .= '</strong></td>';
$body .= '</tr>';


$body .= '
</tbody>
</table>
<br/>
</p>
    <p style="line-height: 24px; margin-bottom:20px;">Puedes acceder al SGRH para revisar el detalle dando clic en el siguiente acceso.</p>
    <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="#d8020a" style="margin-bottom:20px;">
    <tr>
<td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
</tr>
    <tr>
<td align="center" style="color: #ffffff; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">
    <!-- main section button -->
<div style="line-height: 22px;"><a href="http://gestionvacantes.com/" style="color: #ffffff; text-decoration: none;">Ingresar</a></div>
</td>
</tr>
    <tr>
<td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
</tr>
</table>
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
<td align="left" style="color: #aaaaaa; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">
    <div style="line-height: 24px;"><span style="color: #333333;">Sistema de Gestion de Recursos Humanos 2024</span></div>
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
if (!$mail->send()) {    echo "Mailer Error: " . $mail->ErrorInfo; 
}
?>