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
$mis_areas = $row_usuario['IDmatrizes'];
$la_matriz = $row_usuario['IDmatriz'];
$las_matrizes = $row_usuario['IDmatrizes'];

$IDusuario = $_GET['IDusuario'];
mysql_select_db($database_vacantes, $vacantes);
$query_candidatos = "SELECT * FROM cv_activos WHERE IDusuario = '$IDusuario'";
$candidatos = mysql_query($query_candidatos, $vacantes) or die(mysql_error());
$row_candidatos = mysql_fetch_assoc($candidatos);
$totalRows_candidatos = mysql_num_rows($candidatos);


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$fecha2 = date('Y-m-d', strtotime($_POST['c_fecha_nacimiento']));	
$updateSQL = sprintf("UPDATE cv_activos SET a_paterno=%s, a_materno=%s, a_nombre=%s, a_rfc=%s, a_correo=%s, a_curp=%s, a_sexo=%s, a_imss=%s, IDnacionalidad=%s, a_estado_civil=%s,  c_fecha_nacimiento=%s, a_afore=%s, a_licencia=%s, a_pasaporte=%s, a_cedula_profesional=%s, a_cartilla=%s, pretension=%s WHERE IDusuario=%s",
                       GetSQLValueString(htmlentities($_POST['a_paterno'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_materno'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_nombre'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_rfc'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_correo'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_curp'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_sexo'], ENT_COMPAT, ''), "int"),
                       GetSQLValueString(htmlentities($_POST['a_imss'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['IDnacionalidad'], ENT_COMPAT, ''), "int"),
                       GetSQLValueString(htmlentities($_POST['a_estado_civil'], ENT_COMPAT, ''), "int"),
                       GetSQLValueString(htmlentities($fecha2, ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_afore'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_licencia'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_pasaporte'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_cedula_profesional'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['a_cartilla'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['pretension'], ENT_COMPAT, ''), "text"),
                       GetSQLValueString(htmlentities($_POST['IDusuario'], ENT_COMPAT, ''), "text"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, 	$vacantes) or die(mysql_error());

  $updateGoTo = "cv_a.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$la_matriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);

$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
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
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
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

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<!-- /theme JS files -->
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
               

                		<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han guardado correctamente los datos capturados.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Datos Personales</h5>
						</div>

					<div class="panel-body">
					<p><strong>Instrucciones</strong>: ingrese la información solicitada. </br>
                    Los campos marcados con <span class="text-danger">*</span> son obligatorios.</p>
                      
					<legend class="text-semibold">Candidato: <?php echo $row_candidatos['a_paterno'] . " " . $row_candidatos['a_materno']. " " . $row_candidatos['a_nombre']; ?></legend>

                        
                      
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Usuario:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_correo" id="a_correo" readonly="readonly" class="form-control"  value="<?php echo $row_candidatos['a_correo']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_paterno" id="a_paterno" class="form-control" placeholder="Apellido Paterno" value="<?php echo $row_candidatos['a_paterno']; ?>"  required>
										</div>
									</div>
									<!-- /basic text input -->
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Materno:</label>
										<div class="col-lg-9">
											<input type="text" name="a_materno" id="a_materno" class="form-control" placeholder="Apellido Materno" value="<?php echo $row_candidatos['a_materno']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre(s):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_nombre" id="a_nombre" class="form-control" placeholder="Nombres" value="<?php echo $row_candidatos['a_nombre']; ?>"  required>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">RFC:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_rfc" id="a_rfc" class="form-control" placeholder="RFC a 13 posiciones" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo $row_candidatos['a_rfc']; ?>"  maxlength="13" minlength="10" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">CURP:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_curp" id="a_curp" class="form-control" placeholder="CURP a 18 posiciones" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo $row_candidatos['a_curp']; ?>"  maxlength="18" minlength="18" required="required">
										</div>
									</div>
									<!-- /basic text input -->
                      
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sexo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="a_sexo" id="a_sexo" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['a_sexo']))) {echo "SELECTED";} ?>>Hombre</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['a_sexo']))) {echo "SELECTED";} ?>>Mujer</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Nacimiento:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="c_fecha_nacimiento" id="c_fecha_nacimiento" value="<?php  if ($row_candidatos['c_fecha_nacimiento'] == "")
										{ echo "";} else { echo date('d/m/Y', strtotime($row_candidatos['c_fecha_nacimiento'])) ; }?>" required="required">
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Cedula IMSS:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_imss" id="a_imss" class="form-control" placeholder="Cedula IMSS a 11 posiciones" value="<?php echo $row_candidatos['a_imss']; ?>"  maxlength="11" minlength="11"  required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Número de Afore:</label>
										<div class="col-lg-9">
											<input type="text" name="a_afore" id="a_afore" class="form-control" placeholder="Afore" value="<?php echo $row_candidatos['a_afore']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Licencia de conducir y tipo:</label>
										<div class="col-lg-9">
											<input type="text" name="a_licencia" id="a_licencia" class="form-control" placeholder="Licencia de conducir y tipo" value="<?php echo $row_candidatos['a_licencia']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Número de Pasaporte:</label>
										<div class="col-lg-9">
											<input type="text" name="a_pasaporte" id="a_pasaporte" class="form-control" placeholder="Pasaporte" value="<?php echo $row_candidatos['a_pasaporte']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Número de Cartilla Militar:</label>
										<div class="col-lg-9">
											<input type="text" name="a_cartilla" id="a_cartilla" class="form-control" placeholder="Cartilla Militar" value="<?php echo $row_candidatos['a_cartilla']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Número de Cédula Profesional:</label>
										<div class="col-lg-9">
											<input type="text" name="a_cedula_profesional" id="a_cedula_profesional" class="form-control" placeholder="Cédula Profesional" value="<?php echo $row_candidatos['a_cedula_profesional']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nacionalidad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDnacionalidad" id="IDnacionalidad" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['IDnacionalidad']))) {echo "SELECTED";} ?>>Mexicana</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['IDnacionalidad']))) {echo "SELECTED";} ?>>Extranjera</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->
                         
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estado Civil:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="a_estado_civil" id="a_estado_civil" class="form-control" required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['a_estado_civil']))) {echo "SELECTED";} ?>>Soltero</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['a_estado_civil']))) {echo "SELECTED";} ?>>Casado</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Pretension Económica:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="pretension" id="pretension" class="form-control" placeholder="Cuanto pretende ganar" value="<?php echo $row_candidatos['pretension']; ?>"  required="required">
										</div>
									</div>
									<!-- /basic text input -->


                          <div class="text-right">
                            <div>
                         <button type="submit"  name="submit" class="btn btn-primary">Actualizar</button>
                         <button type="button" onClick="window.location.href='candidatos.php'" class="btn btn-default btn-icon">Cancelar</button>
                            </div>
                          </div>

                      <input type="hidden" name="MM_update" value="form1">
                      <input type="hidden" name="IDusuario" value="<?php echo $row_candidatos['IDusuario']; ?>">
                       </fieldset>
                      </form>



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