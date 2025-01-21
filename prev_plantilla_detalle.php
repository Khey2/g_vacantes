<?php require_once('Connections/vacantes.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php');


// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
$restrict->addLevel("3");
$restrict->addLevel("4");
$restrict->addLevel("5");
$restrict->addLevel("5");
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
$desfase = $row_variables['dias_desfase'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDusuario = $row_usuario['IDusuario'];
$las_matrizes = $row_usuario['IDmatrizes'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];
$nivel = $_SESSION['kt_login_level'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

$el_empleado = $_GET['IDempleado']; 
mysql_select_db($database_vacantes, $vacantes);
$query_empleados = "SELECT vac_sucursal.sucursal, vac_matriz.matriz, vac_puestos.denominacion,  vac_puestos.IDaplica_PROD, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.fecha_alta, prod_activos.fecha_antiguedad, prod_activos.fecha_nacimiento, prod_activos.sueldo_mensual, prod_activos.sueldo_total, prod_activos.sueldo_diario, prod_activos.sobre_sueldo, prod_activos.sueldo_total, prod_activos.descripcion_nomina, prod_activos.IDarea, vac_areas.area, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_sucursal ON vac_sucursal.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea WHERE IDempleado = '$el_empleado'";
$empleados = mysql_query($query_empleados, $vacantes) or die(mysql_error());
$row_empleados = mysql_fetch_assoc($empleados);
$totalRows_empleados = mysql_num_rows($empleados);

?>
<!DOCTYPE html>
<html lang="en" xmlns:wdg="http://ns.adobe.com/addt">
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/form_validation.js"></script>
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		<?php require_once('assets/pheader.php'); ?>
<!-- Content area -->
				<div class="content">

					<!-- Colored tabs -->
					<div class="row">
						
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Detalles de Empleado</h6>
								</div>

								<div class="panel-body">
									<div class="tabbable">
										<ul class="nav nav-tabs bg-teal-400">
											<li><a href="#colored-justified-tab2" data-toggle="tab">Datos Personales</a></li>
										</ul>

										<div class="tab-content">
                                            
					<p>A continución se muestran los datos del Empleado.</br>
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>No. Emp:</strong></label>
								  <div class="col-lg-9">
						<?php echo $row_empleados['IDempleado']; ?>
								  </div>
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Nombre Completo:</strong></label>
								  <div class="col-lg-9"><?php echo $row_empleados['emp_paterno'] . " " . $row_empleados['emp_materno']  . " " . $row_empleados['emp_nombre'] ; ?>
								  </div>
							  </div>
							  <!-- /basic text input -->


                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>RFC:</strong></label>
								  <div class="col-lg-9">
						<?php echo $row_empleados['rfc']; ?>
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Fecha -->
                              <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Fecha Alta:</strong></label>
		                        <div class="col-lg-9">
		                          <div class="input-group">
                                   	  <?php echo date('d/m/Y', strtotime($row_empleados['fecha_alta'])); ?>
								  </div>
                                </div>
                            </div> 
							  <!-- Fecha -->

                            <!-- Fecha -->
                              <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Fecha de Antiguedad:</strong></label>
		                        <div class="col-lg-9">
		                          <div class="input-group">
                                    <?php echo date('d/m/Y', strtotime($row_empleados['fecha_antiguedad'])); ?>
								  </div>
                                </div>
                            </div> 
							  <!-- Fecha -->

                            <!-- Fecha -->
                              <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Fecha de Nacimiento:</strong></label>
		                        <div class="col-lg-9">
		                          <div class="input-group">
                                    <?php echo date('d/m/Y', strtotime($row_empleados['fecha_nacimiento'])); ?>
								  </div>
                                </div>
                            </div> 
							  <!-- Fecha -->

						<?php if($row_usuario['sueldos'] == 1) {?>


                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Sueldo Mensual:</strong></label>
								  <div class="col-lg-9">
						<?php echo $row_empleados['sueldo_mensual']; ?>
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Sueldo Diario:</strong></label>
								  <div class="col-lg-9">
						<?php echo $row_empleados['sueldo_diario']; ?>
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Sobre Sueldo:</strong></label>
								  <div class="col-lg-9">
						<?php echo $row_empleados['sobre_sueldo']; ?>
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Sueldo Total:</strong></label>
								  <div class="col-lg-9">
						<?php echo $row_empleados['sueldo_total']; ?>
								  </div>
							  </div>
							  <!-- /basic text input -->

						<?php } ?>


							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Nómina:</strong></label>
								  <div class="col-lg-9">
									<?php echo $row_empleados['descripcion_nomina']; ?>
								  </div>
							  </div>
							  <!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Área:</strong></label>
								  <div class="col-lg-9">
									 <?php echo $row_empleados['area']; ?>
								  </div>
							  </div>
							  <!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3"><strong>Puesto:</strong></label>
										<div class="col-lg-9">
											<?php echo $row_empleados['denominacion']; ?>
										</div>
									</div>
									<!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Matriz:</strong></label>
								  <div class="col-lg-9">
									 <?php echo $row_empleados['matriz']; ?>
								  </div>
							  </div>
							  <!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Sucursal:</strong></label>
								  <div class="col-lg-9">
									 <?php echo $row_empleados['sucursal']; ?>
								  </div>
							  </div>
							  <!-- /basic select -->
                      </form>
					  
			<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-warning btn-icon">Restaurar Password</button>
					  
					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Restauración</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres restaurar el password?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admin_usuarios_reset_prod_activos.php?IDempleado=<?php echo $row_empleados['IDempleado']; ?>">Si restaurar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /colored tabs -->




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