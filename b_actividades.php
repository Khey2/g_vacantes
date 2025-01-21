<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/b_tNG.inc.php');

// Make unified connection variable
$conn_nom35 = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
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
$el_mes = date("m"); 


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM capa_becarios WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

$IDsucursal = $row_usuario['IDsucursal'];
$el_usuario = $row_usuario['IDempleado'];

if (isset($_POST['el_mes']) && ($_POST['el_mes']  > 0)) {$_SESSION['el_mes'] = $_POST['el_mes'];} 
if (!isset($_SESSION['el_mes'])) {$_SESSION['el_mes'] = $el_mes;}
$mi_mes = $_SESSION['el_mes'];
						
if(isset($_POST['el_anio']) && ($_POST['el_anio']  > 0)) {
$_SESSION['el_anio'] = $_POST['el_anio']; } else { $_SESSION['el_anio'] = $anio;}
$el_anio = $_SESSION['el_anio'];

$query_doctos = "SELECT * FROM capa_becarios_actividades WHERE IDempleado = $el_usuario AND borrado = 0 AND IDmes = '$mi_mes' AND anio = '$el_anio'";
$doctos = mysql_query($query_doctos, $vacantes) or die(mysql_error());
$row_doctos = mysql_fetch_assoc($doctos);
$totalRows_doctos = mysql_num_rows($doctos);						

mysql_select_db($database_vacantes, $vacantes);
$query_becarios  = "SELECT capa_becarios.*, capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo FROM capa_becarios LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo WHERE capa_becarios.IDempleado = '$el_usuario'";
mysql_query("SET NAMES 'utf8'");
$becarios = mysql_query($query_becarios , $vacantes) or die(mysql_error());
$row_becarios = mysql_fetch_assoc($becarios);
$totalRows_becarios  = mysql_num_rows($becarios );
$ELempleado = $row_becarios['ELempleado'];
$Eltipo = $row_becarios['IDtipo'];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$IDmes = $_POST['IDmes'];
$IDanio = $_POST['IDanio'];
$fecha_doc = date("Y_m_d_hms"); 
$actividad =  htmlentities($_POST['actividad'], ENT_COMPAT, '');
$query2 = "INSERT INTO capa_becarios_actividades(IDempleado, IDmes, anio, actividad, fecha, borrado, estatus) VALUES ('$ELempleado', '$IDmes', '$IDanio', '$actividad', '$fecha', 0, 0)"; 
$result2 = mysql_query($query2) or die(mysql_error());  
header("Location: b_actividades.php?info=1"); 	
}

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] == 1)) {
$borrado = $_GET['IDactividad'];
$deleteSQL = "UPDATE capa_becarios_actividades SET borrado = 1 WHERE IDactividad ='$borrado'";
mysql_select_db($database_vacantes, $vacantes);
$result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
header("Location: b_actividades.php?info=3");
}

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_meses = "SELECT * FROM vac_meses";
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

$query_mesesx = "SELECT * FROM vac_meses";
$mesesx = mysql_query($query_mesesx, $vacantes) or die(mysql_error());
$row_mesesx = mysql_fetch_assoc($mesesx);
$totalRows_mesesx = mysql_num_rows($mesesx);
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
	<script src="global_assets/js/plugins/tables/datatables/bec_datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/media/fancybox.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html_bec.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/ecommerce_product_list.js"></script>
	<!-- /Theme JS files -->
 	<style>
	.size{ width: 40%;}
	</style>
</head>
<body class= "<?php if (isset($_COOKIE["lmenu"])) { echo 'sidebar-xs';}?>  has-detached-right">

	<?php require_once('assets/b_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/b_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/b_pheader.php'); ?>

			<!-- Content area -->
			  <div class="content">

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la actividad.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la actividad.
					    </div>
                        <?php } ?>


	<div class="panel panel-flat">

	<div class="media panel-body no-margin">
		<div class="media-body">
                                    

								<ul class="media-list">
									<li class="media panel-body stack-media-on-mobile">
										<div class="media-left">
											<a href="#">
												<?php if ($row_becarios['Fotografia'] != '') { ?>
												<img src="<?php echo 'becariosfiles/'.$row_becarios['ELempleado'].'/'.$row_becarios['Fotografia']; ?>" alt="Fotografia" width="80" height="100"><br/>
												<?php } else { ?>
												<img src="files/foto.jpg" alt="Fotografia" width="80" height="100"><br/>
												<?php } ?>
											</a>
										</div>

										<div class="media-body">
											<h6 class="media-heading text-semibold">
												<a href="#"><?php echo $row_becarios['emp_paterno']." ". $row_becarios['emp_materno']." ". $row_becarios['emp_nombre']; ?></a>
											</h6>

											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Programa:</strong> <?php echo $row_becarios['tipo']; ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Fecha alta:</strong> <?php echo date('d/m/Y', strtotime($row_becarios['fecha_alta'])); ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Modalidad:</strong> <?php if ($row_becarios['IDmodalidad'] == 1) {echo "Presencial";} else if ($row_becarios['IDmodalidad'] == 2) {echo "Remoto ";} else {echo "Mixto";} ?></li>
											</ul>												
										</div>
											
									</li>
								</ul>							

					<span class="display-block text-semibold"><h6>Mis Actividades Mensuales</h6></span>
					<p>Instrucciones:<br/>
					<ul>
					<li>Captura de 3 a 5 actividades específicas que realizaste en tu área durante el mes.</li>
					<li>Dichas actividades serán revisadas y evaluadas por tu tutor.</li>
					<li>Una vez que tu tutor inicie la evaluación, ya no podrás editarlas, ni agregar o eliminar alguna otra.</li>
					</ul>
					</p>
					<p>&nbsp;</p>
					
					
					<form method="POST" action="b_actividades.php">
					<table class="table table-condensed table-borderless">
						<tbody>							  
							<tr>
							<td>Año:<select name="el_anio" class="form-control">
							<option value="2025"<?php if (2025 == $anio) {echo "selected=\"selected\"";} ?>>2025</option>
							<option value="2024"<?php if (2024 == $anio) {echo "selected=\"selected\"";} ?>>2024</option>
							<option value="2023"<?php if (2022 == $anio) {echo "selected=\"selected\"";} ?>>2023</option>
							<option value="2022"<?php if (2022 == $anio) {echo "selected=\"selected\"";} ?>>2022</option>						  
							</select></td>
							<td>Mes:<select name="el_mes" class="form-control">
							   <?php do {  ?>
							   <option value="<?php echo $row_meses['IDmes']?>"<?php if (!(strcmp($row_meses['IDmes'], $mi_mes))) {echo "selected=\"selected\"";} ?>><?php echo $row_meses['mes']?></option>
							   <?php
							  } while ($row_meses = mysql_fetch_assoc($meses));
							  $rows = mysql_num_rows($meses);
							  if($rows > 0) {
								  mysql_data_seek($mes, 0);
								  $row_meses = mysql_fetch_assoc($meses);
							  } ?>
							</select></td>
					      	<td><button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i></button></td>
					      	<?php if ($row_doctos['estatus'] == 0) { ?>
							<td><button type="button" data-target="#modal_cargar_documento"  data-toggle="modal" class="btn btn-success">Agregar Actividad</button></td>
							<?php }  ?>
						  </tr>
					    </tbody>
				    </table>
					</form>
					<p>&nbsp;</p>
					
					
						<table class="table table-condensed datatable-button-html5-columns">
						<thead>
							<tr class="bg-blue">
							    <th>Año</th>
							    <th>Mes</th>
							    <th class="size">Descripción de la Actividad</th>
							    <th>Estatus</th>
							    <th>Resultado</th>
							    <th class="text-center">Acciones</th>
						    </tr>
					    </thead>
						<tbody>							  
						<?php if ($totalRows_doctos) { do { ?>
							<tr>
							<td><?php echo $row_doctos['anio']; ?></td>
							<td><?php 
							
							  switch ($row_doctos['IDmes']) {
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

							echo $elmes; ?></td>
							<td><?php echo $row_doctos['actividad']; ?></td>
							<td><?php if ($row_doctos['estatus'] == 0) {echo "Capturada";} else {echo "Evaluada";} ?></a></td>
							<td><?php if ($row_doctos['resultado'] == 1) {echo "Deficiente";} 
								else  if ($row_doctos['resultado'] == 2) {echo "Suficiente";}
								else  if ($row_doctos['resultado'] == 3) {echo "Satisfactorio";}
								else  if ($row_doctos['resultado'] == 4) {echo "Sobresaliente";}
								else {echo "-";} ?></td>
							<td>
							<?php if ($row_doctos['estatus'] == 0) { ?>
								<button type="button" data-target="#modal_theme_danger<?php echo $row_doctos['IDactividad']; ?>"  data-toggle="modal" class="btn btn-danger">Borrar</button>
							<?php } else { ?>
							-
							<?php } ?>							
							</td>
						
							
					<!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_doctos['IDactividad']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la actividad?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="b_actividades.php?IDactividad=<?php echo $row_doctos['IDactividad']; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

						    </tr>

							
							<?php } while ($row_doctos = mysql_fetch_assoc($doctos));  ?>
							<?php } else { ?>
							
							<tr>
							<td colspan="6">No se tienen registradas actividades en este mes.</td>
							</tr>
							
							<?php } ?>
					    </tbody>
				    </table>


									<!-- danger modal -->
									<div id="modal_cargar_documento" class="modal fade" tabindex="-1">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cargar Actividad</h6>
												</div>
												<div class="modal-body">
																			
														<form action="b_actividades.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														<p>&nbsp;</p>


														<!-- Fecha -->
														<div class="form-group">
															<label class="control-label col-lg-3">Año:<span class="text-danger">*</span></label>
														<div class="col-lg-9">
																<select name="IDanio" id="IDanio" class="form-control" required="required">
																<option value="2023">2023</option>
																<option value="2024">2024</option>
																<option value="2025">2025</option>
																</select>
													   </div>
													  </div> 
														<!-- Fecha -->


														<!-- Fecha -->
														<div class="form-group">
															<label class="control-label col-lg-3">Mes:<span class="text-danger">*</span></label>
														<div class="col-lg-9">
																<select name="IDmes" id="IDmes" class="form-control" required="required">
																	<option value="">Seleccione una opción</option> 
																		  <?php do {  ?>
																		  <option value="<?php echo $row_mesesx['IDmes']?>" <?php if ($row_mesesx['IDmes'] == $mi_mes) {echo "selected=\"selected\"";} ?>><?php echo $row_mesesx['mes']?></option>
																		  <?php
																		 } while ($row_mesesx = mysql_fetch_assoc($mesesx));
																		 $rows = mysql_num_rows($mesesx);
																		 if($rows > 0) {
																		 mysql_data_seek($mesesx, 0);
																		 $row_mesesx = mysql_fetch_assoc($mesesx);
																		 } ?>
															  </select>
													   </div>
													  </div> 
														<!-- Fecha -->


															<!-- Basic text input -->
														<div class="form-group">
															<label class="control-label col-lg-3">Actividad:<span class="text-danger">*</span></label>
															<div class="col-lg-9">
																<textarea class="form-control"  name="actividad" id="actividad" rows="4" placeholder="Captura la actividad realizada." required="required" ></textarea>
															</div>
														</div>
														<!-- /basic text input -->

														 
															<input type="hidden" name="comentarios" id="comentarios" class="form-control" placeholder="Comentarios">

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-success">Cargar actividad</button> 
													<input type="hidden" name="MM_insert" value="form1" />
												</div>
												</form>
											</div>
										</div>
									</div>
									<!-- danger modal -->


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
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>