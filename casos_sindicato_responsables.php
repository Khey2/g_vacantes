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
$IDmatrizes = $row_usuario['IDmatrizes'];
$IDmatriz = $row_usuario['IDmatriz'];


$IDsindicato = $_GET['IDsindicato'];
mysql_select_db($database_vacantes, $vacantes);
$query_sindicato = "SELECT vac_sucursal.sucursal, vac_matriz.matriz, vac_areas.area, casos_sindicato.*, casos_sindicato_seguimientos.* FROM casos_sindicato LEFT JOIN casos_sindicato_seguimientos ON  casos_sindicato.IDsindicato = casos_sindicato_seguimientos.IDsindicato LEFT JOIN vac_matriz ON casos_sindicato.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_sucursal ON casos_sindicato.IDsucursal = vac_sucursal.IDsucursal LEFT JOIN vac_areas ON casos_sindicato.IDarea = vac_areas.IDarea WHERE casos_sindicato.IDsindicato = $IDsindicato";
mysql_query("SET NAMES 'utf8'");
$sindicato = mysql_query($query_sindicato, $vacantes) or die(mysql_error());
$row_sindicato = mysql_fetch_assoc($sindicato);
$totalRows_sindicato = mysql_num_rows($sindicato);
$IDmatrizS = $row_sindicato['IDmatriz'];
$IDarea = $row_sindicato['IDarea'];
$IDsucursal = $row_sindicato['IDsucursal'];

$query_casos_responsable = "SELECT casos_responsables.IDresponsable, casos_responsables.IDtipo, vac_usuarios.IDusuario,  vac_usuarios.usuario_correo,  vac_usuarios.usuario_nombre,  vac_usuarios.usuario_parterno,  vac_usuarios.usuario_materno,  vac_usuarios.IDusuario_puesto, vac_puestos.denominacion FROM vac_usuarios INNER JOIN casos_responsables ON  vac_usuarios.IDusuario = casos_responsables.IDusuario INNER JOIN vac_puestos ON  vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto WHERE IDsindicato = $IDsindicato";
$casos_responsable = mysql_query($query_casos_responsable, $vacantes) or die(mysql_error());
$row_casos_responsable = mysql_fetch_assoc($casos_responsable);
$totalRows_casos_responsable = mysql_num_rows($casos_responsable);

$query_responsables_directos = "SELECT * FROM casos_responsables WHERE IDsindicato = $IDsindicato AND IDTipo = 1";
$responsables_directos = mysql_query($query_responsables_directos, $vacantes) or die(mysql_error());
$row_responsables_directos = mysql_fetch_assoc($responsables_directos);
$totalRows_responsables_directos = mysql_num_rows($responsables_directos);

$fecha = date("Y-m-d"); // la fecha actual
$formatos_permitidos =  array('pdf', 'doc');
$fechapp = date("YmdHis"); // la fecha actual

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


//primero agregamos al que captura
if ((isset($_GET["Primero"])) && ($_GET["Primero"] == "1")) {
	
$insertSQL = "INSERT INTO casos_responsables (IDusuario, IDtipo, IDsindicato) VALUES ($IDusuario, '2', $IDsindicato)";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
header("Location: casos_sindicato_responsables.php?IDsindicato=$IDsindicato&info=1");
 }

// se agrega nuevo responsable
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {

$IDusuario = $_POST['IDusuario'];
$IDsindicato = $_POST['IDsindicato'];	
$IDtipo = $_POST["IDtipo"];	

// vemos si ya está lo omitimos
$query_repetidos = "SELECT * FROM casos_responsables WHERE IDusuario = $IDusuario AND IDsindicato = $IDsindicato";
$repetidos = mysql_query($query_repetidos, $vacantes) or die(mysql_error());
$row_repetidos = mysql_fetch_assoc($repetidos);
$totalRows_repetidos = mysql_num_rows($repetidos); 
	
if ($totalRows_repetidos == 0) {	

$insertSQL = sprintf("INSERT INTO casos_responsables (IDusuario, IDtipo, IDsindicato) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['IDusuario'], "int"),
                       GetSQLValueString($_POST['IDtipo'], "int"),
                       GetSQLValueString($_POST['IDsindicato'], "int"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
 
}
 header("Location: casos_sindicato_responsables.php?IDsindicato=$IDsindicato&info=1");
}
 
 
// masivo por área
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
	
$IDarea = $_POST["IDarea"]; 
$IDsindicato = $_POST['IDsindicato'];	
$IDtipo = $_POST["IDtipo"];	

//seleccionamos a los usuarios del área
$query_areasS = "SELECT * FROM casos_temas_responsables WHERE IDarea = $IDarea";
$areasS = mysql_query($query_areasS, $vacantes) or die(mysql_error());
$row_areasS = mysql_fetch_assoc($areasS);
$totalRows_areasS = mysql_num_rows($areasS); 

if ($totalRows_areasS > 0) {	
do {
$IDusuario = $row_areasS['IDusuario'];

// vemos si ya está lo omitimos
$query_repetidos = "SELECT * FROM casos_responsables WHERE IDusuario = $IDusuario AND IDsindicato = $IDsindicato";
$repetidos = mysql_query($query_repetidos, $vacantes) or die(mysql_error());
$row_repetidos = mysql_fetch_assoc($repetidos);
$totalRows_repetidos = mysql_num_rows($repetidos); 

if ($totalRows_repetidos == 0) {

$insertSQL = "INSERT INTO casos_responsables (IDusuario, IDtipo, IDsindicato) VALUES ($IDusuario, $IDtipo, $IDsindicato)";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

}
} while ($row_areasS = mysql_fetch_assoc($areasS));	
}
	
 header("Location: casos_sindicato_responsables.php?IDsindicato=$IDsindicato&info=1");
}

// asignar responsable
if ((isset($_GET['responsable'])) && ($_GET['responsable'] == 1)) {
  
  $borrado = $_GET['IDresponsable'];
  $deleteSQL = "UPDATE casos_responsables SET IDtipo = 1 WHERE IDresponsable ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: casos_sindicato_responsables.php?IDsindicato=$IDsindicato&info=2");
}



// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
  $borrado = $_GET['IDresponsable'];
  $deleteSQL = "DELETE FROM casos_responsables WHERE IDresponsable ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: casos_sindicato_responsables.php?IDsindicato=$IDsindicato&info=4");
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_responsabls = "SELECT vac_puestos.denominacion, vac_matriz.matriz, vac_usuarios.usuario_correo, vac_usuarios.IDusuario, vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno FROM vac_usuarios INNER JOIN vac_puestos ON vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto INNER JOIN vac_matriz ON vac_usuarios.IDmatriz = vac_matriz.IDmatriz WHERE vac_matriz.IDmatriz IN ($IDmatriz) OR (vac_matriz.IDmatriz = 7 AND vac_usuarios.IDarea = 10) ORDER BY vac_usuarios.usuario_parterno ASC";
$responsabls = mysql_query($query_responsabls, $vacantes) or die(mysql_error());
$row_responsabls = mysql_fetch_assoc($responsabls);
$totalRows_responsabls = mysql_num_rows($responsabls);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes) ORDER BY matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);


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
                        <?php if((isset($_GET['info']) && $_GET['info'] == 1)) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Registro agregado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
						
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && $_GET['info'] == 2)) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Registro actualizado correctamente.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && $_GET['info'] == 4)) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el registro.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($totalRows_responsables_directos == 0) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Es necesario asignar al menos a un Responsable para cada caso.
					    </div>
						
                        <?php } else  { ?>
						
					    <div class="alert bg-info-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Da clic en "Terminar y enviar notificación" para enviar un correo a los involucrados.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Responsables</h5>
						</div>

					<div class="panel-body">
					
									<ul>
									<li>Da clic en <i>"Responsable"</i> para asignar al empleado como Responsable.</li>
									<li>Cuando termines de asignar responsables, da clic en <i>"Terminar y enviar notificación"</i> para enviar un correo a los involucrados.</li>
									</ul>
					
							
										<strong>Asunto:</strong> <?php echo $row_sindicato['asunto']; ?><br />
										<strong>Matriz | Sucursal:</strong>  <?php echo $row_sindicato['matriz']." | ".$row_sindicato['sucursal']; ?><br />
										<strong>Fecha de solicitud:</strong>  <?php echo date( 'd/m/Y' , strtotime($row_sindicato['fecha_inicio'])); ?><br />
										<strong>Fecha de atención esperada:</strong>  <?php echo date( 'd/m/Y' , strtotime($row_sindicato['fecha_esperada'])); ?><br />
									<p>&nbsp;</p>
							
							
                            		<button type="button" data-target="#modal_theme_add" data-toggle="modal" class="btn btn-success btn-icon">Agregar responsable</button>
                            		<button type="button" data-target="#modal_theme_mail" data-toggle="modal" class="btn btn-info btn-icon">Terminar y enviar notificación</button>
									<button type="button" onClick="window.location.href='casos_sindicato.php'" class="btn btn-default btn-icon">Regresar</button>
									<p>&nbsp;</p>

					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary">
							  <th>No.</th>
							  <th>Nombre</th>
							  <th>Puesto</th>
							  <th>Tipo</th>
							  <th>Acciones</th>
                        </tr>
						</thead>
						<tbody>
						<?php $numer = 1; do { ?>
						<tr>
							<td><?php echo $numer; ?></td>
							<td><?php echo $row_casos_responsable['usuario_parterno']." ".$row_casos_responsable['usuario_materno']." ".$row_casos_responsable['usuario_nombre']; ?></td>
							<td><?php echo $row_casos_responsable['denominacion']; ?></td>
							<td><?php    if ($row_casos_responsable['IDtipo'] == 1) { echo "Responsable <i class='icon icon-user-check text-success'></i>";}
									else if ($row_casos_responsable['IDtipo'] == 2) { echo "Para Seguimiento";}
									else if ($row_casos_responsable['IDtipo'] == 3) { echo "Para Conocimiento";}
									else { echo "Sin definir";} ?></td>
							<td><button type="button" data-target="#modal_theme_danger<?php echo $row_casos_responsable['IDresponsable']; ?>" data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
							<button type="button" data-target="#modal_theme_danger2<?php echo $row_casos_responsable['IDresponsable']; ?>" data-toggle="modal" class="btn btn-primary btn-icon">Responsable</button></td>
						</tr>			

					<!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_casos_responsable['IDresponsable']; ?>" class="modal fade" tabindex="-1">
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
                                    <a class="btn btn-danger" href="casos_sindicato_responsables.php?IDresponsable=<?php echo $row_casos_responsable['IDresponsable']; ?>&IDsindicato=<?php echo $IDsindicato ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- danger modal -->
					

					<!-- danger modal -->
					<div id="modal_theme_danger2<?php echo $row_casos_responsable['IDresponsable']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-primary">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Asingación</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres asignar a <b><?php echo $row_casos_responsable['usuario_parterno']." ".$row_casos_responsable['usuario_materno']." ".$row_casos_responsable['usuario_nombre']; ?></b> como Responsable?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-primary" href="casos_sindicato_responsables.php?IDresponsable=<?php echo $row_casos_responsable['IDresponsable']; ?>&IDsindicato=<?php echo $IDsindicato ?>&responsable=1">Si asignar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- danger modal -->
					

						<?php  $numer = $numer + 1; } 
						while ($row_casos_responsable = mysql_fetch_assoc($casos_responsable)); ?>							
						</tbody>							
						</table>							                            



					<!-- danger modal -->
					<div id="modal_theme_mail" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-info">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de envio de notificación</h6>
								</div>

								<div class="modal-body">
									<p>Se enviará un correo con los detalles del caso a todos los empleados involucrados. ¿estas seguro?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-info" href="casos_sindicato_responsables_mail.php?IDsindicato=<?php echo $IDsindicato ?>">Si enviar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- danger modal -->



					<!-- danger modal -->
					<div id="modal_theme_add" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-success">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Agregar responsable</h6>
								</div>
								
								<form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

								<div class="modal-body">
									
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Responsable:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDusuario" id="IDusuario" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_responsabls['IDusuario']?>"><?php echo $row_responsabls['usuario_parterno']." ". $row_responsabls['usuario_materno']." ".$row_responsabls['usuario_nombre']." (".$row_responsabls['denominacion']." - ".$row_responsabls['matriz'].") "?></option>
												  <?php
												 } while ($row_responsabls = mysql_fetch_assoc($responsabls));
												   $rows = mysql_num_rows($responsabls);
												   if($rows > 0) {
												   mysql_data_seek($responsabls, 0);
												   $row_responsabls = mysql_fetch_assoc($responsabls);
												 } ?>
											</select>
											</div>
											
											<p>&nbsp;</p>
											
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtipo" id="IDtipo" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												<option value="1">Responsable</option> 
												<option value="2">Para Seguimiento</option> 
											</select>
										</div>
									</div>
									<!-- /basic select -->

											
									</div>
									<!-- /basic select -->
									
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <button type="submit"  class="btn btn-success">Agregar</button>
                                    <input type="hidden" name="MM_insert" value="form1">
                                    <input type="hidden" name="IDsindicato" value="<?php echo $IDsindicato; ?>">
								</div>
							</form>

							</div>
						</div>
					</div>
					<!-- danger modal -->






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