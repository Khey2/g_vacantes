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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$fecha_tope = date("Y-m-d",strtotime($fecha."- 30 days"));

$colname_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = "SELECT * FROM vac_usuarios WHERE IDusuario = '$colname_usuario'";
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];

if (isset($_POST['mi_areaP'])) {  $_SESSION['mi_areaP'] = $_POST['mi_areaP']; } 
elseif (!isset($_SESSION['mi_areaP'])) {  $_SESSION['mi_areaP'] =  '1,2,3,4,5,6,7,8,9,10,11'; } 
$mi_areaP = $_SESSION['mi_areaP'];

if (isset($_POST['la_matrizP'])) {  $_SESSION['la_matrizP'] = $_POST['la_matrizP']; } 
elseif (!isset($_SESSION['la_matrizP'])) {  $_SESSION['la_matrizP'] =  $IDmatriz; } 
$la_matrizP = $_SESSION['la_matrizP'];

if ($la_matrizP == 7) {
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT DISTINCT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.fecha_antiguedad, prod_activos.denominacion, prod_activos.emp_nombre, prod_activos.rfc, Count(exp_files.id) AS Docs, vac_puestos.IDpuesto, vac_puestos.IDaplica_EXP, exp_tultitlan.IDestatus,	exp_tultitlan.IDusuario, exp_tultitlan.detalle FROM prod_activos LEFT JOIN exp_files ON prod_activos.IDempleado = exp_files.IDempleado LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN exp_tultitlan ON prod_activos.IDempleado = exp_tultitlan.IDempleado WHERE prod_activos.IDmatriz = $la_matrizP AND prod_activos.IDarea IN ($mi_areaP) GROUP BY prod_activos.IDempleado";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);

} else {
	
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT DISTINCT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.fecha_antiguedad, prod_activos.denominacion, prod_activos.emp_nombre, prod_activos.rfc, Count(exp_files.id) AS Docs, vac_puestos.IDpuesto, vac_puestos.IDaplica_EXP, exp_tultitlan.IDestatus,	exp_tultitlan.IDusuario, exp_tultitlan.detalle FROM prod_activos LEFT JOIN exp_files ON prod_activos.IDempleado = exp_files.IDempleado LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN exp_tultitlan ON prod_activos.IDempleado = exp_tultitlan.IDempleado WHERE prod_activos.IDmatriz = $la_matrizP AND prod_activos.IDarea IN ($mi_areaP) AND vac_puestos.IDaplica_EXP = 1 GROUP BY prod_activos.IDempleado";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
}

$query_areas = "SELECT * FROM vac_areas WHERE IDarea < 12";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_lmatriz = "SELECT * FROM vac_matriz WHERE IDmatriz in ($IDmatrizes)";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//Total de Activos
mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT DISTINCT prod_activos.IDempleado, prod_activos.emp_paterno,  prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.fecha_antiguedad, prod_activos.denominacion, prod_activos.emp_nombre, prod_activos.rfc, Count(exp_files.id) AS Docs, vac_puestos.IDpuesto, vac_puestos.IDaplica_EXP FROM prod_activos LEFT JOIN exp_files ON prod_activos.IDempleado = exp_files.IDempleado LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDmatriz = $la_matrizP AND prod_activos.IDarea IN ($mi_areaP) AND vac_puestos.IDaplica_EXP = 1 GROUP BY prod_activos.IDempleado";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

$query_capturados = "SELECT DISTINCT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.fecha_antiguedad, prod_activos.denominacion, prod_activos.emp_nombre, prod_activos.rfc, Count(exp_files.id) AS Docs, vac_puestos.IDpuesto, vac_puestos.IDaplica_EXP FROM prod_activos LEFT JOIN exp_files ON prod_activos.IDempleado = exp_files.IDempleado LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDmatriz = $la_matrizP AND prod_activos.IDarea IN ($mi_areaP) AND vac_puestos.IDaplica_EXP = 1 AND exp_files.id IS NOT NULL GROUP BY prod_activos.IDempleado";
$capturados = mysql_query($query_capturados, $vacantes) or die(mysql_error());
$row_capturados = mysql_fetch_assoc($capturados);
$totalRows_capturados = mysql_num_rows($capturados);

$query_faltantes = "SELECT DISTINCT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.fecha_antiguedad, prod_activos.denominacion, prod_activos.emp_nombre, prod_activos.rfc, Count(exp_files.id) AS Docs, vac_puestos.IDpuesto, vac_puestos.IDaplica_EXP FROM prod_activos LEFT JOIN exp_files ON prod_activos.IDempleado = exp_files.IDempleado LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto WHERE prod_activos.IDmatriz = $la_matrizP AND prod_activos.IDarea IN ($mi_areaP) AND vac_puestos.IDaplica_EXP = 1 AND exp_files.id IS NULL GROUP BY prod_activos.IDempleado";
$faltantes = mysql_query($query_faltantes, $vacantes) or die(mysql_error());
$row_faltantes = mysql_fetch_assoc($faltantes);
$totalRows_faltantes = mysql_num_rows($faltantes);

$query_asignados = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($la_matrizP, IDmatrizes) AND user_expediente = 1 ORDER BY usuario_nombre ASC";
$asignados = mysql_query($query_asignados, $vacantes) or die(mysql_error());
$row_asignados = mysql_fetch_assoc($asignados);
$totalRows_asignados = mysql_num_rows($asignados);

$IDempleado_usuario = $el_usuario;

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
	$IDestatus = $_POST["IDestatus"]; 
	$IDempleado_a = $_POST["IDempleado"]; 
	$IDestatus_a = $_POST["IDestatus_a"];
	$detalle_a = $_POST["detalle"]; 
	$IDusuario_a = $_POST["IDusuario"]; 
	$fecha = date("Y-m-d"); 

if ($IDestatus == 0){
	
	$query1 = "INSERT INTO exp_tultitlan (IDempleado, IDempleado_usuario, IDestatus, detalle, IDusuario, fecha_captura) VALUES ('$IDempleado_a', '$IDempleado_usuario', '$IDestatus_a', '$detalle_a', '$IDusuario_a', '$fecha')"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	header("Location: expedientes_consulta.php?info=3"); 	
} else {
	$query1 = "UPDATE exp_tultitlan SET IDempleado_usuario = '$IDempleado_usuario', IDestatus = '$IDestatus_a', detalle = '$detalle_a', IDusuario = '$IDusuario_a', fecha_captura = '$fecha' WHERE IDempleado = '$IDempleado_a'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	header("Location: expedientes_consulta.php?info=3"); 	
}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    <!-- /theme JS files -->
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
			<!-- Content area -->
				<div class="content">


	                <!-- Content area -->
				<div class="content">
               
               
                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el documento.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el estatus.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


							<!-- Simple text stats with icons -->
							<div class="panel panel-body">
								<div class="row text-center">
									<div class="col-xs-4">
										<p><i class="icon-user-plus icon-2x display-inline-block text-info"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $totalRows_activos; ?></h5>
										<span class="text-muted text-size-small">Empleados Activos</span>
									</div>

									<div class="col-xs-4">
										<p><i class="icon-user-minus icon-2x display-inline-block text-warning"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $totalRows_capturados; ?></h5>
										<span class="text-muted text-size-small">Expedientes Cargados</span>
									</div>


									<div class="col-xs-4">									
									<?php  if ($totalRows_faltantes >= 1) { ?>
										<p><i class="icon-cancel-circle2 icon-2x display-inline-block text-danger"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $totalRows_faltantes; ?></h5>
										<span class="text-muted text-size-small">Expedientes Pendientes</span>
									<?php } else { ?>
										<p><i class="icon-checkmark-circle icon-2x display-inline-block text-success"></i></p>
										<h5 class="text-semibold no-margin"><?php echo $totalRows_faltantes; ?></h5>
										<span class="text-muted text-size-small">Expedientes Pendientes</span>
									<?php } ?>

									</div>

								</div>
							</div>
							<!-- /simple text stats with icons -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Expendientes Digitales</h5>
						</div>

					<div class="panel-body">
					  <p>A continuación se muesta el listado de empleados activos de la Sucursal <strong><?php echo $row_matriz['matriz']; ?></strong>.</br>
                      En la columna "<strong># Doctos.</strong>" se indica la cantidad de documentos cargados enb el Expediente Digial del empleado.</br>
                      Los Expedientes con "<strong>Fecha de Alta</strong>" marcados en <span class='text text-warning text-bold'>Naranja</span> rebasan el tiempo esperado para su carga.</br>
                      Los Expedientes con "<strong>IDempleado</strong>" marcados en <span class='text text-danger text-bold'>Rojo</span> se deben cargar nuevamente.</br>
                      <a href="files/checklist.pdf">Descarga aqui </a>el ChekList de documentos que debe contener el Expediente.</br>
                      Utiliza el filtro para cambiar de Sucursal o Área. Da clic en <strong>Editar</strong> para consultar el detalle de documentos cargados y agregar documentos adicionales.</p>



                    <form method="POST" action="expedientes_consulta.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td><div class="col-lg-9">
                                 <select class="form-control" name="el_areaP">
                                   <option value="1,2,3,4,5,6,7,8,9,10,11">Todas</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_areas['IDarea']?>"<?php if (!(strcmp($row_areas['IDarea'], $mi_areaP))) {echo "selected=\"selected\"";} ?>><?php echo $row_areas['area']?></option>
                                   <?php
                                  } while ($row_areas = mysql_fetch_assoc($areas));
                                  $rows = mysql_num_rows($areas);
                                  if($rows > 0) {
                                      mysql_data_seek($areas, 0);
                                      $row_areas = mysql_fetch_assoc($areas);
                                  } ?> </select>
						    </div></td>
							<td><div class="col-lg-9">
                                 <select class="form-control" name="la_matrizP">
                                   <option value="1,2,3,4,5,6,7,8,9,10">Todas</option>
                                <?php do { ?>
                                   <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matrizP)))
                                   {echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
                                   <?php
                                  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
                                  $rows = mysql_num_rows($lmatriz);
                                  if($rows > 0) {
                                      mysql_data_seek($lmatriz, 0);
                                      $row_lmatriz = mysql_fetch_assoc($lmatriz);
                                  } ?> </select>
						    </div></td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>
							<button type="button" onClick="window.location.href='expedientes_consulta_reporte.php?IDmatriz=<?php echo $la_matrizP; ?>&areas=<?php echo $mi_areaP; ?>'" class="btn btn-success">Reporte</button>							
                            </td>
							<td>
                            </td>
					      </tr>
					    </tbody>
				    </table>
				</form>
                
                
                
					<table class="table table-condensed datatable-button-html5-columns">
                    <thead> 
                    <tr class="bg-success"> 
                      <th>IDemp</th>
                      <th>Nombre</th>
                      <th>Puesto</th>
                      <th>Fecha Alta</th>
                      <th># Doctos.</th>
                      <th>Validación</th>
                      <th>Acciones</th>
               		 </tr>
                    </thead>
                    <tbody>
                        <?php if ($totalRows_contratos > 0) { ?>
                        <?php do { ?>
                          <tr>
                            <td><?php $IDempleado_carpeta = 'files/'.$row_contratos['IDempleado']; 
								if (!file_exists($IDempleado_carpeta) AND $row_contratos['Docs'] > 0) {echo "<div class='label label-danger'>".$row_contratos['IDempleado']."</div>";} else {echo $row_contratos['IDempleado'];} ?></td>
                            <td><?php echo $row_contratos['emp_paterno']." ".$row_contratos['emp_materno']." ".$row_contratos['emp_nombre']; ?>&nbsp; </td>
                            <td><?php echo $row_contratos['denominacion']; ?>&nbsp; </td>
                            <td><?php $la_fecha = date("Y-m-d", strtotime($row_contratos['fecha_antiguedad'])); if($la_fecha < $fecha_tope AND $row_contratos['Docs'] == 0) {echo "<div class='label label-warning'>".date("d/m/Y", strtotime($row_contratos['fecha_antiguedad']))."</div>";} else {echo "<div class='label label-success'>".date("d/m/Y", strtotime($row_contratos['fecha_antiguedad']))."</div>";} ?></td>
							<td><?php if ($row_contratos['Docs'] > 0) {echo $row_contratos['Docs']." cargados";} else{ echo "Pendiente";} ?></td>
							<td><?php 
								 if ($row_contratos['IDestatus'] == 1) {echo "Completo sin Obs."; $IDestatus = 1;} 
							else if ($row_contratos['IDestatus'] == 2) {echo "Completo con Obs.";  $IDestatus = 2;} 
							else if ($row_contratos['IDestatus'] == 3) {echo "Incompleto con Obs."; $IDestatus = 3;} 
							else 									   {echo "Sin revisión"; $IDestatus = 0;} ?></td>
                            <td><a class="btn btn-primary" href="expedientes_nuevo.php?IDempleado=<?php echo $row_contratos['IDempleado']; ?>">Doctos</a>
							<?php if ($row_contratos['Docs'] > 0) { ?>
							<button type="button" data-target="#modal_theme_danger<?php echo $row_contratos['IDempleado']; ?>"  data-toggle="modal" class="btn bg-danger">Validar</button>
							<?php } ?>
							</td>
                          </tr>
						  
						  
						  									<!-- danger modal -->
									<div id="modal_theme_danger<?php echo $row_contratos['IDempleado']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cambiar estatus</h6>
												</div>
												<div class="modal-body">
																			
														<form action="expedientes_consulta.php" method="post" name="importar" id="importar">
														 <fieldset>
														 
														 Completa la información solicitada.
														<p>&nbsp;</p>

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Estatus:</label>
															  <div class="col-lg-9">
															<select name="IDestatus_a" id="IDestatus_a" class="form-control" required="required">
																<option value=""<?php if (!(strcmp($row_contratos['IDestatus'], ''))) {echo "selected=\"selected\"";} ?>>Sin revisión</option>
																<option value="1"<?php if (!(strcmp($row_contratos['IDestatus'], 1))) {echo "selected=\"selected\"";} ?>>Completo sin Obs.</option>
																<option value="2"<?php if (!(strcmp($row_contratos['IDestatus'], 2))) {echo "selected=\"selected\"";} ?>>Completo con Obs.</option>
																<option value="3"<?php if (!(strcmp($row_contratos['IDestatus'], 3))) {echo "selected=\"selected\"";} ?>>Incompleto con Obs.</option>
															</select>
															 </div>
														  </div>
														  <!-- /basic text input -->
														<p>&nbsp;</p>

														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Usuario asignado:</label>
															  <div class="col-lg-9">
																<select class="form-control" name="IDusuario" id="IDusuario" required="required">
																   <option value="">Seleccione una opción...</option>
																   <?php do { ?>
																   <option value="<?php echo $row_asignados['IDusuario']?>"<?php if (!(strcmp($row_asignados['IDusuario'], $row_contratos['IDusuario']))) {echo "selected=\"selected\"";} ?>><?php echo $row_asignados['usuario_nombre']." ".$row_asignados['usuario_parterno']." ".$row_asignados['usuario_materno']?></option>
																   <?php } while ($row_asignados = mysql_fetch_assoc($asignados)); 
																   $rows = mysql_num_rows($asignados);  if($rows > 0) 
																   { mysql_data_seek($asignados, 0); $row_asignados = mysql_fetch_assoc($asignados); } ?> 
																</select>
															 </div>
														  </div>
														  <!-- /basic text input -->
														<p>&nbsp;</p>

														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Comentarios:</label>
															  <div class="col-lg-9">
																	<textarea name="detalle" rows="3" class="form-control" id="detalle" placeholder="Observaciones."><?php echo KT_escapeAttribute($row_contratos['detalle']); ?></textarea>
															 </div>
														  </div>
														  <!-- /basic text input -->


														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-danger">Actualizar</button> 
													<input type="hidden" name="MM_insert" value="form1" />
													<input type="hidden" name="IDempleado" value="<?php echo $row_contratos['IDempleado']; ?>" />
													<input type="hidden" name="IDestatus" value="<?php echo $IDestatus; ?>" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->

						  
						  
						  
                          <?php } while ($row_contratos = mysql_fetch_assoc($contratos)); ?>
                        <?php } else { ?>
                           <td colpsan="6">No hay empleado con el filtro seleccionado.</td>
                        <?php }  ?>
                     </tbody>
					</table>
                      
                      
                      <p>&nbsp;</p>
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