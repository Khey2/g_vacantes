<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
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
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 

if (isset($_GET['IDperiodo'])) {$IDperiodo = $_GET['IDperiodo'];} 
elseif (isset($_SESSION['IDperiodo'])) {$IDperiodo = $_SESSION['IDperiodo'];} 
else {$IDperiodo = $row_variables['IDperiodo'];}

$_SESSION['IDperiodo'] = $IDperiodo;
$_SESSION['IDindirectos'] = 0;

if (isset($_GET['IDindirectos']) AND $_GET['IDindirectos'] == 1) {$_SESSION['IDindirectos'] = 1;} 

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }

$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$IDllave = $row_usuario['IDllave'];
$IDpuesto = $row_usuario['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_evaluador = "SELECT prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.fecha_alta, prod_activos.IDllave, vac_areas.area, vac_matriz.matriz FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz  WHERE prod_activos.IDempleado = '$el_usuario'";
mysql_query("SET NAMES 'utf8'");
$evaluador = mysql_query($query_evaluador, $vacantes) or die(mysql_error());
$row_evaluador = mysql_fetch_assoc($evaluador);
$totalRows_evaluador = mysql_num_rows($evaluador);

//datos del evaluado
$_nombre = $row_evaluador['emp_nombre'] . " " . $row_evaluador['emp_paterno'] . " " . $row_evaluador['emp_materno'];
$_puesto = $row_evaluador['denominacion'];
$_sucursal = $row_evaluador['matriz'];
$_area = $row_evaluador['area'];
$_fecha_ingreso = $row_evaluador['fecha_alta'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

$el_usuario = $row_usuario['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_evaluado = "SELECT prod_activos.IDaplica_SED, prod_activos.IDempleadoj, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.fecha_alta, prod_activos.IDllave, vac_areas.area, vac_matriz.matriz FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz  WHERE prod_activos.IDempleado = '$el_usuario'";
mysql_query("SET NAMES 'utf8'");
$evaluado = mysql_query($query_evaluado, $vacantes) or die(mysql_error());
$row_evaluado = mysql_fetch_assoc($evaluado);
$totalRows_evaluado = mysql_num_rows($evaluado);
$IDempleadoj = $row_evaluado['IDempleadoj']; 

mysql_select_db($database_vacantes, $vacantes);
$query_boss = "SELECT * FROM prod_activos WHERE prod_activos.IDempleado = '$IDempleadoj'";
mysql_query("SET NAMES 'utf8'");
$boss = mysql_query($query_boss, $vacantes) or die(mysql_error());
$row_boss = mysql_fetch_assoc($boss);
$totalRows_boss = mysql_num_rows($boss);
$boss_nombre = $row_boss['emp_nombre']. " ".$row_boss['emp_paterno']." ".$row_boss['emp_materno'];

mysql_select_db($database_vacantes, $vacantes);
$query_evaluados = "SELECT  prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.fecha_alta, vac_areas.area, vac_matriz.matriz, prod_llave.IDllaveJ, prod_llave.IDaplica_SED, prod_llave.IDllave FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz LEFT JOIN prod_llave ON prod_llave.IDllave = prod_activos.IDllave WHERE prod_activos.IDempleadoJ = '$el_usuario' AND prod_activos.IDaplica_SED = 1";
mysql_query("SET NAMES 'utf8'");
$evaluados = mysql_query($query_evaluados, $vacantes) or die(mysql_error());
$row_evaluados = mysql_fetch_assoc($evaluados);
$totalRows_evaluados = mysql_num_rows($evaluados);

mysql_select_db($database_vacantes, $vacantes);
$query_evaluados_indirectos = "SELECT  prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.fecha_alta, vac_areas.area, vac_matriz.matriz, prod_llave.IDllaveJ, prod_llave.IDaplica_SED, prod_llave.IDllave FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz LEFT JOIN prod_llave ON prod_llave.IDllave = prod_activos.IDllave WHERE prod_activos.IDempleadoJ = '$el_usuario' AND prod_activos.IDaplica_SED = 1";
mysql_query("SET NAMES 'utf8'");
$evaluados_indirectos = mysql_query($query_evaluados_indirectos, $vacantes) or die(mysql_error());
$row_evaluados_indirectos = mysql_fetch_assoc($evaluados_indirectos);
$totalRows_evaluados_indirectos = mysql_num_rows($evaluados_indirectos);

$query_periodos = "SELECT * FROM sed_periodos_sed WHERE sed_periodos_sed.visible = 1"; 
mysql_query("SET NAMES 'utf8'");
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos);

$query_periodo = "SELECT * FROM sed_periodos_sed WHERE IDperiodo = '$IDperiodo'"; 
$periodo = mysql_query($query_periodo, $vacantes) or die(mysql_error());
$row_periodo = mysql_fetch_assoc($periodo);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$IDempleadoJ = $_POST['IDempleadoJ'];
$query1 = "UPDATE prod_activos SET IDempleadoJ = '$IDempleadoJ' WHERE IDempleado = '$el_usuario'"; 
$result1 = mysql_query($query1) or die(mysql_error());  
  
mysql_select_db($database_vacantes, $vacantes);
$query_boss = "SELECT * FROM prod_activosj WHERE IDempleado = '$el_usuario'"; 
$boss = mysql_query($query_boss, $vacantes) or die(mysql_error());
$row_boss = mysql_fetch_assoc($boss);
$totalRows_boss = mysql_num_rows($boss);
  
if ($totalRows_boss == 0 ) {
$query2 = "INSERT INTO prod_activosj (IDempleado, IDempleadoJ) VALUES ('$el_usuario', '$IDempleadoJ')"; 
$result2 = mysql_query($query2) or die(mysql_error());  
} else {
$query2 = "UPDATE prod_activosj SET IDempleadoJ = '$IDempleadoJ' WHERE IDempleado = '$el_usuario'"; 
$result2 = mysql_query($query2) or die(mysql_error());  
}
header("Location: f_desemp.php?info=12"); 	
}

mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT prod_activos.IDempleado,  prod_activos.emp_paterno,  prod_activos.emp_materno,  prod_activos.emp_nombre, prod_activos.descripcion_nomina, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal, prod_activos.IDarea, vac_puestos.IDpuesto, vac_puestos.valuacion, vac_puestos.denominacion FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto
WHERE IDempleado <> '$el_usuario' AND IDmatriz = '$IDmatriz' AND vac_puestos.valuacion > 11 ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);
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
	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
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
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<!-- /Theme JS files -->
 </head>
<body class= "has-detached-right<?php if (isset($_COOKIE["lmenu"])) { echo ' sidebar-xs';}?>">

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/f_pheader.php'); ?>

			<!-- Content area -->
			  <div class="content">
              
					<h1 class="text-center content-group text-danger">
						Evaluación del Desempeño
						<small class="display-block"><?php echo $row_periodo['periodo']; ?></small>
                    </h1>

						<?php if ($IDempleadoj == '') { ?>		
						<div class="alert bg-warning-600 alert-styled-left">
						<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
						Actualmente no tenemos identificado quien es tu jefe inmediato, 
						<a data-target="#modal_theme_danger"  data-toggle="modal" class="text text-bold">da clic aqui para corregirlo</a>.
						</div>
						<?php } else { ?>		
						<div class="alert alert-styled-left alert-styled-custom alert-arrow-left alpha-teal alert-bordered">
						<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
						Actualmente tienes asignado a <span class="text-semibold"><?php echo $boss_nombre; ?></span> como tu Jefe Inmediato. Si es incorrecto, 
						<a data-target="#modal_theme_danger"  data-toggle="modal" class="text text-bold text-default">da clic aqui</a>.
						</div>
						<?php } ?>		

						<!-- Basic alert -->	
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el Objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el Objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 12))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente tu jefe inmedieto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el Objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

				<!-- Detached content -->
					<div class="container-detached">
						<div class="content-detached">
                        
							<!-- About author -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Mi Evaluacion</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
									<p>Acontinuación se muestra el estatus de su Evaluación del Desempeño para el Periodo indicado.</p>
									<p>Seleccione un periodo en el menú derecho para ver resultados. Seleccione alguna acción.</p>
                                    
                    <table class="table datatable-basic table-bordered">
                    <thead> 
                    <tr class="bg-primary-600"> 
                      <th>No. Empleado</th>
                      <th>Nombre del Empleado</th>
                      <th>Estatus de la Evaluación</th>
                      <th>Resultado del Periodo</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
                        <?php  
						$query_mis_metas1 = "SELECT 
						sed_individuales_resultados.IDresultado,
						sed_individuales_resultados.IDempleado,
						sed_individuales_resultados.IDperiodo,
						sed_individuales_resultados.resultado,
						sed_individuales_resultados.especial,
						sed_individuales_resultados.estatus
						FROM sed_individuales_resultados
						left JOIN sed_periodos_sed ON sed_periodos_sed.IDperiodo = sed_individuales_resultados.IDperiodo
						 WHERE sed_individuales_resultados.IDempleado = '$el_usuario' AND sed_periodos_sed.IDperiodo = '$IDperiodo'"; 
						$mis_metas1 = mysql_query($query_mis_metas1, $vacantes) or die(mysql_error());
						$row_mis_metas1 = mysql_fetch_assoc($mis_metas1);
						$totalRows_mis_metas1 = mysql_num_rows($mis_metas1);
						?>
                          <tr>
                            <td><?php echo $row_evaluado['IDempleado'];  ?></td>
                            <td><?php echo $row_evaluado['emp_paterno']. " ".$row_evaluado['emp_materno']." ".$row_evaluado['emp_nombre']; ?></td>
                            <td><?php 
							      if($row_mis_metas1['estatus'] == "") { echo "Sin Objetivos capturados"; } 
							 else if($row_mis_metas1['estatus'] == 0) { echo "Objetivos en proceso de Captura"; }
							 else if($row_mis_metas1['estatus'] == 1) { echo "Objetivos Capturados"; }
							 else if($row_mis_metas1['estatus'] == 2) { echo "Con resultados propuestos"; }
							 else if($row_mis_metas1['estatus'] == 3) { echo "Evaluado"; }
							 else { echo "-";} ?>
							 </td>
                            <td><?php 
							      if($row_mis_metas1['resultado'] > 95) { echo $row_mis_metas1['resultado']. "% <span class='label label-primary'>Sobresaliente</span>"; } 
							 else if($row_mis_metas1['resultado'] > 75) { echo $row_mis_metas1['resultado']. "% <span class='label label-success'>Satisfactorio</span>"; } 
							 else if($row_mis_metas1['resultado'] > 1 ) { echo $row_mis_metas1['resultado']. "% <span class='label label-warning'>Deficiente</span>"; } 
							 else { echo "<span class='label label-default'>Sin Evaluación</span>";} ?>
							 </td>
                            <td>

  <?php if($row_evaluado['IDaplica_SED'] == 0) { echo "<span class='label label-default'>No aplica a su Puesto</span>"// no le aplica ?>
  
    <?php } else {  ?>


                        <?php if($row_periodo['estatus'] == 1) { // periodo para captura ?>
                        
                        <?php 	if($row_mis_metas1['estatus'] == "" or $row_mis_metas1['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=11
                        <?php if($row_periodo['estatus'] == "") { echo "&open=1"; } ?>">Capturar Objetivos</a>
						<?php } else if($row_mis_metas1['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=12">Ver Objetivos</a>
						<?php } else if($row_mis_metas1['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=13">Ver Objetivos</a>
						<?php } else if($row_mis_metas1['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=14">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>
						
						<?php } else if($row_periodo['estatus'] == 2) {  // periodo para evaluacion ?>

                        <?php if($row_mis_metas1['estatus'] == "" or $row_mis_metas1['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=21">Capturar Objetivos</a>
						<?php } else if($row_mis_metas1['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_proponer.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;; ?>&id=22">Proponer Resultados</a>
						<?php } else if($row_mis_metas1['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_proponer.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;; ?>&id=23">Ver Objetivos</a>
						<?php } else if($row_mis_metas1['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=24">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>

						<?php } else if($row_mis_metas1['especial'] == 2) { // periodo cerrado pero especial ?>

                        <?php 	if($row_mis_metas1['estatus'] == "" or $row_mis_metas1['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=31">Capturar Objetivos</a>
						<?php } else if($row_mis_metas1['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_proponer.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;; ?>&id=32">Proponer Resultados</a>
						<?php } else if($row_mis_metas1['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_proponer.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;; ?>&id=33">Ver Objetivos</a>
						<?php } else if($row_mis_metas1['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=34">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>

						<?php } else if($row_periodo['estatus'] == 3 AND $row_mis_metas1['especial'] == 1) { // periodo cerrado ?>

                        <?php 	if($row_mis_metas1['estatus'] == "" or $row_mis_metas1['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-default" href="#">Periodo Cerrado</a>
						<?php } else if($row_mis_metas1['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=42">Ver Objetivos</a>
						<?php } else if($row_mis_metas1['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&id=43">Ver Objetivos</a>
						<?php } else if($row_mis_metas1['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_usuario;?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>

						<?php } ?>

						<?php } ?>
                            </td>
                          </tr>
                     </tbody>
					</table>

                                    
                                    
                                    </div>
								</div>
							</div>
							<!-- /about author -->

                        
							<!-- About author -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title text-semibold">Mis colaboradores</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
									<p>En el siguiente listado, se muestra el estatus de la evaluación de sus Colaboradores.</p>
									<p>Únicamente se muestran los puestos administrativos que aplican, si no aparece uno de sus colaboradores, solicite que se agregue a <a href="mailto:mahernandez@sahuayo.mx">mahernandez@sahuayo.mx</a>.</p>
									<p>Seleccione una acción.</p>
                                    
                                    
                                    
                    <table class="table datatable-basic table-bordered">
                    <thead> 
                    <tr class="bg-primary-600"> 
                      <th>No. Empleado</th>
                      <th>Nombre del Empleado</th>
                      <th>Estatus de la Evaluación</th>
                      <th>Resultado del Periodo</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
                        <?php if($totalRows_evaluados > 0){ ?>
                        <?php do { 

						$el_evaluado = $row_evaluados['IDempleado']; 
						$query_mis_metas = "SELECT sed_individuales_resultados.IDresultado, sed_individuales_resultados.IDempleado, sed_individuales_resultados.IDperiodo,
						sed_individuales_resultados.resultado, sed_individuales_resultados.especial, sed_individuales_resultados.estatus FROM sed_individuales_resultados
						left JOIN sed_periodos_sed ON sed_periodos_sed.IDperiodo = sed_individuales_resultados.IDperiodo WHERE sed_individuales_resultados.IDempleado = '$el_evaluado' AND sed_periodos_sed.IDperiodo = '$IDperiodo'"; 
						$mis_metas = mysql_query($query_mis_metas, $vacantes) or die(mysql_error());
						$row_mis_metas = mysql_fetch_assoc($mis_metas);
						$totalRows_mis_metas = mysql_num_rows($mis_metas);
						?>
                          <tr>
                            <td><?php echo $row_evaluados['IDempleado']; ?></td>
                            <td><?php echo $row_evaluados['emp_paterno']. " ".$row_evaluados['emp_materno']." ".$row_evaluados['emp_nombre']; ?></td>
                            <td><?php 
							      if($row_mis_metas['estatus'] == "") { echo "Sin Objetivos capturados"; } 
							 else if($row_mis_metas['estatus'] == 0) { echo "Objetivos en proceso de Captura"; }
							 else if($row_mis_metas['estatus'] == 1) { echo "Objetivos Capturados"; }
							 else if($row_mis_metas['estatus'] == 2) { echo "Con resultados propuestos"; }
							 else if($row_mis_metas['estatus'] == 3) { echo "Evaluado"; }
							 else { echo "-";} ?>
							 </td>
                            <td><?php 
							      if($row_mis_metas['resultado'] > 95) { echo $row_mis_metas['resultado']. "% <span class='label label-primary'>Sobresaliente</span>"; } 
							 else if($row_mis_metas['resultado'] > 75) { echo $row_mis_metas['resultado']. "% <span class='label label-success'>Satisfactorio</span>"; } 
							 else if($row_mis_metas['resultado'] > 1 ) { echo $row_mis_metas['resultado']. "% <span class='label label-warning'>Deficiente</span>"; } 
							 else { echo "<span class='label label-default'>Sin Evaluación</span>";} ?>
							 </td>
                            <td>
                            
                        <?php if($row_periodo['estatus'] == 1) { // periodo para captura ?>
                        
                        <?php 	if($row_mis_metas['estatus'] == "" or $row_mis_metas['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=11">Capturar Objetivos</a>
						<?php } else if($row_mis_metas['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=12">Ver Objetivos</a>
						<?php } else if($row_mis_metas['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=13">Ver Objetivos</a>
						<?php } else if($row_mis_metas['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=14">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>
						
						<?php } else if($row_periodo['estatus'] == 2) {  // periodo para evaluacion ?>

                        <?php if($row_mis_metas['estatus'] == "" or $row_mis_metas['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=21">Capturar Objetivos</a>
						<?php } else if($row_mis_metas['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=22">Evaluar Objetivos</a>
						<?php } else if($row_mis_metas['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=23">Evaluar Objetivos</a>
						<?php } else if($row_mis_metas['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=24">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>

						<?php } else if($row_periodo['estatus'] == 3 AND $row_mis_metas['especial'] == 2) { // periodo cerrado pero especial ?>

                        <?php 	if($row_mis_metas['estatus'] == "" or $row_mis_metas['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=31">Capturar Objetivos</a>
						<?php } else if($row_mis_metas['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=32">Evaluar Objetivos</a>
						<?php } else if($row_mis_metas['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=33">Evaluar Objetivos</a>
						<?php } else if($row_mis_metas['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=34">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>

						<?php } else if($row_periodo['estatus'] == 3 AND $row_mis_metas['especial'] == 1 OR $row_mis_metas['especial'] == "") { // periodo cerrado ?>

                        <?php 	if($row_mis_metas['estatus'] == "" or $row_mis_metas['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=41">Ver Resultados</a>
						<?php } else if($row_mis_metas['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=42">Ver Resultados</a>
						<?php } else if($row_mis_metas['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=43">Ver Resultados</a>
						<?php } else if($row_mis_metas['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&id=44">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado; ?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>

						<?php } ?>
                        
                            </td>
                          </tr>
                          <?php } while ($row_evaluados = mysql_fetch_assoc($evaluados)); ?>

                        <?php if($totalRows_evaluados_indirectos > 0 AND $_SESSION['IDindirectos'] == 1){ 
							do { 
						// estos son los directos
						$el_evaluado_indirecto = $row_evaluados_indirectos['IDempleado']; 
						$query_evaluados_indirectos_detalle = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.fecha_alta, vac_areas.area, vac_matriz.matriz, prod_llave.IDllaveJ, prod_llave.IDaplica_SED, prod_llave.IDllave FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz LEFT JOIN prod_llave ON prod_llave.IDllave = prod_activos.IDllave WHERE prod_activos.IDempleadoJ = '$el_evaluado_indirecto'";
						mysql_query("SET NAMES 'utf8'");
						$evaluados_indirectos_detalle  = mysql_query($query_evaluados_indirectos_detalle , $vacantes) or die(mysql_error());
						$row_evaluados_indirectos_detalle  = mysql_fetch_assoc($evaluados_indirectos_detalle);
						$totalRows_evaluados_indirectos_detalle  = mysql_num_rows($evaluados_indirectos_detalle); 
						
						if($totalRows_evaluados_indirectos_detalle > 0) { 
							 do { 

								//este es el colaborador indirecto
								$el_evaluado_indirectos = $row_evaluados_indirectos_detalle['IDempleado']; 
								
								$query_evaluados_indirectos_detalles = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.fecha_alta, vac_areas.area, vac_matriz.matriz, prod_llave.IDllaveJ, prod_llave.IDaplica_SED, prod_llave.IDllave FROM prod_activos LEFT JOIN vac_areas ON vac_areas.IDarea = prod_activos.IDarea LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_activos.IDmatriz LEFT JOIN prod_llave ON prod_llave.IDllave = prod_activos.IDllave WHERE prod_activos.IDempleado = '$el_evaluado_indirectos'";
								mysql_query("SET NAMES 'utf8'");
								$evaluados_indirectos_detalles  = mysql_query($query_evaluados_indirectos_detalles, $vacantes) or die(mysql_error());
								$row_evaluados_indirectos_detalles  = mysql_fetch_assoc($evaluados_indirectos_detalles);
								$totalRows_evaluados_indirectos_detalles  = mysql_num_rows($evaluados_indirectos_detalles); 

								$query_mis_metas_indirectos = "SELECT sed_individuales_resultados.IDresultado, sed_individuales_resultados.IDempleado, sed_individuales_resultados.IDperiodo,
								sed_individuales_resultados.resultado, sed_individuales_resultados.especial, sed_individuales_resultados.estatus FROM sed_individuales_resultados left JOIN sed_periodos_sed ON sed_periodos_sed.IDperiodo = sed_individuales_resultados.IDperiodo WHERE sed_individuales_resultados.IDempleado = '$el_evaluado_indirectos' AND sed_periodos_sed.IDperiodo = '$IDperiodo'"; 
								$mis_metas_indirectos = mysql_query($query_mis_metas_indirectos, $vacantes) or die(mysql_error());
								$row_mis_metas_indirectos = mysql_fetch_assoc($mis_metas_indirectos);
								$totalRows_mis_metas_indirectos = mysql_num_rows($mis_metas_indirectos);
							?>
							<tr>
								<td><?php echo $row_evaluados_indirectos_detalles['IDempleado']; ?></td>
								<td><?php echo $row_evaluados_indirectos_detalles['emp_paterno']. " ".$row_evaluados_indirectos_detalles['emp_materno']." ".$row_evaluados_indirectos_detalles['emp_nombre']; ?> <span class="badge badge-flat border-danger text-danger-600 position-righty"> Indirecto</span></td>
								<td><?php 
									if($row_mis_metas_indirectos['estatus'] == "") { echo "Sin Objetivos capturados"; } 
								else if($row_mis_metas_indirectos['estatus'] == 0) { echo "Objetivos en proceso de Captura"; }
								else if($row_mis_metas_indirectos['estatus'] == 1) { echo "Objetivos Capturados"; }
								else if($row_mis_metas_indirectos['estatus'] == 2) { echo "Con resultados propuestos"; }
								else if($row_mis_metas_indirectos['estatus'] == 3) { echo "Evaluado"; }
								else { echo "-";} ?>
								</td>
								<td><?php 
									if($row_mis_metas_indirectos['resultado'] > 95) { echo $row_mis_metas_indirectos['resultado']. "% <span class='label label-primary'>Sobresaliente</span>"; } 
								else if($row_mis_metas_indirectos['resultado'] > 75) { echo $row_mis_metas_indirectos['resultado']. "% <span class='label label-success'>Satisfactorio</span>"; } 
								else if($row_mis_metas_indirectos['resultado'] > 1 ) { echo $row_mis_metas_indirectos['resultado']. "% <span class='label label-warning'>Deficiente</span>"; } 
								else { echo "<span class='label label-default'>Sin Evaluación</span>";} ?>
								</td>
								<td>
                            
                        <?php if($row_periodo['estatus'] == 1) { // periodo para captura ?>
                        
                        <?php 	if($row_mis_metas_indirectos['estatus'] == "" or $row_mis_metas_indirectos['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=11">Capturar Objetivos</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=12">Ver Objetivos</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=13">Ver Objetivos</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=14">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>
						
						<?php } else if($row_periodo['estatus'] == 2) {  // periodo para evaluacion ?>

                        <?php if($row_mis_metas_indirectos['estatus'] == "" or $row_mis_metas_indirectos['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=21">Capturar Objetivos</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=22">Evaluar Objetivos</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=23">Evaluar Objetivos</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=24">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>

						<?php } else if($row_periodo['estatus'] == 3 AND $row_mis_metas_indirectos['especial'] == 2) { // periodo cerrado pero especial ?>

                        <?php 	if($row_mis_metas_indirectos['estatus'] == "" or $row_mis_metas['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_captura.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=31">Capturar Objetivos</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=32">Evaluar Objetivos</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=33">Evaluar Objetivos</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_evaluacion.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=34">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>

						<?php } else if($row_periodo['estatus'] == 3 AND $row_mis_metas['especial'] == 1 OR $row_mis_metas['especial'] == "") { // periodo cerrado ?>

                        <?php 	if($row_mis_metas_indirectos['estatus'] == "" or $row_mis_metas['estatus'] == 0) { // sin captura ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=41">Ver Resultados</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 1) { // capturado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=42">Ver Resultados</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 2) { // propuesto ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=43">Ver Resultados</a>
						<?php } else if($row_mis_metas_indirectos['estatus'] == 3) { // evaluado ?>
                        <a class="btn btn-success btn-xs" href="f_desemp_ver.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&id=44">Ver Resultados</a>
                        <a class="btn btn-primary btn-xs" href="f_desemp_imprimir.php?IDperiodo=<?php echo $IDperiodo; ?>&IDempleado=<?php echo $el_evaluado_indirectos; ?>&print=1"><i class="icon-printer"></i> Imprimir</a>
						<?php } ?>

						<?php } ?>
                        
                            </td>
                          </tr>

                          <?php } while ($row_evaluados_indirectos_detalle = mysql_fetch_assoc($evaluados_indirectos_detalle)); }
							 		} while ($row_evaluados_indirectos = mysql_fetch_assoc($evaluados_indirectos)); 
						} ?>
                        <?php } else { ?>
                           <tr>
                            <td>No tiene colaboradores asignados.</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tr>
                        <?php } ?>
                     </tbody>
					</table>

                                    
                                    </div>
								</div>
							</div>
							<!-- /about author -->




						</div>
					</div>
					<!-- /detached content -->


					<!-- Detached sidebar -->
					<div class="sidebar-detached">
						<div class="sidebar sidebar-default sidebar-separate">
							<div class="sidebar-content">

								<!-- Course details -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Seleccionar Periodo</span>
									</div>

									<div class="category-content">

										<div class="form-group">


                       <form method="GET" action="f_desemp.php">
							<fieldset class="content-group">

                            <div class="form-group">
                             <select name="IDperiodo" class="form-control">
                               <?php do {  ?>
                               <option value="<?php echo $row_periodos['IDperiodo']?>"<?php if (!(strcmp($row_periodos['IDperiodo'], $IDperiodo)))
							   {echo "selected=\"selected\"";} ?>><?php echo $row_periodos['periodo']?></option>
                               <?php
                              } while ($row_periodos = mysql_fetch_assoc($periodos));
                              $rows = mysql_num_rows($periodos);
                              if($rows > 0) {
                                  mysql_data_seek($periodos, 0);
                                  $row_periodos = mysql_fetch_assoc($periodos);
                              } ?></select>
                            </div>
                            <div class="form-group">
                            <button type="submit" class="btn btn-primary">Seleccionar</button>										
                            </div>
					</fieldset>
					</form>

										<div class="form-group">
											<label class="control-label no-margin text-semibold">Estatus del Periodo:</label>
											<div><?php
											if($row_periodo['estatus'] == 3){ echo "Cerrado"; }
											elseif($row_periodo['estatus'] == 2){ echo "Evaluación de Objetivos"; }
											elseif($row_periodo['estatus'] == 1){ echo "Captura de Objetivos";; } ?></div>
										</div>


										</div>

									</div>
								</div>
								<!-- /course details -->

								<!-- Course details -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Ver Indirectos</span>
									</div>

									<div class="category-content">

										<div class="form-group">


                    <form method="GET" action="f_desemp.php">
						<fieldset class="content-group">

                            <div class="form-group">
                             <select name="IDindirectos" id="IDindirectos" class="form-control">
							 <option value="1"<?php if ($_SESSION['IDindirectos'] == 1) {echo "selected=\"selected\"";} ?>>SI</option>
							 <option value="0"<?php if ($_SESSION['IDindirectos'] == 0) {echo "selected=\"selected\"";} ?>>NO</option>
                               </select>
                            </div>
                            <div class="form-group">
                            <button type="submit" class="btn btn-primary">Seleccionar</button>										
                            </div>
					</fieldset>
					</form>
										</div>

									</div>
								</div>
								<!-- /course details -->

								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Fechas Compromiso</span>
									</div>

									<div class="category-content">
										<ul class="media-list">

												  <?php
	 											    $query_mensajes = "SELECT * FROM sed_mensajes WHERE IDperiodo = '$IDperiodo'"; 
													$mensajes = mysql_query($query_mensajes, $vacantes) or die(mysql_error());
													$row_mensajes = mysql_fetch_assoc($mensajes);
													
													do { ?>

											<li class="media">
												<div class="media-left">
													<h5 class="no-margin text-center text-success"> <?php echo $row_mensajes['mes']; ?>
														<small class="display-block text-size-small no-margin"><?php echo $row_mensajes['anio']; ?></small>
													</h5>
												</div>

												<div class="media-body">
													<span class="text-semibold"><?php echo $row_mensajes['mensaje']; ?></span>
													<ul class="list-inline list-inline-separate no-margin-bottom mt-5">
														<li><span class="text-muted"><?php echo $row_periodo['periodo']; ?></span></li>
													</ul>
												</div>
											</li>

												  <?php } while ($row_mensajes = mysql_fetch_assoc($mensajes)); ?>

										</ul>
									</div>
								</div>
								<!-- /upcoming courses -->

							</div>
						</div>
					</div>
		            <!-- /detached sidebar -->



                  <!-- danger modal -->
				  <div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-info">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Actualización de línea de mando</h6>
								</div>

								<div class="modal-body">


                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

                                   <!-- Basic text input -->
									<div class="form-group">
										Selecciona a tu Jefe Inmediato de la siguiente lista. Si no aparece, por favor contacta a Recursos Humanos.
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<div class="col-lg-12">
 											<select class="bootstrap-select" data-live-search="true" data-width="100%" name="IDempleadoJ" id="IDempleadoJ" required="required">
													  <option value="">Selecciona el nombre de tu jefe inmediato</option>
													  <?php  do { ?>
													  <option value="<?php echo $row_jefes['IDempleado']?>"<?php if (!(strcmp($row_jefes['IDempleado'], $row_usuario['IDempleadoJ']))) 
													  {echo "SELECTED";} ?>><?php echo $row_jefes['emp_nombre'] . " " . $row_jefes['emp_paterno'] . " " . $row_jefes['emp_materno']. " (" . $row_jefes['denominacion'] . ")";?></option>
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


								</div>

								<div class="modal-footer">
									<input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>">
									<button type="submit"  name="KT_Update1" class="btn btn-info">Actualizar</button>
									<input type="hidden" name="MM_update" value="form1">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
								</div>
							 </form>

							</div>
						</div>
					</div>
                  <!-- danger modal -->



					<!-- /Contenido -->

				  <!-- Footer -->
				  <div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
			    </div>
				    <!-- /footer -->
                </div>
				<!-- /content area -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>