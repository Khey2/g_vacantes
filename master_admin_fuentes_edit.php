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
$mis_areas = $row_usuario['IDareas'];
if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}
$el_mes = $_SESSION['el_mes'];
$nivel = $_SESSION['kt_login_level'];

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_fuente = "SELECT * FROM vac_fuentes";
$fuente = mysql_query($query_fuente, $vacantes) or die(mysql_error());
$row_fuente = mysql_fetch_assoc($fuente);
$totalRows_fuente = mysql_num_rows($fuente);

// Make an insert transaction instance
$ins_vac_fuentes = new tNG_multipleInsert($conn_vacantes);
$tNGs->addTransaction($ins_vac_fuentes);
// Register triggers
$ins_vac_fuentes->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_vac_fuentes->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_vac_fuentes->registerTrigger("END", "Trigger_Default_Redirect", 99, "master_admin_fuentes.php?info=1");
// Add columns
$ins_vac_fuentes->setTable("vac_fuentes");
$ins_vac_fuentes->addColumn("fuente", "STRING_TYPE", "POST", "fuente");
$ins_vac_fuentes->addColumn("costo", "CHECKBOX_1_0_TYPE", "POST", "costo");
$ins_vac_fuentes->setPrimaryKey("IDfuente", "NUMERIC_TYPE");

// Make an update transaction instance
$upd_vac_fuentes = new tNG_multipleUpdate($conn_vacantes);
$tNGs->addTransaction($upd_vac_fuentes);
// Register triggers
$upd_vac_fuentes->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Update1");
$upd_vac_fuentes->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$upd_vac_fuentes->registerTrigger("END", "Trigger_Default_Redirect", 99, "master_admin_fuentes.php?info=2");
// Add columns
$upd_vac_fuentes->setTable("vac_fuentes");
$upd_vac_fuentes->addColumn("fuente", "STRING_TYPE", "POST", "fuente");
$upd_vac_fuentes->addColumn("costo", "CHECKBOX_1_0_TYPE", "POST", "costo");
$upd_vac_fuentes->setPrimaryKey("IDfuente", "NUMERIC_TYPE", "GET", "IDfuente");

// borrar alternativo
if ((isset($_GET['IDfuente_borrar'])) && ($_GET['IDfuente_borrar'] != "")) {
  
  $borrado = $_GET['IDfuente_borrar'];
  $deleteSQL = "DELETE FROM vac_fuentes WHERE IDfuente ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: master_admin_fuentes.php?info=3");
}


// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsvac_fuentes = $tNGs->getRecordset("vac_fuentes");
$row_rsvac_fuentes = mysql_fetch_assoc($rsvac_fuentes);
$totalRows_rsvac_fuentes = mysql_num_rows($rsvac_fuentes);
?>
<!DOCTYPE html>
<html lang="en" xmlns:wdg="http://ns.adobe.com/addt">
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


	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>

	<!-- /theme JS files -->
	<script src="includes/common/js/base.js" type="text/javascript"></script>
	<script src="includes/common/js/utility.js" type="text/javascript"></script>
	<link href="includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
	<script src="includes/skins/style.js" type="text/javascript"></script>
	<?php echo $tNGs->displayValidationRules();?>
	<script src="includes/nxt/scripts/form.js" type="text/javascript"></script>
	<script src="includes/nxt/scripts/form.js.php" type="text/javascript"></script>
	<script type="text/javascript">
$NXT_FORM_SETTINGS = {
  duplicate_buttons: false,
  show_as_grid: false,
  merge_down_value: false
}
    </script>
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

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title"><?php if (@$_GET['IDfuente'] == "") { echo "Agregar Vacante"; } else { echo "Actualizar Vacante"; } ?></h5>
						</div>

					<div class="panel-body">
					<p>Ingresa la información solicitada. Los campos marcados con <span class="text-danger">*</span> son obligatorios.</br>
  					Una vez guardada la vacante, algunos campos no se pueden editar.</p>
                    <p>&nbsp;</p>
                    
                          <form method="post" id="form1" name="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" class="form-horizontal form-validate-jquery">
                    
                            <?php $cnt1 = 0; ?>
                            <?php do { ?>
                            <?php $cnt1++; ?>
                            <?php if (@$totalRows_rsvac_fuentes > 1) {?>
                            <?php echo NXT_getResource("Record_FH"); ?> <?php echo $cnt1; ?>  <?php } ?>
                              
                              <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Fuente:</label>
										<div class="col-lg-9">
						                  <input type="text" name="fuente_<?php echo $cnt1; ?>" id="fuente_<?php echo $cnt1; ?>" class="form-control" value="<?php echo KT_escapeAttribute($row_rsvac_fuentes['fuente']); ?>" required="required">
										</div>
									</div>
							 <!-- /basic text input -->
                             
                             
                              <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Costo:</label>
										<div class="col">
										<?php if ($row_rsvac_fuentes['costo'] ==1) { ?>
                                         <input <?php if (!(strcmp($row_rsvac_fuentes['costo'],1))) {echo "checked=\"checked\"";} ?> 
                                         name="costo_<?php echo $cnt1; ?>" type="checkbox" class="switch" value="1" checked data-on-text="Con&nbsp;costo" data-off-text="Sin&nbsp;costo">
                                         <?php } else { ?>
                                         <input <?php if (!(strcmp($row_rsvac_fuentes['costo'],1))) {echo "checked=\"checked\"";} ?> 
                                         name="costo_<?php echo $cnt1; ?>" type="checkbox" class="switch" value="1" data-on-text="Con&nbsp;costo" data-off-text="Sin&nbsp;costo">
                                         <?php } ?>
                                        <div>
							        </div>
							 <!-- /basic text input -->
                              
                              <input type="hidden" name="kt_pk_vac_fuentes_<?php echo $cnt1; ?>" value="<?php echo KT_escapeAttribute($row_rsvac_fuentes['kt_pk_vac_fuentes']); ?>" />
                              
                              <?php } while ($row_rsvac_fuentes = mysql_fetch_assoc($rsvac_fuentes)); ?>
                              
                       <p>&nbsp;</p>
                       <p>&nbsp;</p>
                                <?php if (@$_GET['IDfuente'] == "") {?>
                                  <input type="submit" name="KT_Insert1" id="KT_Insert1" class="btn btn-primary btn-icon" value="Agregar" />
                                  <?php  } else { ?>
                                  <input type="submit" name="KT_Update1" class="btn btn-primary btn-icon" value="Actualizar" />
                                  <?php }      ?>
                                  <input type="button" name="KT_Cancel1" class="btn btn-info btn-icon" value="<?php echo NXT_getResource("Cancel_FB"); ?>" onclick="return UNI_navigateCancel(event, 'master_admin_fuentes.php')" />
                                  <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                          </form>
                       <p>&nbsp;</p>
                    </div>
              </div>
              </div>

					<!-- /Contenido -->
                    
                    
                <!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el registro?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="master_admin_fuentes_edit.php?IDfuente_borrar=<?php echo $_GET['IDfuente']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->


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