<?php require_once('Connections/vacantes.php'); ?>
<?php
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

$currentPage = $_SERVER["PHP_SELF"];
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

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 
$semana = date("W", strtotime($la_fecha));

$colname_usuario = "-1";

if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//echo "apoyo: " . $el_apoyo;
//echo "Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDaplica_PROD = 1";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

//recogemos datos del empleado	
$IDpprueba = $_GET['IDpprueba'];
mysql_select_db($database_vacantes, $vacantes);
$query_empleado = "SELECT pp_prueba.sueldo_actual, pp_prueba.sueldo_nuevo, pp_prueba.file, pp_prueba.IDpprueba, pp_prueba.IDempleado, pp_prueba.IDpuesto, pp_prueba.IDarea, pp_prueba.IDmatriz, pp_prueba.IDpuesto_destino, pp_prueba.IDmatriz_destino, pp_prueba.IDarea_destino, pp_prueba.fecha_fin, pp_prueba.fecha_inicio, pp_prueba.IDestatus, pp_prueba.observaciones, prod_activos.descripcion_nomina,  prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, puesto_origen.denominacion AS denominacion_origen, area_oringen.area AS area_origen, matriz_origen.matriz AS matriz_origen, matriz_destino.matriz as matriz_destino, area_destino.area AS area_destino, puesto_destino.denominacion AS denominacion_destino FROM pp_prueba LEFT JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos AS puesto_origen ON pp_prueba.IDpuesto = puesto_origen.IDpuesto LEFT JOIN vac_areas AS area_oringen ON puesto_origen.IDarea = area_oringen.IDarea LEFT JOIN vac_matriz AS matriz_origen ON pp_prueba.IDmatriz = matriz_origen.IDmatriz LEFT JOIN vac_matriz AS matriz_destino ON pp_prueba.IDmatriz_destino = matriz_destino.IDmatriz LEFT JOIN vac_puestos AS puesto_destino ON pp_prueba.IDpuesto_destino = puesto_destino.IDpuesto LEFT JOIN vac_areas AS area_destino ON puesto_destino.IDarea = area_destino.IDarea WHERE pp_prueba.IDpprueba = $IDpprueba";
mysql_query("SET NAMES 'utf8'");
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);

mysql_select_db($database_vacantes, $vacantes);
$query_pprueba = "SELECT * FROM pp_prueba_pagos WHERE IDpprueba = $IDpprueba";  
$pprueba = mysql_query($query_pprueba, $vacantes) or die(mysql_error());
$row_pprueba = mysql_fetch_assoc($pprueba);
$totalRows_pprueba = mysql_num_rows($pprueba);

$IDempleado = $row_pprueba['IDempleado'];
$sueldo_actual = $row_empleado['sueldo_actual'];
$sueldo_nuevo = $row_empleado['sueldo_nuevo'];
if($row_empleado['descripcion_nomina'] == 'Nomina Semanal Sahuayo') {$tipo_nomina = 1;} else {$tipo_nomina = 2;}

mysql_select_db($database_vacantes, $vacantes);
$query_calendario = "SELECT * FROM pp_prueba_semanas WHERE IDtipo = $tipo_nomina AND fecha_pago > CURDATE() AND (anio = $anio OR anio = $anio + 1)";  
$calendario = mysql_query($query_calendario, $vacantes) or die(mysql_error());
$row_calendario = mysql_fetch_assoc($calendario);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
	
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$fecha1a = $_POST['fecha_pago']; 
$fecha1b = explode("/",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$insertSQL = sprintf("INSERT INTO pp_prueba_pagos (IDempleado, IDpprueba, fecha_pago, monto_pago, IDestatus) VALUES (%s, %s, %s, %s, %s)",
                       GetSQLValueString($row_empleado['IDempleado'], "int"),
                       GetSQLValueString($IDpprueba, "int"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($_POST['monto_pago'], "text"),
                       GetSQLValueString($_POST['IDestatus'], "int"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
 $last_id =  mysql_insert_id();
 header("Location: pprueba_edit_pagos.php?IDpprueba=$IDpprueba&info=1");
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$fecha1a = $_POST['fecha_pago']; 
$fecha1b = explode("/",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];
$updateSQL = sprintf("UPDATE pp_prueba_pagos SET fecha_pago=%s, monto_pago=%s, IDestatus=%s WHERE IDpprueba_pagos=%s",
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($_POST['monto_pago'], "text"),
                       GetSQLValueString($_POST['IDestatus'], "int"),
                       GetSQLValueString($_POST['IDpprueba_pagos'], "text"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();
 header("Location: pprueba_edit_pagos.php?IDpprueba=$IDpprueba&info=1");
}

// precarga de pagos
if ((isset($_GET['pregarca'])) && ($_GET['pregarca'] != "")) {

// monto a pagar
$dif_sueldo = $sueldo_nuevo - $sueldo_actual;

// cantidad de meses
$fecha_inicio = new DateTime($row_empleado['fecha_inicio']);
$fecha_final = new DateTime($row_empleado['fecha_fin']);
$diff = $fecha_inicio->diff($fecha_final);
$periodo =  $diff->m;

$mes_paga = date('Y', strtotime($row_empleado['fecha_inicio'])).'-'.date('m', strtotime($row_empleado['fecha_inicio'])).'-01';

$tipo_nomina = 2;
//bucle
$inicio_bucle = 0;
while ($inicio_bucle < $periodo){
	
if($tipo_nomina == 1){
$mes_paga = strtotime($mes_paga.'+1 month') ;
$mes_paga = date('Y-m-d', $mes_paga);
$mes_paga = strtotime($mes_paga.' next friday'); 
$mes_paga = date('Y-m-d', $mes_paga);
} else {
$mes_paga = strtotime($mes_paga.'+1 month') ;
$mes_paga = date('Y-m-d', $mes_paga);
$mes_paga = date('Y', strtotime($mes_paga)).'-'.date('m', strtotime($mes_paga)).'-15';
}

$insertSQL = "INSERT INTO pp_prueba_pagos (IDempleado, IDpprueba, fecha_pago, monto_pago, IDestatus) VALUES ('$IDempleado', '$IDpprueba', '$mes_paga', '$dif_sueldo', '0')";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

$mes_paga = $mes_paga++;
$inicio_bucle++;
}
 
header("Location: pprueba_edit_pagos.php?IDpprueba=$IDpprueba&info=9");
}

// borrar alternativo
if ((isset($_GET['IDpprueba_borrar'])) && ($_GET['IDpprueba_borrar'] != "")) {
  
  $borrado = $_GET['IDpprueba_borrar'];
  $deleteSQL = "DELETE FROM pp_prueba_pagos WHERE IDpprueba_pagos ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
 header("Location: pprueba_edit_pagos.php?IDpprueba=$IDpprueba&info=3");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
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
	
		<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
    
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>


	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->
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
							Se ha agregado correctamente el pago.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el pago.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el pago.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han cargado correctamente los pagos.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Colored tabs -->
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Periodos de Prueba - Pagos</h5>
								</div>

								<div class="panel-body">
								<p><strong>No. de Empleado:</strong> <?php echo $row_empleado['IDempleado']; ?>.</p>
								<p><strong>Nombre:</strong> <?php echo $row_empleado['emp_paterno'] . " " . $row_empleado['emp_materno'] . " " . $row_empleado['emp_nombre'];?>.</p>
								<p><strong>Puesto actual:</strong> <?php echo $row_empleado['denominacion_origen']; ?>.</p>
								<p><strong>Puesto destino:</strong> <?php echo $row_empleado['denominacion_destino']; ?>.</p>
								
                    <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                     <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-success">Agregar Pago<i class="icon-arrow-right14 position-right"></i></button>
                     <?php if ($totalRows_pprueba == 0) { ?>
                     <a class="btn btn-info" href="pprueba_edit_pagos.php?IDpprueba=<?php echo $IDpprueba; ?>&pregarca=1">Precargar Pagos<i class="icon-arrow-right14 position-right"></i></a>
                     <?php } ?>
                    	 <button type="button" onClick="window.location.href='pprueba.php'" class="btn btn-default btn-icon">Regresar</button>
                    </div>
					</div>
					<!-- /colored button -->

								<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-primary"> 
                                    <th>IDpago</th>
                                    <th>Monto</th>
                                    <th>Fecha pago</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                  </tr>
                                  </thead>
                                <tbody>
								  <?php if ($totalRows_pprueba > 0 ) { ?>
								  <?php do { ?>
                                    <tr>
                                      <td><?php echo $row_pprueba['IDpprueba_pagos']; ?></td>
                                      <td><?php echo $row_pprueba['monto_pago']; ?></td>
                                      <td><?php echo date("d-m-Y",strtotime($row_pprueba['fecha_pago']));  ?></td>
                                      <td><?php if ($row_pprueba['IDestatus'] == 1) {echo "Pagado";} else  {echo "Sin Pago";} ?></td>
									  <td>
									   <button type="button" data-target="#modal_theme_danger2<?php echo $row_pprueba['IDpprueba_pagos']; ?>"  data-toggle="modal" class="btn btn-xs btn-primary">Editar</button>
									  <?php if ($row_pprueba['IDestatus'] != 1) { ?>
									   <button type="button" data-target="#modal_theme_danger<?php echo $row_pprueba['IDpprueba_pagos']; ?>"  data-toggle="modal" class="btn btn-xs btn-warning">Borrar</button>
									  <?php } ?>
                                      
                                                                     <!-- danger modal -->
									<div id="modal_theme_danger2<?php echo $row_pprueba['IDpprueba_pagos']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-primary">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Editar Pago</h6>
												</div>

							<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
							<fieldset class="content-group">

								<div class="modal-body">
													
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Semana de pago:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="fecha_pago" id="fecha_pago" class="form-control"  required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_calendario['IDsemana']?>"<?php if (!(strcmp($row_calendario['IDsemana'], $row_pprueba['fecha_pago']))) 
												  {echo "SELECTED";} ?>><?php echo $row_calendario['semana']?></option>
												  <?php
												 } while ($row_calendario = mysql_fetch_assoc($calendario));
												   $rows = mysql_num_rows($calendario);
												   if($rows > 0) {
												   mysql_data_seek($calendario, 0);
												   $row_calendario = mysql_fetch_assoc($calendario);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Monto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="number" name="monto_pago" class="form-control" value="<?php echo $row_pprueba['monto_pago']; ?>" placeholder="Ingresa el monto sin signos ni espacios" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDestatus" id="IDestatus" class="form-control" required="required">
												  <option value="1"<?php if ($row_pprueba['IDestatus'] == 1) {echo "SELECTED";} ?>>Pagado</option>
												  <option value="0"<?php if ($row_pprueba['IDestatus'] == 0) {echo "SELECTED";} ?>>Sin Pago</option>
											</select>
										</div>
									</div>
                                     <!-- /Basic text input -->
													
								</div>

								<div class="modal-footer">
													<button type="submit"  name="KT_Update1" class="btn btn-primary">Editar</button>
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<input type="hidden" name="MM_update" value="form1">
													<input type="hidden" name="IDpprueba" value="<?php echo $IDpprueba; ?>">
													<input type="hidden" name="IDpprueba_pagos" value="<?php echo $row_pprueba['IDpprueba_pagos']; ?>">
								</div>

								</fieldset>
								</form>


											</div>
										</div>
									</div>
									<!-- danger modal -->

									  </td>
									 </tr>
									
                                    
                                    <!-- danger modal -->
									<div id="modal_theme_danger<?php echo $row_pprueba['IDpprueba_pagos']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Borrado</h6>
												</div>

												<div class="modal-body">
													<p>¿Estas seguro que quieres borrar el Pago?.</p>
												</div>

												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-danger" href="pprueba_edit_pagos.php?IDpprueba=<?php echo $IDpprueba; ?>&IDpprueba_borrar=<?php echo $row_pprueba['IDpprueba_pagos']; ?>">Si borrar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->

                                    <?php } while ($row_pprueba = mysql_fetch_assoc($pprueba)); ?>
 							  <?php } else { ?>
								<tr>
                                      <td>No se tienen pagos para el periodos de prueba.</td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                </tr>
                              <?php } ?>
                                    
                                  </tbody>
                                </table>
						</div>


								</div>
							</div>
						</div>
					<!-- /Contenido -->


									<!-- danger modal -->
									<div id="modal_theme_danger" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Agregar Pago</h6>
												</div>

							<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
							<fieldset class="content-group">

							<div class="modal-body">

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Semana de pago:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="fecha_pago" id="fecha_pago" class="form-control"  required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_calendario['IDsemana']?>"><?php echo $row_calendario['semana']?></option>
												  <?php
												 } while ($row_calendario = mysql_fetch_assoc($calendario));
												   $rows = mysql_num_rows($calendario);
												   if($rows > 0) {
												   mysql_data_seek($calendario, 0);
												   $row_calendario = mysql_fetch_assoc($calendario);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Monto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="number" name="monto_pago" id="monto_pago" class="form-control" value="" placeholder="Ingresa el monto sin signos ni espacios" required="required">
										</div>
									</div>
									<!-- /basic text input -->


                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDestatus" id="IDestatus" class="form-control"  required="required">
												  <option value="1"<?php if ($row_pprueba['IDestatus'] == 1) {echo "SELECTED";} ?>>Pagado</option>
												  <option value="0"<?php if ($row_pprueba['IDestatus'] == 0) {echo "SELECTED";} ?>>Sin Pago</option>
											</select>
										</div>
									</div>
                                     <!-- /Basic text input -->
													
								</div>

								<div class="modal-footer">
													<button type="submit"  name="KT_Insert1" class="btn btn-success">Agregar</button>
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<input type="hidden" name="MM_insert" value="form1">
													<input type="hidden" name="IDpprueba" value="<?php echo $row_pprueba['IDpprueba']; ?>">
								</div>
								
								</fieldset>
								</form>

								
											</div>
										</div>
									</div>
									<!-- danger modal -->


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