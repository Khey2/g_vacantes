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
$mi_fecha =  date('Y/m/d');


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
$fecha_mes = date("m");

mysql_select_db($database_vacantes, $vacantes);
$query_matriz = "SELECT * FROM vac_matriz WHERE IDmatriz = $IDmatriz";
mysql_query("SET NAMES 'utf8'");
$matriz = mysql_query($query_matriz, $vacantes) or die(mysql_error());
$row_matriz = mysql_fetch_assoc($matriz);
$totalRows_matriz = mysql_num_rows($matriz);
set_time_limit(0);

$query_meses = "SELECT * FROM vac_meses";
mysql_query("SET NAMES 'utf8'");
$meses = mysql_query($query_meses, $vacantes) or die(mysql_error());
$row_meses = mysql_fetch_assoc($meses);
$totalRows_meses = mysql_num_rows($meses);

$query_amatriz = "SELECT * FROM vac_matriz";
mysql_query("SET NAMES 'utf8'");
$amatriz = mysql_query($query_amatriz, $vacantes) or die(mysql_error());
$row_amatriz = mysql_fetch_assoc($amatriz);
$totalRows_amatriz = mysql_num_rows($amatriz);

$IDempleado = $_GET['IDempleado'];
$IDCcurso = $_GET['IDCcurso'];
$query_puestos = "SELECT capa_avance.*, capa_cursos.nombre_curso, capa_tipos_cursos.tipo_evento, vac_areas.area, vac_matriz.matriz, vac_sucursal.sucursal  FROM capa_avance LEFT JOIN capa_cursos ON capa_avance.IDC_capa_cursos = capa_cursos.IDC_capa_cursos LEFT JOIN capa_tipos_cursos ON capa_cursos.IDC_tipo_curso = capa_tipos_cursos.ID_tipo_evento LEFT JOIN vac_areas ON capa_avance.IDarea = vac_areas.IDarea LEFT JOIN vac_matriz ON capa_avance.IDmatriz = vac_matriz.IDmatriz LEFT JOIN vac_sucursal ON capa_avance.IDsucursal = vac_sucursal.IDsucursal WHERE IDempleado = $IDempleado";
mysql_query("SET NAMES 'utf8'");
$puestos = mysql_query($query_puestos, $vacantes) or die(mysql_error());
$row_capa = mysql_fetch_assoc($puestos);
$totalRows_puestos = mysql_num_rows($puestos);

// borrar alternativo
if ((isset($_GET['IDC_capa'])) && ($_GET['IDC_capa'] != "")) {
  
  $IDC_capa = $_GET['IDC_capa'];
  $IDempleado = $_GET['IDempleado'];
  $deleteSQL = "DELETE FROM capa_avance WHERE IDC_capa ='$IDC_capa'";

  mysql_select_db($database_vacantes, $vacantes);
  $result = mysql_query($deleteSQL, $vacantes) or die(mysql_error());
  header("Location: capa_reporte_3d.php?info=3&IDempleado=$IDempleado&IDCcurso=$IDCcurso");
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
	<script src="global_assets/js/core/libraries/jquery_ui/widgets.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/forms/selects/bootstrap_multiselect2.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script src="global_assets/js/plugins/forms/selects/select2.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/jszip/jszip.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/pdfmake.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/pdfmake/vfs_fonts.min.js"></script>
	<script src="global_assets/js/plugins/tables/datatables/extensions/buttons.min.js"></script>

	<!-- Theme JS files -->
	<script src="global_assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script src="global_assets/js/plugins/ui/moment/moment.min.js"></script>
	<script src="global_assets/js/plugins/pickers/daterangepicker.js"></script>
	<script src="global_assets/js/plugins/pickers/anytime.min.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.date.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/picker.time.js"></script>
	<script src="global_assets/js/plugins/pickers/pickadate/legacy.js"></script>

	<script src="assets/js/app.js"></script>
	<script src="global_assets/js/demo_pages/1picker_date.js"></script>
	<script src="global_assets/js/demo_pages/form_controls_extended.js"></script>
	<script src="global_assets/js/demo_pages/form_multiselect2.js"></script>
	<script src="global_assets/js/demo_pages/datatables_extension_buttons_html5.js"></script>

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
				
				
					<?php if(isset($_GET['info']) AND $_GET['info'] == 3) { ?>
					<!-- Basic alert -->
					<div class="alert bg-danger-600 alert-styled-left">
						<button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Cerrar</span></button>
						Se ha borrado correctamente el registro.
					</div>
					<!-- /basic alert -->
					<?php } ?>
				
					<!-- Option trees -->
					<div class="row">
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h6 class="panel-title">Histórico Capacitación Individual</h6>
									<div class="heading-elements">
				                	</div>
								</div>

								<div class="panel-body">

								<p>
								<ul>
									<li><b>IDemp: </b><?php echo $row_capa['IDempleado']; ?></li>
									<li><b>Nombre: </b><?php echo $row_capa['emp_paterno']." ".$row_capa['emp_materno']." ".$row_capa['emp_nombre']; ?></li>
									<li><b>Matriz: </b><?php echo $row_capa['matriz']; ?></li>
									<li><b>Area: </b><?php echo $row_capa['area']; ?></li>
								</ul>
								

								<form method="POST" action="capa_reporte_3b.php">
									<button type="submit" class="btn btn-default">Regresar</button> 
									<input type="hidden" name="IDCcurso" id="IDCcurso" value="<?php echo $IDCcurso; ?>">
								</form>
								</p>


								
					<div class="table-responsive">
					<table class="table table-condensed datatable-button-html5-columns">
						<thead>
						  <tr class="bg-primary">
                          <th>ID</th>
                          <th>Curso</th>
                          <th>Fecha</th>
                          <th>Puesto</th>
                          <th>Calificacion</th>
                          <th>Tipo</th>
                          <th>Programado</th>
                          <th>Acciones</th>
                        </tr>
						</thead>
						<tbody>							  
                        <?php do { ?>
                          <tr>
                            <td><?php echo $row_capa['IDC_capa']; ?></td>
                            <td><?php echo $row_capa['nombre_curso']; ?></td>
                            <td><?php echo $row_capa['fecha_evento']; ?></td>
                            <td><?php echo $row_capa['denominacion']; ?></td>
                            <td><?php echo $row_capa['calificacion']; ?></td>
                            <td><?php echo $row_capa['tipo_evento']; ?></td>
                            <td><?php if ($row_capa['IDC_programado'] == 1) {echo "SI";} else {echo "NO";} ?></td>
                            <td><button type="button" data-target="#modal_theme_danger4<?php echo $row_capa['IDC_capa']; ?>" data-toggle="modal" class="btn btn-danger">Borrar</button>	</td>
                           </tr>            

									<!-- danger modal -->
									<div id="modal_theme_danger4<?php echo $row_capa['IDC_capa']; ?>" class="modal fade" tabindex="-1">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header bg-danger">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">Confirmación de Borrado</h6>
												</div>
												<div class="modal-body">
												
												<p>¿Estas seguro de que quieres borrar el registro <b><?php echo $row_capa['nombre_curso']; ?></b>?</p>
												
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
													<a class="btn btn-danger" href="capa_reporte_3d.php?IDC_capa=<?php echo $row_capa['IDC_capa']; ?>&IDempleado=<?php echo $row_capa['IDempleado']; ?>&IDCcurso=<?php echo $IDCcurso; ?>">Si borrar</a>
												</div>
											</div>
										</div>
									</div>
									<!-- danger modal -->


						   
                		 <?php } while ($row_capa = mysql_fetch_assoc($puestos)); ?>
				    </tbody>
				    </table>
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