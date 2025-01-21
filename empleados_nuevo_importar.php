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

if (isset($_GET["IDempleado_temp"])) {
$IDempleado_temp = $_GET['IDempleado_temp'];
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT * FROM con_empleados_temp WHERE IDempleado_temp = '$IDempleado_temp'";
mysql_query("SET NAMES 'utf8'");
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);
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

$updateSQL = sprintf("UPDATE con_empleados_temp SET IDempleado=%s, a_paterno=%s, a_materno=%s, a_nombre=%s, a_rfc=%s, a_correo=%s, a_curp=%s, a_sexo=%s, a_imss=%s, IDnacionalidad=%s, a_estado_civil=%s, a_cuenta_bancaria=%s, a_banco=%s, a_cuenta_bancaria_clabe=%s, fecha_alta=%s, c_fecha_nacimiento=%s, d_calle=%s, d_numero_calle=%s, d_colonia=%s, d_delegacion_municipio=%s, IDestado=%s, d_codigo_postal=%s,  estatus=%s, IDmatriz=%s, local_foraneo=%s, tipo_de_contrato=%s,  b_sueldo_diario_int=%s, b_sueldo_diario=%s, b_sueldo_mensual=%s, IDpuesto=%s, beneficiario_nombre=%s, beneficiario_direccion=%s, beneficiario_telefono=%s, beneficiario_parentesco=%s, IDcuenta=%s, IDsubcuenta=%s WHERE IDempleado_temp=%s",
                       GetSQLValueString($_POST['IDempleado'], "text"),
                       GetSQLValueString($_POST['a_paterno'], "text"),
                       GetSQLValueString($_POST['a_materno'], "text"),
                       GetSQLValueString($_POST['a_nombre'], "text"),
                       GetSQLValueString($_POST['a_rfc'], "text"),
                       GetSQLValueString($_POST['a_correo'], "text"),
                       GetSQLValueString($_POST['a_curp'], "text"),
                       GetSQLValueString($_POST['a_sexo'], "int"),
                       GetSQLValueString($_POST['a_imss'], "text"),
                       GetSQLValueString($_POST['IDnacionalidad'], "int"),
                       GetSQLValueString($_POST['a_estado_civil'], "int"),
                       GetSQLValueString($_POST['a_cuenta_bancaria'], "text"),
                       GetSQLValueString($_POST['a_banco'], "text"),
                       GetSQLValueString($_POST['a_cuenta_bancaria_clabe'], "text"),
                       GetSQLValueString( $fecha_alta, "text"),
                       GetSQLValueString( $c_fecha_nacimiento, "text"),
                       GetSQLValueString($_POST['d_calle'], "text"),
                       GetSQLValueString($_POST['d_numero_calle'], "text"),
                       GetSQLValueString($_POST['d_colonia'], "text"),
                       GetSQLValueString($_POST['d_delegacion_municipio'], "text"),
                       GetSQLValueString($_POST['IDestado'], "int"),
                       GetSQLValueString($_POST['d_codigo_postal'], "text"),
                       GetSQLValueString($_POST['estatus'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['local_foraneo'], "int"),
                       GetSQLValueString($_POST['tipo_de_contrato'], "int"),
                       GetSQLValueString($_POST['b_sueldo_diario_int'], "text"),
                       GetSQLValueString($_POST['b_sueldo_diario'], "text"),
                       GetSQLValueString($_POST['b_sueldo_mensual'], "text"),
                       GetSQLValueString($_POST['IDpuesto'], "text"),
                       GetSQLValueString($_POST['beneficiario_nombre'], "text"),
                       GetSQLValueString($_POST['beneficiario_direccion'], "text"),
                       GetSQLValueString($_POST['beneficiario_telefono'], "text"),
                       GetSQLValueString($_POST['beneficiario_parentesco'], "text"),
                       GetSQLValueString($_POST['IDcuenta'], "text"),
                       GetSQLValueString($_POST['IDsubcuenta'], "text"),
                       GetSQLValueString($_POST['IDempleado_temp'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "empleados_importar.php?info=5";
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
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];
$el_usuario = $row_usuario['IDusuario'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_estado = "SELECT * FROM con_estados";
mysql_query("SET NAMES 'utf8'");
$estado = mysql_query($query_estado, $vacantes) or die(mysql_error());
$row_estado = mysql_fetch_assoc($estado);
$totalRows_estado = mysql_num_rows($estado);

mysql_select_db($database_vacantes, $vacantes);
$query_empresas = "SELECT * FROM vac_matriz";
$empresas = mysql_query($query_empresas, $vacantes) or die(mysql_error());
$row_empresas = mysql_fetch_assoc($empresas);
$totalRows_empresas = mysql_num_rows($empresas);

mysql_select_db($database_vacantes, $vacantes);
$query_bancos = "SELECT * FROM con_bancos ORDER BY con_bancos.banco ASC";
$bancos = mysql_query($query_bancos, $vacantes) or die(mysql_error());
$row_bancos = mysql_fetch_assoc($bancos);
$totalRows_bancos = mysql_num_rows($bancos);

$query_subcuenta = "SELECT * FROM con_subcuentas";
$subcuenta = mysql_query($query_subcuenta, $vacantes) or die(mysql_error());
$row_subcuenta = mysql_fetch_assoc($subcuenta);

$query_cuenta = "SELECT * FROM con_cuentas";
$cuenta = mysql_query($query_cuenta, $vacantes) or die(mysql_error());
$row_cuenta = mysql_fetch_assoc($cuenta);

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
							<h5 class="panel-title"><?php if (@$_GET['IDempleado'] == "") { echo "Agregar Empleado"; } else { echo "Actualizar Empleado"; } ?></h5>
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
											<input type="text" name="IDempleado" id="IDempleado" class="form-control" placeholder="ID del Empleado" value="<?php echo htmlentities($row_contratos['IDempleado'], ENT_COMPAT, ''); ?>"  onKeyUp="this.value=this.value.toUpperCase()" required>
                                            <span class="help-block">AAAAA9999</span>
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
										<label class="control-label col-lg-3">Correo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="email" name="a_correo" id="a_correo" class="form-control" placeholder="correo" value="<?php echo htmlentities($row_contratos['a_correo'], ENT_COMPAT, ''); ?>"  required="required">
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
										<label class="control-label col-lg-3">CURP:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_curp" id="a_curp" class="form-control" placeholder="CURP" onKeyUp="this.value=this.value.toUpperCase()" value="<?php echo htmlentities($row_contratos['a_curp'], ENT_COMPAT, ''); ?>"  maxlength="18"  required="required">
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


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Banco:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="a_banco" id="a_banco" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_bancos['IDbanco']?>"<?php if (!(strcmp($row_bancos['IDbanco'], $row_contratos['a_banco']))) 
												  {echo "SELECTED";} ?>><?php echo $row_bancos['banco']?></option>
												  <?php
												 } while ($row_bancos = mysql_fetch_assoc($bancos));
												   $rows = mysql_num_rows($bancos);
												   if($rows > 0) {
												   mysql_data_seek($bancos, 0);
												   $row_bancos = mysql_fetch_assoc($bancos);
												 } ?>
											</select>
										</div>
									</div>


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Cuenta de Banco (Cuenta):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="a_cuenta_bancaria" id="a_cuenta_bancaria" class="form-control" placeholder="Cuenta de Banco 10 posiciones" value="<?php echo htmlentities($row_contratos['a_cuenta_bancaria'], ENT_COMPAT, ''); ?>"  maxlength="15">
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Cuenta de Banco (Clabe):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<input type="text" name="a_cuenta_bancaria_clabe" id="a_cuenta_bancaria_clabe" class="form-control" placeholder="Clabe bancaria a 20 posiciones" value="<?php echo htmlentities($row_contratos['a_cuenta_bancaria_clabe'], ENT_COMPAT, ''); ?>"  maxlength="20" >
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Diario:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<input type="text" name="b_sueldo_diario" id="b_sueldo_diario" class="form-control" placeholder="Sueldo Diario" value="<?php echo htmlentities($row_contratos['b_sueldo_diario'], ENT_COMPAT, ''); ?>"  maxlength="20" >
                                            <span class="help-block">000.00</span>
										</div>
									</div>
									<!-- /basic text input -->
                                    

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Diario Integrado:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<input type="text" name="b_sueldo_diario_int" id="b_sueldo_diario_int" class="form-control" placeholder="Sueldo Diario" value="<?php echo htmlentities($row_contratos['b_sueldo_diario_int'], ENT_COMPAT, ''); ?>"  maxlength="20" >
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Mensual:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										<input type="text" name="b_sueldo_mensual" id="b_sueldo_mensual" class="form-control" placeholder="Sueldo Diario" value="<?php echo htmlentities($row_contratos['b_sueldo_mensual'], ENT_COMPAT, ''); ?>"  maxlength="20" >
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="IDpuesto" id="IDpuesto" class="form-control" placeholder="Puesto" value="<?php echo htmlentities($row_contratos['IDpuesto'], ENT_COMPAT, ''); ?>" required>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo Contratación:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="tipo_de_contrato" id="tipo_de_contrato" class="form-control"  required="required">
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_contratos['tipo_de_contrato'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Determinado</option>
                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_contratos['tipo_de_contrato'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Indeterminado</option>
                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_contratos['tipo_de_contrato'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Determinado A (Proyecto)</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Local o Foráneo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="local_foraneo" id="local_foraneo" class="form-control"  required="required">
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_contratos['local_foraneo'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Local</option>
                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_contratos['local_foraneo'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Foráneo</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Calle:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_calle" id="d_calle" class="form-control" placeholder="Calle" value="<?php echo htmlentities($row_contratos['d_calle'], ENT_COMPAT, ''); ?>" required >
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
										<label class="control-label col-lg-3">Colónia:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_colonia" id="d_colonia" class="form-control" placeholder="Colonia" value="<?php echo htmlentities($row_contratos['d_colonia'], ENT_COMPAT, ''); ?>" required>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Alcaldía o Municipio:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_delegacion_municipio" id="d_delegacion_municipio" class="form-control" placeholder="Alcaldia o Municipio" value="<?php echo htmlentities($row_contratos['d_delegacion_municipio'], ENT_COMPAT, ''); ?>" required>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Estado:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDestado" id="IDestado" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_estado['IDestado']?>"<?php if (!(strcmp($row_estado['IDestado'], $row_contratos['IDestado']))) 
												  {echo "SELECTED";} ?>><?php echo $row_estado['estado']?></option>
												  <?php
												 } while ($row_estado = mysql_fetch_assoc($estado));
												   $rows = mysql_num_rows($estado);
												   if($rows > 0) {
												   mysql_data_seek($estado, 0);
												   $row_estado = mysql_fetch_assoc($estado);
												 } ?>
											</select>
										</div>
									</div>

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Código Postal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="d_codigo_postal" id="d_codigo_postal" class="form-control" placeholder="Codigo Postal" value="<?php echo htmlentities($row_contratos['d_codigo_postal'], ENT_COMPAT, ''); ?>"  maxlength="6" required>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    
                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Registro Patronal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_empresas['IDmatriz']?>"<?php if (!(strcmp($row_empresas['IDmatriz'], $row_contratos['IDmatriz']))) 
												  {echo "SELECTED";} ?>><?php echo $row_empresas['matriz']?></option>
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
										<label class="control-label col-lg-3">Cuenta:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDcuenta" id="IDcuenta" class="form-control" required="required">
													  <?php  do { ?>
												  <option value="<?php echo $row_cuenta['IDcuenta']?>"<?php if (!(strcmp($row_cuenta['IDcuenta'], $row_contratos['IDcuenta']))) {echo "SELECTED";} ?>><?php echo $row_cuenta['cuenta']?></option>
												  <?php
												 } while ($row_cuenta = mysql_fetch_assoc($cuenta));
												   $rows = mysql_num_rows($cuenta);
												   if($rows > 0) {
												   mysql_data_seek($cuenta, 0);
												   $row_cuenta = mysql_fetch_assoc($cuenta);
												 } ?>
											</select>
										</div>
									</div>


                                    <!-- Basic text input -->
                                    <div class="form-group">
                                    <label class="control-label col-lg-3">Subcuenta:<span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                    <select name="IDsubcuenta" id="IDsubcuenta" class="form-control" required="required">
                                         <option value="">Seleccione una opción</option> 
                                          <?php  do { ?>
                                          <option value="<?php echo $row_subcuenta['IDsubcuenta']?>"<?php if (!(strcmp($row_subcuenta['IDsubcuenta'], $row_contratos['IDsubcuenta']))) {echo "SELECTED";} ?>><?php echo $row_subcuenta['subcuenta']?></option>
                                          <?php
                                         } while ($row_subcuenta = mysql_fetch_assoc($subcuenta));
                                           $rows = mysql_num_rows($subcuenta);
                                           if($rows > 0) {
                                           mysql_data_seek($subcuenta, 0);
                                           $row_subcuenta = mysql_fetch_assoc($subcuenta);
                                         } ?>
                                    </select>
                                    </div>
                                    </div>
                                    <!-- /basic text input -->

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

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Beneficiario Nombre:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="beneficiario_nombre" id="beneficiario_nombre" class="form-control" placeholder="Nombre del Beneficiario" value="<?php echo htmlentities($row_contratos['beneficiario_nombre'], ENT_COMPAT, ''); ?>"   required>
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Beneficiario Dirección:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="beneficiario_direccion" id="beneficiario_direccion" class="form-control" placeholder="Dirección del Beneficiario" value="<?php echo htmlentities($row_contratos['beneficiario_direccion'], ENT_COMPAT, ''); ?>"   required>
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Beneficiario Teléfono:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="beneficiario_telefono" id="beneficiario_telefono" class="form-control" placeholder="Teléfono del Beneficiario" value="<?php echo htmlentities($row_contratos['beneficiario_telefono'], ENT_COMPAT, ''); ?>"   required>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Beneficiario Parentesco:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="beneficiario_parentesco" id="beneficiario_parentesco" class="form-control" required="required">
												<option value ="">No especificado</option> 
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Esposo(a), Concubino(a)</option>
                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Padre</option>
                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Madre</option>
                            <option value="4" <?php if (!(strcmp(4, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Hijo(a)</option>
                            <option value="5" <?php if (!(strcmp(5, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Abuelo(a)</option>
                            <option value="6" <?php if (!(strcmp(6, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Nieto(a)</option>
                            <option value="7" <?php if (!(strcmp(7, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Hermano(a)</option>
                            <option value="8" <?php if (!(strcmp(8, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Tio(a)</option>
                            <option value="9" <?php if (!(strcmp(9, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Sobirno(a)</option>
                            <option value="10" <?php if (!(strcmp(10, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Suegro(a)</option>
                            <option value="11" <?php if (!(strcmp(11, htmlentities($row_contratos['beneficiario_parentesco'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Otro (sin parentezco familiar)</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


                          <div class="text-right">
                            <div>
                         <button type="submit"  name="submit" class="btn btn-primary">Actualizar</button>
                         <button type="button" onClick="window.location.href='empleados_importar.php'" class="btn btn-default btn-icon">Cancelar</button>
                            </div>
                          </div>

                      <input type="hidden" name="MM_update" value="form1">
                      <input type="hidden" name="IDempleado_temp" value="<?php echo $row_contratos['IDempleado_temp']; ?>">
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
