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
date_default_timezone_set("America/Mexico_City");
$desfase = $row_variables['dias_desfase'];
// mes y semana
$el_mes = date("m");
$el_mes_anterior = $el_mes - 1;
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el a�o anterior 
$semana = date("W", strtotime($la_fecha));
$semana = $semana - 1;
$semana_prev = $semana - 2;
$anio = $row_variables['anio'];
$anio_actual = $anio;
$anio_anterior = $anio - 1; // la fecha actual


$query_pprueba = "SELECT pp_prueba_pagos.IDempleado, pp_prueba_pagos.fecha_pago, pp_prueba_pagos.semana, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, pp_prueba.IDmatriz, vac_puestos.denominacion FROM pp_prueba_pagos INNER JOIN pp_prueba ON pp_prueba_pagos.IDpprueba = pp_prueba.IDpprueba INNER JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado INNER JOIN vac_puestos ON pp_prueba.IDpuesto_destino = vac_puestos.IDpuesto WHERE prod_activos.IDVsueldo = 1 AND pp_prueba.IDestatusv = 1 AND pp_prueba_pagos.fecha_pago = DATE_ADD(CURDATE(), INTERVAL 3 DAY)"; 
mysql_query("SET NAMES 'utf8'"); echo $query_pprueba;

$pprueba = mysql_query($query_pprueba, $vacantes) or die(mysql_error());
$row_pprueba = mysql_fetch_assoc($pprueba);
$totalRows_pprueba = mysql_num_rows($pprueba);

//se envia correo
require 'assets/PHPMailer/PHPMailerAutoload.php';

// loop para cada matriz
do { 
$LaMatriz = $row_pprueba['IDmatriz'];

//correo rh
mysql_select_db($database_vacantes, $vacantes);
$query_correo_1 = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($LaMatriz, IDmatrizes) AND correo_semanal  = 5";
$correo_1 = mysql_query($query_correo_1, $vacantes) or die(mysql_error());
$row_correo_1 = mysql_fetch_assoc($correo_1);
$totalRows_correo_1 = mysql_num_rows($correo_1);
if($totalRows_correo_1 > 0) {$El_correo_1 = $row_correo_1['usuario_correo'];} else {$El_correo_1 = 'jacardenas@sahuayo.mx';}
echo $El_correo_1;

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
$mail->addReplyTo('reporte_diario@gestionvacantes.com', 'Recursos Humanos');
$mail->AddAddress($El_correo_1);
$mail->AddAddress('jacardenas@sahuayo.mx');
$mail->AddAddress('mahernandez@sahuayo.mx');
$mail->Subject = 'Programación de pago de Periodo de Prueba.';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Programación de pago de Periodo de Prueba.';
$body = '

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" />
    <!--[if !mso]--><!-- -->
    <link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet">
    <!--<![endif]-->
	<title>Sistema de Gestion de Recursos Humanos</title>

    <style type="text/css">
        body {
            width: 100%;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            mso-margin-top-alt: 0px;
            mso-margin-bottom-alt: 0px;
            mso-padding-alt: 0px 0px 0px 0px;
        }

        p,
        h1,
        h2,
        h3,
        h4 {
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        span.preheader {
            display: none;
            font-size: 1px;
        }

        html {
            width: 100%;
        }

        table {
            font-size: 14px;
            border: 0;
        }
        /* ----------- responsivity ----------- */

        @media only screen and (max-width: 640px) {
            /*------ top header ------ */
            .main-header {
                font-size: 20px !important;
            }
            .main-section-header {
                font-size: 28px !important;
            }
            .show {
                display: block !important;
            }
            .hide {
                display: none !important;
            }
            .align-center {
                text-align: center !important;
            }
            .no-bg {
                background: none !important;
            }
            /*----- main image -------*/
            .main-image img {
                width: 440px !important;
                height: auto !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 440px !important;
            }
            /*-------- container --------*/
            .container590 {
                width: 440px !important;
            }
            .container580 {
                width: 400px !important;
            }
            .main-button {
                width: 220px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 320px !important;
                height: auto !important;
            }
            .team-img img {
                width: 100% !important;
                height: auto !important;
            }
        }

        @media only screen and (max-width: 479px) {
            /*------ top header ------ */
            .main-header {
                font-size: 18px !important;
            }
            .main-section-header {
                font-size: 26px !important;
            }
            /* ====== divider ====== */
            .divider img {
                width: 280px !important;
            }
            /*-------- container --------*/
            .container590 {
                width: 280px !important;
            }
            .container590 {
                width: 280px !important;
            }
            .container580 {
                width: 260px !important;
            }
            /*-------- secions ----------*/
            .section-img img {
                width: 280px !important;
                height: auto !important;
            }
        }
		#customers {
		  border-collapse: collapse;
		  width: 100%;
		}
		
		#customers td, #customers th {
		  border: 1px solid #ddd;
		  padding: 4px;
		}
		
		#customers tr:nth-child(even){background-color: #f2f2f2;}
		
		#customers th {
		  padding-top: 12px;
		  padding-bottom: 12px;
		  text-align: left;
		  background-color: #C30F2D;
		  color: white;
		}
    </style>
    <!--[if gte mso 9]><style type=�text/css�>
        body {
        font-family: arial, sans-serif!important;
        }
        </style>
    <![endif]-->
</head>


<body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <!-- header -->
    <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">

        <tr>
            <td align="center">
                <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590">

                    <tr>
                        <td style="font-size: 25px; line-height: 25px;">&nbsp;</td>
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
                        <td align="center" style="color: #343434; font-size: 24px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 3px; line-height: 35px;"
                            class="main-header">
                            <!-- section text ======-->

                            <div style="line-height: 35px">

                                Programaci&oacute;n de pago de <span style="color: #C30F2D;">Periodo de Prueba</span>

                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td align="center">
                            <table border="0" width="40" align="center" cellpadding="0" cellspacing="0" bgcolor="eeeeee">
                                <tr>
                                    <td height="2" style="font-size: 2px; line-height: 2px;">&nbsp;</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td>
                    </tr>

                    <tr>
                        <td align="left">
                            <table border="0" width="590" align="center" cellpadding="0" cellspacing="0" class="container590">
                                <tr>
                                    <td align="left" style="color: #888888; font-size: 16px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">
                                        <p style="line-height: 24px; margin-bottom:15px;">

                                            Estimado Usuario,

                                        </p>
                                        <p style="line-height: 24px;margin-bottom:15px;">
                                           A continuaci&oacute;n se muestran los pagos programados de empleados en Periodo de Prueba pr&oacute;ximos a aplicarse correspondientes a la semana <strong>'.$row_pprueba['semana'].'</strong>.
                                        </p>
                                        <p style="line-height: 24px; margin-bottom:20px;">
                                           Puedes consultar los detalles accediendo al <strong>Sistema de Gesti&oacute;n de Recursos Humanos Sahuayo </strong> dando clic en el bot&oacute;n al final del presente correo.
                                        </p>
<table id="customers"> 
<thad>
<tr>
<th style="text-align:center" style="text-align:center">Empleado</th>
<th style="text-align:center" style="text-align:center">Puesto a Promoverse</th>
<th style="text-align:center" style="text-align:center">Fecha de pago</th>
<th style="text-align:center" style="text-align:center">Semana</th>
</tr>
</thead>';
$body .= '<tbody>';
$body .= '<tr>';
$body .= '<td align="center">';
$body .=  $row_pprueba['emp_paterno']." ".$row_pprueba['emp_materno']." ".$row_pprueba['emp_nombre'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_pprueba['denominacion'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  date("d-m-Y", strtotime($row_pprueba['fecha_pago']));
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_pprueba['semana'];
$body .= '</td>';
$body .= '</tbody></table>
								
                                        <p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
                                        <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="C30F2D" style="margin-bottom:20px;">

                                            <tr>
                                                <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                                            </tr>

                                            <tr>
                                                <td align="center" style="color: #ffffff; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">
                                                    <!-- main section button -->

                                                    <div style="line-height: 22px;">
                                                        <a href="https://gestionvacantes.com/f_index.php" style="color: #ffffff; text-decoration: none;">Accede al SGRH</a>
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td>
                                            </tr>

                                        </table>
                                        <p style="line-height: 24px">
                                            Saludos Cordiales,</br>
                                            <strong>Sistema de Gesti&oacute;n de Recursos Humanos </strong></br>
											Sahuayo 2021</br>
                                        </p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
        <tr>
            <td><p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>Si recibiste por error este correo o no quieres recibirlo en adelante, solicita tu baja a <a href="mailto:jacardenas@sahuayo.mx">jacardenas@sahuayo.mx</a></td>
        </tr>
    </table>
</body>
</html>
';
$mail->Body = $body;
echo $body;
if (!$mail->send()) {    echo "Mailer Error: " . $mail->ErrorInfo; }
// cierre loop para cada matriz
} while ($row_pprueba = mysql_fetch_assoc($pprueba));
?>


