<?php require_once('Connections/vacantes.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php');

// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Load the KT_back class
require_once('includes/nxt/KT_back.php');

// Make a transaction dispatcher instance
$tNGs = new tNG_dispatcher("");

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

$restrict->addLevel("3");
$restrict->addLevel("4");
$restrict->addLevel("5");
$restrict->Execute();
//End Restrict Access To Page

header('Cache-Control: no cache'); //no cache
session_cache_limiter('private_no_expire'); // works


$IDdocumento = $_GET['IDdocumento'];
mysql_select_db($database_vacantes, $vacantes);
$query_docto = "SELECT * FROM proced_documentos WHERE IDdocumento = $IDdocumento";
$docto = mysql_query($query_docto, $vacantes) or die(mysql_error());
$row_docto = mysql_fetch_assoc($docto);
$totalRows_docto = mysql_num_rows($docto);

$IDdireccion = $row_docto['IDdireccion'];
$IDDarea = $row_docto['IDDarea'];
$IDsubarea = $row_docto['IDsubarea'];

//start Trigger_FileUpload trigger
//remove this line if you want to edit the code by hand 
function Trigger_FileUpload(&$tNG) {
  $uploadObj = new tNG_FileUpload($tNG);
  $uploadObj->setFormFieldName("file");
  $uploadObj->setDbFieldName("file");
  $uploadObj->setFolder('proced/anexos');
  $uploadObj->setMaxSize(3000000);
  $uploadObj->setAllowedExtensions("pdf, jpg, png, jpeg, zip, jpeg, doc, docx, mp3, mov, mp4, ppt, pptx, xls, xlsx, rar");
  $uploadObj->setRename("custom");
  $uploadObj->setRenameRule("{tiempo.FECHA}_{IDdocumento}_{IDanexo}.{KT_ext}");
  return $uploadObj->Execute();
}
//end Trigger_FileUpload trigger

// Start trigger
$formValidation = new tNG_FormValidation();
$tNGs->prepareValidation($formValidation);
// End trigger

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
//set headers to NOT cache a page
  header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
  header("Pragma: no-cache"); //HTTP 1.0
  header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$IDperiodovar = $row_variables['IDperiodo'];

mysql_select_db($database_vacantes, $vacantes);
$query_tiempo = "SELECT DATE_FORMAT(NOW( ), '%d%m%Y%H%i%S' ) as FECHA";
$tiempo = mysql_query($query_tiempo, $vacantes) or die(mysql_error());
$row_tiempo = mysql_fetch_assoc($tiempo);
$totalRows_tiempo = mysql_num_rows($tiempo);


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'"); 
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];

$IDmatriz = $row_usuario['IDmatriz'];


if(isset($_GET['IDanexo'])) {
$IDanexo = $_GET['IDanexo'];
mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT proced_documentos.IDDarea, proced_documentos.IDdireccion, proced_documentos.IDsubarea, proced_anexos.IDanexo, proced_anexos.IDvisible, proced_anexos.documento, proced_anexos.IDdocumento, proced_anexos.descripcion, proced_anexos.file, proced_anexos.anio, proced_anexos.version  FROM proced_anexos INNER JOIN proced_documentos ON proced_anexos.IDdocumento = proced_documentos.IDdocumento WHERE proced_anexos.IDanexo = $IDanexo";
mysql_query("SET NAMES 'utf8'");
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);
if ($row_resultados['file'] != '') { $archivo = $row_resultados['file'];} else { $archivo = '';} 
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'"); 
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

// Make an insert transaction instance
$ins_proced_anexos = new tNG_multipleInsert($conn_vacantes);
$tNGs->addTransaction($ins_proced_anexos);
// Register triggers
$ins_proced_anexos->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_proced_anexos->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_proced_anexos->registerTrigger("END", "Trigger_Default_Redirect", 99, "admin_proced_anexos.php?IDdocumento={IDdocumento}&info=1");
$ins_proced_anexos->registerTrigger("AFTER", "Trigger_FileUpload", 97);
// Add columns
$ins_proced_anexos->setTable("proced_anexos");
$ins_proced_anexos->addColumn("IDdocumento", "STRING_TYPE", "POST", "IDdocumento");
$ins_proced_anexos->addColumn("documento", "STRING_TYPE", "POST", "documento");
$ins_proced_anexos->addColumn("IDvisible", "NUMERIC_TYPE", "POST", "IDvisible");
$ins_proced_anexos->addColumn("descripcion", "STRING_TYPE", "POST", "descripcion");
$ins_proced_anexos->addColumn("file", "FILE_TYPE", "FILES", "file");
$ins_proced_anexos->addColumn("version", "STRING_TYPE", "POST", "version");
$ins_proced_anexos->addColumn("anio", "DATE_TYPE", "POST", "anio");
$ins_proced_anexos->setPrimaryKey("IDanexo", "NUMERIC_TYPE");

// Make an update transaction instance
$upd_proced_anexos = new tNG_multipleUpdate($conn_vacantes);
$tNGs->addTransaction($upd_proced_anexos);
// Register triggers
$upd_proced_anexos->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Update1");
$upd_proced_anexos->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$upd_proced_anexos->registerTrigger("END", "Trigger_Default_Redirect", 99, "admin_proced_anexos.php?IDdocumento={IDdocumento}&info=2");
$upd_proced_anexos->registerTrigger("AFTER", "Trigger_FileUpload", 97);
// Add columns
$upd_proced_anexos->setTable("proced_anexos");
$upd_proced_anexos->addColumn("IDdocumento", "STRING_TYPE", "POST", "IDdocumento");
$upd_proced_anexos->addColumn("documento", "STRING_TYPE", "POST", "documento");
$upd_proced_anexos->addColumn("IDvisible", "NUMERIC_TYPE", "POST", "IDvisible");
$upd_proced_anexos->addColumn("descripcion", "STRING_TYPE", "POST", "descripcion");
$upd_proced_anexos->addColumn("file", "FILE_TYPE", "FILES", "file");
$upd_proced_anexos->addColumn("version", "STRING_TYPE", "POST", "version");
$upd_proced_anexos->addColumn("anio", "DATE_TYPE", "POST", "anio");
$upd_proced_anexos->setPrimaryKey("IDanexo", "NUMERIC_TYPE", "GET", "IDanexo");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsproced_anexos = $tNGs->getRecordset("proced_anexos");
$row_rsproced_anexos = mysql_fetch_assoc($rsproced_anexos);
$totalRows_rsproced_anexos = mysql_num_rows($rsproced_anexos);

// borrar alternativo
if ((isset($_GET['borrar'])) && ($_GET['borrar'] != "")) {
  
  $borrado = $_GET['IDanexo'];
  $IDanexo = $_GET['IDanexo'];
  $deleteSQL = "DELETE FROM proced_anexos WHERE IDanexo ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: admin_proced_anexos.php?IDdireccion=".$IDdireccion."&IDDarea=".$IDDarea."&IDsubarea=".$IDsubarea."&info=3");
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
	<!-- /core JS files -->

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
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
                
<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Ingresa los datos solicitados.</p>
                    
                    <?php echo $tNGs->getErrorMsg(); ?>
                    <div>
                      <h1>
                        <?php if (@$_GET['IDanexo'] == "") { ?> Insertar <?php } else { ?>  Actualizar   <?php } ?> Documento </h1>
                      <div>
                        <form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" enctype="multipart/form-data" class="form-horizontal form-validate-jquery">
                          <?php $cnt1 = 0; ?>
                          <?php do { ?>
                            <?php $cnt1++; ?>
                            <?php if (@$totalRows_rsproced_anexos > 1) { ?>
                              <h2><?php echo NXT_getResource("Record_FH"); ?> <?php echo $cnt1; ?></h2>
                              <?php } ?>
                              
								<fieldset class="content-group">
                                                            

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre del Anexo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="documento_<?php echo $cnt1; ?>" id="documento_<?php echo $cnt1; ?>" class="form-control" placeholder="Indica el nombre del anexo" value="<?php echo KT_escapeAttribute($row_rsproced_anexos['documento']); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                              
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Descripción:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                                          <textarea required="required" name="descripcion_<?php echo $cnt1; ?>" rows="3" class="form-control" id="descripcion_<?php echo $cnt1; ?>" placeholder="Indica la descripción del anexo"><?php echo KT_escapeAttribute($row_rsproced_anexos['descripcion']); ?></textarea>
										</div>
									</div>
									<!-- /basic text input -->
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Visible:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDvisible_<?php echo $cnt1; ?>" id="IDvisible_<?php echo $cnt1; ?>"  class="form-control" required="required">
                                              <option value="1" <?php if (!(strcmp(1, KT_escapeAttribute($row_rsproced_anexos['IDvisible'])))) {echo "SELECTED";} ?>>Si</option>
                                              <option value="0" <?php if (!(strcmp(0, KT_escapeAttribute($row_rsproced_anexos['IDvisible'])))) {echo "SELECTED";} ?>>No</option>
                               				</select>
										</div>
									</div>
									<!-- /basic select -->

                            <!-- Basic text input -->
								  <div class="form-group">
										<label class="control-label col-lg-3">Archivo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="file" name="file_<?php echo $cnt1; ?>" id="file_<?php echo $cnt1; ?>" class="file-styled" placeholder="Seleccione Documento">
                                        <?php if (isset($_GET['IDanexo'])){ echo "<span><a href='proced/anexos/".$archivo."'>Descargar archivo</a></span>"; } ?>
										</div>
									</div>
									<!-- /basic text input -->
                              

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Versión:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
						<input type="text" name="version_<?php echo $cnt1; ?>" id="version_<?php echo $cnt1; ?>" class="form-control" placeholder="Indica la versión del anexo" value="<?php echo KT_escapeAttribute($row_rsproced_anexos['version']); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha del Documento:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="anio_<?php echo $cnt1; ?>" id="anio_<?php echo $cnt1; ?>" value="<?php if ($row_rsproced_anexos['anio'] == "") { echo "";} else  { echo KT_formatDate($row_rsproced_anexos['anio']); }?>" required="required">
									</div>
                                   </div>
                                  </div> 
									<!-- Fecha -->

 <div class="text-right">
                            <div>
                            
                            <input type="hidden" name="kt_pk_proced_anexos_<?php echo $cnt1; ?>" class="id_field" value="<?php echo KT_escapeAttribute($row_rsproced_anexos['kt_pk_proced_anexos']); ?>" />
                            <?php } while ($row_rsproced_anexos = mysql_fetch_assoc($rsproced_anexos)); ?>
                              <?php  if (@$_GET['IDanexo'] == "") { ?>
                                <input type="submit" class="btn btn-success" name="KT_Insert1" id="KT_Insert1" value="Agregar Documento" />
                                <?php } else { ?>
                                <input type="submit" class="btn btn-primary" name="KT_Update1" value="Actualizar" />
                                <?php } ?>
                              <a class="btn btn-default" href="admin_proced_directorio.php">Regresar</a>
                                <input type="hidden" name="IDdocumento" value="<?php echo $IDdocumento; ?>" />
                            </div>
                          </div>
                       </fieldset>
                        </form>


                      </div>
                    </div>
                    <p>&nbsp;</p>
                    
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