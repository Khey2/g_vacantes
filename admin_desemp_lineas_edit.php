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

$IDempleado = $_GET['IDempleado'];
mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT prod_activos.IDempleado, prod_activos.emp_paterno, prod_activos.emp_materno, prod_activos.emp_nombre, prod_activos.denominacion, prod_activos.IDpuesto, prod_activos.IDmatriz, boss.IDempleado AS IDempleadoJ, boss.emp_paterno AS emp_paternoJ, boss.emp_materno AS emp_maternoJ, boss.emp_nombre AS emp_nombreJ, boss.denominacion AS denominacionJ FROM prod_activos LEFT JOIN prod_activos AS boss ON prod_activos.IDempleadoJ = boss.IDempleado WHERE prod_activos.IDempleado = '$IDempleado'";
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_jefes = "SELECT * FROM prod_activos WHERE IDempleado <> '$IDempleado' OR manual IS NOT NULL AND IDmatriz = '$la_matriz' ORDER BY prod_activos.emp_nombre ASC";
mysql_query("SET NAMES 'utf8'");
$jefes = mysql_query($query_jefes, $vacantes) or die(mysql_error());
$row_jefes = mysql_fetch_assoc($jefes);
$totalRows_jefes = mysql_num_rows($jefes);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {

$IDempleadoJ = $_POST['IDempleadoJ'];
$query1 = "UPDATE prod_activos SET IDempleadoJ = '$IDempleadoJ' WHERE IDempleado = '$IDempleado'"; 
$result1 = mysql_query($query1) or die(mysql_error());  
  
mysql_select_db($database_vacantes, $vacantes);
$query_boss = "SELECT * FROM prod_activosj WHERE IDempleado = '$IDempleado'"; 
$boss = mysql_query($query_boss, $vacantes) or die(mysql_error());
$row_boss = mysql_fetch_assoc($boss);
$totalRows_boss = mysql_num_rows($boss);
  
if ($totalRows_boss == 0 ) {
$query2 = "INSERT INTO prod_activosj (IDempleado, IDempleadoJ) VALUES ('$IDempleado', '$IDempleadoJ')"; 
$result2 = mysql_query($query2) or die(mysql_error());  
} else {
$query2 = "UPDATE prod_activosj SET IDempleadoJ = '$IDempleadoJ' WHERE IDempleado = '$IDempleado'"; 
$result2 = mysql_query($query2) or die(mysql_error());  
}
header("Location: admin_desemp_lineas.php?info=2"); 	

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

    
    <script src="global_assets/js/core/libraries/jasny_bootstrap.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/autosize.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/formatter.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/typeahead.bundle.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/typeahead/handlebars.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/passy.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/maxlength.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_select.min.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/form_bootstrap_select.js"></script>
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
							<h5 class="panel-title">Editar Matriz</h5>
                              <?php } else { ?>
							<h5 class="panel-title">Agregar Matriz</h5>
                                <?php }  ?>
						</div>

					<div class="panel-body">
							<p>Ingresa la información solicitada. Algunos campos son obligatorios.</p>
                            <p>&nbsp;</p>
                            
                            <form method="post" name="form1" action="<?php echo $editFormAction; ?>" class="form-horizontal form-validate-jquery">
                            
                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Empleado:</label>
										<div class="col-lg-9">
						                  <p><strong><?php echo $row_resultados['emp_paterno'] . " " . $row_resultados['emp_materno'] . " " . $row_resultados['emp_nombre']; ?> (<?php echo $row_resultados['IDempleado']; ?>)</strong></p>
										</div>
									</div>
									<!-- /basic text input -->

                                   <!-- Basic text input -->
									<div class="form-group">
										<label class="control-label col-lg-3">Puesto:</label>
										<div class="col-lg-9">
						                  <p><strong><?php echo $row_resultados['denominacion']; ?> </strong></p>
										</div>
									</div>
									<!-- /basic text input -->

									<!-- Basic select -->
									<div class="form-group">
										<label class="control-label col-lg-3">Jefe Inmediato:<span class="text-danger">*</span></label>
										<div class="col-lg-9">
 											<select class="bootstrap-select" data-live-search="true" data-width="100%" name="IDempleadoJ" id="IDempleadoJ" required="required">
													  <option value="">Selecciona el nombre de tu jefe inmediato</option>
													  <?php  do { ?>
													  <option value="<?php echo $row_jefes['IDempleado']?>"<?php if (!(strcmp($row_jefes['IDempleado'], $row_resultados['IDempleadoJ']))) 
													  {echo "SELECTED";} ?>><?php echo $row_jefes['emp_nombre'] . " " . $row_jefes['emp_paterno'] . " " . $row_jefes['emp_materno']. " (" . $row_jefes['denominacion'] . ")";?></option>
													  <?php
													 } while ($row_jefes = mysql_fetch_assoc($jefes));
													   $rows = mysql_num_rows($jefes);
													   if($rows > 0) {
													   mysql_data_seek($jefes, 0);
													   $row_jefes = mysql_fetch_assoc($jefes);
													 } ?>
										</select>
										</div>
									</div>
									<!-- /basic select -->

                                    
                         <button type="submit"  name="KT_Update1" class="btn btn-primary">Actualizar</button>
                         <button type="button" data-target="#modal_theme_danger"  data-toggle="modal" class="btn btn-danger btn-icon">Borrar</button>
                         <input type="hidden" name="MM_update" value="form1">
                         <input type="hidden" name="IDresultado" value="<?php echo $row_resultados['IDresultado']; ?>">
                    	 <button type="button" onClick="window.location.href='admin_desemp.php'" class="btn btn-default btn-icon">Cancelar</button>
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
									<p>¿Estas seguro que quieres borrar el resultado?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admin_desemp_edit.php?IDresultado_borrar=<?php echo $row_resultados['IDresultado']; ?>">Si borrar</a>
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