<?php require_once('Connections/vacantes.php'); ?>
<?php

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

$el_grupo = $_SESSION['el_grupo'];

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


if(isset($_GET['IDempleado'])) {
$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT * FROM prod_activos WHERE IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);
} else {$IDempleado = 0;}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'"); 
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_evaluados = "SELECT * FROM prod_activos";
$evaluados = mysql_query($query_evaluados, $vacantes) or die(mysql_error());
$row_evaluados = mysql_fetch_assoc($evaluados);
$totalRows_evaluados = mysql_num_rows($evaluados);

mysql_select_db($database_vacantes, $vacantes);
$query_evaluados2 = "SELECT * FROM prod_activos";
$evaluados2 = mysql_query($query_evaluados2, $vacantes) or die(mysql_error());
$row_evaluados2 = mysql_fetch_assoc($evaluados2);
$totalRows_evaluados2 = mysql_num_rows($evaluados2);

mysql_select_db($database_vacantes, $vacantes);
$query_grupo_ac = "SELECT * FROM sed_competencias_grupos WHERE IDgrupo = $el_grupo";
$grupo_ac = mysql_query($query_grupo_ac, $vacantes) or die(mysql_error());
$row_grupo_ac = mysql_fetch_assoc($grupo_ac);
$nombre_grupo = $row_grupo_ac['grupo'];

mysql_select_db($database_vacantes, $vacantes);
$query_grupos = "SELECT * FROM sed_competencias_grupos";
$grupos = mysql_query($query_grupos, $vacantes) or die(mysql_error());
$row_grupos = mysql_fetch_assoc($grupos);


// Make an insert transaction instance
$ins_sed_competencias_resultados = new tNG_multipleInsert($conn_vacantes);
$tNGs->addTransaction($ins_sed_competencias_resultados);
// Register triggers
$ins_sed_competencias_resultados->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_sed_competencias_resultados->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_sed_competencias_resultados->registerTrigger("END", "Trigger_Default_Redirect", 99, "prev_admin_comp.php?IDempleado={IDempleado}&info=1");
// Add columns
$ins_sed_competencias_resultados->setTable("sed_competencias_resultados");
$ins_sed_competencias_resultados->addColumn("IDempleado", "NUMERIC_TYPE", "POST", "IDempleado");
$ins_sed_competencias_resultados->addColumn("IDempleado_evaluador", "NUMERIC_TYPE", "POST", "IDempleado_evaluador");
$ins_sed_competencias_resultados->addColumn("IDtipo", "NUMERIC_TYPE", "POST", "IDtipo");
$ins_sed_competencias_resultados->addColumn("IDgrupo", "NUMERIC_TYPE", "POST", "IDgrupo");
$ins_sed_competencias_resultados->addColumn("anio", "NUMERIC_TYPE", "POST", "anio");
$ins_sed_competencias_resultados->addColumn("IDestatus", "NUMERIC_TYPE", "POST", "IDestatus");
$ins_sed_competencias_resultados->setPrimaryKey("IDevaluacion", "NUMERIC_TYPE");

// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rssed_competencias_resultados = $tNGs->getRecordset("sed_competencias_resultados");
$row_rssed_competencias_resultados = mysql_fetch_assoc($rssed_competencias_resultados);
$totalRows_rssed_competencias_resultados = mysql_num_rows($rssed_competencias_resultados);
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

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
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
                
<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
                      <h5> Agregar evaluación </h5>
						</div>

					<div class="panel-body">
							<p><strong>Grupo actual:</strong> <?php echo $nombre_grupo; ?></p>

							<p>Ingresa los datos solicitados.</p>
                    
                    <?php echo $tNGs->getErrorMsg(); ?>
                    <div>
                      <div>
                        <form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" enctype="multipart/form-data" class="form-horizontal form-validate-jquery">
                          <?php $cnt1 = 0; ?>
                          <?php do { ?>
                            <?php $cnt1++; ?>
                            <?php if (@$totalRows_rssed_competencias_resultados > 1) { ?>
                              <h2><?php echo NXT_getResource("Record_FH"); ?> <?php echo $cnt1; ?></h2>
                              <?php } ?>
                              
								<fieldset class="content-group">
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Evaluado:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDempleado_<?php echo $cnt1; ?>" id="IDempleado_<?php echo $cnt1; ?>" class="bootstrap-select" data-live-search="true" data-width="100%" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_evaluados['IDempleado']?>"<?php if (!(strcmp($row_evaluados['IDempleado'], $IDempleado)))
												  {echo "SELECTED";} ?>>(<?php echo $row_evaluados['IDempleado']?>) <?php echo $row_evaluados['emp_paterno']." ".$row_evaluados['emp_materno']." ".$row_evaluados['emp_nombre']?> - <?php echo $row_evaluados['denominacion']?></option>
																		  <?php
										} while ($row_evaluados = mysql_fetch_assoc($evaluados));
										  $rows = mysql_num_rows($evaluados);
										  if($rows > 0) {
											  mysql_data_seek($evaluados, 0);
											  $row_evaluados = mysql_fetch_assoc($evaluados);
										  } ?>
                               			 </select>
										</div>
									</div>
									<!-- /basic select -->
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Evaluador:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDempleado_evaluador_<?php echo $cnt1; ?>" id="IDempleado_evaluador_<?php echo $cnt1; ?>" class="bootstrap-select" data-live-search="true" data-width="100%" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_evaluados2['IDempleado']?>">(<?php echo $row_evaluados2['IDempleado']?>) <?php echo $row_evaluados2['emp_paterno']." ".$row_evaluados2['emp_materno']." ".$row_evaluados2['emp_nombre']?> - <?php echo $row_evaluados2['denominacion']?></option>
																		  <?php
										} while ($row_evaluados2 = mysql_fetch_assoc($evaluados2));
										  $rows = mysql_num_rows($evaluados2);
										  if($rows > 0) {
											  mysql_data_seek($evaluados2, 0);
											  $row_evaluados2 = mysql_fetch_assoc($evaluados2);
										  } ?>
                                			</select>
										</div>
									</div>
									<!-- /basic select -->
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDtipo_<?php echo $cnt1; ?>" id="IDtipo_<?php echo $cnt1; ?>" class="form-control" required="required" >
												<option value="">Seleccione una opción</option> 
												<option value="1">Autoevaluación</option> 
												<option value="3">Jefe Inmediato</option> 
												<option value="4">Par</option> 
												<option value="2">Colaborador</option> 
												<option value="5">Cliente Interno</option> 
                                		</select>
										</div>
									</div>
									<!-- /basic select -->
                              
                              
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Grupo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDgrupo_<?php echo $cnt1; ?>" id="IDgrupo_<?php echo $cnt1; ?>" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												<option value="<?php echo $row_grupos['IDgrupo']?>"><?php echo $row_grupos['grupo']?></option>
									<?php } while ($row_grupos = mysql_fetch_assoc($grupos));
									  $rows = mysql_num_rows($grupos);
									  if($rows > 0) {
										  mysql_data_seek($grupos, 0);
										  $row_grupos = mysql_fetch_assoc($grupos);
									  } ?>
                               				</select>
										</div>
									</div>
									<!-- /basic select -->
                              
							<div class="text-right">
                            <div>
                            
                            <input type="hidden" name="kt_pk_sed_competencias_resultados_<?php echo $cnt1; ?>" class="id_field" value="<?php echo KT_escapeAttribute($row_rssed_competencias_resultados['kt_pk_sed_competencias_resultados']); ?>" />
                            <?php } while ($row_rssed_competencias_resultados = mysql_fetch_assoc($rssed_competencias_resultados)); ?>

                                <input type="submit" class="btn btn-success" name="KT_Insert1" id="KT_Insert1" value="Agregar evaluación" />
                              <a class="btn btn-default" href="admin_comp.php">Regresar</a>
                                <input type="hidden" name="anio" value="<?php echo $anio; ?>" />
                                <input type="hidden" name="IDestatus" value="0" />
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