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
	
$fecha3 = date('Y-m-d', strtotime($_POST['fecha_termino']));	
$updateSQL = sprintf("UPDATE cv_activos SET equipo_maquinas=%s, equipo_computo=%s, IDescolaridad=%s, fecha_termino=%s, escuela=%s, estudios_actuales=%s, idioma=%s WHERE IDusuario=%s",
						GetSQLValueString(htmlentities($_POST['equipo_maquinas'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['equipo_computo'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['IDescolaridad'], ENT_COMPAT, ''), "int"),
						GetSQLValueString(htmlentities($fecha3, ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['escuela'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['estudios_actuales'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['idioma'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['IDusuario'], ENT_COMPAT, ''), "text"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "cv_c.php?info=1";
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
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
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
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
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
							<h5 class="panel-title">Datos Escolares</h5>
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
											<input type="text" name="IDempleado" id="a_correo" readonly="readonly" class="form-control"  value="<?php echo htmlentities($row_candidatos['a_correo'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->

							<h6 class="panel-title"><strong>Estudios</strong></h6>
		                    <p>&nbsp;</p>


									<div class="form-group">
										<label class="control-label col-lg-3">Escolaridad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDescolaridad" id="IDescolaridad" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['IDescolaridad']))) {echo "SELECTED";} ?>>Primaria</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['IDescolaridad']))) {echo "SELECTED";} ?>>Secundaria</option>
                            <option value="3" <?php if (!(strcmp(3, $row_candidatos['IDescolaridad']))) {echo "SELECTED";} ?>>Preparatoria / Técnico</option>
                            <option value="4" <?php if (!(strcmp(4, $row_candidatos['IDescolaridad']))) {echo "SELECTED";} ?>>Universidad</option>
                            <option value="5" <?php if (!(strcmp(5, $row_candidatos['IDescolaridad']))) {echo "SELECTED";} ?>>Especialidad / Diplomado</option>
                            <option value="6" <?php if (!(strcmp(6, $row_candidatos['IDescolaridad']))) {echo "SELECTED";} ?>>Maestría</option>
                            <option value="7" <?php if (!(strcmp(7, $row_candidatos['IDescolaridad']))) {echo "SELECTED";} ?>>Doctorado</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Término:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_termino" id="fecha_termino" value="<?php  if ($row_candidatos['fecha_termino'] == "")
										{ echo "";} else { echo date('d/m/Y', strtotime($row_candidatos['fecha_termino'])) ; }?>" required="required">
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->



                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Escuela y lugar del Plantel:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" id="escuela" name="escuela" onKeyUp="showUser(this.value)" class="form-control" placeholder="Escuela y lugar del Plantel" value="<?php echo $row_candidatos['escuela']; ?>" required>
										</div>
									</div>
									<!-- /basic text input -->

									<div id="txtHint"></div>
                                                                        

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Estudia actualmente?:</label>
										<div class="col-lg-9">
											<input type="text" name="estudios_actuales" id="estudios_actuales" class="form-control" placeholder="Estudios actuales" value="<?php echo $row_candidatos['estudios_actuales']; ?>"  >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Idiomas que domina (además del español):</label>
										<div class="col-lg-9">
											<input type="text" name="idioma" id="idioma" class="form-control" placeholder="Idiomas" value="<?php echo $row_candidatos['idioma']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									  <div class="form-group">
										<label class="control-label col-lg-3">Equipo o Máquinas que ha manejado:</label>
										<div class="col-lg-9">
											<input type="text" name="equipo_maquinas" id="equipo_maquinas" class="form-control" placeholder="Montacargas, de Procesamiento, etc." value="<?php echo $row_candidatos['equipo_maquinas']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									  <div class="form-group">
										<label class="control-label col-lg-3">Programas de Cómputación que domina:</label>
										<div class="col-lg-9">
											<input type="text" name="equipo_computo" id="equipo_computo" class="form-control" placeholder="Nombre y nivel de dominio." value="<?php echo $row_candidatos['equipo_computo']; ?>" >
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