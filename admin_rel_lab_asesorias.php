<?php require_once('Connections/vacantes.php'); ?>
<?php
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

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

$la_matriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

// si mando a valiar
if (isset($_POST["IDempleado"])) {
$avalidar = 1;
$IDempleado = $_POST["IDempleado"];

//activo?
$query_empleado_act = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado";
$empleado_act = mysql_query($query_empleado_act, $vacantes) or die(mysql_error());
$row_empleado_act = mysql_fetch_assoc($empleado_act);
$totalRows_empleado_act = mysql_num_rows($empleado_act);

//si esta activo
if($totalRows_empleado_act == 1) {
	
// si ya tiene periodo de prueba	
$query_empleado_dob = "SELECT * FROM rel_lab_asesorias WHERE IDempleado = $IDempleado AND IDestatus = 1";
$empleado_dob = mysql_query($query_empleado_dob, $vacantes) or die(mysql_error());
$row_empleado_dob = mysql_fetch_assoc($empleado_dob);
$totalRows_empleado_dob = mysql_num_rows($empleado_dob);

//recogemos datos del empleado	
$query_empleado = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.fecha_antiguedad, prod_activos.denominacion, prod_activos.rfc13, prod_activos.emp_nombre, vac_puestos.denominacion, vac_areas.area, vac_matriz.matriz, prod_activos.IDpuesto, prod_activos.IDarea, prod_activos.IDmatriz FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz WHERE IDempleado = $IDempleado";
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);
$el_estatus = 1;
// si no esta redireccionamos
} else { header("Location: admin_rel_lab_asesorias.php?info=9"); $avalidar = 2;}

// si no he mandado a validar
} else { $avalidar = 0; }

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$insertSQL = sprintf("INSERT INTO rel_lab_asesorias (IDempleado, IDpuesto, IDarea, IDmatriz, emp_paterno, emp_materno, emp_nombre, denominacion, rfc, anio, IDempleado_captura, fecha_antiguedad, IDestatus, IDmotivo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($row_empleado['IDpuesto'], "int"),
                       GetSQLValueString($row_empleado['IDarea'], "int"),
                       GetSQLValueString($row_empleado['IDmatriz'], "int"),
                       GetSQLValueString($row_empleado['emp_paterno'], "text"),
                       GetSQLValueString($row_empleado['emp_materno'], "text"),
                       GetSQLValueString($row_empleado['emp_nombre'], "text"),
                       GetSQLValueString($row_empleado['denominacion'], "text"),
                       GetSQLValueString($row_empleado['rfc13'], "text"),
                       GetSQLValueString($anio, "int"),
                       GetSQLValueString($row_usuario['IDusuario'], "int"),
                       GetSQLValueString($row_empleado['fecha_antiguedad'], "text"),
                       GetSQLValueString($el_estatus, "text"),
                       GetSQLValueString($_POST['IDmotivo'], "int"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();
 header("Location: admin_rel_lab.php?info=1");
}


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

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos ORDER BY denominacion asc";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_motivos = "SELECT * FROM rel_lab_tipos";
mysql_query("SET NAMES 'utf8'");
$motivos = mysql_query($query_motivos, $vacantes) or die(mysql_error());
$row_motivos = mysql_fetch_assoc($motivos);
$totalRows_motivos = mysql_num_rows($motivos);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="robots" content="noindex" />
	<title><?php echo $row_variables['nombre_sistema']; ?></title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
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
	
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<!-- /theme JS files -->

</head>

<body class="has-detached-right">
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9) && $avalidar == 0)) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El No. de empleado capturado no existe o pertenece a otra Sucursal.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if(isset($_POST['IDempleado']) && $totalRows_empleado_dob > 0) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El No. de empleado indicado ya tiene un periodo de prueba capturado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Agregar Disciplina Progresiva</h5>
						</div>

					<div class="panel-body">
							<p>Ingresa el No de Empelado a proverse para validar que el empleado esté activo y sus datos básicos.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
							<fieldset class="content-group">


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">No. de Empleado:</label>
										<div class="col-lg-9">
                              <?php  if ($avalidar == 1) { ?>
						<input type="text" name="IDempleado" id="IDempleado" class="form-control" value="<?php echo $row_empleado['IDempleado']; ?>" readonly="readonly" placeholder="Ingresa el número de empleado a buscar">
                              <?php } else { ?>
						<input type="text" name="IDempleado" id="IDempleado" class="form-control" value="" required="required" placeholder="Ingresa el número de empleado a buscar">
                              <?php }  ?>
										</div>
									</div>
									<!-- /basic text input -->

								<?php  if ($avalidar == 1) { ?>
								
						<legend class="text-semibold">Resultado</legend>


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre Empleado:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo $row_empleado['emp_paterno']." ".$row_empleado['emp_materno']." ".$row_empleado['emp_nombre']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo $row_empleado['matriz']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha de antiguedad:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo date("d-m-Y",strtotime($row_empleado['fecha_antiguedad']));  ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo $row_empleado['area']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo $row_empleado['denominacion']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->
																		
 									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Motivo de la Asesoría:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmotivo" id="IDmotivo" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_motivos['IDmotivo']?>"><?php echo $row_motivos['motivo']?></option>
												  <?php
												 } while ($row_motivos = mysql_fetch_assoc($motivos));
												   $rows = mysql_num_rows($motivos);
												   if($rows > 0) {
												   mysql_data_seek($motivos, 0);
												   $row_motivos = mysql_fetch_assoc($motivos);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

																		
                            <?php } ?>
	
                                    

                                    
                              <?php if (isset($_POST['IDempleado'])) { ?>
                         <input type="submit" name="KT_insert1" class="btn btn-primary" id="KT_insert1" value="Agregar Disciplina Progresiva" />
                    	 <button type="button" onClick="window.location.href='admin_rel_lab_asesorias.php'" class="btn btn-warning btn-icon">Buscar otro</button>
                         <input type="hidden" name="MM_insert" value="form1" />
                              <?php } else { ?>
                         <input type="submit" name="KT_valida1" class="btn btn-primary" id="KT_valida1" value="Validar Empleado" />
                    	 <button type="button" onClick="window.location.href='admin_rel_lab.php'" class="btn btn-default btn-icon">Regresar</button>
                                <?php }  ?>
						 
						 
						</fieldset>
                        </form>
					
					</div>

</div>

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