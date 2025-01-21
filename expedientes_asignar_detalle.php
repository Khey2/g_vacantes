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
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$la_matriz = $row_usuario['IDmatriz'];

$IDexpc = $_GET['IDexpc'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario1 = "SELECT prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.IDmatriz, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, vac_puestos.denominacion, vac_areas.area, vac_matriz.matriz, exp_consultas.IDusuario, exp_consultas.IDempleado, exp_consultas.fecha_fin, exp_consultas.fecha_inicio, exp_consultas.IDexpc FROM exp_consultas INNER JOIN prod_activos ON exp_consultas.IDempleado = prod_activos.IDempleado INNER JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto INNER JOIN vac_areas ON prod_activos.IDarea = vac_areas.IDarea INNER JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz  WHERE exp_consultas.IDexpc  = '$IDexpc'";
mysql_query("SET NAMES 'utf8'"); 
$usuario1 = mysql_query($query_usuario1, $vacantes) or die(mysql_error());
$row_usuario1 = mysql_fetch_assoc($usuario1);
$totalRows_usuario1 = mysql_num_rows($usuario1);

$IDusuario = $row_usuario1['IDusuario'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario2 = "SELECT vac_matriz.matriz, exp_consultas.IDusuario, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno, vac_usuarios.IDusuario_puesto, vac_usuarios.IDmatriz, vac_matriz.matriz, vac_puestos.denominacion FROM exp_consultas INNER JOIN vac_usuarios ON exp_consultas.IDusuario = vac_usuarios.IDusuario INNER JOIN vac_matriz ON vac_usuarios.IDmatriz = vac_matriz.IDmatriz INNER JOIN vac_puestos ON vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto WHERE exp_consultas.IDusuario = '$IDusuario'";
mysql_query("SET NAMES 'utf8'"); 
$usuario2 = mysql_query($query_usuario2, $vacantes) or die(mysql_error());
$row_usuario2 = mysql_fetch_assoc($usuario2);
$totalRows_usuario2 = mysql_num_rows($usuario2);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$y1 = substr( $_POST['fecha_inicio'], 8, 2 );
$m1 = substr( $_POST['fecha_inicio'], 3, 2 );
$d1 = substr( $_POST['fecha_inicio'], 0, 2 );
$fecha_inicio = "20".$y1."-".$m1."-".$d1;

$y2 = substr( $_POST['fecha_fin'], 8, 2 );
$m2 = substr( $_POST['fecha_fin'], 3, 2 );
$d2 = substr( $_POST['fecha_fin'], 0, 2 );
$fecha_fin = "20".$y2."-".$m2."-".$d2;
	
	
	$updateSQL = sprintf("UPDATE exp_consultas SET fecha_inicio=%s, fecha_fin=%s WHERE IDexpc=%s",
                       GetSQLValueString($fecha_inicio, "text"),
                       GetSQLValueString($fecha_fin, "text"),
                       GetSQLValueString($_POST['IDexpc'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "expedientes_asignar.php?info=2";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));  
} 

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDexpc'];
  $deleteSQL = "UPDATE exp_consultas SET IDestatus = 0 WHERE IDexpc ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: expedientes_asignar.php?info=3");
}
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
    
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>


	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<!-- /theme JS files -->

</head>

<body class="has-detached-right">	<?php require_once('assets/mainnav.php'); ?>
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



					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Actualizar Asignación de Expediente</h5>
						</div>

					<div class="panel-body">
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
								<fieldset class="content-group">

						<legend class="text-semibold">Expediente</legend>


                                   <!-- Basic text input -->
									<div class="form-group">
							<label class="control-label col-lg-3">No. de Empleado:</label>
										<div class="col-lg-9">
						<?php echo $row_usuario1['IDempleado']; ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<?php echo $row_usuario1['emp_paterno']." ".$row_usuario1['emp_materno']." ".$row_usuario1['emp_nombre'] ; ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<?php echo $row_usuario1['denominacion']; ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Área:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<?php echo $row_usuario1['area']; ?>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<?php echo $row_usuario1['matriz']; ?>
										</div>
									</div>
									<!-- /basic text input -->

						<legend class="text-semibold">Asignado</legend>


                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<?php echo $row_usuario2['usuario_parterno']." ".$row_usuario2['usuario_materno']." ".$row_usuario2['usuario_nombre'] ; ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<?php echo $row_usuario2['denominacion']; ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<?php echo $row_usuario2['matriz']; ?>
										</div>
									</div>
									<!-- /basic text input -->

						<legend class="text-semibold">Fechas</legend>


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha Inicio Consulta:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" value="<?php if ($row_usuario1['fecha_inicio'] == "") { echo "";} else { echo date("d/m/Y", strtotime($row_usuario1['fecha_inicio'])); }?>" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha Fin Consulta:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" value="<?php if ($row_usuario1['fecha_fin'] == "") { echo "";} else { echo date("d/m/Y", strtotime($row_usuario1['fecha_fin'])); }?>" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->
                                    
                                    
                         <input class="btn bg-primary btn-icon" type="submit" value="Actualizar" />
                         <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-warning btn-icon">Borrar</button>
                         <button type="button" onClick="window.location.href='expedientes_consultas.php'" class="btn btn-default btn-icon">Regresar</button>
                         <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDexpc" value="<?php echo $row_usuario1['IDexpc']; ?>">
								
								<fieldset class="content-group">
								</form>

                    </div>

</div>

                  <!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la asignación de Expediente?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="expedientes_asignar_detalle.php?borrar=1&IDexpc=<?php echo $row_usuario1['IDexpc']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->
					
					
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