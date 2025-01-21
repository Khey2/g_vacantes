<?php require_once('Connections/vacantes.php'); ?>
<?php
// Load the common classes
require_once('includes/common/KT_common.php');

// Load the tNG classes
require_once('includes/tng/f_tNG.inc.php');

// Make unified connection variable
$conn_vacantes = new KT_connection($vacantes, $database_vacantes);

//Start Restrict Access To Page
$restrict = new tNG_RestrictAccess($conn_vacantes, "");
//Grand Levels: Level
$restrict->addLevel("1");
$restrict->addLevel("2");
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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$fecha = date("Y-m-d"); 

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM prod_activos WHERE IDempleado = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$IDmatriz = $row_usuario['IDmatriz'];

if ($row_usuario['nivel_acceso'] == 1) { header("Location: f_procedimientos.php?info=6"); }


$IDsucursal = $row_usuario['IDsucursal'];
$IDarea = $row_usuario['IDarea'];
$IDpuesto = $row_usuario['IDpuesto'];
$el_usuario = $row_usuario['IDempleado'];

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$IDmatriz'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$mi_matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_periodos = "SELECT * FROM sed_clima_periodos WHERE IDmatriz = $IDmatriz AND estatus = 1";
$periodos = mysql_query($query_periodos, $vacantes) or die(mysql_error());
$row_periodos = mysql_fetch_assoc($periodos);
$totalRows_periodos = mysql_num_rows($periodos);
$IDperiodo = $row_periodos['IDperiodo'];

mysql_select_db($database_vacantes, $vacantes);
$query_elperiodo = "SELECT * FROM sed_clima_periodos WHERE IDperiodo = $IDperiodo";
$elperiodo = mysql_query($query_elperiodo, $vacantes) or die(mysql_error());
$row_elperiodo = mysql_fetch_assoc($elperiodo);
$totalRows_elperiodo = mysql_num_rows($elperiodo); 


if(isset($_GET['IDplan'])){
$IDplan = $_GET['IDplan'];
mysql_select_db($database_vacantes, $vacantes);
$query_plan = "SELECT * FROM sed_clima_planes_liderazgo WHERE IDplan = '$IDplan'";
mysql_query("SET NAMES 'utf8'");
$plan = mysql_query($query_plan, $vacantes) or die(mysql_error());
$row_plan= mysql_fetch_assoc($plan);
$totalRows_plan = mysql_num_rows($plan);
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

// actualizar
if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$fecha_compromiso = date('Y-m-d', strtotime($_POST['fecha_compromiso']));
$avance = $_POST['avance'];
$IDsubdimension = $_POST['IDsubdimension'];

$updateSQL = "UPDATE sed_clima_planes_liderazgo SET  avance = '$avance' WHERE IDplan = '$IDplan'"; 
  mysql_select_db($database_vacantes, $vacantes);
  $Result1 = mysql_query($updateSQL, $vacantes) or die(mysql_error());
  header("Location: f_clima_plan.php?info=2");
}

?>
<!DOCTYPE html>
<html lang="en">
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
	<script src="global_assets/js/plugins/editors/wysihtml5/wysihtml5.min.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/toolbar.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/parsers.js"></script>
	<script src="global_assets/js/plugins/editors/wysihtml5/locales/bootstrap-wysihtml5.ua-UA.js"></script>
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/editor_wysihtml5.js"></script>
	<script src="global_assets/js/demo_pages/picker_date.js"></script>
</head>
<body <?php if (isset($_COOKIE["lmenu"])) { echo 'class="sidebar-xs"';}?>>

	<?php require_once('assets/f_mainnav.php'); ?>

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

		<?php require_once('assets/f_menu.php'); ?>

			<!-- Main content -->
			<div class="content-wrapper">	
            
            <?php require_once('assets/f_pheader.php'); ?>


        <!-- Page container -->
        <div class="page-container">

		<!-- Page content -->
		<div class="page-content">


			<!-- Main content -->
			<div class="content-wrapper">

			<!-- Content area -->
			  <div class="content">
              
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Plan de Acción - Liderazgo</h5>
						</div>

					<div class="panel-body">
							<p>Capture los datos solicitados en el reporte de avance.</p>



						 <form method="post" id="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
								<fieldset class="content-group">


									<div class="form-group">
										<label class="control-label col-lg-3">Subdimensión:</label>
										<div class="col-lg-9">
											<select name="IDsubdimension" class="form-control" id="IDsubdimension" required="required" disabled="disabled">
        <option value="1" <?php if (!(strcmp(1, htmlentities($row_plan['IDsubdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Comunicación</option>
        <option value="2" <?php if (!(strcmp(2, htmlentities($row_plan['IDsubdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Acción y Resultados</option>
        <option value="3" <?php if (!(strcmp(3, htmlentities($row_plan['IDsubdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Participación</option>
        <option value="4" <?php if (!(strcmp(4, htmlentities($row_plan['IDsubdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Liderazgo con Valores</option>
        <option value="5" <?php if (!(strcmp(5, htmlentities($row_plan['IDsubdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Relaciones Interpersonales</option>
        <option value="6" <?php if (!(strcmp(6, htmlentities($row_plan['IDsubdimension'], ENT_COMPAT, 'utf-8')))) {echo "SELECTED";} ?>>Motivación y Reconocimiento</option>
      </select>
										</div>
									</div>

                                    <div class="form-group">
										<label class="control-label col-lg-3">Fecha compromiso:</label>
			                        <div class="col-lg-9">
			                        <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                    	<input type="text" class="form-control  daterange-single" name="fecha_compromiso" id="fecha_compromiso"
                                         value="<?php echo date('d-m-Y', strtotime($row_plan['fecha_compromiso']));?>" disabled="disabled">
									</div>
                                   </div>
                                  </div> 

									<div class="form-group">
										<label class="control-label col-lg-3">Descripción:</label>
										<div class="col-lg-9">
                                          <textarea name="avance" id="avance" rows="5" class="wysihtml5 wysihtml5-min form-control"  placeholder="Describa el resultado obtenido."><?php echo $row_plan['avance']; ?></textarea>
										</div>
									</div>

					        <input type="hidden" name="IDempleado" value="<?php echo $el_usuario; ?>" />
					        <input type="hidden" name="anio" value="<?php echo $anio; ?>" />
					        <input type="hidden" name="fecha" value="<?php echo $fecha; ?>" />

      						  <div class="modal-footer">
					            <input type="submit" class="btn bg-primary-700" name="MM_update" value="Actualizar" />
							    <input type="hidden" name="MM_update" value="form1" />
                                <a class="btn bg-info" href="f_clima_plan.php">Cancelar</a>
                              </div>

								</fieldset>
                            </form>
                    
                    </div>

					<!-- /Contenido -->
                </div>
				  <!-- Footer -->
				  <div class="footer text-muted">
	&copy; <?php echo $anio; ?>. <a href="#"><?php echo $row_variables['nombre_sistema']; ?></a> V: 0.9.2 en <a href="<?php echo $row_variables['direccion_web']; ?>" target="_blank"><?php echo $row_variables['empresa']; ?></a>
			    </div>
				    <!-- /footer -->
                </div>
				<!-- /content area -->

		</div>
		<!-- /page content -->

</div>
	<!-- /page container -->


</body>
</html>