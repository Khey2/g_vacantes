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

$restrict->addLevel("3");
$restrict->addLevel("4");
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
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
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
$query_empleados = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.fecha_alta, prod_activos.IDaplica_SED, prod_activos.IDaplica_PROD, prod_activos.fecha_antiguedad, prod_activos.fecha_nacimiento, prod_activos.sueldo_mensual, prod_activos.sueldo_diario, prod_activos.sobre_sueldo, prod_activos.IDempleadoJ, prod_activos.sueldo_total, prod_activos.descripcion_nomina, prod_activos.IDarea, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.denominacion, prod_llave.IDaplica_PROD, vac_matriz.matriz, vac_puestos.IDaplica_PROD, vac_areas.area FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON prod_activos.IDarea = vac_areas.IDarea LEFT JOIN prod_llave ON prod_activos.IDllave = prod_llave.IDllave WHERE prod_activos.IDempleado = '$el_empleado'";
$empleados = mysql_query($query_empleados, $vacantes) or die(mysql_error());
$row_empleados = mysql_fetch_assoc($empleados);
$totalRows_empleados = mysql_num_rows($empleados);
$boss = $row_empleados['IDempleadoJ'];
$aplica_clima = " ".$row_empleados['IDpuesto'].",";


$query_boss = "SELECT * FROM prod_activos WHERE IDempleado = '$boss'";
$boss = mysql_query($query_boss, $vacantes) or die(mysql_error());
$row_boss = mysql_fetch_assoc($boss);
$el_jefe = $row_boss['emp_nombre']." ".$row_boss['emp_paterno']." ".$row_boss['emp_materno'];
$el_jefe_puesto = $row_boss['denominacion'];

$los_puestos = "87, 145, 146, 147, 148, 149, 150, 120, 250, 252, 95, 96, 176, 253, 254, 121, 154, 177, 97, 98, 203, 221, 211, 202, 209, 255, 220, 207, 227, 232, 218, 219, 222, 204, 225, 214, 217, 233, 256, 215, 234, 272, 241, 257, 205, 224, 262, 223, 261, 258, 208, 231, 216, 99, 100, 101, 102, 122, 10, 123, 36, 103, 124, 37, 125, 180, 181, 126, 11, 12, 13, 182, 201, 127, 128, 129, 51, 130, 131, 183, 184, 265, 264, 266, 267, 191, 213, 192, 17, 270, 56, 58, 193, 198, 235, 237, 238, 239, 240";


// select para Jefe
if(isset($_GET['noboss'])) {
mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE IDpuesto IN ($los_puestos) OR manual IS NOT NULL ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);

} else {
mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE IDmatriz = '$IDmatriz' AND IDpuesto IN ($los_puestos) OR manual IS NOT NULL ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $IDempleado = $_GET['IDempleado'];
  $IDempleadoJ = $_POST['IDempleadoJ'];	
  
  $updateSQL = "UPDATE prod_activos SET IDempleadoJ = '$IDempleadoJ' WHERE IDempleado = '$IDempleado'"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateSQL2 = "UPDATE prod_activosj SET IDempleadoJ = '$IDempleadoJ' WHERE IDempleado = '$IDempleado'"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result2 = mysql_query($updateSQL2, $vacantes) or die(mysql_error());


  header("Location: admin_plantilla_activos_detalle.php?info=1&IDempleado=$IDempleado");
}


?>
<!DOCTYPE html>
<html lang="en" xmlns:wdg="http://ns.adobe.com/addt">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>


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

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	
	<?php require_once('assets/mainnav.php'); ?>
	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		
			<?php require_once('assets/pheader.php'); ?>
				<!-- Content area -->
				<div class="content">
                
                
                         <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actulizado correctamente el Jefe Inmediato.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
						
						                
                		 <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el empleado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



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
								  <label class="control-label col-lg-3"><strong>Aplica Desempeño:</strong></label>
								  <div class="col-lg-9">
									 <?php if($row_empleados['IDaplica_SED'] == 1) {echo "SI";} else {echo "NO";} ?>
								  </div>
							  </div>
							  <!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Aplica Productividad:</strong></label>
								  <div class="col-lg-9">
									 <?php if($row_empleados['IDaplica_PROD'] == 1) {echo "SI";} else {echo "NO";} ?>
								  </div>
							  </div>
							  <!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3"><strong>Jefe Inmediato:</strong></label>
								  <div class="col-lg-9">
									 <?php echo $el_jefe. " (". $el_jefe_puesto.")"; ?>
								  </div>
							  </div>
							  <!-- /basic select -->



                      </form>
                      
                      
			<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-warning btn-icon">Restaurar Password</button>
            <button type="button" data-target="#capturar" data-toggle="modal" class="btn btn-primary">Cambiar línea de mando</button>
			<?php if (strlen(stristr($los_puestos, $aplica_clima))==0) { ?>
            <button type="button" onClick="window.location.href='admin_clima_agregar.php?IDempleado=<?php echo $el_empleado; ?>'" class="btn btn-danger btn-icon">Agregar a Líderes de Clima</button>
			<?php } ?>
            <button type="button" onClick="window.location.href='admin_plantilla_activos.php'" class="btn btn-default btn-icon">Regresar</button>
            </div>

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
                                    <a class="btn btn-danger" href="master_admin_usuarios_reset_prod_activos.php?IDempleado=<?php echo $row_empleados['IDempleado']; ?>">Si restaurar</a>
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


                    <!-- Modal de Captura -->
					<div id="capturar" class="modal fade" tabindex="-3">
						<div class="modal-dialog modal-lg">
							<div class="modal-content text-center">
								<div class="modal-header bg-success">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
               					  <h5 class="modal-title">Cambio de linea de mando</h5>
								</div>

            					<form method="post" class="form-horizontal form-validate-jquery" name="form1" action="admin_plantilla_activos_detalle.php?IDempleado=<?php echo $row_empleados['IDempleado']; ?>" > 
                                <fieldset class="content-group">
                                <div class="modal-body">

                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3"><div class="text-bold content-group">Jefe Actual:</div></label>
										<div class="col-lg-9">
									 <?php echo $el_jefe. " (". $el_jefe_puesto.")"; ?>
                         				</div>
                         				</div>
									<!-- /basic text input -->
                                    
                                    
									<div class="form-group">
										<label class="control-label col-lg-3"><div class="text-bold content-group">Jefe Nuevo:<span class="text-danger">*</span></div></label>
										<div class="col-lg-9">
										<select class="bootstrap-select" data-live-search="true" data-width="100%" name="IDempleadoJ" id="IDempleadoJ" required="required">
										<option value="">Selecciona el Jefe Inmediato</option>
                                        			  <?php  do { ?>
													  <option value="<?php echo $row_jefes['IDempleado']?>"><?php echo $row_jefes['emp_nombre'] . " " . $row_jefes['emp_paterno'] . " " . $row_jefes['emp_materno'] .  " (". $row_jefes['denominacion'] . ")"; ?></option>
													  <?php
													 } while ($row_jefes = mysql_fetch_assoc($jefes));
													   $rows = mysql_num_rows($jefes);
													   if($rows > 0) {
													   mysql_data_seek($jefes, 0);
													   $row_jefes = mysql_fetch_assoc($jefes);
													 } ?>
										</select>										
                                        </div>
									</div>
									<a href="admin_plantilla_activos_detalle.php?IDempleado=<?php echo $row_empleados['IDempleado']; ?>&noboss=1"
                                     class="label label-warning">Haz clic aqui </a> para ampliar la lista.	
									<!-- /basic select -->
                                    
                                    <hr>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          	                      		<input type="hidden" name="MM_update" value="form1">
          	                      		<input type="hidden" name="IDempleado" value="<?php  echo $row_empleados['IDempleado']; ?>">
                                        <input type="submit" class="btn btn-primary" value="Cambiar Jefe">
									</div>
								
                                </div>
                                </fieldset>
                                </form>
                                
                           </div>
                        </div>
                     </div>
                    <!-- //Modal de Captura -->

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