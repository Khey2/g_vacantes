<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);


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

$la_vacante = $_GET['IDvacante'];
mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT vac_vacante.IDvacante, vac_matriz.matriz,  vac_vacante.IDusuario2,  vac_vacante.IDusuario3, vac_puestos.denominacion, vac_areas.area, vac_areas.dias FROM vac_vacante left JOIN vac_puestos ON vac_vacante.IDpuesto = vac_puestos.IDpuesto left JOIN vac_matriz ON vac_matriz.IDmatriz = vac_vacante.IDmatriz left JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE IDvacante = '$la_vacante'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());

if ($row_matriz['IDusuario2'] !=  0) {
$segundo = $row_matriz['IDusuario2'];
mysql_select_db($database_vacantes, $vacantes);
$query_segundo = "SELECT* FROM vac_usuarios WHERE IDusuario = '$segundo'";
$segundo = mysql_query($query_segundo, $vacantes) or die(mysql_error());
$row_segundo = mysql_fetch_assoc($segundo);
$totalRows_segundo = mysql_num_rows($segundo);
$elcorreo2 = $row_segundo['usuario_correo']; 
} else { $elcorreo2 = "NO"; }

if ($row_matriz['IDusuario3'] != 0) {
$tercero = $row_matriz['IDusuario3'];
mysql_select_db($database_vacantes, $vacantes);
$query_tercero = "SELECT* FROM vac_usuarios WHERE IDusuario = '$tercero'";
$tercero = mysql_query($query_tercero, $vacantes) or die(mysql_error());
$row_tercero = mysql_fetch_assoc($tercero);
$totalRows_tercero = mysql_num_rows($tercero);
$elcorreo3 = $row_tercero['usuario_correo']; 
} else { $elcorreo3 = "NO"; }


//solo si hay correos se manda
if ($elcorreo2 != "NO") {

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
$mail->AddAddress($elcorreo2);
$mail->Subject = 'Asignacion de Vacante.';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Asignacion de Vacante.';

$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns:v="urn:schemas-microsoft-com:vml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" /><!--[if !mso]--><!-- --><link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet"><link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet"><!--<![endif]--><title>Sistema de Gestion de Vacantes</title><style type="text/css">    body {        width: 100%;        background-color: #ffffff;        margin: 0;        padding: 0;        -webkit-font-smoothing: antialiased;        mso-margin-top-alt: 0px;        mso-margin-bottom-alt: 0px;        mso-padding-alt: 0px 0px 0px 0px;    }    p,    h1,    h2,    h3,    h4 {        margin-top: 0;        margin-bottom: 0;        padding-top: 0;        padding-bottom: 0;    }    span.preheader {        display: none;        font-size: 1px;    }    html {        width: 100%;    }    table {        font-size: 14px;        border: 0;    }    /* ----------- responsivity ----------- */    @media only screen and (max-width: 640px) {        /*------ top header ------ */        .main-header {font-size: 20px !important;        }        .main-section-header {font-size: 28px !important;        }        .show {display: block !important;        }        .hide {display: none !important;        }        .align-center {text-align: center !important;        }        .no-bg {background: none !important;        }        /*----- main image -------*/        .main-image img {width: 440px !important;height: auto !important;        }        /* ====== divider ====== */        .divider img {width: 440px !important;        }        /*-------- container --------*/        .container590 {width: 440px !important;        }        .container580 {width: 400px !important;        }        .main-button {width: 220px !important;        }        /*-------- secions ----------*/        .section-img img {width: 320px !important;height: auto !important;        }        .team-img img {width: 100% !important;height: auto !important;        }    }    @media only screen and (max-width: 479px) {        /*------ top header ------ */        .main-header {font-size: 18px !important;        }        .main-section-header {font-size: 26px !important;        }        /* ====== divider ====== */        .divider img {width: 280px !important;        }        /*-------- container --------*/        .container590 {width: 280px !important;        }        .container590 {width: 280px !important;        }        .container580 {width: 260px !important;        }        /*-------- secions ----------*/        .section-img img {width: 280px !important;height: auto !important;        }    }</style><!--[if gte mso 9]><style type=�text/css�>    body {    font-family: arial, sans-serif!important;    }    </style><![endif]--></head><body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"><!-- pre-header --><table style="display:none!important;">    <tr>        <td><div style="overflow:hidden;display:none;font-size:1px;color:#ffffff;line-height:1px;font-family:Arial;maxheight:0px;max-width:0px;opacity:0;">   Sistema de Gestion de Vacantes</div>        </td>    </tr></table><!-- pre-header end --><!-- header --><table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">    <tr>        <td align="center"><table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td></tr><tr>    <td align="center">        <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr><td align="center" height="70" style="height:70px;">    <a href="" style="display: block; border-style: none !important; border: 0 !important;"><img width="100" border="0" style="display: block; width: 100px;" src="https://intranet.sahuayo.com.mx/global_assets/images/logo_dark.png" alt="" /></a></td></tr><tr><td align="center">&nbsp;</td></tr>        </table>    </td></tr><tr>    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td></tr></table>        </td>    </tr></table><!-- end header --><!-- big image section --><table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">    <tr>        <td align="center"><table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>    <td align="center" style="color: #343434; font-size: 24px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 3px; line-height: 35px;"        class="main-header">        <!-- section text ======-->        <div style="line-height: 35px">Reporte de Asignación de <span style="color: #5caad2;">Vacante</span>        </div>    </td></tr><tr>    <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td></tr><tr>    <td align="center">        <table border="0" width="40" align="center" cellpadding="0" cellspacing="0" bgcolor="eeeeee"><tr><td height="2" style="font-size: 2px; line-height: 2px;">&nbsp;</td></tr>        </table>    </td></tr><tr>    <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td></tr><tr>    <td align="left">        <table border="0" width="590" align="center" cellpadding="0" cellspacing="0" class="container590"><tr><td align="left" style="color: #888888; font-size: 16px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">    <!-- section text ======-->    <p style="line-height: 24px; margin-bottom:15px;">Estimado Usuario, te informamos que se te ha asignado una nueva vacante:<table  border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color"> <thead> <tr> <th>Folio Vacante</th> <th>Matriz</th> <th>Denominacion</th> <th>Area</th> <th>Dias de Cobertura</th> </tr> </thead>';
$body .= '<tbody>';
$body .= '<tr>';
$body .= '<td align="center">';
$body .=  $row_matriz['IDvacante'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_matriz['matriz'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_matriz['denominacion'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_matriz['area'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_matriz['dias'];
$body .= '</td>';
$body .= '</tr>';
$body .= '
</tbody>
</table><br /></p>    <p style="line-height: 24px; margin-bottom:20px;">Puedes acceder al Sistema para revisar el detalle dando clic en el siguiente acceso.    </p>    <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="5caad2" style="margin-bottom:20px;">    <tr><td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td></tr>    <tr><td align="center" style="color: #ffffff; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">    <!-- main section button --><div style="line-height: 22px;"><a href="https://intranet.sahuayo.com.mx/" style="color: #ffffff; text-decoration: none;">Acceso</a>    </div></td></tr>    <tr><td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td></tr></table>    <p style="line-height: 24px">Saludos cordiales,</br>       SGV Admin Master    </p>    </td>    </tr></table>    </td></tr>    </table></td>    </tr>    <tr><td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>    </tr></table><!-- end section --><!-- main section --><!-- end section --><!-- contact section --><!-- end section --><!-- footer ====== -->  <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="f4f4f4">    <tr><td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>    </tr>    <tr><td align="center">    <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>    <td><table border="0" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"    class="container590">    <tr><td align="left" style="color: #aaaaaa; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">    <div style="line-height: 24px;">    <span style="color: #333333;">Sistema de Gestion de Vacantes 2020</span></div></td>    </tr></table><table border="0" align="left" width="5" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"    class="container590">    <tr><td height="20" width="5" style="font-size: 20px; line-height: 20px;">&nbsp;</td>    </tr></table><table border="0" align="right" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"    class="container590"><tr><td align="center">    <table align="center" border="0" cellpadding="0" cellspacing="0"><tr><td align="center">   </td></tr>    </table></td>    </tr></table>    </td></tr>    </table></td>    </tr>    <tr><td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>    </tr></table><!-- end footer ====== --></body></html>';
$mail->Body = $body;
//echo $body;
if (!$mail->send()) {    echo "Mailer Error: " . $mail->ErrorInfo; }
} 

if ($elcorreo3 != "NO") {

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
$mail->AddAddress($elcorreo2);
$mail->Subject = 'Asignacion de Vacante.';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Asignacion de Vacante.';

$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns:v="urn:schemas-microsoft-com:vml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" /><!--[if !mso]--><!-- --><link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet"><link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet"><!--<![endif]--><title>Sistema de Gestion de Vacantes</title><style type="text/css">    body {        width: 100%;        background-color: #ffffff;        margin: 0;        padding: 0;        -webkit-font-smoothing: antialiased;        mso-margin-top-alt: 0px;        mso-margin-bottom-alt: 0px;        mso-padding-alt: 0px 0px 0px 0px;    }    p,    h1,    h2,    h3,    h4 {        margin-top: 0;        margin-bottom: 0;        padding-top: 0;        padding-bottom: 0;    }    span.preheader {        display: none;        font-size: 1px;    }    html {        width: 100%;    }    table {        font-size: 14px;        border: 0;    }    /* ----------- responsivity ----------- */    @media only screen and (max-width: 640px) {        /*------ top header ------ */        .main-header {font-size: 20px !important;        }        .main-section-header {font-size: 28px !important;        }        .show {display: block !important;        }        .hide {display: none !important;        }        .align-center {text-align: center !important;        }        .no-bg {background: none !important;        }        /*----- main image -------*/        .main-image img {width: 440px !important;height: auto !important;        }        /* ====== divider ====== */        .divider img {width: 440px !important;        }        /*-------- container --------*/        .container590 {width: 440px !important;        }        .container580 {width: 400px !important;        }        .main-button {width: 220px !important;        }        /*-------- secions ----------*/        .section-img img {width: 320px !important;height: auto !important;        }        .team-img img {width: 100% !important;height: auto !important;        }    }    @media only screen and (max-width: 479px) {        /*------ top header ------ */        .main-header {font-size: 18px !important;        }        .main-section-header {font-size: 26px !important;        }        /* ====== divider ====== */        .divider img {width: 280px !important;        }        /*-------- container --------*/        .container590 {width: 280px !important;        }        .container590 {width: 280px !important;        }        .container580 {width: 260px !important;        }        /*-------- secions ----------*/        .section-img img {width: 280px !important;height: auto !important;        }    }</style><!--[if gte mso 9]><style type=�text/css�>    body {    font-family: arial, sans-serif!important;    }    </style><![endif]--></head><body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"><!-- pre-header --><table style="display:none!important;">    <tr>        <td><div style="overflow:hidden;display:none;font-size:1px;color:#ffffff;line-height:1px;font-family:Arial;maxheight:0px;max-width:0px;opacity:0;">   Sistema de Gestion de Vacantes</div>        </td>    </tr></table><!-- pre-header end --><!-- header --><table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">    <tr>        <td align="center"><table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td></tr><tr>    <td align="center">        <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr><td align="center" height="70" style="height:70px;">    <a href="" style="display: block; border-style: none !important; border: 0 !important;"><img width="100" border="0" style="display: block; width: 100px;" src="https://intranet.sahuayo.com.mx/global_assets/images/logo_dark.png" alt="" /></a></td></tr><tr><td align="center">&nbsp;</td></tr>        </table>    </td></tr><tr>    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td></tr></table>        </td>    </tr></table><!-- end header --><!-- big image section --><table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">    <tr>        <td align="center"><table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>    <td align="center" style="color: #343434; font-size: 24px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 3px; line-height: 35px;"        class="main-header">        <!-- section text ======-->        <div style="line-height: 35px">Reporte de Asignación de <span style="color: #5caad2;">Vacante</span>        </div>    </td></tr><tr>    <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td></tr><tr>    <td align="center">        <table border="0" width="40" align="center" cellpadding="0" cellspacing="0" bgcolor="eeeeee"><tr><td height="2" style="font-size: 2px; line-height: 2px;">&nbsp;</td></tr>        </table>    </td></tr><tr>    <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td></tr><tr>    <td align="left">        <table border="0" width="590" align="center" cellpadding="0" cellspacing="0" class="container590"><tr><td align="left" style="color: #888888; font-size: 16px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">    <!-- section text ======-->    <p style="line-height: 24px; margin-bottom:15px;">Estimado Usuario, te informamos que se te ha asignado una nueva vacante:<table  border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color"> <thead> <tr> <th>Folio Vacante</th> <th>Matriz</th> <th>Denominacion</th> <th>Area</th> <th>Dias de Cobertura</th> </tr> </thead>';
$body .= '<tbody>';
$body .= '<tr>';
$body .= '<td align="center">';
$body .=  $row_matriz['IDvacante'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_matriz['matriz'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_matriz['denominacion'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_matriz['area'];
$body .= '</td>';
$body .= '<td align="center">';
$body .=  $row_matriz['dias'];
$body .= '</td>';
$body .= '</tr>';
$body .= '
</tbody>
</table><br /></p>    <p style="line-height: 24px; margin-bottom:20px;">Puedes acceder al Sistema para revisar el detalle dando clic en el siguiente acceso.    </p>    <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="5caad2" style="margin-bottom:20px;">    <tr><td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td></tr>    <tr><td align="center" style="color: #ffffff; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">    <!-- main section button --><div style="line-height: 22px;"><a href="https://intranet.sahuayo.com.mx/" style="color: #ffffff; text-decoration: none;">Acceso</a>    </div></td></tr>    <tr><td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td></tr></table>    <p style="line-height: 24px">Saludos cordiales,</br>       SGV Admin Master    </p>    </td>    </tr></table>    </td></tr>    </table></td>    </tr>    <tr><td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>    </tr></table><!-- end section --><!-- main section --><!-- end section --><!-- contact section --><!-- end section --><!-- footer ====== -->  <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="f4f4f4">    <tr><td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>    </tr>    <tr><td align="center">    <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>    <td><table border="0" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"    class="container590">    <tr><td align="left" style="color: #aaaaaa; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">    <div style="line-height: 24px;">    <span style="color: #333333;">Sistema de Gestion de Vacantes 2020</span></div></td>    </tr></table><table border="0" align="left" width="5" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"    class="container590">    <tr><td height="20" width="5" style="font-size: 20px; line-height: 20px;">&nbsp;</td>    </tr></table><table border="0" align="right" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"    class="container590"><tr><td align="center">    <table align="center" border="0" cellpadding="0" cellspacing="0"><tr><td align="center">   </td></tr>    </table></td>    </tr></table>    </td></tr>    </table></td>    </tr>    <tr><td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>    </tr></table><!-- end footer ====== --></body></html>';
$mail->Body = $body;
echo $body;
if (!$mail->send()) {    echo "Mailer Error: " . $mail->ErrorInfo; }
}	

header("Location: admin_vacantes.php"); 
?>