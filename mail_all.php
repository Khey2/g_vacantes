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
$query_matriz = "SELECT DISTINCT vac_matriz.IDmatriz as LaMatriz, vac_vacante.IDvacante, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.IDmatriz, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 GROUP BY vac_matriz.matriz";
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
$mail->setFrom('reporte_diario@gestionvacantes.com', 'Sistema de Gestion de Vacantes');
$mail->addReplyTo('reporte_diario@gestionvacantes.com', 'Admin');
$mail->AddAddress('jacardenas@sahuayo.mx');
$mail->AddAddress('cgaona@sahuayo.mx');
$mail->AddAddress('rramirez@sahuayo.mx');
$mail->AddAddress('lafloress@sahuayo.mx');
$mail->AddAddress('rtejeda@sahuayo.mx');
$mail->AddAddress('lreyes@sahuayo.mx');
$mail->AddAddress('gguerrero@sahuayo.mx');
$mail->AddAddress('gemendiola@sahuayo.mx');
$mail->AddAddress('dmmartinez@sahuayo.mx');
$mail->AddAddress('tgarcia@sahuayo.mx');
$mail->AddAddress('mahernandez@sahuayo.mx');
$mail->Subject = 'Reporte Diario de Vacantes';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Reporte Diario de Vacantes';

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
Sistema de Gestion de Vacantes</title>
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
        <div style="line-height: 35px">Reporte de Vacantes <span style="color: #5caad2;">Diario</span> (con y sin Requi)</div>
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
        <table border="0" width="590" align="center" cellpadding="0" cellspacing="0" class="container590">
<tr>
<td align="left" style="color: #888888; font-size: 16px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">
    <!-- section text ======-->
    <p style="line-height: 24px; margin-bottom:15px;">
Estimado Usuario, a continuacion te enviamos el reporte diario de vacantes con y sin Requi.</p>
 <table border="0" width="100%" align="center" cellpadding="0" cellspacing="0" class="container880">
 <thead>
 <tr>
 <th>Matriz</th>
 <th>Activas</th>
 <th>Fuera de tiempo</th>
 <th>Eventuales</th>
 <th><span style="color: #DB2B2B;">Pull vacaciones</span></th>
 </tr>
 </thead>';
$body .= '<tbody>';

					$Xactivas = 0; 
                    $Xfuera_tiempo = 0;
					$Xvacantes_pull = 0;
					$Xvacantes_pull2 = 0;

while($row_matriz = mysql_fetch_array($matriz)){
	
$LaMatriz = $row_matriz['LaMatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_global = "SELECT vac_vacante.IDvacante, vac_vacante.IDmotivo_v, vac_vacante.ajuste_dias, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_matriz.matriz FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz LEFT JOIN vac_puestos ON vac_puestos.IDpuesto = vac_vacante.IDpuesto WHERE vac_vacante.IDestatus = 1 AND vac_vacante.IDmatriz = '$LaMatriz'";
$global = mysql_query($query_global, $vacantes) or die(mysql_error());
$row_global = mysql_fetch_assoc($global);

$activas = 0;
$fuera_tiempo = 0;
$vacantes_pull = 0;
$vacantes_pull2 = 0;

do { 

 $startdate = date('Y/m/d', strtotime($row_global['fecha_requi']));
 $end_date =  date('Y/m/d');
 $previo = getWorkingDays($startdate, $end_date, $holidays);
 $ajuste_dias = $row_global['ajuste_dias'];
 
 if ($ajuste_dias != 0) { $previo = $previo - $ajuste_dias; } 
 
 if ($previo >= $row_global['dias'] + 6 AND ($row_global['IDmotivo_v'] != 3 OR $row_global['IDmotivo_v'] != 5)) {$fuera_tiempo = $fuera_tiempo + 1;} 
 
 if ($row_global['IDmotivo_v'] == 3) {$vacantes_pull = $vacantes_pull + 1; }
 
 if ($row_global['IDmotivo_v'] == 5) {$vacantes_pull2 = $vacantes_pull2 + 1; }
 
 $activas = $activas = $activas + 1; 

} while ($row_global = mysql_fetch_assoc($global));

                    $Xfuera_tiempo = $Xfuera_tiempo + $fuera_tiempo;
                    $Xvacantes_pull = $Xvacantes_pull + $vacantes_pull;
                    $Xvacantes_pull2 = $Xvacantes_pull2 + $vacantes_pull2;
					$activas = $activas - $vacantes_pull2 - $vacantes_pull; 
					$Xactivas = $Xactivas + $activas; 


$body .= '<tr>';
$body .= '<td>';
$body .=  $row_matriz['matriz'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $activas;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $fuera_tiempo;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $vacantes_pull;
$body .= '</td>';
$body .= '<td align="center">';
$body .= '<span style="color: #DB2B2B;">'.$vacantes_pull2."</span>";
$body .= '</td>';
$body .= '</tr>';
}
$body .= '<tr>';
$body .= '<td><strong>TOTAL</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xactivas;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xfuera_tiempo;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .=  $Xvacantes_pull;
$body .= '</strong></td>';
$body .= '<td align="center"><strong>';
$body .= '<span style="color: #DB2B2B;">'.$Xvacantes_pull2."</span>";
$body .= '</strong></td>';
$body .= '</tr>';


$body .= '
</tbody>
</table>
<br/>
</p>
    <p style="line-height: 24px; margin-bottom:20px;">Puedes acceder al Sistema para revisar el detalle dando clic en el siguiente acceso.</p>
    <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="5caad2" style="margin-bottom:20px;">
    <tr>
<td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
</tr>
    <tr>
<td align="center" style="color: #ffffff; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">
    <!-- main section button -->
<div style="line-height: 22px;"><a href="http://gestionvacantes.com/" style="color: #ffffff; text-decoration: none;">Acceso</a></div>
</td>
</tr>
    <tr>
<td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
</tr>
</table>
    <p style="line-height: 24px">Saludos cordiales,</br>SGV Admin Master</p>
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
    <div style="line-height: 24px;"><span style="color: #333333;">Sistema de Gestion de Vacantes 2020</span></div>
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