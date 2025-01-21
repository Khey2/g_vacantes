<?php require_once('Connections/vacantes.php'); ?>
<?php
require_once "includes/recaptchalib.php";
// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
$restrict->Execute();
//End Restrict Access To Page

header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works

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


$IDempleado = $_SESSION['kt_login_id']; 
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM prod_activos WHERE IDempleado = '$IDempleado'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$el_usuario = $row_usuario['IDempleado'];
$elrfc = $row_usuario['rfc'];

mysql_select_db($database_vacantes, $vacantes);
$query_cambio = "SELECT * FROM prod_activosj WHERE prod_activosj.IDempleado = '$IDempleado'";
$cambio = mysql_query($query_cambio, $vacantes) or die(mysql_error());
$row_cambio = mysql_fetch_assoc($cambio);
$totalRows_cambio = mysql_num_rows($cambio);

//captcha
$secret = "6Ld8htgSAAAAAIFzIt6RwA9qvESlkPi8FaXzTqX2";
$response = null;
 // comprueba la clave secreta

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $rfc = $row_cambio['rfc'];
  echo $_POST['password'];
  $password = md5($_POST['password']);
  $IDempleado = $_POST['IDempleado'];
  $updateSQL = sprintf("UPDATE prod_activos SET password=%s WHERE IDempleado=%s",
                       GetSQLValueString($password, "text"),
                       GetSQLValueString($_POST['IDempleado'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  
  if($totalRows_cambio > 0){

  $updateSQL = "UPDATE prod_activosj SET password = '$password' WHERE IDempleado = '$IDempleado'"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  
  } else {

  $updateSQL = "INSERT INTO prod_activosj (IDempleado, password) VALUES ('$IDempleado', '$password')"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  }
header('Location: f_panel.php?info=3');
}

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/gif" href="global_assets/images/logo.ico">
	<link href="global_assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/colors.min.css" rel="stylesheet" type="text/css">
	<link href="includes/password.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script src="global_assets/js/plugins/loaders/pace.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery.min.js"></script>
	<script src="global_assets/js/core/libraries/bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<script src="https://www.google.com/recaptcha/api.js?hl=es" async defer></script>
    <script> $(document).ready(function () {
    $('#password').keyup(function () {
        $('#strengthMessage').html(checkStrength($('#password').val()))
    })
    function checkStrength(password) {
        var strength = 0;
		<?php echo "var el_rfc ='$elrfc';"; ?>
		if (password.length < 8) {
            $('#strengthMessage').removeClass()
            $('#strengthMessage').addClass('Short')
			$('#send').attr("disabled", true);
            return 'Ingresa al menos 8 caracteres.'
        }
        if (password == el_rfc) {
            $('#strengthMessage').removeClass()
            $('#strengthMessage').addClass('Short')
			$('#send').attr("disabled", true);
            return 'Ingresa un valor diferente a tu RFC.'
        }
        if (password.length > 8) strength += 1
        // If password contains both lower and uppercase characters, increase strength value.
        if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1
        // If it has numbers and characters, increase strength value.
        if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1
        // If it has one special character, increase strength value.
        if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
        // If it has two special characters, increase strength value.
        if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
        // Calculated strength value, we can return messages
        // If value is less than 2
        if (strength < 2) {
            $('#strengthMessage').removeClass()
            $('#strengthMessage').addClass('Weak')
			$('#send').attr("disabled", true);
			return 'Debes ingresar una Contraseña mas fuerte'
        } else if (strength == 2) {
            $('#strengthMessage').removeClass()
            $('#strengthMessage').addClass('Good')
			$('#send').attr("disabled", false);
            return 'Bueno'
        } else {
            $('#strengthMessage').removeClass()
            $('#strengthMessage').addClass('Strong')
			$('#send').attr("disabled", false);
            return 'Fuerte'
        }
    }

});
</script>
<script type="text/javascript">
function mostrarPassword(){
		var cambio = document.getElementById("password");
		if(cambio.type == "password"){
			cambio.type = "text";
			$('.icon').removeClass('fa fa-eye-slash').addClass('fa fa-eye');
		}else{
			cambio.type = "password";
			$('.icon').removeClass('fa fa-eye').addClass('fa fa-eye-slash');
		}
	} 
	
	$(document).ready(function () {
	//CheckBox mostrar contraseña
	$('#ShowPassword').click(function () {
		$('#Password').attr('type', $(this).is(':checked') ? 'text' : 'password');
	});
});
</script>
<!-- /theme JS files -->
</head>


<body class="login-container">

	<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
            <a class="navbar-brand" href="f_panel.php"><img src="global_assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav navbar-right">

				<li class="dropdown">
					<a href="f_mi_perfil.php">
						<i class="icon-cog3"></i> <span class="visible-xs-inline-block position-right">Mi Perfil</span>
					</a>
				</li>
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

				<!-- Content area -->
				<div class="content">
                             



					<!-- Password recovery -->
					<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
						<div class="panel panel-body login-form">


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 6))) { ?>
					    <div class="alert bg-info-400 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"></button>
							Estimado usuario, por seguridad, debes cambiar tu password.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


							<div class="text-center">
								<div class="icon-object border-warning text-warning"><i class="icon-spinner11"></i></div>
								<h5 class="content-group">Cambio de Password</h5>
							</div>
                            
                            
									<div class="form-group has-feedback has-feedback-left">
										<div class="col-lg-12">
											
                                            <div class="input-group">
											<input type="password" name="password" id="password" class="form-control" required="required" placeholder="Ingresa tu nuevo password">
                                                <div class="input-group-btn">
                                                 <button id="show_password" class="btn btn-info" type="button" onClick="mostrarPassword()"> <span class="icon-eye"></span> </button>
                                                </div>
                                   			<div class="form-control-feedback">
								   			<i class="icon-lock2 text-muted"></i>
								   			</div>                                 
									      </div>
                                         
                                         <div id="strengthMessage"></div>
										
<p>&nbsp;</p>
								<div class="row">
									<div class="col-sm-6">
									
									</div>
									</div>
                                        
                                        
                                        </div>
									</div>
<p>&nbsp;</p>
                                    
                                    

  							  <button id="send" type="submit" class="btn bg-blue btn-block">Cambiar password<i class="icon-arrow-right14 position-right"></i></button>
                              <input type="hidden" name="MM_update" value="form1">
                              <input type="hidden" name="IDempleado" value="<?php echo $row_usuario['IDempleado']; ?>">
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