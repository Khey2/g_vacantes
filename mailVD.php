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

//se envia correo
require 'assets/PHPMailer/PHPMailerAutoload.php';

// loop para cada matriz

$query_vacabtes = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.denominacion, vac_vacante.IDmotivo_baja, vac_vacante.IDmotivo_v, vac_vacante.IDrequi, vac_vacante.ajuste_dias, vac_vacante.fecha_requi, vac_puestos.dias AS Dias_esperados, (DATEDIFF( now(),vac_vacante.fecha_requi)) AS Dias_transcurridos, vac_matriz.matriz, vac_apoyo.apoyo FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz INNER JOIN vac_apoyo ON vac_apoyo.IDapoyo = vac_vacante.IDapoyo WHERE vac_vacante.IDmatriz IN (3,12,13,14,16,18,20,23,25,26) AND vac_vacante.IDestatus = 1 AND vac_vacante.IDarea IN (2,4,6) GROUP BY vac_vacante.IDvacante, vac_vacante.ajuste_dias, vac_vacante.observaciones, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus, vac_vacante.fecha_requi, vac_puestos.denominacion";  
mysql_query("SET NAMES 'utf8'");
$vacabtes = mysql_query($query_vacabtes, $vacantes) or die(mysql_error());
$row_vacabtes = mysql_fetch_assoc($vacabtes);
$totalRows_vacabtes = mysql_num_rows($vacabtes);

$query_cubiertas = "SELECT vac_vacante.IDvacante, vac_areas.area, vac_puestos.denominacion, vac_vacante.IDmotivo_baja, vac_vacante.IDmotivo_v, vac_vacante.IDrequi, vac_vacante.ajuste_dias, vac_vacante.fecha_requi, vac_puestos.dias AS Dias_esperados, vac_matriz.matriz, vac_apoyo.apoyo, (DATEDIFF( now(),vac_vacante.fecha_requi)) AS Dias_transcurridos, vac_vacante.fecha_ocupacion FROM vac_vacante LEFT JOIN vac_areas ON vac_areas.IDarea = vac_vacante.IDarea LEFT JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto INNER JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz INNER JOIN vac_apoyo ON vac_apoyo.IDapoyo = vac_vacante.IDapoyo WHERE vac_vacante.IDmatriz IN (3,12,13,14,16,18,20,23,25,26) AND vac_vacante.IDestatus = 2  AND vac_vacante.IDarea IN (2,4,6) AND vac_vacante.fecha_ocupacion >= date_add(NOW(), INTERVAL -12 DAY) GROUP BY vac_vacante.IDvacante, vac_vacante.ajuste_dias, vac_vacante.observaciones, vac_puestos.dias, vac_areas.area, vac_vacante.IDestatus,  vac_vacante.fecha_requi, vac_puestos.denominacion"; 
mysql_query("SET NAMES 'utf8'");
$cubiertas = mysql_query($query_cubiertas, $vacantes) or die(mysql_error());
$row_cubiertas = mysql_fetch_assoc($cubiertas);
$totalRows_cubiertas = mysql_num_rows($cubiertas);


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
$mail->AddAddress('jacardenas@sahuayo.mx');
$mail->Subject = 'Reporte Semanal de Vacantes de Ventas a Detalle.';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Reporte Semanal de Vacantes de Ventas a Detalle.';
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

                                Reporte Semanal de Vacantes de <br /><span style="color: #C30F2D;">Ventas a Detalle</span>

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
                                           A continuaci&oacute;n se muestra el Reporte de Vacantes activas y cubiertas de Ventas a Detalle correspondientes a la semana <strong>'.$semana.'</strong>.<br/>
										   Las vacantes marcadas con <b>"*"</b> no cuentan con requisición fornal de solicitud.
                                        </p>

                                          <h3>Vacantes Activas</h3>
                                        </p>
<table id="customers"> 
<thad>
<tr>
<th style="text-align:center">Sucursal</th>
<th style="text-align:center">Nombre</th>
<th style="text-align:center">D&iacute;as</th>
<th style="text-align:center">Requi</th>
<th style="text-align:center">Pull Vac</th>
<th style="text-align:center">Estatus</th>
</tr>
</thead>
';
$body .= '<tbody>';
if($totalRows_vacabtes > 0) {
do {
if($row_vacabtes['IDrequi'] == 1) {$reqi = "No";} else {$reqi = "Si";}
if($row_vacabtes['IDrequi'] == 1) {$tiempo = $row_vacabtes['Dias_transcurridos']."*";} else {$tiempo =  $row_vacabtes['Dias_transcurridos'];}
if($row_vacabtes['IDmotivo_v'] == 5) {$pullvacaciones = "Si";} else {$pullvacaciones = "No";}
	
$body .= '<tr>';
$body .= '<td align="center">';
$body .=  $row_vacabtes['matriz'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_vacabtes['denominacion'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $tiempo;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $reqi;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $pullvacaciones;
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_vacabtes['apoyo'];
$body .= '</td>';
$body .= '</tr>';
} while($row_vacabtes=mysql_fetch_array($vacabtes));
} else {
$body .= '<tr><td  colspan="6">No hay vacantes activas.</td></tr>';
}
$body .= '</tbody></table>
                                        <p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
										<p style="line-height: 24px;margin-bottom:15px;">
                                          <h3>Vacantes Cubiertas</h3>
                                        </p>
<table id="customers"> 
<thad>
<tr>
<th style="text-align:center">Matriz</th>
<th style="text-align:center">Nombre de la Vacante</th>
<th style="text-align:center">Dias Transcurridos</th>
<th style="text-align:center">Pull Vac.</th>
</tr>
</thead>';
$body .= '<tbody>';
if($totalRows_cubiertas > 0) {
do{ 
if($row_cubiertas['IDmotivo_v'] == 5) {$pullvacacionesc = "Si";} else {$pullvacacionesc = "No";}
$body .= '<tr>';
$body .= '<td align="center">';
$body .=  $row_cubiertas['matriz'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_cubiertas['denominacion'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=   $row_cubiertas['Dias_transcurridos'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $pullvacacionesc;
$body .= '</td>';
$body .= '</tr>';
} while($row_cubiertas=mysql_fetch_array($cubiertas));
} else {	
$body .= '<tr>';
$body .= '<td  colspan="4">No hay vacantes cubiertas.</td>';
$body .= '</tr>';
}
$body .= '</tbody></table>
                                        <p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
										<p style="line-height: 24px;margin-bottom:15px;">
								
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
											Sahuayo 2024</br>
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
?>


