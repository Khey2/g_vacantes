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


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}



$IDmatrizX = $_GET["IDmatriz"];
$IDpuesto = $_GET["IDpuesto"];
mysql_select_db($database_vacantes, $vacantes);
$query_autorizados = "SELECT prod_garantias.IDgarantia, prod_garantias.IDpuesto, prod_garantias.IDmatriz, prod_garantias.garantia, vac_puestos.denominacion, vac_matriz.matriz, vac_matriz.IDmatriz FROM vac_puestos LEFT JOIN prod_garantias ON vac_puestos.IDpuesto = prod_garantias.IDpuesto LEFT JOIN vac_matriz ON prod_garantias.IDmatriz = vac_matriz.IDmatriz WHERE prod_garantias.IDpuesto = $IDpuesto AND prod_garantias.IDmatriz = $IDmatrizX";
$autorizados = mysql_query($query_autorizados, $vacantes) or die(mysql_error());
$row_autorizados = mysql_fetch_assoc($autorizados);
$totalRows_autorizados = mysql_num_rows($autorizados);


if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
$garantia = $_POST["garantia"];
$asistencia = $_POST["asistencia"];
$monto_asistencia = $_POST["monto_asistencia"];
$updateSQL = "UPDATE prod_garantias SET garantia=$garantia, asistencia=$asistencia, monto_asistencia=$monto_asistencia WHERE IDmatriz = $IDmatrizX AND IDpuesto = $IDpuesto";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
header('Location: admin_productividad_garantias.php?info=2');
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
$garantia = $_POST["garantia"];	
$asistencia = $_POST["asistencia"];
$monto_asistencia = $_POST["monto_asistencia"];
$insertSQL = "INSERT INTO prod_garantias (IDmatriz, IDpuesto, garantia, asistencia, monto_asistencia) VALUES ($IDmatrizX, $IDpuesto, $garantia, $asistencia, $monto_asistencia)";
mysql_select_db($database_vacantes, $vacantes);
$Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());
header('Location: admin_productividad_garantias.php?info=1');
}

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


mysql_select_db($database_vacantes, $vacantes);
$query_matriz1 = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatrizX";
$matriz1 = mysql_query($query_matriz1, $vacantes) or die(mysql_error());
$row_matriz1 = mysql_fetch_assoc($matriz1);
$totalRows_matriz1 = mysql_num_rows($matriz1);
$la_matriz1 = $row_matriz1['matriz']; 

mysql_select_db($database_vacantes, $vacantes);
$query_puesto1 = "SELECT * FROM vac_puestos WHERE IDpuesto = $IDpuesto";
$puesto1 = mysql_query($query_puesto1, $vacantes) or die(mysql_error());
$row_puesto1 = mysql_fetch_assoc($puesto1);
$totalRows_puesto1 = mysql_num_rows($puesto1);
$el_puesto1 = $row_puesto1['denominacion']; 


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
							<h5 class="panel-title">Editar Garantia</h5>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="admin_productividad_garantias_edit.php?IDmatriz=<?php echo $IDmatrizX; ?>&IDpuesto=<?php echo $IDpuesto; ?>" class="form-horizontal form-validate-jquery">
                            
                                     <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Matriz:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
												<?php echo $la_matriz1; ?>
										</div>
									</div>
									<!-- /basic text input -->
                                    
									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<?php echo $el_puesto1; ?>
										</div>
									</div>
									<!-- /basic select -->


									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Garantía (%):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
												<?php  if ($row_autorizados['garantia'] != 0) { ?>
											<input type="number" name="garantia" id="garantia" class="form-control" value="<?php echo htmlentities($row_autorizados['garantia'], ENT_COMPAT, ''); ?>" required="required">
												  <?php } else { ?>
											<input type="number" name="garantia" id="garantia" class="form-control" value="" required="required" >
												<?php }  ?>									
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Aplica Asistencia:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
											<select name="asistencia" id="asistencia" class="form-control" required="required">
                                            	<option value="0" <?php if ($row_autorizados['garantia'] == 0) {echo "SELECTED";} ?> >NO</option> 
                                            	<option value="1" <?php if ($row_autorizados['garantia'] == 1) {echo "SELECTED";} ?>>SI</option> 
                                          </select>
										</div>
									</div>
									<!-- /basic select -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Monto Asistencia ($):<span class="text-danger">*</span></label>
										<div class="col-lg-9">
												<?php  if ($row_autorizados['monto_asistencia'] != 0) { ?>
											<input type="number" name="monto_asistencia" id="monto_asistencia" class="form-control" value="<?php echo htmlentities($row_autorizados['monto_asistencia'], ENT_COMPAT, ''); ?>" required="required">
												  <?php } else { ?>
											<input type="number" name="monto_asistencia" id="monto_asistencia" class="form-control" value="" required="required" >
												<?php }  ?>									
										</div>
									</div>
									<!-- /basic select -->

                                    
                              <?php  if ($row_autorizados['garantia'] != 0) { ?>
                         <input type="submit"  name="KT_Update1" class="btn btn-primary" id="KT_Update1"value="Actualizar" />
                         <input type="hidden" name="MM_update" value="form1">
                              <?php } else { ?>
                         <input type="submit" name="KT_Insert1" class="btn btn-primary" id="KT_Insert1" value="Agregar" />
                         <input type="hidden" name="MM_insert" value="form1" />
                                <?php }  ?>
                    	 <button type="button" onClick="window.location.href='admin_productividad_garantias.php'" class="btn btn-default btn-icon">Cancelar</button>
                            </form>
                            <p>&nbsp;</p>

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