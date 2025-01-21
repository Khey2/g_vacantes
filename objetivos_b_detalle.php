<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

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

// Start trigger
$formValidation = new tNG_FormValidation();
$tNGs->prepareValidation($formValidation);
// End trigger

//start Trigger_FileUpload trigger
//remove this line if you want to edit the code by hand 
function Trigger_FileUpload(&$tNG) {
  $uploadObj = new tNG_FileUpload($tNG);
  $uploadObj->setFormFieldName("file");
  $uploadObj->setDbFieldName("file");
  $uploadObj->setFolder("sed_rh_files/");
  $uploadObj->setMaxSize(18000);
  $uploadObj->setAllowedExtensions("jpg, jpeg, png, gif, pdf, doc, docx, zip, xls, xlsx, rar");
  $uploadObj->setRename("auto");
  return $uploadObj->Execute();
}
//end Trigger_FileUpload trigger


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
$IDusuario = $row_usuario['IDusuario'];

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

$IDavance = $_GET['IDavance'];
mysql_select_db($database_vacantes, $vacantes);
$query_avances = "SELECT * FROM ztar_avances WHERE IDavance = '$IDavance'";
mysql_query("SET NAMES 'utf8'");
$avances = mysql_query($query_avances, $vacantes) or die(mysql_error());
$row_avances = mysql_fetch_assoc($avances);
$totalRows_avances = mysql_num_rows($avances);
$IDtarea = $row_avances['IDtarea'];
$el_mes_esperado = date("m", strtotime($row_avances['fecha_esperada']));

  switch ($el_mes_esperado) {
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


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
  $descripcion = $row_avances['descripcion'];
  $updateSQL = sprintf("UPDATE ztar_avances SET descripcion=%s, progreso_detalle=%s, IDestatus=%s, fecha=%s WHERE IDavance='$IDavance'",
                       GetSQLValueString($descripcion, "text"),
                       GetSQLValueString($_POST['progreso_detalle'], "text"),
                       GetSQLValueString($_POST['IDestatus'], "date"),
                       GetSQLValueString($_POST['fecha'], "date"),
                       GetSQLValueString($_POST['IDavance'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "objetivos_b_detalle.php?IDtarea=$IDtarea&info=5";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_vacantes, $vacantes);
$query_tareas = "SELECT ztar_tareas.IDtarea, ztar_tareas.IDarea_rh, ztar_tareas.foto, ztar_tareas.descripcion, ztar_tareas.ponderacion,  ztar_tareas.IDperiodicidad,  ztar_tareas.por_evento, ztar_areas_rh.area_rh FROM ztar_areas_rh left JOIN ztar_tareas ON ztar_areas_rh.IDarea_rh = ztar_tareas.IDarea_rh WHERE IDtarea = '$IDtarea'";
mysql_query("SET NAMES 'utf8'");
$tareas = mysql_query($query_tareas, $vacantes) or die(mysql_error());
$row_tareas = mysql_fetch_assoc($tareas);
$totalRows_tareas = mysql_num_rows($tareas);
$por_evento = $row_tareas['por_evento'];

mysql_select_db($database_vacantes, $vacantes);
$query_files = "SELECT * FROM ztar_files WHERE IDavance = '$IDavance' AND IDmatriz = '$IDmatriz'";
$files = mysql_query($query_files, $vacantes) or die(mysql_error());
$row_files = mysql_fetch_assoc($files);
$totalRows_files = mysql_num_rows($files);

mysql_select_db($database_vacantes, $vacantes);
$query_files2 = "SELECT * FROM ztar_files WHERE IDtarea = '$IDtarea' AND IDmatriz IS NULL";
$files2 = mysql_query($query_files2, $vacantes) or die(mysql_error());
$row_files2 = mysql_fetch_assoc($files2);
$totalRows_files2 = mysql_num_rows($files2);

// Make an insert transaction instance
$ins_ztar_files = new tNG_insert($conn_vacantes);
$tNGs->addTransaction($ins_ztar_files);
// Register triggers
$ins_ztar_files->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_ztar_files->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_ztar_files->registerTrigger("END", "Trigger_Default_Redirect", 99, "objetivos_b_detalle.php?IDavance={IDavance}&info=1");
$ins_ztar_files->registerTrigger("AFTER", "Trigger_FileUpload", 97);
// Add columns
$ins_ztar_files->setTable("ztar_files");
$ins_ztar_files->addColumn("file", "FILE_TYPE", "FILES", "file");
$ins_ztar_files->addColumn("IDtarea", "NUMERIC_TYPE", "POST", "IDtarea");
$ins_ztar_files->addColumn("IDavance", "NUMERIC_TYPE", "POST", "IDavance");
$ins_ztar_files->addColumn("fecha", "DATE_TYPE", "POST", "fecha");
$ins_ztar_files->addColumn("IDusuario", "STRING_TYPE", "POST", "IDusuario");
$ins_ztar_files->addColumn("IDmatriz", "STRING_TYPE", "POST", "IDmatriz", "{avances.IDmatriz}");
$ins_ztar_files->setPrimaryKey("IDfile", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsztar_files = $tNGs->getRecordset("ztar_files");
$row_rsztar_files = mysql_fetch_assoc($rsztar_files);
$totalRows_rsztar_files = mysql_num_rows($rsztar_files);

$el_area = $row_tareas['IDarea_rh'];
$query_area = "SELECT * FROM ztar_areas_rh WHERE IDarea_rh = '$el_area'";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

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
	
		<!-- Theme JS files -->
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/natural_sort.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/gallery_library.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
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
							Se ha agregado correctamente el archivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el archivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


                                						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 5))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el objetivo.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 6))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el objetivo.
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

									<div> 
                                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>"  class="form-horizontal form-validate-jquery">
                                       <fieldset class="content-group">
                                       
                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><strong>Reporte de avance: * (campo obligatorio)</strong></label>
										<div class="col-lg-10">
											<textarea rows="5" required="required" class="wysihtml5 wysihtml5-min form-control" id="progreso_detalle" name="progreso_detalle"><?php echo htmlentities($row_avances['progreso_detalle'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->

								<?php if ($row_avances['IDestatus'] > 0){ ?>
                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><strong>Retroalimentación:</strong></label>
										<div class="col-lg-10">
											<textarea readonly rows="5" class="wysihtml5 wysihtml5-min form-control" id="coments" name="coments"><?php echo htmlentities($row_avances['coments'], ENT_COMPAT, ''); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    
                                        <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-2"><strong>Calificacion:</strong></label>
										<div class="col-lg-10">
								<?php
									$la_tarea = $row_tareas['IDtarea'];
									$query_ponds = "SELECT * FROM ztar_tareas WHERE IDtarea = $IDtarea";
									$ponds = mysql_query($query_ponds, $vacantes) or die(mysql_error());
									$row_ponds = mysql_fetch_assoc($ponds);
									
									  switch ($row_avances['IDresultado']) {
										case '':  $el_resultado = "Pendiente";  $el_resultado_i = "label-info";   break;     
										case $row_ponds['IDsob']:  $el_resultado = "Sobresaliente";  $el_resultado_i = "label-success";     break;     
										case $row_ponds['IDsat']:  $el_resultado = "Satisfactorio";  $el_resultado_i = "label-primary";   break;    
										case $row_ponds['IDdef']:  $el_resultado = "Deficiente"; $el_resultado_i = "label-danger";   break;    
										case 0:  $el_resultado = "No aplica - Pendiente"; $el_resultado_i = "label-default";   break;    
										  }
											?><a class="label <?php echo $el_resultado_i;  ?>"><?php echo $el_resultado;  ?></a>		
										</div>
									</div>
									<!-- /basic text input -->
                                    
								<?php } ?>

                                       
                                        <div class="text-right">
                                    <div>
								<?php if ($row_avances['IDestatus'] != 2){ ?>
                                 <button type="submit"  class="btn btn-primary">Guardar</button>
                                 <button type="button" onClick="window.location.href='objetivos_c.php?IDtarea=<?php echo $IDtarea; ?>'" class="btn btn-default btn-icon">Cancelar</button>
								<?php } else { ?>
                                 <button type="button" onClick="window.location.href='objetivos_c.php?IDtarea=<?php echo $IDtarea; ?>'" class="btn btn-default btn-icon">Regresar</button>
								<?php }  ?>
                                    </div>
                                  </div>
                                    
                                        <input type="hidden" name="MM_update" value="form1">
                                      <input type="hidden" name="IDavance" value="<?php echo $row_avances['IDavance']; ?>">
                                        <input type="hidden" name="fecha" value="<?php echo $fecha; ?>">
                                        <input type="hidden" name="IDestatus" value="1">
                              		</fieldset>
                               		</form>
								  </div>
								</div>

							</div>
							<!-- /task overview -->


							<!-- Task overview -->
							<div class="panel panel-flat">
							  <div class="panel-body">

								  <p><strong>Documentos cargados:</strong></br>
								  Sube aqui los documentos y formatos del avance solicitado.</p>
									<div>
                                    	<div class="table-responsive content-group">
										<table class="table table-framed">
											<thead>
												<tr>
													<th>#</th>
													<th>Documento</th>
													<th>Fecha</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>
										<?php if($totalRows_files > 0) { ?>
								<?php do { ?>
												<tr>
													<td><?php echo $row_files['IDfile']; ?></td>
													<td><?php echo $row_files['file']; ?></td>
													<td>
									                	<div class="input-group input-group-transparent">
									                		<?php $fecha = date('d/m/Y', strtotime($row_files['fecha']));
															if($row_files['fecha'] > 0) { echo $fecha;} else {echo "-";}?>
									                	</div>
													</td>
													<td>
                                                    <a href="sed_rh_files/<?php echo $row_files['file']; ?>" class="btn btn-success">Descargar</a>
                                                    <button type="button" data-target="#modal_theme_danger<?php echo $row_files['IDavance']; ?>"  
                                                    data-toggle="modal" class="btn btn-danger">Borrar</button>
                                                    </td>
												</tr>
                                                
                                                      <!-- danger modal -->
                                                        <div id="modal_theme_danger<?php echo $row_files['IDavance']; ?>" class="modal fade" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger">
                                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    <h6 class="modal-title">Confirmación de Borrado</h6>
                                                                </div>
                                
                                                                <div class="modal-body">
                                                                    <p>¿Estas seguro que quieres borrar el documento?</p>
                                                                </div>
                                
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                                                 <a href="objetivos_b_detalle_borrar.php?IDfile=<?php echo $row_files['IDfile']; ?>&IDavance=<?php echo $row_files['IDavance']; ?>" class="btn btn-danger" >Si borrar</a>
                                                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- /danger modal -->

			   				    <?php } while ($row_files = mysql_fetch_assoc($files)); ?>
									  <?php } else { ?>
                                      <tr><td colspan="4">Sin Documentos enviados.</td></tr>
							    <?php } ?>
												</tbody>
										</table>
								<?php if ($row_avances['IDestatus'] != 2){ ?>
				                  <p>&nbsp;</p>
								  <p><strong>Agregar documento:</strong></p>
                
                                         <?php echo $tNGs->getErrorMsg(); ?>
                                      <form method="post" id="form2" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" enctype="multipart/form-data">

                                      <!-- Basic text input -->
								  <div class="form-group">
										<div class="col-lg-9">
										<input type="file" name="file" id="file" value="<?php echo KT_escapeAttribute($row_rsztar_files['file']); ?>" class="file-styled" required="required"/>
										</div>
									</div>
									<!-- /basic text input -->
                                      
                                        <input type="hidden" name="IDtarea" id="IDtarea" value="<?php echo $IDtarea; ?>" />
                                        <input type="hidden" name="IDavance" id="IDavance" value="<?php echo $IDavance; ?>" />
                                        <input type="hidden" name="fecha" id="fecha" value="<?php echo $fecha; ?>" />
                                        <input type="hidden" name="IDusuario" id="IDusuario" value="<?php $IDusuario; ?>" />
                                        <?php echo $tNGs->getErrorMsg(); ?>
                                        <input type="hidden" name="IDmatriz" value="<?php echo $row_avances['IDmatriz']; ?>">
                                       <button type="submit"  name="KT_Insert1"  id="KT_Insert1" class="btn btn-primary">Agregar</button>
								<?php } ?>

                                      </form>
                                      <p>&nbsp;</p>
									</div>
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
                                        	<td><i class="icon-calendar52 position-left"></i>Fecha Entrega:</td>
											<td class="text-right text-semibold text-warning"><?php if($por_evento == 1) { echo "Por evento";} 
															else  if($row_avances['fecha_esperada'] > 0) { echo  $fecha = date('d/m/Y', strtotime($row_avances['fecha_esperada'])); } 
															else {echo "-";}?></td>
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
									<h6 class="panel-title"><i class="icon-office position-left"></i>Documentos</h6>
								</div>
								<table class="table table-borderless table-xs content-group-sm">
									<tbody>
									<?php if ($totalRows_files2 > 0) { ?>                                    
									<?php do { ?>                                    
                                    <tr>
											<td><?php echo $row_files2['file']; ?></td>
											<td><a class="btn btn-success" href="sed_rh_files/<?php echo $row_files2['file']; ?>">Descargar</a></td>
                                    </tr>
                                     <?php } while ($row_files2 = mysql_fetch_assoc($files2)); ?>
 									<?php } else{ ?>     
                                    <tr>
									<td>Sin documentos solicitados.	</td>
									<td></td>
                                    </tr>
 									<?php } ?>                                    
									</tbody>
								</table>
							</div>
							<!-- /task details -->


							<!-- Task details -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title"><i class="icon-git-commit position-left"></i>Contacto Corporativo</h6>
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