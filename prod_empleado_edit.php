<?php require_once('Connections/vacantes.php'); ?>
<?php
//MX Widgets3 include
require_once('includes/wdg/WDG.php');


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

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDusuario = $row_usuario['IDusuario'];
$las_matrizes = $row_usuario['IDmatrizes'];



$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	
$captura = $_GET['IDempleado'];

  $updateSQL = sprintf("UPDATE prod_activos SET emp_paterno=%s, emp_materno=%s, emp_nombre=%s, rfc=%s, fecha_alta=%s, fecha_antiguedad=%s, fecha_nacimiento=%s, sueldo_mensual=%s, sueldo_diario=%s, sobre_sueldo=%s, sueldo_total=%s, descripcion_nomina=%s, IDpuesto=%s, IDmatriz=%s, IDsucursal=%s, IDarea=%s, IDusuario_c='$IDusuario' WHERE IDempleado='$captura'",
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['fecha_alta'], "date"),
                       GetSQLValueString($_POST['fecha_antiguedad'], "date"),
                       GetSQLValueString($_POST['fecha_nacimiento'], "date"),
                       GetSQLValueString($_POST['sueldo_mensual'], "text"),
                       GetSQLValueString($_POST['sueldo_diario'], "text"),
                       GetSQLValueString($_POST['sobre_sueldo'], "text"),
                       GetSQLValueString($_POST['sueldo_total'], "text"),
                       GetSQLValueString($_POST['descripcion_nomina'], "text"),
                       GetSQLValueString($_POST['IDpuesto1'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['IDusuario_c'], "int"),
                       GetSQLValueString($_POST['IDempleado'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "prod_empleado_detalle.php?info=2";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
		
$insertSQL = sprintf("INSERT INTO prod_activos (IDempleado, emp_paterno, emp_materno, emp_nombre, rfc, fecha_alta, fecha_antiguedad, fecha_nacimiento, sueldo_mensual, sueldo_diario, sobre_sueldo, sueldo_total, descripcion_nomina, IDpuesto, IDmatriz, IDsucursal, IDarea, IDusuario_c) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$IDusuario')",
                       GetSQLValueString($_POST['IDempleado'], "int"),
                       GetSQLValueString($_POST['emp_paterno'], "text"),
                       GetSQLValueString($_POST['emp_materno'], "text"),
                       GetSQLValueString($_POST['emp_nombre'], "text"),
                       GetSQLValueString($_POST['rfc'], "text"),
                       GetSQLValueString($_POST['fecha_alta'], "date"),
                       GetSQLValueString($_POST['fecha_antiguedad'], "date"),
                       GetSQLValueString($_POST['fecha_nacimiento'], "date"),
                       GetSQLValueString($_POST['sueldo_mensual'], "text"),
                       GetSQLValueString($_POST['sueldo_diario'], "text"),
                       GetSQLValueString($_POST['sobre_sueldo'], "text"),
                       GetSQLValueString($_POST['sueldo_total'], "text"),
                       GetSQLValueString($_POST['descripcion_nomina'], "text"),
                       GetSQLValueString($_POST['IDpuesto1'], "int"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['IDusuario_c'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $captura = mysql_insert_id();

  $insertGoTo = "prod_empleado_detalle.php?IDempleado=$captura";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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
$desfase = $row_variables['dias_desfase'];

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
mysql_query("SET NAMES 'utf8'");
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];
$IDusuario = $row_usuario['IDusuario'];
$las_matrizes = $row_usuario['IDmatrizes'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];
$nivel = $_SESSION['kt_login_level'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($las_matrizes)";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal WHERE IDmatriz IN ($las_matrizes)";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE tipo <= '$nivel'";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

if (isset($_GET['IDempleado'])) {
$el_empleado = $_GET['IDempleado']; 
$a = "WHERE IDempleado = '$el_empleado'";
} else {
$a = "";
}

mysql_select_db($database_vacantes, $vacantes);
$query_empleados = "SELECT vac_sucursal.sucursal, vac_matriz.matriz, vac_puestos.denominacion, prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.rfc, prod_activos.fecha_alta, prod_activos.fecha_antiguedad, prod_activos.fecha_nacimiento, prod_activos.sueldo_mensual, prod_activos.sueldo_total, prod_activos.sueldo_diario, prod_activos.sobre_sueldo, prod_activos.sueldo_total, prod_activos.descripcion_nomina, prod_activos.IDarea, vac_areas.area, prod_activos.IDpuesto, prod_activos.IDmatriz, prod_activos.IDsucursal FROM prod_activos LEFT JOIN vac_puestos ON prod_activos.IDpuesto = vac_puestos.IDpuesto LEFT JOIN vac_matriz ON prod_activos.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_sucursal ON vac_sucursal.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_areas ON vac_puestos.IDarea = vac_areas.IDarea " . $a . "";
$empleados = mysql_query($query_empleados, $vacantes) or die(mysql_error());
$row_empleados = mysql_fetch_assoc($empleados);
$totalRows_empleados = mysql_num_rows($empleados);
?>
<!DOCTYPE html>
<html lang="en" xmlns:wdg="http://ns.adobe.com/addt">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />
	<meta name="robots" content="noindex" />

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
	<script type="text/javascript" src="includes/common/js/sigslot_core.js"></script>
	<script src="includes/common/js/base.js" type="text/javascript"></script>
	<script src="includes/common/js/utility.js" type="text/javascript"></script>
	<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js"></script>
	<script type="text/javascript" src="includes/wdg/classes/MXWidgets.js.php"></script>
	<script type="text/javascript" src="includes/wdg/classes/JSRecordset.js"></script>
	<script type="text/javascript" src="includes/wdg/classes/DependentDropdown.js"></script>
	<?php

//begin JSRecordset
$jsObject_puesto = new WDG_JsRecordset("puesto");
echo $jsObject_puesto->getOutput();
//end JSRecordset
?>
</head>

<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>	<?php require_once('assets/mainnav.php'); ?>
<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/menu.php'); ?>


			<!-- Main content -->
			<div class="content-wrapper">		<?php require_once('assets/pheader.php'); ?>
<!-- Content area -->
				<div class="content">

					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title"><?php if (@$_GET['IDempleado'] == "") { echo "Agregar Empleado"; } else { echo "Actualizar Empleado"; } ?></h5>
						</div>

					<div class="panel-body">
					<p>Ingresa la información solicitada. Los campos marcados con <span class="text-danger">*</span> son obligatorios.</br>
  					Una vez agregado al Empelado, no se pueden editar.</br>
					Únicamente agrega Empelados, cuando sea obligatorio pagar  productividad garantizada en la primer semana de ingreso o que por algún error no se haya dado de alta en nómina.</br>
					El sistema registra el usuario que dio de alta al Empleado.</br>
					Se debe justificar la razón de la excepción.</br>
					Para asegurar la integridad de la información, los empelados agregados por el usuario deberán ser validados para que proceda su pago.</p>
                  </br>



                
                    <div>
                    <div>
                    
                    <?php if (@$_GET['IDempleado'] == "") {?>
                    
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">No. Empleado:</label>
								  <div class="col-lg-9">
						<input type="number" name="IDempleado" id="IDempleado" class="form-control" placeholder="Indica el numero de empelado." value="">
								  </div>
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Nombre Paterno:</label>
								  <div class="col-lg-9">
						<input type="text" name="emp_paterno" id="emp_paterno" class="form-control" onKeyUp="this.value=this.value.toUpperCase()"  placeholder="Indica el nombre del empelado." value="">
								  </div>
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Nombre Materno:</label>
								  <div class="col-lg-9">
						<input type="text" name="emp_materno" id="emp_materno" class="form-control"  onKeyUp="this.value=this.value.toUpperCase()" placeholder="Indica el nombre del empelado." value="">
								  </div>
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Nombre(s):</label>
								  <div class="col-lg-9">
						<input type="text" name="emp_nombre" id="emp_nombre" class="form-control"  onKeyUp="this.value=this.value.toUpperCase()" placeholder="Indica el nombre del empelado." value="">
								  </div>
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">RFC:</label>
								  <div class="col-lg-9">
						<input type="text" name="rfc" id="rfc" class="form-control"  onKeyUp="this.value=this.value.toUpperCase()"  placeholder="Indica el RFC del empelado." value="">
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Fecha -->
                              <div class="form-group">
								  <label class="control-label col-lg-3">Fecha Alta:<span class="text-danger">*</span></label>
		                        <div class="col-lg-9">
		                          <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                   	  <input type="text" class="form-control  daterange-single" name="fecha_alta" id="fecha_alta" value="" required="required">
								  </div>
                                </div>
                            </div> 
							  <!-- Fecha -->

                            <!-- Fecha -->
                              <div class="form-group">
								  <label class="control-label col-lg-3">Fecha Antiguedad:<span class="text-danger">*</span></label>
		                        <div class="col-lg-9">
		                          <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                   	  <input type="text" class="form-control  daterange-single" name="fecha_antiguedad" id="fecha_antiguedad" value="" required="required">
								  </div>
                                </div>
                            </div> 
							  <!-- Fecha -->

                            <!-- Fecha -->
                              <div class="form-group">
								  <label class="control-label col-lg-3">Fecha Nacimiento:<span class="text-danger">*</span></label>
		                        <div class="col-lg-9">
		                          <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                   	  <input type="text" class="form-control  daterange-single" name="fecha_nacimiento" id="fecha_nacimiento" value="" required="required">
								  </div>
                                </div>
                            </div> 
							  <!-- Fecha -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sueldo Mensual:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
						<input type="number" name="sueldo_mensual" id="sueldo_mensual" class="form-control" placeholder="Ingresa el sueldo."  required="required" value="">
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sueldo Diario:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
						<input type="number" name="sueldo_diario" id="sueldo_diario" class="form-control" placeholder="Ingresa el sueldo."  required="required" value="">
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sobre Sueldo:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
						<input type="number" name="sobre_sueldo" id="sobre_sueldo" class="form-control" placeholder="Ingresa el sueldo."  required="required" value="">
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sueldo Total:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
						<input type="number" name="sueldo_total" id="sueldo_total" class="form-control" placeholder="Ingresa el sueldo."  required="required" value="">
								  </div>
							  </div>
							  <!-- /basic text input -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Nómina:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									 <select name="descripcion_nomina" class="form-control" required="required" >
									   <option value="1" <?php if (!(strcmp(1, $row_empleados['descripcion_nomina']))) {echo "selected=\"selected\"";} ?>>VILLOSA</option>
									   <option value="2" <?php if (!(strcmp(2, $row_empleados['descripcion_nomina']))) {echo "selected=\"selected\"";} ?>>PEOPLE OF THE SUN</option>
									   <option value="3" <?php if (!(strcmp(3, $row_empleados['descripcion_nomina']))) {echo "selected=\"selected\"";} ?>>RIGVER</option>
									   <option value="4" <?php if (!(strcmp(4, $row_empleados['descripcion_nomina']))) {echo "selected=\"selected\"";} ?>>CORVI</option>
                      	  </select>
								  </div>
							  </div>
							  <!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Área:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									 <select name="IDarea" class="form-control" id="IDarea" required="required" >
                          <?php do {  ?>
                          <option value="<?php echo $row_area['IDarea']?>" <?php if (!(strcmp($row_area['IDarea'],
							htmlentities($row_empleados['IDarea'], ENT_COMPAT, '')))) {echo "SELECTED";} ?> ><?php echo $row_area['area']?> </option>
                          <?php } while ($row_area = mysql_fetch_assoc($area)); ?>
                      	  </select>
								  </div>
							  </div>
							  <!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto1" class="form-control" id="IDpuesto1" wdg:subtype="DependentDropdown" required="required" wdg:type="widget" wdg:recordset="puesto" wdg:displayfield="denominacion" wdg:valuefield="IDpuesto" wdg:fkey="IDarea" wdg:triggerobject="IDarea" wdg:selected="<?php echo $row_empleados['IDarea'] ?>">
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									 <select name="IDmatriz" class="form-control" id="IDmatriz" required="required" >
                          <?php do {  ?>
                          <option value="<?php echo $row_matriz['IDmatriz']?>" <?php if (!(strcmp($row_matriz['IDmatriz'],
							htmlentities($row_empleados['IDmatriz'], ENT_COMPAT, '')))) {echo "SELECTED";} ?> ><?php echo $row_matriz['matriz']?> </option>
                          <?php } while ($row_matriz = mysql_fetch_assoc($matriz)); ?>
                      	  </select>
								  </div>
							  </div>
							  <!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sucursal:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									 <select name="IDsucursal" class="form-control" id="IDsucursal" required="required" >
                          <?php do {  ?>
                          <option value="<?php echo $row_sucursal['IDsucursal']?>" <?php if (!(strcmp($row_sucursal['IDsucursal'],
							htmlentities($row_empleados['IDsucursal'], ENT_COMPAT, '')))) {echo "SELECTED";} ?> ><?php echo $row_sucursal['sucursal']?> </option>
                          <?php } while ($row_sucursal = mysql_fetch_assoc($sucursal)); ?>
                      	  </select>
								  </div>
							  </div>
							  <!-- /basic select -->
                           
                           <input type="submit" class="btn btn-primary" value="Agregar Empleado">
                        <input type="hidden" name="MM_insert" value="form1">
                      </form>
                      <p>&nbsp;</p>


					<?php } else { ?> 


                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">IDempleado:</label>
								  <div class="col-lg-9">
						<input type="number" name="IDempleado" id="IDempleado" class="form-control" placeholder="Indica el nombre del empelado." value="<?php echo htmlentities($row_empleados['IDempleado'], ENT_COMPAT, ''); ?>">
								  </div>
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Paterno:</label>
								  <div class="col-lg-9">
						<input type="text" name="emp_paterno" id="emp_paterno" class="form-control"  onKeyUp="this.value=this.value.toUpperCase()" placeholder="Indica el nombre del empelado." value="<?php echo htmlentities($row_empleados['emp_paterno'], ENT_COMPAT, ''); ?>">
								  </div>
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Materno:</label>
								  <div class="col-lg-9">
						<input type="text" name="emp_materno" id="emp_materno" class="form-control" onKeyUp="this.value=this.value.toUpperCase()"  placeholder="Indica el nombre del empelado." value="<?php echo htmlentities($row_empleados['emp_materno'], ENT_COMPAT, ''); ?>">
								  </div>
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Nombre (s):</label>
								  <div class="col-lg-9">
						<input type="text" name="emp_nombre" id="emp_nombre" class="form-control"  onKeyUp="this.value=this.value.toUpperCase()" placeholder="Indica el nombre del empelado." value="<?php echo htmlentities($row_empleados['emp_nombre'], ENT_COMPAT, ''); ?>">
								  </div>
							  </div>
							  <!-- /basic text input -->

                             <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">RFC:</label>
								  <div class="col-lg-9">
						<input type="text" name="rfc" id="rfc" class="form-control"  onKeyUp="this.value=this.value.toUpperCase()" placeholder="Indica el RFC del empelado." value="<?php echo htmlentities($row_empleados['rfc'], ENT_COMPAT, ''); ?>">
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Fecha -->
                              <div class="form-group">
								  <label class="control-label col-lg-3">Fecha Alta:<span class="text-danger">*</span></label>
		                        <div class="col-lg-9">
		                          <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                   	  <input type="text" class="form-control pickadate" name="fecha_alta" id="fecha_alta" 
                                      value="<?php echo $row_empleados['fecha_alta']; ?>" required="required">
								  </div>
                                </div>
                            </div> 
							  <!-- Fecha -->

                            <!-- Fecha -->
                              <div class="form-group">
								  <label class="control-label col-lg-3">Fecha de Antiguedad:<span class="text-danger">*</span></label>
		                        <div class="col-lg-9">
		                          <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                   	  <input type="text" class="form-control  pickadate" name="fecha_antiguedad" id="fecha_antiguedad" 
                                      value="<?php echo $row_empleados['fecha_antiguedad']; ?>" required="required">
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
                                   	  <input type="text" class="form-control  pickadate" name="fecha_nacimiento" id="fecha_nacimiento" 
                                      value="<?php echo $row_empleados['fecha_nacimiento']; ?>" required="required">
								  </div>
                                </div>
                            </div> 
							  <!-- Fecha -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sueldo Mensual:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
						<input type="number" name="sueldo_mensual" id="sueldo_mensual" class="form-control" placeholder="Ingresa el sueldo."  required="required" value="<?php echo htmlentities($row_empleados['sueldo_mensual'], ENT_COMPAT, ''); ?>">
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sueldo Diario:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
						<input type="number" name="sueldo_diario" id="sueldo_diario" class="form-control" placeholder="Ingresa el sueldo."  required="required" value="<?php echo htmlentities($row_empleados['sueldo_diario'], ENT_COMPAT, ''); ?>">
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sobre Sueldo:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
						<input type="number" name="sobre_sueldo" id="sobre_sueldo" class="form-control" placeholder="Ingresa el sueldo."  required="required" value="<?php echo htmlentities($row_empleados['sobre_sueldo'], ENT_COMPAT, ''); ?>">
								  </div>
							  </div>
							  <!-- /basic text input -->

                            <!-- Basic text input -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sueldo Total:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
						<input type="number" name="sueldo_total" id="sueldo_total" class="form-control" placeholder="Ingresa el sueldo."  required="required" value="<?php echo htmlentities($row_empleados['sueldo_total'], ENT_COMPAT, ''); ?>">
								  </div>
							  </div>
							  <!-- /basic text input -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Nómina:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									 <select name="descripcion_nomina" class="form-control" required="required" >
									   <option value="1" <?php if (!(strcmp(1, $row_empleados['descripcion_nomina']))) {echo "selected=\"selected\"";} ?>>VILLOSA</option>
									   <option value="2" <?php if (!(strcmp(2, $row_empleados['descripcion_nomina']))) {echo "selected=\"selected\"";} ?>>PEOPLE OF THE SUN</option>
									   <option value="3" <?php if (!(strcmp(3, $row_empleados['descripcion_nomina']))) {echo "selected=\"selected\"";} ?>>RIGVER</option>
									   <option value="4" <?php if (!(strcmp(4, $row_empleados['descripcion_nomina']))) {echo "selected=\"selected\"";} ?>>CORVI</option>
                      	  </select>
								  </div>
							  </div>
							  <!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Área:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									 <select name="IDarea" class="form-control" id="IDarea" required="required" >
                          <?php do {  ?>
                          <option value="<?php echo $row_area['IDarea']?>" <?php if (!(strcmp($row_area['IDarea'],
							htmlentities($row_empleados['IDarea'], ENT_COMPAT, '')))) {echo "SELECTED";} ?> ><?php echo $row_area['area']?> </option>
                          <?php } while ($row_area = mysql_fetch_assoc($area)); ?>
                      	  </select>
								  </div>
							  </div>
							  <!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto1" class="form-control" id="IDpuesto1" wdg:subtype="DependentDropdown" required="required" wdg:type="widget" wdg:recordset="puesto" wdg:displayfield="denominacion" wdg:valuefield="IDpuesto" wdg:fkey="IDarea" wdg:triggerobject="IDarea" wdg:selected="<?php echo $row_empleados['IDarea'] ?>">
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									 <select name="IDmatriz" class="form-control" id="IDmatriz" required="required" >
                          <?php do {  ?>
                          <option value="<?php echo $row_matriz['IDmatriz']?>" <?php if (!(strcmp($row_matriz['IDmatriz'],
							htmlentities($row_empleados['IDmatriz'], ENT_COMPAT, '')))) {echo "SELECTED";} ?> ><?php echo $row_matriz['matriz']?> </option>
                          <?php } while ($row_matriz = mysql_fetch_assoc($matriz)); ?>
                      	  </select>
								  </div>
							  </div>
							  <!-- /basic select -->

							  <!-- Basic select -->
							  <div class="form-group">
								  <label class="control-label col-lg-3">Sucursal:<span class="text-danger">*</span></label>
								  <div class="col-lg-9">
									 <select name="IDsucursal" class="form-control" id="IDsucursal" required="required" >
                          <?php do {  ?>
                          <option value="<?php echo $row_sucursal['IDsucursal']?>" <?php if (!(strcmp($row_sucursal['IDsucursal'],
							htmlentities($row_empleados['IDsucursal'], ENT_COMPAT, '')))) {echo "SELECTED";} ?> ><?php echo $row_sucursal['sucursal']?> </option>
                          <?php } while ($row_sucursal = mysql_fetch_assoc($sucursal)); ?>
                      	  </select>
								  </div>
							  </div>
							  <!-- /basic select -->
                           
                           
                        <input type="submit" class="btn btn-primary" value="Actualizar Empleado">
                		<input type="hidden" name="MM_update" value="form1">
                  		<input type="hidden" name="IDempleado" value="<?php echo $row_empleados['IDempleado']; ?>">
                  		<input type="hidden" name="IDusuario_c" value="<?php echo $IDusuario; ?>">
                      </form>
                      <p>&nbsp;</p>

                      
                  <?php } ?>    
                      
                      

                      <br />
                    </div>
                    <p>&nbsp;</p>
</div>
					</div>
					<!-- /Contenido -->




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
<?php
mysql_free_result($variables);

mysql_free_result($empleados);
?>
