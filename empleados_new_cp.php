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

if(isset($_GET['cp'])) {$q = intval($_GET['cp']);} else {$q = 0;}


$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$insertSQL = sprintf("INSERT INTO con_cp (d_colonia, d_delegacion_municipio, d_estado, IDusuario, IDestado, nuevo, d_codigo_postal) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['d_colonia'], "text"),
                       GetSQLValueString($_POST['d_delegacion_municipio'], "text"),
                       GetSQLValueString($_POST['d_estado'], "text"),
                       GetSQLValueString($_POST['IDusuario'], "text"),
                       GetSQLValueString($_POST['IDestado'], "text"),
                       GetSQLValueString($_POST['nuevo'], "text"),
                       GetSQLValueString($_POST['d_codigo_postal'], "text"));

  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($insertSQL, $vacantes) or die(mysql_error());

  $insertGoTo = "empleados_nuevo.php?info=3";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$query_codpos = "SELECT IDestado, d_codigo_postal, d_estado, d_delegacion_municipio, d_colonia FROM con_cp WHERE d_codigo_postal = '$q' LIMIT 1";
mysql_query("SET NAMES 'utf8'");
$codpos = mysql_query($query_codpos, $vacantes) or die(mysql_error());
$row_codpos = mysql_fetch_assoc($codpos);
$totalRows_codpos = mysql_num_rows($codpos);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $la_matriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
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
                
                
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Agregar Código Postal</h5>
						</div>

					<div class="panel-body">
					<p>Ingresa la información solicitada para agregar la Colonia al catálogo.</p>
                  <p>&nbsp;</p>
                    <div>
                    <div>
                    
                                      
                      <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                      
								<fieldset class="content-group">
						
                                      <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Código Postal:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
										  <input type="text" id="d_codigo_postal" name="d_codigo_postal" readonly="readonly" class="form-control" value="<?php if(isset($_GET['cp'])) { echo $_GET['cp']; } ?>" >
										</div>
									</div>
									<!-- /basic text input -->
								
								  <!-- Basic text input -->
								<div class="form-group">
									<label class="control-label col-lg-3">Alcaldía o Municipio:<span class="text-danger">*</span></label>
									<div class="col-lg-9">
									  <input type="text" name="d_delegacion_municipio" id="d_delegacion_municipio" readonly="readonly"  class="form-control" value="<?php if($q != 0) { echo $row_codpos['d_delegacion_municipio'];} ?>">
									</div>
								</div>
								<!-- /basic text input -->

								<!-- Basic select -->
								<div class="form-group">
									<label class="control-label col-lg-3">Estado:<span class="text-danger">*</span></label>
									<div class="col-lg-9">
									 <input type="text" name="d_estado" id="d_estado" class="form-control" readonly="readonly" value="<?php if($q != 0) { echo $row_codpos['d_estado'];} ?>">
									</div>
								</div>

								<!-- Basic text input -->
								<div class="form-group">
									<label class="control-label col-lg-3">Colonia:<span class="text-danger">*</span></label>
									<div class="col-lg-9">
									  <input type="text" name="d_colonia" id="d_colonia" class="form-control" placeholder="Colonia" required="required">
									</div>
								</div>
								<!-- /basic text input -->


                          <div class="text-right">
                            <div>
								<button type="submit"  name="submit" class="btn btn-primary">Agregar</button>
								<input type="hidden" name="MM_update" value="form1">
								<input type="hidden" name="IDestado" id="IDestado" value="<?php if($q != 0) { echo $row_codpos['IDestado'];} ?>">
								<input type="hidden" name="nuevo" id="nuevo" value="1">
								<input type="hidden" name="IDusuario" id="IDusuario" value="<?php echo $el_usuario; ?>">

                            </div>
                          </div>

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