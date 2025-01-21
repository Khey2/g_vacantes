<?php require_once('Connections/vacantes.php'); 
require_once "includes/recaptchalib.php";
?>
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
$desfase = $row_variables['dias_desfase'];
$error = 0;

if (isset($_POST['email'])) {
	
 $secret = "6LeT7fwUAAAAAPXKM--5e19v_cPOxOszc5x2UWoU";
 $response = null;
 // comprueba la clave secreta
 $reCaptcha = new ReCaptcha($secret);
  if ($_POST["g-recaptcha-response"]) {
     $response = $reCaptcha->verifyResponse(
     $_SERVER["REMOTE_ADDR"],
     $_POST["g-recaptcha-response"]
     );
  }

$correo = $_POST['email'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM prod_activos WHERE a_correo = '$correo'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$esactivo = $row_usuario['activo'];
$elusuario = $row_usuario['usuario'];

$error = 0;

if ($totalRows_usuario == 0) {
$error = 1;  
}

// el usuario no tiene correo
elseif ($esactivo == 0) {
$error = 2;
}

else {
$error = 4;

$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$llave = substr(str_shuffle($permitted_chars), 0, 16);


//mandamos llave
$query1 = "INSERT into f_vac_key (usuario, llave) VALUE ('$elusuario', '$llave')";
$result1 = mysql_query($query1) or die(mysql_error()); 

//se envia correo
require 'assets/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
//$mail->isSMTP();
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';
$mail->Host = "smtp.office365.com";
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->SMTPAutoTLS = false;
$mail->SMTPSecure = '';
$mail->Username = "jacardenas@secape.net";
$mail->Password = "parazoom2020!";
$mail->setFrom('jacardenas@secape.net', 'Sistema de Gestion de Vacantes');
$mail->addReplyTo('jacardenas@secape.net', 'Admin');
$mail->AddAddress($correo);
$mail->Subject = 'Recuperacion de Password.';
$mail->isHTML(true);
$mail->CharSet = 'UTF-8';
$mail->AltBody = 'Recuperacion de Password.';

$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns:v="urn:schemas-microsoft-com:vml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;" /><!--[if !mso]--><!-- --><link href="https://fonts.googleapis.com/css?family=Work+Sans:300,400,500,600,700" rel="stylesheet"><link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet"><!--<![endif]--><title>Sistema de Gestion de Recursos Humanos</title><style type="text/css">    body {        width: 100%;        background-color: #ffffff;        margin: 0;        padding: 0;        -webkit-font-smoothing: antialiased;        mso-margin-top-alt: 0px;        mso-margin-bottom-alt: 0px;        mso-padding-alt: 0px 0px 0px 0px;    }    p,    h1,    h2,    h3,    h4 {        margin-top: 0;        margin-bottom: 0;        padding-top: 0;        padding-bottom: 0;    }    span.preheader {        display: none;        font-size: 1px;    }    html {        width: 100%;    }    table {        font-size: 14px;        border: 0;    }    /* ----------- responsivity ----------- */    @media only screen and (max-width: 640px) {        /*------ top header ------ */        .main-header {font-size: 20px !important;        }        .main-section-header {font-size: 28px !important;        }        .show {display: block !important;        }        .hide {display: none !important;        }        .align-center {text-align: center !important;        }        .no-bg {background: none !important;        }        /*----- main image -------*/        .main-image img {width: 440px !important;height: auto !important;        }        /* ====== divider ====== */        .divider img {width: 440px !important;        }        /*-------- container --------*/        .container590 {width: 440px !important;        }        .container580 {width: 400px !important;        }        .main-button {width: 220px !important;        }        /*-------- secions ----------*/        .section-img img {width: 320px !important;height: auto !important;        }        .team-img img {width: 100% !important;height: auto !important;        }    }    @media only screen and (max-width: 479px) {        /*------ top header ------ */        .main-header {font-size: 18px !important;        }        .main-section-header {font-size: 26px !important;        }        /* ====== divider ====== */        .divider img {width: 280px !important;        }        /*-------- container --------*/        .container590 {width: 280px !important;        }        .container590 {width: 280px !important;        }        .container580 {width: 260px !important;        }        /*-------- secions ----------*/        .section-img img {width: 280px !important;height: auto !important;        }    }</style><!--[if gte mso 9]><style type=�text/css�>    body {    font-family: arial, sans-serif!important;    }    </style><![endif]--></head><body class="respond" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"><!-- pre-header --><table style="display:none!important;">    <tr>        <td><div style="overflow:hidden;display:none;font-size:1px;color:#ffffff;line-height:1px;font-family:Arial;maxheight:0px;max-width:0px;opacity:0;">   Sistema de Gestion de Vacantes</div>        </td>    </tr></table><!-- pre-header end --><!-- header --><table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff">    <tr>        <td align="center"><table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td></tr><tr>    <td align="center">        <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr><td align="center" height="70" style="height:70px;">    <a href="" style="display: block; border-style: none !important; border: 0 !important;"><img width="100" border="0" style="display: block; width: 100px;" src="http://gestionvacantes.com/global_assets/images/logo_dark.png" alt="" /></a></td></tr><tr><td align="center">&nbsp;</td></tr>        </table>    </td></tr><tr>    <td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td></tr></table>        </td>    </tr></table><!-- end header --><!-- big image section --><table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="ffffff" class="bg_color">    <tr>        <td align="center"><table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>    <td align="center" style="color: #343434; font-size: 24px; font-family: Quicksand, Calibri, sans-serif; font-weight:700;letter-spacing: 3px; line-height: 35px;"        class="main-header">        <!-- section text ======-->        <div style="line-height: 35px">Recuperacion de password</span>        </div>    </td></tr><tr>    <td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td></tr><tr>    <td align="center">        <table border="0" width="40" align="center" cellpadding="0" cellspacing="0" bgcolor="eeeeee"><tr><td height="2" style="font-size: 2px; line-height: 2px;">&nbsp;</td></tr>        </table>    </td></tr><tr>    <td height="20" style="font-size: 20px; line-height: 20px;">&nbsp;</td></tr><tr>    <td align="left">        <table border="0" width="590" align="center" cellpadding="0" cellspacing="0" class="container590"><tr><td align="left" style="color: #888888; font-size: 16px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">    <!-- section text ======-->    <p style="line-height: 24px; margin-bottom:15px;">Estimado Usuario, se ha recibido una solicitud de recuperacion de password. </p>
<p style="line-height: 24px; margin-bottom:20px;">Para establecer su password, da clic en el siguiente enlace:</p>    <table border="0" align="center" width="180" cellpadding="0" cellspacing="0" bgcolor="5caad2" style="margin-bottom:20px;">    <tr><td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td></tr>    <tr><td align="center" style="color: #ffffff; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 22px; letter-spacing: 2px;">    <!-- main section button --><div style="line-height: 22px;"><a href="https://secape.me/intevo/vacantes/f_cambio_passr.php?usuario=' . $elusuario . '&key='. $llave .'" style="color: #ffffff; text-decoration: none;">Acceso</a>    </div></td></tr>    <tr><td height="10" style="font-size: 10px; line-height: 10px;">&nbsp;</td></tr></table>    <p style="line-height: 24px">Saludos cordiales,</br>
         SGRH Admin     </p>    </td>    </tr></table>    </td></tr>    </table></td>    </tr>    <tr><td height="40" style="font-size: 40px; line-height: 40px;">&nbsp;</td>    </tr></table><!-- end section --><!-- main section --><!-- end section --><!-- contact section --><!-- end section --><!-- footer ====== -->  <table border="0" width="100%" cellpadding="0" cellspacing="0" bgcolor="f4f4f4">    <tr><td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>    </tr>    <tr><td align="center">    <table border="0" align="center" width="590" cellpadding="0" cellspacing="0" class="container590"><tr>    <td><table border="0" align="left" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"    class="container590">    <tr><td align="left" style="color: #aaaaaa; font-size: 14px; font-family: "Work Sans", Calibri, sans-serif; line-height: 24px;">    <div style="line-height: 24px;">    <span style="color: #333333;">Sistema de Gestion de Recursos Humanos INTEVO 2020</span></div></td>    </tr></table><table border="0" align="left" width="5" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"    class="container590">    <tr><td height="20" width="5" style="font-size: 20px; line-height: 20px;">&nbsp;</td>    </tr></table><table border="0" align="right" cellpadding="0" cellspacing="0" style="border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt;"    class="container590"><tr><td align="center">    <table align="center" border="0" cellpadding="0" cellspacing="0"><tr><td align="center">   </td></tr>    </table></td>    </tr></table>    </td></tr>    </table></td>    </tr>    <tr><td height="25" style="font-size: 25px; line-height: 25px;">&nbsp;</td>    </tr></table><!-- end footer ====== --></body></html>';
$mail->Body = $body;

if (!$mail->send()) {    echo "Mailer Error: " . $mail->ErrorInfo;}

}
} ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
	<meta name="robots" content="noindex" />

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="https://www.google.com/recaptcha/api.js?hl=es" async defer></script>
	<!-- /theme JS files -->
</head>

<body class="login-container">

	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<a class="navbar-brand" href="../index.php"><img src="global_assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			</ul>
		</div>

	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">

   						<!-- Basic alert -->
                        <?php if($error == 1) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Verifica la información ingresada.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

   						<!-- Basic alert -->
                        <?php if($error == 2) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Verifica la información ingresada.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

   						<!-- Basic alert -->
                        <?php if($error == 3) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Debes comprobar la validación.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

   						<!-- Basic alert -->
                        <?php if($error == 4) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han enviado las instrucciones correctamente al correo señalado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



				<!-- Content area -->
				<div class="content">
                               
					<!-- Password recovery -->
                     <form action="login_password_recover.php" method="post">
                    <div class="panel panel-body login-form">
							<div class="text-center">
								<div class="icon-object border-warning text-warning"><i class="icon-spinner11"></i></div>
								<h5 class="content-group">Recuperar Password<small class="display-block">Te enviaremos las instrucciones por correo.</small></h5>
							</div>


                           <div class="col-lg-12">
							<div class="form-group has-feedback has-feedback-left">
                             <input type="email" class="form-control" name="email" id="email" required="required" placeholder="<?php if (isset($_POST['email']))
							 { echo $_POST['email']; } else { echo "Tu correo";} ?>">
								<div class="form-control-feedback">
									<i class="icon-mail5 text-muted"></i>
								</div>
							</div>
							</div>
                          
							 <div>&nbsp;</div>	
							 <div>&nbsp;</div>	
							 <div>&nbsp;</div>	

							<?php if ($error != 3) {?>
							<button type="submit" class="btn bg-blue btn-block">Restablecer Password<i class="icon-arrow-right14 position-right"></i></button>
							<?php } else {?>
                             <div><a class="btn bg-blue btn-block" href="../index.php">Regrasar</a></div>	
							<?php } ?>
                         </div>
					</form>
					<!-- /password recovery -->


					<!-- Footer -->
					<div class="footer text-muted text-center">
						&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
					</div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>
