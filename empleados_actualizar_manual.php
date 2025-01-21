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
}

if (isset($_GET["IDempleado"])) {
$_SESSION['IDempleado'] = $_GET['IDempleado'];
}else{
$_SESSION['IDempleado'] = 0;
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$y1 = substr( $_POST['c_fecha_nacimiento'], 8, 2 );
$m1 = substr( $_POST['c_fecha_nacimiento'], 3, 2 );
$d1 = substr( $_POST['c_fecha_nacimiento'], 0, 2 );
if ($y1 < 10) {$c_fecha_nacimiento = "20".$y1."-".$m1."-".$d1;} else {$c_fecha_nacimiento = "19".$y1."-".$m1."-".$d1;}

$y2 = substr( $_POST['fecha_alta'], 8, 2 );
$m2 = substr( $_POST['fecha_alta'], 3, 2 );
$d2 = substr( $_POST['fecha_alta'], 0, 2 );
if ($y2 < 50) {$fecha_alta = "20".$y2."-".$m2."-".$d2;} else {$fecha_alta = "19".$y2."-".$m2."-".$d2;}

$d_colonia = htmlentities($_POST['d_colonia'], ENT_COMPAT, '');
$d_delegacion_municipio = htmlentities($_POST['d_delegacion_municipio'], ENT_COMPAT, '');
$d_estado = htmlentities($_POST['d_estado'], ENT_COMPAT, '');
$d_calle = htmlentities($_POST['d_calle'], ENT_COMPAT, '');

$updateSQL = sprintf("UPDATE con_empleados SET a_paterno=%s, a_materno=%s, a_nombre=%s, a_rfc=%s, a_correo=%s, a_curp=%s, a_sexo=%s, a_imss=%s, IDnacionalidad=%s, a_estado_civil=%s, fecha_alta=%s, c_fecha_nacimiento=%s, d_calle=%s, d_numero_calle=%s, d_colonia=%s, d_delegacion_municipio=%s, d_estado=%s, d_codigo_postal=%s, estatus=%s, IDmatriz=%s WHERE IDempleado=%s",
                       GetSQLValueString($_POST['a_paterno'], "text"),
                       GetSQLValueString($_POST['a_materno'], "text"),
                       GetSQLValueString($_POST['a_nombre'], "text"),
                       GetSQLValueString($_POST['a_rfc'], "text"),
                       GetSQLValueString($_POST['a_correo'], "text"),
                       GetSQLValueString($_POST['a_curp'], "text"),
                       GetSQLValueString($_POST['a_sexo'], "int"),
                       GetSQLValueString($_POST['a_imss'], "int"),
                       GetSQLValueString($_POST['IDnacionalidad'], "int"),
                       GetSQLValueString($_POST['a_estado_civil'], "int"),
                       GetSQLValueString($fecha_alta, "text"),
                       GetSQLValueString($c_fecha_nacimiento, "text"),
                       GetSQLValueString($d_calle, "text"),
                       GetSQLValueString($_POST['d_numero_calle'], "text"),
                       GetSQLValueString($d_colonia, "text"),
                       GetSQLValueString($d_delegacion_municipio, "text"),
                       GetSQLValueString($d_estado, "text"),
                       GetSQLValueString($_POST['d_codigo_postal'], "text"),
                       GetSQLValueString($_POST['estatus'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDempleado'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "empleados_actualizar.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
  $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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

if (!isset($_SESSION['la_empresa'])) {  $_SESSION['la_empresa'] =  $IDmatriz; } 
$la_empresa = $_SESSION['la_empresa'];

$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_empresas = "SELECT * FROM vac_matriz";
$empresas = mysql_query($query_empresas, $vacantes) or die(mysql_error());
$row_empresas = mysql_fetch_assoc($empresas);
$totalRows_empresas = mysql_num_rows($empresas);

$query_bancos = "SELECT * FROM con_bancos ORDER BY con_bancos.banco ASC";
$bancos = mysql_query($query_bancos, $vacantes) or die(mysql_error());
$row_bancos = mysql_fetch_assoc($bancos);
$totalRows_bancos = mysql_num_rows($bancos);

$query_estados = "SELECT * FROM con_estados"; 
$estados = mysql_query($query_estados, $vacantes) or die(mysql_error());
$row_estados = mysql_fetch_assoc($estados);
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

                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Actualizar Empleado</h5>
						</div>

					<div class="panel-body">
					<p>Ingresa la información solicitada. Los campos marcados con <span class="text-danger">*</span> son obligatorios.</br>
                  <p>&nbsp;</p>
                    <div>
                    <div>
                    
                                      
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
                      
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">IDEmpleado:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" disabled class="form-control" value="<?php echo htmlentities($row_contratos['IDempleado'], ENT_COMPAT, ''); ?>"  required>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Paterno:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_paterno" id="a_paterno" class="form-control" placeholder="Apellido Paterno" value="<?php echo htmlentities($row_contratos['a_paterno'], ENT_COMPAT, ''); ?>"  required>
										</div>
									</div>
									<!-- /basic text input -->
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Apellido Materno:</label>
										<div class="col-lg-9">
											<input type="text" name="a_materno" id="a_materno" class="form-control" placeholder="Apellido Materno" value="<?php echo htmlentities($row_contratos['a_materno'], ENT_COMPAT, ''); ?>" >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre(s):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_nombre" id="a_nombre" class="form-control" placeholder="Nombres" value="<?php echo htmlentities($row_contratos['a_nombre'], ENT_COMPAT, ''); ?>"  required>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">RFC:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_rfc" id="a_rfc" class="form-control" placeholder="RFC a 13 posiciones" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_contratos['a_rfc'], ENT_COMPAT, ''); ?>"  maxlength="13"  required="required">
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Correo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="email" name="a_correo" id="a_correo" class="form-control" placeholder="correo" value="<?php echo htmlentities($row_contratos['a_correo'], ENT_COMPAT, ''); ?>"  required="required">
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">CURP:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_curp" id="a_curp" class="form-control" placeholder="CURP" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_contratos['a_curp'], ENT_COMPAT, ''); ?>"  maxlength="20"  required="required">
										</div>
									</div>
									<!-- /basic text input -->
                      
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sexo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="a_sexo" id="a_sexo" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_contratos['a_sexo'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Hombre</option>
                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_contratos['a_sexo'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Mujer</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Alta:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_alta" id="fecha_alta" value="<?php  if ($row_contratos['fecha_alta'] == "")
										{ echo "";} else { echo date('d/m/Y', strtotime($row_contratos['fecha_alta'])) ; }?>" required="required">
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->



									<!-- Fecha -->
                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha de Nacimiento:<span class="text-danger">*</span></label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="c_fecha_nacimiento" id="c_fecha_nacimiento" value="<?php  if ($row_contratos['c_fecha_nacimiento'] == "")
										{ echo "";} else { echo date('d/m/Y', strtotime($row_contratos['c_fecha_nacimiento'])) ; }?>" required="required">
									</div>
                                   </div>
                                  </div> 
								<!-- Fecha -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Cedula IMSS:</label>
										<div class="col-lg-9">
											<input type="text" name="a_imss" id="a_imss" class="form-control" placeholder="Cedula IMSS a 11 posiciones" value="<?php echo htmlentities($row_contratos['a_imss'], ENT_COMPAT, ''); ?>"  maxlength="11"  required="required">
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nacionalidad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDnacionalidad" id="IDnacionalidad" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_contratos['IDnacionalidad'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Mexicana</option>
                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_contratos['IDnacionalidad'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Extranjera</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->
                         
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estado Civil:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="a_estado_civil" id="a_estado_civil" class="form-control" >
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_contratos['a_estado_civil'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Soltero</option>
                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_contratos['a_estado_civil'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Casado</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Calle:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_calle" id="d_calle" class="form-control" placeholder="Calle" value="<?php echo $row_contratos['d_calle']; ?>" required >
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Número:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_numero_calle" id="d_numero_calle" class="form-control" placeholder="Numero" value="<?php echo htmlentities($row_contratos['d_numero_calle'], ENT_COMPAT, ''); ?>" required>
										</div>
									</div>
									<!-- /basic text input -->



                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Código Postal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" id="d_codigo_postal" name="d_codigo_postal" class="form-control" placeholder="Codigo Postal" value="<?php echo $row_contratos['d_codigo_postal']; ?>"  maxlength="6" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">Colónia:<span class="text-danger">*</span></label>
                                        <div class="col-lg-9">
										  <input type="text" id="d_colonia" name="d_colonia" class="form-control" placeholder="Colónia" value="<?php echo $row_contratos['d_colonia']; ?>" required="required">
                                        </div>
                                    </div>
                                    <!-- /basic text input -->
                            
                                      <!-- Basic text input -->
                                    <div class="form-group">
                                        <label class="control-label col-lg-3">Alcaldía o Municipio:<span class="text-danger">*</span></label>
                                        <div class="col-lg-9">
                                          <input type="text" id="d_delegacion_municipio" name="d_delegacion_municipio" class="form-control" placeholder="Alcaldía o Municipio" value="<?php echo $row_contratos['d_delegacion_municipio']; ?>" required="required">
                                        </div>
                                    </div>
                                    <!-- /basic text input -->
                            
                                    <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estado:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="d_estado" id="d_estado" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_estados['estado']?>"<?php if (!(strcmp($row_estados['estado'], html_entity_decode($row_contratos['d_estado'])))) {echo "SELECTED";} ?>><?php echo $row_estados['estado']?></option>
												  <?php
												 } while ($row_estados = mysql_fetch_assoc($estados));
												   $rows = mysql_num_rows($estados);
												   if($rows > 0) {
												   mysql_data_seek($empresas, 0);
												   $row_estados = mysql_fetch_assoc($estados);
												 } ?>
											</select>
										</div>
									</div>

                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Registro Patronal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_empresas['IDmatriz']?>"<?php if (!(strcmp($row_empresas['IDmatriz'], $row_contratos['IDmatriz']))) {echo "SELECTED";} ?>><?php echo $row_empresas['matriz']?></option>
												  <?php
												 } while ($row_empresas = mysql_fetch_assoc($empresas));
												   $rows = mysql_num_rows($empresas);
												   if($rows > 0) {
												   mysql_data_seek($empresas, 0);
												   $row_empresas = mysql_fetch_assoc($empresas);
												 } ?>
											</select>
										</div>
									</div>


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estatus:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="estatus" id="estatus" class="form-control" required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_contratos['estatus'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Activo</option>
                            <option value="0" <?php if (!(strcmp(0, htmlentities($row_contratos['estatus'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Baja</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                          <div class="text-right">
                            <div>
                         <button type="submit"  name="submit" class="btn btn-primary">Actualizar</button>
                         <button type="button" onClick="window.location.href='empleados_consulta.php'" class="btn btn-default btn-icon">Cancelar</button>
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