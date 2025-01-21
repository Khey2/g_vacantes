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

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos ORDER BY vac_puestos.denominacion ASC";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

// Make an insert transaction instance
$ins_prod_plantilla = new tNG_multipleInsert($conn_vacantes);
$tNGs->addTransaction($ins_prod_plantilla);
// Register triggers
$ins_prod_plantilla->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_prod_plantilla->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_prod_plantilla->registerTrigger("END", "Trigger_Default_Redirect", 99, "admin_plantilla.php?info=2&IDmatriz={IDmatriz}");
// Add columns
$ins_prod_plantilla->setTable("prod_plantilla");
$ins_prod_plantilla->addColumn("IDpuesto", "NUMERIC_TYPE", "VALUE", "");
$ins_prod_plantilla->addColumn("IDmatriz", "NUMERIC_TYPE", "POST", "IDmatriz");
$ins_prod_plantilla->addColumn("IDpuesto", "NUMERIC_TYPE", "POST", "IDpuesto");
$ins_prod_plantilla->addColumn("autorizados", "NUMERIC_TYPE", "POST", "autorizados");
$ins_prod_plantilla->addColumn("sueldo_diario", "NUMERIC_TYPE", "POST", "sueldo_diario");
$ins_prod_plantilla->setPrimaryKey("IDplantilla", "NUMERIC_TYPE");

// Make an update transaction instance
$upd_prod_plantilla = new tNG_multipleUpdate($conn_vacantes);
$tNGs->addTransaction($upd_prod_plantilla);
// Register triggers
$upd_prod_plantilla->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Update1");
$upd_prod_plantilla->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$upd_prod_plantilla->registerTrigger("END", "Trigger_Default_Redirect", 99, "admin_plantilla.php?info=3&IDmatriz={IDmatriz}");
// Add columns
$upd_prod_plantilla->setTable("prod_plantilla");
$upd_prod_plantilla->addColumn("IDpuesto", "NUMERIC_TYPE", "CURRVAL", "");
$upd_prod_plantilla->addColumn("IDmatriz", "NUMERIC_TYPE", "POST", "IDmatriz");
$upd_prod_plantilla->addColumn("IDpuesto", "NUMERIC_TYPE", "POST", "IDpuesto");
$upd_prod_plantilla->addColumn("autorizados", "NUMERIC_TYPE", "POST", "autorizados");
$upd_prod_plantilla->addColumn("sueldo_diario", "NUMERIC_TYPE", "POST", "sueldo_diario");
$upd_prod_plantilla->setPrimaryKey("IDplantilla", "NUMERIC_TYPE", "GET", "IDplantilla");

// Make an instance of the transaction object
$del_prod_plantilla = new tNG_multipleDelete($conn_vacantes);
$tNGs->addTransaction($del_prod_plantilla);
// Register triggers
$del_prod_plantilla->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Delete1");
$del_prod_plantilla->registerTrigger("END", "Trigger_Default_Redirect", 99, "admin_plantilla.php?info=4&IDmatriz={IDmatriz}");
// Add columns
$del_prod_plantilla->setTable("prod_plantilla");
$del_prod_plantilla->setPrimaryKey("IDplantilla", "NUMERIC_TYPE", "GET", "IDplantilla");


// Execute all the registered transactions
$tNGs->executeTransactions();

// Get the transaction recordset
$rsprod_plantilla = $tNGs->getRecordset("prod_plantilla");
$row_rsprod_plantilla = mysql_fetch_assoc($rsprod_plantilla);
$totalRows_rsprod_plantilla = mysql_num_rows($rsprod_plantilla);
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

	<script src="assets/js/app.js"></script>
   	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	
	<script src="global_assets/js/demo_pages/datatables_advanced.js"></script>
    <!-- /theme JS files -->
    <link href="includes/skins/mxkollection3.css" rel="stylesheet" type="text/css" media="all" />
    <script src="includes/common/js/base.js" type="text/javascript"></script>
    <script src="includes/common/js/utility.js" type="text/javascript"></script>
    <script src="includes/skins/style.js" type="text/javascript"></script>
    <?php echo $tNGs->displayValidationRules();?>
    <script src="includes/nxt/scripts/form.js" type="text/javascript"></script>
    <script src="includes/nxt/scripts/form.js.php" type="text/javascript"></script>
    <script type="text/javascript">
$NXT_FORM_SETTINGS = {
  duplicate_buttons: true,
  show_as_grid: true,
  merge_down_value: true
}
    </script>
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
							<p>Acontinuación, edite la información de la plaza autorizada. Todos los Campos son Obligatorios.</p>
							<p>&nbsp;
                              <?php	echo $tNGs->getErrorMsg(); ?>                            
							<div>
							  <div>
							    <form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>">
							      <?php $cnt1 = 0; ?>
							      <?php do { ?>
							        <?php $cnt1++; ?>
							        <?php if (@$totalRows_rsprod_plantilla > 1) { ?>
							          <h2><?php echo NXT_getResource("Record_FH"); ?> <?php echo $cnt1; ?></h2>
							          <?php } ?>
                                      

										 <?php if (isset($_GET['IDplantilla'])) { ?>
											<div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">IDpuesto:</label>
												<div class="col-sm-9"><?php echo KT_escapeAttribute($row_rsprod_plantilla['IDpuesto']); ?>
												</div>
											</div>
	                                    </div>
										<?php } ?>											
                                        
                                        
                                            <div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Matriz:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDmatriz_<?php echo $cnt1; ?>" id="IDmatriz_<?php echo $cnt1; ?>" class="form-control" required="required">
							              <option value=""><?php echo NXT_getResource("Select one..."); ?></option>
							              <?php do {  ?>
							              <option value="<?php echo $row_matriz['IDmatriz']?>"<?php if (!(strcmp($row_matriz['IDmatriz'], $row_rsprod_plantilla['IDmatriz'])))
										  {echo "SELECTED";} ?>><?php echo $row_matriz['matriz']?></option>
										  <?php
                                            } while ($row_matriz = mysql_fetch_assoc($matriz));
                                              $rows = mysql_num_rows($matriz);
                                              if($rows > 0) {
                                                  mysql_data_seek($matriz, 0);
                                                  $row_matriz = mysql_fetch_assoc($matriz);
                                             }
                                            ?>
							              </select>
												</div>
											</div>
	                                    </div>


                                            <div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">puesto:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<select name="IDpuesto_<?php echo $cnt1; ?>" id="IDpuesto_<?php echo $cnt1; ?>" class="form-control" required="required">
							              <option value=""><?php echo NXT_getResource("Select one..."); ?></option>
							              <?php do {  ?>
							              <option value="<?php echo $row_puesto['IDpuesto']?>"<?php if (!(strcmp($row_puesto['IDpuesto'], $row_rsprod_plantilla['IDpuesto'])))
										  {echo "SELECTED";} ?>><?php echo $row_puesto['denominacion']?></option>
										  <?php
                                            } while ($row_puesto = mysql_fetch_assoc($puesto));
                                              $rows = mysql_num_rows($puesto);
                                              if($rows > 0) {
                                                  mysql_data_seek($puesto, 0);
                                                  $row_puesto = mysql_fetch_assoc($puesto);
                                             }
                                            ?>
							              </select>
												</div>
											</div>
	                                    </div>


                                            <div class="form-group">
			                                    <div class="row">
												<label class="control-label col-sm-3" data-popup="tooltip-custom" title="Motivo">Plazas autorizadas:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" name="autorizados_<?php echo $cnt1; ?>" id="autorizados_<?php echo $cnt1; ?>" class="form-control" required="required"
                                            value="<?php echo KT_escapeAttribute($row_rsprod_plantilla['autorizados']); ?>" />
												</div>
											</div>
	                                    </div>


                                            <div class="form-group">
			                                    <div class="row">
											<label class="control-label col-sm-3" data-popup="tooltip-custom" title="sueldo_diario">Sueldo Diario:<span class="text-danger">*</span></label>
												<div class="col-sm-9">
											<input type="text" name="sueldo_diario_<?php echo $cnt1; ?>" id="sueldo_diario_<?php echo $cnt1; ?>" class="form-control" required="required"
                                            value="<?php echo KT_escapeAttribute($row_rsprod_plantilla['sueldo_diario']); ?>" />
												</div>
											</div>
	                                    </div>

							      
							        <input type="hidden" name="kt_pk_prod_plantilla_<?php echo $cnt1; ?>" class="id_field" value="<?php echo KT_escapeAttribute($row_rsprod_plantilla['kt_pk_prod_plantilla']); ?>" />
							        <?php } while ($row_rsprod_plantilla = mysql_fetch_assoc($rsprod_plantilla)); ?>
							      
                                  
                                 <div class="modal-footer">
                                  
							          <?php  if (@$_GET['IDplantilla'] == "") { ?>
							          <input type="submit" name="KT_Insert1" id="KT_Insert1" value="<?php echo NXT_getResource("Insert_FB"); ?>" class="btn btn-primary" />
							            <?php } else { ?>
							            <input type="submit" name="KT_Update1" value="<?php echo NXT_getResource("Update_FB"); ?>"class="btn btn-primary" />
							            <input type="submit" name="KT_Delete1" value="<?php echo NXT_getResource("Delete_FB"); ?>" class="btn btn-danger" onclick="return confirm('<?php echo NXT_getResource("Are you sure?"); ?>');" />
							            <?php }      ?>
							            <a class="btn btn-default" href="admin_plantilla.php?IDmatriz=<?php echo $_GET['IDmatriz']; ?>">Cancelar</a>

                                      
                                      								</div>
						        </form>
                            </p>
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