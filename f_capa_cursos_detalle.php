<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDempleado'];

if ($row_usuario['password'] == md5($row_usuario['IDempleado'])) { header("Location: f_cambio_pass.php?info=6"); } 

$IDmodulo = $_GET['IDmodulo'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_capacitacion = "SELECT * FROM capa_curso_seguridad WHERE IDempleado = $el_usuario";
$capacitacion = mysql_query($query_capacitacion, $vacantes) or die(mysql_error());
$row_capacitacion = mysql_fetch_assoc($capacitacion);
$totalRows_capacitacion = mysql_num_rows($capacitacion);

if ( $row_capacitacion['modulo1'] != '') {$modulo1 = 1;} else {$modulo1 = 0;}
if ( $row_capacitacion['modulo2'] != '') {$modulo2 = 1;} else {$modulo2 = 0;}
if ( $row_capacitacion['modulo3'] != '') {$modulo3 = 1;} else {$modulo3 = 0;}
if ( $row_capacitacion['modulo4'] != '') {$modulo4 = 1;} else {$modulo4 = 0;}
if ( $row_capacitacion['modulo5'] != '') {$modulo5 = 1;} else {$modulo5 = 0;}
if ( $row_capacitacion['evaluacion'] != '') {$evaluacion = 1;} else {$evaluacion = 0;}
if ($modulo1 + $modulo2 + $modulo3 + $modulo4 + $modulo5 == 5) {$acceso_evaluacion = 1;} else {$acceso_evaluacion = 0;}



mysql_select_db($database_vacantes, $vacantes);
$query_capacitacionl = "SELECT * FROM capa_curso_seguridad_ligas WHERE IDmodulo = $IDmodulo";
$capacitacionl = mysql_query($query_capacitacionl, $vacantes) or die(mysql_error());
$row_capacitacionl = mysql_fetch_assoc($capacitacionl);
$totalRows_capacitacionl = mysql_num_rows($capacitacionl);

?>
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
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<!-- /theme JS files -->

	<script>
	var timer = setInterval(function() {
        var current = $("#button").data('time');
        
        // get the current value of the time in seconds
        var newtime = current - 1;
        $("#button").data('time', newtime);
        console.log(newtime);
        if (newtime <= 0) {
            // time is less than or equal to 0
            // so disable the button and stop the interval function
            $("#button").prop('disabled', false);
            clearInterval(timer);
        } else {
            // timer is still above 0 seconds so decrease it
            $("#button").data('time', newtime);
        }
    }, 1000);

	</script>


</head>
<body class="has-detached-right <?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>">

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	

            <?php require_once('assets/f_pheader.php'); ?>

				<!-- Content area -->
				<div class="content">
                
						<!-- Basic alert -->
                        <?php if($IDmodulo == 1 AND $modulo1 == 1) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Este Módulo ya lo has completado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($IDmodulo == 2 AND $modulo2 == 1) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Este Módulo ya lo has completado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($IDmodulo == 3 AND $modulo3 == 1) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Este Módulo ya lo has completado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($IDmodulo == 4 AND $modulo4 == 1) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Este Módulo ya lo has completado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($IDmodulo == 5 AND $modulo5 == 1) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Este Módulo ya lo has completado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

				
					<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">

							<!-- Course overview -->
							<div class="panel panel-white">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Curso de Ciberseguridad Sahuayo</h6>
								</div>

								<div class="tab-content">
										<div class="panel-body">
											<div class="content-group-lg">
												<p><h6><b>Tema: </b><?php echo $row_capacitacionl['nombre']; ?>.</p></h6>
												<p>La ciberseguridad es fundamental en la era digital actual, donde la mayoría de nuestras actividades, tanto personales como empresariales, dependen de sistemas y redes informáticas interconectadas.<br/>
												<spam class="text text-semibold">Una vez que hayas termiando de ver el video, da clic en el boton de "Terminar y Regresar" para que se registre tu avance.</spam><br/>
												Para cualquier duda respecto del Curso, contacta a <span class="text text-primary">Esperanza Flores</span>  al correo  <a href="mailto:EGFlores@sahuayo.mx">EGFlores@sahuayo.mx</a>.</p>

												<input type='submit' value='Terminar y Regresar' onclick="window.location='f_capa_cursos.php?IDmodulo=<?php echo $IDmodulo; ?>'" class="btn btn-success" id='button' disabled="disabled" name='button' data-time='120' />								
												<button type="button" onClick="window.location.href='f_capa_cursos.php'" class="btn btn-default btn-icon">Cancelar</button>
												<p>&nbsp;</p>
												
												<?php echo $row_capacitacionl['liga']; ?>
												
       											</div>
											</div>
										</div>
								</div>
							</div>
							<!-- /course overview -->



						</div>
					</div>
					<!-- /detached content -->


					<!-- /panel heading options -->

					<!-- Footer -->
					<div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <span class="text text-primary"><?php echo $row_variables['nombre_sistema']; ?></> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
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