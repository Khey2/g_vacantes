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
$el_usuario = $row_usuario['IDusuario'];
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

$fecha = date("Y-m-d"); // la fecha actual
$formatos_permitidos =  array('PDF', 'pdf');
$fechapp = date("YmdHis"); // la fecha actual

$IDincapacidad = $_GET["IDincapacidad"];

// si ya tiene PP	
$query_empleado_dob = "SELECT * FROM incapacidades_accidentes WHERE IDincapacidad = $IDincapacidad";
$empleado_dob = mysql_query($query_empleado_dob, $vacantes) or die(mysql_error());
$row_empleado_dob = mysql_fetch_assoc($empleado_dob);
$totalRows_empleado_dob = mysql_num_rows($empleado_dob);
$IDempleado = $row_empleado_dob['IDempleado'];
$nss = $row_empleado_dob['nss'];
$IDregistro_patronal = $row_empleado_dob['IDregistro_patronal']; 
$IDtipo_accidente = $row_empleado_dob['IDtipo_accidente']; 


//recogemos datos del empleado	
$query_empleado = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.imss, prod_activos.fecha_antiguedad, prod_activos.sueldo_total, prod_activos.emp_nombre, prod_activos.descripcion_nomina, prod_activos.IDpuesto, prod_activos.IDarea, prod_activos.IDmatriz, vac_puestos.denominacion, vac_areas.area, vac_matriz.matriz, incapacidades_companias.razon_social, incapacidades_companias.IDllave_compania, incapacidades_companias.IDcompania FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN incapacidades_companias ON prod_activos.descripcion_nomina = incapacidades_companias.IDllave_compania WHERE IDempleado = $IDempleado";
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);
$IDcompania = $row_empleado['IDcompania'];

$candado = 0;
if (isset($_GET["IDcertificado"])){

$IDcertificado = $_GET["IDcertificado"];
$candado = 1;
mysql_select_db($database_vacantes, $vacantes);
$query_elcertificado = "SELECT * FROM incapacidades_certificados WHERE IDcertificado = $IDcertificado";
$elcertificado = mysql_query($query_elcertificado, $vacantes) or die(mysql_error());
$row_elcertificado = mysql_fetch_assoc($elcertificado);
$totalRows_elcertificado = mysql_num_rows($elcertificado);
} 


mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_regpat = "SELECT * FROM incapacidades_registros_patronales WHERE IDregistro_patronal = $IDregistro_patronal";
$regpat = mysql_query($query_regpat, $vacantes) or die(mysql_error());
$row_regpat = mysql_fetch_assoc($regpat);
$totalRows_regpat = mysql_num_rows($regpat);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$IDusuario_carpeta = 'incp/'.$IDempleado;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name']; 
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
header('Location: incapacidades_z.php?info=10&IDincapacidad='.$IDincapacidad.'');
exit;
} 
if ($extension != '') {$name_new = "CERT_".$IDempleado."_".$fechapp.".".$extension;} else {$name_new = $row_elcertificado['file_certificado'];} 
$targetPath = 'incp/'.$IDempleado."/".$name_new; 
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);
$IDestatus = 1;	

$fecha1a = $_POST['fecha_inicio']; 
$fecha1b = explode("/",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$fecha2a = $_POST['fecha_fin']; 
$fecha2b = explode("/",$fecha2a);
$fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];

$date_a = new DateTime($fecha1);
$date_b = new DateTime($fecha2);
$diferencias = $date_a->diff($date_b);
$diff_dias =  $diferencias->days;


$insertSQL = sprintf("UPDATE incapacidades_certificados SET nss=%s, IDempleado=%s, IDusuario=%s, fecha_carga=%s, file_certificado=%s, IDtipo_certificado=%s, IDtipo_incapacidad=%s, folio_certificado=%s, fecha_inicio=%s, fecha_fin=%s, dias=%s WHERE IDcertificado = $IDcertificado",
						GetSQLValueString($nss, "text"),
						GetSQLValueString($IDempleado, "text"),
						GetSQLValueString($el_usuario, "text"),
						GetSQLValueString($fecha, "text"),
						GetSQLValueString($name_new, "text"),
						GetSQLValueString($_POST['IDtipo_certificado'], "text"),
						GetSQLValueString($_POST['IDtipo_incapacidad'], "text"),
						GetSQLValueString($_POST['folio_certificado'], "text"),
						GetSQLValueString($fecha1, "text"),
						GetSQLValueString($fecha2, "text"),
						GetSQLValueString($diff_dias, "text"),
						GetSQLValueString($IDcertificado, "text"));

	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

	$last_id =  mysql_insert_id();

	header("Location: incapacidades_z.php?info=12");
}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$IDusuario_carpeta = 'incp/'.$IDempleado;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name']; 
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
header('Location: incapacidades_z.php?info=10&IDincapacidad='.$IDincapacidad.'');
exit;
} 
if ($_FILES['foto'] != '') {$name_new = "CERT_".$IDempleado."_".$fechapp.".".$extension;} else {$name_new = '';} 
$targetPath = 'incp/'.$IDempleado."/".$name_new; 
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);
$IDestatus = 1;	

$fecha1a = $_POST['fecha_inicio']; 
$fecha1b = explode("/",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$fecha2a = $_POST['fecha_fin']; 
$fecha2b = explode("/",$fecha2a);
$fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];

$date_a = new DateTime($fecha1);
$date_b = new DateTime($fecha2);
$diferencias = $date_a->diff($date_b);
$diff_dias =  $diferencias->days;

$insertSQL = sprintf("INSERT INTO incapacidades_certificados (file_certificado, nss, IDempleado, IDusuario, fecha_carga, IDincapacidad, IDtipo_certificado, IDtipo_incapacidad, folio_certificado, fecha_inicio, fecha_fin, IDestatus, dias) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
						GetSQLValueString($name_new, "text"),
						GetSQLValueString($nss, "text"),
						GetSQLValueString($IDempleado, "text"),
						GetSQLValueString($el_usuario, "text"),
						GetSQLValueString($fecha, "text"),
						GetSQLValueString($IDincapacidad, "text"),
						GetSQLValueString($_POST['IDtipo_certificado'], "text"),
						GetSQLValueString($_POST['IDtipo_incapacidad'], "text"),
						GetSQLValueString($_POST['folio_certificado'], "text"),
						GetSQLValueString($fecha1, "text"),
						GetSQLValueString($fecha2, "text"),
						GetSQLValueString($IDestatus, "text"),
						GetSQLValueString($diff_dias, "text"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();

 header("Location: incapacidades_z.php?info=11");
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
	$borrado = $_GET['IDcertificado'];
	$deleteSQL = "DELETE FROM incapacidades_certificados WHERE IDcertificado ='$borrado'";
  
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: incapacidades_z.php?info=13");
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
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos_accidente = "SELECT * FROM incapacidades_tipos_accidente WHERE IDtipo_accidente = $IDtipo_accidente";
$tipos_accidente = mysql_query($query_tipos_accidente, $vacantes) or die(mysql_error());
$row_tipos_accidente = mysql_fetch_assoc($tipos_accidente);
$totalRows_tipos_accidente = mysql_num_rows($tipos_accidente);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos_incapacidad = "SELECT * FROM incapacidades_tipo_incapacidad WHERE IDtipo_incapacidad != 2";
$tipos_incapacidad = mysql_query($query_tipos_incapacidad, $vacantes) or die(mysql_error());
$row_tipos_incapacidad = mysql_fetch_assoc($tipos_incapacidad);
$totalRows_tipos_incapacidad = mysql_num_rows($tipos_incapacidad);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos_certificado = "SELECT * FROM incapacidades_tipo_certificado";
$tipos_certificado = mysql_query($query_tipos_certificado, $vacantes) or die(mysql_error());
$row_tipos_certificado = mysql_fetch_assoc($tipos_certificado);
$totalRows_tipos_certificado = mysql_num_rows($tipos_certificado);

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
							<p>Agrega la información solicitada. Algunos campos son obligatorios.</p>
                            <p>Asegurate de agregar el Certificado recibido por el empleado en formato PDF.</p>
                            <p>Si ya habías cargado el Certificado, puedes descargarlo en el botón "Descargar archivo".</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
							<fieldset class="content-group">


                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">No. de Empleado:</label>
										<div class="col-lg-9">
											<?php echo $row_empleado['IDempleado']; ?>
										</div>
									</div>
									<!-- /basic text input -->
							
						<legend class="text-semibold">Datos de Accidente</legend>


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
										<label class="control-label col-lg-3">Nombre Empleado:</label>
										<div class="col-lg-9">
											<?php echo $row_empleado['emp_paterno']." ".$row_empleado['emp_materno']." ".$row_empleado['emp_nombre']; ?>
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
																		
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<?php echo $row_empleado['matriz']; ?>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Registro Patronal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<?php echo $row_regpat['registro_patronal']; ?>
										</div>
									</div>
									<!-- /basic select -->



									<!-- Fecha -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha accidente:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
									<?php echo date( 'd/m/Y' , strtotime($row_empleado_dob['fecha_inicio'])); ?>
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de accidente:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<?php echo $row_tipos_accidente['tipo_accidente']; ?>
										</div>
									</div>
									<!-- /basic select -->

									<legend class="text-semibold">Registro de Certificado de Incapacidad</legend>


								<!-- Basic text input -->
								<div class="form-group">
									<label class="control-label col-lg-3">Folio de la Incapacidad:<span class="text-danger">*</span></label>
									<div class="col-lg-9">
									<?php if($candado == 1) { ?>
										<input type="text"  onKeyUp="this.value=this.value.toUpperCase();" name="folio_certificado" id="folio_certificado" class="form-control" 
										value="<?php echo $row_elcertificado['folio_certificado']; ?>" required="required" placeholder="Ingresa el folio">
									<?php } else { ?>
										<input type="text"  onKeyUp="this.value=this.value.toUpperCase();" name="folio_certificado" id="folio_certificado" class="form-control" required="required" placeholder="Ingresa el folio">
									<?php } ?>
									</div>
								</div>
								<!-- /basic text input -->

								<!-- Basic text input -->
								<div class="form-group">
									<label class="control-label col-lg-3">Inicial o Subsecuente:<span class="text-danger">*</span></label>
									<div class="col-lg-9">
									<?php if($candado == 1) { ?>
										<select name="IDtipo_certificado" id="IDtipo_certificado" class="form-control" required="required">
											<option value="">Seleccione una opción</option> 
													<?php do {  ?>
													<option value="<?php echo $row_tipos_certificado['IDtipo_certificado']?>" <?php if (!(strcmp($row_tipos_certificado['IDtipo_certificado'], $row_elcertificado['IDtipo_certificado']))) {echo "SELECTED";} ?>><?php echo $row_tipos_certificado['tipo_certificado']?></option>
													<?php
													} while ($row_tipos_certificado = mysql_fetch_assoc($tipos_certificado));
													$rows = mysql_num_rows($tipos_certificado);
													if($rows > 0) {
													mysql_data_seek($tipos_certificado, 0);
													$row_tipos_certificado = mysql_fetch_assoc($tipos_certificado);
													} ?>
										</select>
										<?php } else {  ?>
										<select name="IDtipo_certificado" id="IDtipo_certificado" class="form-control" required="required">
											<option value="">Seleccione una opción</option> 
													<?php do {  ?>
													<option value="<?php echo $row_tipos_certificado['IDtipo_certificado']?>"><?php echo $row_tipos_certificado['tipo_certificado']?></option>
													<?php
													} while ($row_tipos_certificado = mysql_fetch_assoc($tipos_certificado));
													$rows = mysql_num_rows($tipos_certificado);
													if($rows > 0) {
													mysql_data_seek($tipos_certificado, 0);
													$row_tipos_certificado = mysql_fetch_assoc($tipos_certificado);
													} ?>
										</select>
										<?php } ?>
									</div>
								</div>
								<!-- /basic text input -->

									<!-- Fecha -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha inicial:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
									<?php if($candado == 1) { ?>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" required="required" value ="<?php echo date( 'd/m/Y' , strtotime($row_elcertificado['fecha_inicio'])) ?>">
									<?php } else {  ?>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" required="required">
									<?php } ?>
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Fecha -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha final:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
									<?php if($candado == 1) { ?>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" required="required" value ="<?php echo date( 'd/m/Y' , strtotime($row_elcertificado['fecha_fin'])) ?>">
									<?php } else {  ?>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" required="required">
									<?php } ?>
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->


								<!-- Basic text input -->
								<div class="form-group">
									<label class="control-label col-lg-3">Días:<span class="text-danger">*</span></label>
									<div class="col-lg-9">
									<?php if($candado == 1) { ?>
                                    	<input type="number" name="dias" id="dias" class="form-control" readonly="readonly" placeholder="Dias de incapacidad" 
										value ="<?php echo $row_elcertificado['dias'] ?>">
									<?php } else {  ?>
										<input type="number" name="dias" id="dias" class="form-control" value="" readonly="readonly" placeholder="Dias de incapacidad">
									<?php } ?>
									</div>
								</div>
								<!-- /basic text input -->

								<!-- Basic text input -->
								<div class="form-group">
								<label class="control-label col-lg-3">Tipo de Incapacidad:<span class="text-danger">*</span></label>
									<div class="col-lg-9">
									<?php if($candado == 1) { ?>
										<select name="IDtipo_incapacidad" id="IDtipo_incapacidad" class="form-control" required="required">
											<option value="">Seleccione una opción</option> 
													<?php do {  ?>
													<option value="<?php echo $row_tipos_incapacidad['IDtipo_incapacidad']?>" <?php if (!(strcmp($row_tipos_incapacidad['IDtipo_incapacidad'], $row_elcertificado['IDtipo_incapacidad']))) {echo "SELECTED";} ?>><?php echo $row_tipos_incapacidad['tipo_incapacidad']?> (<?php echo $row_tipos_incapacidad['tipo_incapacidad_codigo']?>)</option>
													<?php
													} while ($row_tipos_incapacidad = mysql_fetch_assoc($tipos_incapacidad));
													$rows = mysql_num_rows($tipos_incapacidad);
													if($rows > 0) {
													mysql_data_seek($tipos_incapacidad, 0);
													$row_tipos_incapacidad = mysql_fetch_assoc($tipos_incapacidad);
													} ?>
										</select>
										<?php } else {  ?>
											<select name="IDtipo_incapacidad" id="IDtipo_incapacidad" class="form-control" required="required">
											<option value="">Seleccione una opción</option> 
													<?php do {  ?>
													<option value="<?php echo $row_tipos_incapacidad['IDtipo_incapacidad']?>"><?php echo $row_tipos_incapacidad['tipo_incapacidad']?> (<?php echo $row_tipos_incapacidad['tipo_incapacidad_codigo']?>)</option>
													<?php
													} while ($row_tipos_incapacidad = mysql_fetch_assoc($tipos_incapacidad));
													$rows = mysql_num_rows($tipos_incapacidad);
													if($rows > 0) {
													mysql_data_seek($tipos_incapacidad, 0);
													$row_tipos_incapacidad = mysql_fetch_assoc($tipos_incapacidad);
													} ?>
										</select>
										<?php }  ?>
									</div>
								</div>
								<!-- /basic text input -->


									<!-- /basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Certificado:<span class="text-danger">*</span></label>
										<div class="col-lg-7">
										<?php if($candado == 1) { ?>
											<input type="file" class="file-styled" name="foto" id="foto">
											<p class="text text-muted">Archivos permitidos: <code>pdf</code>.</br>
										<?php } else { ?>
											<input type="file" class="file-styled" name="foto" id="foto" required="required">
											<p class="text text-muted">Archivos permitidos: <code>pdf</code>.</br>
										<?php } ?>

										</div>
										<div class="col-lg-2">
										<?php if($candado == 1) { ?>
											<p><a href="<?php echo $row_elcertificado['file_certificado']; ?>" class="btn btn-warning">Descargar archivo</a><p>
										<?php } else { ?>
										<?php } ?>

										</div>
									</div>
									<!-- /basic text input -->
                                   
								<?php if(isset($_GET['IDcertificado'])) { ?>
										<button type="submit"  class="btn btn-primary">Actualizar</button>
                                        <input type="hidden" name="MM_update" value="form1">
										<?php if( $row_elcertificado['file_certificado'] != '') { ?>
										<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
										<?php } ?>
                                <?php } else { ?>
										<input type="submit" name="KT_insert1" class="btn btn-primary" id="KT_insert1" value="Agregar Certificado" />
										<input type="hidden" name="MM_insert" value="form1" />
										<input type="hidden" name="IDincapacidad" value="<?php echo $IDincapacidad; ?>" />
								<?php } ?>
								<button type="button" onClick="window.location.href='incapacidades_z.php'" class="btn btn-default btn-icon">Regresar</button>

					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="incapacidades_certificados_z.php?IDincapacidad=<?php echo $IDincapacidad; ?>&IDcertificado=<?php echo $IDcertificado; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- danger modal -->

						 
						 
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