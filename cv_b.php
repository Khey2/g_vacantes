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
$_SESSION['IDusuario_a'] = $_GET['IDusuario'];

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
	
$estado = $_POST['d_estado'];	
$query_estados = "SELECT * FROM cv_estados WHERE estado = '$estado'";
$estados = mysql_query($query_estados, $vacantes) or die(mysql_error());
$row_estados = mysql_fetch_assoc($estados);
$estado_ = 	$row_estados['estado_'];

$updateSQL = sprintf("UPDATE cv_activos SET  vive_solo_familia=%s, no_dependientes=%s, d_calle=%s, d_numero_calle=%s, d_colonia=%s, d_delegacion_municipio=%s, d_estado=%s,  IDestado=%s, d_codigo_postal=%s, tiempo_residencia=%s, telefono_1=%s, telefono_2=%s, medio_vacante=%s, puesto=%s, parentesco=%s, parentesco_nombres=%s, viajar=%s, turnos=%s, contacto_jefe_actual=%s, sindicato=%s, sindicato_cual=%s WHERE IDusuario=%s",
						GetSQLValueString(htmlentities($_POST['vive_solo_familia'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['no_dependientes'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['d_calle'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['d_numero_calle'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['d_colonia'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['d_delegacion_municipio'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['d_estado'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($estado_, ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['d_codigo_postal'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['tiempo_residencia'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['telefono_1'], ENT_COMPAT, ''), "date"),
						GetSQLValueString(htmlentities($_POST['telefono_2'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['medio_vacante'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['puesto'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['parentesco'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['parentesco_nombres'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['viajar'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['turnos'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['contacto_jefe_actual'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['sindicato'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['sindicato_cual'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['IDusuario'], ENT_COMPAT, ''), "text"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "cv_b.php?info=1";
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
    <script>
	function showUser(str) {
	  if (str == 0) {
	  } else {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		  if (this.readyState == 4 && this.status == 200) {
			document.getElementById("txtHint").innerHTML = this.responseText;
		  }
		};
		xmlhttp.open("GET","get_user.php?q="+str,true);
		xmlhttp.send();
	  }
	}
	</script>
	<!-- /theme JS files -->
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
							<h5 class="panel-title">Datos Generales</h5>
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
											<input type="text" name="IDempleado" id="a_correo" readonly="readonly" class="form-control"  value="<?php echo $row_candidatos['a_correo']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

							<h6 class="panel-title"><strong>Dirección</strong></h6>
		                    <p>&nbsp;</p>


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Calle:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_calle" id="d_calle" class="form-control" placeholder="Calle" value="<?php echo $row_candidatos['d_calle']; ?>" required >
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Número:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_numero_calle" id="d_numero_calle" class="form-control" placeholder="Numero" value="<?php echo $row_candidatos['d_numero_calle']; ?>" required>
										</div>
									</div>
									<!-- /basic text input -->



                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Código Postal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" id="d_codigo_postal" name="d_codigo_postal" onKeyUp="showUser(this.value)" class="form-control" placeholder="Codigo Postal" value="<?php echo $row_candidatos['d_codigo_postal']; ?>"  maxlength="6" required>
										</div>
									</div>
									<!-- /basic text input -->

									<div id="txtHint"></div>
                                                                        
                                      <!-- Basic text input -->
									  <div class="form-group">
										<label class="control-label col-lg-3">Tiempo de residencia:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" id="tiempo_residencia" name="tiempo_residencia" class="form-control" placeholder="Tiempo de residencia en años" value="<?php echo $row_candidatos['tiempo_residencia']; ?>"  required>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Vive solo o con familia?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="vive_solo_familia" id="vive_solo_familia" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
												<option value="1" <?php if (!(strcmp(1, $row_candidatos['vive_solo_familia']))) {echo "SELECTED";} ?>>Solo</option>
												<option value="2" <?php if (!(strcmp(2, $row_candidatos['vive_solo_familia']))) {echo "SELECTED";} ?>>Con Familia</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic text input -->
									  <div class="form-group">
										<label class="control-label col-lg-3">No. de dependientes económicos:</label>
										<div class="col-lg-9">
										  <input type="text" id="no_dependientes" name="no_dependientes" class="form-control" placeholder="Número de dependientes económicos" value="<?php echo $row_candidatos['no_dependientes']; ?>"  maxlength="6">
										</div>
									</div>
									<!-- /basic text input -->

									<h6 class="panel-title"><strong>Contacto</strong></h6>
		                    <p>&nbsp;</p>
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono:</label>
										<div class="col-lg-9">
											<input type="text" name="telefono_1" id="telefono_1" class="form-control" placeholder="Teléfono" value="<?php echo $row_candidatos['telefono_1']; ?>"  >
                                            <span class="help-block">55-00-00-00-00</span>
										</div>
									</div>
									<!-- /basic text input -->
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono celular:</label>
										<div class="col-lg-9">
											<input type="text" name="telefono_2" id="telefono_2" class="form-control" placeholder="Teléfono celular" value="<?php echo $row_candidatos['telefono_2']; ?>" >
                                            <span class="help-block">55-00-00-00-00</span>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    
                             <h6 class="panel-title"><strong>Otros datos</strong></h6>
		                    <p>&nbsp;</p>

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Qué puesto le interesa ocupar?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="puesto" id="puesto" class="form-control" placeholder="Puesto solicitado" value="<?php echo $row_candidatos['puesto']; ?>"  required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Cómo se enteró de la Vacante:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="medio_vacante" id="medio_vacante" class="form-control" placeholder="Medio de contacto" value="<?php echo $row_candidatos['medio_vacante']; ?>"  required="required">
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Tiene amistado o parentezco con alguno de nuestros empleados?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="parentesco" id="parentesco" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['parentesco']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['parentesco']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">En caso afirmativo, indique el nombre y parentezco:</label>
										<div class="col-lg-9">
											<input type="text" name="parentesco_nombres" id="parentesco_nombres" class="form-control" placeholder="Datos de patentezco" value="<?php echo $row_candidatos['parentesco_nombres']; ?>"  >
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Puede Viajar?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="viajar" id="viajar" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['viajar']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['viajar']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Esta dispuesto a tabajar en el turno que la Empresa le asigne, ya sea diurno o nocturno?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="turnos" id="turnos" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['turnos']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['turnos']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Podemos comunicarnos con sus actual o último jefe?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="contacto_jefe_actual" id="contacto_jefe_actual" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['contacto_jefe_actual']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['contacto_jefe_actual']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Pertenece o ha pertenecido a un Sindicato o Partido Político?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="sindicato" id="sindicato" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['sindicato']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['sindicato']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">En caso afirmativo, ¿Cuál y que puesto desempeñó en el mismo?:</label>
										<div class="col-lg-9">
											<input type="text" name="sindicato_cual" id="sindicato_cual" class="form-control" placeholder="Datos sindicales" value="<?php echo $row_candidatos['sindicato_cual']; ?>"  >
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