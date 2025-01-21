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

mysql_select_db($database_vacantes, $vacantes);
$query_variables = "SELECT * FROM vac_variables";$variables = mysql_query($query_variables, $vacantes) or die(mysql_error());
$row_variables = mysql_fetch_assoc($variables);
$totalRows_variables = mysql_num_rows($variables);
$_menu = basename($_SERVER['PHP_SELF']);
list($menu, $extra) = explode(".", $_menu);
date_default_timezone_set("America/Mexico_City");
$anio = $row_variables['anio'];
$desfase = $row_variables['dias_desfase'];
$IDperiodovar = $row_variables['IDperiodo'];


$colname_usuario = "-1";
if (isset($_SESSION['kt_login_id'])) {
  $colname_usuario = $_SESSION['kt_login_id'];
}
mysql_select_db($database_vacantes, $vacantes);
$query_usuario = sprintf("SELECT * FROM vac_usuarios WHERE IDusuario = %s", GetSQLValueString($colname_usuario, "int"));
$usuario = mysql_query($query_usuario, $vacantes) or die(mysql_error());
$row_usuario = mysql_fetch_assoc($usuario);
$totalRows_usuario = mysql_num_rows($usuario);
$mis_areas = $row_usuario['IDmatrizes'];

$IDmatriz = $row_usuario['IDmatriz'];


$act_usuario = $_SESSION['kt_login_id'];
mysql_select_db($database_vacantes, $vacantes);
$query_puestos = "SELECT * FROM vac_matriz";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_puestos = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);


mysql_select_db($database_vacantes, $vacantes);
$query_lmatriz = "SELECT * FROM vac_matriz";
$lmatriz = mysql_query($query_lmatriz, $vacantes) or die(mysql_error());
$row_lmatriz = mysql_fetch_assoc($lmatriz);
$totalRows_lmatriz = mysql_num_rows($lmatriz);

if (isset($_POST['el_grupo'])) {$_SESSION['el_grupo'] = $_POST['el_grupo'];} 
if (!isset($_SESSION['el_grupo'])) {$_SESSION['el_grupo'] = 2;} 
$el_grupo = $_SESSION['el_grupo'];

	if (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 1) {$_SESSION['el_estatus'] = 1;}
elseif (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 0) {$_SESSION['el_estatus'] = 0;} 
elseif (isset($_POST['el_estatus']) && $_POST['el_estatus'] == 2) {$_SESSION['el_estatus'] = '1,0';} 
else {$_SESSION['el_estatus'] = '1,0';} 
$el_estatus = $_SESSION['el_estatus'];

//echo $_POST['el_estatus'];
//echo $el_estatus;

mysql_select_db($database_vacantes, $vacantes);
$query_resultados = "SELECT DISTINCT sed_competencias_resultados.IDevaluacion,  sed_competencias_resultados.IDempleado, sed_competencias_resultados.IDempleado_evaluador, sed_competencias_resultados.anio, sed_competencias_resultados.IDtipo, sed_competencias_resultados.IDestatus, sed_competencias_resultados.IDgrupo, prod_activos_evaluado.IDempleado AS IDempleadoO, prod_activos_evaluado.emp_paterno AS emp_paternoO, prod_activos_evaluado.emp_materno AS emp_maternoO, prod_activos_evaluado.emp_nombre AS emp_nombreO, prod_activos_evaluado.denominacion AS denominacionO, prod_activos_evaluador.IDempleado AS IDempleadoOr, prod_activos_evaluador.emp_paterno AS emp_paternoOr, prod_activos_evaluador.emp_materno AS emp_maternoOr, prod_activos_evaluador.emp_nombre AS emp_nombreOr, prod_activos_evaluador.denominacion AS denominacionOr FROM sed_competencias_resultados LEFT JOIN prod_activos AS prod_activos_evaluado ON  sed_competencias_resultados.IDempleado = prod_activos_evaluado.IDempleado left JOIN prod_activos AS prod_activos_evaluador ON  prod_activos_evaluador.IDempleado = sed_competencias_resultados.IDempleado_evaluador WHERE prod_activos_evaluado.IDempleado IS NOT NULL AND sed_competencias_resultados.IDgrupo = $el_grupo GROUP BY sed_competencias_resultados.IDempleado";
$resultados = mysql_query($query_resultados, $vacantes) or die(mysql_error());
$row_resultados = mysql_fetch_assoc($resultados);
$totalRows_resultados = mysql_num_rows($resultados);

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);


mysql_select_db($database_vacantes, $vacantes);
$query_grupos = "SELECT * FROM sed_competencias_grupos";
$grupos = mysql_query($query_grupos, $vacantes) or die(mysql_error());
$row_grupos = mysql_fetch_assoc($grupos);
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
	<!-- /theme JS files -->

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/forms/validation/validate.min.js"></script>
	<script src="global_assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switch.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script src="global_assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script src="global_assets/js/plugins/notifications/pnotify.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/form_validation.js"></script>
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
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 1))) { ?>
					    <div class="alert bg-green-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha agregado correctamente la evaluación.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->
                
						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 2))) { ?>
					    <div class="alert bg-blue-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha actualizado correctamente la evaluación.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 3))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha restaurado correctamente la evaluación.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->

						<!-- Basic alert -->
                        <?php if((isset($_GET['info']) && ($_GET['info'] == 4))) { ?>
					    <div class="alert bg-danger-600 alert-styled-left">
							<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
							Se ha borrado correctamente la evaluación.
					    </div>
                        <?php } ?>
					    <!-- /basic alert -->


					<!-- Contenido -->
                  <div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Bienvenido</h5>
						</div>

					<div class="panel-body">
							<p>Selecciona el resultado que requiera editar.</p>
							<p>Especial puede evaluar aún cuando esté cerrado el Periodo.</p>
                    
                    
                    
                    <form method="POST" action="admin_comp.php">
					<table class="table">
						<tbody>							  
							<tr>
							<td>
                            <strong>Grupos:</strong>
                         <select name="el_grupo" class="form-control">
                           <?php do {  ?>
                           <option value="<?php echo $row_grupos['IDgrupo']?>"<?php if (!(strcmp($row_grupos['IDgrupo'], $el_grupo))) {echo "selected=\"selected\"";} ?>><?php echo $row_grupos['grupo']?></option>
                           <?php
                          } while ($row_grupos = mysql_fetch_assoc($grupos));
                          $rows = mysql_num_rows($grupos);
                          if($rows > 0) {
                              mysql_data_seek($grupos, 0);
                              $row_grupos = mysql_fetch_assoc($grupos);
                          } ?></select>
                            </td>
							<td>
                            <button type="submit" class="btn btn-primary">Filtrar <i class="icon-arrow-right14 position-right"></i> </button>
                             <a href="admin_comp_agregar.php" class="btn btn-success">Agregar Evaluado</a>										
                             </td>
					      </tr>
					    </tbody>
				    </table>
                    </form>

                         
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						 <tr class="bg-blue">
                          <th>No. Emp.</th>
                          <th>Nombre del Evaluado</th>
                          <th>Puesto</th>
                          <th>Total de evaluaciones</th>
					      <th class="text-center">Acciones</th>
						 </tr>
					    </thead>
						<tbody>							  
                      <?php do { 
					$evaluadd =  $row_resultados['IDempleadoO'];



					mysql_select_db($database_vacantes, $vacantes);
					$query_evaluaciones = "SELECT * FROM sed_competencias_resultados WHERE IDempleado = $evaluadd";
					$evaluaciones = mysql_query($query_evaluaciones, $vacantes) or die(mysql_error());
					$row_evaluaciones = mysql_fetch_assoc($evaluaciones);
					$totalRows_evaluaciones = mysql_num_rows($evaluaciones);

					$query_evaluaciones_ok = "SELECT * FROM sed_competencias_resultados WHERE IDempleado = $evaluadd AND IDestatus = 1";
					$evaluaciones_ok = mysql_query($query_evaluaciones_ok, $vacantes) or die(mysql_error());
					$row_evaluaciones_ok = mysql_fetch_assoc($evaluaciones_ok);
					$totalRows_evaluaciones_ok = mysql_num_rows($evaluaciones_ok);
					  ?>
                        <tr>
                          <td><?php echo $row_resultados['IDempleadoO']; ?></td>
                          <td><?php echo $row_resultados['emp_paternoO'] . " " . $row_resultados['emp_maternoO'] . " " . $row_resultados['emp_nombreO']; ?></td>
                          <td><?php echo $row_resultados['denominacionO']; ?></td>
                          <td><?php  echo $totalRows_evaluaciones_ok." de ".$totalRows_evaluaciones; ?></td>
                         <td>
                         <button type="button" class="btn btn-info" onClick="window.location.href='admin_comp_resultado.php?IDempleado=<?php echo $row_resultados['IDempleado']; ?>'">Resultado</button>
                             <a href="prev_admin_comp.php?IDempleado=<?php echo $row_resultados['IDempleado']; ?>" class="btn btn-success">Evaluaciones</a>							</br>			

					<div id="modal_theme_danger<?php echo $row_resultados['IDempleado']; ?>2" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">Confirmación de Borrado</h6>
								</div>

								<div class="modal-body">
									<p>¿Estas seguro que quieres borrar la evaluación?</p>
								</div>

								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                                    <a class="btn btn-danger" href="admin_comp_ver.php?IDevaluacion=<?php echo $row_resultados['IDevaluacion']; ?>&borrar=2">Si borrar</a>
								</div>
							</div>
						</div>
					</div>


                        </td>
                        </tr>                       
                        <?php } while ($row_resultados = mysql_fetch_assoc($resultados)); ?>
                   	</tbody>							  
                 </table>

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