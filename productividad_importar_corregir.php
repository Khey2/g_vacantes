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

$currentPage = $_SERVER["PHP_SELF"];

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
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario); 
$mis_areas = $row_usuario['IDareas'];
$IDmatriz = $row_usuario['IDmatriz'];

$la_matriz = $row_usuario['IDmatriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

set_time_limit(0);

if (isset($_GET['IDempleado'])) { $IDempleado = $_GET['IDempleado']; } elseif (isset($_POST['IDempleado'])) { $IDempleado = $_POST['IDempleado']; } ;

mysql_select_db($database_vacantes, $vacantes);
$query_activos = "SELECT * FROM prod_activos WHERE IDempleado = '$IDempleado'";
$activos = mysql_query($query_activos, $vacantes) or die(mysql_error());
$row_activos = mysql_fetch_assoc($activos);
$totalRows_activos = mysql_num_rows($activos);

$llave = $row_activos['descripcion_nomina'] . $row_activos['descripcion_nivel'] . $row_activos['denominacion'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz  ORDER BY vac_matriz.matriz ASC";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);

mysql_select_db($database_vacantes, $vacantes);
$query_sucursal = "SELECT * FROM vac_sucursal  ORDER BY vac_sucursal.sucursal ASC";
$sucursal = mysql_query($query_sucursal, $vacantes) or die(mysql_error());
$row_sucursal = mysql_fetch_assoc($sucursal);
$totalRows_sucursal = mysql_num_rows($sucursal);

mysql_select_db($database_vacantes, $vacantes);
$query_puesto = "SELECT DISTINCT vac_puestos.IDpuesto, vac_puestos.denominacion, vac_puestos.IDarea, vac_puestos.tipo, vac_puestos.IDaplica_PROD, vac_puestos.IDaplica_INC, vac_puestos.modal, vac_puestos.dias FROM vac_puestos GROUP BY vac_puestos.denominacion ORDER BY vac_puestos.denominacion ASC";
$puesto = mysql_query($query_puesto, $vacantes) or die(mysql_error());
$row_puesto = mysql_fetch_assoc($puesto);
$totalRows_puesto = mysql_num_rows($puesto);

mysql_select_db($database_vacantes, $vacantes);
$query_llaves = "SELECT prod_llave.IDllave, vac_areas.area,  vac_matriz.matriz, prod_llave.denominacion, prod_llave.descripcion_nivel FROM prod_llave LEFT JOIN vac_matriz ON vac_matriz.IDmatriz = prod_llave.IDmatriz LEFT JOIN vac_areas ON vac_areas.IDarea = prod_llave.IDarea ORDER BY prod_llave.IDmatriz ASC, prod_llave.IDarea ASC";
mysql_query("SET NAMES 'utf8'");
$llaves = mysql_query($query_llaves, $vacantes) or die(mysql_error());
$row_llaves = mysql_fetch_assoc($llaves);
$totalRows_llaves = mysql_num_rows($llaves);

mysql_select_db($database_vacantes, $vacantes);
$query_area = "SELECT * FROM vac_areas ORDER BY vac_areas.area ASC";
$area = mysql_query($query_area, $vacantes) or die(mysql_error());
$row_area = mysql_fetch_assoc($area);
$totalRows_area = mysql_num_rows($area);

$descripcion_nomina = $row_activos['descripcion_nomina'];
$descripcion_nivel = $row_activos['descripcion_nivel'];
$denominacion = $row_activos['denominacion']; 
$llave = $row_activos['descripcion_nomina'] . $row_activos['descripcion_nivel'] . $row_activos['denominacion'];

//insertar
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$insertSQL = sprintf("INSERT INTO prod_llave (llave, denominacion, empresa, descripcion_nivel, IDmatriz, IDsucursal, IDarea, IDpuesto, IDllaveJ, IDaplica_PROD, IDaplica_INC, IDaplica_SED)
											   VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['llave'], "text"),
                       GetSQLValueString($_POST['denominacion'], "text"),
                       GetSQLValueString($_POST['descripcion_nomina'], "text"),
                       GetSQLValueString($_POST['descripcion_nivel'], "text"),
                       GetSQLValueString($_POST['IDmatriz'], "int"),
                       GetSQLValueString($_POST['IDsucursal'], "int"),
                       GetSQLValueString($_POST['IDarea'], "int"),
                       GetSQLValueString($_POST['IDpuesto'], "int"),
                       GetSQLValueString($_POST['IDllaveJ'], "int"),
                       GetSQLValueString($_POST['IDaplica_PROD'], "int"),
                       GetSQLValueString($_POST['IDaplica_INC'], "int"),
                       GetSQLValueString($_POST['IDaplica_SED'], "int"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
  
  $IDempleado = $_POST['IDempleado'];
  $IDmatriz = $_POST['IDmatriz'];
  $IDsucursal = $_POST['IDsucursal'];
  $IDarea = $_POST['IDarea'];
  $IDpuesto = $_POST['IDpuesto'];
  $IDllave = mysql_insert_id();
  $IDaplica_PROD = $_POST['IDaplica_PROD'];
  $IDaplica_INC = $_POST['IDaplica_INC'];
  $IDaplica_SED = $_POST['IDaplica_SED'];
  
  $actualizarSQL = "UPDATE prod_activos SET IDmatriz = '$IDmatriz', IDsucursal = '$IDsucursal', IDpuesto = '$IDpuesto', IDarea = '$IDarea', IDaplica_PROD = '$IDaplica_PROD', IDaplica_INC = '$IDaplica_INC', IDllave = '$IDllave', IDaplica_SED= '$IDaplica_SED', activo = 1  WHERE IDempleado = '$IDempleado'";
  $result = mysql_query($actualizarSQL, $vacantes) or die(mysql_error());
  
  
  header("Location: productividad_importar.php?info=1&info=4");
}

// borrar 
if (isset($_GET['borrar']) && $_GET['borrar'] == 1) {
  
  $IDempleado = $_GET['IDempleado'];
  $deleteSQL = "DELETE FROM prod_activos WHERE IDempleado = '$IDempleado'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location:  productividad_importar.php?info=1&info=5");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $row_variables['nombre_sistema']; ?></title>
	<meta name="robots" content="noindex" />

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
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
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>
	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
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
<?php if(isset($_GET['info'])) {$info = $_GET['info']; } else {$info = 0;} ?>

						<?php if($info == 1) {  ?>
						<!-- Basic alert -->
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se han importado correctamente los empleados.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 2) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-warning-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Problema al importar Empleados.
					    </div>
					    <!-- /basic alert -->

						<?php } else if($info == 3) { ?>
						<!-- Basic alert -->
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Archivo no permitido.
					    </div>
					    <!-- /basic alert -->

<?php } ?>

					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Importar Productividad V2.0</h6>
									<div class="heading-elements">
				                	</div>
								</div>
							
								<div class="panel-body">
									<p class="content-group">Completa la información solicitada:</p>

									<p class="content-group"><strong>Descripcion Nomina: </strong><?php echo $row_activos['descripcion_nomina']; ?></p>
									<p class="content-group"><strong>Descripcion Nivel: </strong><?php echo $row_activos['descripcion_nivel']; ?></p>
									<p class="content-group"><strong>Descripcion Denominacion: </strong><?php echo $row_activos['denominacion']; ?></p>


                             <form action="productividad_importar_corregir.php" method="post" name="importar" id="importar"  class="form-horizontal form-validate-jquery">
								<fieldset class="content-group">

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDmatriz" id="IDmatriz" class="bootstrap-select" data-live-search="true" data-width="100%" required="required">
                                            	<option value="">Seleccione una opción</option> 
													  <?php do {  ?>
													  <option value="<?php echo $row_matriz['IDmatriz']?>"><?php echo $row_matriz['matriz']?></option>
													  <?php
													 } while ($row_matriz = mysql_fetch_assoc($matriz));
													 $rows = mysql_num_rows($matriz);
													 if($rows > 0) {
													 mysql_data_seek($matriz, 0);
													 $row_matriz = mysql_fetch_assoc($matriz);
													 } ?>
                                          </select>
										</div>
									</div>
									<!-- /basic select -->



									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Sucursal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDsucursal" id="IDsucursal" class="bootstrap-select" data-live-search="true" data-width="100%" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_sucursal['IDsucursal']?>"><?php echo $row_sucursal['sucursal']?></option>
												  <?php
												 } while ($row_sucursal = mysql_fetch_assoc($sucursal));
												   $rows = mysql_num_rows($sucursal);
												   if($rows > 0) {
												   mysql_data_seek($sucursal, 0);
												   $row_sucursal = mysql_fetch_assoc($sucursal);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Area:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDarea" id="IDarea" class="bootstrap-select" data-live-search="true" data-width="100%" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_area['IDarea']?>"><?php echo $row_area['area']?></option>
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
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDpuesto" id="IDpuesto" class="bootstrap-select" data-live-search="true" data-width="100%" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_puesto['IDpuesto']?>"><?php echo $row_puesto['denominacion']?></option>
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
										<label class="control-label col-lg-3">Jefe Inmediato:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDllaveJ" id="IDllaveJ" class="bootstrap-select" data-live-search="true" data-width="100%" required="required" >
												<option value="">Seleccione una opción</option> 
												  <?php  do { ?>
												  <option value="<?php echo $row_llaves['IDllave']?>"><?php echo $row_llaves['matriz']."-" 
												  .$row_llaves['area']."-".$row_llaves['denominacion']."-".$row_llaves['descripcion_nivel'] ?></option>
												  <?php
												 } while ($row_llaves = mysql_fetch_assoc($llaves));
												   $rows = mysql_num_rows($llaves);
												   if($rows > 0) {
												   mysql_data_seek($puesto, 0);
												   $row_llaves = mysql_fetch_assoc($llaves);
												 } ?>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica Incentivos:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDaplica_INC" id="IDaplica_INC" class="form-control" required="required"  >
												<option value="">Seleccione una opción</option> 
												  <option value="0">Si</option>
												  <option value="1">No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica Productividad:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDaplica_PROD" id="IDaplica_PROD" class="form-control" required="required"  >
												<option value="">Seleccione una opción</option> 
												  <option value="0">Si</option>
												  <option value="1">No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica Desempeño Anual:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="IDaplica_SED" id="IDaplica_SED" class="form-control" required="required" >
												<option value="">Seleccione una opción</option> 
												  <option value="0">Si</option>
												  <option value="1">No</option>
											</select>
										</div>
									</div>
									<!-- /basic select -->

                              
                            <div>
										<input type="hidden" name="importar" id="importar">
          	                      		<input type="hidden" name="MM_insert" value="form1">
          	                      		<input type="hidden" name="IDempleado" value="<?php echo $IDempleado; ?>">
          	                      		<input type="hidden" name="descripcion_nomina" value="<?php echo $descripcion_nomina; ?>">
          	                      		<input type="hidden" name="descripcion_nivel" value="<?php echo $descripcion_nivel; ?>">
          	                      		<input type="hidden" name="denominacion" value="<?php echo $denominacion; ?>">
          	                      		<input type="hidden" name="llave" value="<?php echo $llave; ?>">
                        				<button type="submit" id="submit" name="import" class="btn btn-primary">Agregar Puesto</button>
                            </div>
                            
                           </fieldset>
                             </form>

    </div>
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