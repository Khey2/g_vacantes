<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

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
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_clima_periodos WHERE IDmatriz = $IDmatriz AND estatus = 1";
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos);
$IDperiodo = $row_periodos['IDperiodo'];

mysql_select_db($database_vacantes, $vacantes);
$query_elperiodo = "SELECT * FROM sed_clima_periodos WHERE IDperiodo = $IDperiodo";
$elperiodo = mysql_query($query_elperiodo, $vacantes) or die(mysql_error());
$row_elperiodo = mysql_fetch_assoc($elperiodo);
$totalRows_elperiodo = mysql_num_rows($elperiodo); 

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];


if(isset($_GET['IDplan'])){
$IDplan = $_GET['IDplan'];
mysql_select_db($database_vacantes, $vacantes);
$query_plan = "SELECT * FROM sed_clima_planes_liderazgo WHERE IDplan = '$IDplan'";
mysql_query("SET NAMES 'utf8'");
$plan = mysql_query($query_plan, $vacantes) or die(mysql_error());
$row_plan= mysql_fetch_assoc($plan);
$totalRows_plan = mysql_num_rows($plan);
}


mysql_select_db($database_vacantes, $vacantes);
$query_files2 = "SELECT * FROM sed_clima_files WHERE IDplan = '$IDplan'";
$files2 = mysql_query($query_files2, $vacantes) or die(mysql_error());
$row_files2 = mysql_fetch_assoc($files2);
$totalRows_files2 = mysql_num_rows($files2);



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$fecha_compromiso = date('Y-m-d', strtotime($_POST['fecha_compromiso']));
$IDdimension = $_POST['IDdimension'];
$area_oportunidad = $_POST['area_oportunidad'];
$estrategias = $_POST['estrategias'];
$actividades = $_POST['actividades'];
$objetivo = $_POST['objetivo'];
$periodicidad = $_POST['periodicidad'];
$resultados = $_POST['resultados'];

$updateSQL = "UPDATE sed_clima_planes_liderazgo SET IDdimension = '$IDdimension', area_oportunidad = '$area_oportunidad', estrategias = '$estrategias', actividades = '$actividades', objetivo = '$objetivo',  periodicidad = '$periodicidad',  resultados = '$resultados'  WHERE IDplan = '$IDplan'"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: f_clima_plan.php?info=2");
}

//insertar
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$fecha = date('Y-m-d', strtotime($_POST['fecha_compromiso']));
  $insertSQL = sprintf("INSERT INTO sed_clima_planes_liderazgo (IDempleado, anio, IDdimension, area_oportunidad, estrategias, actividades, objetivo, periodicidad, resultados, tipo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['anio'], "int"),
                       GetSQLValueString($_POST['IDdimension'], "text"),
                       GetSQLValueString($_POST['area_oportunidad'], "text"),
                       GetSQLValueString($_POST['estrategias'], "text"),
                       GetSQLValueString($_POST['actividades'], "text"),
                       GetSQLValueString($_POST['objetivo'], "text"),
                       GetSQLValueString($_POST['periodicidad'], "text"),
                       GetSQLValueString($_POST['resultados'], "text"),
                       GetSQLValueString($_POST['tipo'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  $captura = mysql_insert_id();
  header("Location: f_clima_plan.php?info=1");
}

// borrar alternativo
if (isset($_GET['borrar'])) {
  
  $IDplan = $_GET['IDplan'];
  $deleteSQL = "DELETE FROM sed_clima_planes_liderazgo WHERE IDplan = '$IDplan' AND IDperiodo = '$IDperiodo'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: f_clima_plan.php?info=3");
}

// Make an insert transaction instance
$ins_sed_clima_files = new tNG_insert($conn_vacantes);
$tNGs->addTransaction($ins_sed_clima_files);
// Register triggers
$ins_sed_clima_files->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_sed_clima_files->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_sed_clima_files->registerTrigger("END", "Trigger_Default_Redirect", 99, "f_clima_plan_edit.php?IDplan={IDplan}&info=1");
$ins_sed_clima_files->registerTrigger("AFTER", "Trigger_FileUpload", 97);
// Add columns
$ins_sed_clima_files->setTable("sed_clima_files");
$ins_sed_clima_files->addColumn("file", "FILE_TYPE", "FILES", "file");
$ins_sed_clima_files->addColumn("IDplan", "NUMERIC_TYPE", "POST", "IDplan");
$ins_sed_clima_files->addColumn("fecha", "DATE_TYPE", "POST", "fecha");
$ins_sed_clima_files->addColumn("IDusuario", "STRING_TYPE", "POST", "IDusuario");
$ins_sed_clima_files->setPrimaryKey("IDfile", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rssed_clima_files = $tNGs->getRecordset("sed_clima_files");
$row_rssed_clima_files = mysql_fetch_assoc($rssed_clima_files);
$totalRows_rssed_clima_files = mysql_num_rows($rssed_clima_files);

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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>

	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>

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

              
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Plan de Acción - Liderazgo</h5>
						</div>

					<div class="panel-body">
							<p>A continuación deberá capturar el compromiso que establecerá con base en sus resultados de Liderazgo.</p>



						 <form method="post" id="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
								<fieldset class="content-group">

<?php if (!isset($_GET['IDplan'])) { ?>

                                    <div class="form-group">
										<label class="control-label col-lg-3">Objetivo:</label>
			                        <div class="col-lg-9">
                                    	<input type="text" class="form-control" name="objetivo" id="objetivo" placeholder="Describa el Objetivo."  value="">
                                   </div>
                                  </div> 


									<div class="form-group">
										<label class="control-label col-lg-3">Dimensión:</label>
										<div class="col-lg-9">
											<select name="IDdimension" class="form-control" id="IDdimension" required="required">
                                                <option value="">Seleccione...</option>
                                                <option value="1">Comunicación</option>
                                                <option value="2">Acción y Resultados</option>
                                                <option value="3">Participación</option>
                                                <option value="4">Liderazgo con Valores</option>
                                                <option value="5">Relaciones Interpersonales</option>
                                                <option value="6">Motivación y Reconocimiento</option>
                                              </select>
										</div>
									</div>


                                    <div class="form-group">
										<label class="control-label col-lg-3">Periodicidad:</label>
			                        <div class="col-lg-9">
                                    	<input type="text" class="form-control" name="periodicidad" id="periodicidad"  placeholder="Mensual, Semanal." value="">
                                   </div>
                                  </div> 


                                    <div class="form-group">
										<label class="control-label col-lg-3">Área de Oportunidad:</label>
			                        <div class="col-lg-9">
                                    	<input type="text" class="form-control" name="area_oportunidad" id="area_oportunidad" placeholder="Área de oportunidad." value="">
                                   </div>
                                  </div> 

                                    <div class="form-group">
										<label class="control-label col-lg-3">Estrategias:</label>
			                        <div class="col-lg-9">
                                    	<textarea name="estrategias" id="estrategias" rows="5" class="form-control"  placeholder="Describa las estrategias."></textarea>
                                   </div>
                                  </div> 

                                    <div class="form-group">
										<label class="control-label col-lg-3">Actividades:</label>
			                        <div class="col-lg-9">
                                    	<textarea name="actividades" id="actividades" rows="5" class="form-control"  placeholder="Describa las actividades."></textarea>
                                   </div>
                                  </div> 


									<div class="form-group">
										<label class="control-label col-lg-3">Resultados:</label>
										<div class="col-lg-9">
                                          <textarea name="resultados" id="resultados" rows="5" class="form-control"  placeholder="Describa los resultados."></textarea>
										</div>
									</div>
					        <input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>" />
					        <input type="hidden" name="anio" value="<?php echo $anio; ?>" />
					        <input type="hidden" name="fecha" value="<?php echo $fecha; ?>" />

      							<div class="modal-footer">
					            <input type="submit" class="btn bg-primary-700" name="MM_insert" value="Agregar" />
							    <input type="hidden" name="MM_insert" value="form1" />
                                <a class="btn bg-info" href="f_clima_plan.php">Cancelar</a>
                              </div>

<?php } else { ?>

                                    <div class="form-group">
										<label class="control-label col-lg-3">Objetivo:</label>
			                        <div class="col-lg-9">
                                    	<input type="text" class="form-control" name="objetivo" id="objetivo"
                                         value="<?php echo $row_plan['objetivo'];?>">
                                   </div>
                                  </div> 


									<div class="form-group">
										<label class="control-label col-lg-3">Dimensión:</label>
										<div class="col-lg-9">
											<select name="IDdimension" class="form-control" id="IDdimension" required="required">
        <option value="1" <?php if (!(strcmp(1, htmlentities($row_plan['IDdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Comunicación</option>
        <option value="2" <?php if (!(strcmp(2, htmlentities($row_plan['IDdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Acción y Resultados</option>
        <option value="3" <?php if (!(strcmp(3, htmlentities($row_plan['IDdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Participación</option>
        <option value="4" <?php if (!(strcmp(4, htmlentities($row_plan['IDdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Liderazgo con Valores</option>
        <option value="5" <?php if (!(strcmp(5, htmlentities($row_plan['IDdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Relaciones Interpersonales</option>
        <option value="6" <?php if (!(strcmp(6, htmlentities($row_plan['IDdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Motivación y Reconocimiento</option>
      </select>
										</div>
									</div>


                                    <div class="form-group">
										<label class="control-label col-lg-3">Periodicidad:</label>
			                        <div class="col-lg-9">
                                    	<input type="text" class="form-control" name="periodicidad" id="periodicidad"
                                         value="<?php echo $row_plan['periodicidad'];?>">
                                   </div>
                                  </div> 


                                    <div class="form-group">
										<label class="control-label col-lg-3">Área de Oportunidad:</label>
			                        <div class="col-lg-9">
                                    	<input type="text" class="form-control" name="area_oportunidad" id="area_oportunidad"
                                         value="<?php echo $row_plan['area_oportunidad'];?>">
                                   </div>
                                  </div> 

                                    <div class="form-group">
										<label class="control-label col-lg-3">Estrategias:</label>
			                        <div class="col-lg-9">
                                    	<textarea name="estrategias" id="estrategias" rows="5" class="form-control"  placeholder="Describa las estrategias."><?php echo $row_plan['estrategias']; ?></textarea>
                                   </div>
                                  </div> 

                                    <div class="form-group">
										<label class="control-label col-lg-3">Actividades:</label>
			                        <div class="col-lg-9">
                                    	<textarea name="actividades" id="actividades" rows="5" class="form-control"  placeholder="Describa las actividades."><?php echo $row_plan['actividades']; ?></textarea>
                                   </div>
                                  </div> 


									<div class="form-group">
										<label class="control-label col-lg-3">Resultados:</label>
										<div class="col-lg-9">
                                          <textarea name="resultados" id="resultados" rows="5" class="form-control"  placeholder="Describa los resultados."><?php echo $row_plan['resultados']; ?></textarea>
										</div>
									</div>

					        <input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>" />
					        <input type="hidden" name="anio" value="<?php echo $anio; ?>" />
					        <input type="hidden" name="fecha" value="<?php echo $fecha; ?>" />

      						  <div class="modal-footer">
					            <input type="submit" class="btn bg-primary-700" name="MM_update" value="Actualizar" />
							    <input type="hidden" name="MM_update" value="form1" />
                                <a class="btn bg-info" href="f_clima_plan.php">Regresar</a>
                              </div>

<?php } ?>
								</fieldset>
                            </form>
							
							
							
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
											<td>
											<a class="btn btn-success" href="sed_rh_files/<?php echo $row_files2['file']; ?>">Descargar</a>
											<button type="button" data-target="#modal_theme_danger<?php echo $row_files2['IDfile']; ?>" data-toggle="modal" class="btn btn-danger">Borrar</button>
											</td>
                                    </tr>
									
									                <!-- danger modal -->
                                                        <div id="modal_theme_danger<?php echo $row_files2['IDfile']; ?>" class="modal fade" tabindex="-1">
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
                                                                 <a href="f_clima_plan_edit_borrar.php?IDfile=<?php echo $row_files2['IDfile']; ?>&IDplan=<?php echo $IDplan; ?>" class="btn btn-danger" >Si borrar</a>
                                                                 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- /danger modal -->

									
									
                                     <?php } while ($row_files2 = mysql_fetch_assoc($files2)); ?>
 									<?php } else{ ?>     
                                    <tr>
									<td>Sin documentos cargados.	</td>
									<td></td>
                                    </tr>
 									<?php } ?>

								<td>
								<?php if (isset($_GET['IDplan'])) { ?>
								  <p><strong>Agregar documento:</strong></p>
                
                                      <?php echo $tNGs->getErrorMsg(); ?>
                                      <form method="post" id="form2" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" enctype="multipart/form-data">

                                      <!-- Basic text input -->
								  <div class="form-group">
										<div class="col-lg-9">
										<input type="file" name="file" id="file" value="<?php echo KT_escapeAttribute($row_rssed_clima_files['file']); ?>" class="file-styled" required="required"/>
										</div>
									</div>
									<!-- /basic text input -->
                                      
                                        <input type="hidden" name="IDplan" id="IDplan" value="<?php echo $IDplan; ?>" />
                                        <input type="hidden" name="fecha" id="fecha" value="<?php echo $fecha; ?>" />
                                        <input type="hidden" name="IDusuario" id="IDusuario" value="<?php $IDusuario; ?>" />
                                        <?php echo $tNGs->getErrorMsg(); ?>
                                       <button type="submit"  name="KT_Insert1"  id="KT_Insert1" class="btn btn-primary">Agregar</button>
								<?php } ?>
										</td>
									</tbody>
								</table>
								
							</div>
							<!-- /task details -->

							
                    
                    </div>

					<!-- /Contenido -->
                </div>
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