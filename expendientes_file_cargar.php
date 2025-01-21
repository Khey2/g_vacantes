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
$formValidation->addField("nombre", true, "", "", "", "", "");
$tNGs->prepareValidation($formValidation);
// End trigger

$IDempleado = $_GET['IDempleado'];
//carpeta

//start Trigger_FileUpload trigger
//remove this line if you want to edit the code by hand 
function Trigger_FileUpload(&$tNG) {

	$IDempleado = $_GET['IDempleado'];
	$IDempleado_carpeta = 'files/'.$IDempleado;
	if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}
	$IDempleado_carpeta = $IDempleado_carpeta."/";

  $uploadObj = new tNG_FileUpload($tNG);
  $uploadObj->setFormFieldName("nombre");
  $uploadObj->setDbFieldName("nombre");
  $uploadObj->setFolder($IDempleado_carpeta);
  $uploadObj->setMaxSize(3000000000);
  $uploadObj->setAllowedExtensions("pdf, jpg, png, jpeg, zip, jpeg, doc, docx, mp3, mov, mp4, ppt, pptx, xls, xlsx, rar");
  $uploadObj->setRename("custom");
  $uploadObj->setRenameRule("{tiempo.FECHA}_{empleado.rfc}_{IDtipo}.{KT_ext}");
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
$query_tiempo = "SELECT DATE_FORMAT(NOW( ), '%d%m%Y%H%i%S' ) as FECHA";
$tiempo = mysql_query($query_tiempo, $vacantes) or die(mysql_error());
$row_tiempo = mysql_fetch_assoc($tiempo);
$totalRows_tiempo = mysql_num_rows($tiempo);

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];

// mes y semana
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual

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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


mysql_select_db($database_vacantes, $vacantes);
$query_empleado = "SELECT exp_tipos.tipo, exp_files.nombre, prod_activos.IDempleado, prod_activos.fecha_alta, prod_activos.denominacion,  prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc,  prod_activos.IDsucursal, prod_activos.IDmatriz, prod_activos.IDpuesto, prod_activos.IDarea, prod_activos.IDsucursal FROM prod_activos LEFT JOIN exp_files ON exp_files.IDempleado = prod_activos.IDempleado LEFT JOIN exp_tipos ON exp_tipos.IDTipo = exp_files.IDtipo WHERE prod_activos.IDempleado = '$IDempleado'";
mysql_query("SET NAMES 'utf8'");
$empleado = mysql_query($query_empleado, $vacantes) or die(mysql_error());
$row_empleado = mysql_fetch_assoc($empleado);
$totalRows_empleado = mysql_num_rows($empleado);


mysql_select_db($database_vacantes, $vacantes);
$query_tipos = "SELECT * FROM exp_tipos ORDER BY IDtipo DESC";
mysql_query("SET NAMES 'utf8'");
$tipos = mysql_query($query_tipos, $vacantes) or die(mysql_error());
$row_tipos = mysql_fetch_assoc($tipos);
$totalRows_tipos = mysql_num_rows($tipos);

mysql_select_db($database_vacantes, $vacantes);
$query_files = "select nombre, coalesce(sum(case when IDtipo = 1 then 1 end), 0) as Doc1, coalesce(sum(case when IDtipo = 2 then 1 end), 0) as Doc2, coalesce(sum(case when IDtipo = 3 then 1 end), 0) as Doc3, coalesce(sum(case when IDtipo = 4 then 1 end), 0) as Doc4, coalesce(sum(case when IDtipo = 5 then 1 end), 0) as Doc5, coalesce(sum(case when IDtipo = 6 then 1 end), 0) as Doc6, coalesce(sum(case when IDtipo = 7 then 1 end), 0) as Doc7, coalesce(sum(case when IDtipo = 8 then 1 end), 0) as Doc8, coalesce(sum(case when IDtipo = 9 then 1 end), 0) as Doc9, coalesce(sum(case when IDtipo = 10 then 1 end), 0) as Doc10, coalesce(sum(case when IDtipo = 11 then 1 end), 0) as Doc11, coalesce(sum(case when IDtipo = 12 then 1 end), 0) as Doc12, coalesce(sum(case when IDtipo = 13 then 1 end), 0) as Doc13, coalesce(sum(case when IDtipo = 14 then 1 end), 0) as Doc14 from exp_files WHERE IDempleado = '$IDempleado'";
mysql_query("SET NAMES 'utf8'");
$files = mysql_query($query_files, $vacantes) or die(mysql_error());
$row_files = mysql_fetch_assoc($files);
$totalRows_files = mysql_num_rows($files);

// Make an insert transaction instance
$ins_exp_files = new tNG_insert($conn_vacantes);
$tNGs->addTransaction($ins_exp_files);
// Register triggers
$ins_exp_files->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_exp_files->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_exp_files->registerTrigger("END", "Trigger_Default_Redirect", 99, "expedientes_nuevo.php?IDempleado={IDempleado}&info=1");
$ins_exp_files->registerTrigger("AFTER", "Trigger_FileUpload", 97);
// Add columns
$ins_exp_files->setTable("exp_files");
$ins_exp_files->addColumn("nombre", "FILE_TYPE", "FILES", "nombre");
$ins_exp_files->addColumn("IDempleado", "STRING_TYPE", "POST", "IDempleado");
$ins_exp_files->addColumn("IDtipo", "NUMERIC_TYPE", "POST", "IDtipo");
$ins_exp_files->addColumn("borrado", "NUMERIC_TYPE", "VALUE", "0");
$ins_exp_files->addColumn("fecha", "STRING_TYPE", "POST", "fecha");
$ins_exp_files->addColumn("observaciones", "STRING_TYPE", "POST", "observaciones");
$ins_exp_files->addColumn("emp_paterno", "STRING_TYPE", "POST", "emp_paterno");
$ins_exp_files->addColumn("emp_materno", "STRING_TYPE", "POST", "emp_materno");
$ins_exp_files->addColumn("emp_nombre", "STRING_TYPE", "POST", "emp_nombre");
$ins_exp_files->addColumn("rfc", "STRING_TYPE", "POST", "rfc");
$ins_exp_files->addColumn("denominacion", "STRING_TYPE", "POST", "denominacion");
$ins_exp_files->addColumn("IDmatriz", "STRING_TYPE", "POST", "IDmatriz");
$ins_exp_files->addColumn("IDpuesto", "STRING_TYPE", "POST", "IDpuesto");
$ins_exp_files->addColumn("IDarea", "STRING_TYPE", "POST", "IDarea");
$ins_exp_files->addColumn("IDsucursal", "STRING_TYPE", "POST", "IDsucursal");
$ins_exp_files->setPrimaryKey("id", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsexp_files = $tNGs->getRecordset("exp_files");
$row_rsexp_files = mysql_fetch_assoc($rsexp_files);
$totalRows_rsexp_files = mysql_num_rows($rsexp_files);

// borrar alternativo
if ((isset($_GET['id'])) && ($_GET['id'] != "")) {
  
  $IDempleado = $_GET['IDempleado'];
  $borrado = $_GET['id'];
  $deleteSQL = "DELETE FROM exp_files WHERE id ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: expedientes_nuevo.php?IDempleado=$IDempleado&info=3");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>


<link href="includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
<script src="includes/common/js/base.js" type="text/javascript"></script>
<script src="includes/common/js/utility.js" type="text/javascript"></script>
<script src="includes/skins/style.js" type="text/javascript"></script>
<?php echo $tNGs->displayValidationRules();?>



</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>

<?php echo $tNGs->getErrorMsg();?>

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


	                <!-- Content area -->
				<div class="content">
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el contrato.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El documento no puede pesar más de 28MB.
					    </div>
					    <!-- /basic alert -->


                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Expendientes Digitales</h5>
						</div>

					<div class="panel-body">
					<p>Indica el tipo de documento a cargar. Algunos campos son obligatorios.</p>

					<h6><strong>Datos del Empleado</strong></h6>
                    <p><strong>No. de Empleado: </strong><?php echo $row_empleado['IDempleado']; ?></p>
                    <p><strong>Empleado: </strong><?php echo $row_empleado['emp_paterno']." ".$row_empleado['emp_materno']." ".$row_empleado['emp_nombre']; ?></p>
                    <p><strong>Puesto: </strong><?php echo $row_empleado['denominacion']; ?></p>
                    <p><strong>Sucursal: </strong><?php echo $row_matriz['matriz']; ?></p>
                    <p><strong>Fecha de Ingreso: </strong><?php echo date( 'd/m/Y', strtotime($row_empleado['fecha_alta']));; ?></p>
                    <p>&nbsp;</p>
                    <div>

		  						<form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" enctype="multipart/form-data" class="form-horizontal form-validate-jquery">
								<fieldset class="content-group">
        
                                            <!-- Basic text input -->
                                          	<div class="form-group">
                                                <label class="control-label col-lg-3">Documento:</label>
                                                <div class="col-lg-9">
                           					     <input type="file" name="nombre" id="nombre" class="file-styled" placeholder="Seleccione Documento" required="required">
                                                   <?php echo $tNGs->displayFieldError("exp_files", "nombre"); ?>
                                                </div>
                                            </div>
                                            <!-- /basic text input -->
                                                          
              							<!-- Basic select -->
										<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Documento:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<select name="IDtipo" id="IDtipo" class="select-search" required="required">
                						<option value="" <?php if (!(strcmp("", $row_rsexp_files['IDtipo']))) {echo "selected=\"selected\"";} ?>>Seleccione una opción</option>
					                    <?php do {  ?>
                <option value="<?php echo $row_tipos['IDTipo']?>"<?php if (!(strcmp($row_tipos['IDTipo'], $row_rsexp_files['IDtipo']))) {echo "selected=\"selected\"";} ?>><?php echo $row_tipos['tipo']?></option>
                  <?php
					} while ($row_tipos = mysql_fetch_assoc($tipos));
					  $rows = mysql_num_rows($tipos);
					  if($rows > 0) {
					  mysql_data_seek($tipos, 0);
					  $row_tipos = mysql_fetch_assoc($tipos);
					  } ?>
                </select>
                  <?php echo $tNGs->displayFieldError("exp_files", "IDtipo"); ?>
										</div>
									</div>

                                            <!-- Basic text input -->
                                          	<div class="form-group">
                                                <label class="control-label col-lg-3">Observaciones:</label>
                                                <div class="col-lg-9">
                                                  <textarea name="observaciones" rows="3" class="form-control" id="nombre" placeholder="Ingrese sus observaciones"></textarea>
                                                  <?php echo $tNGs->displayFieldError("exp_files", "observaciones"); ?>
                                              </div>
                                            </div>
                                            <!-- /basic text input -->


                                          <div class="text-right">
                                            <div>
                                        <input type="submit" name="KT_Insert1" id="KT_Insert1" value="Cargar" class="btn btn-primary" />
                         				<button type="button" onClick="window.location.href='expedientes_nuevo.php?IDempleado=<?php echo $IDempleado; ?>'" class="btn btn-default btn-icon">Cancelar</button>
									 	<input type="hidden" name="IDempleado" id="IDempleado"value="<?php echo $IDempleado; ?>" />
                                        <input type="hidden" name="fecha" id="fecha" value="<?php echo $fecha; ?>" />
                                        <input type="hidden" name="emp_paterno" id="emp_paterno" value="<?php echo $row_empleado['emp_paterno']; ?>" />
                                        <input type="hidden" name="emp_materno" id="emp_materno" value="<?php echo $row_empleado['emp_materno']; ?>" />
                                        <input type="hidden" name="emp_nombre" id="emp_nombre" value="<?php echo $row_empleado['emp_nombre']; ?>" />
                                        <input type="hidden" name="rfc" id="rfc" value="<?php echo $row_empleado['rfc']; ?>" />
                                        <input type="hidden" name="denominacion" id="denominacion" value="<?php echo $row_empleado['denominacion']; ?>" />
                                        <input type="hidden" name="IDmatriz" id="IDmatriz" value="<?php echo $row_empleado['IDmatriz']; ?>" />
                                        <input type="hidden" name="IDpuesto" id="IDpuesto" value="<?php echo $row_empleado['IDpuesto']; ?>" />
                                        <input type="hidden" name="IDarea" id="IDarea" value="<?php echo $row_empleado['IDarea']; ?>" />
                                        <input type="hidden" name="IDsucursal" id="IDsucursal" value="<?php echo $row_empleado['IDsucursal']; ?>" />
                                         </div>

                         </fieldset>
                         </form>
                      <p>&nbsp;</p>
                    </div>
                    </div>
                    </div>
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
