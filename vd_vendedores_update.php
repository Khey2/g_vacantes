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
$IDmatriz = $row_usuario['IDmatriz'];

$IDmatrizes = $row_usuario['IDmatrizes'];
$IDusuario_ad = $row_usuario['IDusuario'];
$IDvd = $_GET['IDvd'];

mysql_select_db($database_vacantes, $vacantes);
$query_reporte = "SELECT com_vd.*, vac_matriz.matriz, vac_meses.mes FROM com_vd LEFT JOIN prod_activos AS Empleados ON com_vd.IDempleado = Empleados.IDempleado LEFT JOIN vac_matriz ON com_vd.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_meses ON com_vd.IDmes = vac_meses.IDmes WHERE com_vd.IDvd = '$IDvd'";
mysql_query("SET NAMES 'utf8'"); 
$reporte = mysql_query($query_reporte, $vacantes) or die(mysql_error());
$row_reporte = mysql_fetch_assoc($reporte);
$totalRows_reporte = mysql_num_rows($reporte); 
$el_mes = $row_reporte['IDmes'];
$IDpuesto = $row_reporte['IDpuesto'];
$IDempleado = $row_reporte['IDempleado'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$y1 = substr( $_POST['fecha_antiguedad'], 8, 2 );  	
$m1 = substr( $_POST['fecha_antiguedad'], 3, 2 );
$d1 = substr( $_POST['fecha_antiguedad'], 0, 2 );
if ($y1 < 70) {$fecha_antiguedad = "20".$y1."-".$m1."-".$d1;} else {$fecha_antiguedad = "19".$y1."-".$m1."-".$d1;}

mysql_select_db($database_vacantes, $vacantes);
$query_adenominacion = "SELECT * FROM vac_puestos WHERE IDpuesto = ".$_POST['IDpuesto'];
$adenominacion = mysql_query($query_adenominacion, $vacantes) or die(mysql_error());
$row_adenominacion = mysql_fetch_assoc($adenominacion);
$totalRows_adenominacion = mysql_num_rows($adenominacion);
$denominacion =$row_adenominacion['denominacion'];

$updateSQL = sprintf("UPDATE com_vd SET emp_nombre=%s, emp_paterno=%s, emp_materno=%s, IDempleado=%s, fecha_antiguedad=%s, IDpuesto=%s, denominacion=%s, IDempleadoS=%s, actualizado=%s WHERE IDvd=%s",
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['IDempleado'], "text"),
                       GetSQLValueString($fecha_antiguedad, "text"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($denominacion, "text"),
                       GetSQLValueString($_POST['IDempleadoS'], "int"),
                       GetSQLValueString('1', "int"),
                       GetSQLValueString($_POST['IDvd'], "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header("Location: vd_vendedores.php?info=6");
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$lamatriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT * FROM vac_puestos WHERE IDpuesto in (212,235)";
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT DISTINCT com_vd.IDempleadoS AS Elboss, jefe.emp_paterno, jefe.emp_materno, jefe.emp_nombre, jefe.denominacion FROM com_vd INNER JOIN com_vd AS jefe ON com_vd.IDempleadoS = jefe.IDempleado WHERE com_vd.IDempleadoS > 0 GROUP BY com_vd.IDempleadoS ORDER BY jefe.emp_paterno ASC";
mysql_query("SET NAMES 'utf8'"); 
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);


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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>

	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
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


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo cargado no es del tipo de archivos permitidos.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El registro ha sido borrado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El registro ha sido agregado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->



					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Actualziar Ocupante Comisiones VD</h5>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.</p>
							
							
							                            
                <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
					<fieldset class="content-group">


                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">No. de Empleado:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="number" name="IDempleado" id="IDempleado" class="form-control" value="<?php echo $row_reporte['IDempleado']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->


                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="emp_paterno" id="emp_paterno" class="form-control" value="<?php echo $row_reporte['emp_paterno']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Materno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="emp_materno" id="emp_materno" class="form-control" value="<?php echo $row_reporte['emp_materno']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Nombres:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="emp_nombre" id="emp_nombre" class="form-control" value="<?php echo $row_reporte['emp_nombre']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha Antiguedad:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control daterange-single" name="fecha_antiguedad" id="fecha_antiguedad" value="<?php if ($row_reporte['fecha_antiguedad'] == "") { echo "";} else { echo date( 'd/m/Y' , strtotime($row_reporte['fecha_antiguedad'])); } ?>" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="form-control" readonly="readonly">
												  <option><?php echo $row_reporte['matriz'];?></option>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto" id="IDpuesto" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php do {  ?>
												  <option value="<?php echo $row_puestos['IDpuesto']?>"<?php if (!(strcmp($row_puestos['IDpuesto'], $row_reporte['IDpuesto']))) {echo "SELECTED";} ?>><?php echo $row_puestos['denominacion']?></option>
												  <?php
												 } while ($row_puestos = mysql_fetch_assoc($puestos));
												 $rows = mysql_num_rows($puestos);
												 if($rows > 0) {
												 mysql_data_seek($puestos, 0);
												 $row_puestos = mysql_fetch_assoc($puestos);
												 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Jefe Inmediato:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDempleadoS" id="IDempleadoS" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php do {  ?>
												  <option value="<?php echo $row_jefes['Elboss']?>"<?php if (!(strcmp($row_jefes['Elboss'], $row_reporte['IDempleadoS']))) {echo "SELECTED";} ?>><?php echo "(".$row_jefes['Elboss'].") ".$row_jefes['emp_paterno']." ".$row_jefes['emp_materno']." ".$row_jefes['emp_nombre']?></option>
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
									<!-- /basic select -->



                         <button type="submit"  name="KT_Update" class="btn btn-primary">Actualizar</button>
						 <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDvd" value="<?php echo $IDvd; ?>">
                    	 <button type="button" onClick="window.location.href='vd_vendedores.php'" class="btn btn-default btn-icon">Regresar</button>

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