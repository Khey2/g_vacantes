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
$fecha_mes = date("m");

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
set_time_limit(0);

$query_meses = "SELECT * FROM vac_meses";
mysql_query("SET NAMES 'utf8'");
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

$query_amatriz = "SELECT * FROM vac_matriz";
mysql_query("SET NAMES 'utf8'");
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);


if (isset($_POST['el_anio'])) {$_SESSION['el_anio'] = $_POST['el_anio'];} 
if (!isset($_SESSION['el_anio'])) { $_SESSION['el_anio'] = 2024;}

if (isset($_POST['el_mes'])) {$_SESSION['el_mes'] = $_POST['el_mes'];} 
if (!isset($_SESSION['el_mes'])) { $_SESSION['el_mes'] = $fecha_mes-1;}

if (isset($_POST['la_matriz'])) { foreach ($_POST['la_matriz'] as $matrizes)
	{	$_SESSION['la_matriz'] = implode(", ", $_POST['la_matriz']);} } 
if (!isset($_SESSION['la_matriz'])) { $_SESSION['la_matriz'] = '1,2,3,4,5,6,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,28,29,30';}

if (isset($_POST['el_area'])) {	foreach ($_POST['el_area'] as $areas)
	{	$_SESSION['el_area'] = implode(", ", $_POST['el_area']);}	}  
if (!isset($_SESSION['el_area'])) { $_SESSION['el_area'] = '1,2,3,4,5,6,7,8,9,10,11';}

$el_area = $_SESSION['el_area'];
$la_matriz = $_SESSION['la_matriz'];
$el_anio = $_SESSION['el_anio']; 
$el_mes = $_SESSION['el_mes']; 

$query_puestos = "SELECT capa_avance.*, capa_cursos.nombre_curso, capa_tipos_cursos.tipo_evento, vac_areas.area, vac_matriz.matriz, vac_sucursal.sucursal  FROM capa_avance LEFT JOIN capa_cursos ON capa_avance.IDC_capa_cursos = capa_cursos.IDC_capa_cursos LEFT JOIN capa_tipos_cursos ON capa_cursos.IDC_tipo_curso = capa_tipos_cursos.ID_tipo_evento LEFT JOIN vac_areas ON capa_avance.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON capa_avance.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_sucursal ON capa_avance.IDsucursal = vac_sucursal.IDsucursal WHERE capa_avance.IDarea in ($el_area) AND capa_avance.IDmatriz in ($la_matriz) AND MONTH(capa_avance.fecha_evento) = '$el_mes' AND YEAR(capa_avance.fecha_evento) = '$el_anio'";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_capa = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

$query_area = "SELECT * FROM vac_areas WHERE vac_areas.IDarea < 12"; 
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);

if (!isset($_GET['limite'])) { $LIMTE = " LIMIT 10";} else { $LIMTE = "";}

$query_cargas = "SELECT DISTINCT fecha, COUNT(fecha) AS Registros FROM capa_avance GROUP BY fecha ORDER BY fecha DESC $LIMTE";
$cargas = mysql_query($query_cargas, $vacantes) or die(mysql_error());
$row_cargas = mysql_fetch_assoc($cargas);
$totalRows_cargas = mysql_num_rows($cargas);

$query_cargas2 = "SELECT IDC_capa FROM capa_avance ORDER BY IDC_capa DESC limit 1";
$cargas2 = mysql_query($query_cargas2, $vacantes) or die(mysql_error());
$row_cargas2 = mysql_fetch_assoc($cargas2);
$totalRows_cargas2 = mysql_num_rows($cargas2);

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
  
	$borrado = $_GET['fecha'];
	$deleteSQL = "DELETE FROM capa_avance WHERE fecha ='$borrado'";
  
  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: capa_cargab.php?info=3");
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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/1picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>

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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la carga.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Histórico Capacitación</h6>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="panel-body">
								
								
								
				<form method="POST" action="capa_cargab.php">
					<table class="table">
							<tr>
							<td>
                            Área: <select name="el_area[]"  class="multiselect" multiple="multiple" >
											<?php $cadena1 = $el_area; $array1 = explode(", ", $cadena1);  do { ?>
											<option value="<?php echo $row_area['IDarea']?>"<?php foreach ($array1 as $elarea) { if (!(strcmp($row_area['IDarea'], $elarea))) {echo "SELECTED";} } ?>><?php echo $row_area['area']?></option>
                                               <?php
											  } while ($row_area = mysql_fetch_assoc($area));
											  $rows = mysql_num_rows($area);
											  if($rows > 0) {
												  mysql_data_seek($area, 0);
												  $row_area = mysql_fetch_assoc($area);
											  } ?>
											  </select>
                            </td>
                           <td> Matriz: <select name="la_matriz[]" class="multiselect" multiple="multiple">
											<?php $cadena2 = $la_matriz; $array2 = explode(", ", $cadena2);  do { ?>
										    <option value="<?php echo $row_amatriz['IDmatriz']?>"<?php foreach ($array2 as $lamatriz) { if (!(strcmp($row_amatriz['IDmatriz'], $lamatriz))) {echo "SELECTED";} } ?>><?php echo $row_amatriz['matriz']?></option>
											<?php
											} while ($row_amatriz = mysql_fetch_assoc($amatriz));
											  $rows = mysql_num_rows($amatriz);
											  if($rows > 0) {
												  mysql_data_seek($amatriz, 0);
												  $row_amatriz = mysql_fetch_assoc($amatriz);
											} ?>
                                             </select>
                            </td>
                           <td> Año: <select name="el_anio" class="form-control">
										<option value="2025"<?php if ($el_anio == 2025) {echo "selected=\"selected\"";} ?>>2025</option>
										<option value="2024"<?php if ($el_anio == 2024) {echo "selected=\"selected\"";} ?>>2024</option>
										<option value="2023"<?php if ($el_anio == 2023) {echo "selected=\"selected\"";} ?>>2023</option>
                                        <option value="2022"<?php if ($el_anio == 2022) {echo "selected=\"selected\"";} ?>>2022</option>
                                        <option value="2021"<?php if ($el_anio == 2021) {echo "selected=\"selected\"";} ?>>2021</option>
                                        <option value="2020"<?php if ($el_anio == 2020) {echo "selected=\"selected\"";} ?>>2020</option>
									 </select>
                            </td>
                           <td> Mes: <select name="el_mes" class="form-control">
                                           <option value="1"<?php if ($el_mes == 1) {echo "selected=\"selected\"";} ?>>Enero</option>
                                           <option value="2"<?php if ($el_mes == 2) {echo "selected=\"selected\"";} ?>>Febrero</option>
                                           <option value="3"<?php if ($el_mes == 3) {echo "selected=\"selected\"";} ?>>Marzo</option>
                                           <option value="4"<?php if ($el_mes == 4) {echo "selected=\"selected\"";} ?>>Abril</option>
                                           <option value="5"<?php if ($el_mes == 5) {echo "selected=\"selected\"";} ?>>Mayo</option>
                                           <option value="6"<?php if ($el_mes == 6) {echo "selected=\"selected\"";} ?>>Junio</option>
                                           <option value="7"<?php if ($el_mes == 7) {echo "selected=\"selected\"";} ?>>Julio</option>
                                           <option value="8"<?php if ($el_mes == 8) {echo "selected=\"selected\"";} ?>>Agosto</option>
                                           <option value="9"<?php if ($el_mes == 9) {echo "selected=\"selected\"";} ?>>Septiembre</option>
                                           <option value="10"<?php if ($el_mes == 10) {echo "selected=\"selected\"";} ?>>Octubre</option>
                                           <option value="11"<?php if ($el_mes == 11) {echo "selected=\"selected\"";} ?>>Noviembre</option>
                                           <option value="12"<?php if ($el_mes == 12) {echo "selected=\"selected\"";} ?>>Diciembre</option>
									 </select>
                            <td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button> 
							<a href="plantilla.php" class="btn btn-default">Borrar filtro</a>							
							<button type="button" data-target="#modal_theme_descargas"  data-toggle="modal" class="btn btn-info">Consulta de Cargas</button>  &nbsp;
							<a href="capa_acumulado.php" class="btn btn-danger">Acumulado</a>							
							</td>
					      </tr>
						  <tr><td colspan="3">Fecha de última carga: <b><?php echo date( 'd/m/Y' , strtotime($row_cargas['fecha'])); ?></b>
						  <td colspan="2">Último IDSGRH: <b><?php echo $row_cargas2['IDC_capa']; ?></b></td>
						  </tr>
				    </table>
				</form>


					                <!-- danger modal -->
									<div id="modal_theme_descargas" class="modal fade"  role="dialog" style="z-index: 1400;">
										<div class="modal-dialog-xs">
											<div class="modal-content">
												<div class="modal-header bg-info">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Reporte de Cargas Realizadas</h6>
												</div>
												<div class="modal-body">
												<p>Selecciona la fecha para descargar el reporte en Excel.</p>
												<p>Solo se muestran las últimas 10 cargas. Da clic <a href="capa_cargab.php?limite=1" class="text text-semibold text-danger">aquí</a> para mostrar todas las cargas.</p>
												<p>Clic en botón <span class="text text-info">azul</span> para descargar.</p>
												<p>Clic en botón <span class="text text-danger">rojo</span> para borrar.</p>

												<?php
												$cols = 4;
												$i = 0;
												echo "<table class='table table-stripped'>";
												do {
													if($cols == $i) $i = 0; 
														if($i == 0) { 
															echo "<tr>";
														} ?> <td><b>Fecha:</b><?php echo date('d/m/Y', strtotime($row_cargas['fecha'])); ?> <b>Regs:</b><?php echo $row_cargas['Registros']; ?> &nbsp;  <a class="btn btn-info btn-xs" href="capa_cargab_reporte.php?fecha=<?php echo $row_cargas['fecha']; ?>"><i class="icon icon-file-download2"></i></a>&nbsp; 
														
														<button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#confirmar_borrado<?php echo $row_cargas['fecha']; ?>"><i class="icon icon-trash"></i></button>

														<div id="confirmar_borrado<?php echo $row_cargas['fecha']; ?>" class="modal fade" role="dialog" style="z-index: 1600;">
															<div class="modal-dialog">
																<!-- Modal content-->
																<div class="modal-content">
																<div class="modal-header bg-danger">
																<button type="button" class="close" data-dismiss="modal">&times;</button>
																<h6 class="modal-title">Confirmación de Borrado</h6>
																</div>

																<div class="modal-body">
																	
																	Estas seguro de que quieres borrar la carga del <b><?php echo date('d/m/Y', strtotime($row_cargas['fecha'])); ?></b> con <b><?php echo $row_cargas['Registros']; ?></b> registros?
																	
																</div>      
																<div class="modal-footer">
																<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
																<a href="capa_cargab.php?fecha=<?php echo $row_cargas['fecha']; ?>&borrar=1" class="btn btn-danger" >Si Borrar</a>
																</div>

																</div>
															</div>
														</div>
														
														 </td> <?php 
														if($cols == $i) { 
															echo "</tr>";
														}
														$i++;
													} while ($row_cargas = mysql_fetch_assoc($cargas));
													echo "</table>";
												?>
												
												</p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->


								
					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary">
                          <th>IDSGRH</th>
                          <th>No. Emp.</th>
                          <th>Nombre</th>
                          <th>Puesto</th>
                          <th>Matriz</th>
                          <th>Area</th>
                          <th>Evento</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_puestos > 0) { ?>

                        <?php do { ?>
                          <tr>
                            <td><?php echo $row_capa['IDC_capa']; ?></td>
                            <td><a href="capa_detalle_empleado.php?IDempleado=<?php echo $row_capa['IDempleado']; ?>"><?php echo $row_capa['IDempleado']; ?></a></td>
                            <td><?php echo $row_capa['emp_paterno']." ".$row_capa['emp_materno']." ".$row_capa['emp_nombre']; ?></td>
                            <td><?php echo $row_capa['denominacion']; ?></td>
                            <td><?php echo $row_capa['matriz']; ?></td>
                            <td><?php echo $row_capa['area']; ?></td>
                            <td><a <?php if ($row_capa['IDC_capa_cursos'] == 999) {echo "class='collapsed text-warning'";} ?>
							data-toggle="collapse" href="#collapse-group<?php echo $row_capa['IDC_capa']; ?>">
							<?php if ($row_capa['IDC_capa_cursos'] == 999) {echo $row_capa['nombre_cargado']; } else { echo $row_capa['nombre_curso']; } ?>
							<span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_capa['IDC_capa']; ?>" class="panel-collapse collapse">
								<ul>
								<li><strong>Fecha: </strong><?php echo $row_capa['fecha_evento']; ?></li>
								<li><strong>Calificacion: </strong><?php echo $row_capa['calificacion']; ?></li>
								<li><strong>Tipo: </strong><?php echo $row_capa['tipo_evento']; ?></li>
								<li><strong>Programado: </strong><?php if ($row_capa['IDC_programado'] == 1) {echo "SI";} else {echo "NO";} ?></li>
								</ul>
							</div>
							</td>
                           </tr>                         
						
                		 <?php } while ($row_capa = mysql_fetch_assoc($puestos)); ?>

                         <?php } else { ?>
                         <td colspan="6">Sin empleados con el filtro seleccionado.</td>
                         <?php } ?>
					    </tbody>
				    </table>
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