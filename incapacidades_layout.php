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
$query_variables = "SELECT * FROM vac_variables";
$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);

$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];$el_usuario = $row_usuario['IDusuario'];
$IDmatrizes = $row_usuario['IDmatrizes'];
$matriz_actual = $row_usuario['IDmatriz'];

$mes_actual = date("m");
$fecha = date("Y-m-d"); // la fecha actual
$elanio = '2020';
//echo " Mes: " . $el_mes;
//echo " Matriz: " . $la_matriz;
//echo " Estatus: " . $el_estatus;
//echo " Area: " . $el_area;

mysql_select_db($database_vacantes, $vacantes);
$query_mes = "SELECT * FROM vac_meses";
$mes = mysql_query($query_mes, $vacantes) or die(mysql_error());
$row_mes = mysql_fetch_assoc($mes);
$totalRows_mes = mysql_num_rows($mes);

mysql_select_db($database_vacantes, $vacantes);
$query_semana = "SELECT * FROM vac_semanas";
$semana = mysql_query($query_semana, $vacantes) or die(mysql_error());
$row_semana = mysql_fetch_assoc($semana);
$totalRows_semana = mysql_num_rows($semana);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = '$matriz_actual'";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
$matriz = $row_matriz['matriz'];

mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM incapacidades_registros_patronales";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

//utiles
date_default_timezone_set('America/Mexico_City');
$ahora = date ( 'd/m/Y' , time()); 

// el mes
  switch ($mes_actual) {
    case 1:  $elmes = "Enero";      break;     
    case 2:  $elmes = "Febrero";    break;    
    case 3:  $elmes = "Marzo";      break;    
    case 4:  $elmes = "Abril";      break;    
    case 5:  $elmes = "Mayo";       break;    
    case 6:  $elmes = "Junio";      break;    
    case 7:  $elmes = "Julio";      break;    
    case 8:  $elmes = "Agosto";     break;    
    case 9:  $elmes = "Septiembre"; break;    
    case 10: $elmes = "Octubre";    break;    
    case 11: $elmes = "Noviembre";  break;    
    case 12: $elmes = "Diciembre";  break;   
      }
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
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>

	<script src="global_assets/js/demo_pages/form_validation.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect.js"></script>

	<script src="assets/js/app.js"></script>
	<!-- /theme JS files -->

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

				<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Layout de Accidentes de Trabajo</h5>
						</div>

						<div class="panel-body">
									<p class="content-group"><strong>Instrucciones:</strong></br>
                                    1. Utiliza el filtro para descargar el Layout.</br>
									</p><p>&nbsp;</p>



							<form class="form-horizontal" action="incapacidades_layout_reporte.php" method="POST">
								<fieldset class="content-group">
									<legend class="text-bold">Criterios de Descarga</legend>

									<div class="form-group">
										<label class="control-label col-lg-2">Estatus:</label>
										<div class="col-lg-10">
										<select class="form-control" name="IDestatus">
											<option value="1">En proceso</option>
											<option value="2">Cerrados</option>
											<option value="3">Borrado</option>
										</select>
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-2">Año:</label>
										<div class="col-lg-10">
										<select class="form-control" name="el_anio">
										<option value="2025">2025</option>
										<option value="2024">2024</option>
											<option value="2023">2023</option>
										</select>
										</div>
									</div>

									<div class="form-group">
										<label class="control-label col-lg-2">Matriz:</label>
										<div class="col-lg-10">
											<select class="multiselect" multiple="multiple" id="la_matriz[]" name="la_matriz[]" required="required">
											<?php do { ?>
                                               <option value="<?php echo $row_lmatriz['IDmatriz']?>"><?php echo $row_lmatriz['matriz']?></option>
                                               <?php
											  } while ($row_lmatriz = mysql_fetch_assoc($lmatriz));
											  $rows = mysql_num_rows($lmatriz);
											  if($rows > 0) {
												  mysql_data_seek($lmatriz, 0);
												  $row_lmatriz = mysql_fetch_assoc($lmatriz);
											  } ?> </select>
										</div>
									</div>


									<div class="form-group">
										<label class="control-label col-lg-2">Mes:</label> 
										<div class="col-lg-10">
											<select class="multiselect-full-featured" multiple="multiple" id="el_mes[]" name="el_mes[]" required="required">
											<option value="0"><span class="text text-bold">No aplica</span></option>
                                               <?php do {  ?>
                                               	<option value="<?php echo $row_mes['IDmes']?>"><?php echo $row_mes['mes']?></option>
                                               <?php
											  } while ($row_mes = mysql_fetch_assoc($mes));
											  $rows = mysql_num_rows($mes);
											  if($rows > 0) {
												  mysql_data_seek($mes, 0);
												  $row_mes = mysql_fetch_assoc($mes);
											  } ?></select>
										</div>
									</div>


									<div class="form-group">
										<label class="control-label col-lg-2">Semana:</label> 
										<div class="col-lg-10">
											<select class="multiselect-full-featured" multiple="multiple" id="la_semana[]" name="la_semana[]" required="required">
											<option value="0"><span class="text text-bold">No aplica</span></option>
                                               <?php do {  ?>
                                               	<option value="<?php echo $row_semana['IDsemana']?>"><?php echo $row_semana['semana']?></option>
                                               <?php
											  } while ($row_semana = mysql_fetch_assoc($semana));
											  $rows = mysql_num_rows($semana);
											  if($rows > 0) {
												  mysql_data_seek($semana, 0);
												  $row_semana = mysql_fetch_assoc($semana);
											  } ?></select>
										</div>
									</div>


									<div class="form-group">
										<label class="control-label col-lg-2">Tipo de Archivo:</label> 
										<div class="col-lg-10">
											<select class="form-control" id="IDtipo" name="IDtipo" required="required">
											<option value="1">Excel Accidentes</option>
											<option value="2">CSV Incapacidades</option>
											</select>
										</div>
									</div>

								</fieldset>


								<div class="text-right">
									<button type="submit" class="btn btn-primary">Descargar</button>
								</div>
							</form>
					</div>
					<!-- /form horizontal --> 

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