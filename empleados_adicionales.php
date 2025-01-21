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
$fecha = date("dmY"); // la fecha actual



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if (isset($_GET["IDempleado"])) {
$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT * FROM con_empleados WHERE IDempleado = '$IDempleado'";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
$foto_anterior = $row_contratos['file'];
}

if (isset($_GET["IDempleado"])) {
$_SESSION['IDempleado'] = $_GET['IDempleado'];
}else{
$_SESSION['IDempleado'] = 0;
}
$Elempleado = $_SESSION['IDempleado'];

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

// agregar FOTO
$formatos_permitidos =  array('jpeg', 'png', 'jpg');
$IDempleado_carpeta = 'CRED/'.$Elempleado;
$name=$_FILES['file']['name'];
$size=$_FILES['file']['size'];
$type=$_FILES['file']['type'];
$temp=$_FILES['file']['tmp_name'];
$extension = pathinfo($name, PATHINFO_EXTENSION);
$name_new=$Elempleado.$fecha.'.'.$extension;
$targetPath = 'CRED/'.$Elempleado.'/'.$name_new;


// si se mandó archivo
if ($name != '') {	
	
if(!in_array($extension, $formatos_permitidos) ) { echo "error archivos"; 
header("Location: empleados_adicionales.php?IDempleado=$Elempleado&info=9"); 
}
	
if (!file_exists($IDempleado_carpeta)) {mkdir($IDempleado_carpeta, 0777, true);}


move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
}

if ($name != '') {$name_new = $name_new;} else {$name_new = $foto_anterior; }

$alergias = htmlentities($_POST['alergias'], ENT_COMPAT, '');
$enfermedad = htmlentities($_POST['enfermedad'], ENT_COMPAT, '');
$medicamento = htmlentities($_POST['medicamento'], ENT_COMPAT, '');
$escolaridad = htmlentities($_POST['escolaridad'], ENT_COMPAT, '');
	
	
$updateSQL = sprintf("UPDATE con_empleados SET file=%s, telefono_1=%s, b_sueldo_mensual_extra=%s, unidad_medica=%s, tiene_infonavit=%s, telefono_2=%s, correo_2=%s, licencia_manejo_tipo=%s,  licencia_manejo_vigencia=%s, sangre=%s, alergias=%s, enfermedad=%s, medicamento=%s, escolaridad=%s WHERE IDempleado=%s",
                       GetSQLValueString($name_new, "text"),
                       GetSQLValueString($_POST['telefono_1'], "text"),
                       GetSQLValueString($_POST['b_sueldo_mensual_extra'], "text"),
                       GetSQLValueString($_POST['unidad_medica'], "text"),
                       GetSQLValueString($_POST['tiene_infonavit'], "text"),
                       GetSQLValueString($_POST['telefono_2'], "text"),
                       GetSQLValueString($_POST['correo_2'], "text"),
                       GetSQLValueString($_POST['licencia_manejo_tipo'], "text"),
                       GetSQLValueString($_POST['licencia_manejo_vigencia'], "text"),
                       GetSQLValueString($_POST['sangre'], "text"),
                       GetSQLValueString($alergias, "text"),
                       GetSQLValueString($enfermedad, "text"),
                       GetSQLValueString($medicamento, "text"),
                       GetSQLValueString($escolaridad, "int"),
                       GetSQLValueString($_POST['IDempleado'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "empleados_consulta.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

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

if (!isset($_SESSION['la_empresa'])) {  $_SESSION['la_empresa'] =  $IDmatriz; } 
$la_empresa = $_SESSION['la_empresa'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_escolaridad = "SELECT * FROM con_escolaridad ORDER BY con_escolaridad.escolaridad ASC";
$escolaridad = mysql_query($query_escolaridad, $vacantes) or die(mysql_error());
$row_escolaridad = mysql_fetch_assoc($escolaridad);
$totalRows_escolaridad = mysql_num_rows($escolaridad);
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
							Se ha actualizado correctamente el contrato.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 9))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							El archivo es incorrecto.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Actualizar Empleado. Datos Adicionales</h5>
						</div>

					<div class="panel-body">
					<p>Ingresa la información solicitada. Los campos marcados con <span class="text-danger">*</span> son obligatorios.</br>
                  <p>&nbsp;</p>
                    <div>
                    <div>

                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery" enctype="multipart/form-data">
                      
								<fieldset class="content-group">

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Escolaridad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="escolaridad" id="escolaridad" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_contratos['escolaridad']))) {echo "SELECTED";} ?>>Primaria</option>
                            <option value="2" <?php if (!(strcmp(2, $row_contratos['escolaridad']))) {echo "SELECTED";} ?>>Secundaria</option>
                            <option value="3" <?php if (!(strcmp(3, $row_contratos['escolaridad']))) {echo "SELECTED";} ?>>Preparatoria / Técnico</option>
                            <option value="4" <?php if (!(strcmp(4, $row_contratos['escolaridad']))) {echo "SELECTED";} ?>>Universidad</option>
                            <option value="5" <?php if (!(strcmp(5, $row_contratos['escolaridad']))) {echo "SELECTED";} ?>>Especialidad / Diplomado</option>
                            <option value="6" <?php if (!(strcmp(6, $row_contratos['escolaridad']))) {echo "SELECTED";} ?>>Maestría</option>
                            <option value="7" <?php if (!(strcmp(7, $row_contratos['escolaridad']))) {echo "SELECTED";} ?>>Doctorado</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

											<input type="hidden" name="file" id="file" class="file-styled" value="">



                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono (con Whatsapp):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="telefono_1" id="telefono_1" class="form-control" placeholder="Teléfono" value="<?php echo $row_contratos['telefono_1']; ?>"  required="required" >
                                            <span class="help-block">55-00-00-00-00</span>
										</div>
									</div>
									<!-- /basic text input -->
									
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Teléfono adicional:</label>
										<div class="col-lg-9">
											<input type="text" name="telefono_2" id="telefono_2" class="form-control" placeholder="Teléfono adicional" value="<?php echo $row_contratos['telefono_2']; ?>" >
                                            <span class="help-block">55-00-00-00-00</span>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Correo adicional:</label>
										<div class="col-lg-9">
											<input type="email" name="correo_2" id="correo_2" class="form-control" placeholder="Correo adicional" value="<?php echo $row_contratos['correo_2']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Unidad Médica Familiar:</label>
										<div class="col-lg-9">
											<input type="text" name="unidad_medica" id="unidad_medica" class="form-control" placeholder="Unidad Medica Familiar" value="<?php echo $row_contratos['unidad_medica']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Tiene Crédito del Infonativ?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="tiene_infonavit" id="tiene_infonavit" class="form-control">
												<option value ="">Seleccione una opción</option> 
                            <option value="0" <?php if (!(strcmp(0, $row_contratos['tiene_infonavit']))) {echo "SELECTED";} ?>>No tiene</option>
                            <option value="1" <?php if (!(strcmp(1, $row_contratos['tiene_infonavit']))) {echo "SELECTED";} ?>>Si tiene</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Sangre:</label>
										<div class="col-lg-9">
											<input type="text" name="sangre" id="sangre" class="form-control" placeholder="Tipo de sangre" value="<?php echo $row_contratos['sangre']; ?>"  >
										</div>
									</div>
									<!-- /basic text input -->


                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Enfermedad:</label>
										<div class="col-lg-9">
											<input type="text" name="enfermedad" id="enfermedad" class="form-control" placeholder="Enfermedad que padece" value="<?php echo $row_contratos['enfermedad']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->
                      
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Alergias:</label>
										<div class="col-lg-9">
											<input type="text" name="alergias" id="alergias" class="form-control" placeholder="Alergias que padece" value="<?php echo $row_contratos['alergias']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Medicamentos:</label>
										<div class="col-lg-9">
											<input type="text" name="medicamento" id="medicamento" class="form-control" placeholder="Medicamentos" value="<?php echo $row_contratos['medicamento']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tallas de Ropa y Zapatos:</label>
										<div class="col-lg-9">
											<input type="text" name="b_sueldo_mensual_extra" id="b_sueldo_mensual_extra" class="form-control" placeholder="Tallas de Ropa y Zapatos" value="<?php echo $row_contratos['b_sueldo_mensual_extra']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Licencia de Conducir:</span></label>
										<div class="col-lg-9">
											<select name="licencia_manejo_tipo" id="licencia_manejo_tipo" class="form-control">
											<option value="">Seleccione una opción</option> 
											<option value="0" <?php if (!(strcmp(0, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>No aplica</option> 
											<option value="1" <?php if (!(strcmp(1, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Estatal A</option>
											<option value="2" <?php if (!(strcmp(2, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Estatal B</option>
											<option value="3" <?php if (!(strcmp(3, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Estatal C</option>
											<option value="4" <?php if (!(strcmp(4, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Estatal D</option>
											<option value="5" <?php if (!(strcmp(5, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Estatal E</option>
											<option value="6" <?php if (!(strcmp(6, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Federal A</option>
											<option value="7" <?php if (!(strcmp(7, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Federal B</option>
											<option value="8" <?php if (!(strcmp(8, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Federal C</option>
											<option value="9" <?php if (!(strcmp(9, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Federal D</option>
											<option value="10" <?php if (!(strcmp(10, $row_contratos['licencia_manejo_tipo']))) {echo "SELECTED";} ?>>Federal E</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

								<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Vigencia Licencia (captura fecha o "Permanente"):</span></label>
			                        <div class="col-lg-9">
                                    	<input type="text" class="form-control" name="licencia_manejo_vigencia" id="licencia_manejo_vigencia" value="<?php echo $row_contratos['licencia_manejo_vigencia']; ?>">
                                   </div>
                                  </div> 
								<!-- Fecha -->



									<div class="text-right">
                            <div>
                         <button type="submit"  name="submit" class="btn btn-primary">Actualizar</button>
                         <button type="button" onClick="window.location.href='empleados_consulta.php'" class="btn btn-default btn-icon">Cancelar / Regresar</button>
                            </div>
                          </div>

                      <input type="hidden" name="MM_update" value="form1">
                      <input type="hidden" name="IDempleado" value="<?php echo $row_contratos['IDempleado']; ?>">
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
