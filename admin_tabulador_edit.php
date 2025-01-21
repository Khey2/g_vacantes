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
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

if (isset($_GET['IDtabulador'])) { 
$IDtabulador = $_GET['IDtabulador'];
mysql_select_db($database_vacantes, $vacantes);
$query_tabulador = "SELECT * FROM vac_tabulador WHERE vac_tabulador.IDtabulador = '$IDtabulador'";
$tabulador = mysql_query($query_tabulador, $vacantes) or die(mysql_error());
$row_tabulador = mysql_fetch_assoc($tabulador);
$totalRows_tabulador = mysql_num_rows($tabulador);
}

$fecha = date("Y-m-d");
$el_mes = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$la_fecha = date("Y-m-d", strtotime($fecha . $desfase)); //ayer 
$semana = date("W", strtotime($la_fecha)); //la semana empieza ayer 


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

$query_areas = "SELECT * FROM vac_areas";
$areas = mysql_query($query_areas, $vacantes) or die(mysql_error());
$row_areas = mysql_fetch_assoc($areas);
$totalRows_areas = mysql_num_rows($areas);

$query_sucursal = "SELECT * FROM vac_sucursal";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos ORDER BY vac_puestos.denominacion ASC";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

// Make an insert transaction instance
$ins_vac_tabulador = new tNG_multipleInsert($conn_vacantes);
$tNGs->addTransaction($ins_vac_tabulador);
// Register triggers
$ins_vac_tabulador->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Insert1");
$ins_vac_tabulador->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$ins_vac_tabulador->registerTrigger("END", "Trigger_Default_Redirect", 99, "admin_tabulador.php?info=1");
// Add columns
$ins_vac_tabulador->setTable("vac_tabulador");
$ins_vac_tabulador->addColumn("IDmatriz", "NUMERIC_TYPE", "POST", "IDmatriz");
$ins_vac_tabulador->addColumn("IDarea", "NUMERIC_TYPE", "POST", "IDarea");
$ins_vac_tabulador->addColumn("IDpuesto", "NUMERIC_TYPE", "POST", "IDpuesto");
$ins_vac_tabulador->addColumn("IDnivel", "STRING_TYPE", "POST", "IDnivel");
$ins_vac_tabulador->addColumn("sueldo_diario", "DOUBLE_TYPE", "POST", "sueldo_diario");
$ins_vac_tabulador->addColumn("sueldo_mensual", "DOUBLE_TYPE", "POST", "sueldo_mensual");
$ins_vac_tabulador->addColumn("sueldo_integrado", "DOUBLE_TYPE", "POST", "sueldo_integrado");
$ins_vac_tabulador->setPrimaryKey("IDtabulador", "NUMERIC_TYPE");

// Make an update transaction instance
$upd_vac_tabulador = new tNG_multipleUpdate($conn_vacantes);
$tNGs->addTransaction($upd_vac_tabulador);
// Register triggers
$upd_vac_tabulador->registerTrigger("STARTER", "Trigger_Default_Starter", 1, "POST", "KT_Update1");
$upd_vac_tabulador->registerTrigger("BEFORE", "Trigger_Default_FormValidation", 10, $formValidation);
$upd_vac_tabulador->registerTrigger("END", "Trigger_Default_Redirect", 99, "admin_tabulador.php?info=2");
// Add columns
$upd_vac_tabulador->setTable("vac_tabulador");
$upd_vac_tabulador->addColumn("sueldo_diario", "DOUBLE_TYPE", "POST", "sueldo_diario");
$upd_vac_tabulador->addColumn("sueldo_mensual", "DOUBLE_TYPE", "POST", "sueldo_mensual");
$upd_vac_tabulador->addColumn("sueldo_integrado", "DOUBLE_TYPE", "POST", "sueldo_integrado");
$upd_vac_tabulador->addColumn("IDnivel", "STRING_TYPE", "POST", "IDnivel");
$upd_vac_tabulador->setPrimaryKey("IDtabulador", "NUMERIC_TYPE", "GET", "IDtabulador");

// Execute all the registered transactions
$tNGs->executeTransactions();

// borrar alternativo
if ((isset($_GET['IDtabulador_borrar'])) && ($_GET['IDtabulador_borrar'] != "")) {
  
  $borrado = $_GET['IDtabulador_borrar'];
  $deleteSQL = "DELETE FROM vac_tabulador WHERE IDtabulador ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: admin_tabulador.php?info=3");
}

// Get the transaction recordset
$rsvac_tabulador = $tNGs->getRecordset("vac_tabulador");
$row_rsvac_tabulador = mysql_fetch_assoc($rsvac_tabulador);
$totalRows_rsvac_tabulador = mysql_num_rows($rsvac_tabulador);
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>

	<script src="includes/common/js/base.js" type="text/javascript"></script>
	<script src="includes/common/js/utility.js" type="text/javascript"></script>
	<script type="text/javascript" src="includes/common/js/sigslot_core.js"></script>
    <script type="text/javascript" src="includes/wdg/classes/MXWidgets.js"></script>
    <script type="text/javascript" src="includes/wdg/classes/MXWidgets.js.php"></script>
    <script type="text/javascript" src="includes/wdg/classes/JSRecordset.js"></script>
    <script type="text/javascript" src="includes/wdg/classes/DependentDropdown.js"></script>

<?php
//begin JSRecordset
$jsObject_sucursal = new WDG_JsRecordset("sucursal");
echo $jsObject_sucursal->getOutput();
//end JSRecordset

//begin JSRecordset
$jsObject_puesto = new WDG_JsRecordset("puesto");
echo $jsObject_puesto->getOutput();
//end JSRecordset
?>
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
                      <div>
                        <p><strong>Instrucciones:</strong> Ingrese la información solicitada.</p>
                       
                       
                          <form method="post" id="form1" action="<?php echo KT_escapeAttribute(KT_getFullUri()); ?>" class="form-horizontal form-validate-jquery">
                            <?php $cnt1 = 0; ?>
                            <?php do { ?>
                              <?php $cnt1++; ?>
                              <?php if (@$totalRows_rsvac_tabulador > 1) { ?>
                                <h2></h2>
                                <?php } ?>
                                
                                
                                <?php if (@$_GET['IDtabulador'] == "") { ?>
                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz_<?php echo $cnt1; ?>" id="IDmatriz_<?php echo $cnt1; ?>" class="form-control" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_lmatriz['IDmatriz']?>"<?php if (!(strcmp($row_lmatriz['IDmatriz'], $row_rsvac_tabulador['IDmatriz']))) {echo "SELECTED";} ?>><?php echo $row_lmatriz['matriz']?></option>
													  <?php
													 } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
													 $rows = mysql_num_rows($lmatriz);
													 if($rows > 0) {
													 mysql_data_seek($lmatriz, 0);
													 $row_lmatriz = mysql_fetch_assoc($lmatriz);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->
                                  <?php } ?>
                                  
                                                                  
                                  
                                <?php if (@$_GET['IDtabulador'] == "") {?>
                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea_<?php echo $cnt1; ?>" id="IDarea_<?php echo $cnt1; ?>" class="form-control" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_areas['IDarea']?>"<?php if (!(strcmp($row_areas['IDarea'], $row_rsvac_tabulador['IDarea']))) 
												  {echo "SELECTED";} ?>><?php echo $row_areas['area']?></option>
												  <?php
												 } while ($row_areas = mysql_fetch_assoc($areas));
												   $rows = mysql_num_rows($areas);
												   if($rows > 0) {
												   mysql_data_seek($areas, 0);
												   $row_areas = mysql_fetch_assoc($areas);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->
                                  <?php } ?>
                                  
                                  
                                <?php if (@$_GET['IDtabulador'] == "") { ?>
                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto_<?php echo $cnt1; ?>" class="form-control" id="IDpuesto_<?php echo $cnt1; ?>" wdg:subtype="DependentDropdown" required="required" wdg:type="widget" wdg:recordset="puesto" wdg:displayfield="denominacion" wdg:valuefield="IDpuesto" wdg:fkey="IDarea" wdg:triggerobject="IDarea_<?php echo $cnt1; ?>" wdg:selected="<?php echo $row_rsvac_vacante['IDpuesto'] ?>">
											  <option value="">Seleccione una opción</option>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->
                                  <?php } ?>


								    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nivel:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDnivel_<?php echo $cnt1; ?>" id="IDnivel_<?php echo $cnt1; ?>" class="form-control" >
												<option value="">Seleccione una opción</option> 
												<option value="A"<?php if ($row_rsvac_tabulador['IDnivel'] == "A") {echo "SELECTED";} ?>>A</option>
												<option value="B"<?php if ($row_rsvac_tabulador['IDnivel'] == "B") {echo "SELECTED";} ?>>B</option>
												<option value="C"<?php if ($row_rsvac_tabulador['IDnivel'] == "C") {echo "SELECTED";} ?>>C</option>
												<option value="D"<?php if ($row_rsvac_tabulador['IDnivel'] == "D") {echo "SELECTED";} ?>>D</option>
												<option value="0"<?php if ($row_rsvac_tabulador['IDnivel'] == "0") {echo "SELECTED";} ?>>0 (no aplica)</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                  
                                    <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Diario:</label>
										<div class="col-lg-9">
						<input type="text" name="sueldo_diario_<?php echo $cnt1; ?>" id="sueldo_diario_<?php echo $cnt1; ?>" class="form-control" placeholder="Indica el sueldo diario." value="<?php echo KT_escapeAttribute($row_rsvac_tabulador['sueldo_diario']); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                
                                    <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Mensual Total:</label>
										<div class="col-lg-9">
						<input type="text" name="sueldo_mensual_<?php echo $cnt1; ?>" id="sueldo_mensual_<?php echo $cnt1; ?>" class="form-control" placeholder="Indica el sueldo total." value="<?php echo KT_escapeAttribute($row_rsvac_tabulador['sueldo_mensual']); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                  
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Integrado:</label>
										<div class="col-lg-9">
						<input type="text" name="sueldo_integrado_<?php echo $cnt1; ?>" id="sueldo_integrado_<?php echo $cnt1; ?>" class="form-control" placeholder="Indica el sueldo integrado." value="<?php echo KT_escapeAttribute($row_rsvac_tabulador['sueldo_integrado']); ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                  
                              <input type="hidden" name="kt_pk_vac_tabulador_<?php echo $cnt1; ?>" class="id_field" value="<?php echo KT_escapeAttribute($row_rsvac_tabulador['kt_pk_vac_tabulador']); ?>" />
                              <?php } while ($row_rsvac_tabulador = mysql_fetch_assoc($rsvac_tabulador)); ?>



                                <?php  if (@$_GET['IDtabulador'] == "") { ?>
                                  <input type="submit" name="KT_Insert1" id="KT_Insert1" value="Agregar" class="btn btn-primary" />
                                  <?php } else { ?>
                                  <input type="submit" name="KT_Update1" value="Actualizar" class="btn btn-primary" />
                                <?php } ?>
                                <input type="button" name="KT_Cancel1" value="Cancelar" onClick="window.location.href='admin_tabulador.php'"  class="btn btn-info"/>
                         <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>

                          </form>
                      </div>
					</div>
				  </div>
					<!-- /panel heading options -->
                    
                    
                    <!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el Tabulador?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admin_tabulador_edit.php?IDtabulador_borrar=<?php echo $IDtabulador;?>">Si borrar</a>
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