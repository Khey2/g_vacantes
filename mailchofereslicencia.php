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

$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$la_fechaA = date("Y-m-d", strtotime($fecha." + 42 day")); 
$la_fechaB = date("Y-m-d", strtotime($fecha." + 49 day"));  

$query_matriz = "SELECT * FROM vac_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);

//se envia correo
require 'assets/PHPMailer/PHPMailerAutoload.php';

// loop para cada matriz
do { 

$LaMatriz = $row_matriz['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$LaMatriz'";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);

if ($row_matriz['choferes'] == 2){
    
$query_vacabtes = "SELECT prod_activos.IDempleado, prod_activos.IDmatriz, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.fecha_alta, prod_activos.denominacion, prod_activos.fecha_antiguedad, prod_activos.estatus, sed_uniformes.Licencia_vigencia FROM prod_activos LEFT JOIN sed_uniformes ON prod_activos.IDempleado = sed_uniformes.IDempleado WHERE (sed_uniformes.Licencia_vigencia BETWEEN '$la_fechaA' AND '$la_fechaB') AND prod_activos.IDpuesto IN (42,43,44) AND prod_activos.IDmatriz = $LaMatriz";
mysql_query("SET NAMES 'utf8'"); 
$vacabtes = mysql_query($query_vacabtes, $vacantes) or die(mysql_error());
$row_vacabtes = mysql_fetch_assoc($vacabtes);
$totalRows_vacabtes = mysql_num_rows($vacabtes);
        
} else {

$totalRows_vacabtes = 0;

}



if ($totalRows_vacabtes > 0) {

//correo rh
mysql_select_db($database_vacantes, $vacantes);
$query_correo_1 = "SELECT * FROM vac_usuarios WHERE IDmatriz = $LaMatriz AND IDusuario_puesto in (10, 11, 56, 129)";
$correo_1 = mysql_query($query_correo_1, $vacantes) or die(mysql_error());
$row_correo_1 = mysql_fetch_assoc($correo_1);
$totalRows_correo_1 = mysql_num_rows($correo_1);


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
do { $mail->AddAddress($row_correo_1['usuario_correo']); } while ($row_correo_1 = mysql_fetch_assoc($correo_1)); 
$mail->AddAddress('jacardenas@sahuayo.mx');
$mail->Subject = 'Reporte Semanal de licencias proximas a vencimiento.';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Reporte Semanal de licencias proximas a vencimiento.';
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
    <!--[if gte mso 9]><style type="text/css">
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

                                Reporte Semanal de <span style="color: #C30F2D;">Licencias pr&oacute;ximas a vencimiento</span>

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
                                           A continuaci&oacute;n se muestran los próximos vencimientos de licencias de Conducir para Choferes de la Sucursal <strong> '.$row_lmatriz['matriz'].'</strong>. <br/>
										   Favor de realizar la validaci&oacute;n de la fecha y en su caso, el tramite para renovaci&oacute;n de Licencia.
                                        </p>
<table id="customers"> 
<thad>
<tr>
<th style="text-align:center" style="text-align:center">No. Emp</th>
<th style="text-align:center" style="text-align:center">Nombre</th>
<th style="text-align:center" style="text-align:center">Puesto</th>
<th style="text-align:center" style="text-align:center">Fecha Vigencia</th>
</tr>
</thead>';
$body .= '<tbody>';
$body .= '<tr>';
$body .= '<td align="center">';
$body .=  $row_vacabtes['IDempleado'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_vacabtes['emp_paterno']." ".$row_vacabtes['emp_materno']." ".$row_vacabtes['emp_nombre'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_vacabtes['denominacion'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  date( 'd/m/Y' , strtotime($row_vacabtes['Licencia_vigencia']));
$body .= '</td>';
$body .= '</tbody></table>

                                        <p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>							
                                        <p style="line-height: 24px; margin-bottom:20px;">&nbsp;</p>
                                        <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="C30F2D" style="margin-bottom:20px;">
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

}

} while ($row_matriz = mysql_fetch_assoc($matriz));
?>


