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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_becario = "SELECT capa_becarios.*,  capa_becarios.IDempleado AS ELempleado, capa_becarios.file AS Fotografia, capa_becarios_tipo.tipo, capa_becarios_evaluacion.IDevaluacion, capa_becarios_evaluacion.anio, capa_becarios_evaluacion.IDmes, vac_meses.mes FROM capa_becarios LEFT JOIN capa_becarios_evaluacion ON capa_becarios.IDempleado = capa_becarios_evaluacion.IDempleado LEFT JOIN vac_meses ON capa_becarios_evaluacion.IDmes = vac_meses.IDmes LEFT JOIN capa_becarios_tipo ON capa_becarios.IDtipo = capa_becarios_tipo.IDtipo WHERE capa_becarios.IDempleado = '$el_usuario'";
mysql_query("SET NAMES 'utf8'");
$becario = mysql_query($query_becario, $vacantes) or die(mysql_error());
$row_becario = mysql_fetch_assoc($becario);
$totalRows_becario = mysql_num_rows($becario);
$IDsubarea = $row_becario['IDsubarea'];
$IDarea = $row_becario['IDarea'];
$IDmatriz_b = $row_becario['IDmatriz'];
$IDsucursal = $row_becario['IDsucursal'];
$Elempleado = $row_becario['ELempleado'];


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz_b = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz_b";
$matriz_b = mysql_query($query_matriz_b, $vacantes) or die(mysql_error());
$row_matriz_b = mysql_fetch_assoc($matriz_b);
$totalRows_matriz_b = mysql_num_rows($matriz_b);

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDsucursal = $IDsucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas WHERE IDarea = $IDarea";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_subarea = "SELECT * FROM vac_subareas WHERE IDsubarea = $IDsubarea";
$subarea = mysql_query($query_subarea, $vacantes) or die(mysql_error());
$row_subarea = mysql_fetch_assoc($subarea);
$totalRows_subarea = mysql_num_rows($subarea);

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$fecha_doc = date("Y_m_d"); 

// agregar PDF
$formatos_permitidos =  array('jpg', 'jepg', 'png');
$IDempleado_carpeta = 'becariosfiles/'.$Elempleado;
$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new="FOTO_".$Elempleado.'.'.$extension;
$targetPath = 'becariosfiles/'.$Elempleado.'/'.$name_new;

// si se mandó archivo
if ($name != '') {	
	
if(!in_array($extension, $formatos_permitidos) ) { echo "error archivos"; 
header("Location: b_datos.php?info=9"); 
}
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
}
if ($name != '') {$name_new = $name_new;} else {$name_new = $foto_anterior; }

$query2 = "UPDATE capa_becarios SET file = '$name_new' WHERE IDempleado ='$Elempleado'"; 
$result2 = mysql_query($query2) or die(mysql_error());  
header("Location: b_datos.php?info=1"); 
	
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
$fecha_doc = date("Y_m_d"); 

// agregar PDF
$formatos_permitidos =  array('pdf');
$IDempleado_carpeta = 'becariosfiles/'.$Elempleado;
$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new="CV_".$Elempleado.'.'.$extension;
$targetPath = 'becariosfiles/'.$Elempleado.'/'.$name_new;

// si se mandó archivo
if ($name != '') {	
	
if($extension != 'pdf' Or $extension != 'PDF')  { echo "error archivos"; 
header("Location: b_datos.php?info=9"); 
}
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}
move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
}
if ($name != '') {$name_new = $name_new;} else {$name_new = $foto_anterior; }

$query2 = "UPDATE capa_becarios SET file_cv = '$name_new' WHERE IDempleado ='$Elempleado'"; 
$result2 = mysql_query($query2) or die(mysql_error());  
header("Location: b_datos.php?info=5"); 
	
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex" />
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

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>

	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/xpicker_date.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
</head>
<body class="has-detached-right">	
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
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo tiene un formato no autorizado.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente tu foto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 5))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente tu Curriculum.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                

					
					<!-- Navigation widget -->
							<div class="panel panel-flat">
								<div class="panel-heading">
								
								<ul class="media-list">
									<li class="media panel-body stack-media-on-mobile">
										<div class="media-left">
											<a href="#">
												<?php if ($row_becario['Fotografia'] != '') { ?>
												<img src="<?php echo 'becariosfiles/'.$row_becario['ELempleado'].'/'.$row_becario['Fotografia']; ?>" alt="Fotografia" width="80" height="100"><br/>
												<?php } else { ?>
												<img src="files/foto.jpg" alt="Fotografia" width="80" height="100"><br/>
												<?php } ?>
											</a>
										</div>

										<div class="media-body">
											<h6 class="media-heading text-semibold">
												<a href="#"><?php echo $row_becario['emp_paterno']." ". $row_becario['emp_materno']." ". $row_becario['emp_nombre']; ?></a>
											</h6>

											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Programa:</strong> <?php echo $row_becario['tipo']; ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Fecha alta:</strong> <?php echo date('d/m/Y', strtotime($row_becario['fecha_alta'])); ?></li>
											</ul>
											<ul class="list-inline list-inline-separate mb-10">
												<li><strong>Modalidad:</strong> <?php if ($row_becario['IDmodalidad'] == 1) {echo "Presencial";} else if ($row_becario['IDmodalidad'] == 2) {echo "Remoto ";} else {echo "Mixto";} ?></li>
											</ul>												
										</div>
											
									</li>
								</ul>	


									<button type="button" data-target="#modal_cargar_documento"  data-toggle="modal" class="btn btn-xs btn-success">Actualizar foto</button>
												<?php if ($row_becario['file_cv'] == '') { ?>
									<button type="button" data-target="#modal_cargar_cv"  data-toggle="modal" class="btn btn-xs btn-primary">Cargar Curriculum</button>
												<?php } else { ?>
									<button type="button" data-target="#modal_cargar_cv"  data-toggle="modal" class="btn btn-xs btn-primary">Actualizar Curriculum</button>
									<a target="_blank" href="<?php echo 'becariosfiles/'.$row_becario['ELempleado'].'/'.$row_becario['file_cv']; ?>" class="btn btn-xs btn-info">Descargar Curriculum</a>
									<?php } ?>

									<div class="list-group-divider"></div>

								
								<div class="list-group no-border no-padding-top">
									<a href="#" class="list-group-item"><strong>Nombre: </strong><?php echo htmlentities($row_becario['emp_paterno']." ".$row_becario['emp_materno']." ".$row_becario['emp_nombre'], ENT_COMPAT, ''); ?></a>
									<div class="list-group-divider"></div>
									<a href="#" class="list-group-item"><strong>CURP: </strong><?php echo htmlentities($row_becario['curp'], ENT_COMPAT, ''); ?></a>
									<a href="#" class="list-group-item"><strong>RFC: </strong><?php echo htmlentities($row_becario['rfc'], ENT_COMPAT, ''); ?></a>
									<a href="#" class="list-group-item"><strong>Teléfono: </strong><?php echo htmlentities($row_becario['telefono'], ENT_COMPAT, ''); ?></a>
									<a href="#" class="list-group-item"><strong>Correo: </strong><?php echo htmlentities($row_becario['correo'], ENT_COMPAT, ''); ?></a>
									<div class="list-group-divider"></div>
									<a href="#" class="list-group-item"><strong>Estatus: </strong><?php if ($row_becario['activo'] == 1) {echo "Activo";} else {echo "Inactivo";} ?></a>
									<a href="#" class="list-group-item"><strong>Rol de Asistencia: </strong><?php echo htmlentities($row_becario['IDrol'], ENT_COMPAT, ''); ?></a>
									<div class="list-group-divider"></div>
									<a href="#" class="list-group-item"><strong>Fecha de alta: </strong><?php echo date('d/m/Y', strtotime($row_becario['fecha_alta']));?></a>
									<?php  if ($row_becario['fecha_baja'] != '') { ?>
									<a href="#" class="list-group-item"><strong>Fecha de baja: </strong><?php echo date('d/m/Y', strtotime($row_becario['fecha_baja']));?></a>
									<?php } ?>
									<div class="list-group-divider"></div>
									<a href="#" class="list-group-item"><strong>Hora entrada: </strong><?php echo $row_becario['hora_entrada'];?></a>
									<a href="#" class="list-group-item"><strong>Hora Salida: </strong><?php echo $row_becario['hora_salida'];?></a>
									<div class="list-group-divider"></div>
									<a href="#" class="list-group-item"><strong>Matriz:	</strong><?php echo $row_matriz_b['matriz']?></a>
									<a href="#" class="list-group-item"><strong>Sucursal: </strong> <?php echo $row_sucursal['sucursal']?></a>
									<a href="#" class="list-group-item"><strong>Area: </strong> <?php echo $row_area['area']?></a>
									<a href="#" class="list-group-item"><strong>Subarea: </strong> <?php echo $row_subarea['subarea']?></a>
									<a href="#" class="list-group-item"><strong>Asignado a: </strong> <?php echo $row_usuario['emp_paterno']." ".$row_usuario['emp_materno']." ".$row_usuario['emp_nombre']; ?></a>
							<!-- /navigation widget -->
								</div>
								
									<p>Para cualquier duda o aclaración, contáctate con Mariana Mora Muñoz al correo <a href="amilto:mmora@sahuayo.mx">mmora@sahuayo.mx</a>, o con Aline de la Fuente al correo <a href="amilto:amdelafunete@sahuayo.mx">amdelafunete@sahuayo.mx</a>.</p>

								</div>


									<!-- danger modal -->
									<div id="modal_cargar_documento" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-success">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cargar Documento</h6>
												</div>
												<div class="modal-body">
																			
														<form action="b_datos.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														<p>Selecciona tu Foto.<br/> Solo se permiten fotos en formato JPG y PNG.</p>
														<p>&nbsp;</p>

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Foto:</label>
															  <div class="col-lg-9">
															<input type="file" name="file" id="file" class="file-styled" >
															 </div>
														  </div>
														  <!-- /basic text input -->

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-success">Cargar</button> 
													<input type="hidden" name="MM_insert" value="form1" />
												</div>
												</form>
											</div>
										</div>
									</div>
									<!-- danger modal -->


									<!-- danger modal -->
									<div id="modal_cargar_cv" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-primary">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Cargar Currículm</h6>
												</div>
												<div class="modal-body">
																			
														<form action="b_datos.php" method="post" name="importar" id="importar" class="form-horizontal" enctype="multipart/form-data">
														 <fieldset>
														<p>Selecciona tu Curriculum.<br/> Solo se permiten fotos en formato PDF.</p>
														<p>&nbsp;</p>

														 
														 <!-- Basic text input -->
														  <div class="form-group">
															  <label class="control-label col-lg-3">Curriculum:</label>
															  <div class="col-lg-9">
															<input type="file" name="file" id="file" class="file-styled" >
															 </div>
														  </div>
														  <!-- /basic text input -->

														 </fieldset>

														<div>
														</div>
														
																			
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<button type="submit" id="submit" name="import" class="btn btn-primary">Cargar</button> 
													<input type="hidden" name="MM_insert" value="form2" />
												</div>
												</form>
											</div>
										</div>
									</div>
									<!-- danger modal -->


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