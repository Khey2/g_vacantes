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
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$mi_fecha =  date('Y/m/d');


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

$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$el_mes = date("m"); 
set_time_limit(0);


// mes y semana
$fecha = date("Y-m-d"); // la fecha actual
$fecha_tope = date("d-m-Y",strtotime($fecha."- 30 days"));

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

if (isset($_POST['la_matriz'])) {  $_SESSION['la_matriz'] = $_POST['la_matriz']; } 
elseif (!isset($_SESSION['la_matriz'])) {  $_SESSION['la_matriz'] =  $IDmatriz; } 
$la_matriz = $_SESSION['la_matriz'];

if (isset($_POST['el_mess'])) {  $_SESSION['el_mess'] = $_POST['el_mess']; } 
elseif (!isset($_SESSION['el_mess'])) {  $_SESSION['el_mess'] =  $el_mes; } 
$el_mess = $_SESSION['el_mess']; 

if (isset($_POST['el_anio'])) {  $_SESSION['el_anio'] = $_POST['el_anio']; } 
elseif (!isset($_SESSION['el_anio'])) {  $_SESSION['el_anio'] =  $anio; } 
$el_anio = $_SESSION['el_anio'];


if ($el_mess == 0){ $losmeses = " AND rel_lab_asesorias.mes IN (1,2,3,4,5,6,7,8,9,10,11,12) "; } else { $losmeses = " AND rel_lab_asesorias.mes = $el_mess "; }

mysql_select_db($database_vacantes, $vacantes);
$query_asesorias = "SELECT rel_lab_asesorias.IDasesoria, rel_lab_asesorias.IDestatus, rel_lab_asesorias.IDempleado, rel_lab_asesorias.fecha_antiguedad, rel_lab_asesorias.anio, rel_lab_asesorias.mes, rel_lab_asesorias.rfc, rel_lab_asesorias.emp_paterno, rel_lab_asesorias.emp_materno, rel_lab_asesorias.emp_nombre, rel_lab_asesorias.denominacion, rel_lab_asesorias.IDmatriz, rel_lab_asesorias.IDpuesto, rel_lab_asesorias.IDarea, rel_lab_asesorias.IDsucursal, rel_lab_asesorias.anio FROM rel_lab_asesorias WHERE rel_lab_asesorias.IDmatriz = '$la_matriz' AND rel_lab_asesorias.anio = '$el_anio' ".$losmeses." AND rel_lab_asesorias.IDestatus = 1"; 
mysql_query("SET NAMES 'utf8'"); 
$asesorias = mysql_query($query_asesorias, $vacantes) or die(mysql_error());
$row_asesorias = mysql_fetch_assoc($asesorias);
$totalRows_asesorias = mysql_num_rows($asesorias);

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

$query_los_meses = "SELECT * FROM vac_meses";
$los_meses = mysql_query($query_los_meses, $vacantes) or die(mysql_error());
$row_los_meses = mysql_fetch_assoc($los_meses);
$totalRows_los_meses = mysql_num_rows($los_meses);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$IDasesoria_n = $_POST["IDasesoria_n"]; 
	$IDestatus_p = $_POST["IDestatus"]; 
	$query1 = "UPDATE rel_lab_asesorias SET IDestatus = '$IDestatus_p' WHERE rel_lab_asesorias.IDasesoria = '$IDasesoria_n'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: rel_lab.php?info=3"); 	
}

if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
	$IDasesoria_b = $_GET["IDasesoria"]; 
	$query1 = "UPDATE rel_lab_asesorias SET IDestatus = 0 WHERE rel_lab_asesorias.IDasesoria = '$IDasesoria_b'"; 
	$resultado = mysql_query($query1) or die(mysql_error());  
	//redirecto
	header("Location: rel_lab.php?info=4"); 	
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
                        <?php if(isset($_GET['info']) && $_GET['info'] == 3) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha cambiado el estatus de forma correcta.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if(isset($_GET['info']) && $_GET['info'] == 4) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la Disciplina Progresiva.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la Disciplina Progresiva.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la Disciplina Progresiva.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Disciplina Progresiva</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group">Instrucciones:</br>
                                    1. Utiliza el filtro para identificar las disciplinas por sucursal.</br>
                                    2. Da clic en agregar para identificar al empleado activo que se le aplicará la Disciplina Progresiva.</br>
                                    3. Da clic en los eventos indicados en cada etapa para agregar eventos o ver el detalle de cada uno al dar clic en la fecha del evento.</br>
                                    4. Los eventos son progresivos, debes capturarlos en orden.</br>
                                    5. En Estatus puedes cambiar a Cerrada o Abierta.<p>

                             <!-- Basic text input -->

					<form method="POST" action="rel_lab.php" class="form-horizontal">
					<fieldset class="content-group">
									<div class="col-lg-3">
										<select class="form-control" name="la_matriz">
										<?php do { ?>
										<option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $la_matriz)))
										{echo "selected=\"selected\"";} ?>><?php echo $row_lmatriz['matriz']?></option>
										<?php } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
										$rows = mysql_num_rows($lmatriz);
										if($rows > 0) {  mysql_data_seek($lmatriz, 0);
										$row_lmatriz = mysql_fetch_assoc($lmatriz); } ?>
										 </select>
						    		</div>
									<div class="col-lg-1">
										<select class="form-control" name="el_anio">
										<option value="2025"<?php if ( $el_anio == 2025 ) {echo "selected=\"selected\"";} ?>>2025</option>
										<option value="2024"<?php if ( $el_anio == 2024 ) {echo "selected=\"selected\"";} ?>>2024</option>
										<option value="2023"<?php if ( $el_anio == 2023 ) {echo "selected=\"selected\"";} ?>>2023</option>
										 </select>
						    		</div>
									<div class="col-lg-1">
										<select class="form-control" name="el_mess">
										<option value="0"<?php if ( $el_anio == 0 ) {echo "selected=\"selected\"";} ?>>TODOS</option>
										<?php do { ?>
										<option value="<?php echo $row_los_meses['IDmes']?>"<?php if (!(strcmp($row_los_meses['IDmes'], $el_mess)))
										{echo "selected=\"selected\"";} ?>><?php echo $row_los_meses['mes']?></option>
										<?php } while ($row_los_meses = mysql_fetch_assoc($los_meses));
										$rows = mysql_num_rows($lmatriz);
										if($rows > 0) {  mysql_data_seek($los_meses, 0);
										$row_los_meses = mysql_fetch_assoc($los_meses); } ?>
										 </select>
						    		</div>
                            		<div class="col-lg-3">
										<button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button>										
										<a class="btn btn-success" href="rel_lab_asesorias.php">Agregar</a>&nbsp;&nbsp;&nbsp;
										<a class="btn btn-info" href="rel_lab_asesorias_reporte.php?mes=<?php echo $el_mess; ?>&anio=<?php echo $el_anio; ?>&IDmatriz=<?php echo $la_matriz; ?>">Descargar Reporte</a>
									</div>
					</fieldset>
					</form>


					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary">
							  <th>IDempleado</th>
							  <th>Nombre</th>
							  <th>Puesto</th>
							  <th>Mes/Año</th>
							  <th class="text text-center">1. Reorientación<br/>Eficaz</th>
							  <th class="text text-center">2. Asesoría para<br/>Mejorar</th>
							  <th class="text text-center">3. Acta <br/>Administrativa</th>
							  <th>Acciones</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_asesorias > 0) { ?>

                        <?php do { 

						$IDasesoria = $row_asesorias['IDasesoria'];
						$IDempleado = $row_asesorias['IDempleado'];
						mysql_select_db($database_vacantes, $vacantes);
						$query_asesorias_etapa1 = "SELECT * FROM rel_lab_etapas WHERE IDasesoria = '$IDasesoria' AND IDetapa = 1";
						$asesorias_etapa1 = mysql_query($query_asesorias_etapa1, $vacantes) or die(mysql_error());
						$row_asesorias_etapa1 = mysql_fetch_assoc($asesorias_etapa1);
						$totalRows_asesorias_etapa1 = mysql_num_rows($asesorias_etapa1);
						
						$query_asesorias_etapa2 = "SELECT * FROM rel_lab_etapas WHERE IDasesoria = '$IDasesoria' AND IDetapa = 2";
						$asesorias_etapa2 = mysql_query($query_asesorias_etapa2, $vacantes) or die(mysql_error());
						$row_asesorias_etapa2 = mysql_fetch_assoc($asesorias_etapa2);
						$totalRows_asesorias_etapa2 = mysql_num_rows($asesorias_etapa2);

						$query_asesorias_etapa3 = "SELECT * FROM rel_lab_etapas WHERE IDasesoria = '$IDasesoria' AND IDetapa = 3";
						$asesorias_etapa3 = mysql_query($query_asesorias_etapa3, $vacantes) or die(mysql_error());
						$row_asesorias_etapa3 = mysql_fetch_assoc($asesorias_etapa3);
						$totalRows_asesorias_etapa3 = mysql_num_rows($asesorias_etapa3);


						?>
                          <tr>
                            <td><?php echo $row_asesorias['IDempleado']; ?></td>
                            <td><?php echo $row_asesorias['emp_paterno']." ".$row_asesorias['emp_materno']." ".$row_asesorias['emp_nombre']; ?></td>
                            <td><?php echo $row_asesorias['denominacion']; ?></td>
                            <td><?php echo $row_asesorias['mes']."/".$el_anio; ?></td>
							<td>
							<a class="collapsed text-orange text-semibold" data-toggle="collapse" href="#collapse-group<?php echo $row_asesorias['IDasesoria']; ?>E1"><?php echo $totalRows_asesorias_etapa1; ?> evento(s)<span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_asesorias['IDasesoria']; ?>E1" class="panel-collapse collapse">
								<ul class="list list-icons">
							<?php if ($totalRows_asesorias_etapa1 > 0) { ?>
							<?php do { ?>
								<li><i class="icon-files-empty2 text-orange position-left"></i><a class="text-orange" href="rel_lab_etapas.php?IDetapa=1&IDasesoria=<?php echo $row_asesorias['IDasesoria']; ?>&IDasesoria_etapa=<?php echo $row_asesorias_etapa1['IDasesoria_etapa']; ?>&IDempleado=<?php echo $row_asesorias['IDempleado']; ?>"><?php echo date( 'd/m/Y', strtotime($row_asesorias_etapa1['fecha_inicio'])); ?></a></li>
							<?php } while ($row_asesorias_etapa1 = mysql_fetch_assoc($asesorias_etapa1)); ?>
							<?php } ?>
								<li><i class="icon-file-plus2 text-success position-left"></i><a class="text-success" href="rel_lab_etapas.php?IDetapa=1&IDempleado=<?php echo $row_asesorias['IDempleado']; ?>&IDasesoria=<?php echo $row_asesorias['IDasesoria']; ?>">Agregar</a></li>
								</ul>
							</div>
							</td>
							 <td>
							<a class="collapsed text-warning text-semibold" data-toggle="collapse" href="#collapse-group<?php echo $row_asesorias['IDasesoria']; ?>E2"><?php echo $totalRows_asesorias_etapa2; ?> evento(s)<span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_asesorias['IDasesoria']; ?>E2" class="panel-collapse collapse">
								<ul class="list list-icons">
							<?php if ($totalRows_asesorias_etapa2 > 0) { ?>
							<?php do { ?>
								<li><i class="icon-files-empty2 text-warning position-left"></i><a class="text-warning" href="rel_lab_etapas.php?IDetapa=2&IDasesoria=<?php echo $row_asesorias['IDasesoria']; ?>&IDasesoria_etapa=<?php echo $row_asesorias_etapa2['IDasesoria_etapa']; ?>&IDempleado=<?php echo $row_asesorias['IDempleado']; ?>"><?php echo date( 'd/m/Y', strtotime($row_asesorias_etapa2['fecha_inicio'])); ?></a></li>
							<?php } while ($row_asesorias_etapa2 = mysql_fetch_assoc($asesorias_etapa2)); ?>
							<?php } ?>
							<?php if ($totalRows_asesorias_etapa1 > 0) { ?>
								<li><i class="icon-file-plus2 text-success position-left"></i><a class="text-success" href="rel_lab_etapas.php?IDetapa=2&IDempleado=<?php echo $row_asesorias['IDempleado']; ?>&IDasesoria=<?php echo $row_asesorias['IDasesoria']; ?>">Agregar</a></li>
								</ul>
							<?php } ?>
							</div>
							</td>
							 <td>
							<a class="collapsed text-danger text-semibold" data-toggle="collapse" href="#collapse-group<?php echo $row_asesorias['IDasesoria']; ?>E3"><?php echo $totalRows_asesorias_etapa3; ?> evento(s)<span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_asesorias['IDasesoria']; ?>E3" class="panel-collapse collapse">
								<ul class="list list-icons">
							<?php if ($totalRows_asesorias_etapa3 > 0) { ?>
							<?php do { ?>
								<li><i class="icon-files-empty2 text-danger position-left"></i><a class="text-danger" href="rel_lab_etapas.php?IDetapa=3&IDasesoria=<?php echo $row_asesorias['IDasesoria']; ?>&IDasesoria_etapa=<?php echo $row_asesorias_etapa3['IDasesoria_etapa']; ?>&IDempleado=<?php echo $row_asesorias['IDempleado']; ?>"><?php echo date( 'd/m/Y', strtotime($row_asesorias_etapa3['fecha_inicio'])); ?></a></li>
							<?php } while ($row_asesorias_etapa3 = mysql_fetch_assoc($asesorias_etapa3)); ?>
							<?php } ?>
							<?php if ($totalRows_asesorias_etapa2 > 0) { ?>
								<li><i class="icon-file-plus2 text-success position-left"></i><a class="text-success" href="rel_lab_etapas.php?IDetapa=3&IDempleado=<?php echo $row_asesorias['IDempleado']; ?>&IDasesoria=<?php echo $row_asesorias['IDasesoria']; ?>">Agregar</a></li>
								</ul>
							<?php } ?>
							</div>
							</td>
							 <td>
							 <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-xs btn-primary">Estatus</button>
							 <button type="button" data-target="#modal_theme_danger2"  data-toggle="modal" class="btn btn-xs btn-danger">Borrar</button>
							 </td>
                           </tr>

					                <!-- danger modal -->
									<div id="modal_theme_danger" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-info">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cambiar estatus</h6>
												</div>
												<div class="modal-body">
																			
														<form action="rel_lab.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Estatus:</label>
															  <div class="col-lg-9">
															<select name="IDestatus" id="IDestatus" class="form-control" >
																<option value="1"<?php if (!(strcmp($row_asesorias['IDestatus'], 1))) {echo "selected=\"selected\"";} ?>>Activo</option>
																<option value="2"<?php if (!(strcmp($row_asesorias['IDestatus'], 2))) {echo "selected=\"selected\"";} ?>>Cerrado</option>
															</select>
															 </div>
														  </div>
														  <!-- /basic text input -->

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-info">Actualizar</button> 
													<input type="hidden" name="MM_insert" value="form1" />
													<input type="hidden" name="IDasesoria_n" value="<?php echo $row_asesorias['IDasesoria']; ?>" />
												</div>
														 </form>
											</div>
										</div>
									</div>
									<!-- danger modal -->
									
									<!-- danger modal -->
									<div id="modal_theme_danger2" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de borrado</h6>
												</div>
												<div class="modal-body">
												<p>¿Estas seguro que quieres borrar la Disciplina Progresiva?</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-danger" href="rel_lab.php?IDasesoria=<?php echo $row_asesorias['IDasesoria']; ?>&borrar=1">Si borrar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->



						   
                          <?php } while ($row_asesorias = mysql_fetch_assoc($asesorias)); ?>
                         <?php } else { ?>
                         <tr><td colspan="9">Sin empleados con el filtro seleccionado.</td></tr>
                         <?php } ?>
					    </tbody>
				    </table>
				</div>                   
	</div>
	</div>
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