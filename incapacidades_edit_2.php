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
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 

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


// borrar alternativo
if ((isset($_GET['cerrar'])) && ($_GET['cerrar'] == 1)) {
  
	$cerrado = $_GET['IDincapacidad'];
	$deleteSQL = "UPDATE incapacidades_accidentes SET IDestatus = 3 WHERE IDincapacidad ='$cerrado'";
  
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: incapacidades.php?info=3");
  }

  

// si mando a valiar
if (isset($_GET["IDincapacidad"])) {
$IDincapacidad = $_GET["IDincapacidad"]; }

// si ya tiene PP	
$query_incapacidades = "SELECT * FROM incapacidades_accidentes WHERE IDincapacidad = $IDincapacidad AND IDincapacidad_accidente = 1";
$incapacidades = mysql_query($query_incapacidades, $vacantes) or die(mysql_error());
$row_incapacidades = mysql_fetch_assoc($incapacidades);
$totalRows_incapacidades = mysql_num_rows($incapacidades);
$IDempleado = $row_incapacidades['IDempleado']; 

//recogemos datos del empleado	
$query_empleado = "SELECT prod_activos.IDempleado,  prod_activos.curp, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.imss, prod_activos.fecha_antiguedad, prod_activos.sueldo_total, prod_activos.emp_nombre, prod_activos.descripcion_nomina, prod_activos.IDpuesto, prod_activos.IDarea, prod_activos.IDmatriz, vac_puestos.denominacion, vac_areas.area, vac_matriz.matriz, incapacidades_companias.razon_social, incapacidades_companias.IDllave_compania, incapacidades_companias.IDcompania FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN incapacidades_companias ON prod_activos.descripcion_nomina = incapacidades_companias.IDllave_compania WHERE IDempleado = $IDempleado";
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);
$IDcompania = $row_empleado['IDcompania'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

$query_reg_patronales = "SELECT * FROM incapacidades_registros_patronales WHERE IDmatriz = $IDmatriz ORDER BY matriz ASC";
$reg_patronales = mysql_query($query_reg_patronales, $vacantes) or die(mysql_error());
$row_reg_patronales = mysql_fetch_assoc($reg_patronales);
$totalRows_reg_patronales = mysql_num_rows($reg_patronales);


if ((isset($_POST["KT_update"])) && ($_POST["KT_update"] == "form1")) {
$IDusuario_carpeta = 'incp/'.$IDempleado;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name3=$_FILES['foto3']['name']; 
$size3=$_FILES['foto3']['size'];
$type3=$_FILES['foto3']['type'];
$temp3=$_FILES['foto3']['tmp_name'];
$extension3 = pathinfo($name3, PATHINFO_EXTENSION);
if(!in_array($extension3, $formatos_permitidos) AND isset($_POST['foto3'])) {
header('Location: incapacidades_edit_2.php?info=9&IDincapacidad='.$IDincapacidad.'');
exit;
} 
if ($extension3 != '') {$name_new3 = $IDempleado."_".$fechapp.".".$extension3;} else {$name_new3 = $row_incapacidades['file_st2'];} 
$targetPath = 'incp/'.$IDempleado."/".$name_new; 
move_uploaded_file($_FILES['foto3']['tmp_name'], $targetPath);

$name=$_FILES['foto1']['name']; 
$size=$_FILES['foto1']['size'];
$type=$_FILES['foto1']['type'];
$temp=$_FILES['foto1']['tmp_name'];
$extension1 = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension1, $formatos_permitidos) AND isset($_POST['foto1'])) {
header('Location: incapacidades_edit_2.php?info=9&IDincapacidad='.$IDincapacidad.'');
exit;
} 
if ($extension1 != '') {$name_new1 = $IDempleado."_".$fechapp.".".$extension1;} else {$name_new1 = $row_incapacidades['file_st7_prev'];} 
$targetPath = 'incp/'.$IDempleado."/".$name_new; 
move_uploaded_file($_FILES['foto1']['tmp_name'], $targetPath);

$name=$_FILES['foto2']['name']; 
$size=$_FILES['foto2']['size'];
$type=$_FILES['foto2']['type'];
$temp=$_FILES['foto2']['tmp_name'];
$extension2 = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension2, $formatos_permitidos) AND isset($_POST['foto2'])) {
header('Location: incapacidades_edit_2.php?info=9&IDincapacidad='.$IDincapacidad.'');
exit;
} 
if ($extension2 != '') {$name_new2 = $IDempleado."_".$fechapp.".".$extension2;} else {$name_new2 = $row_incapacidades['file_st7'];} 
$targetPath = 'incp/'.$IDempleado."/".$name_new; 
move_uploaded_file($_FILES['foto2']['tmp_name'], $targetPath);
$IDestatus = 1;	


$fecha1a = $_POST['fecha_inicio']; 
$fecha1b = explode("/",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];
$anio_i = $fecha1b[2]; 
$mes_i = $fecha1b[1]; 

$fecha2a = $_POST['fecha_fin']; 
$fecha2b = explode("/",$fecha2a);
$fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];

$semana = date("W", strtotime($fecha1a)); //la semana en la que se reporta como inicio del accidente 
//$anio_i = date("Y", strtotime($fecha1a)); 


$insertSQL = sprintf("UPDATE incapacidades_accidentes  SET file_st2=%s, file_st7_prev=%s, file_st7=%s, comentarios=%s, IDperm_parcial=%s, IDregistro_patronal=%s, fecha_inicio=%s, fecha_fin=%s, IDtipo_accidente=%s, defuncion=%s, invalidez_porcentaje=%s, anio=%s, mes=%s, semana=%s, IDestatus=%s WHERE IDincapacidad = $IDincapacidad ",
						GetSQLValueString($name_new3, "text"),
						GetSQLValueString($name_new1, "text"),
						GetSQLValueString($name_new2, "text"),
						GetSQLValueString($_POST['comentarios'], "text"),
						GetSQLValueString($_POST['IDperm_parcial'], "text"),
						GetSQLValueString($_POST['IDregistro_patronal'], "text"),
						GetSQLValueString($fecha1, "text"),
						GetSQLValueString($fecha2, "text"),
						GetSQLValueString($_POST['IDtipo_accidente'], "text"),
						GetSQLValueString($_POST['defuncion'], "text"),
						GetSQLValueString($_POST['invalidez_porcentaje'], "text"),
						GetSQLValueString($anio_i, "text"),
						GetSQLValueString($mes_i, "text"),
						GetSQLValueString($semana, "text"),
						GetSQLValueString($_POST['IDestatus'], "text"),
						GetSQLValueString($IDincapacidad, "int"));
						
 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();

 header("Location: incapacidades.php?IDincapacidad=$last_id&info=2");
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos_accidente = "SELECT * FROM incapacidades_tipos_accidente";
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
							El No. de empleado capturado ya tiene un accidente de trabajo capturado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Accidentes de Trabajo</h5>
						</div>

					<div class="panel-body">
							<p>Actualiza el estatus del accidente.<br/>
							Para poder cerrar el Accidente, debes agregar el formato ST-2.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
							<fieldset class="content-group">


							<legend class="text-semibold">Datos Empleado</legend>

                                   <!-- Basic text input -->
								   <div class="form-group">
										<label class="control-label col-lg-3">No. Empleado:</label>
										<div class="col-lg-9">
											<?php echo $row_empleado['IDempleado']; ?>
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
													  <option value="<?php echo $row_reg_patronales['IDregistro_patronal']?>" <?php if (!(strcmp($row_reg_patronales['IDregistro_patronal'], $row_incapacidades['IDregistro_patronal']))) {echo "SELECTED";} ?>><?php echo $row_reg_patronales['matriz']?> (<?php echo $row_reg_patronales['registro_patronal']?>)</option>
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
										<label class="control-label col-lg-3">Fecha accidente:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" required="required" value="<?php if ($row_incapacidades['fecha_inicio'] != '') { echo date('d/m/Y', strtotime($row_incapacidades['fecha_inicio'])); } else { echo "";} ?>">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de accidente:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtipo_accidente" id="IDtipo_accidente" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_tipos_accidente['IDtipo_accidente']?>" <?php if (!(strcmp($row_tipos_accidente['IDtipo_accidente'], $row_incapacidades['IDtipo_accidente']))) {echo "SELECTED";} ?>><?php echo $row_tipos_accidente['tipo_accidente']?></option>
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


									<!-- Fecha -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha de alta o defunción:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" required="required" value="<?php if ($row_incapacidades['fecha_fin'] != '') { echo date('d/m/Y', strtotime($row_incapacidades['fecha_fin'])); } else { echo "";} ?>">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Fue defunción?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="defuncion" id="defuncion" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
												<option value="NO" <?php if ($row_incapacidades['defuncion'] == 0) {echo "SELECTED";} ?>>NO</option>
												<option value="SI" <?php if ($row_incapacidades['defuncion'] == 1) {echo "SELECTED";} ?>>SI</option>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->


                                   <!-- Basic text input -->
								   <div class="form-group">
										<label class="control-label col-lg-3">% de invalidez:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="number" name="invalidez_porcentaje" id="invalidez_porcentaje" class="form-control" required="required" value="<?php echo $row_incapacidades['invalidez_porcentaje'] ?>">
										</div>
									</div>
									<!-- /basic text input -->

 									<!-- Basic select -->
									 <div class="form-group">
										<label class="control-label col-lg-3">Tipo de Incapacidad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDperm_parcial" id="IDperm_parcial" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
												<option value="1" <?php if ($row_incapacidades['IDperm_parcial'] == 1) {echo "SELECTED";} ?>>Permanente</option>
												<option value="2" <?php if ($row_incapacidades['IDperm_parcial'] == 2) {echo "SELECTED";} ?>>Parcial</option>
												<option value="2" <?php if ($row_incapacidades['IDperm_parcial'] == 3) {echo "SELECTED";} ?>>Total</option>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3"><span class="text text-danger text-semibold">Estatus:</span></label>
										<div class="col-lg-9">
											<select name="IDestatus" id="IDestatus" class="form-control">
                                            	<option value="">Seleccione una opción</option> 
												<option value="1" <?php if ($row_incapacidades['IDestatus'] == 1) {echo "SELECTED";} ?>>Abierto</option>
												<option value="2" <?php if ($row_incapacidades['IDestatus'] == 2) {echo "SELECTED";} ?>>Cerrado</option>
                                          </select>
										  <p class="text text-muted">Al seleccionar cerrado, el accidente de trabajo pasará a la sección de Cerrados.</p>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Comentarios:</label>
										<div class="col-lg-9">
										<textarea rows="5" cols="5" class="form-control" name="comentarios" id="comentarios" placeholder="Comentarios relevantes del Accidente"><?php echo $row_incapacidades['comentarios'] ?></textarea>
										</div>
									</div>
									<!-- /basic select -->

								
									<legend class="text-semibold">Archivos</legend>

									<!-- /basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Formato ST-7 (previo):<span class="text-danger">*</span></label>
										<div class="col-lg-7">
										<input type="file" class="file-styled" name="foto1" id="foto1">
										<p class="text text-muted">Archivos permitidos: <code>pdf</code>.</p>
										</div>
										<div class="col-lg-2">
										<?php if($row_incapacidades['file_st7_prev'] != '') { ?>
										<p><a href="incp/<?php echo $row_incapacidades['IDempleado']; ?>/<?php echo $row_incapacidades['file_st7_prev']; ?>" target="_blank" class="btn btn-warning">Descargar</a><p>
										<?php } else { echo "No se tiene archivo cargado."; } ?>
										</div>
									</div>
									<!-- /basic text input -->


									<!-- /basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Formato ST-7 (final):<span class="text-danger">*</span></label>
										<div class="col-lg-7">
										<input type="file" class="file-styled" name="foto2" id="foto2">
										<p class="text text-muted">Archivos permitidos: <code>pdf</code>.</p>
										</div>
										<div class="col-lg-2">
										<?php if($row_incapacidades['file_st7'] != '') { ?>
										<p><a href="incp/<?php echo $row_incapacidades['IDempleado']; ?>/<?php echo $row_incapacidades['file_st7']; ?>" target="_blank" class="btn btn-warning">Descargar</a><p>
										<?php } else { echo "No se tiene archivo cargado."; } ?>
										</div>
									</div>
									<!-- /basic text input -->


									<!-- /basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Formato ST-2:<span class="text-danger">*</span></label>
										<div class="col-lg-7">
											<input type="file" class="file-styled" name="foto3" id="foto3">
										<p class="text text-muted">Archivos permitidos: <code>pdf</code>.</p>
										</div>
										<div class="col-lg-2">
										<?php if($row_incapacidades['file_st2'] != '') { ?>
										<p><a href="incp/<?php echo $row_incapacidades['IDempleado']; ?>/<?php echo $row_incapacidades['file_st2']; ?>" class="btn btn-warning">Descargar</a><p>
										<?php } else { echo "No se tiene archivo cargado."; } ?>
										</div>
									</div>
									<!-- /basic text input -->


                         <input type="submit" name="KT_update" class="btn btn-primary" id="KT_update" value="Actualizar" />
						 <button type="button" data-target="#modal_theme_danger<?php echo $IDincapacidad; ?>"  data-toggle="modal" class="btn bg-danger">Borrar</button></td>
						 <button type="button" onClick="window.location.href='incapacidades.php'" class="btn btn-default btn-icon">Regresar</button>
						<input type="hidden" name="KT_update" value="form1" />
						 
						 
						</fieldset>
                        </form>
					
					</div>

</div>



                     <!-- danger modal -->
					 <div id="modal_theme_danger<?php echo $IDincapacidad; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el accidente de trabajo?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="incapacidades_edit_2.php?IDincapacidad=<?php echo $IDincapacidad; ?>&cerrar=1">Si borrar</a>
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