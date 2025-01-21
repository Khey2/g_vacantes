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

require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

$actualusuario = $_SESSION['kt_login_id'];
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$actualusuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$el_usuario = $row_usuario['IDusuario'];
$correo_remitente = $row_usuario['usuario_correo'];

$IDempleado = $_GET['IDusuario'];	
mysql_select_db($database_vacantes, $vacantes);
$query_candidatos = "SELECT cv_activos.IDusuario, cv_activos.a_paterno, cv_activos.a_materno,cv_activos.a_materno, cv_activos.a_correo, cv_activos.a_nombre,  cv_activos.fecha_captura, cv_activos.fecha_entrevista, cv_activos.hora_entrevista, cv_activos.IDentrevista, cv_activos.IDmatriz, cv_activos.IDpuesto, cv_activos.estatus, cv_activos.tipo, vac_puestos.denominacion, vac_puestos.IDarea, vac_areas.area FROM cv_activos left JOIN vac_puestos ON vac_puestos.IDpuesto = cv_activos.IDpuesto LEFT JOIN vac_areas ON vac_areas.IDarea = vac_puestos.IDarea  WHERE cv_activos.IDusuario = '$IDempleado'";
mysql_query("SET NAMES 'utf8'");
$candidatos = mysql_query($query_candidatos, $vacantes) or die(mysql_error());
$row_candidatos = mysql_fetch_assoc($candidatos);
$totalRows_candidatos = mysql_num_rows($candidatos);
$correo = $row_candidatos['a_correo'];
$correo_name = $row_candidatos['usuario_nombre']." ".$row_candidatos['usuario_parterno']." ".$row_candidatos['usuario_materno'];


$IDmatriz = $row_candidatos['IDmatriz'];
$query_ubiacion = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
mysql_query("SET NAMES 'utf8'");
$ubiacion = mysql_query($query_ubiacion, $vacantes) or die(mysql_error());
$row_ubiacion = mysql_fetch_assoc($ubiacion);
$totalRows_ubiacion = mysql_num_rows($ubiacion);

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$IDrecibe = $row_matriz['correo_JRH'];
$IDcopia = $row_matriz['correo_RRH'];
$IDcopiaSINO = $row_matriz['copiar_correo'];

$query_recibe = "SELECT vac_matriz.matriz, vac_usuarios.usuario_correo, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno, vac_usuarios.IDusuario, vac_matriz.correo_JRH  FROM vac_matriz left JOIN vac_usuarios ON vac_matriz.IDmatriz = vac_usuarios.IDmatriz WHERE vac_usuarios.IDusuario = '$IDrecibe'";
$recibe  = mysql_query($query_recibe, $vacantes) or die(mysql_error());
$row_recibe  = mysql_fetch_assoc($recibe);
$totalRows_recibe  = mysql_num_rows($recibe);

$query_copia = "SELECT vac_matriz.matriz, vac_usuarios.usuario_correo, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno, vac_usuarios.IDusuario, vac_matriz.correo_JRH  FROM vac_matriz left JOIN vac_usuarios ON vac_matriz.IDmatriz = vac_usuarios.IDmatriz WHERE vac_usuarios.IDusuario = '$IDcopia'";
$copia  = mysql_query($query_copia, $vacantes) or die(mysql_error());
$row_copia  = mysql_fetch_assoc($copia);
$totalRows_copia  = mysql_num_rows($copia);


$recibe_nombre = $row_recibe['usuario_nombre']." ".$row_recibe['usuario_parterno']." ".$row_recibe['usuario_materno'];
$recibe_correo = $row_recibe['usuario_correo'];

$envia_nombre = $row_usuario['usuario_nombre']." ".$row_usuario['usuario_parterno']." ".$row_usuario['usuario_materno'];
$envia_correo = $row_usuario['usuario_correo'];

$copia_nombre = $row_copia['usuario_nombre']." ".$row_copia['usuario_parterno']." ".$row_copia['usuario_materno'];
$copia_correo = $row_copia['usuario_correo'];
	
//actualiza estatus envio correo
$deleteSQL = "UPDATE cv_activos SET enviado_mail = 1  WHERE IDusuario = '$IDempleado'";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());

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
$mail->setFrom('reporte_diario@gestionvacantes.com', 'Datos de Entrevista - Sahuayo Abarrotes');
$mail->addReplyTo('reporte_diario@gestionvacantes.com', 'Recursos Humanos Sahuayo Abarrotes');
$mail->AddAddress($correo, $correo_name);
$mail->AddCC($envia_correo, $envia_nombre);
$mail->AddCC($recibe_correo, $recibe_nombre);
if ($IDcopiaSINO == 1) {$mail->AddCC($copia_correo, $copia_nombre);}
$mail->Subject = 'Datos de Entrevista - Sahuayo Abarrotes.';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Datos de Entrevista - Sahuayo Abarrotes.';

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
        <td align="center">&nbsp;</td>
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
        <div style="line-height: 35px">Información de <span style="color: #5caad2;">Entrevista</span></div>
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

<ul>
					<li><strong>Nombre</strong>: '. $row_candidatos['a_nombre']. ' ' .$row_candidatos['a_paterno']. ' ' .$row_candidatos['a_materno'].'   
					<li></i><strong>Dia</strong>: '. date('d/m/Y', strtotime($row_candidatos['fecha_entrevista'])).'</li>
                    <li><strong>Hora</strong>: '. date('g:i A', strtotime($row_candidatos['hora_entrevista'])) .'</li>
                    <li><strong>Vacante</strong>: '. $row_candidatos['denominacion'].'</a>
                    <p>&nbsp;</p>
                                    
								<strong><p>Documentos</p></strong>
								<div class="text-left">
                                    <ul>
                                        <li>Solicitud elaborada</li>
                                        <li>IFE</li>
                                        <li>CURP</li>
                                        <li>RFC</li>
                                        <li>IMSS</a></li>
                                        <li>Licencia (solo si aplica)</li>
                                        <li>Acta de nacimiento</li>
                                        <li>Acta de matrimonio (solo si aplica)</li>
                                        <li>Comp. Estudios</li>
                                        <li>Comp. Domicilio</li>
                                        <li>2 cartas laborales (membretadas, firmadas y selladas)</li>
                                        <li>2 cartas personales (amigos o vecinos + 5 a&ntilde;os de conocer + copia de INE)</li>
                                        <li>Certificado Medico</li>
                                        <li>4 fotos tama&ntilde;o infantil a color.</li>
                                        <li>Correo electr&oacute;nico</li>
                                        <li>Solicitud elaborada</li>
                                        <div class="list-group-divider"></div>
                                    </ul>
								</div>
	                    <p>&nbsp;</p>
								<strong><p>Direcci&oacute;n</p></strong>
								<div class="text-left">
                                  <ul>
                                        <li>'. $row_ubiacion['direccion']. '</li>
                                        <li><a href="'. $row_ubiacion['ubicacion']. '">'. $row_ubiacion['ubicacion']. '</a></li>
                                  </ul>
                				</div>
                    <p>&nbsp;</p>
                  ***Obligatorio uso de cubrebocas***</br>                                        
                  ****No es necesario acudir a entrevista con todos los documentos***
							</div>
							<!-- /navigation --></p>
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
    <div style="line-height: 24px;"><span style="color: #333333;">Sistema de Gestion de Recursos Humanos 2021</span></div>
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
if (!$mail->send()) { echo "Mailer Error: " . $mail->ErrorInfo; }
header('Location: candidatos_nuevo.php?IDusuario='.$IDempleado.'&info=4');
 ?>