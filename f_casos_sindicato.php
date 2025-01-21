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
$mi_fecha =  date('Y/m/d');

if (isset($_GET['IDperiodo'])) {$IDperiodo = $_GET['IDperiodo'];} 
elseif (isset($_SESSION['IDperiodo'])) {$IDperiodo = $_SESSION['IDperiodo'];} 
else {$IDperiodo = $row_variables['IDperiodo'];}

$_SESSION['IDperiodo'] = $IDperiodo;

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
$query_casos = "SELECT * FROM casos_sindicato WHERE IDempleado = $el_usuario";
mysql_query("SET NAMES 'utf8'");
$casos = mysql_query($query_casos, $vacantes) or die(mysql_error());
$row_casos = mysql_fetch_assoc($casos);
$totalRows_casos = mysql_num_rows($casos);

$query_areas = "SELECT * FROM vac_areas WHERE IDarea < 12";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

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
    <!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

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
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	
	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5Sin2.js"></script>
	<script src="global_assets/js/demo_pages/xpicker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>

    <script src="assets/js/app.js"></script>
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
						Atención de Inquietudes
                    </h1>

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
									<h6 class="panel-title text-semibold">Atención de Inquietudes</h6>
								</div>

								<div class="media panel-body no-margin">
									<div class="media-body">
										
                                    
					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary">
							  <th>No. Caso</th>
							  <th class="text text-center">Asunto General</th>
							  <th>Fecha captura</th>
							  <th>Fecha solicitada</th>
							  <th class="text text-center">Estatus</th>
							  <th class="text text-center">Seguimiento(s)</th>
							  <th>Acciones</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php if ($totalRows_casos > 0) { ?>

                        <?php do { 

						$IDsindicato = $row_casos['IDsindicato'];
						mysql_select_db($database_vacantes, $vacantes);
						$query_casos_seguimientos = "SELECT * FROM casos_sindicato_seguimientos WHERE IDsindicato = $IDsindicato AND IDestatus_seguimiento = 1";
						$casos_seguimientos = mysql_query($query_casos_seguimientos, $vacantes) or die(mysql_error());
						$row_casos_seguimientos = mysql_fetch_assoc($casos_seguimientos);
						$totalRows_casos_seguimientos = mysql_num_rows($casos_seguimientos);

						mysql_select_db($database_vacantes, $vacantes);
						$query_casos_responsables = "SELECT vac_usuarios.usuario_nombre, vac_usuarios.usuario_parterno, vac_usuarios.usuario_materno, casos_responsables.IDtipo, casos_responsables.IDusuario, vac_puestos.denominacion FROM casos_responsables LEFT JOIN vac_usuarios ON casos_responsables.IDusuario = vac_usuarios.IDusuario LEFT JOIN vac_puestos ON vac_usuarios.IDusuario_puesto = vac_puestos.IDpuesto WHERE IDsindicato = $IDsindicato";
						$casos_responsables = mysql_query($query_casos_responsables, $vacantes) or die(mysql_error());
						$row_casos_responsables = mysql_fetch_assoc($casos_responsables);
						$totalRows_casos_responsables = mysql_num_rows($casos_responsables);

						?>

                          <tr>
                            <td><?php echo $row_casos['IDsindicato']; ?></td>
                            <td><?php echo $row_casos['asunto']; ?></td>
                            <td><?php echo date( 'd/m/Y' , strtotime($row_casos['fecha_inicio']))?></td>
                            <td><?php echo date( 'd/m/Y' , strtotime($row_casos['fecha_esperada']))?>
							
							
							
							<?php if (strtotime($mi_fecha) > strtotime($row_casos['fecha_esperada']) AND $row_casos['IDestatus'] != 3) echo "<i style='font-size:10px;' class='icon-warning text-danger'></i>"; ?>
							
							
							</td>
                            <td><?php if ($row_casos['IDestatus'] == 1) { echo "En proceso"; } 
								 else if ($row_casos['IDestatus'] == 2) { echo "Atendido"; } 
								 else { echo "Sin Estatus"; } ?></td>
							<td>
							<a class="collapsed text-info text-semibold" data-toggle="collapse" href="#collapse-group<?php echo $row_casos['IDsindicato']; ?>E1"><?php echo $totalRows_casos_seguimientos; ?> seguimiento(s)<span class="caret"></span></a>
							<div id="collapse-group<?php echo $row_casos['IDsindicato']; ?>E1" class="panel-collapse collapse">
								<ul class="list list-icons">
							<?php if ($totalRows_casos_seguimientos > 0) { ?>
							<?php do { ?>
								<li><i class="icon-files-empty2 text-info position-left"></i><a class="text-info" href="casos_sindicato_seguimientos.php?IDsindicato=<?php echo $row_casos['IDsindicato'];?>&IDsindicato_seguimientos=<?php echo $row_casos_seguimientos['IDsindicato_seguimientos']; ?>"><?php echo date( 'd/m/Y', strtotime($row_casos_seguimientos['fecha_reporte'])); ?></a></li>
							<?php } while ($row_casos_seguimientos = mysql_fetch_assoc($casos_seguimientos)); ?>
							<?php } else { ?>
								<li>-</li>
							<?php } ?>	
								</ul>
							</div>
							</td>
							 <td>
							 <button type="button" data-target="#modal_theme_danger2<?php echo $row_casos['IDsindicato']; ?>"  data-toggle="modal" class="btn btn-primary">Descripción</button>
							 <button type="button" data-target="#modal_theme_danger4<?php echo $row_casos['IDsindicato']; ?>"  data-toggle="modal" class="btn btn-info">Responsables</button>						
							<?php if ($row_usuario['IDempleado'] == $row_casos['IDempleado']) { ?>
							<a href="f_casos_sindicato_edit.php?IDsindicato=<?php echo $row_casos['IDsindicato']; ?>" class="btn btn-success">Editar</a>	


							<?php if ($totalRows_casos_responsables > 0 OR $row_casos['IDestatus'] == 2) { ?>
							<button type="button" data-target="#modal_theme_danger3<?php echo $row_casos['IDsindicato']; ?>"  data-toggle="modal" class="btn btn-warning">Cerrar</button>
							<?php } ?>
							<?php } ?>
							</td>
                           </tr>

									<!-- danger modal -->
									<div id="modal_theme_danger2<?php echo $row_casos['IDsindicato']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-primary">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Descripción del Caso</h6>
													
												</div>
												<div class="modal-body">
												<p>A continuación se muestra la descripción de tu caso:</p>	
												<?php echo $row_casos['descripcion']; ?><br /></p>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-success" href="f_casos_sindicato_edit.php?IDsindicato=<?php echo $row_casos['IDsindicato']; ?>">Editar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->
						   
									<!-- danger modal -->
									<div id="modal_theme_danger4<?php echo $row_casos['IDsindicato']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-info">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Responsables</h6>
												</div>
												<div class="modal-body">
												<p>A continuación se muestran los responsables asignados a tu caso:</p>
												
												<?php if ($totalRows_casos_responsables > 0) {  ?>
												<?php do {  ?>

												<?php echo $row_casos_responsables['usuario_nombre']." ".$row_casos_responsables['usuario_parterno']." (".$row_casos_responsables['denominacion'].")";  ?><br /> 
												
												<?php } while ($row_casos_responsables = mysql_fetch_assoc($casos_responsables)); ?>
												<?php }  ?>
												
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->

									<!-- danger modal -->
									<div id="modal_theme_danger3<?php echo $row_casos['IDsindicato']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-warning">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cierre de Caso</h6>
												</div>
												<div class="modal-body">
											
												
												<form action="f_casos_sindicato.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">

												<!-- Fecha -->
												<div class="form-group">
													<label class="control-label col-lg-3">Asunto:</label>
												<div class="col-lg-9">
												<div class="input-group">
													<?php echo $row_casos['asunto']; ?><p>&nbsp;</p>
												</div>
											   </div>
											  </div> 
												<!-- Fecha -->
 
												<!-- Fecha -->
												<div class="form-group">
													<label class="control-label col-lg-3">Fecha de cierre:<span class="text-danger">*</span></label>
												<div class="col-lg-9">
												<div class="input-group">
												<span class="input-group-addon"><i class="icon-calendar22"></i></span>
													<input type="text" required="required" class="form-control pickadate-format" placeholder="Seleccione la fecha" name="fecha_fin" id="fecha_fin" value="<?php if ($row_casos['fecha_fin'] == "") { echo "";} else { echo date('d-m-Y', strtotime($row_casos['fecha_fin'])); }?>">
												</div>
											   </div>
											  </div> 
												<!-- Fecha -->

												<p>&nbsp;</p>

												<!-- Basic text input -->
											  <div class="form-group">
													<label class="control-label col-lg-3">Detalle del cierre:</label>
													<div class="col-lg-9">
													  <textarea name="descripcion_cierre" required="required" rows="3" class="form-control" id="descripcion_cierre" placeholder="Indique los acuerdos y soluciones obtenidas en el caso."><?php echo $row_casos['descripcion_cierre']; ?></textarea>
													</div>
												</div>
												<!-- /basic text input -->

														<div>
														</div>
												<p>&nbsp;</p>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-warning">Cerrar caso</button> 
													<input type="hidden" name="MM_insert" value="form1" />
													<input type="hidden" name="IDsindicato" value="<?php echo $row_casos['IDsindicato']; ?>" />
												</div>
												
												</form>
												
											</div>
										</div>
									</div>
									<!-- danger modal -->
						   

                          <?php } while ($row_casos = mysql_fetch_assoc($casos)); ?>
                         <?php } else { ?>
                         <tr><td colspan="7">Sin casos con el filtro seleccionado. <a href="f_casos_sindicato_edit.php" class="btn btn-success btn-xs">Agregar Nuevo</a></td></tr>
                         <?php } ?>
					    </tbody>
				    </table>
				</div>        
                                    
                                    
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
										<span>Instrucciones</span>
									</div>

									<div class="category-content">

										<div class="form-group">


										<p class="content-group">
										1. Dando clic en <i>"Seguimiento(s)"</i> puedes ver el listado de reportes de avance de tu caso.</br>
										2. Dando clic en el botón <i>"Descripción"</i> puedes ver la información detallada del caso.</br>
										3. Da clic en <i>"Agregar"</i> o <i>"Editar"</i> casos, segín sea necesario.</br>
										4. Da clic en <i>"Cerrar"</i>, se dará por atendido el caso.</br>
										</p>
									</div>

									</div>
								</div>
								<!-- /course details -->

								<!-- Upcoming courses -->
								<div class="sidebar-category">
									<div class="category-title">
										<span>Contacto</span>
									</div>

									<div class="category-content">

										<div class="form-group">

										<p class="content-group">
										Para cualquier duda respecto del uso de SGRH, por favor contactanos al teléfono 55 772 394 9396 o al correo jacardenas@sahuayo.mx.
										</p>


										</div>

									</div>
								</div>
								<!-- /upcoming courses -->

							</div>
						</div>
					</div>
		            <!-- /detached sidebar -->


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