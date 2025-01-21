<?php require_once('Connections/vacantes.php'); 
require_once "includes/recaptchalib.php";

// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

// Start trigger
$formValidation = new tNG_FormValidation();
$formValidation->addField("kt_login_user", true, "text", "", "", "", "Ingresa tu usuario");
$formValidation->addField("kt_login_password", true, "text", "", "", "", "Ingresa tu Password");
$tNGs->prepareValidation($formValidation);
// End trigger

if (isset($_SESSION['kt_login_id'])) { header("Location: panel.php"); }
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


//! Migracion a conn
$conn = new mysqli( $hostname_vacantes, $username_vacantes, "", $database_vacantes );



$query_variables = "SELECT * FROM vac_variables";
$result = $conn->query( $query_variables );
// Comprobar si la consulta se ejecutó correctamente 
if ($result === FALSE) { die("Error: " . $conn->error); };
// Obtener los resultados 
$row_variables = $result->fetch_assoc(); 
$totalRows_variables = $result->num_rows;


// mysql_select_db($database_vacantes, $vacantes);
// $query_variables = "SELECT * FROM vac_variables";
// $variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
// $row_variables = mysql_fetch_assoc($variables);
// $totalRows_variables = mysql_num_rows($variables);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];

//captcha
$secret = "6Ld8htgSAAAAAIFzIt6RwA9qvESlkPi8FaXzTqX2";
$response = null;
 // comprueba la clave secreta
$reCaptcha = new ReCaptcha($secret);
 
 if (isset($_POST["g-recaptcha-response"])) {
     $response = $reCaptcha->verifyResponse(
     $_SERVER["REMOTE_ADDR"],
     $_POST["g-recaptcha-response"]
     );
  }

 if ($response != null && $response->success) {

// Make a login transaction instance
$loginTransaction = new tNG_login($conn_vacantes);
$tNGs->addTransaction($loginTransaction);
// Register triggers
$loginTransaction->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "kt_login1");
$loginTransaction->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$loginTransaction->registerTrigger("END", "Trigger_Default_Redirect", 99, "{kt_login_redirect}");
// Add columns
$loginTransaction->addColumn("kt_login_user", "STRING_TYPE", "POST", "kt_login_user");
$loginTransaction->addColumn("kt_login_password", "STRING_TYPE", "POST", "kt_login_password");
// End of login transaction instance

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rscustom = $tNGs->getRecordset("custom");
$row_rscustom = mysql_fetch_assoc($rscustom);
$totalRows_rscustom = mysql_num_rows($rscustom);
  } else { $recaptcha = 1;}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema'];?></title>

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
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/login.js"></script>
	<script src="https://www.google.com/recaptcha/api.js?hl=es" async defer></script>
<!-- /theme JS files -->

</head>

<body class="login-container login-cover">

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">
            
               			<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && ($_GET['info'] == 3)) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han restablecido el password correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


				<!-- Content area -->
				<div class="content">
							 <div>&nbsp;</div>	

   					<!-- Tabbed form -->
					<div class="tabbable panel login-form width-400">
						<ul class="nav nav-tabs nav-justified">
							<li><a href="default.php"><h6>Acceso Empleados</h6></a></li>
							<li class="active"><a href="f_index.php"><h6>Acceso Gestión RH</h6></a></li>
						</ul>

						<div class="tab-content panel-body">
							<div class="tab-pane fade in active" id="basic-tab1">
							
							<form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>">
									<div class="text-center">
									<div>&nbsp;</div>	
										<div><img src="assets/img/logo_sahuayo.png" width="219" height="60" alt="logo"></div>
									<div>&nbsp;</div>	
									<h5 class="content-group-lg"><?php echo $row_variables['nombre_sistema'];?><small class="display-block">Acceso</small></h5>
									</div>

									<div class="form-group has-feedback has-feedback-left">
										<input type="email" class="form-control" name="kt_login_user" id="kt_login_user" placeholder="Correo Sahuayo" 
										value="<?php if(isset($_POST['kt_login_user'])) {echo KT_escapeAttribute($_POST['kt_login_user']); } ?>" required="required" >
										<div class="form-control-feedback">
											<i class="icon-user text-muted"></i>
									</div>
									</div>

									<div class="form-group has-feedback has-feedback-left">
										<input type="password" class="form-control" placeholder="Password" name="kt_login_password" id="kt_login_password" value="<?php if(isset($_POST['kt_login_password'])) {echo KT_escapeAttribute($_POST['kt_login_password']); } ?>" required="required">
										<div class="text-danger"><?php echo $tNGs->displayFieldHint("kt_login_password");?></div>
										<div class="text-danger"><?php echo $tNGs->displayFieldError("custom", "kt_login_password"); ?></div>
										<div class="text-danger"><?php echo $tNGs->displayFieldHint("kt_login_user");?></div>
										<div class="text-danger"> <?php echo $tNGs->displayFieldError("custom", "kt_login_user"); ?></div>
										<div class="form-control-feedback">
											<i class="icon-lock2 text-muted"></i>
									</div>
									</div>

									<div class="form-group login-options">
										<div class="row">
											<div class="col-sm-6">
											</div>
											<div class="g-recaptcha" data-sitekey="6Ld8htgSAAAAAKKTuo_p6Xd7Ezccry2zbfgtMEjQ"></div>
											<?php if(isset($recaptcha) && ($recaptcha == 1) && isset($_POST["g-recaptcha-response"])) { ?>
											<div class="text-danger">Comprueba la validación</div>                                     
											<?php } ?>
									<div>&nbsp;</div>	
											
									<div class="col-sm-12 text-right">
												<p><a href="mailto:jacardenas@sahuayo.mx">Olvidase tu Password?</a></p>
											</div>
											
									</div>
									</div>

								<div class="form-group">
									<button type="submit" name="kt_login1" id="kt_login1" class="btn bg-blue btn-block">Acceder 
									<i class="icon-circle-right2 position-right"></i></button>
									</div>
								<span class="help-block text-center no-margin">Accediendo, confirmas que aceptas los </br><a href="terminos.php">Términos y condiciones del servicio.</a></span>
							</form>
                                
							</div>

							
						</div>
					</div>
					<!-- /tabbed form -->

                    

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
<?php
	$result->free();
	$conn->close();

?>