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
$IDmatrizes = $row_usuario['IDmatrizes'];

$IDempleado = $_GET['IDempleado'];
$IDetapa = $_GET['IDetapa'];
$IDasesoria = $_GET['IDasesoria'];

if(isset($_GET['IDasesoria_etapa'])) { $IDasesoria_etapa = $_GET['IDasesoria_etapa'];

mysql_select_db($database_vacantes, $vacantes);
$query_asesoria = "SELECT rel_lab_etapas.IDasesoria_etapa, rel_lab_etapas.IDasesoria, rel_lab_etapas.IDetapa, rel_lab_etapas.IDestatus, rel_lab_etapas.IDempleado_jefe, rel_lab_etapas.IDempleado_rh, rel_lab_etapas.IDempleado_testigo, rel_lab_etapas.file, rel_lab_etapas.fecha_captura, rel_lab_etapas.hora_inicio, rel_lab_etapas.fecha_inicio, rel_lab_etapas.fecha_fin, rel_lab_etapas.texto_esperado, rel_lab_etapas.texto_observado, rel_lab_etapas.texto_acuerdos, rel_lab_etapas.texto_resultados, rel_lab_etapas.observaciones, rel_lab_asesorias.IDempleado, rel_lab_asesorias.emp_paterno, rel_lab_asesorias.emp_materno, rel_lab_asesorias.fecha_antiguedad, rel_lab_asesorias.emp_nombre, rel_lab_asesorias.denominacion, rel_lab_asesorias.rfc, rel_lab_asesorias.IDpuesto, rel_lab_asesorias.IDsucursal, rel_lab_asesorias.IDarea, rel_lab_asesorias.anio, rel_lab_asesorias.IDmatriz, rel_lab_asesorias.IDestatus, rel_lab_asesorias.jefe_inmediato, rel_lab_etapas.politica1, rel_lab_etapas.politica2, rel_lab_etapas.politica3, rel_lab_etapas.politica4, rel_lab_asesorias.IDmotivo, rel_lab_tipos.motivo, rel_lab_tipos.instrucciones, rel_lab_tipos.tiempo FROM rel_lab_etapas LEFT JOIN rel_lab_asesorias ON rel_lab_etapas.IDasesoria = rel_lab_asesorias.IDasesoria LEFT JOIN rel_lab_tipos ON rel_lab_asesorias.IDmotivo = rel_lab_tipos.IDmotivo WHERE rel_lab_etapas.IDasesoria_etapa = $IDasesoria_etapa";
mysql_query("SET NAMES 'utf8'");
$asesoria = mysql_query($query_asesoria, $vacantes) or die(mysql_error());
$row_asesoria = mysql_fetch_assoc($asesoria);
$totalRows_asesoria = mysql_num_rows($asesoria);

} else  { 

mysql_select_db($database_vacantes, $vacantes);
$query_asesoria = "SELECT rel_lab_asesorias.IDempleado, rel_lab_asesorias.emp_paterno, rel_lab_asesorias.jefe_inmediato, rel_lab_asesorias.emp_materno, rel_lab_asesorias.fecha_antiguedad, rel_lab_asesorias.emp_nombre, rel_lab_asesorias.denominacion, rel_lab_asesorias.rfc, rel_lab_asesorias.IDpuesto, rel_lab_asesorias.IDsucursal, rel_lab_asesorias.IDarea, rel_lab_asesorias.anio, rel_lab_asesorias.IDmatriz, rel_lab_asesorias.IDestatus, rel_lab_tipos.motivo, rel_lab_tipos.instrucciones, rel_lab_tipos.tiempo FROM rel_lab_asesorias  LEFT JOIN rel_lab_tipos ON rel_lab_asesorias.IDmotivo = rel_lab_tipos.IDmotivo WHERE rel_lab_asesorias.IDasesoria = $IDasesoria";
mysql_query("SET NAMES 'utf8'");
$asesoria = mysql_query($query_asesoria, $vacantes) or die(mysql_error());
$row_asesoria = mysql_fetch_assoc($asesoria);
$totalRows_asesoria = mysql_num_rows($asesoria);

}

$fechac = date("dmY"); // la fecha actual
$fecha3 = date("Y-m-d"); // la fecha actual
$formatos_permitidos =  array('pdf', 'doc');
$fechapp = date("YmdHis"); // la fecha actual


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
$IDusuario_carpeta = 'RELAB/'.$IDempleado;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name'];
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
header('Location: rel_lab_etapas.php?info=9&IDempleado='.$IDempleado.'&IDetapa='.$IDetapa.'&IDasesoria='.$IDasesoria.'');
exit;
} 
$name_new = $IDasesoria."_".$IDetapa."_".$fechac.".".$extension;
$targetPath = 'RELAB/'.$IDempleado."/".$name_new;
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);

$fecha1a = $_POST['fecha_inicio']; 
$fecha1b = explode("/",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$fecha2a = $_POST['fecha_fin']; 
$fecha2b = explode("/",$fecha2a);
$fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];

if(isset($_POST['politica1'])) {$politica1 = $_POST['politica1'];} else {$politica1 = $row_asesoria['politica1'];} 
if(isset($_POST['politica2'])) {$politica2 = $_POST['politica2'];} else {$politica2 = $row_asesoria['politica2'];} 
if(isset($_POST['politica3'])) {$politica3 = $_POST['politica3'];} else {$politica3 = $row_asesoria['politica3'];} 
if(isset($_POST['politica4'])) {$politica4 = $_POST['politica4'];} else {$politica4 = $row_asesoria['politica4'];} 
if(isset($_POST['politica5'])) {$politica5 = $_POST['politica5'];} else {$politica5 = $row_asesoria['politica5'];} 

$insertSQL = sprintf("INSERT INTO rel_lab_etapas (IDasesoria, IDetapa, IDempleado_jefe, IDempleado_rh, IDempleado_testigo, file, fecha_captura, fecha_inicio, fecha_fin, hora_inicio, texto_esperado, texto_observado, texto_acuerdos, texto_resultados, observaciones, politica1, politica2, politica3, politica4, politica5) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDasesoria'], "text"),
                       GetSQLValueString($_POST['IDetapa'], "text"),
                       GetSQLValueString($_POST['IDempleado_jefe'], "text"),
                       GetSQLValueString($_POST['IDempleado_rh'], "text"),
                       GetSQLValueString($_POST['IDempleado_testigo'], "text"),
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($fecha3, "text"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($fecha2, "text"),
                       GetSQLValueString($_POST['hora_inicio'], "text"),
                       GetSQLValueString($_POST['texto_esperado'], "text"),
                       GetSQLValueString($_POST['texto_observado'], "text"),
                       GetSQLValueString($_POST['texto_acuerdos'], "text"),
                       GetSQLValueString($_POST['texto_resultados'], "text"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($politica1, "int"),
                       GetSQLValueString($politica2, "int"),
                       GetSQLValueString($politica3, "int"),
                       GetSQLValueString($politica4, "int"),
                       GetSQLValueString($politica5, "int"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();
 header("Location: rel_lab.php?IDasesoria=$IDasesoria&info=1");
 }
 
 
 if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$IDusuario_carpeta = 'RELAB/'.$IDempleado;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name'];
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
header('Location: rel_lab_etapas.php?info=9&IDempleado='.$IDempleado.'&IDetapa='.$IDetapa.'&IDasesoria='.$IDasesoria.'');
exit;
} 
$name_new = $IDasesoria."_".$IDetapa."_".$fechac.".".$extension;
$targetPath = 'RELAB/'.$IDempleado."/".$name_new;
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);

$fecha1a = $_POST['fecha_inicio']; 
$fecha1b = explode("/",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$fecha2a = $_POST['fecha_fin']; 
$fecha2b = explode("/",$fecha2a);
$fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];

if(isset($_POST['politica1'])) {$politica1 = $_POST['politica1'];} else {$politica1 = $row_asesoria['politica1'];} 
if(isset($_POST['politica2'])) {$politica2 = $_POST['politica2'];} else {$politica2 = $row_asesoria['politica2'];} 
if(isset($_POST['politica3'])) {$politica3 = $_POST['politica3'];} else {$politica3 = $row_asesoria['politica3'];} 
if(isset($_POST['politica4'])) {$politica4 = $_POST['politica4'];} else {$politica4 = $row_asesoria['politica4'];} 
if(isset($_POST['politica5'])) {$politica5 = $_POST['politica5'];} else {$politica5 = $row_asesoria['politica5'];} 

$updateSQL = sprintf("UPDATE rel_lab_etapas SET file=%s, IDempleado_jefe=%s, IDempleado_rh=%s, IDempleado_testigo=%s, fecha_captura=%s, fecha_inicio=%s, fecha_fin=%s, hora_inicio=%s, texto_esperado=%s, texto_observado=%s, texto_acuerdos=%s, texto_resultados=%s,  observaciones=%s, politica1=%s, politica2=%s, politica3=%s, politica4=%s, politica5=%s WHERE IDetapa = $IDetapa AND IDasesoria_etapa=%s",
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($_POST['IDempleado_jefe'], "text"),
                       GetSQLValueString($_POST['IDempleado_rh'], "text"),
                       GetSQLValueString($_POST['IDempleado_testigo'], "text"),
                       GetSQLValueString($fecha3, "text"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($fecha2, "text"),
                       GetSQLValueString($_POST['hora_inicio'], "text"),
                       GetSQLValueString($_POST['texto_esperado'], "text"),
                       GetSQLValueString($_POST['texto_observado'], "text"),
                       GetSQLValueString($_POST['texto_acuerdos'], "text"),
                       GetSQLValueString($_POST['texto_resultados'], "text"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($politica1, "int"),
                       GetSQLValueString($politica2, "int"),
                       GetSQLValueString($politica3, "int"),
                       GetSQLValueString($politica4, "int"),
                       GetSQLValueString($politica5, "int"),
                       GetSQLValueString($_POST['IDasesoria_etapa'], "text"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();
 header("Location: rel_lab.php?IDasesoria=$IDasesoria&info=2");
 }

 

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDasesoria_etapa'];
  $deleteSQL = "DELETE FROM rel_lab_etapas WHERE IDasesoria_etapa ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: rel_lab.php?info=4");
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
$query_puesto = "SELECT DISTINCT * FROM vac_puestos ORDER BY denominacion asc";
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
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/interactions.min.js"></script>

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	
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
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<!-- /theme JS files -->
</head>

<body class="has-detached-right" onLoad="realizaProceso()">	
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
                        <?php if((isset($_GET['info']) && $_GET['info'] == 1)) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Datos actualizados correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo cargado no es del tipo de archivos permitidos.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">									
							<?php if(isset($_GET['IDasesoria_etapa'])) { ?>Editar Evento<?php } else {?>Agregar Evento<?php } ?></h5>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.</p>
                            <p>&nbsp;</p>
							
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
							<fieldset class="content-group">
								
						<legend class="text-semibold">Datos Empleado</legend>

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Detalles de la Asesoría:</label>
										<div class="col-lg-6">
											<ul>
											<li><strong>Motivo:</strong> <?php echo $row_asesoria['motivo']; ?></li>
											<li><strong>Recomendaciones: </strong><?php echo $row_asesoria['instrucciones']; ?></li>
											<li><strong>Jefe Inmediato: </strong><?php echo $row_asesoria['jefe_inmediato']; ?></li>
											</ul>
										</div>
									</div>
									<!-- /basic text input -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">No. de Empleado:</label>
										<div class="col-lg-9">
						<input type="text" name="IDempleado" id="IDempleado" class="form-control" value="<?php echo $row_asesoria['IDempleado']; ?>" readonly="readonly" >
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre Empleado:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo $row_asesoria['emp_paterno']." ".$row_asesoria['emp_materno']." ".$row_asesoria['emp_nombre']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha de antiguedad:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo date("d-m-Y",strtotime($row_asesoria['fecha_antiguedad']));  ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->
					

<?php if ($IDetapa == 1) {?>
							<legend class="text-semibold">Validación de Política</legend>
							
  									<!-- Basic text input -->
										<input type="hidden" name="hora_inicio" id="hora_inicio" class="form-control" value="0">
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Conoce sus funciones:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="politica1" id="politica1" class="form-control"  required="required">
												<option value=""<?php if (isset($row_asesoria['politica1']) and $row_asesoria['politica1'] == '') {echo "SELECTED";} ?>>Seleccione</option>
												<option value="0"<?php if (isset($row_asesoria['politica1']) and $row_asesoria['politica1'] == 0) {echo "SELECTED";} ?>>No las conoce</option>
												<option value="1"<?php if (isset($row_asesoria['politica1']) and $row_asesoria['politica1'] == 1) {echo "SELECTED";} ?>>Si las conoce</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Recibió capacitación y entrenamiento a su llegada a la empresa:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="politica2" id="politica2" class="form-control"  required="required">
												<option value=""<?php if (isset($row_asesoria['politica2']) and $row_asesoria['politica2'] == '') {echo "SELECTED";} ?>>Seleccione</option>
												<option value="0"<?php if (isset($row_asesoria['politica2']) and $row_asesoria['politica2'] == 0) {echo "SELECTED";} ?>>No recibió capacitación</option>
												<option value="1"<?php if (isset($row_asesoria['politica2']) and $row_asesoria['politica2'] == 1) {echo "SELECTED";} ?>>Si recibió capacitación</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Conoce sus metas / objetivos de desempeño:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="politica3" id="politica3" class="form-control"  required="required">
												<option value=""<?php if (isset($row_asesoria['politica3']) and $row_asesoria['politica3'] == '') {echo "SELECTED";} ?>>Seleccione</option>
												<option value="0"<?php if (isset($row_asesoria['politica3']) and $row_asesoria['politica3'] == 0) {echo "SELECTED";} ?>>No conoce sus metas y/o objetivos</option>
												<option value="1"<?php if (isset($row_asesoria['politica3']) and $row_asesoria['politica3'] == 1) {echo "SELECTED";} ?>>Si conoce sus metas y/o objetivos</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sabe dónde encontrar las políticas de la empresa:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="politica4" id="politica4" class="form-control"  required="required">
												<option value=""<?php if (isset($row_asesoria['politica4']) and $row_asesoria['politica4'] == '') {echo "SELECTED";} ?>>Seleccione</option>
												<option value="0"<?php if (isset($row_asesoria['politica4']) and $row_asesoria['politica4'] == 0) {echo "SELECTED";} ?>>No sabe donde encontrar las políticas</option>
												<option value="1"<?php if (isset($row_asesoria['politica4']) and $row_asesoria['politica4'] == 1) {echo "SELECTED";} ?>>Si sabe donde encontrar las políticas</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
									
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Recibió las herramientas adecuadas de acuerdo con sus funciones:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="politica5" id="politica5" class="form-control"  required="required">
												<option value=""<?php if (isset($row_asesoria['politica5']) and $row_asesoria['politica5'] == '') {echo "SELECTED";} ?>>Seleccione</option>
												<option value="0"<?php if (isset($row_asesoria['politica5']) and $row_asesoria['politica5'] == 0) {echo "SELECTED";} ?>>No las recibió</option>
												<option value="1"<?php if (isset($row_asesoria['politica5']) and $row_asesoria['politica5'] == 1) {echo "SELECTED";} ?>>Si las recibió</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
									
						<legend class="text-semibold">Etapa 1: Reorientación Eficaz</legend>

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de la reorientación eficaz: <span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" value="<?php if (!isset($row_asesoria['fecha_inicio']) or $row_asesoria['fecha_inicio'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_asesoria['fecha_inicio']));  } ?>"  required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Basic text input -->
										<input type="hidden" name="texto_esperado" id="texto_esperado" class="form-control" value="0">
									<!-- /basic text input -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Motivo que generó la reorientación eficaz: </label>
										<div class="col-lg-9">
                                          <textarea name="texto_observado" rows="2" class="wysihtml5 wysihtml5-min form-control" id="texto_observado" placeholder="Indique los comportamiento, desempeño, faltas o actitud observados."><?php if (isset($row_asesoria['texto_observado'])) { echo $row_asesoria['texto_observado']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->



									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Acuerdos de la reorientación eficaz:</label>
										<div class="col-lg-9">
                                          <textarea name="texto_acuerdos" rows="2" class="wysihtml5 wysihtml5-min form-control" id="texto_acuerdos" placeholder="Indique los acuerdos verbales que se hayan comprometidos en la reorientación eficaz."><?php if (isset($row_asesoria['texto_acuerdos'])) { echo $row_asesoria['texto_acuerdos']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
										<input type="hidden" name="fecha_fin" id="fecha_fin" class="form-control" value="0">
									<!-- /basic text input -->
									
									<!-- Basic text input -->
										<input type="hidden" name="texto_resultados" id="texto_resultados" class="form-control" value="0">
									<!-- /basic text input -->
									
									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Observaciones:</label>
										<div class="col-lg-9">
                                          <textarea name="observaciones" rows="2" class="wysihtml5 wysihtml5-min form-control" id="observaciones" placeholder="Observaciones."><?php if (isset($row_asesoria['observaciones'])) { echo $row_asesoria['observaciones']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->


<?php } else if ($IDetapa == 2) {?>

						<legend class="text-semibold">Etapa 2: Asesoría para Mejorar</legend>

									<!-- Basic text input -->
										<input type="hidden" name="hora_inicio" id="hora_inicio" class="form-control" value="0">
									<!-- /basic text input -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Jefe Inmediato:</label>
										<div class="col-lg-9">
						<input type="text" name="IDempleado_jefe" id="IDempleado_jefe" class="form-control" value="<?php if (isset($row_asesoria['IDempleado_jefe'])) { echo $row_asesoria['IDempleado_jefe']; } ?>" placeholder="Indique el nombre del jefe inmediato del Empleado" required="required">
										</div>
									</div>
									<!-- /basic text input -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Recursos Humanos:</label>
										<div class="col-lg-9">
						<input type="text" name="IDempleado_rh" id="IDempleado_rh" class="form-control" value="<?php if (isset($row_asesoria['IDempleado_rh'])) { echo $row_asesoria['IDempleado_rh']; } ?>" placeholder="Indique el nombre del representante de RH" required="required">
										</div>
									</div>
									<!-- /basic text input -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Testigo:</label>
										<div class="col-lg-9">
						<input type="text" name="IDempleado_testigo" id="IDempleado_testigo" class="form-control" value="<?php if (isset($row_asesoria['IDempleado_testigo'])) { echo $row_asesoria['IDempleado_testigo']; } ?>" placeholder="Indique el nombre del testigo">
										</div>
									</div>
									<!-- /basic text input -->
									
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de la asesoría para mejorar:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" value="<?php if (!isset($row_asesoria['fecha_inicio']) or $row_asesoria['fecha_inicio'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_asesoria['fecha_inicio']));  }?>"  required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->


									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Comportamiento, desempeño o actitud esperados:</label>
										<div class="col-lg-9">
                                          <textarea name="texto_esperado" rows="2" class="wysihtml5 wysihtml5-min form-control" id="texto_esperado" placeholder="Indique los comportamiento, desempeño o actitud esperados."><?php if (isset($row_asesoria['texto_esperado'])) { echo $row_asesoria['texto_esperado']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Comportamiento, desempeño, falta o actitud observados:</label>
										<div class="col-lg-9">
                                          <textarea name="texto_observado" rows="2" class="wysihtml5 wysihtml5-min form-control" id="texto_observado" placeholder="Indique los comportamiento, desempeño, faltas o actitud observados."><?php if (isset($row_asesoria['texto_observado'])) { echo $row_asesoria['texto_observado']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Acuerdos establecidos:</label>
										<div class="col-lg-9">
                                          <textarea name="texto_acuerdos" rows="2" class="wysihtml5 wysihtml5-min form-control" id="texto_acuerdos" placeholder="Indique los acuerdos verbales que se hayan comprometidos en la reorientación eficaz."><?php if (isset($row_asesoria['texto_acuerdos'])) { echo $row_asesoria['texto_acuerdos']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->
									
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de revisión de acuerdos:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" value="<?php if (!isset($row_asesoria['fecha_fin']) or $row_asesoria['fecha_fin'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_asesoria['fecha_fin']));  }?>"  required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->


									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Acuerdos logrados:</label>
										<div class="col-lg-9">
                                          <textarea name="texto_resultados" rows="2" class="wysihtml5 wysihtml5-min form-control" id="texto_resultados" placeholder="Indique los acuerdos que se hayan cumplido y los que no se hayan logrado cubrir y las razones."><?php if (isset($row_asesoria['texto_resultados'])) { echo $row_asesoria['texto_resultados']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Observaciones:</label>
										<div class="col-lg-9">
                                          <textarea name="observaciones" rows="2" class="wysihtml5 wysihtml5-min form-control" id="observaciones" placeholder="Observaciones."><?php if (isset($row_asesoria['observaciones'])) { echo $row_asesoria['observaciones']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->

								  <div class="form-group">
										<label class="control-label col-lg-3">Documento:</label>
										<div class="col-lg-5">
											<input type="file" class="file-styled" name="foto" id="foto">
										<p class="text text-muted">Archivos permitidos: <code>pdf</code>.</br>
										</div>
										<div class="col-lg-4">
                                        <?php if (isset($row_asesoria['file']) and $row_asesoria['file'] != ''){ ?><button type="button" onClick="window.location.href='<?php echo "RELAB/".$row_asesoria['IDempleado']."/".$row_asesoria['file']; ?>'" class="btn btn-info btn-icon">Descargar archivo</button><?php } ?></p>
										</div>
									</div>
									<!-- /basic text input -->

							<?php if(isset($_GET['IDasesoria_etapa'])) { ?>
								  <div class="form-group">
										<label class="control-label col-lg-3">Formato Prellenado:</label>
										<div class="col-lg-9">
                                        <button type="button" onClick="window.location.href='rel_lab_formato.php?IDasesoria_etapa=<?php echo $row_asesoria['IDasesoria_etapa']; ?>&formato=2'" class="btn btn-success btn-icon">Descargar archivo</button></p>
										</div>
									</div>
									<!-- /basic text input -->
							<?php } ?>


<?php } else if ($IDetapa == 3) {?>

						<legend class="text-semibold">Etapa 3: Acta Administrativa</legend>

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Jefe Inmediato:</label>
										<div class="col-lg-9">
						<input type="text" name="IDempleado_jefe" id="IDempleado_jefe" class="form-control" value="<?php if (isset($row_asesoria['IDempleado_jefe'])) { echo $row_asesoria['IDempleado_jefe']; } ?>" placeholder="Indique el nombre del jefe inmediato del Empleado" required="required">
										</div>
									</div>
									<!-- /basic text input -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Recursos Humanos:</label>
										<div class="col-lg-9">
						<input type="text" name="IDempleado_rh" id="IDempleado_rh" class="form-control" value="<?php if (isset($row_asesoria['IDempleado_jefe'])) { echo $row_asesoria['IDempleado_rh']; } ?>" placeholder="Indique el nombre del representante de RH" required="required">
										</div>
									</div>
									<!-- /basic text input -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Testigo:</label>
										<div class="col-lg-9">
						<input type="text" name="IDempleado_testigo" id="IDempleado_testigo" class="form-control" value="<?php if (isset($row_asesoria['IDempleado_jefe'])) { echo $row_asesoria['IDempleado_testigo']; } ?>" placeholder="Indique el nombre del testigo">
										</div>
									</div>
									<!-- /basic text input -->
									
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha del acta administrativa:<span class="text-danger">*</span></label>
			                        <div class="col-lg-6">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" value="<?php if (!isset($row_asesoria['fecha_inicio']) or  $row_asesoria['fecha_inicio'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_asesoria['fecha_inicio']));  }?>"  required="required">
									</div>
                                   </div>
   			                        <div class="col-lg-3">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-alarm"></i></span>
                                    	<input type="text" class="form-control  pickatime" name="hora_inicio" id="hora_inicio" value="<?php  if ($row_asesoria['hora_inicio'] == "") { echo "";} else { echo $row_asesoria['hora_inicio']; }?>" required="required"  placeholder="Selecciona la hora">
									</div>
                                   </div>

                                  </div> 
									<!-- Fecha -->


	
									<!-- Basic text input -->
										<input type="hidden" name="texto_esperado" id="texto_esperado" class="form-control" value="0">
									<!-- /basic text input -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Hechos (redactar texto de echos para el Acta):</label>
										<div class="col-lg-9">
                                          <textarea name="texto_observado" rows="2" class="wysihtml5 wysihtml5-min form-control" id="texto_observado" placeholder="Indique los comportamiento, desempeño, faltas o actitud observados."><?php if (isset($row_asesoria['texto_observado'])) { echo $row_asesoria['texto_observado']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Observaciones de RH, indicar lo esperado:</label>
										<div class="col-lg-9">
                                          <textarea name="texto_acuerdos" rows="2" class="wysihtml5 wysihtml5-min form-control" id="texto_acuerdos" placeholder="Indique los acuerdos verbales que se hayan comprometidos en la reorientación eficaz."><?php if (isset($row_asesoria['texto_acuerdos'])) { echo $row_asesoria['texto_acuerdos']; }?></textarea>

										</div>
									</div>
									<!-- /basic text input -->
									
									<!-- Basic text input -->
										<input type="hidden" name="fecha_fin" id="fecha_fin" class="form-control" value="0">
									<!-- /basic text input -->

									
									<!-- Basic text input -->
										<input type="hidden" name="texto_resultados" id="texto_resultados" class="form-control" value="0">
									<!-- /basic text input -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Observaciones del empleado:</label>
										<div class="col-lg-9">
                                          <textarea name="observaciones" rows="2" class="wysihtml5 wysihtml5-min form-control" id="observaciones" placeholder="Observaciones."><?php if (isset($row_asesoria['observaciones'])) { echo $row_asesoria['observaciones']; } ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->

								  <div class="form-group">
										<label class="control-label col-lg-3">Documento:</label>
										<div class="col-lg-5">
											<input type="file" class="file-styled" name="foto" id="foto">
										<p class="text text-muted">Archivos permitidos: <code>pdf</code>.</br>
										</div>
										<div class="col-lg-4">
                                        <?php if (isset($row_asesoria['file']) and $row_asesoria['file'] != ''){ ?><button type="button" onClick="window.location.href='<?php echo "RELAB/".$row_asesoria['IDempleado']."/".$row_asesoria['file']; ?>'" class="btn btn-info btn-icon">Descargar archivo</button><?php } ?></p>
										</div>
									</div>
									<!-- /basic text input -->

							<?php if(isset($_GET['IDasesoria_etapa'])) { ?>
								  <div class="form-group">
										<label class="control-label col-lg-3">Formato Prellenado:</label>
										<div class="col-lg-9">
                                        <button type="button" onClick="window.location.href='rel_lab_formato2.php?IDasesoria_etapa=<?php echo $row_asesoria['IDasesoria_etapa']; ?>&formato=3'" class="btn btn-success btn-icon">Descargar archivo</button></p>
										</div>
									</div>
									<!-- /basic text input -->
							<?php } ?>

<?php } ?>



								<?php if(isset($_GET['IDasesoria_etapa']) OR  $row_asesoria['IDestatus'] != 2) { ?>
								<?php if(isset($_GET['IDasesoria_etapa'])) { ?>
										<button type="submit"  class="btn btn-primary">Actualizar</button>
                                        <input type="hidden" name="MM_update" value="form1">
										<input type="hidden" name="IDtarea" value="<?php echo $_GET['IDtarea']; ?>">
                         				<input type="hidden" name="IDasesoria_etapa" value="<?php echo $IDasesoria_etapa; ?>">
										<button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                                <?php } else { ?>
										<button type="submit"  class="btn btn-primary">Agregar</button>
                                        <input type="hidden" name="MM_insert" value="form1">
										<input type="hidden" name="IDasesoria" value="<?php echo $IDasesoria; ?>">
										<input type="hidden" name="IDetapa" value="<?php echo $IDetapa; ?>">
								<?php } ?>
								<?php } ?>
										<button type="button" onClick="window.location.href='rel_lab.php'" class="btn btn-default btn-icon">Regresar</button>

						 
					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la Asesoría?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="rel_lab_etapas.php?IDasesoria_etapa=<?php echo $IDasesoria_etapa; ?>&borrar=1">Si borrar</a>
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