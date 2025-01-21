<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the tNG classes
require_once('includes/tng/tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level

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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
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
$mis_areas = $row_usuario['IDareas'];
$la_matriz = $row_usuario['IDmatriz'];
$IDmatrizes = $row_usuario['IDmatrizes'];

if(!isset($_SESSION['el_mes'])) 
{ $_SESSION['el_mes'] = date("m");}

$el_mes = $_SESSION['el_mes'];


if (isset($_GET["IDpuesto"])) {
$IDpuesto = $_GET["IDpuesto"];
mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT * FROM vac_puestos WHERE IDpuesto = '$IDpuesto'";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE vac_puestos SET IDaguinaldo=%s, valuacion=%s, clave_puesto=%s, IDarea_contratos=%s, IDaplica_SIND=%s, modal=%s, IDnivel_puesto=%s, sgmm=%s, denominacion=%s, IDarea=%s, visible=%s,  tipo=%s, IDaplica_PROD=%s, IDaplica_INC=%s, IDaplica_SED=%s, dias=%s, descrito=%s, estatus=%s WHERE IDpuesto=%s",
                      GetSQLValueString($_POST['IDaguinaldo'], "text"),
                      GetSQLValueString($_POST['valuacion'], "text"),
                      GetSQLValueString($_POST['clave_puesto'], "text"),
                       GetSQLValueString($_POST['IDarea_contratos'], "text"),
                       GetSQLValueString($_POST['IDaplica_SIND'], "text"),
                       GetSQLValueString($_POST['modal'], "text"),
                       GetSQLValueString($_POST['IDnivel_puesto'], "text"),
                       GetSQLValueString($_POST['sgmm'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['visible'], "int"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['IDaplica_PROD'], "int"),
                       GetSQLValueString($_POST['IDaplica_INC'], "int"),
                       GetSQLValueString($_POST['IDaplica_SED'], "int"),
                       GetSQLValueString($_POST['dias'], "int"),
                       GetSQLValueString($_POST['descrito'], "int"),
                       GetSQLValueString($_POST['estatus'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());

  $updateGoTo = "admin_puestos.php?info=2";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

else if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	
  $insertSQL = sprintf("INSERT INTO vac_puestos (IDaguinaldo, valuacion, clave_puesto, IDarea_contratos, IDaplica_SIND, modal, IDnivel_puesto, sgmm, denominacion, IDarea, visible, tipo, dias, descrito, IDaplica_INC, IDaplica_SED, IDaplica_PROD, estatus) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                      GetSQLValueString($_POST['IDaguinaldo'], "text"),
                      GetSQLValueString($_POST['valuacion'], "text"),
                      GetSQLValueString($_POST['clave_puesto'], "text"),
                       GetSQLValueString($_POST['IDarea_contratos'], "text"),
                       GetSQLValueString($_POST['IDaplica_SIND'], "text"),
                       GetSQLValueString($_POST['modal'], "text"),
                       GetSQLValueString($_POST['IDnivel_puesto'], "text"),
                       GetSQLValueString($_POST['sgmm'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['visible'], "int"),
                       GetSQLValueString($_POST['tipo'], "int"),
                       GetSQLValueString($_POST['dias'], "int"),
                       GetSQLValueString($_POST['descrito'], "int"),
                       GetSQLValueString($_POST['IDaplica_INC'], "int"),
                       GetSQLValueString($_POST['IDaplica_SED'], "int"),
                       GetSQLValueString($_POST['IDaplica_PROD'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "admin_puestos.php?info=1";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

// borrar alternativo
if ((isset($_GET['IDpuesto_borrar'])) && ($_GET['IDpuesto_borrar'] != "")) {
  
  $borrado = $_GET['IDpuesto_borrar'];
  $deleteSQL = "DELETE FROM vac_puestos WHERE IDpuesto ='$borrado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: admin_puestos.php?info=3");
}


mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$la_matriz = $row_matriz['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_matrizes = "SELECT * FROM vac_matriz WHERE IDmatriz IN ($IDmatrizes)";
$matrizes = mysql_query($query_matrizes, $vacantes) or die(mysql_error());
$row_matrizes = mysql_fetch_assoc($matrizes);
$totalRows_matrizes = mysql_num_rows($matrizes);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

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
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="assets/js/app.js"></script>
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
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>

	<script src="global_assets/js/demo_pages/login_validation.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="global_assets/js/demo_pages/tasks_grid.js"></script>
	<!-- /theme JS files -->

</head>

<body class="has-detached-right">	<?php require_once('assets/mainnav.php'); ?>
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



					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
							<h5 class="panel-title">Editar Puesto</h5>
                              <?php } else { ?>
							<h5 class="panel-title">Agregar Puesto</h5>
                                <?php }  ?>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                            
                  <?php  if (isset($_GET['IDpuesto'])) { ?>
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">IDPuesto:</label>
										<div class="col-lg-9">
						                  <p><strong><?php echo $row_puesto['IDpuesto']; ?> </strong></p>
										</div>
									</div>
									<!-- /basic text input -->
                  
                  <?php }  ?>


                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Clave Nomina:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
						          <input type="number" name="clave_puesto" id="clave_puesto" class="form-control" value="<?php echo htmlentities($row_puesto['clave_puesto'], ENT_COMPAT, ''); ?>" 
                        required="required">
                              <?php } else { ?>
						          <input type="number" name="clave_puesto" id="clave_puesto" class="form-control" value="" 
                        required="required">
                                <?php }  ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    


                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Denominación:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
						          <input type="text" name="denominacion" id="denominacion" class="form-control" value="<?php echo htmlentities($row_puesto['denominacion'], ENT_COMPAT, ''); ?>" required="required">
                              <?php } else { ?>
						          <input type="text" name="denominacion" id="denominacion" class="form-control" value="" required="required">
                                <?php }  ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea" id="IDarea" class="form-control" required="required">
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
												  <option value="<?php echo $row_area['IDarea']?>"<?php if (!(strcmp($row_area['IDarea'], $row_puesto['IDarea']))) 
												  {echo "SELECTED";} ?>><?php echo $row_area['area']?></option>
                              <?php } else { ?>
												  <option value="<?php echo $row_area['IDarea']?>"><?php echo $row_area['area']?></option>
                                <?php }  ?>
												  <?php
												 } while ($row_area = mysql_fetch_assoc($area));
												   $rows = mysql_num_rows($area);
												   if($rows > 0) {
												   mysql_data_seek($area, 0);
												   $row_area = mysql_fetch_assoc($area);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="tipo" id="tipo" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SUCURSAL</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>CORPORATIVO</option>
                                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_puesto['tipo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ESPECIALES</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">SUCURSAL</option>
                                           <option value="2">CORPORATIVO</option>
                                           <option value="3">ESPECIALES</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area para Contrato:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="IDarea_contratos" id="IDarea_contratos" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['IDarea_contratos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['IDarea_contratos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ALMACEN Y DISTRIBUCION</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['IDarea_contratos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>VENTAS</option>
                                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_puesto['IDarea_contratos'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>ADMINISTRATIVOS</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">ALMACEN Y DISTRIBUCION</option>
                                           <option value="2">VENTAS</option>
                                           <option value="3">ADMINISTRATIVOS</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Visible para Sucursales:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="visible" id="visible" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['visible'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['visible'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Si</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['visible'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>No</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">Si</option>
                                           <option value="2">No</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica SGMM:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="sgmm" id="sgmm" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['sgmm'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['sgmm'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Si</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['sgmm'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>No</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">Si</option>
                                           <option value="2">No</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica Incentivos:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 										<select name="IDaplica_INC" id="IDaplica_INC" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                           <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['IDaplica_INC'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                           <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['IDaplica_INC'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SI</option>
                                           <option value="0" <?php if (!(strcmp(0, htmlentities($row_puesto['IDaplica_INC'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NO</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">SI</option>
                                           <option value="0">NO</option>
                                <?php }  ?>
                                        </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica Desempeño:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 										<select name="IDaplica_SED" id="IDaplica_SED" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                           <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['IDaplica_SED'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                           <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['IDaplica_SED'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SI</option>
                                           <option value="0" <?php if (!(strcmp(0, htmlentities($row_puesto['IDaplica_SED'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NO</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">SI</option>
                                           <option value="0">NO</option>
                                <?php }  ?>
                                        </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica Productividad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 										<select name="IDaplica_PROD" id="IDaplica_PROD" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                           <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['IDaplica_PROD'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                           <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['IDaplica_PROD'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SI</option>
                                           <option value="0" <?php if (!(strcmp(0, htmlentities($row_puesto['IDaplica_PROD'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NO</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">SI</option>
                                           <option value="0">NO</option>
                                <?php }  ?>
                                        </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica Sindicato:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 										<select name="IDaplica_SIND" id="IDaplica_SIND" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                           <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['IDaplica_SIND'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                           <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['IDaplica_SIND'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>SI</option>
                                           <option value="0" <?php if (!(strcmp(0, htmlentities($row_puesto['IDaplica_SIND'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>NO</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="1">SI</option>
                                           <option value="0">NO</option>
                                <?php }  ?>
                                        </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto Descrito:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="descrito" id="descrito" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="0" <?php if (!(strcmp(0, htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Pendiente</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>En captura</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>En revisión RH</option>
                                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_puesto['descrito'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Terminado</option>
                              <?php } else { ?>
                                           <option value="">Sin Captura</option>
                                           <option value="0">Pendiente</option>
                                           <option value="1">Captuarado</option>
                                           <option value="2">Validado</option>
                                           <option value="3">Terminado</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->
                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Productividad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="modal" id="modal" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['modal'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="0" <?php if (!(strcmp(0, htmlentities($row_puesto['modal'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>No aplica</option>
                                            <option value="100" <?php if (!(strcmp(100, htmlentities($row_puesto['modal'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>KPIs</option>
                                            <option value="200" <?php if (!(strcmp(200, htmlentities($row_puesto['modal'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Cajas</option>
                              <?php } else { ?>
                                           <option value="">Sin Captura</option>
                                           <option value="0">No aplica</option>
                                           <option value="100">KPIs</option>
                                           <option value="200">Cajas</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Tipo de Aguinaldo:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="IDaguinaldo" id="IDaguinaldo" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['IDaguinaldo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['IDaguinaldo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Según Antiguedad </option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['IDaguinaldo'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>22 días Administrativos</option>
                              <?php } else { ?>
                                           <option value="">Sin Captura</option>
                                           <option value="1">Según Antiguedad Sindicalizado</option>
                                           <option value="2">22 días Administrativos</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->


                  <!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Nivel del Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="IDnivel_puesto" id="IDnivel_puesto" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="10" <?php if (!(strcmp(10, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Operativo y Ventas</option>
                                            <option value="9" <?php if (!(strcmp(9, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Auxiliar</option>
                                            <option value="8" <?php if (!(strcmp(8, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Analista</option>
                                            <option value="7" <?php if (!(strcmp(7, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Jefe B</option>
                                            <option value="6" <?php if (!(strcmp(6, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Jefe A</option>
                                            <option value="5" <?php if (!(strcmp(5, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Sub Gerente</option>
                                            <option value="4" <?php if (!(strcmp(4, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Gerente</option>
                                            <option value="3" <?php if (!(strcmp(3, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Gerente Corporativo</option>
                                            <option value="2" <?php if (!(strcmp(2, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Director</option>
                                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['IDnivel_puesto'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>VP</option>
                              <?php } else { ?>
                                            <option value="">Sin captura</option>
                                            <option value="10">Operativo y Ventas</option>
                                            <option value="9">Auxiliar</option>
                                            <option value="8">Analista</option>
                                            <option value="7">Jefe B</option>
                                            <option value="6">Jefe A</option>
                                            <option value="5">Sub Gerente</option>
                                            <option value="4">Gerente</option>
                                            <option value="3">Gerente Corporativo|</option>
                                            <option value="2">Director</option>
                                            <option value="1">VP</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->
									
									
									 <!-- Basic select -->
                   <div class="form-group">
										<label class="control-label col-lg-3">Valuación:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="valuacion" id="valuacion" class="form-control" required="required">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                                            <option value="11" <?php if (!(strcmp(11, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>11</option>
                                            <option value="12" <?php if (!(strcmp(12, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>12</option>
                                            <option value="13" <?php if (!(strcmp(13, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>13</option>
                                            <option value="14" <?php if (!(strcmp(14, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>14</option>
                                            <option value="15" <?php if (!(strcmp(15, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>15</option>
                                            <option value="16" <?php if (!(strcmp(16, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>16</option>
                                            <option value="17" <?php if (!(strcmp(17, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>17</option>
                                            <option value="18" <?php if (!(strcmp(18, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>18</option>
                                            <option value="19" <?php if (!(strcmp(19, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>19</option>
                                            <option value="20" <?php if (!(strcmp(20, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>20</option>
                                            <option value="21" <?php if (!(strcmp(21, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>21</option>
                                            <option value="22" <?php if (!(strcmp(22, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>22</option>
                                            <option value="23" <?php if (!(strcmp(23, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>23</option>
                                            <option value="24" <?php if (!(strcmp(24, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>24</option>
                                            <option value="25" <?php if (!(strcmp(25, htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>25</option>
                                            <option value="OP" <?php if (!(strcmp('OP', htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Operativo</option>
                                            <option value="VE" <?php if (!(strcmp('VE', htmlentities($row_puesto['valuacion'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Ventas</option>
                              <?php } else { ?>
                                           <option value="">Seleccione...</option>
                                           <option value="11">11</option>
                                           <option value="12">12</option>
                                           <option value="13">13</option>
                                           <option value="14">14</option>
                                           <option value="15">15</option>
                                           <option value="16">16</option>
                                           <option value="17">17</option>
                                           <option value="18">18</option>
                                           <option value="19">19</option>
                                           <option value="20">20</option>
                                           <option value="21">21</option>
                                           <option value="22">22</option>
                                           <option value="23">23</option>
                                           <option value="24">24</option>
                                           <option value="25">25</option>
                                           <option value="OP">Operativo</option>
                                           <option value="VE">Ventas</option>
                                <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									 <!-- Basic select -->
                   <div class="form-group">
										<label class="control-label col-lg-3">Estatus (visible):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select name="estatus" id="estatus" class="form-control" required="required">
                          <?php  if (isset($_GET['IDpuesto'])) { ?>
                            <option value="" <?php if (!(strcmp('', htmlentities($row_puesto['estatus'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Seleccione...</option>
                            <option value="1" <?php if (!(strcmp(1, htmlentities($row_puesto['estatus'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Si</option>
                            <option value="0" <?php if (!(strcmp(0, htmlentities($row_puesto['estatus'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>No</option>
                          <?php } else { ?>
                            <option value="">Seleccione...</option>
                            <option value="1">Si</option>
                            <option value="0">No</option>
                            <?php }  ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->
                                   

                                    <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Días de Cobertura:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
						            <input type="text" name="dias" id="dias" class="form-control" value="<?php echo htmlentities($row_puesto['dias'], ENT_COMPAT, ''); ?>" required="required">
                              <?php } else { ?>
						            <input type="text" name="dias" id="dias" class="form-control" value=""  required="required">
                                <?php }  ?>
										</div>
									</div>
									<!-- /basic text input -->

                                    
                                    
                              <?php  if (isset($_GET['IDpuesto'])) { ?>
                          <button type="submit"  name="KT_Update1" class="btn btn-primary">Actualizar</button>
                          <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                          <input type="hidden" name="MM_update" value="form1">
                          <input type="hidden" name="IDpuesto" value="<?php echo $row_puesto['IDpuesto']; ?>">
                              <?php } else { ?>
                          <input type="submit" name="KT_Insert1" class="btn btn-primary" id="KT_Insert1" value="Agregar" />
                          <input type="hidden" name="MM_insert" value="form1" />
                                <?php }  ?>
                    	    <button type="button" onClick="window.location.href='admin_puestos.php'" class="btn btn-default btn-icon">Cancelar</button>
                            </form>
                            <p>&nbsp;</p>



                  <!-- danger modal -->
					<div id="modal_theme_danger" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar el Puesto?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admin_puestos_edit.php?IDpuesto_borrar=<?php echo $row_puesto['IDpuesto']; ?>">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					
					</div>


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