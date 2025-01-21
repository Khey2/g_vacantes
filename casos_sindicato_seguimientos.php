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
$IDusuario = $row_usuario['IDusuario'];
$mis_areas = $row_usuario['IDareas'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

$IDsindicato = $_GET['IDsindicato'];
$query_sindicato = "SELECT casos_sindicato.IDsindicato, casos_sindicato.fecha_inicio, casos_sindicato.fecha_fin, casos_sindicato.IDusuario, casos_sindicato.IDmatriz, casos_sindicato.IDsucursal, casos_sindicato.IDarea, casos_sindicato.IDestatus, casos_sindicato.asunto, casos_sindicato.descripcion, casos_sindicato.file, casos_sindicato.descripcion_cierre, vac_matriz.matriz, vac_sucursal.sucursal, vac_areas.area  FROM casos_sindicato LEFT JOIN vac_matriz ON casos_sindicato.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_sucursal ON casos_sindicato.IDsucursal = vac_sucursal.IDsucursal LEFT JOIN vac_areas ON casos_sindicato.IDarea = vac_areas.IDarea WHERE IDsindicato = '$IDsindicato'"; 
mysql_query("SET NAMES 'utf8'");
$sindicato = mysql_query($query_sindicato, $vacantes) or die(mysql_error());
$row_sindicato = mysql_fetch_assoc($sindicato);
$totalRows_sindicato = mysql_num_rows($sindicato);
$fecha_inicio = date( 'd/m/Y' , strtotime($row_sindicato['fecha_inicio'])); 
$fecha_esperada = date( 'd/m/Y' , strtotime($row_sindicato['fecha_esperada'])); 

if(isset($_GET['IDsindicato_seguimientos'])) { 

$IDsindicato_seguimientos = $_GET['IDsindicato_seguimientos'];
mysql_select_db($database_vacantes, $vacantes);
$query_sindicato_seguimiento = "SELECT * FROM casos_sindicato_seguimientos WHERE IDsindicato_seguimientos = '$IDsindicato_seguimientos'";
$sindicato_seguimiento = mysql_query($query_sindicato_seguimiento, $vacantes) or die(mysql_error());
$row_sindicato_seguimiento = mysql_fetch_assoc($sindicato_seguimiento);
$totalRows_sindicato_seguimiento = mysql_num_rows($sindicato_seguimiento);

$query_casos_responsable = "SELECT casos_responsables.IDresponsable, vac_usuarios.IDusuario, vac_usuarios.usuario, vac_usuarios.usuario_correo, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno,  vac_usuarios.usuario_materno, vac_usuarios.IDusuario_puesto, vac_puestos.denominacion FROM vac_usuarios INNER JOIN casos_responsables ON vac_usuarios.IDusuario = casos_responsables.IDusuario INNER JOIN vac_puestos ON  vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto WHERE IDsindicato = $IDsindicato";
$casos_responsable = mysql_query($query_casos_responsable, $vacantes) or die(mysql_error());
$row_casos_responsable = mysql_fetch_assoc($casos_responsable);
$totalRows_casos_responsable = mysql_num_rows($casos_responsable);

}

$fecha = date("Y-m-d"); // la fecha actual
$formatos_permitidos =  array('PDF', 'JPG', 'JEPG', 'PNG', 'DOC', 'pdf', 'jpg', 'jepg', 'png', 'doc');
$fechapp = date("YmdHis"); // la fecha actual


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
$IDusuario_carpeta = 'SINDICATO/'.$IDsindicato;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name'];
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
header('Location: casos_sindicato_edit.php?info=9&IDsindicato_seguimientos='.$IDsindicato_seguimientos.'&IDsindicato='.$IDsindicato.'');
exit;
} 
$name_new = $IDsindicato."_".$IDsindicato_seguimientos."_".$fechapp.".".$extension;
$targetPath = 'SINDICATO/'.$IDsindicato."/".$name_new;
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);

$fecha1a = $_POST['fecha_reporte']; 
$fecha1b = explode("-",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$insertSQL = sprintf("INSERT INTO casos_sindicato_seguimientos (file, fecha_reporte, descripcion_seguimiento, IDusuario, IDestatus_seguimiento, IDsindicato) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($_POST['descripcion_seguimiento'], "text"),
                       GetSQLValueString($IDusuario, "int"),
                       GetSQLValueString($_POST['IDestatus_seguimiento'], "int"),
                       GetSQLValueString($IDsindicato, "int"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
 
 $last_id =  mysql_insert_id();
 header("Location: casos_seguimiento.php?IDsindicato=$IDsindicato");
 }
 
 
 if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$IDusuario_carpeta = 'SINDICATO/'.$IDsindicato;
if (!file_exists($IDusuario_carpeta)) {mkdir($IDusuario_carpeta, 0777, true);}
	
$name=$_FILES['foto']['name'];
$size=$_FILES['foto']['size'];
$type=$_FILES['foto']['type'];
$temp=$_FILES['foto']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
if(!in_array($extension, $formatos_permitidos) AND isset($_POST['foto'])) {
header('Location: casos_sindicato_edit.php?info=9&IDsindicato_seguimientos='.$IDsindicato_seguimientos.'&IDsindicato='.$IDsindicato.'');
exit;
} 
$name_new = $IDsindicato."_".$IDsindicato_seguimientos."_".$fechapp.".".$extension;
$targetPath = 'SINDICATO/'.$IDsindicato."/".$name_new;
move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);

$fecha1a = $_POST['fecha_reporte']; 
$fecha1b = explode("-",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$updateSQL = sprintf("UPDATE casos_sindicato_seguimientos SET file=%s, fecha_reporte=%s, descripcion_seguimiento=%s, IDusuario=%s, IDsindicato=%s WHERE IDsindicato_seguimientos=%s",
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($_POST['descripcion_seguimiento'], "text"),
                       GetSQLValueString($IDusuario, "int"),
                       GetSQLValueString($IDsindicato, "int"),
                       GetSQLValueString($IDsindicato_seguimientos, "int"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();
 header("Location: casos_sindicato.php?info=12");
 }

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDsindicato_seguimientos'];
  $deleteSQL = "UPDATE casos_sindicato_seguimientos SET IDestatus_seguimiento = 0 WHERE IDsindicato_seguimientos ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: casos_sindicato.php?info=13");
}


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz ORDER BY matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_lsucursal = "SELECT * FROM vac_sucursal ORDER BY sucursal";
$lsucursal = mysql_query($query_lsucursal, $vacantes) or die(mysql_error());
$row_lsucursal = mysql_fetch_assoc($lsucursal);
$totalRows_lsucursal = mysql_num_rows($lsucursal);

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
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js"></script>

	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/xpicker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<!-- /theme JS files -->
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
							Registro agregado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && $_GET['info'] == 9)) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Formato de archivo adjunto no permitido.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && $_GET['info'] == 2)) { ?>
					    <div class="alert bg-primary-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Registro actualizado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
						<!-- Basic alert -->
                        <?php if(isset($row_sindicato['IDestatus']) AND $row_sindicato['IDestatus'] == 2 AND $row_sindicato['fecha_fin'] == '') { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Por favor indique fecha y detalles del cierre del caso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">									
							<?php if(isset($_GET['IDsindicato_seguimientos'])) { ?>Editar Seguimiento<?php } else {?>Agregar Seguimiento<?php } ?></h5>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.<br />
							Se enviará un correo de actualización a los responsables del caso.</p>
                            <p>&nbsp;</p>
							
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
							<fieldset class="content-group">
							
						<legend class="text-semibold">Datos Caso</legend>
							
							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Asunto:</label>
										<div class="col-lg-9">
										<?php echo $row_sindicato['asunto']; ?>
										</div>
									</div>
									<!-- /basic text input -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha:</label>
										<div class="col-lg-9">
										<?php echo date( 'd/m/Y' , strtotime($row_sindicato['fecha_inicio']))?>
										</div>
									</div>
									<!-- /basic text input -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz | Sucursal | Área:</label>
										<div class="col-lg-9">
										<?php echo $row_sindicato['matriz']; ?> | <?php echo $row_sindicato['sucursal']; ?> | <?php echo $row_sindicato['area']; ?>
										</div>
									</div>
									<!-- /basic text input -->


							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Descripción Detallada:</label>
										<div class="col-lg-9">
										<?php echo $row_sindicato['descripcion']; ?>
										</div>
									</div>
									<!-- /basic text input -->
							
									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus:</label>
										<div class="col-lg-9">
										<?php if ($row_sindicato['IDestatus'] == 1 ) { echo "En Proceso"; }
										 else if ($row_sindicato['IDestatus'] == 2 ) { echo "Atendido"; } 
										 else if ($row_sindicato['IDestatus'] == 3 ) { echo "Cerrado"; }
										 else { echo "Sin Estatus"; }?>
										</div>
									</div>
									<!-- /basic text input -->

						<legend class="text-semibold">Datos Seguimiento</legend>

								
<?php if(isset($_GET['IDsindicato_seguimientos'])) { ?>
								
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha seguimiento:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Capturar fecha" name="fecha_reporte" id="fecha_reporte" value="<?php if ($row_sindicato_seguimiento['fecha_reporte'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_sindicato_seguimiento['fecha_reporte'])); }?>" required="required">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->


									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Descripción del seguimiento: </label>
										<div class="col-lg-9">
                                          <textarea name="descripcion_seguimiento" rows="3" required="required" class="wysihtml5 wysihtml5-min form-control" id="descripcion_seguimiento" placeholder="Indique a detalle el seguimiento realizado."><?php if (isset($row_sindicato_seguimiento['descripcion_seguimiento'])) { echo $row_sindicato_seguimiento['descripcion_seguimiento']; } ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- /basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Documento:</label>
										<div class="col-lg-5">
											<input type="file" class="file-styled" name="foto" id="foto">
										<p class="text text-muted">Archivos permitidos: <code>pdf</code>, <code>jpg</code>, <code>png</code>, <code>doc</code>.</br>
										</div>
										<div class="col-lg-4">
                                        <?php if (isset($row_sindicato_seguimiento['file']) and $row_sindicato_seguimiento['file'] != ''){ ?><a href='<?php echo "SINDICATO/".$IDsindicato."/".$row_sindicato_seguimiento['file']; ?>' class="btn btn-info btn-icon" target="_blank">Descargar archivo</a><?php } ?></p>
										</div>
									</div>
									<!-- /basic text input -->

<?php } else { ?>


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha seguimiento:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control pickadate-format" placeholder="Capturar fecha" name="fecha_reporte" id="fecha_reporte" value="" required="required">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->


									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Descripción del seguimiento: </label>
										<div class="col-lg-9">
                                          <textarea name="descripcion_seguimiento" rows="3" required="required" class="wysihtml5 wysihtml5-min form-control" id="descripcion_seguimiento" placeholder="Indique a detalle el caso observado y las acciones solicitadas."></textarea>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- /basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Documento:</label>
										<div class="col-lg-5">
											<input type="file" class="file-styled" name="foto" id="foto">
										<p class="text text-muted">Archivos permitidos: <code>pdf</code>, <code>jpg</code>, <code>png</code>, <code>doc</code>.</br>
										</div>
										<div class="col-lg-4">
                                        <?php if (isset($row_sindicato_seguimiento['file']) and $row_sindicato_seguimiento['file'] != ''){ ?><a href='<?php echo "SINDICATO/".$IDsindicato."/".$row_sindicato_seguimiento['file']; ?>' class="btn btn-info btn-icon" target="_blank">Descargar archivo</a><?php } ?></p>
										</div>
									</div>
									<!-- /basic text input -->


<?php }  ?>


								<?php if(isset($_GET['IDsindicato_seguimientos'])) { ?>
										<button type="submit"  class="btn btn-primary">Actualizar</button>
                                        <input type="hidden" name="MM_update" value="form1">
                                        <input type="hidden" name="IDusuario" value="<?php echo $IDusuario; ?>">
										<button type="button" data-target="#modal_theme_danger" data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                                <?php } else { ?>
										<button type="submit"  class="btn btn-primary">Agregar</button>
                                        <input type="hidden" name="IDusuario" value="<?php echo $IDusuario; ?>">
                                        <input type="hidden" name="IDestatus_seguimiento" value="1">
                                        <input type="hidden" name="MM_insert" value="form1">
								<?php } ?>
										<button type="button" onClick="window.location.href='casos_sindicato.php'" class="btn btn-default btn-icon">Regresar</button>

						 
					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el caso?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="casos_sindicato_seguimientos.php?IDsindicato_seguimientos=<?php echo $_GET['IDsindicato_seguimientos']; ?>&IDsindicato=<?php echo $IDsindicato; ?>&borrar=1">Si borrar</a>
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