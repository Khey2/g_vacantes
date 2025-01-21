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
$query_usuario = sprintf("SELECT prod_activosj.IDturno, prod_activosj.IDjornada, prod_activosj.IDsucursal, prod_activosj.IDhoffice, prod_activosj.comentarios, prod_activos.nivel_acceso, vac_matriz.IDmatriz, vac_matriz.matriz, vac_matriz.IDmatriz, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.IDmatriz, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc13,  prod_activos.curp, prod_activos.fecha_alta, prod_activos.denominacion FROM prod_activos left JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN prod_activosj ON prod_activos.IDempleado = prod_activosj.IDempleado WHERE prod_activos.IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];
$IDsucursal = $row_usuario['IDsucursal'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDsucursal = $IDsucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>

	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/login_validation.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<!-- /theme JS files -->

</head>

<body class="has-detached-right">
	
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

					<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Mi Perfil</h5>
						</div>

					<div class="panel-body">
							<p>A continuación se muestra tu información personal.</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                            
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">No. de Empleado:</label>
										<div class="col-lg-9">
						                  <p><strong><?php echo $row_usuario['IDempleado']; ?> </strong></p>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:</label>
										<div class="col-lg-9">
						<?php echo $row_usuario['emp_nombre']." ".$row_usuario['emp_paterno']." ".$row_usuario['emp_materno']; ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">RFC:</label>
										<div class="col-lg-9">
						<?php echo htmlentities($row_usuario['rfc13'], ENT_COMPAT, ''); ?>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">CURP:</label>
                                        <div class="col-lg-9">
                                        <?php echo htmlentities($row_usuario['curp'], ENT_COMPAT, ''); ?>
                                    	</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha de Alta:</label>
                                    <div class="col-lg-9">
                                    <?php echo date( 'd/m/Y', strtotime($row_usuario['fecha_alta'])); ?>
                                                    </div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:</label>
                                        <div class="col-lg-9">
                                        <?php echo htmlentities($row_usuario['denominacion'], ENT_COMPAT, ''); ?>
                                    	</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Horario:</label>
                                        <div class="col-lg-9">
										<?php	   if ($row_usuario['IDturno'] == 1) { echo "8:00 a 17:00 Horas";}
											  else if ($row_usuario['IDturno'] == 2) { echo "9:00 a 18:00 Horas";}
											  else if ($row_usuario['IDturno'] == 3) { echo "Otro";}
											  else 	{ echo "Sin definir";} ?>
                                    	</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Jornada Laboral:</label>
                                        <div class="col-lg-9">
										<?php	   if ($row_usuario['IDjornada'] == 1) { echo "Lunes a Viernes";}
											  else if ($row_usuario['IDjornada'] == 2) { echo "Lunes a Sabado";}
											  else if ($row_usuario['IDjornada'] == 3) { echo "Otro";}
											  else 	{ echo "Sin definir";} ?>
                                    	</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Labora en Home Office?:</label>
                                        <div class="col-lg-9">
										<?php	   if ($row_usuario['IDhoffice'] == 1) { echo "Si";}
											  else if ($row_usuario['IDhoffice'] == 2) { echo "No";}
											  else if ($row_usuario['IDhoffice'] == 3) { echo "Parcial";}
											  else 	{ echo "Sin definir";} ?>
                                    	</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Ubicacion:</label>
                                        <div class="col-lg-9">
										<?php echo $row_sucursal['sucursal']; ?>
                                    	</div>
									</div>
									<!-- /basic text input -->

                            </form>
                    </div>

						</div>
					</div>
					<!-- /detached content -->


					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default">
							<div class="sidebar-content">


								<!-- Task navigation -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Mi Perfil</span>
									</div>

									<div class="category-content no-padding">
										<ul class="navigation navigation-alt navigation-accordion">
											<li class="navigation-header">Acciones</li>
											<li><a href="mailto:<?php echo $row_variables['contacto_interno']; ?> ?subject=Cambio%20Password"><i class="icon-envelop2"></i>Cambiar Password</a></li>
										    <li><a href="mailto:<?php echo $row_variables['contacto_interno']; ?>"><i class="icon-envelop2"></i> Enviar correo al Admin</a></li>
										</ul>
									</div>
								</div>
								<!-- /task navigation -->

							</div>
						</div>
					</div>
					<!-- /detached sidebar -->


					<!-- Footer -->
					<div class="footer text-muted">
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