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


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$matriz_cv = htmlentities($_POST['matriz_cv'], ENT_COMPAT, '');
$direccion = htmlentities($_POST['direccion'], ENT_COMPAT, '');
$ubicacion = htmlentities($_POST['ubicacion'], ENT_COMPAT, '');
if (isset($_POST["copiar_correo"])) { $copiar_correo = 1; } else {$copiar_correo = 0; }
	
$updateSQL = sprintf("UPDATE vac_matriz SET matriz_cv=%s, direccion=%s, ubicacion=%s, telefono=%s, correo_JRH=%s, correo_RRH=%s, copiar_correo=%s WHERE IDmatriz=%s",
                       GetSQLValueString($matriz_cv, "text"),
                       GetSQLValueString($direccion, "text"),
                       GetSQLValueString($ubicacion, "text"),
                       GetSQLValueString($_POST['telefono'], "text"),
                       GetSQLValueString($_POST['correo_JRH'], "text"),
                       GetSQLValueString($_POST['correo_RRH'], "text"),
                       GetSQLValueString($copiar_correo, "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_usuarios_correo = "SELECT * FROM vac_usuarios WHERE FIND_IN_SET($la_matriz, IDmatrizes) AND candidatos = 1"; 
$usuarios_correo = mysql_query($query_usuarios_correo, $vacantes) or die(mysql_error());
$row_usuarios_correo = mysql_fetch_assoc($usuarios_correo);
$totalRows_usuarios_correo = mysql_num_rows($usuarios_correo);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz1 = "SELECT * FROM vac_matriz";
$matriz1 = mysql_query($query_matriz1, $vacantes) or die(mysql_error());
$row_matriz1 = mysql_fetch_assoc($matriz1);
$totalRows_matriz1 = mysql_num_rows($matriz1);
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
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- /core JS files -->

	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
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
	<script src="global_assets/js/plugins/notifications/bootbox.min.js"></script>
	<script src="global_assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_select2.js"></script>
	<script src="global_assets/js/demo_pages/2picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?> onLoad="showUser()">
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
							Se han actualizado correctamente los registros.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Actualizar Catálogos para Reclutamento</h5>
						</div>

					<div class="panel-body">
					<p>Ingresa la información solicitada.</br>
					Los campos marcados con <span class="text-danger">*</span> son obligatorios.</br>
                  <p>&nbsp;</p>
                    <div>
                    <div>
                    
                                      
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
						
						<legend class="text-semibold">Información de la Sucursal</legend>

						            <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Ubicación de la Sucursal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="matriz_cv" id="matriz_cv" class="form-control" placeholder="Localidad" value="<?php echo $row_matriz['matriz_cv']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Dirección:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="direccion" id="direccion" class="form-control" placeholder="Direccion" value="<?php echo $row_matriz['direccion']; ?>"  required="required">
										</div>
									</div>
									<!-- /basic text input -->
 
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Ubicación (google maps):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="ubicacion" id="ubicacion" class="form-control" placeholder="https://goo.gl/maps/vncuw85MHzuthKTD8" value="<?php echo $row_matriz['ubicacion']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Telefono de la Sucursal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="telefono" id="telefono" class="form-control" placeholder="55 55 55 55 55" value="<?php echo $row_matriz['telefono']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                                    
						<legend class="text-semibold">Envío de correos</legend>

                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Correo RRHH (copia de correo):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="correo_JRH" id="correo_JRH" class="select-search" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_usuarios_correo['IDusuario']?>" <?php if (!(strcmp($row_matriz['correo_JRH'], htmlentities($row_usuarios_correo['IDusuario'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>><?php echo $row_usuarios_correo['usuario_nombre']." ".$row_usuarios_correo['usuario_parterno']." ".$row_usuarios_correo['usuario_materno']?></option>
													  <?php
													 } while ($row_usuarios_correo = mysql_fetch_assoc($usuarios_correo));
													 $rows = mysql_num_rows($usuarios_correo);
													 if($rows > 0) {
													 mysql_data_seek($usuarios_correo, 0);
													 $row_usuarios_correo = mysql_fetch_assoc($usuarios_correo);
													 } ?>
											</select>
											</div>
									</div>
									<!-- /basic text input -->
                                                                        
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Correo Copia RRHH (copia de correos 2):</label>
										<div class="col-lg-6">
											<select name="correo_RRH" id="correo_RRH" class="select-search" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_usuarios_correo['IDusuario']?>" <?php if (!(strcmp($row_matriz['correo_RRH'], htmlentities($row_usuarios_correo['IDusuario'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>><?php echo $row_usuarios_correo['usuario_nombre']." ".$row_usuarios_correo['usuario_parterno']." ".$row_usuarios_correo['usuario_materno']?></option>
													  <?php
													 } while ($row_usuarios_correo = mysql_fetch_assoc($usuarios_correo));
													 $rows = mysql_num_rows($usuarios_correo);
													 if($rows > 0) {
													 mysql_data_seek($usuarios_correo, 0);
													 $row_usuarios_correo = mysql_fetch_assoc($usuarios_correo);
													 } ?>
											</select>
										</div>
										<div class="col-lg-3">
											<input type="checkbox" data-on-color="success" class="switch" data-on-text="Si" data-off-text="No" name="copiar_correo" id="copiar_correo" <?php if ($row_matriz['copiar_correo'] == 1) { echo "checked='checked'"; } ?> value="1" > Se envía copia del correo.
										</div>
									</div>
									<!-- /basic text input -->


                          <div class="text-right">
                            <div>
                         <button type="submit"  name="submit" class="btn btn-primary">Actualizar</button>
                            </div>
                          </div>

                      <input type="hidden" name="MM_update" value="form1">
                      <input type="hidden" name="IDmatriz" value="<?php echo $row_usuario['IDmatriz']; ?>">
                       </fieldset>
                      </form>

                      <p>&nbsp;</p>
                    </div>
                    </div>
                    </div>
				  </div>


<!-- Footer -->
					<div class="footer text-muted">
	&copy; 2020. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
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
<?php
mysql_free_result($variables);
?>
