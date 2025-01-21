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

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

$IDtarea = $_GET['IDtarea'];
$query_meses_inicio = "SELECT DISTINCT ztar_meses.IDmes FROM ztar_meses LEFT JOIN ztar_avances ON MONTH(fecha_esperada) = ztar_meses.IDmes
WHERE ztar_avances.IDtarea = '$IDtarea' ORDER BY ztar_meses.IDmes ASC LIMIT 1";
$meses_inicio = mysql_query($query_meses_inicio, $vacantes) or die(mysql_error());
$row_meses_inicio = mysql_fetch_assoc($meses_inicio);
$primer_mes = $row_meses_inicio['IDmes'];

if(isset($_POST['mi_mes'])) {$mi_mes = $_POST['mi_mes'];} 
else if(isset($_SESSION['mi_mes'])) {$mi_mes = $_SESSION['mi_mes'];} 
else {$mi_mes = $primer_mes;}
$_SESSION['mi_mes'] = $mi_mes;

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_meses = "SELECT DISTINCT ztar_meses.IDmes, ztar_meses.mes, ztar_avances.fecha_esperada, ztar_avances.IDtarea FROM ztar_meses LEFT JOIN ztar_avances ON MONTH(fecha_esperada) = ztar_meses.IDmes WHERE ztar_avances.IDtarea = '$IDtarea' ORDER BY ztar_meses.IDmes ASC";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

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

mysql_select_db($database_vacantes, $vacantes);
$query_tareas = "SELECT ztar_tareas.foto, ztar_tareas.descripcion_larga, ztar_tareas.por_evento, ztar_tareas.meses, ztar_tareas.dia, ztar_tareas.matrizes, ztar_tareas.IDtarea,  ztar_tareas.IDarea_rh,  ztar_tareas.descripcion, ztar_tareas.ponderacion,  ztar_tareas.IDperiodicidad,  ztar_areas_rh.area_rh FROM ztar_areas_rh left JOIN ztar_tareas ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh WHERE IDtarea = '$IDtarea'";
mysql_query("SET NAMES 'utf8'");
$tareas = mysql_query($query_tareas, $vacantes) or die(mysql_error());
$row_tareas = mysql_fetch_assoc($tareas);
$totalRows_tareas = mysql_num_rows($tareas);


mysql_select_db($database_vacantes, $vacantes);
$query_avances = "SELECT ztar_avances.IDavance, ztar_avances.anio, ztar_avances.dias_recorrer, ztar_avances.IDtarea, ztar_avances.IDestatus, ztar_avances.IDmatriz, ztar_avances.instrucciones,  ztar_avances.IDresultado, ztar_avances.descripcion, ztar_avances.progreso_detalle, ztar_avances.fecha, ztar_avances.fecha_esperada, ztar_avances.coments, vac_matriz.matriz, ztar_files.IDfile FROM ztar_avances left JOIN vac_matriz ON vac_matriz.IDmatriz = ztar_avances.IDmatriz left JOIN ztar_files ON ztar_files.IDavance = ztar_avances.IDavance WHERE ztar_avances.IDtarea = '$IDtarea' AND MONTH(fecha_esperada) = '$mi_mes' AND ztar_avances.IDmatriz NOT IN (7,27,5,10) GROUP BY vac_matriz.IDmatriz";
mysql_query("SET NAMES 'utf8'");
$avances = mysql_query($query_avances, $vacantes) or die(mysql_error());
$row_avances = mysql_fetch_assoc($avances);
$totalRows_avances = mysql_num_rows($avances);

mysql_select_db($database_vacantes, $vacantes);
$query_files = "SELECT * FROM ztar_files WHERE IDtarea = '$IDtarea' AND IDmatriz IS NULL";
$files = mysql_query($query_files, $vacantes) or die(mysql_error());
$row_files = mysql_fetch_assoc($files);
$totalRows_files = mysql_num_rows($files);

$el_area = $row_tareas['IDarea_rh'];
$query_area = "SELECT * FROM ztar_areas_rh WHERE IDarea_rh = '$el_area'";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

switch ($mi_mes) {
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<script src="global_assets/js/demo_pages/gallery_library.js"></script>
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el avance.
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
                

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 5))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el avance esperado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 6))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el avance esperado para todas las sucursales.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

					<!-- Detailed task -->
					<div class="row">
						<div class="col-lg-8">

							<!-- Task overview -->
							<div class="panel panel-flat">
								<div class="panel-body">
                                
									<legend class="text-bold">Objetivo:</legend>
									<p><?php echo  KT_escapeAttribute($row_tareas['descripcion']); ?></p>

									<legend class="text-bold">Información Solicitada:</legend>
                                    <p><?php echo $row_avances['descripcion'];?></p>


									<legend class="text-bold">Ficha Técnica:</legend>

                                     <?php if($row_tareas['foto'] > 0){ ?>
									 <a href="drhimg/<?php echo $row_tareas['foto'];?>" data-popup="lightbox">
									 <?php } else { ?>
                                     <a href="img/<?php echo $row_avances['IDtarea'];?>.PNG" data-popup="lightbox">
									 <?php }  ?>
					                 <img src="global_assets/images/placeholders/placeholder_.png" alt="" class="img-rounded img-preview"></a>

                                    <p>&nbsp;</p>

									<div class="table-responsive">
									<table class="table table-condensed">
											<thead>
												<tr>
													<th>Sucursal</th>
													<th>Estatus</th>
													<th>Fecha Límite</th>
													<th>Calificación</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>
                                            
                                       <?php if($totalRows_avances > 0) { ?>   
										<?php $i = 1; 
                                        do { ?>
                                           <tr>
													<td><?php echo $row_avances['matriz'];?></td>
													<td><?php if ($row_avances['IDestatus'] == 0) {echo "Sin Envío"; }
														 else if ($row_avances['IDestatus'] == 2) {echo "Calificado"; } 
														 else if ($row_avances['IDestatus'] == 1 OR $row_avances['IDfile'] > 0) {echo "Con Envío"; }
														 ?>
													</td>
                                                    <td>
									                	<div class="input-group input-group-transparent">
									                		<?php $fecha = date('d/m/Y', strtotime($row_avances['fecha_esperada']. '+'.$row_avances['dias_recorrer'].'day')); 
																  if($row_tareas['por_evento'] == 1) { echo "Por evento";} 
															else  if($row_avances['fecha_esperada'] > 0) { echo $fecha;} 
															else {echo "-";}?>
									                	</div>
													</td>
                                                    <td><?php
									$la_tarea = $row_avances['IDtarea'];
									$query_ponds = "SELECT * FROM ztar_tareas WHERE IDtarea = $la_tarea";
									$ponds = mysql_query($query_ponds, $vacantes) or die(mysql_error());
									$row_ponds = mysql_fetch_assoc($ponds);

									  switch ($row_avances['IDresultado']) {
										case '':  $el_resultado = "Pendiente";  $el_resultado_i = "label-info";   break;     
										case $row_ponds['IDsob']:  $el_resultado = "Sobresaliente";  $el_resultado_i = "label-success";     break;     
										case $row_ponds['IDsat']:  $el_resultado = "Satisfactorio";  $el_resultado_i = "label-primary";   break;    
										case $row_ponds['IDdef']:  $el_resultado = "Deficiente"; $el_resultado_i = "label-danger";   break;    
										case 0:  $el_resultado = "En proceso / Incompleto"; $el_resultado_i = "label-info";   break;    
										  }
											?><a class="label <?php echo $el_resultado_i;  ?>"><?php echo $el_resultado;  ?></a></td>
													<td>
                                           <a href="objetivos_d_editar.php?IDavance=<?php echo $row_avances['IDavance']; ?>&IDtarea=<?php echo $IDtarea; ?>" class="btn-warning btn-sm">Editar</a>
                                        <?php $i = $i+1;  ?>
                                           &nbsp;&nbsp; <a href="objetivos_d_evaluar.php?IDavance=<?php echo $row_avances['IDavance']; ?>" class="btn-success btn-sm">Evaluar</a>
                                     
                                            
                                            </td>
												</tr>
					    			<?php } while ($row_avances = mysql_fetch_assoc($avances));  ?>
                          		   <?php } else { ?>   
													<tr>
													<td colspan="5"><span class="text text-semibold text-info">Mostrando el mes de <?php echo $elmes; ?>, da clic en el botón "Cambiar Mes" para actualizar.</td>
                                                    </tr>
                          		   <?php }  ?>   
                                                    </tbody>
										</table>
									</div>
								</div>

							</div>
							<!-- /task overview -->

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
										</tr>
                                        <tr>
                                        	<td><i class="icon-calendar2 position-left"></i>Mes:</td>
											<td class="text-right"><?php echo $elmes;?></td>
										</tr>
									</tbody>
								</table>
							</div>
							<!-- /task details -->
                            
                            							<!-- Task details -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-office position-left"></i>Mes</h6>
								</div>
								<table class="table table-borderless table-xs content-group-sm">
									<tbody>
                                        <tr>
											<td>
                                            <div> Selecciona el mes a consultar: </div>  <p>&nbsp;</p>
                                            <form method="POST" action="objetivos_d_detalle.php?IDtarea=<?php echo $_GET['IDtarea'];?>">
                                            <div class="col-lg-9"> <select name="mi_mes" class="form-control">
											<?php do { ?>
                                               <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $mi_mes))) {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']?></option>
                                               <?php
											  } while ($row_meses = mysql_fetch_assoc($meses));
											  $rows = mysql_num_rows($meses);
											  if($rows > 0) {
												  mysql_data_seek($meses, 0);
												  $row_meses = mysql_fetch_assoc($meses);
											  } ?> </select>
                                              </div>
                                            <div class="col-lg-3">
                                            <button type="submit" class="btn btn-primary">Cambiar</button>
										 	</div>
                                            </form>
                                            </td>
                                         </tr>
									</tbody>
								</table>
							</div>
							<!-- /task details -->


							<!-- Task details -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-office position-left"></i>Documentos</h6>
								</div>
								<table class="table table-borderless table-xs content-group-sm">
									<tbody>
									<?php if ($totalRows_files > 0) { ?>                                    
									<?php do { ?>                                    
                                    <tr>
											<td><?php echo $row_files['file']; ?></td>
											<td><a class="btn btn-success" href="sed_rh_files/<?php echo $row_files['file']; ?>">Descargar</a></td>
                                    </tr>
                                     <?php } while ($row_files = mysql_fetch_assoc($files)); ?>
                                    <tr>
											<td><a href="objetivos_d_edita.php?IDtarea=<?php echo $IDtarea; ?>" class="btn btn-success">Agregar</a></td>
                                    </tr>
 									<?php } else{ ?>     
                                    <tr>
									<td></td>
									<td></td>
                                    </tr>
                                    <tr>
									<td><a href="objetivos_d_edita.php?IDtarea=<?php echo $IDtarea; ?>" class="btn btn-success">Agregar</a></td>
                                    </tr>
 									<?php } ?>                                    
									</tbody>
								</table>
							</div>
							<!-- /task details -->


							<!-- Task details -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-git-commit position-left"></i>Contacto Sucursal</h6>
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
												<?php echo $row_area['usuario_responsable'];?>
												<div class="media-annotation"><?php echo $row_area['usuario_correo'];?></div>
												<div class="media-annotation"><?php echo $row_area['usuario_telefono'];?></div>
											</div>
										</li>

									</ul>
								</div>
							</div>
							<!-- /revisions -->


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