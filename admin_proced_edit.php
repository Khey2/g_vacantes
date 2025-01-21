<?php require_once('Connections/vacantes.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php');

// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Load the KT_back class
require_once('includes/nxt/KT_back.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

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


mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$IDperiodovar = $row_variables['IDperiodo'];
$fechac = date("dmYHis"); // la fecha actual
$formatos_permitidos =  array("pdf, jpg, png, jpeg, zip, jpeg, doc, docx, mp3, mov, mp4, ppt, pptx, xls, xlsx, rar");

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'"); 
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDdireccion = $_GET['IDdireccion'];
$IDDarea = $_GET['IDDarea'];
$IDsubarea = $_GET['IDsubarea'];


if(isset($_GET['IDdocumento'])) {
$IDdocumento = $_GET['IDdocumento'];
mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT proced_documentos.tags, proced_documentos.IDdocumento, proced_documentos.IDDarea, proced_documentos.IDdireccion, proced_documentos.IDsubarea, proced_documentos.IDtipo, proced_documentos.IDvisible, proced_documentos.documento, proced_documentos.descripcion, proced_documentos.file,  proced_documentos.version, proced_documentos.anio, proced_areas.area, proced_subareas.subarea, proced_direcciones.direccion  FROM proced_documentos LEFT JOIN proced_direcciones ON proced_documentos.IDdireccion = proced_direcciones.IDdireccion LEFT JOIN proced_areas ON proced_documentos.IDDarea = proced_areas.IDDarea LEFT JOIN proced_subareas ON proced_areas.IDDarea = proced_subareas.IDDarea WHERE IDdocumento = $IDdocumento";
mysql_query("SET NAMES 'utf8'");
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);
if ($row_resultados['file'] != '') { $archivo = $row_resultados['file'];} else { $archivo = '';} 
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'"); 
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_areas = "SELECT proced_areas.area, proced_direcciones.direccion, proced_areas.IDDarea, proced_areas.IDdireccion FROM proced_areas INNER JOIN proced_direcciones ON proced_areas.IDdireccion = proced_direcciones.IDdireccion ORDER BY direccion, area ASC";
mysql_query("SET NAMES 'utf8'"); 
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);
$totalRows_areas = mysql_num_rows($areas);

mysql_select_db($database_vacantes, $vacantes);
$query_direccions = "SELECT * FROM proced_direcciones";
mysql_query("SET NAMES 'utf8'"); 
$direccions = mysql_query($query_direccions, $vacantes) or die(mysql_error());
$row_direccions = mysql_fetch_assoc($direccions);
$totalRows_direccions = mysql_num_rows($direccions);

mysql_select_db($database_vacantes, $vacantes);
$query_subareas = "SELECT proced_subareas.subarea, proced_subareas.IDDarea, proced_subareas.IDsubarea, proced_areas.area FROM proced_subareas INNER JOIN proced_areas ON proced_subareas.IDDarea = proced_areas.IDDarea ORDER BY area, subarea ASC";
mysql_query("SET NAMES 'utf8'"); 
$subareas = mysql_query($query_subareas, $vacantes) or die(mysql_error());
$row_subareas = mysql_fetch_assoc($subareas);
$totalRows_subareas = mysql_num_rows($subareas);

mysql_select_db($database_vacantes, $vacantes);
$query_tipos = "SELECT * FROM proced_tipos";
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if(isset($_GET['IDdocumento'])) {
	$IDdireccion = $_GET['IDdireccion'];
	$IDDarea = $_GET['IDDarea'];
	$IDsubarea = $_GET['IDsubarea'];
	$IDdocumento = $_GET['IDdocumento'];	
}	


if(isset($_GET['IDDarea']) AND $_GET['IDDarea'] > 0){
	$IDDarea = $_GET['IDDarea'];
	} else {
	$IDDarea = '0';
	}
if(isset($_GET['IDsubarea']) AND $_GET['IDsubarea'] > 0){
	$IDsubarea = $_GET['IDsubarea'];
	} else {
	$IDsubarea = '0';
	}

$ubicacion = 'proced/'.$IDdireccion."/".$IDDarea."/".$IDsubarea;


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$IDdireccion = $_POST['IDdireccion'];
	$IDDarea = $_POST['IDDarea'];
	$IDsubarea = $_POST['IDsubarea'];
	$ubicacion = 'proced/'.$IDdireccion."/".$IDDarea."/".$IDsubarea;
		
	$fecha1a = $_POST['anio']; 
	$fecha1b = explode("/",$fecha1a);
	$lafecha = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

	if (!file_exists($ubicacion)) {mkdir($ubicacion, 0777, true);}
		
	$name=$_FILES['file']['name'];
	$size=$_FILES['file']['size'];
	$type=$_FILES['file']['type'];
	$temp=$_FILES['file']['tmp_name'];
	$extension = pathinfo($name, PATHINFO_EXTENSION);
	if(!in_array($extension, $formatos_permitidos) AND isset($_POST['file'])) {
	header('Location: admin_proced_edit.php?info=9');
	exit;
	} 

	$name_new = $fechac."_".$IDdireccion."_".$IDDarea.".".$extension;
	$targetPath = 'proced/'.$IDdireccion."/".$IDDarea."/".$IDsubarea."/".$name_new;
	move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
	
	$insertSQL = sprintf("INSERT INTO proced_documentos (IDDarea, IDdireccion, IDsubarea, IDtipo, IDvisible, documento, descripcion, file, version, anio, tags) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
						   GetSQLValueString($_POST['IDDarea'], "text"),
						   GetSQLValueString($_POST['IDdireccion'], "text"),
						   GetSQLValueString($_POST['IDsubarea'], "text"),
						   GetSQLValueString($_POST['IDtipo'], "text"),
						   GetSQLValueString($_POST['IDvisible'], "text"),
						   GetSQLValueString($_POST['documento'], "text"),
						   GetSQLValueString($_POST['descripcion'], "text"),
						   GetSQLValueString($name_new, "text"),
						   GetSQLValueString($_POST['version'], "text"),
						   GetSQLValueString($lafecha, "text"),
						   GetSQLValueString($_POST['tags'], "text"));
	
	 mysql_select_db($database_vacantes, $vacantes);
	 $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
	
	 $last_id =  mysql_insert_id();
	 header("Location: admin_proced.php?IDdocumento=$IDdocumento&IDdireccion=$IDdireccion&IDDarea=$IDDarea&IDsubarea=$IDsubarea&info=2");
}



	
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$IDdocumento = $_POST['IDdocumento'];
	$IDdireccion = $_POST['IDdireccion'];
	$IDDarea = $_POST['IDDarea'];
	$IDsubarea = $_POST['IDsubarea'];
	$ubicacion = 'proced/'.$IDdireccion."/".$IDDarea."/".$IDsubarea;

	$fecha1a = $_POST['anio']; 
	$fecha1b = explode("/",$fecha1a);
	$lafecha = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];


	if (!file_exists($ubicacion)) {mkdir($ubicacion, 0777, true);}
		
	$name=$_FILES['file']['name'];
	$size=$_FILES['file']['size'];
	$type=$_FILES['file']['type'];
	$temp=$_FILES['file']['tmp_name'];
	$extension = pathinfo($name, PATHINFO_EXTENSION);
	if(!in_array($extension, $formatos_permitidos) AND isset($_POST['file'])) {
	header('Location: admin_proced_edit.php?info=9');
	exit;
	} 

	if ($_FILES['file']['size'] > 0) {
		$name_new = $fechac."_".$IDdireccion."_".$IDDarea.".".$extension;
		$targetPath = 'proced/'.$IDdireccion."/".$IDDarea."/".$IDsubarea."/".$name_new;
		move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
	} else {
		$name_new = $row_resultados['file']; 
	}

	$updateSQL = sprintf("UPDATE proced_documentos SET IDDarea=%s, IDdireccion=%s, IDsubarea=%s, IDtipo=%s, IDvisible=%s, documento=%s, descripcion=%s, file=%s, version=%s, anio=%s, tags=%s WHERE IDdocumento = $IDdocumento",
	GetSQLValueString($_POST['IDDarea'], "text"),
	GetSQLValueString($_POST['IDdireccion'], "text"),
	GetSQLValueString($_POST['IDsubarea'], "text"),
	GetSQLValueString($_POST['IDtipo'], "text"),
	GetSQLValueString($_POST['IDvisible'], "text"),
	GetSQLValueString($_POST['documento'], "text"),
	GetSQLValueString($_POST['descripcion'], "text"),
	GetSQLValueString($name_new, "text"),
	GetSQLValueString($_POST['version'], "text"),
	GetSQLValueString($lafecha, "text"),
	GetSQLValueString($_POST['tags'], "text"));

	$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
	
	$last_id =  mysql_insert_id();
	header("Location: admin_proced.php?IDdocumento=$IDdocumento&IDdireccion=$IDdireccion&IDDarea=$IDDarea&IDsubarea=$IDsubarea&info=2");
}
	
mysql_select_db($database_vacantes, $vacantes);
$query_direcciones = "SELECT * FROM proced_direcciones WHERE IDdireccion = $IDdireccion";
mysql_query("SET NAMES 'utf8'"); 
$direcciones = mysql_query($query_direcciones, $vacantes) or die(mysql_error());
$row_direcciones = mysql_fetch_assoc($direcciones);
$totalRows_direcciones = mysql_num_rows($direcciones);



// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] != "")) {
  
  $borrado = $_GET['IDdocumento'];
  $IDdocumento = $_GET['IDdocumento'];
  $deleteSQL = "DELETE FROM proced_documentos WHERE IDdocumento ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: admin_proced.php?IDdireccion=".$IDdireccion."&IDDarea=".$IDDarea."&IDsubarea=".$IDsubarea."&info=3");
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
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Ingresa los datos solicitados.</p>
                    
                    <?php echo $tNGs->getErrorMsg(); ?>
					
                    <div>

					<div>

		<?php if (!isset($_GET['IDdocumento'])) { ?>

						<form method="post" id="form1" action="admin_proced_edit.php?IDdireccion=<?php echo $IDdireccion; ?>&IDDarea=<?php echo $IDDarea; ?>&IDsubarea=<?php echo $IDsubarea; ?>" enctype="multipart/form-data" class="form-horizontal form-validate-jquery">
					
						<fieldset class="content-group">
                              
							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Dirección:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									  <select name="IDdireccion" id="IDdireccion" class="form-control" required="required" >
										  <option value="">Seleccione una opción</option> 
											<?php  do { ?>
											<option value="<?php echo $row_direccions['IDdireccion']?>"<?php if (!(strcmp($row_direccions['IDdireccion'], $IDdireccion)))
											{echo "SELECTED";} ?>><?php echo $row_direccions['direccion']?></option>
																	<?php
								  } while ($row_direccions = mysql_fetch_assoc($direccions));
									$rows = mysql_num_rows($direccions);
									if($rows > 0) {
										mysql_data_seek($direccions, 0);
										$row_direccions = mysql_fetch_assoc($direccions);
									} ?>
									  </select>
								  </div>
							  </div>
							  <!-- /basic select -->
						
							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Área:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									  <select name="IDDarea" id="IDDarea" class="form-control" required="required" >
										  <option value="">Seleccione una opción</option> 
											<?php  do { ?>
										   <option value="<?php echo $row_areas['IDDarea']?>"<?php if (!(strcmp($row_areas['IDDarea'], $IDDarea))) 
										   {echo "SELECTED";} ?>><?php echo $row_areas['direccion']?> - <?php echo $row_areas['area']?></option>
							<?php } while ($row_areas = mysql_fetch_assoc($areas));
									$rows = mysql_num_rows($areas);
									if($rows > 0) {
										mysql_data_seek($areas, 0);
										$row_areas = mysql_fetch_assoc($areas);
									} ?>
									  </select>
								  </div>
							  </div>
							  <!-- /basic select -->
						
							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Subárea:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									  <select name="IDsubarea" id="IDsubarea" class="form-control" required="required" >
										  <option value="">Seleccione una opción</option> 
											<?php  do { ?>
										   <option value="<?php echo $row_subareas['IDsubarea']?>"<?php if (!(strcmp($row_subareas['IDsubarea'], $IDsubarea))) 
										   {echo "SELECTED";} ?>><?php echo $row_subareas['area']?> - <?php echo $row_subareas['subarea']?></option>
																	<?php
								  } while ($row_subareas = mysql_fetch_assoc($subareas));
									$rows = mysql_num_rows($subareas);
									if($rows > 0) {
										mysql_data_seek($subareas, 0);
										$row_subareas = mysql_fetch_assoc($subareas);
									} ?>
								  </select>
								  </div>
							  </div>
							  <!-- /basic select -->
						
						
							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Tipo de documento:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									  <select name="IDtipo" id="IDtipo" class="form-control" required="required">
										  <option value="">Seleccione una opción</option> 
											<?php  do { ?>
										  <option value="<?php echo $row_tipos['IDtipo']?>"><?php echo $row_tipos['tipo']?></option>
							  <?php } while ($row_tipos = mysql_fetch_assoc($tipos));
								$rows = mysql_num_rows($tipos);
								if($rows > 0) {
									mysql_data_seek($tipos, 0);
									$row_tipos = mysql_fetch_assoc($tipos);
								} ?>
										 </select>
								  </div>
							  </div>
							  <!-- /basic select -->
						
						
							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Visible:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									  <select name="IDvisible" id="IDvisible"  class="form-control" required="required">
										<option value="1">Si</option>
										<option value="0">No</option>
										 </select>
								  </div>
							  </div>
							  <!-- /basic select -->



							   <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Nombre del Documento:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
				  <input type="text" name="documento" id="documento" class="form-control" placeholder="Indica el nombre del documento" required="required">
								  </div>
							  </div>
							  <!-- /basic text input -->
						
							   <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Descripción:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									<textarea required="required" name="descripcion" rows="3" class="form-control" id="descripcion" placeholder="Indica la descripción del documento"></textarea>
								  </div>
							  </div>
							  <!-- /basic text input -->

							   <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Etiquetas de búsqueda<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
				  <input type="text" name="tags" id="tags" class="form-control" placeholder="Etiquetas separadas por coma" required="required">
								  </div>
							  </div>
							  <!-- /basic text input -->

					  <!-- Basic text input -->
							<div class="form-group">
								  <label class="control-label col-lg-3">Archivo:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
				  <input type="file" name="file" id="file" class="file-styled" placeholder="Seleccione Documento" 
				  <?php if (!isset($_GET['IDdocumento'])){ echo "required='required'";} ?> >
								  <?php if (isset($_GET['IDdocumento'])){ echo "<span><a href=".$ubicacion."/".$archivo.">Descargar archivo</a></span>"; } ?>
								  </div>
							  </div>
							  <!-- /basic text input -->
						

							   <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Versión:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
				  <input type="text" name="version" id="version" class="form-control" placeholder="Indica la versión del documento" required="required">
								  </div>
							  </div>
							  <!-- /basic text input -->


							  <!-- Fecha -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Fecha del Documento:<span class="text-danger">*</span></label>
							  <div class="col-lg-9">
							  <div class="input-group">
							  <span class="input-group-addon"><i class="icon-calendar22"></i></span>
								  <input type="text" class="form-control  daterange-single" name="anio" id="anio" required="required">
							  </div>
							 </div>
							</div> 
							  <!-- Fecha -->
					
		<?php } else { ?>

						<form method="post" id="form1" action="admin_proced_edit.php?IDdocumento=<?php echo $IDdocumento; ?>&IDdireccion=<?php echo $IDdireccion; ?>&IDDarea=<?php echo $IDDarea; ?>&IDsubarea=<?php echo $IDsubarea; ?>" enctype="multipart/form-data" class="form-horizontal form-validate-jquery">

								<fieldset class="content-group">
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Dirección:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDdireccion" id="IDdireccion" class="form-control" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_direccions['IDdireccion']?>"<?php if (!(strcmp($row_direccions['IDdireccion'], $IDdireccion)))
												  {echo "SELECTED";} ?>><?php echo $row_direccions['direccion']?></option>
																		  <?php
										} while ($row_direccions = mysql_fetch_assoc($direccions));
										  $rows = mysql_num_rows($direccions);
										  if($rows > 0) {
											  mysql_data_seek($direccions, 0);
											  $row_direccions = mysql_fetch_assoc($direccions);
										  } ?>
                               			 </select>
										</div>
									</div>
									<!-- /basic select -->
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Área:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDDarea" id="IDDarea" class="form-control" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												 <option value="<?php echo $row_areas['IDDarea']?>"<?php if (!(strcmp($row_areas['IDDarea'], $IDDarea))) 
												 {echo "SELECTED";} ?>><?php echo $row_areas['direccion']?> - <?php echo $row_areas['area']?></option>
                                  <?php } while ($row_areas = mysql_fetch_assoc($areas));
										  $rows = mysql_num_rows($areas);
										  if($rows > 0) {
											  mysql_data_seek($areas, 0);
											  $row_areas = mysql_fetch_assoc($areas);
										  } ?>
                                			</select>
										</div>
									</div>
									<!-- /basic select -->
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Subárea:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsubarea" id="IDsubarea" class="form-control" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												 <option value="<?php echo $row_subareas['IDsubarea']?>"<?php if (!(strcmp($row_subareas['IDsubarea'], $IDsubarea))) 
												 {echo "SELECTED";} ?>><?php echo $row_subareas['area']?> - <?php echo $row_subareas['subarea']?></option>
																		  <?php
										} while ($row_subareas = mysql_fetch_assoc($subareas));
										  $rows = mysql_num_rows($subareas);
										  if($rows > 0) {
											  mysql_data_seek($subareas, 0);
											  $row_subareas = mysql_fetch_assoc($subareas);
										  } ?>
                                		</select>
										</div>
									</div>
									<!-- /basic select -->
                              
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de documento:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtipo" id="IDtipo" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												<option value="<?php echo $row_tipos['IDtipo']?>"<?php if (!(strcmp($row_tipos['IDtipo'], $row_resultados['IDtipo']))) 
												{echo "SELECTED";} ?>><?php echo $row_tipos['tipo']?></option>
									<?php } while ($row_tipos = mysql_fetch_assoc($tipos));
									  $rows = mysql_num_rows($tipos);
									  if($rows > 0) {
										  mysql_data_seek($tipos, 0);
										  $row_tipos = mysql_fetch_assoc($tipos);
									  } ?>
                               				</select>
										</div>
									</div>
									<!-- /basic select -->
                              
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Visible:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDvisible" id="IDvisible"  class="form-control" required="required">
                                              <option value="1" <?php if (!(strcmp(1, KT_escapeAttribute($row_resultados['IDvisible'])))) {echo "SELECTED";} ?>>Si</option>
                                              <option value="0" <?php if (!(strcmp(0, KT_escapeAttribute($row_resultados['IDvisible'])))) {echo "SELECTED";} ?>>No</option>
                               				</select>
										</div>
									</div>
									<!-- /basic select -->



                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre del Documento:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="documento" id="documento" class="form-control" placeholder="Indica el nombre del documento" value="<?php echo KT_escapeAttribute($row_resultados['documento']); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                              
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Descripción:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                                          <textarea required="required" name="descripcion" rows="3" class="form-control" id="descripcion" placeholder="Indica la descripción del documento"><?php echo KT_escapeAttribute($row_resultados['descripcion']); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Etiquetas de búsqueda<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="tags" id="tags" class="form-control" placeholder="Etiquetas separadas por coma" value="<?php echo KT_escapeAttribute($row_resultados['tags']); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                            <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Archivo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="file" name="file" id="file" class="file-styled" placeholder="Seleccione Documento" 
						<?php if (!isset($_GET['IDdocumento'])){ echo "required='required'";} ?> >
                                        <?php if (isset($_GET['IDdocumento'])){ echo "<span><a href=".$ubicacion."/".$archivo.">Descargar archivo</a></span>"; } ?>
										</div>
									</div>
									<!-- /basic text input -->
                              

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Versión:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="version" id="version" class="form-control" placeholder="Indica la versión del documento" value="<?php echo KT_escapeAttribute($row_resultados['version']); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha del Documento:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="anio" id="anio" value="<?php if ($row_resultados['anio'] == "") { echo "";} else  { echo KT_formatDate($row_resultados['anio']); }?>" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->


		<?php } ?>

 						<div class="text-right">
                            <div>
                            
                              <?php  if (!isset($_GET['IDdocumento'])) { ?>
									<button type="submit"  class="btn btn-success">Agregar Documento</button>
                                    <input type="hidden" name="MM_insert" value="form1">
                                <?php } else { ?>
									<button type="submit"  class="btn btn-primary">Actualizar</button>
                                    <input type="hidden" name="MM_update" value="form1">
									<input type="hidden" name="IDdireccion" value="<?php echo $IDdireccion; ?>" />
									<input type="hidden" name="IDDarea" value="<?php echo $IDDarea; ?>" />
									<input type="hidden" name="IDsubarea" value="<?php echo $IDsubarea; ?>" />
									<input type="hidden" name="IDdocumento" value="<?php echo $IDdocumento; ?>" />
                                <?php } ?>
                              <a class="btn btn-default" href="admin_proced_directorio.php">Regresar</a>
                            </div>
                          </div>
                       </fieldset>
                        </form>


                      </div>
                    </div>
                    <p>&nbsp;</p>
                    
					</div>

<!-- /Contenido -->

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