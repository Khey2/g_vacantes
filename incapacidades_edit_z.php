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

$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //Sabemos el año anterior 

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
$IDmatrizes = $row_usuario['IDmatrizes'];

$fecha = date("Y-m-d"); // la fecha actual
$formatos_permitidos =  array('PDF', 'pdf');
$fechapp = date("YmdHis"); // la fecha actual


// si mando a valiar
if (isset($_POST["IDempleado"]) OR isset($_GET["IDempleado"])) {

	if (isset($_POST["IDempleado"])) { $IDempleado = $_POST["IDempleado"];}
	if (isset($_GET["IDempleado"]))  { $IDempleado = $_GET["IDempleado"];}

	$avalidar = 1;
$alerta = 0;

//activo?
$query_empleado_act = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado  AND IDmatriz = $IDmatriz";
$empleado_act = mysql_query($query_empleado_act, $vacantes) or die(mysql_error());
$row_empleado_act = mysql_fetch_assoc($empleado_act);
$totalRows_empleado_act = mysql_num_rows($empleado_act);

//si esta activo
if($totalRows_empleado_act == 1) {
	
// si ya tiene PP	
$query_empleado_dob = "SELECT * FROM incapacidades_accidentes WHERE IDempleado = $IDempleado AND IDestatus = 1 AND IDincapacidad_accidente = 2";
$empleado_dob = mysql_query($query_empleado_dob, $vacantes) or die(mysql_error());
$row_empleado_dob = mysql_fetch_assoc($empleado_dob);
$totalRows_empleado_dob = mysql_num_rows($empleado_dob);
if($totalRows_empleado_dob > 0) {  $alerta = 2;} 

//recogemos datos del empleado	
$query_empleado = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.curp, prod_activos.imss, prod_activos.fecha_antiguedad, prod_activos.sueldo_total, prod_activos.emp_nombre, prod_activos.descripcion_nomina, prod_activos.IDpuesto, prod_activos.IDarea, prod_activos.IDmatriz, vac_puestos.denominacion, vac_areas.area, vac_matriz.matriz, incapacidades_companias.razon_social, incapacidades_companias.IDllave_compania, incapacidades_companias.IDcompania FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN incapacidades_companias ON prod_activos.descripcion_nomina = incapacidades_companias.IDllave_compania WHERE IDempleado = $IDempleado";
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);
$IDcompania = $row_empleado['IDcompania']; 
$el_estatus = 1;

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

$query_reg_patronales = "SELECT * FROM incapacidades_registros_patronales WHERE IDmatriz = $IDmatriz ORDER BY matriz ASC";
$reg_patronales = mysql_query($query_reg_patronales, $vacantes) or die(mysql_error());
$row_reg_patronales = mysql_fetch_assoc($reg_patronales);
$totalRows_reg_patronales = mysql_num_rows($reg_patronales);


// si no esta redireccionamos
} else { 
header("Location: incapacidades_edit_z.php?info=9"); $avalidar = 2;
}

// si no he mandado a validar
} else { $avalidar = 0; }

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$IDregistro_patronal = 	$_POST["IDregistro_patronal"];
$query_regpp = "SELECT * FROM incapacidades_registros_patronales WHERE IDregistro_patronal = $IDregistro_patronal";
$regpp = mysql_query($query_regpp, $vacantes) or die(mysql_error());
$row_regpp = mysql_fetch_assoc($regpp);
$totalRows_regpp = mysql_num_rows($regpp);

$IDusuario_carpeta = 'incp/'.$IDempleado;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
$IDestatus = 1;	

$fecha1a = $_POST['fecha_inicio']; 
$fecha1b = explode("/",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];
$anio_i = $fecha1b[2];
$mes_i = $fecha1b[1]; 

$semana = date("W", strtotime($fecha1a)); //la semana en la que se reporta como inicio del accidente 
$IDmatriz_e = $row_regpp['IDmatriz'];
$IDincapacidad_accidente = 2;

$insertSQL = sprintf("INSERT INTO incapacidades_accidentes (IDincapacidad_accidente, comentarios, IDempleado, IDmatriz, IDtipo_accidente, IDregistro_patronal, nss, curp, emp_paterno, emp_materno, emp_nombre, denominacion, fecha_inicio, semana, IDestatus, mes, anio) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString($IDincapacidad_accidente, "text"),
						GetSQLValueString($_POST['comentarios'], "text"),
						GetSQLValueString($row_empleado['IDempleado'], "text"),
						GetSQLValueString($IDmatriz_e, "text"),
						GetSQLValueString($_POST['IDtipo_accidente'], "text"),
						GetSQLValueString($_POST['IDregistro_patronal'], "text"),
						GetSQLValueString($row_empleado['imss'], "text"),
						GetSQLValueString($row_empleado['curp'], "text"),
						GetSQLValueString($row_empleado['emp_paterno'], "text"),
						GetSQLValueString($row_empleado['emp_materno'], "text"),
						GetSQLValueString($row_empleado['emp_nombre'], "text"),
						GetSQLValueString($row_empleado['denominacion'], "text"),
						GetSQLValueString($fecha1, "text"),
						GetSQLValueString($semana, "int"),
						GetSQLValueString($IDestatus, "int"),
						GetSQLValueString($mes_i, "int"),
						GetSQLValueString($anio_i, "int"));
 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();

	if ((isset($_GET['agregar'])) && ($_GET['agregar'] == 1)) {
	$IDcertificado = $_GET['IDcertificado'];
	$UpdateSQL = "UPDATE incapacidades_certificados SET IDincapacidad = $last_id WHERE IDcertificado = $IDcertificado";
	mysql_select_db($database_vacantes, $vacantes);
	$result = mysql_query($UpdateSQL, $vacantes) or die(mysql_error());
	}
  
 header("Location: incapacidades_z.php?IDincapacidad=$last_id&info=1");
}


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) ORDER BY matriz ASC";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos_accidente = "SELECT * FROM incapacidades_tipo_incapacidad WHERE IDtipo_incapacidad != 2";
$tipos_accidente = mysql_query($query_tipos_accidente, $vacantes) or die(mysql_error());
$row_tipos_accidente = mysql_fetch_assoc($tipos_accidente);
$totalRows_tipos_accidente = mysql_num_rows($tipos_accidente);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos ORDER BY denominacion asc";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);



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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 8) && $alerta == 2)) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El No. de empleado capturado ya tiene un incidente con incapacidad de trabajo capturado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($alerta) && $alerta == 2)) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El Empleado ya tiene un incidente con incapacidad de trabajo capturado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Incidentes de Trabajo (Incapacidades)</h5>
						</div>

					<div class="panel-body">
							<p>Ingresa el No. de Empleado para validar que el empleado esté activo y sus datos básicos.<br/>
							Una vez validado, captura los datos que se te piden. Todos los campos son obligatorios.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
							<fieldset class="content-group">


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">No. de Empleado:</label>
										<div class="col-lg-9">
                              <?php  if ($avalidar == 1) { ?>
						<?php echo $row_empleado['IDempleado']; ?>
                              <?php } else { ?>
						<input type="text" name="IDempleado" id="IDempleado" class="form-control" value="" required="required" placeholder="Ingresa el número de empleado a buscar">
                              <?php }  ?>
										</div>
									</div>
									<!-- /basic text input -->

									<?php  if ($avalidar == 1) { ?>
								
						<legend class="text-semibold">Datos Empleado</legend>


                                   <!-- Basic text input -->
								   <div class="form-group">
										<label class="control-label col-lg-3">Matriz:</label>
										<div class="col-lg-9">
											<?php echo $row_empleado['matriz']; ?>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
								   <div class="form-group">
										<label class="control-label col-lg-3">NSS:</label>
										<div class="col-lg-9">
											<?php echo $row_empleado['imss']; ?>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
								   <div class="form-group">
										<label class="control-label col-lg-3">CURP:</label>
										<div class="col-lg-9">
											<?php echo $row_empleado['curp']; ?>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
								   <div class="form-group">
										<label class="control-label col-lg-3">Nombre Empleado:</label>
										<div class="col-lg-9">
											<?php echo $row_empleado['emp_paterno']." ".$row_empleado['emp_materno']." ".$row_empleado['emp_nombre']; ?>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha de antiguedad:</label>
										<div class="col-lg-9">
											<?php echo date("d/m/Y",strtotime($row_empleado['fecha_antiguedad']));  ?>
										</div>
									</div>
									<!-- /basic text input -->


                                   <!-- Basic text input -->
								   <div class="form-group">
										<label class="control-label col-lg-3">Puesto:</label>
										<div class="col-lg-9">
											<?php echo $row_empleado['denominacion']; ?>
										</div>
									</div>
									<!-- /basic text input -->
																		
                                   <!-- Basic text input -->
								   <div class="form-group">
										<label class="control-label col-lg-3">Razon Social:</label>
										<div class="col-lg-9">
											<?php echo $row_empleado['razon_social']; ?>
										</div>
									</div>
									<!-- /basic text input -->
																		

									<legend class="text-semibold">Captura</legend>

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Registro Patronal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDregistro_patronal" id="IDregistro_patronal" class="form-control" required="required">
											<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_reg_patronales['IDregistro_patronal']?>">
													  <?php echo $row_reg_patronales['matriz']?> (<?php echo $row_reg_patronales['registro_patronal']?>)</option>
													  <?php
													 } while ($row_reg_patronales = mysql_fetch_assoc($reg_patronales));
													 $rows = mysql_num_rows($reg_patronales);
													 if($rows > 0) {
													 mysql_data_seek($reg_patronales, 0);
													 $row_reg_patronales = mysql_fetch_assoc($reg_patronales);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->



									<!-- Fecha -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha inicio incapacidad:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de incapacidad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtipo_accidente" id="IDtipo_accidente" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_tipos_accidente['IDtipo_incapacidad']?>"><?php echo $row_tipos_accidente['tipo_incapacidad']?> (<?php echo $row_tipos_accidente['tipo_incapacidad_codigo']?>) 	</option>
													  <?php
													 } while ($row_tipos_accidente = mysql_fetch_assoc($tipos_accidente));
													 $rows = mysql_num_rows($tipos_accidente);
													 if($rows > 0) {
													 mysql_data_seek($tipos_accidente, 0);
													 $row_tipos_accidente = mysql_fetch_assoc($tipos_accidente);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Comentarios:</label>
										<div class="col-lg-9">
										<textarea rows="5" cols="5" class="form-control" name="comentarios" id="comentarios" placeholder="Comentarios relevantes del Accidente"></textarea>
										</div>
									</div>
									<!-- /basic select -->



                             <?php } ?>
	
                                    
                              <?php if (isset($_POST['IDempleado']) OR isset($_GET['IDempleado'])) { ?>
						 <input type="submit" name="KT_insert1" class="btn btn-primary" id="KT_insert1" value="Agregar Incidente" />
                    	 <button type="button" onClick="window.location.href='incapacidades_edit_z.php'" class="btn btn-warning btn-icon">Buscar otro</button>
                         <input type="hidden" name="MM_insert" value="form1" />
                         <input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>" />
                              <?php } else { ?>
                         <input type="submit" name="KT_valida1" class="btn btn-primary" id="KT_valida1" value="Validar Empleado" />
                    	 <button type="button" onClick="window.location.href='incapacidades_z.php'" class="btn btn-default btn-icon">Regresar</button>
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