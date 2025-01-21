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

$IDsindicato_seguimientos = $_GET['IDsindicato_seguimientos'];
mysql_select_db($database_vacantes, $vacantes);
$query_sindicato_seguimiento = "SELECT * FROM casos_sindicato_seguimientos WHERE IDsindicato_seguimientos = '$IDsindicato_seguimientos'";
mysql_query("SET NAMES 'utf8'");
$sindicato_seguimiento = mysql_query($query_sindicato_seguimiento, $vacantes) or die(mysql_error());
$row_sindicato_seguimiento = mysql_fetch_assoc($sindicato_seguimiento);
$totalRows_sindicato_seguimiento = mysql_num_rows($sindicato_seguimiento);

$fecha = date("Y-m-d"); // la fecha actual
$formatos_permitidos =  array('pdf', 'doc');
$fechapp = date("YmdHis"); // la fecha actual


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
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.</p>
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
										<label class="control-label col-lg-3">Matriz | Sucursal:</label>
										<div class="col-lg-9">
										<?php echo $row_sindicato['matriz']; ?> | <?php echo $row_sindicato['sucursal']; ?>
										</div>
									</div>
									<!-- /basic text input -->

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Área:</label>
										<div class="col-lg-9">
										<?php echo $row_sindicato['area']; ?>
										</div>
									</div>
									<!-- /basic text input -->
							
									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus:</label>
										<div class="col-lg-9">
										<?php if ($row_sindicato['asunto'] == 1 ) { echo "En Proceso"; } else { echo "Cerrado"; }?>
										</div>
									</div>
									<!-- /basic text input -->

						<legend class="text-semibold">Datos Seguimiento</legend>

															
									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha seguimiento:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" readonly="readonly" class="form-control" placeholder="Seleccione la fecha" name="fecha_reporte" id="fecha_reporte" value="<?php if ($row_sindicato_seguimiento['fecha_reporte'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_sindicato_seguimiento['fecha_reporte'])); }?>">
									</div>
								   </div>
                                  </div> 
									<!-- Fecha -->


									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Descripción del seguimiento: </label>
										<div class="col-lg-9">
                                          <textarea readonly="readonly" name="descripcion_seguimiento" rows="3" class="wysihtml5 wysihtml5-min form-control" id="descripcion_seguimiento" placeholder="Indique a detalle el seguimiento realizado."><?php if (isset($row_sindicato_seguimiento['descripcion_seguimiento'])) { echo $row_sindicato_seguimiento['descripcion_seguimiento']; } ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- /basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Documento:</label>
										<div class="col-lg-9">
                                        <?php if (isset($row_sindicato_seguimiento['file']) and $row_sindicato_seguimiento['file'] != ''){ ?><a href='<?php echo "SINDICATO/".$IDsindicato."/".$row_sindicato_seguimiento['file']; ?>' class="btn btn-info btn-icon" target="_blank">Descargar archivo</a><?php } ?></p>
										</div>
									</div>
									<!-- /basic text input -->


										<button type="button" onClick="window.location.href='casos_sindicato_cerrados.php'" class="btn btn-default btn-icon">Regresar</button>

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