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

// mes y semana
$el_mes = date("m")+1;
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

  switch ($el_mes) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
      }

$IDtarea = $_GET['IDtarea'];
mysql_select_db($database_vacantes, $vacantes);
$query_tareas = "SELECT ztar_tareas.IDtarea, ztar_tareas.IDarea_rh,     ztar_tareas.descripcion, ztar_tareas.ponderacion,  ztar_tareas.IDperiodicidad,    ztar_areas_rh.area_rh FROM ztar_areas_rh left JOIN ztar_tareas ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh WHERE IDtarea = '$IDtarea'";
mysql_query("SET NAMES 'utf8'");
$tareas = mysql_query($query_tareas, $vacantes) or die(mysql_error());
$row_tareas = mysql_fetch_assoc($tareas);
$totalRows_tareas = mysql_num_rows($tareas);

mysql_select_db($database_vacantes, $vacantes);
$query_avances = "SELECT * FROM ztar_avances WHERE IDtarea = '$IDtarea' AND IDmatriz = '$IDmatriz'";
$avances = mysql_query($query_avances, $vacantes) or die(mysql_error());
$row_avances = mysql_fetch_assoc($avances);
$totalRows_avances = mysql_num_rows($avances);

mysql_select_db($database_vacantes, $vacantes);
$query_files = "SELECT * FROM ztar_files WHERE IDtarea = '$IDtarea' AND IDmatriz = '$IDmatriz'";
$files = mysql_query($query_files, $vacantes) or die(mysql_error());
$row_files = mysql_fetch_assoc($files);
$totalRows_files = mysql_num_rows($files);

mysql_select_db($database_vacantes, $vacantes);
$query_files2 = "SELECT * FROM ztar_files WHERE ztar_files.IDtarea = '$IDtarea' AND ztar_files.IDmatriz IS NULL";
$files2 = mysql_query($query_files2, $vacantes) or die(mysql_error());
$row_files2 = mysql_fetch_assoc($files2);
$totalRows_files2 = mysql_num_rows($files2);
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/tasks_list.js"></script>
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
				<div class="content">
                
                
                						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el avance.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el avance.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el avance.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Detailed task -->
					<div class="row">
						<div class="col-lg-8">

							<!-- Task overview -->
							<div class="panel panel-flat">
								<div class="panel-heading mt-5">
									<h1 class="panel-title"><?php echo $IDtarea . ": " . $row_tareas['descripcion'] . " (" . $row_tareas['area_rh'] . ")."; ?></h1>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="panel-body">

									<legend class="text-bold">Parámetros de evaluación del objetivo</legend>



							<!-- Task overview -->
							  <div>

									<legend class="text-bold">Documentos / Formatos solicitados</legend>

									<div>
                                    	<div>
										<table class="table table-framed table-condensed">
											<thead>
												<tr>
													<th>#</th>
													<th>Documento</th>
													<th>Fecha</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>
										<?php if($totalRows_files2 > 0) { ?>
								<?php do { ?>
												<tr>
													<td><?php echo $row_files2['IDfile']; ?></td>
													<td><?php echo $row_files2['file']; ?></td>
													<td>
									                	<div class="input-group input-group-transparent">
									                		<?php $fecha = date('d/m/Y', strtotime($row_files2['fecha']));
															if($row_files2['fecha'] > 0) { echo $fecha;} else {echo "-";}?>
									                	</div>
													</td>
													<td>
                                                    <a href="sed_rh_files/<?php echo $row_files2['file']; ?>" class="btn btn-success">Descargar</a>
                                                    </td>
												</tr>
                                                
			   				    <?php } while ($row_files2 = mysql_fetch_assoc($files2)); ?>
									  <?php } else { ?>
                                      <tr><td colspan="4">Sin Documentos enviados.</td></tr>
							    <?php } ?>
												</tbody>
										</table>

									</div>
									</div>

							</div>
							<!-- /task overview -->
                                    
                                    
                                 <p>&nbsp;</p>


									<legend class="text-bold">Avances solicitados:</legend>
                                    <p>Da clic en Reportar para capturar los avances de cada uno de los entregables.</p>
									<div class="table-responsive content-group">
										<table class="table table-framed">
											<thead>
												<tr>
													<th style="width: 100px;">#</th>
													<th class="col-xs-2">Fecha límite</th>
													<th>Información requerida</th>
													<th>Estatus</th>
													<th>Resultado</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>
										<?php $i = 1; 
                                        do { ?>
												<tr>
													<td><?php echo $i . " de " . $row_tareas['avances_esperados']; ?></td>
													<td>
									                	<div class="input-group input-group-transparent">
									                		<?php $fecha = date('d/m/Y', strtotime($row_avances['fecha_esperada']));
															if($row_avances['fecha_esperada'] > 0) { echo $fecha;} else {echo "No aplica";}?>
									                	</div>
													</td>
													<td><?php echo $row_avances['descripcion']; ?></td>
													<td><?php
									  switch ($row_avances['IDestatus']) {
										case 0:  $el_estatus = "Pendiente";      break;     
										case 1:  $el_estatus = "Reportado";      break;     
										case 2:  $el_estatus = "Pendiente";    break;    
										  }
											echo $el_estatus; ?></td>
													<td><?php
									  switch ($row_avances['IDresultado']) {
										case '':  $el_resultado = "Pendiente";  $el_resultado_i = "label-info";   break;     
										case 3:  $el_resultado = "Sobresaliente";  $el_resultado_i = "label-success";     break;     
										case 2:  $el_resultado = "Satisfactorio";  $el_resultado_i = "label-primary";   break;    
										case 1:  $el_resultado = "Deficiente"; $el_resultado_i = "label-danger";   break;    
										case 0:  $el_resultado = "No aplica"; $el_resultado_i = "label-danger";   break;    
										  }
											?><a class="label <?php echo $el_resultado_i;  ?>"><?php echo $el_resultado;  ?></a></td>
													<td>
                                                    <?php if($row_avances['IDestatus'] == NULL) { ?>
                                                    <a href="objetivos_b_detalle.php?IDavance=<?php echo $row_avances['IDavance']; ?>" class="btn btn-info">Reportar</a>
                                                    <?php } else { ?>
                                                    <a href="objetivos_b_detalle.php?IDavance=<?php echo $row_avances['IDavance']; ?>" class="btn btn-info">Actualizar</a>
													 
                                                     </td>
                                                    <?php } ?>
												</tr>
					    <?php $i = $i+1;  ?>
						<?php } while ($row_avances = mysql_fetch_assoc($avances)); ?>
												</tbody>
										</table>
									</div>
                                     <a href="objetivos_b_agregar.php?IDtarea=<?php echo $IDtarea; ?>" class="btn btn-info">Agregar avance no programado</a>
								</div>

							</div>
							<!-- /task overview -->

						</div>

						<div class="col-lg-4">


							<!-- Task details -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-files-empty position-left"></i>Detalles</h6>
									<div class="heading-elements">
				                	</div>
								</div>

								<table class="table table-borderless table-xs content-group-sm">
									<tbody>
										<tr>
											<td><i class="icon-briefcase position-left"></i> Área:</td>
											<td class="text-right"><span class="pull-right"><a><?php echo $row_tareas['area_rh']; ?></a></span></td>
										</tr>
										<tr>
											<td><i class="icon-circles2 position-left"></i> Ponderación:</td>
											<td class="text-right"><?php echo $row_tareas['ponderacion']; ?>% </td>
										</tr>
										<tr>
											<td><i class="icon-alarm-check position-left"></i>Periodicidad:</td>
											<td class="text-right"><?php 
												  switch ($row_tareas['IDperiodicidad']) {
												case 1:  $periodicidad = "Semanal";      break;     
												case 2:  $periodicidad = "Quincenal";    break;    
												case 3:  $periodicidad = "Mensual";      break;    
												case 4:  $periodicidad = "Bimestral";      break;    
												case 5:  $periodicidad = "Trimestral";       break;    
												case 6:  $periodicidad = "Semestral";      break;    
												case 7:  $periodicidad = "Por evento";      break;    
												  }
											echo  $periodicidad; ?></td>
										</tr>
										<tr>
											<td><i class="icon-list-numbered position-left"></i> Avances Esperados:</td>
											<td class="text-right"><?php echo $row_tareas['avances_esperados']; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
							<!-- /task details -->





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
<?php
mysql_free_result($variables);

mysql_free_result($tareas);
?>