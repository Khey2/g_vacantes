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
$factor_integracion = $row_variables['factor_integracion'];

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


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$IDempleado = $_GET['IDempleado'];
$_SESSION['ElEmpleado'] = $IDempleado;
mysql_select_db($database_vacantes, $vacantes);
$query_contratos = "SELECT * FROM con_empleados WHERE IDempleado = '$IDempleado'";
$contratos = mysql_query($query_contratos, $vacantes) or die(mysql_error());
$row_contratos = mysql_fetch_assoc($contratos);
$totalRows_contratos = mysql_num_rows($contratos);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
if (strpos($_POST['b_sueldo_diario'], '.') === false) { $_POST['b_sueldo_diario'] = $_POST['b_sueldo_diario'].".00"; }

$IDempleadoJ = htmlentities($_POST['IDempleadoJ'], ENT_COMPAT, '');
$IDempleadoJP = htmlentities($_POST['IDempleadoJP'], ENT_COMPAT, '');

$updateSQL = sprintf("UPDATE con_empleados SET IDpuesto=%s, a_banco=%s, IDempleadoJ=%s, IDempleadoJP=%s, a_cuenta_bancaria_clabe=%s, a_cuenta_bancaria=%s, tipo_nomina=%s, b_sueldo_diario_int=%s, b_sueldo_diario=%s, b_sueldo_mensual=%s WHERE IDempleado=%s",
                       GetSQLValueString($_POST['IDpuesto'], "text"),
                       GetSQLValueString($_POST['a_banco'], "int"),
                       GetSQLValueString($IDempleadoJ, "text"),
                       GetSQLValueString($IDempleadoJP, "text"),
                       GetSQLValueString($_POST['a_cuenta_bancaria_clabe'], "text"),
                       GetSQLValueString($_POST['a_cuenta_bancaria'], "text"),
                       GetSQLValueString($_POST['tipo_nomina'], "int"),
                       GetSQLValueString($_POST['b_sueldo_diario_int'], "text"),
                       GetSQLValueString($_POST['b_sueldo_diario'], "text"),
                       GetSQLValueString($_POST['b_sueldo_mensual'], "text"),
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

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

$query_empresas = "SELECT * FROM vac_empresas";
$empresas = mysql_query($query_empresas, $vacantes) or die(mysql_error());
$row_empresas = mysql_fetch_assoc($empresas);

$query_bancos = "SELECT * FROM con_bancos ORDER BY con_bancos.banco ASC";
mysql_query("SET NAMES 'utf8'"); 
$bancos = mysql_query($query_bancos, $vacantes) or die(mysql_error());
$row_bancos = mysql_fetch_assoc($bancos);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT DISTINCT vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.IDpuesto FROM vac_puestos WHERE IDarea in (1,2,3,4,5,6,7,8,9,10,11)GROUP BY vac_puestos.IDpuesto ORDER BY vac_puestos.denominacion ASC";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);	


if (isset($_GET["IDempleado"])) {
$_SESSION['IDempleado'] = $_GET['IDempleado'];
}else{
$_SESSION['IDempleado'] = 0;
}

if(isset($_GET['q'])) {
	
$q = $_GET['q'];
$s_diario = $q;
$s_diario_int = number_format($q * $factor_integracion, 2, '.', '');
$s_mensual = number_format($q * 30, 2, '.', '');
} else {
$q = 0;
$s_diario = 0;
$s_diario_int = 0;
$s_mensual = 0;
}
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
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/components_modals.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>

<script>
function showHint(str) {
	  if (str.length == 0) {
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "get_sueldo.php?q=" + str, true);
        xmlhttp.send();
    }
}
</script>
<script>
function showHint2(str) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint2").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "empleados_get_banco.php?p=" + str, true);
        xmlhttp.send();
}
</script>
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?> onLoad="showHint2();">
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
							<h5 class="panel-title">Actualizar Empleado. Datos de Pago</h5>
						</div>

					<div class="panel-body">
					<p>
					<ul>
					<li>Ingresa la información solicitada. Los campos marcados con <span class="text-danger">*</span> son obligatorios.</li>
					<li>Captura el sueldo diario para que se calcule el mensual y el integrado.</li>
					<li>Selecciona la opción Efectivo para omitir Cuenta y Clabe de Banco.</li>
                  	<li>Si ya capturaste cuenta de Banco, no puedes cambiarlo a Efectivo. Solicita el cambio a Desarrollo Organizacional.</li>

					<p>&nbsp;</p>
                    <div>
                    <div>
                    
                                      
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
                                
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto" id="IDpuesto" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_puesto['IDpuesto']?>"<?php if (!(strcmp($row_puesto['IDpuesto'], $row_contratos['IDpuesto']))) 
												  {echo "SELECTED";} ?>><?php echo $row_puesto['denominacion']?></option>
												  <?php
												 } while ($row_puesto = mysql_fetch_assoc($puesto));
												   $rows = mysql_num_rows($puesto);
												   if($rows > 0) {
												   mysql_data_seek($puesto, 0);
												   $row_puesto = mysql_fetch_assoc($puesto);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->


                                  	<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Banco:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="a_banco" id="a_banco" class="form-control" required="required" onchange="showHint2(this.value)">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_bancos['IDbanco']?>"<?php if (!(strcmp($row_bancos['IDbanco'], $row_contratos['a_banco']))) {echo "SELECTED";} ?>><?php echo $row_bancos['banco']?></option>
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
									
									
                                    <span id="txtHint2">
									
									</span>


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Diario:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="number" step='0.01' min="0.01" max="500000.01" onKeyUp="showHint(this.value)" name="b_sueldo_diario" id="b_sueldo_diario" class="form-control" placeholder="Sueldo diario con decimales" value="<?php if($q != 0) { echo $s_diario; } else { echo $row_contratos['b_sueldo_diario']; }?>" required="required">
                                            <span class="help-block">000.00</span>
										</div>
									</div>
									<!-- /basic text input -->
									
                                    
                                    <span id="txtHint">

                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Diario Integrado:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="number" step='0.01' min="0.01" max="500000.01" name="b_sueldo_diario_int" id="b_sueldo_diario_int" class="form-control" placeholder="Sueldo diario integrado con decimales" value="<?php if($q != 0) { echo $s_diario_int; } else { echo $row_contratos['b_sueldo_diario_int']; }?>" required="required">
                                            <span class="help-block">000.00 | <?php echo "Factor de Integración: ". $factor_integracion; ?> </span>
										</div>
									</div>
									<!-- /basic text input -->


                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sueldo Mensual:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="number" step='0.01' min="0.01" max="500000.01" name="b_sueldo_mensual" id="b_sueldo_mensual" class="form-control" placeholder="Sueldo mensual con decimales" value="<?php if($q != 0) { echo $s_mensual; } else { echo $row_contratos['b_sueldo_mensual']; }?>" required="required">
                                            <span class="help-block">000.00</span>
										</div>
									</div>
									<!-- /basic text input -->
                                    

                                    </span>
									
									
									
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Periodo de Nómina:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="tipo_nomina" id="tipo_nomina" class="form-control"  required="required">
												<option value ="">Seleccione una opción</option> 
                            <option value="1" <?php if (!(strcmp(1, $row_contratos['tipo_nomina']))) {echo "SELECTED";} ?>>Semanal</option>
                            <option value="2" <?php if (!(strcmp(2, $row_contratos['tipo_nomina']))) {echo "SELECTED";} ?>>Quincenal</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->
									
									
									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre del Jefe Imendiato (para el formato de Requi):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="IDempleadoJ" id="IDempleadoJ" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" placeholder="Nombre del Jefe Imendiato" value="<?php echo $row_contratos['IDempleadoJ']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nombre del Puesto del Jefe Imendiato (para el formato de Requi):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" name="IDempleadoJP" id="IDempleadoJP" class="form-control" onKeyUp="this.value=this.value.toUpperCase()" placeholder="Nombre del Puesto del Jefe Imendiato" value="<?php echo $row_contratos['IDempleadoJP']; ?>" required="required">
										</div>
									</div>
									<!-- /basic text input -->



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