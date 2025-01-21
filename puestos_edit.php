<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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
$la_matriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];


if (isset($_GET["IDpuesto"])) {
$IDpuesto = $_GET["IDpuesto"];
mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDpuesto = '$IDpuesto'";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);
}

if (isset($_GET["IDpuesto"]) && $row_puesto['manual'] == 1){$manual = 1;} else {$manual = 0;}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE vac_puestos SET manual=%s, denominacion=%s, IDarea=%s, visible=%s,  tipo=%s, IDaplica_PROD=%s, dias=%s, descrito=%s WHERE IDpuesto=%s",
                       GetSQLValueString($_POST['manual'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['visible'], "int"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['IDaplica_PROD'], "int"),
                       GetSQLValueString($_POST['dias'], "int"),
                       GetSQLValueString($_POST['descrito'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "puestos.php?info=2";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
  $insertSQL = sprintf("INSERT INTO vac_puestos (manual, denominacion, IDarea, visible, tipo, dias, descrito, IDaplica_PROD) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['manual'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['visible'], "int"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['dias'], "int"),
                       GetSQLValueString($_POST['descrito'], "int"),
                       GetSQLValueString($_POST['IDaplica_PROD'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "puestos.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

// borrar alternativo
if ((isset($_GET['IDpuesto_borrar'])) && ($_GET['IDpuesto_borrar'] != "")) {
  
  $borrado = $_GET['IDpuesto_borrar'];
  $deleteSQL = "DELETE FROM vac_puestos WHERE IDpuesto ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: puestos.php?info=3");
}


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

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

	<script src="assets/js/app.js"></script>
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/login_validation.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<!-- /theme JS files -->

</head>

<body class="has-detached-right">	<?php require_once('assets/mainnav.php'); ?>
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
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
							<h5 class="panel-title">Editar Puesto</h5>
                              <?php } else { ?>
							<h5 class="panel-title">Agregar Puesto</h5>
                                <?php }  ?>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                            
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">IDPuesto:</label>
										<div class="col-lg-9">
						                  <p><strong><?php echo $row_puesto['IDpuesto']; ?> </strong></p>
										</div>
									</div>
									<!-- /basic text input -->
                                <?php }  ?>


                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Denominación:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
						<input type="text" name="denominacion" id="denominacion" class="form-control" value="<?php echo htmlentities($row_puesto['denominacion'], ENT_COMPAT, ''); ?>" 
                        required="required">
                              <?php } else { ?>
						<input type="text" name="denominacion" id="denominacion" class="form-control" value="" 
                        required="required">
                                <?php }  ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea" id="IDarea" class="form-control" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
												  <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $row_puesto['IDarea']))) 
												  {echo "SELECTED";} ?>><?php echo $row_area['area']?></option>
                              <?php } else { ?>
												  <option value="<?php echo $row_area['IDarea']?>"><?php echo $row_area['area']?></option>
                                <?php }  ?>
												  <?php
												 } while ($row_area = mysql_fetch_assoc($area));
												   $rows = mysql_num_rows($area);
												   if($rows > 0) {
												   mysql_data_seek($area, 0);
												   $row_area = mysql_fetch_assoc($area);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="tipo" id="tipo" class="form-control" >
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SUCURSAL</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CORPORATIVO</option>
                                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_puesto['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESPECIALES</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">SUCURSAL</option>
                                           <option value="2">CORPORATIVO</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Visible para Sucursales:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="visible" id="visible" class="form-control" >
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['visible'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['visible'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Si</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['visible'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>No</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">Si</option>
                                           <option value="2">No</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->



									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica Productividad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 										<select name="IDaplica_PROD" id="IDaplica_PROD" class="form-control" >
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                           <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['IDaplica_PROD'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                           <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['IDaplica_PROD'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SI</option>
                                           <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['IDaplica_PROD'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NO</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">SI</option>
                                           <option value="2">NO</option>
                                <?php }  ?>
                                        </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto Descrito:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="descrito" id="descrito" class="form-control" >
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Sin captura</option>
                                            <option value="0" <?php if (!(strcmp(0, htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Sin captura</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>En captura</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>En revisión RH</option>
                                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Terminado</option>
                              <?php } else { ?>
                                           <option value="">Sin Captura</option>
                                           <option value="0">Sin Captura</option>
                                           <option value="1">Captuarado</option>
                                           <option value="2">Validado</option>
                                           <option value="3">Terminado</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->
                                    
                                                                         <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Días de Cobertura:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
						<input type="text" name="dias" id="dias" class="form-control" value="<?php echo htmlentities($row_puesto['dias'], ENT_COMPAT, ''); ?>" 
                        required="required">
                              <?php } else { ?>
						<input type="text" name="dias" id="dias" class="form-control" value=""  required="required">
                                <?php }  ?>
										</div>
									</div>
									<!-- /basic text input -->

                                    
                                    
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                         <button type="submit"  name="KT_Update1" class="btn btn-primary">Actualizar</button>
                              <?php  if ($manual == 1) { ?>
                         <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                              <?php } ?>
                         <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDpuesto" value="<?php echo $row_puesto['IDpuesto']; ?>">
                              <?php } else { ?>
                                <input type="submit" name="KT_Insert1" class="btn btn-primary" id="KT_Insert1" value="Agregar" />
                                <?php }  ?>
                    	 <button type="button" onClick="window.location.href='puestos.php'" class="btn btn-default btn-icon">Cancelar</button>
                         <input type="hidden" name="MM_insert" value="form1" />
                         <input type="hidden" name="manual" value="1" />
                            </form>
                            <p>&nbsp;</p>



                  <!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la vacante?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="puestos_edit.php?IDpuesto_borrar=<?php echo $row_puesto['IDpuesto']; ?>">Si borrar</a>
								</div>
							</div>
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