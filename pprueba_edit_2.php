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

$IDpprueba = $_GET["IDpprueba"];
$_SESSION['IDpprueba'] = $_GET["IDpprueba"];
mysql_select_db($database_vacantes, $vacantes);
$query_pprueba = "SELECT  pp_prueba.val1, pp_prueba.val2, pp_prueba.val3, pp_prueba.val4, pp_prueba.IDpprueba, pp_prueba.IDempleado, pp_prueba.IDpuesto, pp_prueba.IDarea, pp_prueba.sueldo_actual, pp_prueba.sueldo_nuevo, pp_prueba.IDmatriz, pp_prueba.IDpuesto_destino, pp_prueba.IDmatriz_destino, pp_prueba.IDarea_destino, pp_prueba.fecha_fin, pp_prueba.fecha_inicio, pp_prueba.IDestatus, pp_prueba.observaciones, prod_activos.fecha_antiguedad, prod_activos.sueldo_total, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, puesto_origen.denominacion AS denominacion_origen, area_oringen.area AS area_origen, matriz_origen.matriz AS matriz_origen, matriz_destino.matriz as matriz_destino, area_destino.area AS area_destino, puesto_destino.denominacion AS denominacion_destino FROM pp_prueba LEFT JOIN prod_activos ON pp_prueba.IDempleado = prod_activos.IDempleado LEFT JOIN vac_puestos AS puesto_origen ON pp_prueba.IDpuesto = puesto_origen.IDpuesto LEFT JOIN vac_areas AS area_oringen ON puesto_origen.IDarea = area_oringen.IDarea LEFT JOIN vac_matriz AS matriz_origen ON pp_prueba.IDmatriz = matriz_origen.IDmatriz LEFT JOIN vac_matriz AS matriz_destino ON pp_prueba.IDmatriz_destino = matriz_destino.IDmatriz LEFT JOIN vac_puestos AS puesto_destino ON pp_prueba.IDpuesto_destino = puesto_destino.IDpuesto LEFT JOIN vac_areas AS area_destino ON puesto_destino.IDarea = area_destino.IDarea WHERE IDpprueba = '$IDpprueba'";
$pprueba = mysql_query($query_pprueba, $vacantes) or die(mysql_error());
$row_pprueba = mysql_fetch_assoc($pprueba);
$totalRows_pprueba = mysql_num_rows($pprueba);

// criterior antiguedad
$fecha_actual = date("d-m-Y");
$antiguo = strtotime($row_pprueba['fecha_antiguedad']); 
$antiguo1 = strtotime($fecha_actual."- 3 month"); 
if($antiguo < $antiguo1) {$cumple_antiguedad = 1;} else {$cumple_antiguedad = 0;}
if($row_pprueba['IDarea_destino'] == 5 AND $row_pprueba['IDarea'] == 8) {$cumple_area = 0;} else {$cumple_area = 1;}
if($row_pprueba['IDmatriz_destino'] == '') {$Matriz_destino = $row_pprueba['IDmatriz'];} else {$Matriz_destino = $row_pprueba['IDmatriz_destino'];}
if(($row_pprueba['sueldo_nuevo'] < $row_pprueba['sueldo_actual']) AND $row_pprueba['sueldo_nuevo'] != 0) {$cumple_sueldo = 0;} else {$cumple_sueldo = 1;}

//meses a pagar
$fecha_inicio = new DateTime($row_pprueba['fecha_inicio']);
$fecha_final = new DateTime($row_pprueba['fecha_fin']);
$diff = $fecha_inicio->diff($fecha_final);
$meses =  $diff->m;

if($row_pprueba['fecha_inicio'] != '' AND $meses > 6) {$cumple_meses = 0;} else {$cumple_meses = 1;}
if($row_pprueba['fecha_inicio'] != '' AND $meses < 3) {$cumple_meses = 0;} else {$cumple_meses = 1;}


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$fecha1a = $_POST['fecha_inicio']; 
$fecha1b = explode("/",$fecha1a);
$fecha1 = $fecha1b[2]."-".$fecha1b[1]."-".$fecha1b[0];

$fecha2a = $_POST['fecha_fin']; 
$fecha2b = explode("/",$fecha2a);
$fecha2 = $fecha2b[2]."-".$fecha2b[1]."-".$fecha2b[0];

$val1 = $_POST['val1']; 
$val2 = $_POST['val2']; 
$val3 = $_POST['val3']; 
$val4 = $_POST['val4']; 
	
$el_estatus = $row_pprueba['IDestatus'];
if ($el_estatus == 1 OR $el_estatus == '') {$el_estatus = 2;}
  $updateSQL = sprintf("UPDATE pp_prueba SET IDpuesto_destino=%s, IDmatriz_destino=%s, sueldo_actual=%s, IDarea_destino=%s,  fecha_inicio=%s, fecha_fin=%s, observaciones=%s, sueldo_nuevo=%s, IDestatus=%s, val1=%s, val2=%s, val3=%s, val4=%s WHERE IDpprueba=%s",
                       GetSQLValueString($_POST['IDpuesto_destino'], "int"),
                       GetSQLValueString($_POST['IDmatriz_destino'], "int"),
                       GetSQLValueString($_POST['sueldo_actual'], "text"),
                       GetSQLValueString($_POST['IDarea_destino'], "int"),
                       GetSQLValueString($fecha1, "text"),
                       GetSQLValueString($fecha2, "text"),
                       GetSQLValueString($_POST['observaciones'], "text"),
                       GetSQLValueString($_POST['sueldo_nuevo'], "text"),
                       GetSQLValueString($el_estatus, "int"),
                       GetSQLValueString($val1, "int"),
                       GetSQLValueString($val2, "int"),
                       GetSQLValueString($val3, "int"),
                       GetSQLValueString($val4, "int"),
                       GetSQLValueString($_POST['IDpprueba'], "text"));

 mysql_select_db($database_vacantes, $vacantes);
 $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

 $last_id =  mysql_insert_id();
header("Location: pprueba_edit_2.php?IDpprueba=$IDpprueba&info=1");
}

// borrar alternativo
if ((isset($_GET['IDpprueba_borrar'])) && ($_GET['IDpprueba_borrar'] != "")) {
  
  $borrado = $_GET['IDpprueba_borrar'];
  $deleteSQL = "DELETE FROM pp_prueba WHERE IDpprueba ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: pprueba.php?info=3");
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

	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<!-- /theme JS files -->
<script>
function realizaProceso(valorCaja1, valorCaja2){
        var parametros = {
                "valorCaja1" : valorCaja1,
                "valorCaja2" : valorCaja2
        };
        $.ajax({
                data:  parametros,
                url:   'get_puesto.php',
                type:  'post',
                beforeSend: function () {
                        $("#txtHint").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#txtHint").html(response);
                }
        });
}
</script>
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
                        <?php if($cumple_antiguedad == 0) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El empleado no cumple con el criterio de antiguedad.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($cumple_area == 0) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se requiere autorización especial para promover empleados de Finanzas a Ventas.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($cumple_meses == 0) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El PP debe ser mayor a 3 meses y menor a 6.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if($cumple_sueldo == 0) { ?>
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El sueldo es menor al actual, por favor validar.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Agregar PP</h5>
						</div>

					<div class="panel-body">
							<p>PASO 2. Ingresa la información del puesto destino.<br/>
							Si no aparece el sueldo, solicitalo a Desarrollo Organizacional.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
							<fieldset class="content-group">
								
						<legend class="text-semibold">Datos Empleado</legend>

							        <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">No. de Empleado:</label>
										<div class="col-lg-9">
						<input type="text" name="IDempleado" id="IDempleado" class="form-control" value="<?php echo $row_pprueba['IDempleado']; ?>" readonly="readonly" >
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre Empleado:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo $row_pprueba['emp_paterno']." ".$row_pprueba['emp_materno']." ".$row_pprueba['emp_nombre']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fecha de antiguedad:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo date("d-m-Y",strtotime($row_pprueba['fecha_antiguedad']));  ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

						<legend class="text-semibold">Puesto Origen</legend>

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo $row_pprueba['matriz_origen']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo $row_pprueba['area_origen']; ?>" readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:</label>
										<div class="col-lg-9">
											<input type="text"  class="form-control" value="<?php echo $row_pprueba['denominacion_origen']; ?>"  readonly="readonly">
										</div>
									</div>
									<!-- /basic text input -->
									
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo actual:</label>
										<div class="col-lg-9">
											<input type="text" name="sueldo_actual" id="sueldo_actual" class="form-control" value="<?php echo round($row_pprueba['sueldo_actual'],2); ?>" 
											<?php if ($row_pprueba['sueldo_actual'] != 0) { ?> readonly="readonly" <?php } ?>>
										</div>
									</div>
									<!-- /basic text input -->



	<legend class="text-semibold">Puesto destino</legend>

									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz_destino" id="IDmatriz_destino" class="bootstrap-select" data-live-search="true" data-width="100%"   required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $Matriz_destino))) 
												  {echo "SELECTED";} ?>><?php echo $row_lmatriz['matriz']?></option>
												  <?php
												 } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
												   $rows = mysql_num_rows($lmatriz);
												   if($rows > 0) {
												   mysql_data_seek($lmatriz, 0);
												   $row_lmatriz = mysql_fetch_assoc($lmatriz);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea_destino" id="IDarea_destino" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $row_pprueba['IDarea_destino']))) 
												  {echo "SELECTED";} ?>><?php echo $row_area['area']?></option>
												  <?php
												 } while ($row_area = mysql_fetch_assoc($area));
												   $rows = mysql_num_rows($area);
												   if($rows > 0) {
												   mysql_data_seek($area, 0);
												   $row_area = mysql_fetch_assoc($area);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

								<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto_destino" id="IDpuesto_destino"class="bootstrap-select" data-live-search="true" data-width="100%" onchange="realizaProceso($('#IDpuesto_destino').val(), $('#IDmatriz_destino').val());return false;" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_puesto['IDpuesto']?>"<?php if (!(strcmp($row_puesto['IDpuesto'], $row_pprueba['IDpuesto_destino']))) 
												  {echo "SELECTED";} ?>><?php echo $row_puesto['denominacion']?></option>
												  <?php
												 } while ($row_puesto = mysql_fetch_assoc($puesto));
												   $rows = mysql_num_rows($puesto);
												   if($rows > 0) {
												   mysql_data_seek($puesto, 0);
												   $row_puesto = mysql_fetch_assoc($puesto);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

									<div id="txtHint"></div>


	<legend class="text-semibold">Fechas</legend>

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha inicio:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_inicio" id="fecha_inicio" value="<?php if ($row_pprueba['fecha_inicio'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_pprueba['fecha_inicio'])); }?>"  required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha término:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_fin" id="fecha_fin" value="<?php if ($row_pprueba['fecha_fin'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_pprueba['fecha_fin'])); }?>"  required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->
									
									
	<legend class="text-semibold">Validación de Política</legend>
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Cubre el Perfil:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="val1" id="val1" class="form-control"  required="required">
												  <option value="1"<?php if ($row_pprueba['val1'] == 1) {echo "SELECTED";} ?>>Si cubre</option>
												  <option value="0"<?php if ($row_pprueba['val1'] == 0) {echo "SELECTED";} ?>>No cubre</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Resultado de Evaluaciones:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="val2" id="val2" class="form-control"  required="required">
												  <option value="1"<?php if ($row_pprueba['val2'] == 1) {echo "SELECTED";} ?>>Si cubre</option>
												  <option value="0"<?php if ($row_pprueba['val2'] == 0) {echo "SELECTED";} ?>>No cubre</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Antigüedad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="val3" id="val3" class="form-control"  required="required">
												  <option value="1"<?php if ($row_pprueba['val3'] == 1) {echo "SELECTED";} ?>>Si cubre</option>
												  <option value="0"<?php if ($row_pprueba['val3'] == 0) {echo "SELECTED";} ?>>No cubre</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Evaluación del Desempeño:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="val4" id="val4" class="form-control"  required="required">
												  <option value="1"<?php if ($row_pprueba['val4'] == 1) {echo "SELECTED";} ?>>Si cubre</option>
												  <option value="0"<?php if ($row_pprueba['val4'] == 0) {echo "SELECTED";} ?>>No cubre</option>
											</select>
										</div>
									</div>
									<!-- /basic text input -->
									
									<!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Observaciones:</label>
										<div class="col-lg-9">
                                          <textarea name="observaciones" rows="3" class="form-control" id="observaciones" placeholder="Observaciones"><?php echo $row_pprueba['observaciones']; ?></textarea>

										</div>
									</div>
									<!-- /basic text input -->

	
                                    
                         <button type="submit"  name="KT_Update1" class="btn btn-primary">Actualizar</button>
                         <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDpprueba" value="<?php echo $IDpprueba; ?>">
						 <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                    	 <button type="button" onClick="window.location.href='pprueba.php'" class="btn btn-default btn-icon">Regresar</button>
						 
					<!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el PP?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="pprueba_edit_2.php?IDpprueba_borrar=<?php echo $IDpprueba; ?>">Si borrar</a>
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