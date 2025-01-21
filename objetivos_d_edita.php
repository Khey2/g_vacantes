<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

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

// Start trigger
$formValidation = new tNG_FormValidation();
$tNGs->prepareValidation($formValidation);
// End trigger

//start Trigger_FileUpload trigger
//remove this line if you want to edit the code by hand 
function Trigger_FileUpload(&$tNG) {
  $uploadObj = new tNG_FileUpload($tNG);
  $uploadObj->setFormFieldName("file");
  $uploadObj->setDbFieldName("file");
  $uploadObj->setFolder("sed_rh_files/");
  $uploadObj->setMaxSize(9000);
  $uploadObj->setAllowedExtensions("PNG, jpg, jpeg, png, ppt, pptx, gif, pdf, doc, docx, zip, xls, xlsx, rar");
  $uploadObj->setRename("auto");
  return $uploadObj->Execute();
}
//end Trigger_FileUpload trigger

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
mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
//$anio = $row_variables['anio'];
$anio = 2024;
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
$IDusuario = $row_usuario['IDusuario'];
$el_area = $_SESSION['el_area'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

if(isset($_GET['IDtarea'])) {$IDtarea = $_GET['IDtarea'];} else { $IDtarea = 0;}
mysql_select_db($database_vacantes, $vacantes);
$query_tareas = "SELECT ztar_tareas.foto, ztar_tareas.IDsob, ztar_tareas.dias_recorrer, ztar_tareas.IDsat, ztar_tareas.IDdef, ztar_tareas.descripcion_larga, ztar_tareas.por_evento, ztar_tareas.meses, ztar_tareas.dia, ztar_tareas.matrizes, ztar_tareas.IDtarea,  ztar_tareas.IDarea_rh,  ztar_tareas.descripcion, ztar_tareas.ponderacion,  ztar_tareas.IDperiodicidad,  ztar_areas_rh.area_rh FROM ztar_areas_rh left JOIN ztar_tareas ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh WHERE IDtarea = '$IDtarea'";
mysql_query("SET NAMES 'utf8'");
$tareas = mysql_query($query_tareas, $vacantes) or die(mysql_error());
$row_tareas = mysql_fetch_assoc($tareas);
$totalRows_tareas = mysql_num_rows($tareas);

$query_area = "SELECT * FROM ztar_areas_rh ORDER By area_rh ASC";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

$formatos_permitidos =  array('jpeg', 'png', 'jpg', 'JPEG', 'PNG', 'JPG');
$fechapp = date("YmdHis"); // la fecha actual

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

	if (isset($_POST['matrizes'])) { foreach ($_POST['matrizes'] as $matrize) {	$matrizes = implode(",", $_POST['matrizes']);}	}  else { $matrizes = '1,2,3,4,6,8,9,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,28,29,30';}
	if (isset($_POST['meses'])) { foreach ($_POST['meses'] as $mese) {	$meses = implode(",", $_POST['meses']);}	}  else { $meses = $row_tareas['meses'];}
	if (isset($_POST['por_evento']) AND $_POST['por_evento'] == 1)  { $meses = '1,2,3,4,5,6,7,8,9,10,11,12';}
	if ($_POST['dia'] == 0 or $_POST['dia'] == '')  { $eldia = 1;} else  { $eldia = $_POST['dia'];}
	if ($matrizes == '' or $matrizes == 0) { $matrizes = '1,2,3,4,6,8,9,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,28,29,30'; }
				
	if ($_FILES['foto']['name'] == null ) { $name_new = $row_tareas['foto'];  } else { 
	$name=$_FILES['foto']['name'];
	$size=$_FILES['foto']['size'];
	$type=$_FILES['foto']['type'];
	$temp=$_FILES['foto']['tmp_name'];	
	$extension = pathinfo($name, PATHINFO_EXTENSION);
	if(!in_array($extension, $formatos_permitidos) ) {
	header('Location: objetivos_d_edita.php?IDtarea='.$IDtarea.'&info=9');
	exit;
	} 
	$name_new = $IDtarea.$fechapp.".".$extension;
	$targetPath = 'drhimg/'.$name_new;
	move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);
	}
  $updateSQL = sprintf("UPDATE ztar_tareas SET foto=%s, descripcion=%s, ponderacion=%s,  anio=%s,  meses=%s,  dia=%s,  por_evento=%s, dias_recorrer=%s, matrizes=%s, IDsob=%s, IDsat=%s, IDdef=%s, descripcion_larga=%s WHERE IDtarea='$IDtarea'",
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['ponderacion'], "text"),
                       GetSQLValueString($_POST['anio'], "text"),
                       GetSQLValueString($meses, "text"),
                       GetSQLValueString($eldia, "text"),
                       GetSQLValueString($_POST['por_evento'], "text"),
                       GetSQLValueString($_POST['dias_recorrer'], "int"),
                       GetSQLValueString($matrizes, "text"),
                       GetSQLValueString($_POST['IDsob'], "text"),
                       GetSQLValueString($_POST['IDsat'], "text"),
                       GetSQLValueString($_POST['IDdef'], "text"),
                       GetSQLValueString($_POST['descripcion_larga'], "text"),
                       GetSQLValueString($_POST['IDtarea'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
   header('Location: objetivos_d_edita_update.php?IDtarea='.$IDtarea.'&info=2');
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

	if (isset($_POST['matrizes'])) { foreach ($_POST['matrizes'] as $matrize) {	$matrizes = implode(",", $_POST['matrizes']);}	}  else { $matrizes = '1,2,3,4,6,8,9,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,28,29,30';}
	if (isset($_POST['meses'])) { foreach ($_POST['meses'] as $mese) {	$meses = implode(",", $_POST['meses']);}	}  else { $meses = $row_tareas['meses'];}
	if (isset($_POST['por_evento']) AND $_POST['por_evento'] == 1)  { $meses = '1,2,3,4,5,6,7,8,9,10,11,12';}
	if ($_POST['dia'] == 0 or $_POST['dia'] == '')  { $eldia = 1;} else  { $eldia = $_POST['dia'];}
	if ($matrizes == '' or $matrizes == 0) { $matrizes = '1,2,3,4,6,8,9,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,28,29,30'; }

	if ($_FILES['foto']['name'] == null ) { $name_new = $row_tareas['foto'];  } else { 
	$name=$_FILES['foto']['name'];
	$size=$_FILES['foto']['size'];
	$type=$_FILES['foto']['type'];
	$temp=$_FILES['foto']['tmp_name'];	
	$extension = pathinfo($name, PATHINFO_EXTENSION);
	if(!in_array($extension, $formatos_permitidos) ) {
	header('Location: objetivos_d_edita.php?info=9');
	exit;
	} 
	$name_new = $IDtarea.$fechapp.".".$extension;
	$targetPath = 'drhimg/'.$name_new;
	move_uploaded_file($_FILES['foto']['tmp_name'], $targetPath);
	}

$insertSQL = sprintf("INSERT INTO ztar_tareas (foto, descripcion, ponderacion, anio, meses, dia, por_evento, dias_recorrer, matrizes, IDsob, IDsat, IDdef, descripcion_larga, IDarea_rh) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($_POST['descripcion'], "text"),
                       GetSQLValueString($_POST['ponderacion'], "text"),
                       GetSQLValueString($_POST['anio'], "text"),
                       GetSQLValueString($meses, "text"),
                       GetSQLValueString($eldia, "text"),
                       GetSQLValueString($_POST['por_evento'], "text"),
                       GetSQLValueString($_POST['dias_recorrer'], "int"),
                       GetSQLValueString($matrizes, "text"),
                       GetSQLValueString($_POST['IDsob'], "text"),
                       GetSQLValueString($_POST['IDsat'], "text"),
                       GetSQLValueString($_POST['IDdef'], "text"),
                       GetSQLValueString($_POST['descripcion_larga'], "text"),
                       GetSQLValueString($_POST['IDarea_rh'], "int"));

	mysql_select_db($database_vacantes, $vacantes);
	$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
    $last_id =  mysql_insert_id();
	header('Location: objetivos_d_edita_update.php?IDtarea='.$last_id.'&info=1');
}

mysql_select_db($database_vacantes, $vacantes);
$query_files = "SELECT * FROM ztar_files WHERE ztar_files.IDtarea = '$IDtarea' AND ztar_files.IDmatriz IS NULL";
$files = mysql_query($query_files, $vacantes) or die(mysql_error());
$row_files = mysql_fetch_assoc($files);
$totalRows_files = mysql_num_rows($files);

$query_meses = "SELECT * FROM ztar_meses";
mysql_query("SET NAMES 'utf8'");
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

$query_dias = "SELECT * FROM ztar_dias";
mysql_query("SET NAMES 'utf8'");
$dias = mysql_query($query_dias, $vacantes) or die(mysql_error());
$row_dias = mysql_fetch_assoc($dias);
$totalRows_dias = mysql_num_rows($dias);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz not in (5,7,10,27)";
mysql_query("SET NAMES 'utf8'");
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);


// Make an insert transaction instance
$ins_ztar_files = new tNG_insert($conn_vacantes);
$tNGs->addTransaction($ins_ztar_files);
// Register triggers
$ins_ztar_files->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_ztar_files->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_ztar_files->registerTrigger("END", "Trigger_Default_Redirect", 99, "objetivos_d_edita.php?IDtarea={IDtarea}&info=4");
$ins_ztar_files->registerTrigger("AFTER", "Trigger_FileUpload", 97);
// Add columns
$ins_ztar_files->setTable("ztar_files");
$ins_ztar_files->addColumn("file", "FILE_TYPE", "FILES", "file");
$ins_ztar_files->addColumn("IDtarea", "NUMERIC_TYPE", "POST", "IDtarea");
$ins_ztar_files->addColumn("fecha", "DATE_TYPE", "POST", "fecha");
$ins_ztar_files->addColumn("IDusuario", "STRING_TYPE", "POST", "IDusuario");
$ins_ztar_files->setPrimaryKey("IDfile", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsztar_files = $tNGs->getRecordset("ztar_files");
$row_rsztar_files = mysql_fetch_assoc($rsztar_files);
$totalRows_rsztar_files = mysql_num_rows($rsztar_files);

	// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $IDtarea = $_GET['IDtarea'];
  $deleteSQL = "DELETE FROM ztar_tareas WHERE IDtarea ='$IDtarea'";
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());

  $deleteSQL2 = "DELETE FROM ztar_avances WHERE IDtarea ='$IDtarea'";
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL2, $vacantes) or die(mysql_error());


  header('Location: objetivos_d.php?IDtarea='.$IDtarea.'&info=3');
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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/natural_sort.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/gallery_library.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<!-- /theme JS files -->
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
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
							Se ha agregado correctamente el objetivo a cada Sucursal, favor de validar en avances.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                

                        <!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el archivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 6))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el archivo.
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

					<!-- Detailed task -->
					<div class="row">
						<div class="col-lg-8">

							<!-- Task overview -->
							<div class="panel panel-flat">
								<div class="panel-heading mt-5">
									<h4 class="panel-title"><?php echo $row_area['area_rh']; ?><?php if(isset($_GET['IDtarea'])) {echo ": ".$row_tareas['descripcion'];} ?></h4>
								</div>

								<div class="panel-body">
								<p>Indica la configuración del Objetivo a medir.<br/>
								<strong>Al dar clic en Insertar o Actualizar, se agregarán y/o actualizarán todas las tareas asignadas a los Jefes de RH.</strong><br/>
								Si el Objetivo se evalúa "Por evento", selecciona "Todos" en Meses y "No aplica" en días.</p>

                                    <p>&nbsp;</p>
									<div> 
                                      
									  <form method="post" name="form1" action="<?php echo $editFormAction; ?>"  class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
                                      
                                       <fieldset class="content-group">
                                       
                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Área:</label>
										<div class="col-lg-10">
                                         <select class="form-control" name="IDarea_rh"  required="required">
											<?php do { ?>
											   <option value="<?php echo $row_area['IDarea_rh']?>"<?php 
											   if (!(strcmp($row_area['IDarea_rh'], $el_area))) {echo "selected=\"selected\"";} ?>><?php echo $row_area['area_rh']?></option>
											   <?php
											  } while ($row_area = mysql_fetch_assoc($area));
											  $rows = mysql_num_rows($area);
											  if($rows > 0) { mysql_data_seek($area, 0);
											  $row_area = mysql_fetch_assoc($area); 
											  } ?> 
                                         </select>	
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                       
                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Nombre del Objetivo:</label>
										<div class="col-lg-10">
										<input type="text" name="descripcion" id="descripcion" value="<?php echo KT_escapeAttribute($row_tareas['descripcion']); ?>" class="form-control" required="required"/>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Sucursales:</label>
										<div class="col-lg-6">
                                         <select class="multiselect" multiple="multiple" name="matrizes[]"  required="required">
											<?php $cadena2 = $row_tareas['matrizes']; $array = explode(",", $cadena2);
											do { ?>
											   <option value="<?php echo $row_matrizes['IDmatriz']?>"<?php foreach ($array as $lamatriz) { if (!(strcmp($row_matrizes['IDmatriz'], $lamatriz))) {echo "selected=\"selected\"";} } ?>><?php echo $row_matrizes['matriz']?></option>
											   <?php
											  } while ($row_matrizes = mysql_fetch_assoc($matrizes));
											  $rows = mysql_num_rows($matrizes);
											  if($rows > 0) { mysql_data_seek($matrizes, 0);
											  $row_matrizes = mysql_fetch_assoc($matrizes); 
											  } ?> 
                                         </select>	
                                       </div>
									   
									   	<label class="control-label col-lg-2">Ponderacion (%):</label>
										<div class="col-lg-2">
										<input type="number" min="1" max="100" name="ponderacion" id="ponderacion" value="<?php echo KT_escapeAttribute($row_tareas['ponderacion']); ?>" class="form-control" required="required"/>
										</div>

									   
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-1">Meses:</label>
										<div class="col-lg-2">
                                         <select class="multiselect" multiple="multiple" name="meses[]" required="required">
											<?php $cadena = $row_tareas['meses']; $array = explode(",", $cadena);
											do { ?>
											   <option value="<?php echo $row_meses['IDmes']?>"<?php foreach ($array as $lapromocio) { if (!(strcmp($row_meses['IDmes'], $lapromocio))) {echo "selected=\"selected\"";} } ?>><?php echo $row_meses['mes']?></option>
											   <?php
											  } while ($row_meses = mysql_fetch_assoc($meses));
											  $rows = mysql_num_rows($meses);
											  if($rows > 0) { mysql_data_seek($meses, 0);
											  $row_meses = mysql_fetch_assoc($meses); 
											  } ?> 
                                         </select>	
                                       </div>

										<label class="control-label col-lg-1">Día:</label>
									   <div class="col-lg-2">
                                         <select class="form-control" name="dia" required="required">
											   <option value="99"<?php if (!(strcmp($row_dias['IDdia'], $row_tareas['dia']))) {echo "selected=\"selected\"";} ?>>Último día del mes</option>
											<?php do { ?>
											   <option value="<?php echo $row_dias['IDdia']?>"<?php 
											   if (!(strcmp($row_dias['IDdia'], $row_tareas['dia']))) {echo "selected=\"selected\"";} ?>><?php echo $row_dias['dia']?></option>
											   <?php
											  } while ($row_dias = mysql_fetch_assoc($dias));
											  $rows = mysql_num_rows($dias);
											  if($rows > 0) { mysql_data_seek($dias, 0);
											  $row_dias = mysql_fetch_assoc($dias); 
											  } ?> 
                                         </select>	
                                       </div>
									   
										<label class="control-label col-lg-1">Recorrer:</label>
									   <div class="col-lg-2">
                                         <input type="number" name="dias_recorrer" id="dias_recorrer" value="<?php if ($row_tareas['dias_recorrer'] > 0) { echo $row_tareas['dias_recorrer'];} else { echo '0';} ?>" class="form-control"/>	
                                       </div>

									   	<label class="control-label col-lg-1">Por evento:</label>
										<div class="col-lg-2">
                                         <select class="form-control"  name="por_evento" required="required">
											   <option value="0"<?php if (!(strcmp($row_tareas['por_evento'], 0))) {echo "selected=\"selected\"";} ?>>No</option>
											   <option value="1"<?php if (!(strcmp($row_tareas['por_evento'], 1))) {echo "selected=\"selected\"";} ?>>Si</option>
                                         </select>	
                                       </div>

									</div>
									<!-- /basic text input -->
									                                  

                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Información Solicitada e instrucciones para el JRH:</label>
										<div class="col-lg-10">
											<textarea class="wysihtml5 wysihtml5-min form-control"  name="descripcion_larga" id="descripcion_larga" rows="4"><?php echo $row_tareas['descripcion_larga']; ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Imagen PPT:</label>
										<div class="col-lg-10">
											<input type="file" class="file-styled" name="foto" id="foto">
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2">Rangos de Calificación:</label>
										<div class="col-lg-3">Sobresaliente:
										<input type="number" name="IDsob" id="IDsob" min="1" max="150" value="<?php echo KT_escapeAttribute($row_tareas['IDsob']); ?>" placeholder="120" class="form-control" required="required"/>
                                       </div>

									   <div class="col-lg-3">Satisfactorio:
										<input type="number" name="IDsat" id="IDsat" min="1" max="150" value="<?php echo KT_escapeAttribute($row_tareas['IDsat']); ?>" placeholder="100"  class="form-control" required="required"/>
                                       </div>
									   
										<div class="col-lg-3">Deficiente:
										<input type="number" name="IDdef" id="IDdef" min="1" max="150" value="<?php echo KT_escapeAttribute($row_tareas['IDdef']); ?>" placeholder="50" class="form-control" required="required"/>
                                       </div>

									</div>
									<!-- /basic text input -->
                                        <div class="text-right">
                                    <div>
									<?php if(isset($_GET['IDtarea'])) { ?>
										<button type="submit"  class="btn btn-primary">Actualizar Objetivo</button>
                                        <input type="hidden" name="MM_update" value="form1">
										<input type="hidden" name="IDtarea" value="<?php echo $_GET['IDtarea']; ?>">
										<input type="hidden" name="anio" value="<?php echo $anio; ?>">
                                 <?php } else { ?>
										<button type="submit"  class="btn btn-primary">Agregar Objetivo</button>
                                        <input type="hidden" name="MM_insert" value="form1">
										<input type="hidden" name="anio" value="<?php echo $anio; ?>">
								 <?php } ?>
								 <button type="button" onClick="window.location.href='objetivos_d.php?IDtarea=<?php echo $IDtarea; ?>'" class="btn btn-default btn-icon">Regresar</button>
                                    </div>
                                  </div>
                                    
                                        
                              		</fieldset>
                               		</form>
								  </div>
								</div>

							</div>
							<!-- /task overview -->
							
							
							<?php if(isset($_GET['IDtarea'])) { ?>
							
							<!-- Task overview -->
							<div class="panel panel-flat">
							  <div class="panel-body">

								  <h4>Documentos</h4>
								  Agrega los formatos o documentos que deberán llenar los Jefes de RH.	</p>

									<div>
                                    	<div class="table-responsive content-group">
										<table class="table table-framed">
											<thead>
												<tr>
													<th>#</th>
													<th>Documento</th>
													<th>Fecha</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>
										<?php if($totalRows_files > 0) { ?>
								<?php do { ?>
												<tr>
													<td><?php echo $row_files['IDfile']; ?></td>
													<td><?php echo $row_files['file']; ?></td>
													<td>
									                	<div class="input-group input-group-transparent">
									                		<?php $fecha = date('d/m/Y', strtotime($row_files['fecha']));
															if($row_files['fecha'] > 0) { echo $fecha;} else {echo "-";}?>
									                	</div>
													</td>
													<td>
                                                    <a href="sed_rh_files/<?php echo $row_files['file']; ?>" class="btn btn-success">Descargar</a>
                                                    <button type="button" data-target="#modal_theme_danger<?php echo $row_files['IDavance']; ?>"  
                                                    data-toggle="modal" class="btn btn-danger">Borrar</button>
                                                    </td>
												</tr>
                                                
                                                      <!-- danger modal -->
                                                        <div id="modal_theme_danger<?php echo $row_files['IDavance']; ?>" class="modal fade" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger">
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    <h6 class="modal-title">Confirmación de Borrado</h6>
                                                                </div>
                                
                                                                <div class="modal-body">
                                                                    <p>¿Estas seguro que quieres borrar el Objetivo?</p>
                                                                </div>
                                
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                                 <a href="objetivos_d_borrar.php?IDfile=<?php echo $row_files['IDfile']; ?>&IDtarea=<?php echo $IDtarea; ?>" class="btn btn-danger" >Si borrar</a>
                                                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- /danger modal -->

			   				    <?php } while ($row_files = mysql_fetch_assoc($files)); ?>
									  <?php } else { ?>
                                      <tr><td colspan="4">Sin Documentos enviados.</td></tr>
							    <?php } ?>
												</tbody>
										</table>

				                  <p>&nbsp;</p>
								  <p><strong>Agregar documento:</strong></p>
                
                                         <?php echo $tNGs->getErrorMsg(); ?>
                                      <form method="post" id="form2" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" enctype="multipart/form-data">

                                      <!-- Basic text input -->
								  <div class="form-group">
										<div class="col-lg-9">
										<input type="file" name="file" id="file" value="<?php echo KT_escapeAttribute($row_rsztar_files['file']); ?>" class="file-styled" required="required"/>
										</div>
									</div>
									<!-- /basic text input -->
                                      
                                        <input type="hidden" name="IDtarea" id="IDtarea" value="<?php echo $IDtarea; ?>" />
                                        <input type="hidden" name="IDavance" id="IDavance" value="<?php echo $IDavance; ?>" />
                                        <input type="hidden" name="fecha" id="fecha" value="<?php echo $fecha; ?>" />
                                        <input type="hidden" name="IDusuario" id="IDusuario" value="<?php $IDusuario; ?>" />
                                        <?php echo $tNGs->getErrorMsg(); ?>
                                        <input type="hidden" name="IDmatriz" value="<?php echo $row_avances['IDmatriz']; ?>">

                                       <button type="submit"  name="KT_Insert1"  id="KT_Insert1" class="btn btn-primary">Agregar</button>

                                      </form>
                                      <p>&nbsp;</p>
									</div>
									</div>
								</div>

							</div>
							<!-- /task overview -->
							<?php } ?>
						</div>

						<div class="col-lg-4">

							<!-- Task details -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-files-empty position-left"></i>Detalles</h6>
								</div>

								<table class="table table-borderless table-xs content-group-sm">
									<tbody>
										<tr>
											<td><i class="icon-briefcase position-left"></i> Objetivo:</td>
											<td class="text-right"><span class="pull-right">
                                            <?php echo $row_tareas['descripcion'];?></span></td>
										</tr>
                                        <tr>
											<td><i class="icon-briefcase position-left"></i> Área:</td>
											<td class="text-right"><span class="pull-right"><?php echo $row_tareas['area_rh']; ?></span></td>
										</tr>
										<tr>
											<td><i class="icon-circles2 position-left"></i> Ponderación:</td>
											<td class="text-right"><?php echo $row_tareas['ponderacion']; ?>% </td>
									</tbody>
								</table>
							</div>
							<!-- /task details -->


							<!-- Task details -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-git-commit position-left"></i>Contacto Corporativo</h6>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="panel-body">
									<ul class="media-list">
										<li class="media">
											<div class="media-left">
                                            <a href="#" class="btn border-primary text-primary btn-icon btn-flat btn-sm btn-rounded">
                                            <i class="icon-git-pull-request"></i></a></div>
											<div class="media-body">
												Juan Antonio Cardenas
												<div class="media-annotation">jacardenas@sahuayo.mx</div>
												<div class="media-annotation">55 7901 0399</div>
											</div>
										</li>

									</ul>
								</div>
							</div>
							<!-- /revisions -->


						<?php if ($row_tareas['foto'] != '') { ?>
							<!-- Task details -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-file-presentation position-left"></i>Imagen PPT</h6>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="panel-body">
									<ul class="media-list">
										<li class="media">
										<a href="drhimg/<?php echo $row_tareas['foto'];?>" data-popup="lightbox"> <img src="drhimg/<?php echo $row_tareas['foto'];?>" alt="" class="img-rounded img-preview"></a>
												
										</li>

									</ul>
								</div>
							</div>
							<!-- /revisions -->
						<?php } ?>


						</div>
				  </div>
					<!-- /detailed task -->

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