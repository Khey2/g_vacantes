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
$query_plan = "SELECT * FROM sed_clima_planes_liderazgo WHERE IDempleado = '$el_usuario'";
mysql_query("SET NAMES 'utf8'");
$plan = mysql_query($query_plan, $vacantes) or die(mysql_error());
$row_plan= mysql_fetch_assoc($plan);
$totalRows_plan = mysql_num_rows($plan);

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
	<script src="global_assets/js/core/libraries/jquery_ui/core.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/effects.min.js"></script>
	<script src="global_assets/js/core/libraries/jquery_ui/interactions.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<!-- /theme JS files -->
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


			<!-- Content area -->
			  <div class="content">

                      	<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente el compromiso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-success-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente el compromiso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente el compromiso.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Plan de Acción - Liderazgo</h5>
						</div>

					<div class="panel-body">
							<p>A continuación se muestran las acciones comprometidas en su Plan de Acción, con base en sus resultados de la Evaluación de Liderazgo.</p>

                    <!-- Colored button -->
					<div class="row">
					<div class="panel-body text-center">
                    <a class="btn btn-primary" href="f_clima_plan_edit.php">Agregar Objetivo<i class="icon-arrow-right14 position-right"></i></a>
                    </div>
					</div>
					<!-- /colored button -->



					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
                    			<thead>
                                	<tr class="bg-success-700"> 
                                    <th>Dimension</th>
                                    <th>Objetivo</th>
                                    <th>Periodicidad</th>
                                    <th>Area Oportunidad</th>
                                    <th>Estrategias</th>
                                    <th>Avance</th>
                                    <th>Acciones</th>
                                  </tr>
							</thead>
                                <tbody>
								  <?php if($totalRows_plan > 0) { ?>
								  <?php do { ?>
                                    <tr>
                                      <td><?php 
									  
									   switch ($row_plan['IDdimension']) {
  								case 1: $dimension = "Comunicación";  break;    
  								case 2: $dimension = "Acción y Resultados";  break;    
  								case 3: $dimension = "Participación";  break;    
  								case 4: $dimension = "Liderazgo con Valores";  break;    
  								case 5: $dimension = "Relaciones Interpersonales";  break;    
  								case 6: $dimension = "Motivación y Reconocimiento";  break;    
  								case '': $dimension = "-";  break;   } 
									  
									  echo $dimension; ?></td>
                                      <td class="col-xs-2"><?php echo $row_plan['objetivo']; ?></td>
                                      <td class="col-xs-1"><?php echo $row_plan['periodicidad']; ?></td>
                                      <td class="col-xs-2"><?php echo $row_plan['area_oportunidad']; ?></td>
                                      <td class="col-xs-2"><?php echo $row_plan['estrategias']; ?></td>
                                      <td class="col-xs-1"><?php if ($row_plan['resultados'] != '') { echo "SI";} else {echo "NO";}?></td>
                                      <td><a class="btn btn-success" href="f_clima_plan_edit.php?IDplan=<?php echo $row_plan['IDplan']; ?>">Ver detalles</a> 
                                      <button type="button" data-target="#modal_theme_danger<?php echo $row_plan['IDplan']; ?>" data-toggle="modal" class="btn btn-danger btn-icon">
                                      Borrar</button>
                                       </td>
                                    </tr>
                                    
                        <!-- danger modal -->
					<div id="modal_theme_danger<?php echo $row_plan['IDplan']; ?>" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la captura?.</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="clima_plan_edit_borrar.php?IDplan=<?php echo $row_plan['IDplan']; ?>&IDempleado=<?php echo $el_usuario; ?>&borrar=1">Si borrar</a>
								</div>
							</div>
						</div>
					</div>
					<!-- /danger modal -->

                                    
                                    
                                    <?php } while ($row_plan = mysql_fetch_assoc($plan)); ?>
								  <?php } else  { ?>
                                    <tr>
                                      <td>No se han capturado objetivos en Plan de Acción</td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                    </tr>
								  <?php } ?>
                                  </tbody>
                                </table>
							</div>
                      <p>&nbsp;</p>
                    
                    
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