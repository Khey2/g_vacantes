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
	
$updateSQL = sprintf("UPDATE cv_activos SET no_tarjeta_credito=%s, banco_tarjeta_credito=%s, bienes=%s, bienes_valor=%s, renta=%s, renta_monto=%s, auto=%s, auto_modelo=%s, auto_valor=%s, afianzado=%s, afianzado_empleo=%s, afianzado_motivo=%s,  afianzado_compania=%s, afianzado_rechazo=%s, deudas=%s, deutas_tipo=%s, deudas_monto=%s, gastos=%s, ingresos_adicionales=%s, negocio=%s, negocio_nombre=%s, a_banco=%s, a_cuenta_bancaria_clabe=%s, a_cuenta_bancaria=%s WHERE IDusuario=%s",
						GetSQLValueString(htmlentities($_POST['no_tarjeta_credito'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['banco_tarjeta_credito'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['bienes'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['bienes_valor'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['renta'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['renta_monto'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['auto'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['auto_modelo'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['auto_valor'], ENT_COMPAT, ''), "int"),
						GetSQLValueString(htmlentities($_POST['afianzado'], ENT_COMPAT, ''), "int"),
						GetSQLValueString(htmlentities($_POST['afianzado_empleo'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['afianzado_motivo'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['afianzado_compania'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['afianzado_rechazo'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['deudas'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['deutas_tipo'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['deudas_monto'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['gastos'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['ingresos_adicionales'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['negocio'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['negocio_nombre'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['a_banco'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['a_cuenta_bancaria_clabe'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['a_cuenta_bancaria'], ENT_COMPAT, ''), "text"),
						GetSQLValueString(htmlentities($_POST['IDusuario'], ENT_COMPAT, ''), "text"));

mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "cv_e.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysql_select_db($database_vacantes, $vacantes);
$query_bancos = "SELECT * FROM con_bancos ORDER BY con_bancos.banco ASC";
$bancos = mysql_query($query_bancos, $vacantes) or die(mysql_error());
$row_bancos = mysql_fetch_assoc($bancos);
$totalRows_bancos = mysql_num_rows($bancos);

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
							<h5 class="panel-title">Datos Económicos</h5>
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
											<input type="text" name="a_correo" id="a_correo" readonly="readonly" class="form-control"  value="<?php echo htmlentities($row_candidatos['a_correo'], ENT_COMPAT, ''); ?>">
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Posee Inmuebles?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="bienes" id="bienes" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['bienes']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['bienes']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Cuál es el valor de sus Inmuebles? (si aplica):</label>
										<div class="col-lg-9">
											<input type="text" name="bienes_valor" id="bienes_valor" class="form-control" placeholder="Valor inmuebles" value="<?php echo $row_candidatos['bienes_valor']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Paga Renta?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="renta" id="renta" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['renta']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['renta']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Cuanto paga de renta? (si aplica):</label>
										<div class="col-lg-9">
											<input type="text" name="renta_monto" id="renta_monto" class="form-control" placeholder="Monto renta" value="<?php echo $row_candidatos['renta_monto']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Tiene automovil propio?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="auto" id="auto" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['auto']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['auto']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->
                                    

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Modelo (si aplica):</label>
										<div class="col-lg-9">
											<input type="text" name="auto_modelo" id="auto_modelo" class="form-control" placeholder="Modelo" value="<?php echo $row_candidatos['auto_modelo']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Valor estimado (si aplica)</label>
										<div class="col-lg-9">
											<input type="text" name="auto_valor" id="auto_valor" class="form-control" placeholder="Valor del automovil" value="<?php echo $row_candidatos['auto_valor']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Banco Debito o Ahorros:</label>
										<div class="col-lg-9">
											<select name="a_banco" id="a_banco" class="form-control">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_bancos['IDbanco']?>"<?php if (!(strcmp($row_bancos['IDbanco'], $row_candidatos['a_banco']))) {echo "SELECTED";} ?>><?php echo $row_bancos['banco']?></option>
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
										<label class="control-label col-lg-3">Cuenta de Banco  Debito o Ahorros:</label>
										<div class="col-lg-9">
											<input type="text" name="a_cuenta_bancaria" id="a_cuenta_bancaria" class="form-control" placeholder="Cuenta de Banco" value="<?php echo $row_candidatos['a_cuenta_bancaria']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Clabe Interbancaria:</label>
										<div class="col-lg-9">
											<input type="text" name="a_cuenta_bancaria_clabe" id="a_cuenta_bancaria_clabe" class="form-control" placeholder="Clabe Interbancaria"  value="<?php echo $row_candidatos['a_cuenta_bancaria_clabe']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->
                      
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Numero Tarjeta Crédito:</label>
										<div class="col-lg-9">
											<input type="text" name="no_tarjeta_credito" id="no_tarjeta_credito" class="form-control" placeholder="Cuenta de Banco" value="<?php echo $row_candidatos['no_tarjeta_credito']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Banco Tarjeta de Credito:</label>
										<div class="col-lg-9">
											<select name="banco_tarjeta_credito" id="banco_tarjeta_credito" class="form-control">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_bancos['IDbanco']?>"<?php if (!(strcmp($row_bancos['IDbanco'], $row_candidatos['banco_tarjeta_credito']))) {echo "SELECTED";} ?>><?php echo $row_bancos['banco']?></option>
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
                                    
                                    
                                    									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Ha sido afianzado?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="afianzado" id="afianzado" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['afianzado']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['afianzado']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿En que empleo has sido afianzado? (si aplica):</label>
										<div class="col-lg-9">
											<input type="text" name="afianzado_empleo" id="afianzado_empleo" class="form-control" placeholder="Empleo de la Fianza" value="<?php echo $row_candidatos['a_imss']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Por qué motivo fue afianzado? (si aplica):</label>
										<div class="col-lg-9">
											<input type="text" name="afianzado_motivo" id="afianzado_motivo" class="form-control" placeholder="Motivo de la Fianza" value="<?php echo $row_candidatos['afianzado_motivo']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre de la Compañía afianzadora (si aplica):</label>
										<div class="col-lg-9">
											<input type="text" name="afianzado_compania" id="afianzado_compania" class="form-control" placeholder="Nombre de la Compañía afianzadorao" value="<?php echo $row_candidatos['afianzado_compania']; ?>">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Ha sido rechazada la fianza? (si aplica): </label>
										<div class="col-lg-9">
											<input type="text" name="afianzado_rechazo" id="afianzado_rechazo" class="form-control" placeholder="Ha sido rechazada la fianza" value="<?php echo $row_candidatos['afianzado_rechazo']; ?>">
										</div>
									</div>
									<!-- /basic text input -->
                                    
                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Tiene deudas?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="deudas" id="deudas" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['deudas']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['deudas']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->
                                    

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿De que tipo? (si aplica):</label>
										<div class="col-lg-9">
											<input type="text" name="deutas_tipo" id="deutas_tipo" class="form-control" placeholder="Tipo de deuda" value="<?php echo $row_candidatos['deutas_tipo']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Por que monto? (si aplica):</label>
										<div class="col-lg-9">
											<input type="text" name="deudas_monto" id="deudas_monto" class="form-control" placeholder="Monto de la dueda" value="<?php echo $row_candidatos['deudas_monto']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿A cuanto ascienden sus gastos mensuales?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="gastos" id="gastos" class="form-control" placeholder="Gastos mensuales" value="<?php echo $row_candidatos['gastos']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Tiene otros ingresos adicionales?¿De que tipo?¿A cuanto ascienden?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<input type="text" name="ingresos_adicionales" id="ingresos_adicionales" class="form-control" placeholder="Ingresos adicionales" value="<?php echo $row_candidatos['ingresos_adicionales']; ?>" >
										</div>
									</div>
									<!-- /basic text input -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">¿Tiene negocio propio?:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="negocio" id="negocio" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_candidatos['negocio']))) {echo "SELECTED";} ?>>Si</option>
                            <option value="2" <?php if (!(strcmp(2, $row_candidatos['negocio']))) {echo "SELECTED";} ?>>No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Indique el nombre, la ubicación, ingreso mensual promedio y fechas de inicio y término (si aplica):</label>
										<div class="col-lg-9">
											<input type="text" name="negocio_nombre" id="negocio_nombre" class="form-control" placeholder="Ingresos adicionales" value="<?php echo $row_candidatos['negocio_nombre']; ?>" >
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